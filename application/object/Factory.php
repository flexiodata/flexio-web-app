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

    public static function createConnectionFromFile(string $user_eid, string $file_name) : string
    {
        // STEP 1: read the pipe file and convert it to JSON
        $buf = \Flexio\Base\File::read($file_name);
        $definition = @json_decode($buf,true);
        if ($definition === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // STEP 2: create the object
        $call_params['name'] = $definition['name'] ?? 'sample-connection';
        $call_params['description'] = $definition['description'] ?? '';

        if (isset($definition['connection_status']))
            $call_params['connection_status'] = $definition['connection_status'];
        if (isset($definition['connection_type']))
            $call_params['connection_type'] = $definition['connection_type'];
        if (isset($definition['connection_mode']))
            $call_params['connection_mode'] = $definition['connection_mode'];
        if (isset($definition['connection_info']))
            $call_params['connection_info'] = $definition['connection_info'];
        if (isset($definition['expires']))
            $call_params['expires'] = $definition['expires'];

        $call_params['owned_by'] = $user_eid;
        $call_params['created_by'] = $user_eid;
        $connection = \Flexio\Object\Connection::create($call_params);

        return $connection->getEid();
    }

    public static function createPipeFromFile(string $user_eid, string $file_name) : string
    {
        // STEP 1: read the pipe file and convert it to JSON
        $buf = \Flexio\Base\File::read($file_name);
        $definition = @json_decode($buf,true);
        if ($definition === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // STEP 2: get a default pipe name in case name isn't specified in
        // the pipe info
        $file_name_base = \Flexio\Base\File::getFilename($file_name);
        $default_pipe_name = \Flexio\Base\Identifier::makeValid($file_name_base);

        // STEP 2: create the object
        $call_params['name'] = $definition['name'] ?? $default_pipe_name;
        $call_params['title'] = $definition['title'] ?? '';
        $call_params['description'] = $definition['description'] ?? '';
        $call_params['deploy_mode'] = $definition['deploy_mode'] ?? \Model::PIPE_DEPLOY_MODE_RUN;
        $call_params['deploy_api'] = $definition['deploy_api'] ?? \Model::PIPE_DEPLOY_STATUS_ACTIVE;
        $call_params['deploy_schedule'] = $definition['deploy_schedule'] ?? \Model::PIPE_DEPLOY_STATUS_INACTIVE;
        $call_params['deploy_email'] = $definition['deploy_email'] ?? \Model::PIPE_DEPLOY_STATUS_INACTIVE;
        $call_params['deploy_ui'] = $definition['deploy_ui'] ?? \Model::PIPE_DEPLOY_STATUS_INACTIVE;
        $call_params['examples'] = $definition['examples'] ?? [];
        $call_params['params'] = $definition['params'] ?? [];
        $call_params['notes'] = $definition['notes'] ?? '';
        $call_params['task'] = $definition['task'] ?? [];
        $call_params['owned_by'] = $user_eid;
        $call_params['created_by'] = $user_eid;
        $pipe = \Flexio\Object\Pipe::create($call_params);

        return $pipe->getEid();
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

    private static function getExampleObjects() : array
    {
        $demo_dir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'demo' . DIRECTORY_SEPARATOR;

        $objects = array(

            // sample connection with data used for creating some sample functions
            array('eid_type' => \Model::TYPE_CONNECTION, 'path' => $demo_dir . 'connection_amazons3.json'),

            // sample execute functions
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'pipe_example_currency_rates.json'),
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'pipe_example_currency_converter.json'),

            // sample extract function
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'pipe_example_sales.json')
        );

        return $objects;
    }
}
