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

        $extension = strtolower(\Flexio\Base\File::getFileExtension($file_name));
        $definition = self::getPipeInfoFromContent($content, $extension);
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

    public static function getPipeInfoFromContent(string $content, string $extension) : ?array
    {
        try
        {
            // get the language from the extension type
            $language = false;
            switch ($extension)
            {
                case 'flexio':  $language = 'flexio'; break;
                case 'py':      $language = 'python'; break;
                case 'js':      $language = 'nodejs'; break;
            }

            if ($language === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

            // set basic pipe info using mostly same parameter names as
            // pipe api; use defaults supplied by object/model as well
            $yaml = \Flexio\Base\Yaml::extract($content);
            $pipe_info_from_content = \Flexio\Base\Yaml::parse($yaml);
            $pipe_params = array();

            // note: in the yaml, it's convenient to have placeholders for different elements,
            // such as "examples"; when these are empty arrays, the value passed is null,
            // which may cause an error when creating the object if an empty array is expected;
            // so, supply appropriate defaults for pipe elements that are null
            $pipe_params['name'] = $pipe_info_from_content['name'] ?? '';
            $pipe_params['title'] = $pipe_info_from_content['title'] ?? '';
            $pipe_params['icon'] = $pipe_info_from_content['icon'] ?? '';
            $pipe_params['description'] = $pipe_info_from_content['description'] ?? '';
            $pipe_params['examples'] = $pipe_info_from_content['examples'] ?? [];
            $pipe_params['params'] = $pipe_info_from_content['params'] ?? [];
            $pipe_params['returns'] = $pipe_info_from_content['returns'] ?? [];
            $pipe_params['notes'] = $pipe_info_from_content['notes'] ?? '';

            // convert config type into pipe run mode; default to pass-through
            $pipe_params['run_mode'] = \Model::PIPE_RUN_MODE_PASSTHROUGH;
            if (isset($pipe_info_from_content['config']) && $pipe_info_from_content['config'] === 'index')
                $pipe_params['run_mode'] = \Model::PIPE_RUN_MODE_INDEX;

            // convert deploy flag into deploy mode; default to inactive (build)
            $pipe_params['deploy_mode'] = \Model::PIPE_DEPLOY_MODE_BUILD;
            if (isset($pipe_info_from_content['deployed']) && $pipe_info_from_content['deployed'] === true)
                $pipe_params['deploy_mode'] = \Model::PIPE_DEPLOY_MODE_RUN;

            // content format consolidates schedule information: if it exists
            // the schedule is activated and if it doesn't exist, the schedule
            // isn't; unpack into form currently used by the model/api
            $pipe_params['deploy_schedule'] = \Model::PIPE_DEPLOY_STATUS_INACTIVE;
            if (isset($pipe_info_from_content['schedule']))
            {
                $pipe_params['deploy_schedule'] = \Model::PIPE_DEPLOY_STATUS_ACTIVE;
                $pipe_params['schedule'] = $pipe_info_from_content['schedule'];
            }

            // set the task info
            if ($language === 'flexio')
            {
                // TODO: need to trim off front-matter; currently, 'flexio' pipes
                // aren't used, so this isn't a factor and following code provides
                // an outline
                $task = array();
                $pipe = @json_decode($content,true);
                if (!is_null($pipe))
                    $task = $pipe['task'] ?? array();
                $pipe_params['task'] = $task;
            }
            else
            {
                $execute_job_params = array();
                $execute_job_params['op'] = 'execute'; // set the execute operation so this doesn't need to be supplied
                $execute_job_params['lang'] = $language;
                $execute_job_params['code'] = base64_encode($content); // encode the script

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

    public static function getStreamFromFile(string $owner, string $path) : \Flexio\Base\Stream
    {
        // TODO: connection mounts in connection object contain a similar caching
        // mechanism; should factor

        // get the connection identifier and remote path from the given path
        $connection_identifier = '';
        $remote_path = '';
        $vfs = new \Flexio\Services\Vfs($owner);
        $service = $vfs->getServiceFromPath($path, $connection_identifier, $remote_path);

        // get the file info
        $connection_eid = \Flexio\Object\Connection::getEidFromName($owner, $connection_identifier);
        $file_info = $service->getFileInfo($remote_path);

        // generate a handle for the content signature that will uniquely identify it;
        // use the owner plus a hash of some identifiers that constitute unique content
        $content_handle = '';
        $content_handle .= $owner; // include owner in the identifier so that even if the connection owner changes (later?), the cache will only exist for this owner
        $content_handle .= $file_info['hash']; // not always populated, so also add on info from the file
        $content_handle .= md5(
            $remote_path .
            strval($file_info['size']) .
            $file_info['modified']
        );

        // get the cached content; if it doesn't exist, create the cache
        $stored_stream = self::getStreamContentCache($connection_eid, $content_handle);
        if (!isset($stored_stream))
            $stored_stream = self::createStreamContentCache($connection_eid, $remote_path, $content_handle);

        // copy the stream contents to a memory stream
        $memory_stream = \Flexio\Base\Stream::create();

        $streamreader = $stored_stream->getReader();
        $streamwriter = $memory_stream->getWriter();

        while (($data = $streamreader->read(32768)) !== false)
            $streamwriter->write($data);

        return $memory_stream;
    }

    public static function getStreamFromConnectionInfo(array $connection_info, array $item_info) : \Flexio\Object\Stream
    {
        // generate a handle for the content signature that will uniquely identify it;
        // use the owner plus a hash of some identifiers that constitute unique content
        $content_handle = '';
        $content_handle .= $connection_info['owned_by']['eid']; // include owner in the identifier so that even if the connection owner changes (later?), the cache will only exist for this owner

        if ($connection_info['connection_type'] === \Model::CONNECTION_TYPE_HTTP)
        {
            // for HTTP content, download the content each time; to trigger this,
            // generate a unique handle for the content
            // TODO: use etags to get a hash signature, and then we can cache content
            $content_handle .= md5(\Flexio\Base\Util::generateHandle());
        }
        else
        {
            $content_handle .= $item_info['hash']; // not always populated, so also add on info from the file
            $content_handle .= md5(
                $item_info['path'] .
                strval($item_info['size']) .
                $item_info['modified']
            );
        }

        // get the cached content; if it doesn't exist, create the cache
        $connection_eid = $connection_info['eid'];
        $stored_stream = self::getStreamContentCache($connection_eid, $content_handle);
        if (!isset($stored_stream))
        {
            $remote_path = $item_info['path'];
            $stored_stream = self::createStreamContentCache($connection_eid, $remote_path, $content_handle);
        }

        return $stored_stream;
    }

    private static function createStreamContentCache(string $connection_eid, string $remote_path, string $handle) : \Flexio\Object\Stream
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

    private static function getStreamContentCache(string $connection_eid, string $handle) : ?\Flexio\Object\Stream
    {
        $filter = array('connection_eid' => $connection_eid, 'name' => $handle);
        $streams = \Flexio\Object\Stream::list($filter);

        if (count($streams) !== 1)
            return null;

        return $streams[0];
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
