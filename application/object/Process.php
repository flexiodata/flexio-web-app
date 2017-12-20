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


class Process extends \Flexio\Object\Base
{
    // variables and errors
    private $response_code;
    private $error;

    public function __construct()
    {
        $this->setType(\Model::TYPE_PROCESS);
        $this->response_code = \Flexio\Jobs\Process::RESPONSE_NORMAL;
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
            $properties['process_mode'] = \Flexio\Jobs\Process::MODE_RUN;

        $object = new static();
        $model = $object->getModel();
        $local_eid = $model->create($object->getType(), $properties);

        $object->setEid($local_eid);
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

    public function get() : array
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties;
    }
/*
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
            case \Flexio\Jobs\Process::STATUS_UNDEFINED:
            case \Flexio\Jobs\Process::STATUS_PENDING:
                break;

            // job is already running or has been run, so don't do anything
            case \Flexio\Jobs\Process::STATUS_WAITING:
            case \Flexio\Jobs\Process::STATUS_RUNNING:
            case \Flexio\Jobs\Process::STATUS_CANCELLED:
            case \Flexio\Jobs\Process::STATUS_FAILED:
            case \Flexio\Jobs\Process::STATUS_COMPLETED:
                return $this;

            // job is paused, so resume it
            case \Flexio\Jobs\Process::STATUS_PAUSED:
                $process_model->setProcessStatus($this->getEid(), \Flexio\Jobs\Process::STATUS_RUNNING);
                return $this;
        }

        // STEP 2: set the status
        $process_model->setProcessStatus($this->getEid(), \Flexio\Jobs\Process::STATUS_RUNNING);

        // STEP 3: run the job
        if ($background !== true)
        {
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
        $object->execute();
        return true;
    }
*/
    public function pause() : \Flexio\Object\Process
    {
        $this->clearCache();
        $process_model = $this->getModel()->process;
        $process_status = $process_model->getProcessStatus($this->getEid());

        switch ($process_status)
        {
            // only allow jobs that are running to be paused
            case \Flexio\Jobs\Process::STATUS_RUNNING:
                $process_model->setProcessStatus($this->getEid(), \Flexio\Jobs\Process::STATUS_PAUSED);
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
            case \Flexio\Jobs\Process::STATUS_CANCELLED:
            case \Flexio\Jobs\Process::STATUS_FAILED:
            case \Flexio\Jobs\Process::STATUS_COMPLETED:
                return $this;
        }

        $process_model->setProcessStatus($this->getEid(), \Flexio\Jobs\Process::STATUS_CANCELLED);
        return $this;
    }

