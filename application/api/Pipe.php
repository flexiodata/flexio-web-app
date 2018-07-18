<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
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
                'alias'           => array('type' => 'alias',  'required' => false),
                'name'            => array('type' => 'string', 'required' => false),
                'description'     => array('type' => 'string', 'required' => false),
                'ui'              => array('type' => 'object', 'required' => false),
                'task'            => array('type' => 'object', 'required' => false),
                'pipe_mode'       => array('type' => 'string', 'required' => false),
                'schedule'        => array('type' => 'object', 'required' => false),
                'schedule_status' => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_post_params = $validator->getParams();

        // check the rights on the owner; ability to create an object is governed
        // currently by user write privileges
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // create the object
        $pipe_properties = $validated_post_params;
        $pipe_properties['owned_by'] = $owner_user_eid;
        $pipe_properties['created_by'] = $requesting_user_eid;
        $pipe = \Flexio\Object\Pipe::create($pipe_properties);

        // grant default rights to the owner
        $pipe->grant($owner_user_eid, \Model::ACCESS_CODE_TYPE_EID,
            array(
                \Flexio\Object\Right::TYPE_READ_RIGHTS,
                \Flexio\Object\Right::TYPE_WRITE_RIGHTS,
                \Flexio\Object\Right::TYPE_READ,
                \Flexio\Object\Right::TYPE_WRITE,
                \Flexio\Object\Right::TYPE_DELETE,
                \Flexio\Object\Right::TYPE_EXECUTE
            )
        );

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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_post_params = $validator->getParams();
        $original_pipe_eid = $validated_post_params['copy_eid'];

        // check the rights on the owner; ability to create an object is governed
        // currently by user write privileges
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // load the pipe
        $original_pipe = \Flexio\Object\Pipe::load($original_pipe_eid);

        // check the rights on the pipe being read from
        if ($original_pipe->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($original_pipe->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
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

        $new_pipe->grant($owner_user_eid, \Model::ACCESS_CODE_TYPE_EID,
            array(
                \Flexio\Object\Right::TYPE_READ_RIGHTS,
                \Flexio\Object\Right::TYPE_WRITE_RIGHTS,
                \Flexio\Object\Right::TYPE_READ,
                \Flexio\Object\Right::TYPE_WRITE,
                \Flexio\Object\Right::TYPE_DELETE,
                \Flexio\Object\Right::TYPE_EXECUTE
            )
        );

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
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_DELETE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $pipe->delete();

        $properties = $pipe->get();
        $result = self::cleanProperties($properties);
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
                'alias'           => array('type' => 'alias',  'required' => false),
                'name'            => array('type' => 'string', 'required' => false),
                'description'     => array('type' => 'string', 'required' => false),
                'ui'              => array('type' => 'object', 'required' => false),
                'task'            => array('type' => 'object', 'required' => false),
                'pipe_mode'       => array('type' => 'string', 'required' => false),
                'schedule'        => array('type' => 'object', 'required' => false),
                'schedule_status' => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_post_params = $validator->getParams();

        // load the object; make sure the eid is associated with the owner
        // as an additional check
        $pipe = \Flexio\Object\Pipe::load($pipe_eid);
        if ($owner_user_eid !== $pipe->getOwner())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // check the rights on the object
        if ($pipe->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
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
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

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
            if ($p->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
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
        $request->track(\Flexio\Api\Action::TYPE_PIPE_RUN);
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
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_EXECUTE) === false)
             throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // create a new process
        $pipe_properties = $pipe->get();
        $process_properties = array(
            'parent_eid' => $pipe_properties['eid'],
            'task' => $pipe_properties['task'],
            'owned_by' => $pipe_properties['owned_by']['eid'], // same as $owner_user_eid
            'created_by' => $requesting_user_eid
        );
        $process = \Flexio\Object\Process::create($process_properties);
        $process->setRights($pipe->getRights());

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
            $content = json_encode($content);
        }

        echo($content);
        exit(0);
    }

    private static function cleanProperties(array $properties) : array
    {
        if (isset($properties['task']) && count($properties['task']) === 0)
            $properties['task'] = (object)$properties['task'];

        return $properties;
    }
}
