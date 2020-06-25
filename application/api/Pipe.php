<?php
/**
 *
 * Copyright (c) 2015, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-10-23
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class Pipe
{
    public static function create(\Flexio\Api\Request $request)  : void
    {
        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        // if the copy_eid parameter is set, then copy the pipe,
        // using the original parameters; this simply allows us to reuse
        // the create api call, even though the two functions are distinct
        if (isset($post_params['copy_eid']))
        {
            self::copy($request);
            return;
        }

        // start tracking the request after copy() since copy() may be called
        // separately and has it's own tracking function
        $request->track(\Flexio\Api\Action::TYPE_PIPE_CREATE);
        $request->setRequestParams($post_params);

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'copy_eid'        => array('type' => 'eid',    'required' => false),
                'eid_status'      => array('type' => 'string', 'required' => false),
                'name'            => array('type' => 'identifier',  'required' => false),
                'title'           => array('type' => 'string', 'required' => false),
                'icon'            => array('type' => 'string', 'required' => false),
                'description'     => array('type' => 'string', 'required' => false),
                'examples'        => array('type' => 'object', 'required' => false),
                'params'          => array('type' => 'object', 'required' => false),
                'returns'         => array('type' => 'object', 'required' => false),
                'notes'           => array('type' => 'string', 'required' => false),
                'task'            => array('type' => 'object', 'required' => false),
                'schedule'        => array('type' => 'object', 'required' => false),
                'run_mode'        => array('type' => 'string', 'required' => false),
                'deploy_mode'     => array('type' => 'string', 'required' => false),
                'deploy_schedule' => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();

        // check the rights on the owner; ability to create an object is governed
        // currently by user write privileges
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_PIPE_CREATE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // create the object
        $pipe_properties = $validated_post_params;
        $pipe_properties['owned_by'] = $owner_user_eid;
        $pipe_properties['created_by'] = $requesting_user_eid;
        $pipe = \Flexio\Object\Pipe::create($pipe_properties);

        // get the pipe properties
        $properties = $pipe->get();
        $result = self::cleanProperties($properties, $request);
        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    public static function delete(\Flexio\Api\Request $request) : void
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $pipe_eid = $request->getObjectFromUrl();

        $request->track(\Flexio\Api\Action::TYPE_PIPE_DELETE);

        // load the object; make sure the eid is associated with the owner
        // as an additional check
        $pipe = \Flexio\Object\Pipe::load($pipe_eid);
        if ($owner_user_eid !== $pipe->getOwner())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // check the rights on the object
        if ($pipe->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($pipe->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_PIPE_DELETE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $pipe->delete();

        $properties = $pipe->get();
        $result = self::cleanProperties($properties, $request);
        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    public static function bulkdelete(\Flexio\Api\Request $request) : void
    {
        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        $request->track(\Flexio\Api\Action::TYPE_PIPE_DELETE);

        // for bulk delete, incoming form is an array of objects to delete in
        // the following format:
        // [{"eid": ""}, {"eid": ""}, ...]

        // make sure we have an array of at least one item to delete
        $pipes_eids_to_delete = $post_params;
        if (!is_array($pipes_eids_to_delete))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
        if (count($pipes_eids_to_delete) === 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // load the pipes to delete
        $pipes_to_delete = array();
        foreach ($pipes_eids_to_delete as $p)
        {
            $pipe_eid = $p['eid'] ?? false;
            if ($pipe_eid === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            $pipe = \Flexio\Object\Pipe::load($pipe_eid);
            if ($owner_user_eid !== $pipe->getOwner())
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            // check the rights on the object
            // in bulk, don't throw an error if a pipe is already deleted; just take
            // the list and do the operation silently
            // if ($pipe->getStatus() === \Model::STATUS_DELETED)
            //     throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
            if ($pipe->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_PIPE_DELETE) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

            $pipes_to_delete[] = $pipe;
        }

        // if everything is good, delete all the pipes at the same time;
        // only return the list of pipes that were deleted
        $result = array();
        foreach ($pipes_to_delete as $pipe)
        {
            $pipe->delete();
            $properties = $pipe->get();
            $result[] = array('eid' => $pipe->getEid());
        }

        // send the response
        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    public static function set(\Flexio\Api\Request $request) : void
    {
        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $pipe_eid = $request->getObjectFromUrl();

        $request->track(\Flexio\Api\Action::TYPE_PIPE_UPDATE);
        $request->setRequestParams($post_params);

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'eid_status'      => array('type' => 'string', 'required' => false),
                'name'            => array('type' => 'identifier',  'required' => false),
                'title'           => array('type' => 'string', 'required' => false),
                'icon'            => array('type' => 'string', 'required' => false),
                'description'     => array('type' => 'string', 'required' => false),
                'examples'        => array('type' => 'object', 'required' => false),
                'params'          => array('type' => 'object', 'required' => false),
                'returns'         => array('type' => 'object', 'required' => false),
                'notes'           => array('type' => 'string', 'required' => false),
                'task'            => array('type' => 'object', 'required' => false),
                'schedule'        => array('type' => 'object', 'required' => false),
                'run_mode'        => array('type' => 'string', 'required' => false),
                'deploy_mode'     => array('type' => 'string', 'required' => false),
                'deploy_schedule' => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();

        // load the object; make sure the eid is associated with the owner
        // as an additional check
        $pipe = \Flexio\Object\Pipe::load($pipe_eid);
        if ($owner_user_eid !== $pipe->getOwner())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // check the rights on the object
        if ($pipe->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($pipe->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_PIPE_UPDATE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // set the properties
        $pipe->set($validated_post_params);

        $properties = $pipe->get();
        $result = self::cleanProperties($properties, $request);
        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    public static function get(\Flexio\Api\Request $request) : void
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $pipe_eid = $request->getObjectFromUrl();

        // load the object; make sure the eid is associated with the owner
        // as an additional check
        $pipe = \Flexio\Object\Pipe::load($pipe_eid);
        if ($owner_user_eid !== $pipe->getOwner())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // check the rights on the object
        if ($pipe->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($pipe->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_PIPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the properties
        $properties = $pipe->get();
        $result = self::cleanProperties($properties, $request);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
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
                    'enum' => [\Model::STATUS_AVAILABLE, \Model::STATUS_PENDING, \Model::STATUS_UPDATING]),
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

        // get the pipes
        $result = array();

        $filter = array('owned_by' => $owner_user_eid);
        $filter = array_merge($validated_query_params, $filter); // only allow items for owner
        $pipes = \Flexio\Object\Pipe::list($filter);

        foreach ($pipes as $p)
        {
            if ($p->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_PIPE_READ) === false)
                continue;

            $properties = $p->get();

            // TODO: remove 'task' from pipe list to prepare for getting pipe
            // content from a separate call; leave in pipe item get() function
            unset($properties['task']);

            $result[] = self::cleanProperties($properties, $request);
        }

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function run(\Flexio\Api\Request $request) : void
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $pipe_eid = $request->getObjectFromUrl();

/*
        $request->track(\Flexio\Api\Action::TYPE_PROCESS_CREATE);
        $request->setRequestParams($post_params);
*/

        // load the object; make sure the eid is associated with the owner
        // as an additional check
        $pipe = \Flexio\Object\Pipe::load($pipe_eid);
        if ($owner_user_eid !== $pipe->getOwner())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // check the rights on the pipe object
        if ($pipe->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($pipe->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_PROCESS_CREATE) === false)
        {
            if (self::authenticatorAllowsExecute($request) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);
        }

        // TODO: check that user is within usage limits; should this be factored out into a separate object along with rights?
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->processUsageWithinLimit() === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::RATE_LIMIT_EXCEEDED);

        // if the process is created with a request from an api token, it's
        // triggered with an api; if there's no api token, it's triggered
        // from a web session, in which case it's triggered by the UI;
        // TODO: this will work until we allow processes to be created from
        // public pipes that don't require a token
        $triggered_by = strlen($request->getToken()) > 0 ? \Model::PROCESS_TRIGGERED_API : \Model::PROCESS_TRIGGERED_INTERFACE;

        // get the pipe properties
        $pipe_properties = $pipe->get();

        // only allow pipes to be triggered from an API call if the pipe is active
        $api_trigger_active = ($pipe_properties['deploy_mode'] === \Model::PIPE_DEPLOY_MODE_RUN);
        if ($triggered_by === \Model::PROCESS_TRIGGERED_API && $api_trigger_active === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // create a new process; run what's there by default (pass-through mode);
        // if we're in index mode, create a special search task
        $pipe_run_mode = $pipe_properties['run_mode'] ?? \Model::PIPE_RUN_MODE_PASSTHROUGH;
        $process_properties = array(
            'parent_eid' => $pipe_properties['eid'],
            'pipe_info' => $pipe_properties,
            'task' => $pipe_properties['task'],
            'triggered_by' => $triggered_by,
            'owned_by' => $pipe_properties['owned_by']['eid'], // same as $owner_user_eid
            'created_by' => $requesting_user_eid
        );
        if ($pipe_run_mode === \Model::PIPE_RUN_MODE_INDEX)
        {
            $search_task = array(
                "op" => "search",
                "index" => $pipe_properties['eid'],
                'structure' => $pipe_properties['returns'] // optional structure parameter that's used for getting available columns (not columns to actually return)
            );
            $process_properties['task'] = $search_task;
        }

        // create a new process object for storing process info
        $process_store = \Flexio\Object\Process::create($process_properties);

        // EXPERIMENTAL (LINKED PIPE): integrations can be based on a team; in these cases, the
        // pipes in the integration point to the pipes in the team and the request/results need
        // to be proxied; this allows the team to track the usage as well as allow any indexes
        // powering the team pipes to be utilized; to track this type of case, the task has a
        // special type of "op" called "redirect" along with a "path" parameter specifying the
        // redirect; note: we could use the job engine to process these, but it's more efficient
        // to stream the result; to still track the job in the local list of processes, create
        // the object process but don't use the process engine
        $op = $process_properties['task']['op'] ?? '';
        if ($op === 'redirect')
        {
            try
            {
                // we want make a request as the current user; generate a token representing
                // the user and make an api call with the token
                $token = \Flexio\Object\User::generateTokenFromUserEid($requesting_user_eid);

                // pass the pipe where the request is coming from; this will be used by the
                // api to load the mount info; note: if we want to isolate the params, we
                // could package them up with the body info as well, but this should work
                // for now; TODO: review
                $x_flexio_caller = $pipe_eid;

                // make the request
                $client = new \GuzzleHttp\Client(['verify' => false]);
                $php_stream_handle = \Flexio\System\System::openPhpInputStream(); // pass on input post
                $path = $process_properties['task']['path'] ?? '';
                $content = [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Accept'        => 'application/json',
                        'x-flexio-caller' => $x_flexio_caller
                    ],
                    'body' => $php_stream_handle
                ];
                $options = array('stream' => true);
                $request = $client->request('POST', $path, $content, $options);

                // process the response
                $stream = $request->getBody();
                while (!$stream->eof())
                {
                    $line = $stream->read(4096);
                    echo($line);
                }
                exit(0);
            }
            catch (\Flexio\Base\Exception | \Exception | \Error $e)
            {
                // note: we don't want to pass on any error information
                // from the proxy request, so catch everything and raise a
                // new exception
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
            }
        }

        // create a new process engine for running a process

        // mounts currently have two types of parameters:
        // 1. parameters that are part of the configuration settings as fixed values
        // 2. parameters that are entered by the user when they set up the mount
        // currently, #1 is general purpose for both search and custom execute
        // and #2 is only related to running scripts; in the following, always
        // add the items from #1 to the process, but only add #2 to the process if
        // it's an execute job to save a little bit of work
        $process_engine = \Flexio\Jobs\Process::create();
        $process_engine->queue('\Flexio\Jobs\ProcessHandler::addMountConfig', $process_properties);
        if ($pipe_run_mode == \Model::PIPE_RUN_MODE_PASSTHROUGH)
            $process_engine->queue('\Flexio\Jobs\ProcessHandler::addMountParams', $process_properties);
        $process_engine->queue('\Flexio\Jobs\Task::run', $process_properties['task']);

        $php_stream_handle = \Flexio\System\System::openPhpInputStream();
        $post_content_type = \Flexio\System\System::getPhpInputStreamContentType();
        \Flexio\Jobs\ProcessHandler::addProcessInputFromStream($php_stream_handle, $post_content_type, $process_engine);

        // create a process host to connect the store/engine and run the process
        $process_host = \Flexio\Jobs\ProcessHost::create($process_store, $process_engine);

        // run the job; only increment/decrement the process count if we're running in passthrough;
        // do this manually so that the increment/decrement happen regardless of whether or not
        // the process has an error
        if (!IS_DEBUG() && $pipe_run_mode === \Model::PIPE_RUN_MODE_PASSTHROUGH)
            \Flexio\Jobs\ProcessHandler::incrementProcessCount($process_engine, array());

        $process_host->run(false /*true: run in background*/);

        if (!IS_DEBUG() && $pipe_run_mode === \Model::PIPE_RUN_MODE_PASSTHROUGH)
            \Flexio\Jobs\ProcessHandler::decrementProcessCount($process_engine, array());

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

        // send headers
        $mime_type = $stream_info['mime_type'];
        $response_code = $process_engine->getResponseCode();
        \Flexio\Api\Response::setDefaultHeaders($mime_type, $response_code);

        // send the content
        $reader = $stream->getReader();
        while (($content = $reader->read(4096)) !== false)
        {
            echo($content);
        }

        exit(0);
    }

    public static function populatecache(\Flexio\Api\Request $request) : void
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $pipe_eid = $request->getObjectFromUrl();

        // experimental/test function for populating an elasticsearch cache
        // from the output of a pipe; this cache then be queried via another
        // endpoint
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

