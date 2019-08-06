<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
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
                'short_description' => array('type' => 'string', 'required' => false),
                'description'     => array('type' => 'string', 'required' => false),
                'ui'              => array('type' => 'object', 'required' => false),
                'task'            => array('type' => 'object', 'required' => false),
                'schedule'        => array('type' => 'object', 'required' => false),
                'deploy_mode'     => array('type' => 'string', 'required' => false),
                'deploy_schedule' => array('type' => 'string', 'required' => false),
                'deploy_email'    => array('type' => 'string', 'required' => false),
                'deploy_api'      => array('type' => 'string', 'required' => false),
                'deploy_ui'       => array('type' => 'string', 'required' => false)
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
        $result = self::cleanProperties($properties);
        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    public static function copy(\Flexio\Api\Request $request) : void
    {
        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        $request->track(\Flexio\Api\Action::TYPE_PIPE_CREATE);
        $request->setRequestParams($post_params);

        // note: the copy_eid parameter needs to be an eid because we
        // don't know if it's coming outside the owner namespace; we
        // may need to convert this over to a full URL path that includes
        // owner info
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'copy_eid' => array('type' => 'eid', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();
        $original_pipe_eid = $validated_post_params['copy_eid'];

        // check the rights on the owner
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_PIPE_CREATE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // load the pipe
        $original_pipe = \Flexio\Object\Pipe::load($original_pipe_eid);

        // check the rights on the pipe being read from
        if ($original_pipe->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($original_pipe->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_PIPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // create a new pipe; copy the logic, but not the status, scheduling, etc
        $original_pipe_properties = $original_pipe->get();

        $new_pipe_properties = array();
        $new_pipe_properties['name'] = $original_pipe_properties['name'] . ' copy';
        $new_pipe_properties['description'] = $original_pipe_properties['description'];
        $new_pipe_properties['ui'] = $original_pipe_properties['ui'];
        $new_pipe_properties['task'] = $original_pipe_properties['task'];
        $new_pipe_properties['owned_by'] = $owner_user_eid;
        $new_pipe_properties['created_by'] = $requesting_user_eid;
        $new_pipe = \Flexio\Object\Pipe::create($new_pipe_properties);

        $properties = $new_pipe->get();
        $result = self::cleanProperties($properties);
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
        $result = self::cleanProperties($properties);
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
            if ($pipe->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
            if ($pipe->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_PIPE_DELETE) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

            $pipes_to_delete[] = $pipe;
        }

        // if everything is good, delete all the pipes at the same time
        $result = array();
        foreach ($pipes_to_delete as $pipe)
        {
            $pipe->delete();
            $properties = $pipe->get();
            $result[] = self::cleanProperties($properties);
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
                'short_description' => array('type' => 'string', 'required' => false),
                'description'     => array('type' => 'string', 'required' => false),
                'ui'              => array('type' => 'object', 'required' => false),
                'task'            => array('type' => 'object', 'required' => false),
                'schedule'        => array('type' => 'object', 'required' => false),
                'deploy_mode'     => array('type' => 'string', 'required' => false),
                'deploy_schedule' => array('type' => 'string', 'required' => false),
                'deploy_email'    => array('type' => 'string', 'required' => false),
                'deploy_api'      => array('type' => 'string', 'required' => false),
                'deploy_ui'       => array('type' => 'string', 'required' => false)
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
        $result = self::cleanProperties($properties);
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
        $result = self::cleanProperties($properties);
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

        $filter = array('owned_by' => $owner_user_eid, 'eid_status' => \Model::STATUS_AVAILABLE);
        $filter = array_merge($validated_query_params, $filter); // give precedence to fixed owner/status
        $pipes = \Flexio\Object\Pipe::list($filter);

        foreach ($pipes as $p)
        {
            if ($p->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_PIPE_READ) === false)
                continue;

            $properties = $p->get();
            $result[] = self::cleanProperties($properties);
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
             throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // if the process is created with a request from an api token, it's
        // triggered with an api; if there's no api token, it's triggered
        // from a web session, in which case it's triggered by the UI;
        // TODO: this will work until we allow processes to be created from
        // public pipes that don't require a token
        $triggered_by = strlen($request->getToken()) > 0 ? \Model::PROCESS_TRIGGERED_API : \Model::PROCESS_TRIGGERED_INTERFACE;

        // get the pipe properties
        $pipe_properties = $pipe->get();

        // only allow pipes to be triggered from an API call if the pipe is deployed
        // and the api deployment option is activated
        $api_trigger_active = ($pipe_properties['deploy_mode'] === \Model::PIPE_DEPLOY_MODE_RUN &&
                               $pipe_properties['deploy_api'] === \Model::PIPE_DEPLOY_STATUS_ACTIVE);
        if ($triggered_by === \Model::PROCESS_TRIGGERED_API && $api_trigger_active === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // create a new process
        $process_properties = array(
            'parent_eid' => $pipe_properties['eid'],
            'pipe_info' => $pipe_properties,
            'task' => $pipe_properties['task'],
            'triggered_by' => $triggered_by,
            'owned_by' => $pipe_properties['owned_by']['eid'], // same as $owner_user_eid
            'created_by' => $requesting_user_eid
        );
        $process = \Flexio\Object\Process::create($process_properties);

        // create a job engine, attach it to the process object
        $engine = \Flexio\Jobs\StoredProcess::create($process);

        // parse the request content and set the stream info
        $php_stream_handle = fopen('php://input', 'rb');
        $post_content_type = $_SERVER['CONTENT_TYPE'] ?? '';
         \Flexio\Base\Util::addProcessInputFromStream($php_stream_handle, $post_content_type, $engine);

        // run the process
        $engine->run(false /*true: run in background*/);

        if ($engine->hasError())
        {
            $error = $engine->getError();
            \Flexio\Api\Response::sendError($error);
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
            $content = json_encode($content, JSON_UNESCAPED_SLASHES);
        }

        \Flexio\Api\Response::sendRaw($content);
    }


    // using json_encode/decode() on arrays leads to ambiguities
    // in the case of empty arrays, which should actually be objects,
    // but get translated into empty arrays; this code ensures
    // that certain values are kept as objects

    private static function doTaskCasting(array &$a)
    {
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

    private static function cleanProperties(array $properties) : array
    {
        if (isset($properties['task']) && count($properties['task']) === 0)
            $properties['task'] = (object)$properties['task'];

        if (isset($properties['task']) && is_array($properties['task']))
        {
            self::doTaskCasting($properties['task']);
        }

        if (isset($properties['ui']) && count($properties['ui']) === 0)
            $properties['ui'] = (object)$properties['ui'];

        if (isset($properties['ui']) && is_array($properties['ui']))
        {
            self::doTaskCasting($properties['ui']);
        }

        return $properties;
    }
}
