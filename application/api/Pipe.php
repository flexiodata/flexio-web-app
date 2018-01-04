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
    public static function create(\Flexio\Api\Request $request) : array
    {
        $params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'copy_eid'        => array('type' => 'identifier', 'required' => false),
                'parent_eid'      => array('type' => 'identifier', 'required' => false),
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
        $project_identifier = isset($validated_params['parent_eid']) ? $validated_params['parent_eid'] : false;

        // if the copy_eid parameter is set, then copy the pipe,
        // using the original parameters; this simply allows us to reuse
        // the create api call, even though the two functions are distinct
        if (isset($validated_params['copy_eid']))
            return self::copy($request);

        // check rights
        $project = false;
        if ($project_identifier !== false)
        {
            $project = \Flexio\Object\Project::load($project_identifier);
            if ($project === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

            if ($project->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);
        }

        // create the object
        $pipe_properties = $validated_params;
        $pipe = \Flexio\Object\Pipe::create($pipe_properties);

        $pipe->setCreatedBy($requesting_user_eid);
        $pipe->setOwner($requesting_user_eid);

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

        // if a parent project is specified, add the object as a member of the project
        if ($project !== false)
            $project->addPipe($pipe);

        // get the pipe properties
        return $pipe->get();
    }

    public static function copy(\Flexio\Api\Request $request) : array
    {
        $params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'copy_eid'    => array('type' => 'identifier', 'required' => true),
                'parent_eid'  => array('type' => 'identifier', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $original_pipe_identifier = $validated_params['copy_eid'];
        $project_identifier = $validated_params['parent_eid'] ?? false;

        // make sure we can read the pipe
        $original_pipe = \Flexio\Object\Pipe::load($original_pipe_identifier);
        if ($original_pipe === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        if ($original_pipe->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // make sure we can save the copied pipe to any specified parent
        if ($project_identifier!== false)
        {
            $project = \Flexio\Object\Project::load($project_identifier);
            if ($project === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

            if ($project->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);
        }

        // create a new pipe; copy the logic, but not the status, scheduling, etc
        $original_pipe_properties = $original_pipe->get();

        $new_pipe_properties = array();
        $new_pipe_properties['name'] = $original_pipe_properties['name'] . ' copy';
        $new_pipe_properties['description'] = $original_pipe_properties['description'];
        $new_pipe_properties['task'] = $original_pipe_properties['task'];

        $new_pipe = \Flexio\Object\Pipe::create($new_pipe_properties);
        $new_pipe->setCreatedBy($requesting_user_eid);
        $new_pipe->setOwner($requesting_user_eid);

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

        // if a parent project is specified, add the object as a member of the project
        if ($project !== false)
            $project->addPipe($new_pipe);

        return $new_pipe->get();
    }

    public static function delete(\Flexio\Api\Request $request) : array
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
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_DELETE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $pipe->delete();

        $result = array();
        $result['eid'] = $pipe->getEid();
        $result['eid_type'] = $pipe->getType();
        $result['eid_status'] = $pipe->getStatus();
        return $result;
    }

    public static function set(\Flexio\Api\Request $request) : array
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
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // set the properties
        $pipe->set($validated_params);

        return $pipe->get();
    }

    public static function get(\Flexio\Api\Request $request) : array
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
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the properties
        return $pipe->get();
    }

    public static function listall(\Flexio\Api\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // get the pipes
        $result = array();

        $pipes = $user->getPipeList();
        foreach ($pipes as $p)
        {
            if ($p->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
                continue;

            $result[] = $p->get();
        }

        return $result;
    }

    public static function processes(\Flexio\Api\Request $request) : array
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
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the processes that are accessible
        $processes_accessible = array();
        $processes = $pipe->getProcessList();
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
            $result[] = $p->get();
        }

        return $result;
    }

    public static function run(\Flexio\Api\Request $request) // TODO: add return type
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

        // load the pipe object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the pipe object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_EXECUTE) === false)
             throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // create a new process
        $process_properties = array();
        $process = \Flexio\Object\Process::create($process_properties);
        $process->setOwner($pipe->getOwner());

        if ($requesting_user_eid === \Flexio\Object\User::MEMBER_PUBLIC)
            $process->setCreatedBy($pipe->getOwner());
            else
            $process->setCreatedBy($requesting_user_eid);

        $process->setRights($pipe->getRights());
        $pipe->addProcess($process);

        $pipe_properties = $pipe->get();
        $process_properties['task'] = $pipe_properties['task'];
        $process->set($process_properties);

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
}
