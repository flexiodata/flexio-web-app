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


namespace Flexio\Object;


require_once dirname(__DIR__) . '/services/Abstract.php';


class Process extends \Flexio\Object\Base
{
    // variables and errors
    private $errors;

    // progress variables
    private $current_executing_subprocess_eid;
    private $last_percentage_saved;

    public function __construct()
    {
        $this->setType(\Model::TYPE_PROCESS);
        $this->errors = array();
        $this->current_executing_subprocess_eid = false;
        $this->last_percentage_saved = false;
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
        $object->setRights();
        $object->clearCache();
        return $object;
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

    public function get()
    {
        if ($this->isCached() === true)
            return $this->properties;

        if ($this->populateCache() === true)
            return $this->properties;

        return false;
    }

    public function run(bool $background = true) : \Flexio\Object\Process
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
            $this->prepare();
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
        $object->prepare();
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

    public function setProgress(float $pct) : \Flexio\Object\Process
    {
        // get the executing subprocess; if we don't have any, we're done
        $executing_subprocess_eid = $this->getCurrentExecutingSubProcess();
        if ($executing_subprocess_eid === false)
            return $this;

        $pct = intval($pct);
        if ($pct < 0)
            $pct = 0;
        if ($pct > 100)
            $pct = 100;

        // if the percentage hasn't changed, don't write the value; this allows
        // the function to be called in tight loops without worrying about the
        // overhead of database writes
        if ($this->last_percentage_saved === $pct)
            return $this;

        $process_info = array();
        $process_info['pct'] = $pct;
        $process_params['process_info'] = json_encode($process_info);
        $this->getModel()->process->set($executing_subprocess_eid, $process_params);
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
        if ($this->isCached() === true)
            return $this->properties['process_status'];

        if ($this->populateCache() === true)
            return $this->properties['process_status'];

        return \Model::PROCESS_STATUS_UNDEFINED;
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

    public function getProcessInfo()
    {
        if ($this->isCached() === true)
            return $this->properties['process_info'];

        if ($this->populateCache() === true)
            return $this->properties['process_info'];

        return array();
    }

    public function setParams(array $params) : \Flexio\Object\Process
    {
        // TODO: make sure the params are a set of key/value pairs;
        // we have params coming in from the api that need to be
        // verified so that template variable replacement doesn't
        // choke downstream
        if (count($params) === 0)
            return $this;

        // make sure the params are key/value pairs
        if (\Flexio\Base\Util::isAssociativeArray($params) === false)
            return $this;

        // add on the new input
        $input_params = json_encode($params);
        $this->getModel()->process->set($this->getEid(), array('input_params' => $input_params));

        return $this;
    }

    public function getParams()
    {
        // get whatever is in the input_params of the initial process step
        $process_properties = $this->getModel()->process->get($this->getEid());
        $input_params = $process_properties['input_params'];
        $input = @json_decode($input_params, true);
        return $input;
    }

    public function getEnvironmentParams() : array
    {
        // return a list of environment parameters;
        // TODO: determine list; for now, include current user information and time
        // TODO: do we want to "namespace" the variables? right now, variables are
        // limited to alphanumeric, but maybe we want to do something like:
        // "flexio.user_firstname", "flexio.user_lastname", etc
        $environment_params = array();

        $environment_params['flexio_user_firstname'] = \Flexio\System\System::getCurrentUserFirstName();
        $environment_params['flexio_user_lastname'] = \Flexio\System\System::getCurrentUserLastName();
        $environment_params['flexio_user_email'] = \Flexio\System\System::getCurrentUserEmail();
        $environment_params['flexio_timestamp'] = \Flexio\System\System::getTimestamp();

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

    public function addInput($stream) : \Flexio\Object\Process // TODO: add input parameter type
    {
        // TODO: only allow input to be added before a job is run

        // a stream can be either a stream or an array with a stream eid
        if (is_array($stream) && isset($stream['eid']))
            $stream = \Flexio\Object\Stream::load($stream['eid']);
        if (!($stream instanceof \Flexio\Object\Stream))
            return $this;

        // get the current input
        $process_properties = $this->getModel()->process->get($this->getEid());
        $input = $process_properties['input'];
        $input_collection = self::unstringifyCollectionEids($input);

        // add on the new input
        $input_collection->push($stream);
        $input_updated = self::stringifyCollectionEids($input_collection);
        $this->getModel()->process->set($this->getEid(), array('input' => $input_updated));

        return $this;
    }

    public function getInput() : \Flexio\Object\Collection
    {
        // TODO: implement similarly to self::getOutput(), using getTaskStreams();
        // need better way of working with subprocesses

        // get whatever is in the input of the initial process step
        $process_properties = $this->getModel()->process->get($this->getEid());
        $input = $process_properties['input'];
        $input_collection = self::unstringifyCollectionEids($input);

        $input_collection = \Flexio\Object\Collection::create();
        foreach ($input_collection as $input)
        {
            $input_collection->push($input['eid']);
        }

        return $input_collection;
    }

    public function getOutput() : \Flexio\Object\Collection
    {
        $task_identifier = false; // last task
        $input_collection = \Flexio\Object\Collection::create();
        $output_collection = \Flexio\Object\Collection::create();
        $this->getTaskStreams($input_collection, $output_collection, $task_identifier);

        return $output_collection;
    }

    public function getTaskStreams(&$input_collection, &$output_collection, $task_eid = false) // TODO: add input parameter types
    {
        // returns a collection of input streams for the specified task of a
        // process; if no task is specified, the streams from the last subprocess
        // are used
        $input_collection = \Flexio\Object\Collection::create();
        $output_collection = \Flexio\Object\Collection::create();

        // get the suprocesses
        $process_info = $this->get();
        $subprocesses = $process_info['subprocesses'];
        $subprocess_count = count($subprocesses);
        if ($subprocess_count < 1) // if there isn't any subprocess, there's no items
            return;

        // find the subprocess with the relevant items
        $specified_subprocess = false;
        if ($task_eid === false)
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

        // get the streams
        $input_stream_list = $specified_subprocess['input'];
        foreach ($input_stream_list as $item)
        {
            $input_collection->push($item['eid']);
        }

        $output_stream_list = $specified_subprocess['output'];
        foreach ($output_stream_list as $item)
        {
            $output_collection->push($item['eid']);
        }
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

    public function setError($code, $message = null, $file = null, $line = null) : \Flexio\Object\Process // TODO: add input parameter types
    {
        $this->errors[] = array('code' => $code, 'message' => $message, 'file' => $file, 'line' => $line);
        return $this;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors() : bool
    {
        if (empty($this->errors))
            return false;

        return true;
    }

    public function clearErrors() : \Flexio\Object\Process
    {
        $this->errors = array();
        return $this;
    }

    public function failValidation($validator, $file = null, $line = null) // TODO: add input parameter types
    {
        $errors = $validator->getErrors();
        foreach ($errors as $e)
        {
            $this->setError($e['code'], $e['message'], $file, $line);
        }
        return;
    }

    public function fail($code = '', $message = null, $file = null, $line = null) // TODO: add input parameter types
    {
        $this->setError($code, $message, $file, $line);
        return;
    }

    private function setCurrentExecutingSubProcess(string $subprocess)
    {
        // if the subprocess is the same, we're done
        if ($subprocess === $this->current_executing_subprocess_eid)
            return;

        $this->last_percentage_saved = false;
        $this->current_executing_subprocess_eid = $subprocess;
    }

    private function getCurrentExecutingSubProcess()
    {
        return $this->current_executing_subprocess_eid;
    }

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

    private function createTaskSubProcess(string $main_process_eid, string $sub_process_eid, array $step)
    {
        $subprocess_properties = array();
        $subprocess_properties['process_status'] = \Model::PROCESS_STATUS_PENDING;
        $subprocess_properties['process_eid'] = $main_process_eid;
        $subprocess_properties['parent_eid'] = $sub_process_eid;
        $subprocess_properties['task_type'] = $step['type'] ?? '';
        $subprocess_properties['parent_eid'] = $sub_process_eid;
        $subprocess_properties['task'] = json_encode($step);
        $subprocess_properties['input'] = json_encode("[]"); // set by the loop
        $subprocess_properties['output'] = json_encode("[]"); // set by the loop

        $process_model = $this->getModel()->process;
        return $process_model->create($subprocess_properties, false);  // true creates main process; false subprocess
    }

    private function execute()
    {
        /*
        global $g_procerr_info;

        // this shutdown function should only be registered once
        if (!isset($g_procerr_info))
        {
            // turn off all error reporting, as it will be handled by this error handler
            error_reporting(0);

            $current_process_eid = $this->getEid();
            $current_subprocess_eid = $this->getCurrentExecutingSubProcess();

            $g_procerr_info = array(
                'current_process_eid' => $current_process_eid,
                'current_subprocess_eid' => $current_subprocess_eid
            );

            register_shutdown_function(function() {

                global $g_procerr_info;

                // did a fatal PHP error happen?
                $err = error_get_last();
                if (isset($err))
                {
                    // yes -- set the process state to failed
                    $process_model = $this->getModel()->process;

                    $subprocess_params = array();
                    $subprocess_params['process_status'] = \Model::PROCESS_STATUS_FAILED;
                    $process_model->set($g_procerr_info['current_subprocess_eid'], $subprocess_params);

                    $process_params = array();
                    $process_params['process_info'] = json_encode($err);
                    $process_params['process_status'] = \Model::PROCESS_STATUS_FAILED;
                    $process_model->set($g_procerr_info['current_process_eid'], $process_params);
                }
            });
        }
        */


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

        // create stream inputs and output collections
        $input = \Flexio\Object\Collection::create();
        $output = \Flexio\Object\Collection::create();

        // STEP 1: get the list of subprocesses and reset the current subprocess
        $subprocesses = $local_properties['subprocesses'];
        $this->setCurrentExecutingSubProcess(false);

        // STEP 2: set any initial input in the input record; these may be set
        // by an experimental api endpoint
        $current_process_properties = $this->getModel()->process->get($this->getEid());
        $current_input = $current_process_properties['input'];
        $current_input_collection = self::unstringifyCollectionEids($current_input);
        $input->set($current_input_collection);

        // TODO: experimental:
        // STEP 3: add system and user-supplied variables
        $user_variables = $this->getParams();
        if (count($user_variables ) > 0)
        {
            // separate out the file upload variables from the task variables;
            // file upload variables (those with a valid stream eid) go into
            // the initial input; TODO: for now, this assumes that all the form
            // info should be processed at the beginning of the pipe; but it may
            // be that it should be processed as it occurs in the pipe
            foreach ($user_variables as $name => $value)
            {
                if (\Flexio\Base\Eid::isValid($value))
                {
                    $stream = \Flexio\Object\Stream::load($value);
                    if ($stream !== false)
                        $input->push($stream);
                }
            }
        }

        // STEP 4: iterate through the subprocesses and run each one
        $has_errors = false;
        $cache_used_on_subprocess = false;

        foreach ($subprocesses as $sp)
        {
            // get the subprocess eid and task
            $subprocess_eid = $sp['eid'];
            $task = $sp['task'];

            // set the current subprocess
            $this->setCurrentExecutingSubProcess($subprocess_eid);

            // save the input from the output of the previous step
            $subprocess_params = array();
            $subprocess_params['input'] = self::stringifyCollectionEids($input);
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
                $result_exists = $this->findCachedResult($implementation_revision, $task, $input, $output);
                if ($result_exists)
                    $cache_used = true;
            }

            if ($result_exists === false)
            {
                // execute the step and generate a result hash
                $this->executeStep($task, $this, $input, $output);
                $process_hash = $this->generateTaskHash($implementation_revision, $task, $input);
            }

            // if the implementation has changed during the task, the result is
            // unreliable; set an error so that the process fails
            $implementation_revision_update = \Flexio\System\System::getGitRevision();
            if ($implementation_revision !== $implementation_revision_update)
                $this->fail(\Flexio\Base\Error::GENERAL, _(''), __FILE__, __LINE__);

            // if there are errors, fail the job
            $process_status = \Model::PROCESS_STATUS_COMPLETED;
            if ($this->hasErrors())
            {
                $has_errors = true;
                $process_hash = ''; // don't cache failures
                $process_status = \Model::PROCESS_STATUS_FAILED;
            }

            // if the cache is used, track it for the overall process information
            if ($cache_used === true)
                $cache_used_on_subprocess = true;

            // save the output from the last step
            $subprocess_params = array();
            $subprocess_params['output'] = self::stringifyCollectionEids($output);
            $subprocess_params['finished'] = self::getProcessTimestamp();
            $subprocess_params['process_status'] = $process_status;
            $subprocess_params['process_hash'] = $process_hash;
            $subprocess_params['cache_used'] = $cache_used === true ? 'Y' : 'N';
            $this->getModel()->process->set($subprocess_eid, $subprocess_params);

            // set the input for the next job step to be the output from the previous
            // step; reset the output for the next job step
            $input->set($output);
            $output->clear();

            // if the step failed, stop the job
            if ($has_errors === true)
                break;
        }

	    // reset the current subprocess
        $this->setCurrentExecutingSubProcess(false);

        // set final job status
        $process_params = array();
        $process_params['finished'] = self::getProcessTimestamp();
        $process_params['process_status'] = $has_errors ? \Model::PROCESS_STATUS_FAILED : \Model::PROCESS_STATUS_COMPLETED;
        $process_params['cache_used'] = $cache_used_on_subprocess === true ? 'Y' : 'N';
        $this->getModel()->process->set($this->getEid(), $process_params);

        // if we're in debug mode and the process failed, set the error info in the process status
        if (IS_DEBUG() && $has_errors === true)
        {
            $process_info = $this->getProcessInfo();
            $process_info['errors'] = $this->getErrors();
            $this->setProcessInfo($process_info);
        }

        // clear the cache
        $this->clearCache();
    }

    private function executeStep($task, $process, &$input, &$output) // TODO: add input parameter types
    {
        // if the process is something besides running, we're done
        $status = $this->getModel()->process->getProcessStatus($this->getEid());
        if ($status !== \Model::PROCESS_STATUS_RUNNING)
            return; // nothing to do, but we're still ok

        // TODO: experimental; set task variables from the process variable list;
        // replace task info here so that each task has access to updated variables
        // from the previous step; right now, since the setParams() is on the Task
        // object (which is an array of individual steps), we have to take the task
        // step supplied in this step, and wrap it in an array, then unwrap it
        // after parameterization in order to use the Task object function; perhaps
        // parameterization should be on the base job object?

        $environment_variables = $this->getEnvironmentParams();
        $user_variables = $this->getParams();

        // merge in this order so that user-supplied variables don't override environment variables
        $variables = array_merge($user_variables, $environment_variables);
        if (count($variables) > 0)
        {
            $task_wrapper = \Flexio\Object\Task::create()->push($task);
            $updated_task = $task_wrapper->setParams($variables)->get();
            if ($updated_task !== false && count($updated_task) > 0)
                $task = $updated_task[0];
        }

        // create the job with the task
        $job = self::createJob($this, $task);
        if ($job === false)
            return $process->fail(\Flexio\Base\Error::INVALID_PARAMETER, _(''), __FILE__, __LINE__);

        try
        {
            // set the job input, run the job, and get the output
            $job->getInput()->set($input);
            $job->run();
            $output = $job->getOutput();
        }
        catch (\Exception $e)
        {
            if (isset($GLOBALS['g_config']->debug_error_log))
            {
                $message = $e->getMessage();
                $task = $job->getProperties();
                $json = json_encode($task);
                file_put_contents($GLOBALS['g_config']->debug_error_log, "Job exception caught '$message'; json was $json\n\n", FILE_APPEND);
            }

            return $process->fail(\Flexio\Base\Error::GENERAL, _(''), __FILE__, __LINE__);
        }
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

    private function populateCache()
    {
        // get the properties
        $local_properties = $this->getProperties();
        if ($local_properties === false)
            return false;

        // save the properties
        $this->properties = $local_properties;
        $this->eid_status = $local_properties['eid_status'];
        return true;
    }

    private function getProperties()
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
            "params=input_params": null,
            "started_by" : null,
            "started" : null,
            "finished" : null,
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
            return false;

        // unpack the primary process task json
        $task = @json_decode($properties['task'],true);
        if ($task !== false)
            $properties['task'] = $task;

        // unpack the primary process task input
        $input_params = @json_decode($properties['params']); // unpack as an object
        if ($input_params  !== false)
            $properties['params'] = $input_params;

        // unpack the primary process process info json
        $process_info = @json_decode($properties['process_info'],true);
        if ($process_info !== false)
            $properties['process_info'] = $process_info;

        // get the subprocesses
        $process_model = $this->getModel()->process;
        $process_tree = $process_model->getProcessTree($this->getEid());
        if ($process_tree === false)
            return false;

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

            // further fill out the input/output info with names, paths, etc
            $p['input'] = self::populateProcessIOStreamInfo($p['input']);
            $p['output'] = self::populateProcessIOStreamInfo($p['output']);

            // save the info
            $subprocesses[] = $p;
        }

        $properties['subprocesses'] = $subprocesses;
        return $properties;
    }

    private function populateProcessIOStreamInfo(array $stream_eid_arr)
    {
        $result = array();
        foreach ($stream_eid_arr as $item)
        {
            if (!isset($item['eid']))
                continue;

            $stream_eid = $item['eid'];
            $stream = \Flexio\Object\Stream::load($stream_eid);
            if ($stream === false)
                continue;

            $stream_properties = $stream->get();

            $result_item = array();
            $result_item['eid'] = $stream_properties['eid'];
            $result_item['eid_type'] = $stream_properties['eid_type'];
            $result_item['name'] = $stream_properties['name'];
            $result_item['path'] = $stream_properties['path'];
            $result_item['mime_type'] = $stream_properties['mime_type'];
            $result_item['size'] = $stream_properties['size'];

            $result[] = $result_item;
        }

        return $result;
    }

    private function findCachedResult($implementation_revision, $task, $input, &$output) : bool // TODO: add input parameter types
    {
        // find the hash for the input and the task
        $hash = self::generateTaskHash($implementation_revision, $task, $input);
        if ($hash === false)
            return false;

        $process_output = $this->getModel()->process->getOutputByHash($hash);
        if ($process_output === false)
            return false;

        $process_output = json_decode($process_output,true);
        if (!is_array($process_output))
            return false;

        foreach ($process_output as $o)
        {
            $eid = $o['eid'];
            $output->push($eid);
        }

        return true;
    }

    private function generateTaskHash($implementation_version, $task, $input) // TODO: add input parameter types
    {
        // if we dont' have an implementation version or an invalid implementation
        // version (git revision), don't cache anything
        if (!is_string($implementation_version) && strlen($implementation_version) < 40)
            return false;

        // generate an md5 hash the uniquely identifies the combination
        // of the task and the input
        if (!is_array($task) || !($input instanceof \Flexio\Object\Collection))
            return false;

        // require something more than an empty task and input
        $task_input = $input->enum();
        if (count($task) === 0 && count($task_input) === 0)
            return false;

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

        $task_type = $task['type'] ?? false;
        $task_parameters = $task['params'] ?? false;

        if ($task_type === false || $task_parameters === false)
            return false;

        $encoded_task_type = json_encode($task_type);
        $encoded_task_parameters = json_encode($task_parameters);
        $encoded_task_input = json_encode($task_input);

        $hash = md5(
            $implementation_version .
            $encoded_task_type .
            $encoded_task_parameters .
            $encoded_task_input
        );

        return $hash;
    }

    private static function createJob($process, $task) // TODO: add input parameter types
    {
        if (!($process instanceof \Flexio\Object\Process))
            return false;

        if (!isset($task))
            return false;

        if (!isset($task['type']))
            return false;

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
            return false;

        // try to find the job file
        $class_name_parts = explode("\\", $full_class_name);
        if (!isset($class_name_parts[3]))
            return false;

        $class_file = \Flexio\System\System::getApplicationDirectory() . DIRECTORY_SEPARATOR . 'jobs' . DIRECTORY_SEPARATOR . $class_name_parts[3] . '.php';
        if (!@file_exists($class_file))
            return false;

        // load the job's php file and instantiate the job object
        include_once $class_file;
        $job = $full_class_name::create($process, $task);

        if ($job === false)
            return false;

        if (!($job instanceof \Flexio\Jobs\IJob))
            return false;

        return $job;
    }

    private static function stringifyCollectionEids($collection) : string // TODO: add input parameter types
    {
        $result = array();
        if (!($collection instanceof \Flexio\Object\Collection))
            return json_encode($result);

        $stream_objects = $collection->enum();
        foreach ($stream_objects as $stream)
        {
            $stream_eid = $stream->getEid();
            if ($stream_eid === false)
                continue;

            $result[] = array('eid' => $stream_eid);
        }

        return json_encode($result);
    }

    private static function unstringifyCollectionEids(string $string) : \Flexio\Object\Collection
    {
        $collection = \Flexio\Object\Collection::create();
        $items = json_decode($string,true);
        if (!is_array($items))
            return $collection;

        foreach ($items as $i)
        {
            $collection->push($i['eid']);
        }

        return $collection;
    }

    private static function formatProcessInfo($params, $task_type, $finished = true) : array // TODO: add input parameter types
    {
        $status_text = "";
        $filename = $params['name'] ?? '';

        if ($finished === true)
        {
            $status_text = "Finished";
        }
         else
        {
            $status_text = "Processing $filename";
            $task_manifest = \Flexio\Object\Task::manifest();

            foreach ($task_manifest as $t)
            {
                if ($t['type'] !== $task_type)
                    continue;

                $status_text = $t['verb'] . " $filename";
                break;
            }
        }

        // configure the output
        $result = array();
        $result['name'] = $params['name'] ?? '';
        $result['description'] = $status_text;

        return $result;
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
        $time = microtime(true);
        $time_micropart = sprintf("%06d", ($time - floor($time)) * 1000000);
        $date = new \DateTime(date('Y-m-d H:i:s.' . $time_micropart, $time));
        return ($date->format("Y-m-d H:i:s.u"));
    }
}
