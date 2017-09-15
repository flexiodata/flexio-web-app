<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-04-07
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class Process
{
    public static function create(array $params, string $requesting_user_eid = null) : array
    {
        $process = self::create_internal($params, $requesting_user_eid);
        if ($process === false)
            return false;  // API error was set by create_internal
        return $process->get();
    }

    public static function debug(array $params, string $requesting_user_eid = null) : array
    {
        // allow in debug mode or on test site
        if (!IS_DEBUG() && !IS_TESTSITE())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_METHOD);

        // run the specified job in blocking mode
        $params['background'] = $params['background'] ?? false;
        $params['debug'] = true;
        $process = self::create_internal($params, $requesting_user_eid);
        if ($process === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        $properties = $process->get();

        return $properties;
    }

    private static function create_internal(array $params, string $requesting_user_eid = null) : \Flexio\Object\Process
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'parent_eid'   => array('type' => 'identifier', 'required' => false),
                'process_mode' => array('type' => 'string', 'required' => false, 'default' => \Model::PROCESS_MODE_RUN),
                'task'         => array('type' => 'object', 'required' => false),
                'params'       => array('type' => 'object', 'required' => false),
                'background'   => array('type' => 'boolean', 'required' => false, 'default' => true),
                'debug'        => array('type' => 'boolean', 'required' => false, 'default' => false),
                'run'          => array('type' => 'boolean', 'required' => false, 'default' => false)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $pipe_identifier = isset($params['parent_eid']) ? $params['parent_eid'] : false;
        $background = toBoolean($params['background']);
        $debug = toBoolean($params['debug']);
        $autorun = toBoolean($params['run']);

        // check rights
        $pipe = false;
        if ($pipe_identifier !== false)
        {
            $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
            if ($pipe === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

            // make sure to set the parent_eid to the eid since this is what objects
            // downstream are expecting
            $params['parent_eid'] = $pipe->getEid();

            // we're getting the logic from the pipe, and we're associating the process with
            // the pipe, so we should have both read/write access to the pipe;
            if ($pipe->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_EXECUTE) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);
        }

        // STEP 1: create a new process job with the default task
        $process_properties = $params;
        unset($process_properties['params']);
        $process = \Flexio\Object\Process::create($process_properties);

        if ($pipe !== false)
        {
            // if the process is created from a pipe, it runs with pipe owner privileges
            // and inherits the rights from the pipe
            $process->setOwner($pipe->getOwner());

            if ($requesting_user_eid === \Flexio\Object\User::MEMBER_PUBLIC)
                $process->setCreatedBy($pipe->getOwner());
                else
                $process->setCreatedBy($requesting_user_eid);

            $process->setRights($pipe->getRights());
        }
         else
        {
            // if the process is created independent of a pipe, it runs with requesting
            // user privileges
            $process->setOwner($requesting_user_eid);
            $process->setCreatedBy($requesting_user_eid);
        }

        if (isset($params['params']))
            $process->setParams($params['params']);

        // STEP 2: if a parent eid is specified, associate the process with the
        // parent; if a task is specified, override the task of the parent (to
        // allow runtime parameters to be set on the pipe that override the
        // pipe variables); if a task isn't specified, use the parent's task
        if ($pipe !== false)
        {
            if (!isset($process_properties['task']))
                 $process_properties['task'] = $pipe->getTask();

            $process->set($process_properties);
            $pipe->addProcess($process);
        }

        // STEP 3: run the process and return the process info
        if ($autorun === true)
            $process->run($background, $debug);

        return $process;
    }

    public static function delete(array $params, string $requesting_user_eid = null) : bool
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $process_identifier = $params['eid'];

        // load the object
        $process = \Flexio\Object\Process::load($process_identifier);
        if ($process === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($process->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_DELETE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $process->delete();
        return true;
    }

    public static function set(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true),
                'task' => array('type' => 'object', 'required' => false),
                'params' => array('type' => 'object', 'required' => false)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $process_identifier = $params['eid'];

        // load the object
        $process = \Flexio\Object\Process::load($process_identifier);
        if ($process === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($process->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // TODO: we shouldn't allow the task to be set if the process is anything
        // past the initial pending state

        if (isset($params['params']))
        {
            $process->setParams($params['params']);
            unset($params['params']);
        }

        // set the properties
        $process->set($params);
        return $process->get();
    }

    public static function get(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true),
                'wait' => array('type' => 'integer', 'required' => false), // how long to block (milliseconds) until a change is detected
                'status' => array('type' => 'boolean', 'required' => false, 'default' => false) // false returns everything; true only the process info
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $process_identifier = $params['eid'];
        $process_info_only = toBoolean($params['status']);

        // load the object
        $process = \Flexio\Object\Process::load($process_identifier);
        if ($process === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($process->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // if no wait period is specified, return the information immediately
        if (!isset($params['wait']))
        {
            $process_info = $process->get();

            if ($process_info_only === true)
            {
                $result = array();
                $result['eid'] = $process_info['eid'];
                $result['process_status'] = $process_info['process_status'];
                $result['process_info'] = $process_info['process_info'];
                return $result;
            }

            return $process_info;
        }

        // wait for any changes, then reload process to refresh the data
        $wait_for_change = $params['wait'];
        self::waitforchangewhilerunning($process->getEid(), $wait_for_change);
        $process = \Flexio\Object\Process::load($process->getEid());
        if ($process === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        $process_info = $process->get();

        if ($process_info_only === true)
        {
            $result = array();
            $result['eid'] = $process_info['eid'];
            $result['process_status'] = $process_info['process_status'];
            $result['process_info'] = $process_info['process_info'];
            return $result;
        }

        return $process_info;
    }

    public static function listall(array $params, string $requesting_user_eid = null) : array
    {
        // load the object
        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // get the processes
        $filter = array('eid_type' => array(\Model::TYPE_PIPE), 'eid_status' => array(\Model::STATUS_AVAILABLE));
        $processes = $user->getProcesses($filter);

        $result = array();
        foreach ($processes as $p)
        {
            if ($p->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
                continue;

            $process_info = $p->get();

            $process_info_subset = array();
            $process_info_subset['eid'] = $process_info['eid'];
            $process_info_subset['eid_type'] = $process_info['eid_type'];
            $process_info_subset['eid_status'] = $process_info['eid_status'];
            $process_info_subset['parent'] = $process_info['parent'];
            $process_info_subset['owned_by'] = $process_info['owned_by'];
            $process_info_subset['started_by'] = $process_info['started_by'];
            $process_info_subset['started'] = $process_info['started'];
            $process_info_subset['finished'] = $process_info['finished'];
            $process_info_subset['duration'] = $process_info['duration'];
            $process_info_subset['process_status'] = $process_info['process_status'];
            $process_info_subset['created'] = $process_info['created'];
            $process_info_subset['updated'] = $process_info['updated'];

            $result[] = $process_info_subset;
        }

        return $result;
    }

    public static function run(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true),
                'stream' => array('type' => 'string', 'required' => false)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $process_identifier = $params['eid'];
        $stream_to_echo = $params['stream'] ?? false;
        $background = false;

        // load the object
        $process = \Flexio\Object\Process::load($process_identifier);
        if ($process === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($process->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_EXECUTE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // parse the content and set the stream info
        $php_stream_handle = fopen('php://input', 'rb');
        $post_content_type = $_SERVER['CONTENT_TYPE'] ?? '';
         \Flexio\Object\Process::addProcessInputFromStream($php_stream_handle, $post_content_type, $process);

         // run the process and get the output
        $process->run($background);
        $output_streams = $process->getOutput()->getStreams();

        // if a stream wasn't specified, echo the last stream; otherwise, try to
        // return the requested stream
        if ($stream_to_echo === false)
        {
            $last_stream_idx = count($output_streams) - 1;
            if ($last_stream_idx >= 0)
                $stream_to_echo = $output_streams[$last_stream_idx]->getName();
                 else
                exit(0); // nothing was requested, but nothing to return
        }

        for ($i = 0; $i < count($output_streams); ++$i)
        {
            if ($output_streams[$i]->getName() == $stream_to_echo || (is_numeric($stream_to_echo) && $i == $stream_to_echo))
            {
                $stream = $output_streams[$i];
                $stream_info = $stream->get();
                if ($stream_info === false)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

                $mime_type = $stream_info['mime_type'];
                $start = 0;
                $limit = PHP_INT_MAX;
                $content = $stream->content($start, $limit);

                if ($mime_type !== \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE)
                {
                    // return content as-is
                    header('Content-Type: ' . $mime_type);
                }
                else
                {
                    // flexio table; return application/json in place of internal mime
                    header('Content-Type: ' . \Flexio\Base\ContentType::MIME_TYPE_JSON);
                    $content = json_encode($content);
                }

                echo($content);
                exit(0);
            }
        }

        header('HTTP/1.0 404 Not Found');
        exit(0);
    }

    public static function addInput(array $params, string $requesting_user_eid = null) : array
    {
        // TODO: handle manual streams that are added so that proces inputs
        // can come from files that are directly uploaded as well as from
        // streams that are already uploaded and are then added

        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $process_identifier = $params['eid'];

        // load the object
        $process = \Flexio\Object\Process::load($process_identifier);
        if ($process === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        // if ($process->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
        //     throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $stream = \Flexio\Object\Stream::create();


        \Flexio\Api\Stream::handleStreamUpload($params, $stream);


        return $process->addInput($stream)->get();
    }

    public static function getInput(array $params, string $requesting_user_eid = null) // TODO: set function return type
    {
        // return the process input before any tasks; these will only be
        // streams that are added via addInput(); otherwise, the result
        // will be empty

        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true),
                'fields' => array('type' => 'string', 'array' => true, 'required' => false),
                'streams' => array('type' => 'string', 'array' => true, 'required' => false),
                'format' => array('type' => 'string', 'required' => false),
                'content-limit' => array('type' => 'string', 'required' => false)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $process_identifier = $params['eid'];

        // load the object
        $process = \Flexio\Object\Process::load($process_identifier);
        if ($process === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        $process_streams = $process->getInput()->getStreams();
        return self::echoStreamInfo($process_streams, $params);
    }

    public static function getOutput(array $params, string $requesting_user_eid = null) // TODO: set function return type
    {
        // return the process output after the last task; this will be
        // empty if the process hasn't run
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true),
                'fields' => array('type' => 'string', 'array' => true, 'required' => false),
                'streams' => array('type' => 'string', 'array' => true, 'required' => false),
                'format' => array('type' => 'string', 'required' => false),
                'content-limit' => array('type' => 'string', 'required' => false)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $process_identifier = $params['eid'];

        // load the object
        $process = \Flexio\Object\Process::load($process_identifier);
        if ($process === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        $process_streams = $process->getOutput()->getStreams();
        return self::echoStreamInfo($process_streams, $params);
    }

    public static function getTaskInputInfo(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'parent_eid' => array('type' => 'identifier', 'required' => true),
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $process_identifier = $params['parent_eid'];
        $task_identifier = $params['eid'];

        $process = \Flexio\Object\Process::load($process_identifier);
        if ($process === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        $input_context = \Flexio\Object\Context::create();
        $output_context = \Flexio\Object\Context::create();
        $process->getTaskStreams($input_context, $output_context, $task_identifier);
        $input_streams = $input_context->getStreams();

        // get the structures
        $structures = array();
        foreach ($input_streams as $s)
        {
            $structures[] = $s->getStructure();
        }

        // create a merged structure
        $merged_structure = \Flexio\Base\Structure::union($structures);
        return $merged_structure->enum();
    }

    public static function getTaskOutputInfo(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'parent_eid' => array('type' => 'identifier', 'required' => true),
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $process_identifier = $params['parent_eid'];
        $task_identifier = $params['eid'];

        $process = \Flexio\Object\Process::load($process_identifier);
        if ($process === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        $input_context = \Flexio\Object\Context::create();
        $output_context = \Flexio\Object\Context::create();
        $process->getTaskStreams($input_context, $output_context, $task_identifier);
        $output_streams = $output_context->enum();

        // get the structures
        $structures = array();
        foreach ($output_streams as $s)
        {
            $structures[] = $s->getStructure();
        }

        // create a merged structure
        $merged_structure = \Flexio\Base\Structure::union($structures);
        return $merged_structure->enum();
    }

    private static function waitforchangewhilerunning(string $eid, int $time_to_wait_for_change) // TODO: set function return type
    {
        // TODO: move part of implemention to some type of function
        // on the object?  e.g. $object->hasChanged() so we can cache
        // non-changed portions of the object in the object layer (right
        // now, we don't cache the process status so that we always have
        // a fresh status since it's being updated by another process;
        // but as a consequence, we don't cache any of the other process
        // values either, many of which won't change (e.g. list of tasks))

        // $wait_for_change is how long to wait for a change (milliseconds)
        // before returning what we have

        $minimum_wait = 0;
        $maximum_wait = 10000; // wait 10 seconds before returning
        $wait_interval = 100;  // check for changes every 100 milliseconds
        $time_to_wait_for_change = $time_to_wait_for_change;

        if ($time_to_wait_for_change <= $minimum_wait)
            $time_to_wait_for_change = $minimum_wait;
        if ($time_to_wait_for_change > $maximum_wait)
            $time_to_wait_for_change = $maximum_wait;

        $process = \Flexio\Object\Process::load($eid);
        if ($process === false)
            return;

        $status_initial = $process->getProcessStatus();

        // if the job is cancelled, failed, or completed, then it's in
        // the final state, so there's nothing to wait for
        if ($status_initial === \Model::PROCESS_STATUS_CANCELLED ||
            $status_initial === \Model::PROCESS_STATUS_FAILED ||
            $status_initial === \Model::PROCESS_STATUS_COMPLETED)
            return;

        $time_waited = 0;
        while (true)
        {
            usleep($wait_interval*1000);
            $time_waited += $wait_interval;

            // if the time is up, return what we have
            if ($time_waited > $time_to_wait_for_change)
                return;

            $status_current = $process->getProcessStatus();

            // compare the states of the two proces instances; use !=
            // for value comparison rather than instance comparison
            if ($status_current != $status_initial)
                return;
        }
    }

    private static function echoStreamInfo(array $process_streams, array $params)
    {
        // return the stream info the user requests
        $flags = array();
        $flags['content-limit'] = $params['content-limit'] ?? false;
        $flags['fields'] = $params['fields'] ?? false;
        $flags['streams'] = $params['streams'] ?? false;
        $mime_type = $params['format'] ?? false;

        // determine the output format
        switch ($mime_type)
        {
            // use json if the format is something that isn't allowed
            default:
                $mime_type = \Flexio\Base\ContentType::MIME_TYPE_JSON;
                break;

            // allowed formats
            case \Flexio\Base\ContentType::MIME_TYPE_TXT:
            case \Flexio\Base\ContentType::MIME_TYPE_JSON:
                break;
        }

        // if we have a stream filter, prepare the stream array for checking names later
        $streams = false;
        if ($flags['streams'] !== false)
            $streams = array_flip($flags['streams']);

        header('Content-Type: ' . $mime_type);

        if ($mime_type === \Flexio\Base\ContentType::MIME_TYPE_JSON)
            echo('[');

        $first = true;
        foreach ($process_streams as $stream)
        {
            // if a stream name filter is set, only return the specified streams
            if ($streams !== false)
            {
                $stream_name = $stream->getName();
                if (array_key_exists($stream_name, $streams) === false)
                    continue;
            }

            if ($first === false)
                echo(',');

            $result = self::packageRequestedStreamInfo($stream, $flags, $mime_type);
            echo($result);
            $first = false;
        }

        if ($mime_type === \Flexio\Base\ContentType::MIME_TYPE_JSON)
            echo(']');

        exit(0);
    }

    private static function packageRequestedStreamInfo(\Flexio\Object\Stream $stream, array $flags, string $mime_type) : string
    {
        $fields = $flags['fields'];
        $contentlimit = $flags['content-limit'];

        // if fields are specified, filter the list by these fields, otherwise
        // include them all
        $stream_properties = $stream->get();
        if ($stream_properties === false)
            return '';

        $include_content = false;
        if ($fields !== false)
        {
            $stream_properties = \Flexio\Base\Util::filterArray($stream_properties, $fields);

            // see whether or not to include the content
            $include_content = array_search('content', $fields);
            if ($include_content !== false)
                $include_content = true;
        }

        // if the content field is specified, include the content
        $content = '';
        if ($include_content === true)
        {
            $start = 0;
            $limit = $contentlimit === false ? PHP_INT_MAX : (int)$contentlimit;
            $content = $stream->content($start, $limit);
        }

        // return the output
        $result = '';
        switch ($mime_type)
        {
            case \Flexio\Base\ContentType::MIME_TYPE_TXT:
            {
                if (count($stream_properties) > 0)
                    $result .= json_encode($stream_properties);

                if (is_array($content))
                    $content = @json_encode($content['rows']);

                if ($include_content === true)
                    $result .= $content;
            }
            break;

            case \Flexio\Base\ContentType::MIME_TYPE_JSON:
            {
                if ($include_content === true)
                    $stream_properties['content'] = $content;

                $result .= @json_encode($stream_properties, JSON_PRETTY_PRINT);
            }
            break;
        }

        return $result;
    }
}
