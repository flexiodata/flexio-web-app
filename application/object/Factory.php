<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-11-16
 *
 * @package flexio
 * @subpackage Object
 */


declare(strict_types=1);
namespace Flexio\Object;


class Factory
{
    public static function load(string $eid, string $eid_type = null)
    {
        $model = \Flexio\System\System::getModel();
        if (!isset($eid_type))
            $eid_type = $model->getType($eid);

        switch ($eid_type)
        {
            default:
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            case \Model::TYPE_COMMENT:
                return \Flexio\Object\Comment::load($eid);

            case \Model::TYPE_CONNECTION:
                return \Flexio\Object\Connection::load($eid);

            case \Model::TYPE_PIPE:
                return \Flexio\Object\Pipe::load($eid);

            case \Model::TYPE_PROCESS:
                return \Flexio\Object\Process::load($eid);

            case \Model::TYPE_STREAM:
                return \Flexio\Object\Stream::load($eid);

            case \Model::TYPE_USER:
                return \Flexio\Object\User::load($eid);

            case \Model::TYPE_TOKEN:
                return \Flexio\Object\Token::load($eid);
        }
    }

    public static function createStreamContentCache(string $connection_eid, string $remote_path, string $handle) : \Flexio\Object\Stream
    {
        // STEP 1: get the connection
        $connection = \Flexio\Object\Connection::load($connection_eid);

        // STEP 2: create the stream
        $properties = array();
        $properties['connection_eid'] = $connection->getEid();
        $properties['parent_eid'] = ''; // cached content has no parent
        $properties['stream_type'] = \Flexio\Object\Stream::TYPE_FILE;
        $properties['name'] = $handle;
        $properties['path'] = \Flexio\Base\Util::generateHandle(); // path is the path to storage, not the path in the connection
        $properties['owned_by'] = $connection->getOwner();
        $storable_stream = \Flexio\Object\Stream::create($properties);

        // TODO: cache metadata about the file

        // STEP 3: copy the contents
        $streamwriter = $storable_stream->getWriter();
        $connection->getService()->read(['path' => $remote_path], function($data) use (&$streamwriter) {
            $streamwriter->write($data);
        });

        return $storable_stream;
    }

    public static function getStreamContentCache(string $connection_eid, string $handle) : ?\Flexio\Object\Stream
    {
        $filter = array('connection_eid' => $connection_eid, 'name' => $handle);
        $streams = \Flexio\Object\Stream::list($filter);

        if (count($streams) !== 1)
            return null;

        return $streams[0];
    }

    public static function createExampleObjects(string $user_eid) : array
    {
        $created_items = array();

        $objects = self::getExampleObjects();
        foreach ($objects as $o)
        {
            if (!isset($o['eid_type']))
                continue;

            $new_object = false;
            switch ($o['eid_type'])
            {
                case \Model::TYPE_CONNECTION:
                    $object_eid = self::createConnectionFromFile($user_eid, $o['path']);
                    $new_object = \Flexio\Object\Connection::load($object_eid);
                    break;

                case \Model::TYPE_PIPE:
                    $object_eid = self::createPipeFromFile($user_eid, $o['path']);
                    $new_object = \Flexio\Object\Pipe::load($object_eid);
                    break;
            }

            if ($new_object !== false)
                $created_items[] = $new_object->get();
        }

        return $created_items;
    }

    public static function createConnectionFromFile(string $user_eid, string $file_name) : string
    {
        // STEP 1: read the file and extract the info
        $content = \Flexio\Base\File::read($file_name);
        if (!is_string($content))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $definition = self::getConnectionInfoFromContent($content);
        if (!isset($definition))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // STEP 2: add additional info
        $call_params = $definition;
        $call_params['name'] = $definition['name'] ?? 'sample-connection';
        $call_params['owned_by'] = $user_eid;
        $call_params['created_by'] = $user_eid;

        // STEP 3: create the object
        $connection = \Flexio\Object\Connection::create($call_params);
        return $connection->getEid();
    }

    public static function createPipeFromFile(string $user_eid, string $file_name) : string
    {
        // STEP 1: read the file and extract the info
        $content = \Flexio\Base\File::read($file_name);
        if (!is_string($content))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $definition = self::getPipeInfoFromContent($content);
        if (!isset($definition))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // STEP 2: add additional info
        $file_name_base = \Flexio\Base\File::getFilename($file_name);
        $default_pipe_name = \Flexio\Base\Identifier::makeValid($file_name_base);

        $call_params = $definition;
        $call_params['name'] = $definition['name'] ?? $default_pipe_name;
        $call_params['owned_by'] = $user_eid;
        $call_params['created_by'] = $user_eid;

        // STEP 3: create the object
        $pipe = \Flexio\Object\Pipe::create($call_params);
        return $pipe->getEid();
    }

    public static function getConnectionInfoFromContent(string $content) : ?array
    {
        try
        {
            $yaml = \Flexio\Base\Yaml::extract($content);
            $connection_info_from_content = \Flexio\Base\Yaml::parse($yaml);
            return $connection_info_from_content;
        }
        catch (\Exception $exception)
        {
            // DEBUG:
            // echo('Unable to parse the YAML string: %s', $exception->getMessage());
        }

        return null;
    }

    public static function getPipeInfoFromContent(string $content, string $language = 'python') : ?array
    {
        try
        {
            // set basic pipe info using mostly same parameter names as
            // pipe api; use defaults supplied by object/model as well
            $yaml = \Flexio\Base\Yaml::extract($content);
            $pipe_info_from_content = \Flexio\Base\Yaml::parse($yaml);
            $pipe_params = $pipe_info_from_content;

            // content format consolidates schedule information: if it exists
            // the schedule is activated and if it doesn't exist, the schedule
            // isn't; unpack into form currently used by the model/api
            if (isset($pipe_info_from_content['schedule']))
                $pipe_params['deploy_schedule'] = \Model::PIPE_DEPLOY_STATUS_ACTIVE;
                 else
                $pipe_params['deploy_schedule'] = \Model::PIPE_DEPLOY_STATUS_INACTIVE;

            // set the task info
            if ($language === 'flexio')
            {
                // TODO: need to trim off front-matter; currently, 'flexio' pipes
                // aren't used, so this isn't a factor and following code provides
                // an outline
                $task = array();
                $pipe = @json_decode($pipe_info_from_content,true);
                if (!is_null($pipe))
                    $task = $pipe['task'] ?? array();
                $pipe_params['task'] = $task;
            }
            else
            {
                $execute_job_params = array();
                $execute_job_params['op'] = 'execute'; // set the execute operation so this doesn't need to be supplied
                $execute_job_params['lang'] = $language;
                $execute_job_params['code'] = base64_encode($pipe_info_from_content); // encode the script

                $pipe_params['task'] = [
                    "op" => "sequence",
                    "items" => [
                        $execute_job_params
                    ]
                ];
            }

            return $pipe_params;
        }
        catch (\Exception $exception)
        {
            // DEBUG:
            // echo('Unable to parse the YAML string: %s', $exception->getMessage());
        }

        return null;
    }

    private static function getExampleObjects() : array
    {
        $demo_dir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'demo' . DIRECTORY_SEPARATOR;

        $objects = array(
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'currency-rates.py'),
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'currency-converter.py'),
        );

        return $objects;
    }
}
