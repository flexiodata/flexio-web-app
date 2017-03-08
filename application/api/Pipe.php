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


namespace Flexio\Api;


class Pipe
{
    public static function create($params, $request)
    {
        $params_original = $params;
        if (($params = $request->getValidator()->check($params, array(
                'copy_eid'        => array('type' => 'identifier', 'required' => false),
                'parent_eid'      => array('type' => 'identifier', 'required' => false),
                'eid_status'      => array('type' => 'string', 'required' => false),
                'ename'           => array('type' => 'identifier', 'required' => false),
                'name'            => array('type' => 'string', 'required' => false),
                'description'     => array('type' => 'string', 'required' => false),
                'task'            => array('type' => 'object', 'required' => false),
                'schedule'        => array('type' => 'object', 'required' => false),
                'schedule_status' => array('type' => 'string', 'required' => false)
            ))) === false)
            return $request->getValidator()->fail();

        $project_identifier = isset($params['parent_eid']) ? $params['parent_eid'] : false;
        $requesting_user_eid = $request->getRequestingUser();

        // if the copy_eid parameter is set, then copy the pipe,
        // using the original parameters; this simply allows us to reuse
        // the create api call, even though the two functions are distinct
        if (isset($params['copy_eid']))
            return self::copy($params_original, $request);

        // check rights
        $project = false;
        if ($project_identifier !== false)
        {
            $project = \Flexio\Object\Project::load($project_identifier);
            if ($project === false)
                return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

            if ($project->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_WRITE) === false)
                return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);
        }

        // create the object
        $pipe_properties = $params;
        $pipe = \Flexio\Object\Pipe::create($pipe_properties);
        if ($pipe === false)
            return $request->getValidator()->fail(Api::ERROR_CREATE_FAILED);

        // set the owner and creator
        $pipe->setOwner($requesting_user_eid);
        $pipe->setCreatedBy($requesting_user_eid);

        // if a parent project is specified, add the object as a member of the project
        if ($project !== false)
            $project->addMember($pipe->getEid());

        // get the pipe properties
        return $pipe->get();
    }

    public static function copy($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'copy_eid'    => array('type' => 'identifier', 'required' => true),
                'parent_eid'  => array('type' => 'identifier', 'required' => false)
            ))) === false)
            return $request->getValidator()->fail();

        $original_pipe_identifier = $params['copy_eid'];
        $project_identifier = isset($params['parent_eid']) ? $params['parent_eid'] : false;
        $requesting_user_eid = $request->getRequestingUser();

        // make sure we can read the pipe
        $original_pipe = \Flexio\Object\Pipe::load($original_pipe_identifier);
        if ($original_pipe === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        if ($original_pipe->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
            return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);

        // make sure we can save the copied pipe to any specified parent
        if ($project_identifier!== false)
        {
            $project = \Flexio\Object\Project::load($project_identifier);
            if ($project === false)
                return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

            if ($project->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_WRITE) === false)
                return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);
        }

        // create a new pipe; copy the logic, but not the status, scheduling, etc
        $original_pipe_properties = $original_pipe->get();

        $new_pipe_properties = array();
        $new_pipe_properties['name'] = $original_pipe_properties['name'] . ' copy';
        $new_pipe_properties['description'] = $original_pipe_properties['description'];
        $new_pipe_properties['task'] = $original_pipe_properties['task'];

        $new_pipe = \Flexio\Object\Pipe::create($new_pipe_properties);
        if ($new_pipe === false)
            return $request->getValidator()->fail(Api::ERROR_CREATE_FAILED);

        // set the owner and creator
        $new_pipe->setOwner($requesting_user_eid);
        $new_pipe->setCreatedBy($requesting_user_eid);

        // if a parent project is specified, add the object as a member of the project
        if ($project !== false)
            $project->addMember($new_pipe->getEid());

        return $new_pipe->get();
    }

    public static function delete($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $pipe_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_DELETE) === false)
            return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);

        $pipe->delete();
        return true;
    }

    public static function set($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid'             => array('type' => 'identifier', 'required' => true),
                'eid_status'      => array('type' => 'string', 'required' => false),
                'ename'           => array('type' => 'identifier', 'required' => false),
                'name'            => array('type' => 'string', 'required' => false),
                'description'     => array('type' => 'string', 'required' => false),
                'task'            => array('type' => 'object', 'required' => false),
                'schedule'        => array('type' => 'object', 'required' => false),
                'schedule_status' => array('type' => 'string', 'required' => false)
            ))) === false)
            return $request->getValidator()->fail();

        $pipe_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_WRITE) === false)
            return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);

        // set the properties
        $pipe->set($params);
        return $pipe->get();
    }

    public static function get($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $pipe_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
            return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);

        // get the properties
        return $pipe->get();
    }

    public static function listall($params, $request)
    {
        // get the pipes for the requesting user
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        // check the rights on the object
        if ($user->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
            return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);

        // get the pipes
        $result = array();
        $pipes = $user->getPipes();
        foreach ($pipes as $p)
        {
            if ($p->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
                continue;

            $result[] = $p->get();
        }

        return $result;
    }

    public static function comments($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $pipe_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
            return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);

        // get the comments
        $result = array();
        $comments = $pipe->getComments();
        foreach ($comments as $c)
        {
            $result[] = $c->get();
        }

        return $result;
    }

    public static function processes($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $pipe_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
            return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);

        // get the processes
        $result = array();
        $processes = $pipe->getProcesses();
        foreach ($processes as $p)
        {
            // a pipe can be run by different users; make sure the given user can only see
            // processes that they have rights to
            if ($p->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
                continue;

            $result[] = $p->get();
        }

        return $result;
    }

    public static function run($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $pipe_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();
        $background = false;

        // load the object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        // check the rights on the object
        // TODO: re-enable rights checking with execute check
        // if ($pipe->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
        //     return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);

        // STEP 1: create a new process
        $process_properties = array();
        $process = \Flexio\Object\Process::create($process_properties);

        if ($process === false)
            return $request->getValidator()->fail(Api::ERROR_CREATE_FAILED);

        $process->setOwner($requesting_user_eid);
        $process->setCreatedBy($requesting_user_eid);
        $pipe->addProcess($process);

        $pipe_properties = $pipe->get();
        $process_properties['task'] = $pipe_properties['task'];
        $process->set($process_properties);

        // STEP 2: parse the content and set the stream info
        $php_stream_handle = fopen('php://input', 'rb');
        $post_content_type = isset_or($_SERVER['CONTENT_TYPE'], '');

        $stream = false;
        $streamwriter = false;
        $form_params = array();

        $parser = \Flexio\Services\MultipartParser::create();

        $parser->parse($php_stream_handle, $post_content_type, function ($type, $name, $data, $filename, $content_type) use (&$stream, &$streamwriter, &$process, &$form_params) {
            if ($type == \Flexio\Services\MultipartParser::TYPE_FILE_BEGIN)
            {
                $stream = \Flexio\Object\Stream::create();
                if ($stream)
                {
                    // stream name will be the post variable name, not the multipart filename
                    $stream->setName($name);

                    $streamwriter = \Flexio\Object\StreamWriter::create($stream);
                    if ($streamwriter === false)
                        $stream = false;
                }
            }
             else if ($type == \Flexio\Services\MultipartParser::TYPE_FILE_DATA)
            {
                if ($streamwriter !== false)
                {
                    // write out the data
                    $streamwriter->write($data);
                }
            }
             else if ($type == \Flexio\Services\MultipartParser::TYPE_FILE_END)
            {
                $process->addInput($stream);
                $streamwriter = false;
                $stream = false;
            }
             else if ($type == \Flexio\Services\MultipartParser::TYPE_KEY_VALUE)
            {
                $form_params[$name] = $data;
            }
        });
        fclose($php_stream_handle);



        $process->setParams($form_params);
        $process->run($background);



        if (isset($_GET['stream']))
        {
            $desired_stream = $_GET['stream'];

            $output_streams = $process->getTaskOutputStreams();
            for ($i = 0; $i < count($output_streams); ++$i)
            {
                if ($output_streams[$i]->getName() == $desired_stream || (is_numeric($desired_stream) && $i == $desired_stream))
                {
                    $stream = $output_streams[$i];
                    $stream_info = $stream->get();
                    if ($stream_info === false)
                        return $request->getValidator()->fail(Api::ERROR_READ_FAILED);

                    $mime_type = $stream_info['mime_type'];
                    $start = 0;
                    $limit = pow(2,24);
                    $columns = true;
                    $metadata = false;
                    $handle = 'create';
                    $content = $stream->content($start, $limit, $columns, $metadata, $handle);

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



        return $process->get();

        /*
        // TODO: experimental

        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $pipe_identifier= $params['eid'];

        // get the raw post input and feed it into the 'params' parameter
        $p = array();
        $p['parent_eid'] = $pipe_identifier;
        $p['background'] = true;
        $p['run'] = true;
        $p['params'] = $_POST;

        return Process::create($p, $request);
        */
    }

    public static function addTaskStep($params, $request)
    {
        // the params that are posted is the task step; note: tasks don't
        // restrict key/values that can be passed, so don't limit them
        // here or validate them; simply make sure we have a parent_eid
        if (($request->getValidator()->check($params, array(
                'eid'   => array('type' => 'identifier', 'required' => true),
                'index' => array('type' => 'integer', 'required' => false)
            ))) === false)
            return $request->getValidator()->fail();

        $pipe_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_WRITE) === false)
            return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);

        // add the task
        $index = isset_or($params['index'], null);
        if (isset($index))
            $index = (int)$index;

        $task = $params;
        unset($task['eid']);
        unset($task['index']);

        $task_identifier = $pipe->addTaskStep($task, $index);
        return $pipe->getTaskStep($task_identifier);
    }

    public static function deleteTaskStep($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid'        => array('type' => 'identifier', 'required' => true),
                'parent_eid' => array('type' => 'identifier', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $task_identifier = $params['eid'];
        $pipe_identifier = $params['parent_eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_WRITE) === false)
            return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);

        // delete the task
        $pipe->deleteTaskStep($task_identifier);
        return true;
    }

    public static function setTaskStep($params, $request)
    {
        // the params that are posted is the task step; note: tasks don't
        // restrict key/values that can be passed, so don't limit them
        // here or validate them; simply make sure we have an eid and a
        // parent_eid
        if (($request->getValidator()->check($params, array(
                'eid'        => array('type' => 'identifier', 'required' => true),
                'parent_eid' => array('type' => 'identifier', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $task_identifier = $params['eid'];
        $pipe_identifier = $params['parent_eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_WRITE) === false)
            return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);

        // update the task
        $task = $params;
        unset($task['eid']);
        unset($task['parent_eid']);

        $pipe->setTaskStep($task_identifier, $task);
        return $pipe->getTaskStep($task_identifier);
    }

    public static function getTaskStep($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid'        => array('type' => 'identifier', 'required' => true),
                'parent_eid' => array('type' => 'identifier', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $task_identifier = $params['eid'];
        $pipe_identifier = $params['parent_eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
            return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);

        // get the task
        return $pipe->getTaskStep($task_identifier);
    }
}
