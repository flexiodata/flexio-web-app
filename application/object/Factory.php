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

        // STEP 2: create the object
        $call_params['name'] = $definition['name'] ?? 'sample-pipe';
        $call_params['description'] = $definition['description'] ?? '';
        $call_params['task'] = array();
        if (isset($definition['task']))
            $call_params['task'] = $definition['task'];

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

            // sample execute function
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'pipe_func_exchangerates.json'),

            // sample extract function
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'pipe_func_sales.json')
        );

        return $objects;
    }

    private static function getExampleObjectsOld() : array
    {
        // TODO: following examples are the legacy serverless example objects; here for reference,
        // but can be deleted when new examples are

        $demo_dir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'demo' . DIRECTORY_SEPARATOR;

        $objects = array(
            array('eid_type' => \Model::TYPE_CONNECTION, 'path' => $demo_dir . 'connection_amazons3.json'),

            /*
            TODO:

            Example: Keyword API
            Description: Create an API from keywords from a website
            * Demonstrates how to parse data from a website
            * Demonstrates how to create an use an API endpoint to get pipe contents

            Example: Access APIs that require Oauth
            Description: Extract keywords from Gmail
            * Demonstrates ability to access APIs that use Oauth
            * Demonstrates ability to read read/manipulate Gmail

            Example: Process Incoming Emails
            * Demonstrates ability to email pipes
            * Demonstrates how to extract parts from incoming email and process it

            */

            // Example: Email Results of a Python Function
            // Description: Get the top 5 stories from the Firebase Hacker News Feed and deliver via email
            // * Demonstrates scheduling
            // * Demonstrates emailing of results
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'pipe_email_results_of_python_function.json'),

            // Example: Save Data to Local Storage
            // Description: Aggregate, de-duplicate and save recent stories from Hacker News to local storage
            // * Demonstrates saving results to local storage
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'pipe_save_data_to_local_storage.json'),

            // Example: Analyze Data with Nodejs and Python
            // Description: Analyze Job Postings on GitHub Jobs with Nodejs Lodash and Python Pandas
            // * Demonstrates fact that various libraries are supported; execute is more than bare-bones
            // * Demonstrates fact that executes can be chained together
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'pipe_analyze_data_with_nodejs_and_python.json'),

            // Example: Server-Side Input Validation
            // Description: Validate input with a server-side logic that hides data used to validate with Python Cerberus
            // * Demonstrates how to use input area for testing
            // * Demonstrates reading from server-side data to validate/invalidate data
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'pipe_serverside_input_validation.json'),

            // legacy
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'pipe_generate_sample_data_api_feed.json'),
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'pipe_visualize_data_from_an_api_feed.json'),
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'pipe_render_webpage.json')
        );

        return $objects;
    }
}
