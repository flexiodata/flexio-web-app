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
namespace Flexio\Api1;


class Process
{
    public static function create(\Flexio\Api1\Request $request) : array
    {
        $process = self::create_internal($request);
        if ($process === false)
            return false;  // API error was set by create_internal
        return $process->get();
    }

    public static function debug(\Flexio\Api1\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        // allow in debug mode or on test site
        if (!IS_DEBUG() && !IS_TESTSITE())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_REQUEST);

        // run the specified job in blocking mode
        $params['background'] = $params['background'] ?? false;
        $params['run'] = true;

        $copied_request = $request->clone();
        $copied_request->setPostParams($params);

        $process = self::create_internal($copied_request);
        if ($process === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        $properties = $process->get();

        return $properties;
    }

    private static function create_internal(\Flexio\Api1\Request $request) : \Flexio\Object\Process
    {
        $params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'parent_eid'   => array('type' => 'identifier', 'required' => false),
                'process_mode' => array('type' => 'string', 'required' => false, 'default' => \Flexio\Jobs\Process::MODE_RUN),
                'task'         => array('type' => 'object', 'required' => false),
                'background'   => array('type' => 'boolean', 'required' => false, 'default' => true),
                'debug'        => array('type' => 'boolean', 'required' => false, 'default' => false),
                'run'          => array('type' => 'boolean', 'required' => false, 'default' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $pipe_identifier = isset($validated_params['parent_eid']) ? $validated_params['parent_eid'] : false;
        $background = toBoolean($validated_params['background']);
        $debug = toBoolean($validated_params['debug']);
        $autorun = toBoolean($validated_params['run']);

        // check rights
        $pipe = false;
        if ($pipe_identifier !== false)
        {
            if (\Flexio\Base\Eid::isValid($pipe_identifier) === false)
            {
                $eid_from_identifier = \Flexio\Object\Pipe::getEidFromName($requesting_user_eid, $pipe_identifier);
                $pipe_identifier = $eid_from_identifier !== false ? $eid_from_identifier : '';
            }
            $pipe = \Flexio\Object\Pipe::load($pipe_identifier);

            // make sure to set the parent_eid to the eid since this is what objects
            // downstream are expecting
            $validated_params['parent_eid'] = $pipe->getEid();

            // we're getting the logic from the pipe, and we're associating the process with
            // the pipe, so we should have both read/write access to the pipe;
            if ($pipe->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
            if ($pipe->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_EXECUTE) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);
        }

        if ($pipe !== false)
        {
            // if the process is created from a pipe, it runs with pipe owner privileges
            // and inherits the rights from the pipe
            $validated_params['owned_by'] = $pipe->getOwner();
            $validated_params['created_by'] = $requesting_user_eid;
        }
        else
        {
            // if the process is created independent of a pipe, it runs with requesting
            // user privileges
            $validated_params['owned_by'] = $requesting_user_eid;
            $validated_params['created_by'] = $requesting_user_eid;
        }

        // STEP 1: create a new process job with the default task
        $process = \Flexio\Object\Process::create($validated_params);

        // if the process is created from a pipe, it runs with pipe owner privileges
        // and inherits the rights from the pipe
        if ($pipe !== false)
            $process->setRights($pipe->getRights());

        // STEP 2: if a parent eid is specified, associate the process with the
        // parent; if a task is specified, override the task of the parent (to
        // allow runtime parameters to be set on the pipe that override the
        // pipe variables); if a task isn't specified, use the parent's task
        if ($pipe !== false)
        {
            $parent_properties = array();
            $parent_properties['parent_eid'] = $pipe->getEid();

            if (!isset($validated_params['task']))
                $parent_properties['task'] = $pipe->getTask();

            $process->set($parent_properties);
        }

        // note: removed debug setting

        // STEP 3: run the process and return the process info
        if ($autorun === true)
        {
            $engine = \Flexio\Jobs\StoredProcess::create($process);
            $engine->run($background);
        }

        return $process;
    }

    public static function delete(\Flexio\Api1\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $process_identifier = $validated_params['eid'];

        // load the object
        $process = \Flexio\Object\Process::load($process_identifier);

        // check the rights on the object
        if ($process->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($process->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_DELETE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $process->delete();

        $result = array();
        $result['eid'] = $process->getEid();
        $result['eid_type'] = $process->getType();
        $result['eid_status'] = $process->getStatus();
        return $result;
    }

    public static function set(\Flexio\Api1\Request $request) : array
    {
        $params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true),
                'task' => array('type' => 'object', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $process_identifier = $validated_params['eid'];

        // load the object
        $process = \Flexio\Object\Process::load($process_identifier);

        // check the rights on the object
        if ($process->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($process->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // TODO: we shouldn't allow the task to be set if the process is anything
        // past the initial pending state

        // set the properties
        $process->set($validated_params);
        return $process->get();
    }

    public static function get(\Flexio\Api1\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true),
                'wait' => array('type' => 'integer', 'required' => false), // how long to block (milliseconds) until a change is detected
                'status' => array('type' => 'boolean', 'required' => false, 'default' => false) // false returns everything; true only the process info
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $process_identifier = $validated_params['eid'];
        $process_info_only = toBoolean($validated_params['status']);

        // load the object
        $process = \Flexio\Object\Process::load($process_identifier);

        // check the rights on the object
        if ($process->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($process->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // if no wait period is specified, return the information immediately
        if (!isset($validated_params['wait']))
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
        $wait_for_change = $validated_params['wait'];
        self::waitforchangewhilerunning($process->getEid(), $wait_for_change);
        $process = \Flexio\Object\Process::load($process->getEid());
        if ($process->getStatus() === \Model::STATUS_DELETED)
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

    public static function list(\Flexio\Api1\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        // make sure we have an active user
        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // get the processes
        $filter = array('owned_by' => $user->getEid(), 'eid_status' => \Model::STATUS_AVAILABLE);
        $filter = array_merge($params, $filter);
        $processes = \Flexio\Object\Process::list($filter);

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
            $process_info_subset['parent'] = $process_info['parent'] ?? null;
            $process_info_subset['started_by'] = $process_info['started_by'];
            $process_info_subset['started'] = $process_info['started'];
            $process_info_subset['finished'] = $process_info['finished'];
            $process_info_subset['duration'] = $process_info['duration'];
            $process_info_subset['process_status'] = $process_info['process_status'];
            $process_info_subset['owned_by'] = $process_info['owned_by'];
            $process_info_subset['created'] = $process_info['created'];
            $process_info_subset['updated'] = $process_info['updated'];

            $result[] = $process_info_subset;
        }

        return $result;
    }

    public static function log(\Flexio\Api1\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $process_identifier = $validated_params['eid'];
        $background = false;

        // load the object
        $process = \Flexio\Object\Process::load($process_identifier);

        // check the rights on the object
        if ($process->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($process->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $log = $process->getLog();
        return $log;
    }

    public static function run(\Flexio\Api1\Request $request) : array
    {
        $params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $process_identifier = $validated_params['eid'];

        // load the process object
        $process = \Flexio\Object\Process::load($process_identifier);

        // check the rights on the process object
        if ($process->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($process->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_EXECUTE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // create a job engine, attach it to the process object
        $engine = \Flexio\Jobs\StoredProcess::create($process);

        // parse the request content and set the stream info
        $php_stream_handle = fopen('php://input', 'rb');
        $post_content_type = $_SERVER['CONTENT_TYPE'] ?? '';
        \Flexio\Base\Util::addProcessInputFromStream($php_stream_handle, $post_content_type, $engine);

        // run the process
        $engine->run(false  /*true: run in background*/);

        if ($engine->hasError())
        {
            header('Content-Type: application/json', true, 500);
            $content = json_encode($engine->getError());
            echo $content;
            exit(0);
        }

        $stream = $engine->getStdout();
        $stream_info = $stream->get();
        if ($stream_info === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $mime_type = $stream_info['mime_type'];
        $start = 0;
        $limit = PHP_INT_MAX;
        $content = \Flexio\Base\Util::getStreamContents($stream, $start, $limit);
        $response_code = $engine->getResponseCode();

        if ($mime_type !== \Flexio\Base\ContentType::FLEXIO_TABLE)
        {
            // return content as-is
            header('Content-Type: ' . $mime_type, true, $response_code);
        }
        else
        {
            // flexio table; return application/json in place of internal mime
            header('Content-Type: ' . \Flexio\Base\ContentType::JSON, true, $response_code);
            $content = json_encode($content);
        }

        echo($content);
        exit(0);
    }

    public static function cancel(\Flexio\Api1\Request $request) : array
    {
        $params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $process_identifier = $params['eid'];

        // load the object
        $process = \Flexio\Object\Process::load($process_identifier);
        if ($process->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        // if ($process->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_WRITE) === false)
        //     throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        return $process->cancel()->get();
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

        $process = false;
        try
        {
            $process = \Flexio\Object\Process::load($eid);
            if ($process->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        }
        catch (\Flexio\Base\Exception $e)
        {
        }

        if ($process === false)
            return;

        $status_initial = $process->getProcessStatus();

        // if the job is cancelled, failed, or completed, then it's in
        // the final state, so there's nothing to wait for
        if ($status_initial === \Flexio\Jobs\Process::STATUS_CANCELLED ||
            $status_initial === \Flexio\Jobs\Process::STATUS_FAILED ||
            $status_initial === \Flexio\Jobs\Process::STATUS_COMPLETED)
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
}
