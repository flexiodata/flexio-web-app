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
namespace Flexio\Api1;


class Pipe
{
    public static function create(\Flexio\Api1\Request $request) : array
    {
        $params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'copy_eid'        => array('type' => 'identifier', 'required' => false),
                'eid_status'      => array('type' => 'string', 'required' => false),
                'ename'           => array('type' => 'identifier', 'required' => false),
                'name'            => array('type' => 'string', 'required' => false),
                'description'     => array('type' => 'string', 'required' => false),
                'task'            => array('type' => 'object', 'required' => false),
                'schedule'        => array('type' => 'object', 'required' => false),
                'schedule_status' => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();

        // if the copy_eid parameter is set, then copy the pipe,
        // using the original parameters; this simply allows us to reuse
        // the create api call, even though the two functions are distinct
        if (isset($validated_params['copy_eid']))
            return self::copy($request);

        // create the object
        $pipe_properties = $validated_params;
        $pipe_properties['owned_by'] = $requesting_user_eid;
        $pipe_properties['created_by'] = $requesting_user_eid;
        $pipe = \Flexio\Object\Pipe::create($pipe_properties);

        $pipe->grant($requesting_user_eid, \Model::ACCESS_CODE_TYPE_EID,
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
        return self::get_internal($pipe);
    }

    public static function copy(\Flexio\Api1\Request $request) : array
    {
        $params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'copy_eid'    => array('type' => 'identifier', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $original_pipe_identifier = $validated_params['copy_eid'];

        // load the pipe
        if (\Flexio\Base\Eid::isValid($original_pipe_identifier) === false)
        {
            $eid_from_identifier = \Flexio\Object\Pipe::getEidFromName($requesting_user_eid, $original_pipe_identifier);
            $original_pipe_identifier = $eid_from_identifier !== false ? $eid_from_identifier : '';
        }
        $original_pipe = \Flexio\Object\Pipe::load($original_pipe_identifier);

        // make sure we can read the pipe
        if ($original_pipe->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($original_pipe->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // create a new pipe; copy the logic, but not the status, scheduling, etc
        $original_pipe_properties = $original_pipe->get();

        $new_pipe_properties = array();
        $new_pipe_properties['name'] = $original_pipe_properties['name'] . ' copy';
        $new_pipe_properties['description'] = $original_pipe_properties['description'];
        $new_pipe_properties['task'] = $original_pipe_properties['task'];
        $new_pipe_properties['owned_by'] = $requesting_user_eid;
        $new_pipe_properties['created_by'] = $requesting_user_eid;
        $new_pipe = \Flexio\Object\Pipe::create($new_pipe_properties);

        $new_pipe->grant($requesting_user_eid, \Model::ACCESS_CODE_TYPE_EID,
            array(
                \Flexio\Object\Right::TYPE_READ_RIGHTS,
                \Flexio\Object\Right::TYPE_WRITE_RIGHTS,
                \Flexio\Object\Right::TYPE_READ,
                \Flexio\Object\Right::TYPE_WRITE,
                \Flexio\Object\Right::TYPE_DELETE,
                \Flexio\Object\Right::TYPE_EXECUTE
            )
        );

        return self::get_internal($new_pipe);
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
        $pipe_identifier = $validated_params['eid'];

        // load the object
        if (\Flexio\Base\Eid::isValid($pipe_identifier) === false)
        {
            $eid_from_identifier = \Flexio\Object\Pipe::getEidFromName($requesting_user_eid, $pipe_identifier);
            $pipe_identifier = $eid_from_identifier !== false ? $eid_from_identifier : '';
        }
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);

        // check the rights on the object
        if ($pipe->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_DELETE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $pipe->delete();

        $result = array();
        $result['eid'] = $pipe->getEid();
        $result['eid_type'] = $pipe->getType();
        $result['eid_status'] = $pipe->getStatus();
        return $result;
    }

    public static function set(\Flexio\Api1\Request $request) : array
    {
        $params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid'             => array('type' => 'identifier', 'required' => true),
                'eid_status'      => array('type' => 'string', 'required' => false),
                'ename'           => array('type' => 'identifier', 'required' => false),
                'name'            => array('type' => 'string', 'required' => false),
                'description'     => array('type' => 'string', 'required' => false),
                'task'            => array('type' => 'object', 'required' => false),
                'schedule'        => array('type' => 'object', 'required' => false),
                'schedule_status' => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $pipe_identifier = $validated_params['eid'];

        // load the object
        if (\Flexio\Base\Eid::isValid($pipe_identifier) === false)
        {
            $eid_from_identifier = \Flexio\Object\Pipe::getEidFromName($requesting_user_eid, $pipe_identifier);
            $pipe_identifier = $eid_from_identifier !== false ? $eid_from_identifier : '';
        }
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);

        // check the rights on the object
        if ($pipe->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // set the properties
        $pipe->set($validated_params);

        return self::get_internal($pipe);
    }

    public static function get(\Flexio\Api1\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $pipe_identifier = $validated_params['eid'];

        // load the object
        if (\Flexio\Base\Eid::isValid($pipe_identifier) === false)
        {
            $eid_from_identifier = \Flexio\Object\Pipe::getEidFromName($requesting_user_eid, $pipe_identifier);
            $pipe_identifier = $eid_from_identifier !== false ? $eid_from_identifier : '';
        }
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);

        // check the rights on the object
        if ($pipe->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the properties
        return self::get_internal($pipe);
    }

    public static function list(\Flexio\Api1\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // get the pipes
        $result = array();

        $filter = array('owned_by' => $user->getEid(), 'eid_status' => \Model::STATUS_AVAILABLE);
        $pipes = \Flexio\Object\Pipe::list($filter);

        foreach ($pipes as $p)
        {
            if ($p->allows($user->getEid(), \Flexio\Object\Right::TYPE_READ) === false)
                continue;

            $result[] = self::get_internal($p);
        }

        return $result;
    }

    public static function processes(\Flexio\Api1\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true),
                'start'    => array('type' => 'integer', 'required' => false),
                'tail'     => array('type' => 'integer', 'required' => false),
                'limit'    => array('type' => 'integer', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $pipe_identifier = $validated_params['eid'];
        $start = isset($validated_params['start']) ? (int)$validated_params['start'] : null;
        $tail = isset($validated_params['tail']) ? (int)$validated_params['tail'] : null;
        $limit = isset($validated_params['limit']) ? (int)$validated_params['limit'] : null;

        // load the object
        if (\Flexio\Base\Eid::isValid($pipe_identifier) === false)
        {
            $eid_from_identifier = \Flexio\Object\Pipe::getEidFromName($requesting_user_eid, $pipe_identifier);
            $pipe_identifier = $eid_from_identifier !== false ? $eid_from_identifier : '';
        }
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);

        // check the rights on the object
        if ($pipe->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $filter = array('owned_by' => $requesting_user_eid, 'eid_status' => \Model::STATUS_AVAILABLE, 'parent_eid' => $pipe->getEid());
        $processes = \Flexio\Object\Process::list($filter);

        // get the processes that are accessible
        $processes_accessible = array();
        foreach ($processes as $p)
        {
            // a pipe can be run by different users; make sure the given user can only see
            // processes that they have rights to
            if ($p->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
                continue;

            $processes_accessible[] = $p;
        }

        // get the subset of the processes for any start/tail/limit
        if (isset($start) || isset($tail) || isset($limit))
            $processes_accessible = self::filter($processes_accessible, $start, $tail, $limit);

        // get the content for the selected processes
        $result = array();
        foreach ( $processes_accessible as $p)
        {
            $result[] = self::get_internal($p);
        }

        return $result;
    }

    public static function run(\Flexio\Api1\Request $request) // TODO: add return type
    {
        $params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $pipe_identifier = $validated_params['eid'];

        // load the object
        if (\Flexio\Base\Eid::isValid($pipe_identifier) === false)
        {
            $eid_from_identifier = \Flexio\Object\Pipe::getEidFromName($requesting_user_eid, $pipe_identifier);
            $pipe_identifier = $eid_from_identifier !== false ? $eid_from_identifier : '';
        }
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);

        // check the rights on the pipe object
        if ($pipe->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_EXECUTE) === false)
             throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // create a new process
        $pipe_properties = $pipe->get();
        $process_properties = array(
            'parent_eid' => $pipe_properties['eid'],
            'task' => $pipe_properties['task'],
            'owned_by' => $pipe_properties['owned_by']['eid'],
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

    private static function filter(array $items, int $start = null, int $tail = null, int $limit = null) : array
    {
        // if limit isn't defined, set it to the largest integer size
        if (!isset($limit))
            $limit = PHP_INT_MAX;

        if ($limit < 0)
            $limit = 0;

        // if start is specified, supercede any tail
        if (isset($start))
        {
            if ($start < 0)
                $start = 0;
            return array_slice($items, $start, $limit);
        }

        if (isset($tail))
        {
            $cnt = count($items);
            if ($tail > $cnt )
                $tail = $cnt + 1 ;
            return array_slice($items, $cnt - $tail - 1, $limit);
        }

        // no start or tail; start at zero and return the limit
        return array_slice($items, 0, $limit);
    }

    private static function get_internal($object)
    {
        // TODO: tasks are objects right now; and an empty task is returning as [];
        // internally, we still look for tasks as arrays, so we can't do a full
        // fix until we start handling objects as objects and not as arrays; this
        // fix let's us return empty tasks as {} until we get a more general fix;
        // similar to fixEmptyParams() for task params

        $properties = $object->get();
        if (isset($properties['task']) && count($properties['task']) === 0)
            $properties['task'] = (object)$properties['task'];

        return $properties;
    }
}
