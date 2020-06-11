<?php
/**
 *
 * Copyright (c) 2015, Flex Research LLC. All rights reserved.
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
                $created_items[] = $new_object;
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
            $language = false;
            switch ($extension)
            {
                default:
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

                case 'yml': $language = 'yml'; break;
                case 'py':  $language = 'python'; break;
                case 'js':  $language = 'nodejs'; break;
            }

            // if we have yml; the file supplies the parameters directly
            if ($language === 'yml')
                return self::getPipeParamsFromYaml($content);

            // if we have one of the languages, extract the yaml, get the properties,
            // and then set the execute job manually; override the task with a task
            // to execute the content
            $yaml = \Flexio\Base\Yaml::extract($content);
            $pipe_params = self::getPipeParamsFromYaml($yaml);

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

            return $pipe_params;
        }
        catch (\Exception $exception)
        {
            // DEBUG:
            // echo('Unable to parse the YAML string: %s', $exception->getMessage());
        }

        return null;
    }

    private static function getPipeParamsFromYaml(string $yaml) : array
    {
        $pipe_info_from_content = \Flexio\Base\Yaml::parse($yaml);

        // note: in the yaml, it's convenient to have placeholders for different elements,
        // such as "examples"; when these are empty arrays, the value passed is null,
        // which may cause an error when creating the object if an empty array is expected;
        // so, supply appropriate defaults for pipe elements that are null
        $pipe_params = array();
        $pipe_params['name'] = $pipe_info_from_content['name'] ?? '';
        $pipe_params['title'] = $pipe_info_from_content['title'] ?? '';
        $pipe_params['icon'] = $pipe_info_from_content['icon'] ?? '';
        $pipe_params['description'] = $pipe_info_from_content['description'] ?? '';
        $pipe_params['examples'] = $pipe_info_from_content['examples'] ?? [];
        $pipe_params['params'] = $pipe_info_from_content['params'] ?? [];
        $pipe_params['returns'] = $pipe_info_from_content['returns'] ?? [];
        $pipe_params['notes'] = $pipe_info_from_content['notes'] ?? '';
        $pipe_params['task'] = $pipe_info_from_content['task'] ?? json_decode('{}');

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

        return $pipe_params;
    }

    private static function getExampleObjects() : array
    {
        $demo_dir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'demo' . DIRECTORY_SEPARATOR;

        $objects = array(
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'flex-sample-contacts.py'),
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'flex-zipcode-stats.py'),
        );

        return $objects;
    }
}
