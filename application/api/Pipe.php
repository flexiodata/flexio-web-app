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
            $project->addMember($pipe);

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
            $project->addMember($new_pipe);

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
        $filter = array('eid_type' => array(\Model::TYPE_PIPE), 'eid_status' => array(\Model::STATUS_AVAILABLE));
        $pipes = $user->getObjects($filter);

        $result = array();
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
                'limit'    => array('type' => 'integer', 'required' => false),
                'order'    => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $pipe_identifier = $validated_params['eid'];
        $start = isset($validated_params['start']) ? (int)$validated_params['start'] : null;
        $limit = isset($validated_params['limit']) ? (int)$validated_params['limit'] : null;
        $order = isset($validated_params['order']) ? (string)$validated_params['order'] : null;

        // load the object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the processes
        $processes_accessible = array();
        $processes = $pipe->getProcesses();
        foreach ($processes as $p)
        {
            // a pipe can be run by different users; make sure the given user can only see
            // processes that they have rights to
            if ($p->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
                continue;

            $processes_accessible[] = $p->get();
        }

        // filter/order the result items
        $result = self::filter($processes_accessible, $start, $limit, $order);
        return $result;
    }

    public static function run(\Flexio\Api\Request $request) // TODO: add return type
    {
        $params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true),
                'stream' => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $pipe_identifier = $validated_params['eid'];
        $stream_to_echo = $validated_params['stream'] ?? false;
        $background = false;

        // load the object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_EXECUTE) === false)
             throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // STEP 1: create a new process
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

        // STEP 2: parse the content and set the stream info
        $php_stream_handle = fopen('php://input', 'rb');
        $post_content_type = $_SERVER['CONTENT_TYPE'] ?? '';
         \Flexio\Object\Process::addProcessInputFromStream($php_stream_handle, $post_content_type, $process);

         // STEP 3: run the process
        $process->run($background);

        $stream = $process->getStdout();
        $stream_info = $stream->get();
        if ($stream_info === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $mime_type = $stream_info['mime_type'];
        $start = 0;
        $limit = PHP_INT_MAX;
        $content = $stream->content($start, $limit);
        $response_code = $process->getResponseCode();


        if ($mime_type !== \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE)
        {
            // return content as-is
            header('Content-Type: ' . $mime_type, true, $response_code);
        }
        else
        {
            // flexio table; return application/json in place of internal mime
            header('Content-Type: ' . \Flexio\Base\ContentType::MIME_TYPE_JSON, true, $response_code);
            $content = json_encode($content);
        }

        echo($content);
        exit(0);
    }

    public static function validate(\Flexio\Api\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid'      => array('type' => 'identifier', 'required' => true),
                'task_eid' => array('type' => 'identifier', 'required' => false)
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

        // get the tasks
        $pipe_info = $pipe->get();
        $task = $pipe_info['task'];

        // iterate through the task steps an validate each one
        $result = array();
        foreach ($task as $t)
        {
            $task_eid = $t['eid'] ?? '';
            if (strlen($task_eid) > 0 && isset($validated_params['task_eid']))
            {
                // user wants to validate only one step of a pipe (e.g. an execute job/script)
                if ($validated_params['task_eid'] != $task_eid)
                    continue;
            }


            $task_errors = array();

            $type = $t['type'] ?? '';
            $job_params = $t['params'] ?? array();

            if (empty($type))
            {
                $task_errors[] = array(
                    'error' => 'missing_parameter',
                    'message' => 'Job type parameter is missing'
                );
            }


            if ($type == 'flexio.execute')
            {
                $lang = $job_params['lang'] ?? '';
                $code = base64_decode($job_params['code'] ?? '');
                $code = is_null($code) ? '' : $code;

                try
                {
                    $err = \Flexio\Jobs\Execute::checkScript($lang, $code);
                    if ($err !== true)
                    {
                        $task_errors[] = array(
                            'error' => 'compile_error',
                            'message' => $err
                        );
                    }
                }
                catch (\Flexio\Base\Exception $e)
                {
                    $task_errors[] = array(
                        'error' => 'unknown_language',
                        'message' => 'The scripting language specified is unknown'
                    );
                }
            }


            // push back any steps with an error
            if (count($task_errors) > 0)
            {
                $result[] = array('task_eid' => $task_eid,
                                  'errors' => $task_errors);
            }
        }

        return $result;
    }

    public static function addTaskStep(\Flexio\Api\Request $request) : array
    {
        $params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        // the params that are posted is the task step; note: tasks don't
        // restrict key/values that can be passed, so don't limit them
        // here or validate them; simply make sure we have a parent_eid
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid'   => array('type' => 'identifier', 'required' => true),
                'index' => array('type' => 'integer', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $all_params = $params;
        $validated_params = $validator->getParams();
        $pipe_identifier = $validated_params['eid'];

        // load the object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // add the task
        $index = $validated_params['index'] ?? null;
        if (isset($index))
            $index = (int)$index;

        $task = $all_params;
        unset($task['eid']);
        unset($task['index']);

        $task_identifier = $pipe->addTaskStep($task, $index);

        $result = $pipe->getTaskStep($task_identifier);

        // coerce an empty associative array() from [] into object {};
        if (isset($result['params']) && is_array($result['params']) && count($result['params'])==0)
        {
            $result['params'] = (object)$result['params'];
        }

        return $result;
    }

    public static function deleteTaskStep(\Flexio\Api\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid'        => array('type' => 'identifier', 'required' => true),
                'parent_eid' => array('type' => 'identifier', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $task_identifier = $validated_params['eid'];
        $pipe_identifier = $validated_params['parent_eid'];

        // load the object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // delete the task
        $pipe->deleteTaskStep($task_identifier);

        $result = array();
        $result['eid'] = $task_identifier;
        return $result;
    }

    public static function setTaskStep(\Flexio\Api\Request $request) /* : array */ // TODO: set function return type
    {
        $params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        // the params that are posted is the task step; note: tasks don't
        // restrict key/values that can be passed, so don't limit them
        // here or validate them; simply make sure we have an eid and a
        // parent_eid
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid'        => array('type' => 'identifier', 'required' => true),
                'parent_eid' => array('type' => 'identifier', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $all_params = $params;
        $validated_params = $validator->getParams();
        $task_identifier = $validated_params['eid'];
        $pipe_identifier = $validated_params['parent_eid'];

        // load the object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // update the task
        $task = $all_params;
        unset($task['eid']);
        unset($task['parent_eid']);

        $pipe->setTaskStep($task_identifier, $task);
        return $pipe->getTaskStep($task_identifier);
    }

    public static function getTaskStep(\Flexio\Api\Request $request) /* : array */ // TODO: set function return type
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid'        => array('type' => 'identifier', 'required' => true),
                'parent_eid' => array('type' => 'identifier', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $task_identifier = $validated_params['eid'];
        $pipe_identifier = $validated_params['parent_eid'];

        // load the object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the task
        return $pipe->getTaskStep($task_identifier);
    }

    private static function filter(array $processes_accessible, int $start = null, int $limit = null, string $order = null) : array
    {
        // TODO: generalize this function so it can be used for other API endpoints
        // that involve lists


        // if no parameters are defined, return the input
        if (!isset($start) && !isset($limit) && !isset($order))
            return $processes_accessible;

        if (isset($order))
        {
            $desc = false;
            if (substr($order,0,1) === '-' && strlen($order) > 1)
            {
                // if the field starts with a -, sort in descending order
                $desc = true;
                $order = substr($order, 1);
            }

            if ($desc === true)
                \Flexio\Base\Util::sortByFieldDesc($processes_accessible, $order);
                 else
                \Flexio\Base\Util::sortByFieldAsc($processes_accessible, $order);
        }

        $start = $start ?? 0;
        $limit = $limit ?? PHP_INT_MAX;

        if ($start < 0)
            $start = 0;
        if ($limit < 0)
            $limit = 0;

        $row = 0;
        $result = array();
        foreach ($processes_accessible as $p)
        {
            $row++;

            if ($row < $start + 1)
                continue;

            if ($row > $start + $limit)
                break;

            $result[] = $p;
        }

        return $result;
    }
}
