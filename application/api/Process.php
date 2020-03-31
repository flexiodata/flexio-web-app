<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
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
    public static function create(\Flexio\Api\Request $request) : void
    {
        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        // note: the parent_eid parameter needs to be an eid because we
        // don't know if it's coming outside the owner namespace; we
        // may need to convert this over to a full URL path that includes
        // owner info
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'parent_eid'   => array('type' => 'eid',     'required' => false),
                'task'         => array('type' => 'object',  'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();
        $process_params = $validated_post_params;
        $pipe_eid = $validated_post_params['parent_eid'] ?? false;

        // note: in pipes and connections, the ability to create is governed by
        // the user; the ability to create a process is governed by execute
        // rights on the pipe; if the process is anonymous, it's goverend by
        // the ability to write to the user, simliar to pipes and connections

        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_PROCESS_CREATE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // TODO: check that user is within usage limits; should this be factored out into a separate object along with rights?
        if ($owner_user->processUsageWithinLimit() === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::RATE_LIMIT_EXCEEDED);

        // check additional rights
        $pipe = false;
        if ($pipe_eid !== false)
        {
            // load the object; make sure the eid is associated with the owner
            // as an additional check; note: the pipe may not have the same
            // owner as the owner that the process is being created for
            $pipe = \Flexio\Object\Pipe::load($pipe_eid);

            // we're getting the logic from the pipe, so we should have read access to
            // the pipe; TODO: verify if this is correct logic
            if ($pipe->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
            if ($pipe->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_PIPE_READ) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);
        }

        if ($pipe !== false)
        {
            // TODO: we have the following two cases; should both of these run
            // with pipe owner privileges or with the base :teamid privileges?
            // right now, we're using pipe owner privileges, but does this make
            // sense for both? the pipe owner is in the path for the first, but
            // the second, the owner of the pipe may be different than the user
            // in the root of the URL
            // POST /:teamid/pipes/:pipeid/processes
            // POST /:teamid/processes?parent_eid=:pipeid

            // if the process is created from a pipe, it runs with pipe owner
            // privileges and inherits the rights from the pipe
            $process_params['parent_eid'] = $pipe->getEid();
            $process_params['owned_by'] = $pipe->getOwner();
            $process_params['created_by'] = $requesting_user_eid;

            // save the pipe info
            $process_params['pipe_info'] = $pipe->get();

            // use the task of the parent, unless a task is specified directly,
            // in which case use the task that's specified to allow runtime
            // parameters to be set on the pipe that override the pipe variables
            // TODO: is this still correct?
            if (!isset($process_params['task']))
                $process_params['task'] = $pipe->getTask();
        }
        else
        {
            // if the process is created independent of a pipe, it runs for
            // the owner user being posted to
            $process_params['owned_by'] = $owner_user_eid;
            $process_params['created_by'] = $requesting_user_eid;
        }

        // create a new process job with the default task;
        // if the process is created with a request from an api token, it's
        // triggered with an api; if there's no api token, it's triggered
        // from a web session, in which case it's triggered by the UI;
        // TODO: this will work until we allow processes to be created from
        // public pipes that don't require a token
        $triggered_by = strlen($request->getToken()) > 0 ? \Model::PROCESS_TRIGGERED_API : \Model::PROCESS_TRIGGERED_INTERFACE;
        $process_params['triggered_by'] = $triggered_by;
        $process = \Flexio\Object\Process::create($process_params);

        // return the process info
        $result = $process->get();
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function get(\Flexio\Api\Request $request) : void
    {
        $query_params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $process_eid = $request->getObjectFromUrl();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($query_params, array(
                'wait'   => array('type' => 'integer', 'required' => false), // how long to block (milliseconds) until a change is detected
                'status' => array('type' => 'boolean', 'required' => false, 'default' => false) // false returns everything; true only the process info
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_query_params = $validator->getParams();
        $milliseconds_to_wait_for_change = $validated_query_params['wait'] ?? 0;
        $process_info_only = toBoolean($validated_query_params['status']);

        // load the object; make sure the eid is associated with the owner
        // as an additional check
        $process = \Flexio\Object\Process::load($process_eid);
        if ($owner_user_eid !== $process->getOwner())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // check the rights on the object
        if ($process->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($process->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_PROCESS_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // wait for any changes, then return the information
        $process->blockUntilStatusChanges($milliseconds_to_wait_for_change);
        if ($process->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        $process_info = $process->get();

        if ($process_info_only === true)
        {
            $result = array();
            $result['eid'] = $process_info['eid'];
            $result['process_status'] = $process_info['process_status'];
            $result['process_info'] = $process_info['process_info'];

            $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
            \Flexio\Api\Response::sendContent($result);
            return;
        }

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($process_info);
        return;
    }

    public static function list(\Flexio\Api\Request $request) : void
    {
        $query_params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($query_params, array(
                'eid_status'  => array(
                    'required' => false,
                    'array' => true, // explode parameter into array, each element of which must satisfy type/enum
                    'type' => 'string',
                    'default' => \Model::STATUS_AVAILABLE,
                    'enum' => [\Model::STATUS_AVAILABLE, \Model::STATUS_PENDING]),
                'parent_eid'  => array(
                    'required' => false,
                    'array' => true, // explode parameter into array, each element of which must satisfy type/enum
                    'type' => 'eid'),
                'start'       => array('type' => 'integer', 'required' => false),
                'tail'        => array('type' => 'integer', 'required' => false),
                'limit'       => array('type' => 'integer', 'required' => false),
                'created_min' => array('type' => 'date',    'required' => false),
                'created_max' => array('type' => 'date',    'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_query_params = $validator->getParams();

        // make sure the owner exists
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // get the processes
        $filter = array('owned_by' => $owner_user_eid);
        $filter = array_merge($validated_query_params, $filter); // only allow items for owner
        $processes = \Flexio\Object\Process::list($filter);

        $result = array();
        foreach ($processes as $p)
        {
            if ($p->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_PROCESS_READ) === false)
                continue;

            $process_info = $p->get();

            $process_info_subset = array();
            $process_info_subset['eid'] = $process_info['eid'];
            $process_info_subset['eid_type'] = $process_info['eid_type'];
            $process_info_subset['eid_status'] = $process_info['eid_status'];
            $process_info_subset['parent'] = $process_info['parent'] ?? null;
            $process_info_subset['triggered_by'] = $process_info['triggered_by'];
            $process_info_subset['started_by'] = $process_info['started_by'];
            $process_info_subset['started'] = $process_info['started'];
            $process_info_subset['finished'] = $process_info['finished'];
            $process_info_subset['duration'] = $process_info['duration'];
            $process_info_subset['process_status'] = $process_info['process_status'];
            $process_info_subset['owned_by'] = $process_info['owned_by'];
            $process_info_subset['created_by'] = $process_info['created_by'];
            $process_info_subset['created'] = $process_info['created'];
            $process_info_subset['updated'] = $process_info['updated'];

            $result[] = $process_info_subset;
        }

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function run(\Flexio\Api\Request $request) : void
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $process_eid = $request->getObjectFromUrl();

        // load the object; make sure the eid is associated with the owner
        // as an additional check
        $process_store = \Flexio\Object\Process::load($process_eid);
        if ($owner_user_eid !== $process_store->getOwner())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($process_store->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // check the rights
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_PROCESS_CREATE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // TODO: check that user is within usage limits; should this be factored out into a separate object along with rights?
        if ($owner_user->processUsageWithinLimit() === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::RATE_LIMIT_EXCEEDED);

        // only allow a process to be run once
        $process_status = $process_store->getProcessStatus();
        if ($process_status !== \Flexio\Jobs\Process::STATUS_PENDING)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::EXECUTE_FAILED);

        // create a new process engine for running a process
        $process_properties = $process_store->get();
        $process_engine = \Flexio\Jobs\Process::create();
        $process_engine->queue('\Flexio\Jobs\ProcessHandler::addMountParams', $process_properties);
        $process_engine->queue('\Flexio\Jobs\Task::run', $process_properties['task']);

        $php_stream_handle = \Flexio\System\System::openPhpInputStream();
        $post_content_type = \Flexio\System\System::getPhpInputStreamContentType();
        \Flexio\Jobs\ProcessHandler::addProcessInputFromStream($php_stream_handle, $post_content_type, $process_engine);

        // create a process host to connect the store/engine and run the process
        $process_host = \Flexio\Jobs\ProcessHost::create($process_store, $process_engine);
        $process_host->run(false  /*true: run in background*/);

        // return the result
        if ($process_engine->hasError())
        {
            $error = $process_engine->getError();
            \Flexio\Api\Response::sendError($error);
            exit(0);
        }

        $stream = $process_engine->getStdout();
        $stream_info = $stream->get();
        if ($stream_info === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $mime_type = $stream_info['mime_type'];
        $start = 0;
        $limit = PHP_INT_MAX;
        $content = \Flexio\Base\StreamUtil::getStreamContents($stream, $start, $limit);
        $response_code = $process_engine->getResponseCode();

        if ($mime_type !== \Flexio\Base\ContentType::FLEXIO_TABLE)
        {
            // return content as-is
            header('Content-Type: ' . $mime_type, true, $response_code);
        }
        else
        {
            // flexio table; return application/json in place of internal mime
            header('Content-Type: ' . \Flexio\Base\ContentType::JSON, true, $response_code);
            $content = json_encode($content, JSON_UNESCAPED_SLASHES);
        }

        \Flexio\Api\Response::sendRaw($content);
    }

    public static function exec(\Flexio\Api\Request $request) : void
    {
        // EXPERIMENTAL API endpoint: creates and runs a process straight from code
        $query_params = $request->getQueryParams();
        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        // query/posted parameters are params used in execute job; TODO: these should be
        // validated using execute job validator for consistency; for now, check
        // the params separately
        $params = array_merge($query_params, $post_params);
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'lang'         => array('type' => 'string',  'required' => false),
                'code'         => array('type' => 'string',  'required' => false),
                'path'         => array('type' => 'string',  'required' => false),
                'integrity'    => array('type' => 'string',  'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_params = $validator->getParams();
        $execute_job_params = $validated_params;
        $execute_job_params['op'] = 'execute'; // set the execute operation so this doesn't need to be supplied

        // create a new process job with the default task;
        // if the process is created with a request from an api token, it's
        // triggered with an api; if there's no api token, it's triggered
        // from a web session, in which case it's triggered by the UI;
        // TODO: this will work until we allow processes to be created from
        // public pipes that don't require a token
        $triggered_by = strlen($request->getToken()) > 0 ? \Model::PROCESS_TRIGGERED_API : \Model::PROCESS_TRIGGERED_INTERFACE;

        // check the rights on the owner
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_PROCESS_CREATE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // TODO: check that user is within usage limits; should this be factored out into a separate object along with rights?
        if ($owner_user->processUsageWithinLimit() === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::RATE_LIMIT_EXCEEDED);

        // create a new process object for storing process info;
        // set the owner based on the owner being posted to
        $process_params = array();
        $process_params['owned_by'] = $owner_user_eid;
        $process_params['created_by'] = $requesting_user_eid;
        $process_params['triggered_by'] = $triggered_by;
        $process_params['task'] = [
            "op" => "sequence",
            "items" => [
                $execute_job_params
            ]
        ];
        $process_store = \Flexio\Object\Process::create($process_params);

        // create a new process engine for running a process
        $process_engine = \Flexio\Jobs\Process::create($process);

        // NOTE: disabled, because posted parameters contain the logic, not the
        // parameters to run against; re-enable if posted info changes to
        // include data to process
        // parse the request content and set the stream info
        //$php_stream_handle = \Flexio\System\System::openPhpInputStream();
        //$post_content_type = \Flexio\System\System::getPhpInputStreamContentType();
        //\Flexio\Jobs\ProcessHandler::addProcessInputFromStream($php_stream_handle, $post_content_type, $process_engine);

        // create a process host to connect the store/engine and run the process
        $process_host = \Flexio\Jobs\ProcessHost::create($process_store, $process_engine);
        $process_host->run(false  /*true: run in background*/);

        // return the result
        if ($process_engine->hasError())
        {
            $error = $process_engine->getError();
            \Flexio\Api\Response::sendError($error);
            exit(0);
        }

        $stream = $process_engine->getStdout();
        $stream_info = $stream->get();
        if ($stream_info === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $mime_type = $stream_info['mime_type'];
        $start = 0;
        $limit = PHP_INT_MAX;
        $content = \Flexio\Base\StreamUtil::getStreamContents($stream, $start, $limit);
        $response_code = $process_engine->getResponseCode();

        if ($mime_type !== \Flexio\Base\ContentType::FLEXIO_TABLE)
        {
            // return content as-is
            header('Content-Type: ' . $mime_type, true, $response_code);
        }
        else
        {
            // flexio table; return application/json in place of internal mime
            header('Content-Type: ' . \Flexio\Base\ContentType::JSON, true, $response_code);
            $content = json_encode($content, JSON_UNESCAPED_SLASHES);
        }

        \Flexio\Api\Response::sendRaw($content);
    }
}