    public function getProcessStatus() : string
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['process_status'];
    }

    public function setTasks(array $task) : \Flexio\Object\Process
    {
        $properties = array();
        $properties['task'] = $task;
        return $this->set($properties);
    }

    public function getTasks() : array
    {
        $properties = $this->get();
        return $properties['task'];
    }

    public function getMode() : string
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['process_mode'];
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

    public function setError(string $code = '', string $message = null, string $file = null, int $line = null, string $type = null, array $trace = null)
    {
        // only save the first error we come to
        if ($this->hasError())
            return;

        if (!isset($message))
            $message = \Flexio\Base\Error::getDefaultMessage($code);

        $this->error = array('code' => $code, 'message' => $message, 'file' => $file, 'line' => $line, 'type' => $type, 'trace' => $trace);
    }

    public function getResponseCode() : int
    {
        return $this->response_code;
    }

    public function handleEvent(string $event, \Flexio\IFace\IProcess $process_engine)
    {
        // if we're not in build mode, don't do anything with events
        if ($this->getMode() !== \Flexio\Jobs\Process::MODE_BUILD)
            return;

        switch ($event)
        {
            // don't do anything if it's an event we don't care about
            default:
            case \Flexio\Jobs\Process::EVENT_STARTING:
            case \Flexio\Jobs\Process::EVENT_FINISHED:
                return;

            case \Flexio\Jobs\Process::EVENT_STARTING_TASK:
                $this->startLog($process_engine);
                break;

            case \Flexio\Jobs\Process::EVENT_FINISHED_TASK:
                $this->finishLog($process_engine);
                break;
        }
    }

    private function execute()
    {
        // TODO: need to handle process cancellation

        // track what version of the task implementation we're using
        // (more granular than task version, which may or may not be updated
        // with small logic changes)
        //$implementation_revision = \Flexio\System\System::getGitRevision();

        // set initial job status
        $process_params = array();
        $process_params['started'] = self::getProcessTimestamp();
        $process_params['process_status'] = \Flexio\Jobs\Process::STATUS_RUNNING;
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

        // STEP 3: get the parameters and add the environment variables in
        $environment_variables = $this->getEnvironmentParams();
        $user_variables = $this->getParams();
        $variables = array_merge($user_variables, $environment_variables);

        // STEP 4: create the process engine and configure it
        $process_engine = \Flexio\Jobs\Process::create();
        $process_engine->setTasks($process_tasks);
        $process_engine->setParams($variables);
        $process_engine->getStdin()->copy($this->getStdin());
        $process_engine->addEventHandler([$this, 'handleEvent']);

        // STEP 5: execute the job
        $process_engine->execute();

        // STEP 6: patch through the response code and any error
        $this->response_code = $process_engine->getResponseCode();
        if ($process_engine->hasError())
            $this->error = $process_engine->getError();

        // STEP 7: save final job output and status
        $process_params = array();
        $process_params['finished'] = self::getProcessTimestamp();
        $process_params['process_status'] = $this->hasError() ? \Flexio\Jobs\Process::STATUS_FAILED : \Flexio\Jobs\Process::STATUS_COMPLETED;
        $process_params['cache_used'] = 'N';
        $this->getModel()->process->set($this->getEid(), $process_params);

        // STEP 8: save the process output; TODO: saving stdout also writes to the database; could we consolidate with other writes for efficiency?
        //$this->setStdout($process_engine->getStdout());

        // clear the process object cache
        $this->clearCache();
    }

    public function getLog() : array
    {
        $process_model = $this->getModel()->process;
        $log_entries = $process_model->getProcessLogEntries($this->getEid());
        if ($log_entries == false)
            return array();

        $result = array();
        foreach ($log_entries as $entry)
        {
            // unpack the task
            $task = @json_decode($entry['task'],true);
            if ($task !== false)
                $entry['task'] = $task;

            // unpack the input
            $input = @json_decode($entry['input'],true);
            if ($input !== false)
                $entry['input'] = $input;

            // unpack the output
            $output = @json_decode($entry['output'],true);
            if ($output !== false)
                $entry['output'] = $output;

            $result[] = $entry;
        }

        return $result;
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
            "created" : null,
            "updated" : null
        }
        ';

        // get the primary process info
        $query = json_decode($query);
        $properties = \Flexio\Object\Query::exec($this->getEid(), $query);

        // sanity check: if the data record is missing, then eid will be null
        if (!$properties || ($properties['eid'] ?? null) === null)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // unpack the primary process task json
        if (isset($properties['task']))
        {
            $task = @json_decode($properties['task'],true);
            if ($task !== false)
                $properties['task'] = $task;
        }

        // unpack the primary process process info json
        if (isset($properties['process_info']))
        {
            $process_info = @json_decode($properties['process_info'],true);
            if ($process_info !== false)
                $properties['process_info'] = $process_info;
        }

        return $properties;
    }

    private function setProcessInfo(array $info) : \Flexio\Object\Process
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

    private function getProcessInfo() : array
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['process_info'];
    }

    private function startLog(\Flexio\IFace\IProcess $process_engine)
    {
        $storable_stream_info = self::getStreamLogInfo($process_engine);
        $process_info = $process_engine->getStatusInfo();
        $task = $process_info['current_task'] ?? array();

        // create a log record
        $params = array();
        $params['task_op'] = $task['op'] ?? '';
        $params['task'] = json_encode($task);
        $params['started'] = self::getProcessTimestamp();
        $params['input'] = json_encode($storable_stream_info);
        $params['log_type'] = \Flexio\Jobs\Process::LOG_TYPE_SYSTEM;
        $params['message'] = '';

        $log_eid = $this->getModel()->process->log(null, $this->getEid(), $params);

        // pass on the log entry for the log finish function
        $process_engine->setMetadata(array('log_eid' => $log_eid));
    }

    private function finishLog(\Flexio\IFace\IProcess $process_engine)
    {
        // make sure we have a log eid record to complete
        $process_engine_metadata = $process_engine->getMetadata();
        if (!isset($process_engine_metadata['log_eid']))
            return;

        $log_eid = $process_engine_metadata['log_eid'];
        $storable_stream_info = self::getStreamLogInfo($process_engine);
        $process_info = $process_engine->getStatusInfo();
        $task = $process_info['current_task'] ?? array();

        // update the log record
        $params = array();
        $params['task_op'] = $task['op'] ?? '';
        $params['task'] = json_encode($task);
        $params['finished'] = self::getProcessTimestamp();
        $params['output'] = json_encode($storable_stream_info);
        $params['log_type'] = \Flexio\Jobs\Process::LOG_TYPE_SYSTEM;
        $params['message'] = '';

        $this->getModel()->process->log($log_eid, $this->getEid(), $params);
    }

    private static function getStreamLogInfo(\Flexio\IFace\IProcess $process_engine) : array
    {
        $stdin = $process_engine->getStdin();
        $stdout = $process_engine->getStdout();

        $storable_stdin = self::createStorableStream($stdin);
        $storable_stdout = self::createStorableStream($stdout);

        $info = array();
        $info['stdin'] = array('eid' => $storable_stdin->getEid());
        $info['stdout'] = array('eid' => $storable_stdout->getEid());
        return $info;
    }

    private static function createStorableStream(\Flexio\IFace\IStream $stream) : \Flexio\Object\Stream
    {
        $properties['path'] = \Flexio\Base\Util::generateHandle();
        $properties = array_merge($stream->get(), $properties);
        $storable_stream = \Flexio\Object\Stream::create($properties);

        // copy from the input stream to the storable stream
        $streamreader = $stream->getReader();
        $streamwriter = $storable_stream->getWriter();

        while (true)
        {
            $row = false;
            if ($stream->getMimeType() === \Flexio\Base\ContentType::FLEXIO_TABLE)
                $row = $streamreader->readRow();
                 else
                $row = $streamreader->read();

            if ($row === false)
                break;

            $streamwriter->write($row);
        }

        return $storable_stream;
    }

    private static function createMemoryStream(\Flexio\IFace\IStream $stream) : \Flexio\Base\Stream
    {
        $properties = $stream->get();
        unset($properties['path']);
        unset($properties['connection_eid']);
        $stream_memory = \Flexio\Base\Stream::create($properties);

        // copy from the input stream to the memory stream
        $streamreader = $stream->getReader();
        $streamwriter = $stream_memory->getWriter();

        while (true)
        {
            $row = false;
            if ($stream->getMimeType() === \Flexio\Base\ContentType::FLEXIO_TABLE)
                $row = $streamreader->readRow();
                 else
                $row = $streamreader->read();

            if ($row === false)
                break;

            $streamwriter->write($row);
        }

        return $stream_memory;
    }

    private static function getEnvironmentParams() : array
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

    private static function getProcessTimestamp() : string
    {
        // return the timestamp as accurately as we can determine
        $time_exact = microtime(true);
        $time_rounded = floor($time_exact);
        $time_micropart = sprintf("%06d", ($time_exact - $time_rounded) * 1000000);
        $date = new \DateTime(date('Y-m-d H:i:s.' . $time_micropart, (int)$time_rounded));
        return ($date->format("Y-m-d H:i:s.u"));
    }
}