/*
        $request->track(\Flexio\Api\Action::TYPE_PROCESS_CREATE);
        $request->setRequestParams($post_params);
*/

        // load the object; make sure the eid is associated with the owner
        // as an additional check
        $pipe = \Flexio\Object\Pipe::load($pipe_eid);
        if ($owner_user_eid !== $pipe->getOwner())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // check the rights on the pipe object
        if ($pipe->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($pipe->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_PROCESS_CREATE) === false)
             throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // TODO: check that user is within usage limits; should this be factored out into a separate object along with rights?
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->processUsageWithinLimit() === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::RATE_LIMIT_EXCEEDED);

        // if the process is created with a request from an api token, it's
        // triggered with an api; if there's no api token, it's triggered
        // from a web session, in which case it's triggered by the UI;
        // TODO: this will work until we allow processes to be created from
        // public pipes that don't require a token
        $triggered_by = strlen($request->getToken()) > 0 ? \Model::PROCESS_TRIGGERED_API : \Model::PROCESS_TRIGGERED_INTERFACE;

        // get the pipe properties
        $pipe_properties = $pipe->get();

        // only allow pipes to be triggered from an API call if the pipe is active
        $api_trigger_active = ($pipe_properties['deploy_mode'] === \Model::PIPE_DEPLOY_MODE_RUN);
        if ($triggered_by === \Model::PROCESS_TRIGGERED_API && $api_trigger_active === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // create a new process object for storing process info
        $process_properties = array(
            'parent_eid' => $pipe_properties['eid'],
            'pipe_info' => $pipe_properties,
            'task' => $pipe_properties['task'],
            'triggered_by' => $triggered_by,
            'owned_by' => $pipe_properties['owned_by']['eid'], // same as $owner_user_eid
            'created_by' => $requesting_user_eid
        );
        $process_store = \Flexio\Object\Process::create($process_properties);

        // create a new process engine for running a process
        $elastic_search_params = array(
            'index' => $pipe_properties['eid']
        );
        $process_engine = \Flexio\Jobs\Process::create();
        $process_engine->queue('\Flexio\Jobs\ProcessHandler::addMountParams', $process_properties);
        $process_engine->queue('\Flexio\Jobs\Task::run', $process_properties['task']);
        if ($pipe_properties['run_mode'] === \Model::PIPE_RUN_MODE_INDEX)
            $process_engine->queue('\Flexio\Jobs\ProcessHandler::saveStdoutToElasticSearch', $elastic_search_params);

        $php_stream_handle = \Flexio\System\System::openPhpInputStream();
        $post_content_type = \Flexio\System\System::getPhpInputStreamContentType();
        \Flexio\Jobs\ProcessHandler::addProcessInputFromStream($php_stream_handle, $post_content_type, $process_engine);

        // create a process host to connect the store/engine and run the process
        $process_host = \Flexio\Jobs\ProcessHost::create($process_store, $process_engine);
        $process_host->run(true /*true: run in background*/);

        // return information about the process
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($process_store->get());
    }

    public static function runFromEid(string $pipe_eid) : void
    {
        // runs a pipe from an eid instead of an api endpoint

        // load the pipe
        $pipe_properties = false;
        try
        {
            $pipe = \Flexio\Object\Pipe::load($pipe_eid);
            if ($pipe->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            // TODO: check that user is within usage limits; should this be factored out into a separate object along with rights?
            // note: use here is primarily to avoid running pipes for users whose trials have expired and not
            // so much a limitation on total process executions
            $owner_user_eid = $pipe->getOwner();
            $owner_user = \Flexio\Object\User::load($owner_user_eid);
            if ($owner_user->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
            if ($owner_user->processUsageWithinLimit() === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::RATE_LIMIT_EXCEEDED);

            $pipe_properties = $pipe->get();
        }
        catch (\Flexio\Base\Exception $e)
        {
        }

        // if we couldn't get the pipe properties, don't run the pipe
        if ($pipe_properties === false)
            return;

        // create a new process object for storing process info
        $process_properties = array(
            'parent_eid' => $pipe_properties['eid'],
            'pipe_info' => $pipe_properties,
            'task' => $pipe_properties['task'],
            'triggered_by' => \Model::PROCESS_TRIGGERED_SCHEDULER,
            'owned_by' => $pipe_properties['owned_by']['eid'],
            'created_by' => $pipe_properties['owned_by']['eid'] // scheduled processes are created by the owner
        );
        $process_store = \Flexio\Object\Process::create($process_properties);

        // create a new process engine for running a process
        $elastic_search_params = array(
            'index' => $pipe_properties['eid']
        );
        $process_engine = \Flexio\Jobs\Process::create();
        $process_engine->queue('\Flexio\Jobs\ProcessHandler::addMountParams', $process_properties);
        $process_engine->queue('\Flexio\Jobs\Task::run', $process_properties['task']);
        if ($pipe_properties['run_mode'] === \Model::PIPE_RUN_MODE_INDEX)
            $process_engine->queue('\Flexio\Jobs\ProcessHandler::saveStdoutToElasticSearch', $elastic_search_params);

        // create a process host to connect the store/engine and run the process
        $process_host = \Flexio\Jobs\ProcessHost::create($process_store, $process_engine);
        $process_host->run(true /*true: run in background*/);
    }

    private static function authenticatorAllowsExecute(\Flexio\Api\Request $request) : bool
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $pipe_eid = $request->getObjectFromUrl();

        // if the pipe normally can't be run by the requesting user, see if there's
        // a special authentication function defined as part of any mount this pipe
        // is part of, and if so, perform additional rights checking using a custom
        // authentication function and information passed by the caller; if there's
        // no function, don't allow execution
        $authentication_function_name = \Flexio\Jobs\ProcessHandler::getMountAuthenticator($requesting_user_eid, $pipe_eid);
        if (!isset($authentication_function_name))
            return false;

        // we have an authentication function, so perform rights checking using
        // it and information passed by the caller

        // get information from the caller
        $caller_mount_params = array();
        $request_header_params = $request->getHeaderParams();
        if (isset($request_header_params['x-flexio-caller']))
        {
            // get the calling pipe mount params; note: calling user and current
            // requesting user are the same because we authenticated the proxied
            // call with the calling user
            $caller_pipe_eid = $request_header_params['x-flexio-caller'];
            $caller_mount_params = \Flexio\Jobs\ProcessHandler::getMountParams($requesting_user_eid, $caller_pipe_eid);
        }

        try
        {
            $authentication_function_eid = \Flexio\Object\Pipe::getEidFromName($owner_user_eid, $authentication_function_name);
            if ($authentication_function_eid === false)
                return false;

            $authentication_function = \Flexio\Object\Pipe::load($authentication_function_eid);
            $authentication_function_info = $authentication_function->get();
            $authentication_function_task = $authentication_function_info['task'];

            $authentication_process_engine = \Flexio\Jobs\Process::create();
            $authentication_process_engine->setOwner($requesting_user_eid);
            $authentication_process_engine->setParams($caller_mount_params);
            $authentication_process_engine->execute($authentication_function_task);

            if ($authentication_process_engine->hasError())
                return false;
        }
        catch (\Flexio\Base\Exception $e)
        {
            // if there are any errors, don't allow execution
            return false;
        }

        return true;
    }

    private static function doTaskCasting(array &$a)
    {
        // using json_encode/decode() on arrays leads to ambiguities
        // in the case of empty arrays, which should actually be objects,
        // but get translated into empty arrays; this code ensures
        // that certain values are kept as objects

        if (($a['op'] ?? '') == 'request')
        {
            if (isset($a['headers']) && is_array($a['headers']) && count($a['headers']) == 0)
            {
                $a['headers'] = (object)$a['headers'];
            }

            if (isset($a['data']) && is_array($a['data']) && count($a['data']) == 0)
            {
                $a['data'] = (object)$a['data'];
            }
        }

        foreach ($a as $k => $v)
        {
            if (is_array($v))
            {
                self::doTaskCasting($a[$k]);
            }
        }
    }

    private static function cleanProperties(array $properties, \Flexio\Api\Request $request) : array
    {
        if (isset($properties['task']) && count($properties['task']) === 0)
            $properties['task'] = (object)$properties['task'];

        if (isset($properties['task']) && is_array($properties['task']))
        {
            self::doTaskCasting($properties['task']);
        }

        return $properties;
    }
}
