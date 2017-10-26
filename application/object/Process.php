<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-03-28
 *
 * @package flexio
 * @subpackage Object
 */


declare(strict_types=1);
namespace Flexio\Object;


require_once dirname(__DIR__) . '/services/Abstract.php';


class Process extends \Flexio\Object\Base
{
    // variables and errors
    private $debug;
    private $errors;
    private $response_code = 200;

    public function __construct()
    {
        $this->setType(\Model::TYPE_PROCESS);
        $this->debug = false;
        $this->error = array();
    }

    public static function create(array $properties = null) : \Flexio\Object\Process
    {
        if (isset($properties) && isset($properties['task']))
            $properties['task'] = \Flexio\Object\Task::create($properties['task'])->get();

        // task is stored as a json string, so it needs to be encoded
        if (isset($properties) && isset($properties['task']))
            $properties['task'] = json_encode($properties['task']);

        // if not process mode is specified, run everything
        if (!isset($properties['process_mode']))
            $properties['process_mode'] = \Model::PROCESS_MODE_RUN;

        $object = new static();
        $model = \Flexio\Object\Store::getModel();
        $local_eid = $model->create($object->getType(), $properties);

        $object->setModel($model);
        $object->setEid($local_eid);
        $object->clearCache();

        return $object;
    }

    public static function addProcessInputFromStream($php_stream_handle, string $post_content_type, \Flexio\Object\Process $process) // TODO: add type checking for handle
    {
        $stream = false;
        $streamwriter = false;
        $form_params = array();
        $post_streams = array();

        // first fetch query string parameters
        foreach ($_GET as $key => $value)
        {
            $form_params["query.$key"] = $value;
        }

        $parser = \Flexio\Base\MultipartParser::create();
        $parser->parse($php_stream_handle, $post_content_type, function ($type, $name, $data, $filename, $content_type) use (&$stream, &$streamwriter, &$process, &$form_params, &$post_streams) {
            if ($type == \Flexio\Base\MultipartParser::TYPE_FILE_BEGIN)
            {
                $stream = \Flexio\Object\Stream::create();

                if ($content_type === false)
                {
                    $content_type = \Flexio\Base\ContentType::getMimeType($filename, '');
                }

                // stream name will be the post variable name, not the multipart filename
                // TODO: should we be using filename in the path and form name in the name?
                $stream_info = array();
                $stream_info['name'] = $name;
                $stream_info['mime_type'] = $content_type;

                $stream->set($stream_info);
                $streamwriter = $stream->getWriter();
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
                $post_streams[] = $stream;
                $streamwriter = false;
                $stream = false;
            }
            else if ($type == \Flexio\Base\MultipartParser::TYPE_KEY_VALUE)
            {
                $form_params[$name] = $data;
            }
        });
        fclose($php_stream_handle);

        $input = \Flexio\Object\Context::create();
        $input->setParams($form_params);

        foreach ($post_streams as $s)
        {
            // TODO: add streams to stdin instead?
            $input->addStream($s);
        }

        $process->setInput($input);
    }

    public function set(array $properties) : \Flexio\Object\Process
    {
        // TODO: add properties check

        // TODO: some properties shouldn't be able to be changed once
        // a process is running; e.g. the task

        // if a task parameter is set, we need to assign a client id to each element
        if (isset($properties) && isset($properties['task']))
            $properties['task'] = \Flexio\Object\Task::create($properties['task'])->get();

        // task and schedule are stored as a json string, so these need to be encoded
        if (isset($properties) && isset($properties['task']))
            $properties['task'] = json_encode($properties['task']);

        $this->clearCache();
        $process_model = $this->getModel()->process;
        $process_model->set($this->getEid(), $properties);
        return $this;
    }

