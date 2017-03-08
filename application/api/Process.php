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


namespace Flexio\Api;


class Process
{
    public static function create($params, $request)
    {
        $process = self::create_internal($params, $request);
        if ($process === false)
            return false;  // API error was set by create_internal
        return $process->get();
    }

    public static function debug($params, $request)
    {
        // allow in debug mode or on test site
        if (!IS_DEBUG() && !IS_TESTSITE())
            return false;

        // run the specified job in blocking mode
        $params['background'] = isset_or($params['background'], false);
        $process = self::create_internal($params, $request);
        if ($process === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        $properties = $process->get();
        return $properties;
    }

    private static function create_internal($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'parent_eid'   => array('type' => 'identifier', 'required' => false),
                'process_mode' => array('type' => 'string', 'required' => false, 'default' => \Model::PROCESS_MODE_RUN),
                'task'         => array('type' => 'object', 'required' => false, 'default' => []),
                'params'       => array('type' => 'object', 'required' => false),
                'background'   => array('type' => 'boolean', 'required' => false, 'default' => true),
                'run'          => array('type' => 'boolean', 'required' => false, 'default' => false)
            ))) === false)
            return $request->getValidator()->fail();

        $pipe_identifier = isset($params['parent_eid']) ? $params['parent_eid'] : false;
        $background = toBoolean($params['background']);
        $autorun = toBoolean($params['run']);
        $requesting_user_eid = $request->getRequestingUser();

        // check rights
        $pipe = false;
        if ($pipe_identifier !== false)
        {
            $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
            if ($pipe === false)
                return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

            // make sure to set the parent_eid to the eid since this is what objects
            // downstream are expecting
            $params['parent_eid'] = $pipe->getEid();

            // TODO: rights check should be for "execute", not read/write

            // we're getting the logic from the pipe, and we're associating the process with
            // the pipe, so we should have both read/write access to the pipe;
            if ($pipe->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
                return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);
            if ($pipe->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_WRITE) === false)
                return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);
        }

        // STEP 1: create a new process job with the default task
        $process_properties = $params;
        unset($process_properties['params']);
        $process = \Flexio\Object\Process::create($process_properties);

        if (isset($params['params']))
            $process->setParams($params['params']);

        if ($process === false)
            return $request->getValidator()->fail(Api::ERROR_CREATE_FAILED);

        $process->setOwner($requesting_user_eid);
        $process->setCreatedBy($requesting_user_eid);

        // STEP 2: if a parent eid is specified, override the task with the parent's
        // task info, and associate the process
        if ($pipe !== false)
        {
            $process_properties['task'] = $pipe->getTask();
            $process->set($process_properties);
            $pipe->addProcess($process);
        }

        // STEP 3: run the process and return the process info
        if ($autorun === true)
            $process->run($background);

        return $process;
    }

    public static function delete($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $process_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $process = \Flexio\Object\Process::load($process_identifier);
        if ($process === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        // check the rights on the object
        if ($process->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_DELETE) === false)
            return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);

        $process->delete();
        return true;
    }

    public static function set($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true),
                'task' => array('type' => 'object', 'required' => false),
                'params' => array('type' => 'object', 'required' => false)
            ))) === false)
            return $request->getValidator()->fail();

        $process_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $process = \Flexio\Object\Process::load($process_identifier);
        if ($process === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        // check the rights on the object
        if ($process->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_WRITE) === false)
            return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);

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

    public static function get($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true),
                'wait' => array('type' => 'integer', 'required' => false), // how long to block (milliseconds) until a change is detected
                'status' => array('type' => 'boolean', 'required' => false, 'default' => false) // false returns everything; true only the process info
            ))) === false)
            return $request->getValidator()->fail();

        $process_identifier = $params['eid'];
        $process_info_only = toBoolean($params['status']);
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $process = \Flexio\Object\Process::load($process_identifier);
        if ($process === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        // check the rights on the object
        if ($process->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
            return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);

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
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

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

    public static function run($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true),
                'background'  => array('type' => 'boolean', 'required' => false, 'default' => true),
            ))) === false)
            return $request->getValidator()->fail();

        $process_identifier = $params['eid'];
        $background = toBoolean($params['background']);
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $process = \Flexio\Object\Process::load($process_identifier);
        if ($process === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        // check the rights on the object
        // if ($process->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_WRITE) === false)
        //     return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);

        return $process->run($background)->get();
    }

    public static function cancel($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $process_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $process = \Flexio\Object\Process::load($process_identifier);
        if ($process === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        // check the rights on the object
        // if ($process->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_WRITE) === false)
        //     return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);

        return $process->cancel()->get();
    }

    public static function pause($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $process_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $process = \Flexio\Object\Process::load($process_identifier);
        if ($process === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        // check the rights on the object
        // if ($process->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_WRITE) === false)
        //     return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);

        return $process->pause()->get();
    }

    public static function addInput($params, $request)
    {
        // TODO: handle manual streams that are added so that proces inputs
        // can come from files that are directly uploaded as well as from
        // streams that are already uploaded and are then added

        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $process_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $process = \Flexio\Object\Process::load($process_identifier);
        if ($process === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        // check the rights on the object
        // if ($process->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_WRITE) === false)
        //     return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);

        $stream = \Flexio\Object\Stream::create();


        \Flexio\Api\Stream::handleStreamUpload($params, $stream);


        return $process->addInput($stream)->get();
    }

    public static function getInput($params, $request)
    {
        // return the process input before any tasks; these will only be
        // streams that are added via addInput(); otherwise, the result
        // will be empty

        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true),
                'fields' => array('type' => 'string', 'array' => true, 'required' => false),
                'streams' => array('type' => 'string', 'array' => true, 'required' => false),
                'format' => array('type' => 'string', 'required' => false),
                'content-limit' => array('type' => 'string', 'required' => false)
            ))) === false)
            return $request->getValidator()->fail();

        $process_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $process = \Flexio\Object\Process::load($process_identifier);
        if ($process === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        $process_streams = $process->getInput();
        return self::echoStreamInfo($process_streams, $params);
    }

    public static function getOutput($params, $request)
    {
        // return the process output after the last task; this will be
        // empty if the process hasn't run

        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true),
                'fields' => array('type' => 'string', 'array' => true, 'required' => false),
                'streams' => array('type' => 'string', 'array' => true, 'required' => false),
                'format' => array('type' => 'string', 'required' => false),
                'content-limit' => array('type' => 'string', 'required' => false)
            ))) === false)
            return $request->getValidator()->fail();

        $process_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $process = \Flexio\Object\Process::load($process_identifier);
        if ($process === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        $process_streams = $process->getOutput();
        return self::echoStreamInfo($process_streams, $params);
    }

    public static function getTaskInputInfo($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'parent_eid' => array('type' => 'identifier', 'required' => true),
                'eid' => array('type' => 'identifier', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $process_identifier = $params['parent_eid'];
        $task_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        $process = \Flexio\Object\Process::load($process_identifier);
        if ($process === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        $streams = $process->getTaskInputStreams($task_identifier);
        if (!is_array($streams))
            return array(); // return an empty array if we don't have any streams

        // get the structures
        $structures = array();
        foreach ($streams as $s)
        {
            $structures[] = $s->getStructure();
        }

        // create a merged structure
        $merged_structure = \Flexio\Object\Structure::union($structures);
        return $merged_structure->enum();
    }

    public static function getTaskOutputInfo($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'parent_eid' => array('type' => 'identifier', 'required' => true),
                'eid' => array('type' => 'identifier', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $process_identifier = $params['parent_eid'];
        $task_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        $process = \Flexio\Object\Process::load($process_identifier);
        if ($process === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        $streams = $process->getTaskOutputStreams($task_identifier);
        if (!is_array($streams))
            return array(); // return an empty array if we don't have any streams

        // get the structures
        $structures = array();
        foreach ($streams as $s)
        {
            $structures[] = $s->getStructure();
        }

        // create a merged structure
        $merged_structure = \Flexio\Object\Structure::union($structures);
        return $merged_structure->enum();
    }

    public static function getStatistics($params, $request)
    {
        // only allow users from flex.io to get this info

        $requesting_user_eid = $request->getRequestingUser();
        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user === false)
            return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);

        if ($user->isAdministrator() !== true)
            return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);

        return \Flexio\System\System::getModel()->process->getProcessStatistics();
    }

    private static function waitforchangewhilerunning($eid, $time_to_wait_for_change)
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

    private static function echoStreamInfo($process_streams, $params)
    {
        // return the stream info the user requests
        $flags = array();
        $flags['content-limit'] = isset_or($params['content-limit'], false);
        $flags['fields'] = isset_or($params['fields'], false);
        $flags['streams'] = isset_or($params['streams'], false);
        $mime_type = isset_or($params['format'], false);

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

    private static function packageRequestedStreamInfo($stream, $flags, $mime_type)
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
            $stream_properties = \Flexio\System\Util::filterArray($stream_properties, $fields);

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
            $limit = $contentlimit === false ? pow(2,24) : (int)$contentlimit;
            $columns = true;
            $metadata = false;
            $handle = 'create';
            $content = $stream->content($start, $limit, $columns, $metadata, $handle);
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
