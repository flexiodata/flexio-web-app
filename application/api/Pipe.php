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
    public static function create(array $params, string $requesting_user_eid = null) : array
    {
        $params_original = $params;
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'copy_eid'        => array('type' => 'identifier', 'required' => false),
                'parent_eid'      => array('type' => 'identifier', 'required' => false),
                'eid_status'      => array('type' => 'string', 'required' => false),
                'ename'           => array('type' => 'identifier', 'required' => false),
                'name'            => array('type' => 'string', 'required' => false),
                'description'     => array('type' => 'string', 'required' => false),
                'rights'          => array('type' => 'object', 'required' => false),
                'task'            => array('type' => 'object', 'required' => false),
                'schedule'        => array('type' => 'object', 'required' => false),
                'schedule_status' => array('type' => 'string', 'required' => false)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $project_identifier = isset($params['parent_eid']) ? $params['parent_eid'] : false;

        // if the copy_eid parameter is set, then copy the pipe,
        // using the original parameters; this simply allows us to reuse
        // the create api call, even though the two functions are distinct
        if (isset($params['copy_eid']))
            return self::copy($params_original, $requesting_user_eid);

        // check rights
        $project = false;
        if ($project_identifier !== false)
        {
            $project = \Flexio\Object\Project::load($project_identifier);
            if ($project === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

            if ($project->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_WRITE) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);
        }

        // create the object
        $pipe_properties = $params;
        $pipe = \Flexio\Object\Pipe::create($pipe_properties);
        $pipe->setOwner($requesting_user_eid);
        $pipe->setCreatedBy($requesting_user_eid);

        // the created of the object is the owner, so if rights property is
        // set, then set the rights
        if (isset($params['rights']))
        {
            $acl = \Flexio\Object\Acl::create($params['rights']);
            $pipe->setRights($acl);
        }

        // if a parent project is specified, add the object as a member of the project
        if ($project !== false)
            $project->addMember($pipe);

        // get the pipe properties
        return $pipe->get();
    }

    public static function copy(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'copy_eid'    => array('type' => 'identifier', 'required' => true),
                'parent_eid'  => array('type' => 'identifier', 'required' => false)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $original_pipe_identifier = $params['copy_eid'];
        $project_identifier = isset($params['parent_eid']) ? $params['parent_eid'] : false;

        // make sure we can read the pipe
        $original_pipe = \Flexio\Object\Pipe::load($original_pipe_identifier);
        if ($original_pipe === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        if ($original_pipe->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // make sure we can save the copied pipe to any specified parent
        if ($project_identifier!== false)
        {
            $project = \Flexio\Object\Project::load($project_identifier);
            if ($project === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

            if ($project->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_WRITE) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);
        }

        // create a new pipe; copy the logic, but not the status, scheduling, etc
        $original_pipe_properties = $original_pipe->get();

        $new_pipe_properties = array();
        $new_pipe_properties['name'] = $original_pipe_properties['name'] . ' copy';
        $new_pipe_properties['description'] = $original_pipe_properties['description'];
        $new_pipe_properties['task'] = $original_pipe_properties['task'];

        $new_pipe = \Flexio\Object\Pipe::create($new_pipe_properties);
        $new_pipe->setOwner($requesting_user_eid);
        $new_pipe->setCreatedBy($requesting_user_eid);

        // copy the project with the original rights
        $acl = \Flexio\Object\Acl::create($original_pipe_properties['rights']);
        $new_pipe->setRights($acl);

        // if a parent project is specified, add the object as a member of the project
        if ($project !== false)
            $project->addMember($new_pipe);

        return $new_pipe->get();
    }

    public static function delete(array $params, string $requesting_user_eid = null) : bool
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $pipe_identifier = $params['eid'];

        // load the object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_DELETE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $pipe->delete();
        return true;
    }

    public static function set(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid'             => array('type' => 'identifier', 'required' => true),
                'eid_status'      => array('type' => 'string', 'required' => false),
                'ename'           => array('type' => 'identifier', 'required' => false),
                'name'            => array('type' => 'string', 'required' => false),
                'description'     => array('type' => 'string', 'required' => false),
                'rights'          => array('type' => 'object', 'required' => false),
                'task'            => array('type' => 'object', 'required' => false),
                'schedule'        => array('type' => 'object', 'required' => false),
                'schedule_status' => array('type' => 'string', 'required' => false)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $pipe_identifier = $params['eid'];

        // load the object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // set the properties
        $pipe->set($params);

        // if we're the owner and the rights property is set, then set the rights
        if ($requesting_user_eid === $pipe->getOwner() && isset($params['rights']))
        {
            $acl = \Flexio\Object\Acl::create($params['rights']);
            $pipe->setRights($acl);
        }

        return $pipe->get();
    }

    public static function get(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $pipe_identifier = $params['eid'];

        // load the object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the properties
        return $pipe->get();
    }

    public static function listall(array $params, string $requesting_user_eid = null) : array
    {
        // load the object
        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($user->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the pipes
        $result = array();
        $pipes = $user->getPipes();
        foreach ($pipes as $p)
        {
            if ($p->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_READ) === false)
                continue;

            $result[] = $p->get();
        }

        return $result;
    }

    public static function processes(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true),
                'start'    => array('type' => 'integer', 'required' => false),
                'limit'    => array('type' => 'integer', 'required' => false),
                'order'    => array('type' => 'string', 'required' => false)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $pipe_identifier = $params['eid'];
        $start = isset($params['start']) ? (int)$params['start'] : null;
        $limit = isset($params['limit']) ? (int)$params['limit'] : null;
        $order = isset($params['order']) ? (string)$params['order'] : null;

        // load the object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the processes
        $processes_accessible = array();
        $processes = $pipe->getProcesses();
        foreach ($processes as $p)
        {
            // a pipe can be run by different users; make sure the given user can only see
            // processes that they have rights to
            if ($p->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_READ) === false)
                continue;

            $processes_accessible[] = $p->get();
        }

        // filter/order the result items
        $result = self::filter($processes_accessible, $start, $limit, $order);
        return $result;
    }

    public static function run(array $params, string $requesting_user_eid = null) // TODO: add return type
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $pipe_identifier = $params['eid'];
        $background = false;

        // load the object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        // TODO: re-enable rights checking with execute check
        // if ($pipe->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_READ) === false)
        //     throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // STEP 1: create a new process
        $process_properties = array();
        $process = \Flexio\Object\Process::create($process_properties);
        $process->setOwner($requesting_user_eid);
        $process->setCreatedBy($requesting_user_eid);
        $pipe->addProcess($process);

        $pipe_properties = $pipe->get();
        $process_properties['task'] = $pipe_properties['task'];
        $process->set($process_properties);

        // STEP 2: parse the content and set the stream info
        $php_stream_handle = fopen('php://input', 'rb');
        $post_content_type = $_SERVER['CONTENT_TYPE'] ?? '';

        $stream = false;
        $streamwriter = false;
        $form_params = array();

        $parser = \Flexio\Base\MultipartParser::create();

        $parser->parse($php_stream_handle, $post_content_type, function ($type, $name, $data, $filename, $content_type) use (&$stream, &$streamwriter, &$process, &$form_params) {
            if ($type == \Flexio\Base\MultipartParser::TYPE_FILE_BEGIN)
            {
                $stream = \Flexio\Object\Stream::create();

                // stream name will be the post variable name, not the multipart filename
                // TODO: should we be using filename in the path and form name in the name?
                $stream_info = array();
                $stream_info['name'] = $name;
                //$stream_info['name'] = $filename; // TODO: test
                $stream_info['mime_type'] = $content_type;
                $stream->set($stream_info);

                $streamwriter = \Flexio\Object\StreamWriter::create($stream);
            }
             else if ($type == \Flexio\Base\MultipartParser::TYPE_FILE_DATA)
            {
                if ($streamwriter !== false)
                {
                    // write out the data
                    $streamwriter->write($data);
                }
            }
             else if ($type == \Flexio\Base\MultipartParser::TYPE_FILE_END)
            {
                $process->addInput($stream);
                $streamwriter = false;
                $stream = false;
            }
             else if ($type == \Flexio\Base\MultipartParser::TYPE_KEY_VALUE)
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

            $output_streams = $process->getOutput()->enum();
            for ($i = 0; $i < count($output_streams); ++$i)
            {
                if ($output_streams[$i]->getName() == $desired_stream || (is_numeric($desired_stream) && $i == $desired_stream))
                {
                    $stream = $output_streams[$i];
                    $stream_info = $stream->get();
                    if ($stream_info === false)
                        throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

                    $mime_type = $stream_info['mime_type'];
                    $start = 0;
                    $limit = PHP_INT_MAX;
                    $content = $stream->content($start, $limit);

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

        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
             throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $pipe_identifier= $params['eid'];

        // get the raw post input and feed it into the 'params' parameter
        $p = array();
        $p['parent_eid'] = $pipe_identifier;
        $p['background'] = true;
        $p['run'] = true;
        $p['params'] = $_POST;

        return Process::create($p, $requesting_user_eid);
        */
    }

    public static function validate(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid'      => array('type' => 'identifier', 'required' => true),
                'task_eid' => array('type' => 'identifier', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $pipe_identifier = $params['eid'];

        // load the object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the tasks
        $pipe_info = $pipe->get();
        $task = $pipe_info['task'];

        // iterate through the task steps an validate each one
        $result = array();
        foreach ($task as $t)
        {
            $task_eid = $t['eid'] ?? '';
            if (strlen($task_eid) > 0 && isset($params['task_eid']))
            {
                // user wants to validate only one step of a pipe (e.g. an execute job/script)
                if ($params['task_eid'] != $task_eid)
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

    public static function addTaskStep(array $params, string $requesting_user_eid = null) : array
    {
        // the params that are posted is the task step; note: tasks don't
        // restrict key/values that can be passed, so don't limit them
        // here or validate them; simply make sure we have a parent_eid
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid'   => array('type' => 'identifier', 'required' => true),
                'index' => array('type' => 'integer', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $pipe_identifier = $params['eid'];

        // load the object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // add the task
        $index = $params['index'] ?? null;
        if (isset($index))
            $index = (int)$index;

        $task = $params;
        unset($task['eid']);
        unset($task['index']);

        $task_identifier = $pipe->addTaskStep($task, $index);
        return $pipe->getTaskStep($task_identifier);
    }

    public static function deleteTaskStep(array $params, string $requesting_user_eid = null) : bool
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid'        => array('type' => 'identifier', 'required' => true),
                'parent_eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $task_identifier = $params['eid'];
        $pipe_identifier = $params['parent_eid'];

        // load the object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // delete the task
        $pipe->deleteTaskStep($task_identifier);
        return true;
    }

    public static function setTaskStep(array $params, string $requesting_user_eid = null) /* : array */ // TODO: set function return type
    {
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

        $task_identifier = $params['eid'];
        $pipe_identifier = $params['parent_eid'];

        // load the object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // update the task
        $task = $params;
        unset($task['eid']);
        unset($task['parent_eid']);

        $pipe->setTaskStep($task_identifier, $task);
        return $pipe->getTaskStep($task_identifier);
    }

    public static function getTaskStep(array $params, string $requesting_user_eid = null) /* : array */ // TODO: set function return type
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid'        => array('type' => 'identifier', 'required' => true),
                'parent_eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $task_identifier = $params['eid'];
        $pipe_identifier = $params['parent_eid'];

        // load the object
        $pipe = \Flexio\Object\Pipe::load($pipe_identifier);
        if ($pipe === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($pipe->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_READ) === false)
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