    public function get() : array
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties;
    }

    public function run(bool $background = true, bool $debug = false) : \Flexio\Object\Process
    {
        // STEP 1: check the status; don't run the job in certain circumstances
        $this->clearCache();
        $process_model = $this->getModel()->process;
        $process_status = $process_model->getProcessStatus($this->getEid());

        switch ($process_status)
        {
            // run job; fall through
            default:
            case \Model::PROCESS_STATUS_UNDEFINED:
            case \Model::PROCESS_STATUS_PENDING:
                break;

            // job is already running or has been run, so don't do anything
            case \Model::PROCESS_STATUS_WAITING:
            case \Model::PROCESS_STATUS_RUNNING:
            case \Model::PROCESS_STATUS_CANCELLED:
            case \Model::PROCESS_STATUS_FAILED:
            case \Model::PROCESS_STATUS_COMPLETED:
                return $this;

            // job is paused, so resume it
            case \Model::PROCESS_STATUS_PAUSED:
                $process_model->setProcessStatus($this->getEid(), \Model::PROCESS_STATUS_RUNNING);
                return $this;
        }

        // STEP 2: set the status
        $process_model->setProcessStatus($this->getEid(), \Model::PROCESS_STATUS_RUNNING);

        // STEP 3: run the job
        if ($background !== true)
        {
            if ($debug === true)
                $this->setDebug(true);

            //$this->prepare();
            $this->execute();

            return $this;
        }

        $process_eid = $this->getEid();
        \Flexio\System\Program::runInBackground("\Flexio\Object\Process::run_internal('$process_eid')");
        return $this;
    }

    public static function run_internal(string $eid) : bool
    {
        // this is a non-blocking internal static run function called that's
        // run in the background by \Flexio\Objects\Process::run($background) when $background
        // is set to true

        // load the process and run in non-blocking mode
        $object = self::load($eid);

        // run the job
        //$object->prepare();
        $object->execute();
        return true;
    }

    public function pause() : \Flexio\Object\Process
    {
        $this->clearCache();
        $process_model = $this->getModel()->process;
        $process_status = $process_model->getProcessStatus($this->getEid());

        switch ($process_status)
        {
            // only allow jobs that are running to be paused
            case \Model::PROCESS_STATUS_RUNNING:
                $process_model->setProcessStatus($this->getEid(), \Model::PROCESS_STATUS_PAUSED);
                break;
        }

        return $this;
    }

    public function cancel() : \Flexio\Object\Process
    {
        $this->clearCache();
        $process_model = $this->getModel()->process;
        $process_status = $process_model->getProcessStatus($this->getEid());

        switch ($process_status)
        {
            // if a job is already completed, don't allow it to be cancelled
            case \Model::PROCESS_STATUS_CANCELLED:
            case \Model::PROCESS_STATUS_FAILED:
            case \Model::PROCESS_STATUS_COMPLETED:
                return $this;
        }

        $process_model->setProcessStatus($this->getEid(), \Model::PROCESS_STATUS_CANCELLED);
        return $this;
    }

    public function setProcessStatus(string $status) : \Flexio\Object\Process
    {
        if (self::isValidProcessStatus($status) === false)
            return $this;

        $this->clearCache();
        $process_model = $this->getModel()->process;
        $process_model->setProcessStatus($this->getEid(), $status);
        return $this;
    }

    public function getProcessStatus() : string
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['process_status'];
    }

    public function setProcessInfo(array $info) : \Flexio\Object\Process
    {
        // pack the process info
        $params = array();
        $params['process_info'] = json_encode($info);

        // set the info
        $this->clearCache();
        $process_model = $this->getModel()->process;
        $process_model->set($this->getEid(), $params);

        return $this;
    }

    public function getProcessInfo() : array
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['process_info'];
    }

    public function setDebug(bool $debug)
    {
        $this->debug = $debug;
    }

    public function getDebug() : bool
    {
        return $this->debug;
    }

    public function getEnvironmentParams() : array
    {
        // return a list of environment parameters;
        // TODO: determine list; for now, include current user information and time
        // TODO: do we want to "namespace" the variables? right now, variables are
        // limited to alphanumeric, but maybe we want to do something like:
        // "flexio.user_firstname", "flexio.user_lastname", etc
        $environment_params = array();

        $environment_params['process.user.firstname'] = \Flexio\System\System::getCurrentUserFirstName();
        $environment_params['process.user.lastname'] = \Flexio\System\System::getCurrentUserLastName();
        $environment_params['process.user.email'] = \Flexio\System\System::getCurrentUserEmail();
        $environment_params['process.time.started'] = \Flexio\System\System::getTimestamp();

        return $environment_params;
    }

    public function setTask(array $task) : \Flexio\Object\Process
    {
        $properties = array();
        $properties['task'] = $task;
        return $this->set($properties);
    }

    public function getTask() : array
    {
        $properties = $this->get();
        return $properties['task'];
    }

    public function setParams(array $params) : \Flexio\Object\Process
    {
        // shorthand for setting just the parameters of the input;
        // used in the test suite

        // get the existing input
        $context = $this->getInput();
        $context->setParams($params);
        $this->setInput($context);

        return $this;
    }

    public function setInput(\Flexio\Object\Context $context) : \Flexio\Object\Process
    {
        // set the input
        $input = \Flexio\Object\Context::toString($context);
        $this->getModel()->process->set($this->getEid(), array('input' => $input));

        return $this;
    }

    public function getInput() : \Flexio\Object\Context
    {
        $process_properties = $this->getModel()->process->get($this->getEid());
        $input = $process_properties['input'];
        return \Flexio\Object\Context::fromString($input);
    }

    public function getOutput() : \Flexio\Object\Context
    {
        $process_properties = $this->getModel()->process->get($this->getEid());
        $output = $process_properties['output'];
        return \Flexio\Object\Context::fromString($output);
    }

    public function getStdout() : \Flexio\Object\IStream
    {
        return $this->getOutput()->getStdout();
    }

    public function getTaskInputOutput(\Flexio\Object\Context &$input_context, \Flexio\Object\Context &$output_context, string $task_eid = null)
    {
        // returns the context of input streams for the specified task of a
        // process; if no task is specified, the streams from the last subprocess
        // are used
        $input_context = \Flexio\Object\Context::create();
        $output_context = \Flexio\Object\Context::create();

        // get the subprocesses
        $process_info = $this->get();
        $subprocesses = $process_info['subprocesses'];
        $subprocess_count = count($subprocesses);
        if ($subprocess_count < 1) // if there isn't any subprocess, there's no items
            return;

        // find the subprocess with the relevant items
        $specified_subprocess = false;
        if (!isset($task_eid))
        {
            // if no task is specified, use the last subprocess as the default
            $specified_subprocess = $subprocesses[$subprocess_count-1];
        }
         else
        {
            foreach ($subprocesses as $sp)
            {
                $task = $sp['task'];
                if (isset($task['eid']) && $task['eid'] === $task_eid)
                {
                    $specified_subprocess = $sp;
                    break;
                }
            }

            if ($specified_subprocess === false)
                return;
        }

        $input_context = \Flexio\Object\Context::fromString(json_encode($specified_subprocess['input']));  // TODO: cumbersome; the context uses stream objects, but we only have eid info, so we need to repack and unpack
        $output_context = \Flexio\Object\Context::fromString(json_encode($specified_subprocess['output']));  // TODO: cumbersome; the context uses stream objects, but we only have eid info, so we need to repack and unpack
    }

    public function isBuildMode() : bool
    {
        if ($this->isCached() === false)
            $this->populateCache();

        if ($this->properties['process_mode'] === \Model::PROCESS_MODE_BUILD)
            return true;

        return false;
    }

    public function isRunMode() : bool
    {
        if ($this->isCached() === false)
            $this->populateCache();

        if ($this->properties['process_mode'] === \Model::PROCESS_MODE_RUN)
            return true;

        return false;
    }

    public function getError() : array
    {
        return $this->error;
    }

    public function hasError() : bool
    {
        if (empty($this->error))
            return false;

        return true;
    }

    private function fail(string $code = '', string $message = null, string $file = null, int $line = null, string $type = null, array $trace = null)
    {
        // only save the first error we come to
        if ($this->hasError())
            return;

        if (!isset($message))
            $message = \Flexio\Base\Error::getDefaultMessage($code);

        $this->error = array('code' => $code, 'message' => $message, 'file' => $file, 'line' => $line, 'type' => $type, 'trace' => $trace);
    }

    /*
    private function prepare()
    {
        // take the main job task and split it out into subprocesses

        // get the main process eid, initial subprocess eid (which is the main
        // process eid, but will change with each step), and the process steps
        $main_process_eid = $this->getEid();
        $sub_process_eid = $this->getEid();
        $local_properties = $this->get();

        // split the main process steps up into subprocesses
        $task = $local_properties['task'];
        if (!is_array($task))
            $task = array();

        // add the tasks after the initial import
        $main_process = true;
        foreach ($task as $step)
        {
            // add the task subprocess step
            $sub_process_eid = $this->createTaskSubProcess($main_process_eid, $sub_process_eid, $step);
            if ($sub_process_eid === false)
                return $this->setProcessStatus(\Model::PROCESS_STATUS_FAILED); // unable to create a step

            $main_process = false;
        }

        // we're writing, so invalidate the cache
        $this->clearCache();
    }

    private function createTaskSubProcess(string $main_process_eid, string $sub_process_eid, array $step) : string
    {
        $subprocess_properties = array();
        $subprocess_properties['process_status'] = \Model::PROCESS_STATUS_PENDING;
        $subprocess_properties['process_eid'] = $main_process_eid;
        $subprocess_properties['parent_eid'] = $sub_process_eid;
        $subprocess_properties['task_type'] = $step['type'] ?? '';
        $subprocess_properties['parent_eid'] = $sub_process_eid;
        $subprocess_properties['task'] = json_encode($step);
        $subprocess_properties['input'] = json_encode("{}"); // set by the loop
        $subprocess_properties['output'] = json_encode("{}"); // set by the loop

        $process_model = $this->getModel()->process;
        return $process_model->create($subprocess_properties, false); // false parameter: create subprocess; TODO: change parameter? this is confusing
    }

    private function execute()
    {
        // TODO: set appropriate status for failures

        // track what version of the task implementation we're using
        // (more granular than task version, which may or may not be updated
        // with small logic changes)
        $implementation_revision = \Flexio\System\System::getGitRevision();

        // set initial job status
        $process_params = array();
        $process_params['started'] = self::getProcessTimestamp();
        $process_params['process_status'] = \Model::PROCESS_STATUS_RUNNING;
        $process_params['impl_revision'] = $implementation_revision;
        $this->getModel()->process->set($this->getEid(), $process_params);

        // make sure we have the latest list of subprocesses
        $local_properties = $this->get();

        // STEP 1: get the list of subprocesses
        $subprocesses = $local_properties['subprocesses'];

        // STEP 2: set any initial input/variables in the input record; these may be
        // set by an experimental api endpoint
        $current_process_properties = $this->getModel()->process->get($this->getEid());
        $current_input = $current_process_properties['input'];
        $context = \Flexio\Object\Context::fromString($current_input);

        // needs to be reworked because context object now stores env and params separately
        $environment_variables = $this->getEnvironmentParams();
        $user_variables = $this->getInput()->getParams();
        $variables = array_merge($user_variables, $environment_variables);
        $context->setEnv($variables);

        // STEP 3: if stdin/stdout aren't initialized with a stream object, initialize them
        $stdin = $context->getStdin();
        if (!isset($stdin))
        {
            $stdin = \Flexio\Object\Stream::create();
            $stdin->setMimeType(\Flexio\Base\ContentType::MIME_TYPE_TXT); // default mime type
            $context->setStdin($stdin);
        }

        $stdout= $context->getStdout();
        if (!isset($stdout))
        {
            $stdout = \Flexio\Object\Stream::create();
            $stdout->setMimeType(\Flexio\Base\ContentType::MIME_TYPE_TXT); // default mime type
            $context->setStdout($stdout);
        }

        // STEP 4: iterate through the subprocesses and run each one
        $cache_used_on_subprocess = false;

        foreach ($subprocesses as $sp)
        {
            // get the subprocess eid and task
            $subprocess_eid = $sp['eid'];
            $task = $sp['task'];

            // save the input from the output of the previous step
            $subprocess_params = array();
            $subprocess_params['input'] = \Flexio\Object\Context::toString($context);
            $subprocess_params['started'] = self::getProcessTimestamp();
            $subprocess_params['process_status'] = \Model::PROCESS_STATUS_RUNNING;
            $subprocess_params['impl_revision'] = $implementation_revision;
            $result = $this->getModel()->process->set($subprocess_eid, $subprocess_params);

            // in build mode, cache the result
            $process_hash = '';
            $cache_used = false;

            // if we're in build mode, use the cache
            $result_exists = false;
            if ($this->isBuildMode())
            {
                $result_exists = $this->findCachedResult($implementation_revision, $task, $context);
                if ($result_exists)
                {
                    $cache_used = true;
                    $cache_used_on_subprocess = true; // flag for the overall process; set once
                }
            }

            if ($result_exists === false)
            {
                // execute the step and generate a result hash
                $this->executeStep($task, $context);
                $process_hash = $this->generateTaskHash($implementation_revision, $task, $context);
            }

            // if the implementation has changed during the task, the result is
            // unreliable; set an error so that the process fails
            $implementation_revision_update = \Flexio\System\System::getGitRevision();
            if ($implementation_revision !== $implementation_revision_update)
                $this->fail(\Flexio\Base\Error::GENERAL, '', __FILE__, __LINE__);

            // save the output from the last step
            $subprocess_params = array();
            $subprocess_params['output'] = \Flexio\Object\Context::toString($context);
            $subprocess_params['finished'] = self::getProcessTimestamp();
            $subprocess_params['process_status'] = \Model::PROCESS_STATUS_COMPLETED;
            $subprocess_params['process_hash'] = $process_hash;
            $subprocess_params['cache_used'] = $cache_used === true ? 'Y' : 'N';

            // if we have errors, use different values for some of the params
            if ($this->hasError())
            {
                $error = $this->getError();
                $subprocess_error_info = array();
                $subprocess_error_info['code'] = $error['code'];
                $subprocess_error_info['message'] = $error['message'];

                if (IS_DEBUG())
                {
                    $subprocess_error_info['type'] = $error['type'];
                    $subprocess_error_info['file'] = $error['file'];
                    $subprocess_error_info['line'] = $error['line'];
                    //$subprocess_error_info['trace'] = $error['trace']; // makes the error info verbose, but available if needed
                }

                $subprocess_params['process_status'] = \Model::PROCESS_STATUS_FAILED; // set the failed flag
                $subprocess_params['process_hash'] = ''; // don't cache failures
                $subprocess_params['process_info'] = json_encode(array(
                    'error' => $subprocess_error_info
                ));
            }

            $this->getModel()->process->set($subprocess_eid, $subprocess_params);

            // set the stdin for the next job step to be the output from the stdout
            // of the step just executed
            $context->setStdin($context->getStdout());

            $stdout = \Flexio\Object\Stream::create();
            $stdout->setMimeType(\Flexio\Base\ContentType::MIME_TYPE_TXT); // default mime type
            $context->setStdout($stdout);

            // if the step failed, stop the job
            if ($this->hasError())
                break;

            // if the step exited, stop the job
            $response_code = $context->getExitCode();
            if ($response_code !== null)
            {
                $this->response_code = $response_code;
                break;
            }
        }

        // set final job status
        $process_params = array();
        $process_params['finished'] = self::getProcessTimestamp();
        $process_params['process_status'] = $this->hasError() ? \Model::PROCESS_STATUS_FAILED : \Model::PROCESS_STATUS_COMPLETED;
        $process_params['cache_used'] = $cache_used_on_subprocess === true ? 'Y' : 'N';
        $this->getModel()->process->set($this->getEid(), $process_params);

        // clear the cache
        $this->clearCache();
    }
*/

    private function execute()
    {
        // TODO: set appropriate status for failures

        // track what version of the task implementation we're using
        // (more granular than task version, which may or may not be updated
        // with small logic changes)
        //$implementation_revision = \Flexio\System\System::getGitRevision();

        // set initial job status
        $process_params = array();
        $process_params['started'] = self::getProcessTimestamp();
        $process_params['process_status'] = \Model::PROCESS_STATUS_RUNNING;
        //$process_params['impl_revision'] = $implementation_revision;
        $this->getModel()->process->set($this->getEid(), $process_params);


        // STEP 1: get the process properties
        $current_process_properties = $this->getModel()->process->get($this->getEid());

        // STEP 2: get the process tasks
        $process_tasks = $current_process_properties['task'];
        $process_tasks = json_decode($process_tasks, true);
        if ($process_tasks === false)
        {
            // TODO: set process error, and we're done
        }

        // STEP 3: create the initial process context; make sure stdin/stdout are initialized
        // and initialize the context variables
        $process_input = $current_process_properties['input'];
        $context = \Flexio\Object\Context::fromString($process_input);

        $stdin = $context->getStdin();
        if (!isset($stdin))
        {
            $stdin = \Flexio\Object\Stream::create();
            $stdin->setMimeType(\Flexio\Base\ContentType::MIME_TYPE_TXT); // default mime type
            $context->setStdin($stdin);
        }

        $stdout = $context->getStdout();
        if (!isset($stdout))
        {
            $stdout = \Flexio\Object\Stream::create();
            $stdout->setMimeType(\Flexio\Base\ContentType::MIME_TYPE_TXT); // default mime type
            $context->setStdout($stdout);
        }

        $environment_variables = $this->getEnvironmentParams();
        $user_variables = $this->getInput()->getParams();
        $variables = array_merge($user_variables, $environment_variables);
        $context->setEnv($variables);

        // STEP 4: iterate through the tasks and run each one
        $first_task = true;
        foreach ($process_tasks as $task)
        {
            if ($first_task === false)
            {
                // set the stdin for the next job step to be the output from the stdout
                // of the step just executed and create a new stdout
                $context->setStdin($context->getStdout());
                $stdout = \Flexio\Object\Stream::create();
                $stdout->setMimeType(\Flexio\Base\ContentType::MIME_TYPE_TXT); // default mime type
                $context->setStdout($stdout);
            }

            // execute the step
            $this->executeStep($task, $context);
            $first_task = false;

            // if the implementation has changed during the task, the result is
            // unreliable; set an error so that the process fails
            //$implementation_revision_update = \Flexio\System\System::getGitRevision();
            //if ($implementation_revision !== $implementation_revision_update)
            //    $this->fail(\Flexio\Base\Error::GENERAL, '', __FILE__, __LINE__);

            // if the step failed, stop the job
            if ($this->hasError())
                break;

            // if the step exited, stop the job
            $response_code = $context->getExitCode();
            if ($response_code !== null)
            {
                $this->response_code = $response_code;
                break;
            }
        }

        // save final job output and status
        $process_params = array();
        $process_params['output'] = \Flexio\Object\Context::toString($context);
        $process_params['finished'] = self::getProcessTimestamp();
        $process_params['process_status'] = $this->hasError() ? \Model::PROCESS_STATUS_FAILED : \Model::PROCESS_STATUS_COMPLETED;
        $process_params['cache_used'] = 'N';
        $this->getModel()->process->set($this->getEid(), $process_params);

        // clear the process object cache
        $this->clearCache();
    }

    private function executeStep(array $task, \Flexio\Object\Context &$context)
    {
        // if the process is something besides running, we're done
        $status = $this->getModel()->process->getProcessStatus($this->getEid());
        if ($status !== \Model::PROCESS_STATUS_RUNNING)
            return; // nothing to do, but we're still ok

        // create the job with the task
        try
        {
            // TODO: experimental: pass context parameters through run function parameter

            // set the job input, run the job, and get the output
            $job = self::createJob($task);
            $job->run($context);
        }
        catch (\Flexio\Base\Exception $e)
        {
            self::logExceptionIfConfigured($e, $task);

            $info = $e->getMessage(); // exception info is packaged up in message
            $info = json_decode($info,true);
            $file = $e->getFile();
            $line = $e->getLine();
            $trace = $e->getTrace();
            $code = $info['code'];
            $message = $info['message'];
            $type = 'flexio exception';
            $this->fail($code, $message, $file, $line, $type, $trace);

            //if ($this->getDebug() === true)
            //    die("<pre> Exception in $file line $line\n" . $e->getTraceAsString());
        }
        catch (\Exception $e)
        {
            self::logExceptionIfConfigured($e, $task);

            $file = $e->getFile();
            $line = $e->getLine();
            $trace = $e->getTrace();
            $type = 'php exception';
            $this->fail(\Flexio\Base\Error::GENERAL, '', $file, $line, $type, $trace);

            if ($this->getDebug() === true)
                die("<pre> Exception in $file line $line\n" . $e->getTraceAsString());
        }
        catch (\Error $e)
        {
            self::logExceptionIfConfigured($e, $task);

            $file = $e->getFile();
            $line = $e->getLine();
            $trace = $e->getTrace();
            $type = 'php error';
            $this->fail(\Flexio\Base\Error::GENERAL, '', $file, $line, $type, $trace);

            if ($this->getDebug() === true)
                die("<pre> Exception in $file line $line\n" . $e->getTraceAsString());
        }
    }


    public function getResponseCode() : int
    {
        return $this->response_code;
    }

    private function isCached() : bool
    {
        // a process may be run in the background and update values
        // in the model; never cache process data so an object always
        // represents the latest state of a process
        return false;

        // note: following is normal cache behavior
        // if ($this->properties === false)
        //    return false;
        //
        // return true;
    }

    private function clearCache() : bool
    {
        $this->eid_status = false;
        $this->properties = false;
        return true;
    }

    private function populateCache() : bool
    {
        // get the properties
        $local_properties = $this->getProperties();
        $this->properties = $local_properties;
        $this->eid_status = $local_properties['eid_status'];
        return true;
    }

    private function getProperties() : array
    {
        $query = '
        {
            "eid" : null,
            "eid_type" : "'.\Model::TYPE_PROCESS.'",
            "eid_status" : null,
            "parent='.\Model::EDGE_PROCESS_OF.'" : {
                "eid" : null,
                "eid_type" : "'.\Model::TYPE_PIPE.'",
                "name" : null,
                "description" : null
            },
            "owned_by='.\Model::EDGE_OWNED_BY.'" : {
                "eid" : null,
                "eid_type" : "'.\Model::TYPE_USER.'",
                "user_name" : null,
                "first_name" : null,
                "last_name" : null,
                "email_hash" : null
            },
            "process_mode": null,
            "task" : null,
            "started_by" : null,
            "started" : null,
            "finished" : null,
            "duration" : null,
            "process_info" : null,
            "process_status" : null,
            "cache_used" : null,
            "subprocesses" : null,
            "created" : null,
            "updated" : null
        }
        ';

        // get the primary process info
        $query = json_decode($query);
        $properties = \Flexio\Object\Query::exec($this->getEid(), $query);
        if (!$properties)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // unpack the primary process task json
        $task = @json_decode($properties['task'],true);
        if ($task !== false)
            $properties['task'] = $task;

        // unpack the primary process process info json
        $process_info = @json_decode($properties['process_info'],true);
        if ($process_info !== false)
            $properties['process_info'] = $process_info;

        // get the subprocesses
        $process_model = $this->getModel()->process;
        $process_tree = $process_model->getProcessTree($this->getEid());
        if ($process_tree === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // unpack the process tree into the properties
        $primary_process = array();
        $subprocesses = array();
        foreach ($process_tree as $p)
        {
            // only get the subprocesses
            if ($p['eid'] === $p['process_eid'])
                continue;

            unset($p['eid_status']);
            unset($p['process_eid']);
            unset($p['parent_eid']);
            unset($p['process_mode']);

            // unpack the task
            $task = $p['task'];
            $task = @json_decode($task, true);
            if (!is_array($task))
                $task = array();
            $p['task'] = $task;

            // unpack the input
            $input = $p['input'];
            $input = @json_decode($input, true);
            if (!is_array($input))
                $input = array();
            $p['input'] = $input;

            // unpack the output
            $output = $p['output'];
            $output = @json_decode($output, true);
            if (!is_array($output))
                $output = array();
            $p['output'] = $output;

            // unpack the process info
            $info = $p['process_info'];
            $info = @json_decode($info, true);
            if (!is_array($info))
                $info = array();
            $p['process_info'] = $info;

            // save the info
            $subprocesses[] = $p;
        }

        $properties['subprocesses'] = $subprocesses;
        return $properties;
    }

    private function findCachedResult(string $implementation_revision, array $task, \Flexio\Object\Context &$context) : bool
    {
        // find the hash for the input and the task
        $hash = self::generateTaskHash($implementation_revision, $task, $context);
        if (strlen($hash) === 0)
            return false;

        $process_output = $this->getModel()->process->getOutputByHash($hash);
        if ($process_output === false)
            return false;

        $process_output_context = \Flexio\Object\Context::fromString($process_output);

        $context->clear();
        $context->set($process_output_context);
        return true;
    }

    private static function generateTaskHash(string $implementation_version, array $task, \Flexio\Object\Context $context) : string
    {
        // if we dont' have an implementation version or an invalid implementation
        // version (git revision), don't cache anything
        if (strlen($implementation_version) < 40)
            return '';

        // task hash should uniquely identify the result; use
        // a hash of:
        //     1. implementation version (git version)
        //     2. task type
        //     3. task version
        //     4. task parameters
        //     5. job input
        //     6. job input params (user specified)
        // leave out the job name or other identifiers, such as the
        // the task eid; if we can't find one of these, don't generate
        // the hash

        // make sure have a valid task
        $task_type = $task['type'] ?? false;
        $task_parameters = $task['params'] ?? false;

        if (is_string($task_type) === false || is_array($task_parameters) === false)
            return '';

        // require an input
        $task_input = $context->getStreams();
        if (count($task_input) === 0)
            return '';

        $encoded_task_parameters = json_encode($task_parameters);
        $encoded_task_input = \Flexio\Object\Context::toString($context);

        $hash = md5(
            $implementation_version .
            $task_type .
            $encoded_task_parameters .
            $encoded_task_input
        );

        return $hash;
    }

    public static function createJob(array $task) : \Flexio\Jobs\IJob
    {
        if (!isset($task['type']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        $job_type = $task['type'];

        // make sure the job is registered; note: this isn't strictly necessary,
        // but gives us a convenient way of limiting what jobs are available for
        // processing
        $full_class_name = false;
        $manifest = \Flexio\Object\Task::manifest();
        foreach ($manifest as $m)
        {
            if ($m['type'] !== $job_type)
                continue;

            $full_class_name = $m['class'];
            break;
        }

        if ($full_class_name === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        // try to find the job file
        $class_name_parts = explode("\\", $full_class_name);
        if (!isset($class_name_parts[3]))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        $class_file = \Flexio\System\System::getApplicationDirectory() . DIRECTORY_SEPARATOR . 'jobs' . DIRECTORY_SEPARATOR . $class_name_parts[3] . '.php';
        if (!@file_exists($class_file))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        // load the job's php file and instantiate the job object
        include_once $class_file;
        $job = $full_class_name::create($task);

        if ($job === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        return $job;
    }

    private static function isValidProcessStatus(string $status) : bool
    {
        switch ($status)
        {
            default:
                return false;

            case \Model::PROCESS_STATUS_UNDEFINED:
            case \Model::PROCESS_STATUS_PENDING:
            case \Model::PROCESS_STATUS_WAITING:
            case \Model::PROCESS_STATUS_RUNNING:
            case \Model::PROCESS_STATUS_CANCELLED:
            case \Model::PROCESS_STATUS_PAUSED:
            case \Model::PROCESS_STATUS_FAILED:
            case \Model::PROCESS_STATUS_COMPLETED:
                return true;
        }
    }

    private static function getProcessTimestamp() : string
    {
        // return the timestamp as accurately as we can determine
        $time_exact = microtime(true);
        $time_rounded = floor($time_exact);
        $time_micropart = sprintf("%06d", ($time_exact - $time_rounded) * 1000000);
        $date = new \DateTime(date('Y-m-d H:i:s.' . $time_micropart, (int)$time_rounded));
        return ($date->format("Y-m-d H:i:s.u"));
    }

    private static function logExceptionIfConfigured($exception, $task)
    {
        if (isset($GLOBALS['g_config']->debug_error_log))
        {
            $message = $exception->getMessage();
            $json = json_encode($task);
            file_put_contents($GLOBALS['g_config']->debug_error_log, "Job exception caught '$message'; json was $json\n\n", FILE_APPEND);
        }
    }
}
