<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2017-12-01
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;


class StoredProcess implements \Flexio\IFace\IProcess
{
    private $engine;            // instance of \Flexio\Jobs\Process
    private $procobj;           // instance of \Flexio\Object\Process -- used only during execution phase
    private $procmode;

    public function __construct()
    {
    }

    public static function create(\Flexio\Object\Process $procobj) : \Flexio\Jobs\StoredProcess
    {
        $object = new static();
        $object->procobj = $procobj;
        $object->engine = \Flexio\Jobs\Process::create();
        $object->engine->setTasks($procobj->getTasks());
        $object->procmode = $procobj->getMode();
        return $object;
    }

    public static function load(string $process_eid) : \Flexio\Jobs\StoredProcess
    {
        $procobj = \Flexio\Object\Process::load($process_eid);
        return self::create($procobj);
    }

    public function addEventHandler($handler) : \Flexio\Jobs\StoredProcess
    {
        $this->engine->addEventHandler($handler);
        return $this;
    }

    public function setMetadata(array $metadata)
    {
        $this->engine->setMetadata($handler);
        return $this;
    }

    public function getMetadata() : array
    {
        return $this->engine->getMetadata();
    }

    public function setTasks(array $tasks) : \Flexio\Jobs\StoredProcess
    {
        $this->engine->setTasks($tasks);
        return $this;
    }

    public function getTasks() : array
    {
        return $this->engine->getTasks();
    }

    public function setParams(array $arr) : \Flexio\Jobs\StoredProcess
    {
        $this->engine->setParams($arr);
        return $this;
    }

    public function getParams() : array
    {
        return $this->engine->getParams();
    }

    public function addFile(string $name, \Flexio\IFace\IStream $stream) : \Flexio\Jobs\StoredProcess
    {
        $this->engine->addFile($name, $stream);
        return $this;
    }

    public function getFiles() : array
    {
        return $this->engine->getFiles();
    }

    public function setStdin(\Flexio\IFace\IStream $stream) : \Flexio\Jobs\StoredProcess
    {
        $this->engine->setStdin($stream);
        return $this;
    }

    public function getStdin() : \Flexio\IFace\IStream
    {
        return $this->engine->getStdin();
    }

    public function setStdout(\Flexio\IFace\IStream $stream) : \Flexio\Jobs\StoredProcess
    {
        $this->engine->setStdout($stream);
        return $this;
    }

    public function getStdout() : \Flexio\IFace\IStream
    {
        return $this->engine->getStdout();
    }

    public function setResponseCode(int $code) : \Flexio\Jobs\StoredProcess
    {
        $this->engine->setResponseCode($code);
        return $this;
    }

    public function getResponseCode() : int
    {
        return $this->engine->getResponseCode();
    }

    public function setError(string $code = '', string $message = null, string $file = null, int $line = null, string $type = null, array $trace = null) : \Flexio\IFace\IProcess
    {
        $this->engine->setError($code, $message, $file, $line, $type, $trace);
        return $this;
    }

    public function getError() : array
    {
        return $this->engine->getError();
    }

    public function hasError() : bool
    {
        return $this->engine->hasError();
    }

    public function getStatusInfo() : array
    {
        return $this->engine->getStatusInfo();
    }

    public function execute() : \Flexio\Jobs\StoredProcess
    {
        // calling this function will execute the job locally without creating a
        // database process record, so no statistics will be serialized
        $this->engine->execute();
        return $this;
    }

    public function run(bool $background = true) : \Flexio\Jobs\StoredProcess
    {
        $this->procobj->set([
            'process_status' => \Flexio\Jobs\Process::STATUS_RUNNING,
            'started' => self::getProcessTimestamp()
        ]);

        // STEP 3: run the job
        if ($background === true)
        {
            // job will run in background across process boundry; we'll serialize the
            // variables and streams here; they will be unserialized in the background
            // process in the static background_entry() function

            // stdin
            // filelist
            // params

            $storable_stdin = self::createStorableStream($this->engine->getStdin());
            $storable_stdout = self::createStorableStream($this->engine->getStdout());

            $input = [
                'params' => $this->engine->getParams(),
                'stream' => $storable_stdin->getEid()
            ];

            $output = [
                'stream' => $storable_stdout->getEid()
            ];

            $this->procobj->set(['input' => json_encode($input), 'output' => json_encode($output)]);
            $process_eid = $this->procobj->getEid();

            \Flexio\System\Program::runInBackground("\Flexio\Jobs\StoredProcess::background_entry('$process_eid')");
        }
         else
        {
            return $this->run_internal();
        }

        return $this;
    }

    public static function background_entry($process_eid) : \Flexio\Jobs\StoredProcess
    {
        $proc = \Flexio\Jobs\StoredProcess::load($process_eid);
        return $proc->run_internal();
    }

    public function handleEvent(string $event, \Flexio\IFace\IProcess $process_engine)
    {
        // if we're not in build mode, don't do anything with events;
        // TODO: store as a property so we don't have to ping the database
        if ($this->procmode !== \Flexio\Jobs\Process::MODE_BUILD)
            return;

        switch ($event)
        {
            // don't do anything if it's an event we don't care about
            default:
            case \Flexio\Jobs\Process::EVENT_STARTING:
            case \Flexio\Jobs\Process::EVENT_FINISHED:
                return;

            case \Flexio\Jobs\Process::EVENT_STARTING_TASK:
                $this->startLog();
                break;

            case \Flexio\Jobs\Process::EVENT_FINISHED_TASK:
                $this->finishLog();
                break;
        }
    }

    private function run_internal() : \Flexio\Jobs\StoredProcess
    {
        // STEP 1: add the environment variables in
        $environment_variables = $this->getEnvironmentParams();
        $user_variables = $this->getParams();
        $this->setParams(array_merge($user_variables, $environment_variables));

        // STEP 2: get events for logging, if necessary
        $this->addEventHandler([$this, 'handleEvent']);

        // STEP 3: execute the job
        $this->execute();

        // STEP 4: save final job output and status
        $process_params = array();
        $process_params['finished'] = self::getProcessTimestamp();
        $process_params['process_status'] = $this->hasError() ? \Flexio\Jobs\Process::STATUS_FAILED : \Flexio\Jobs\Process::STATUS_COMPLETED;
        $process_params['cache_used'] = 'N';
        $this->procobj->set($process_params);

        return $this;
    }

    private function startLog()
    {
        $process_engine = $this->engine;

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

        $log_eid = $this->procobj->addToLog($log_eid, $params);

        // pass on the log entry for the log finish function
        $process_engine->setMetadata(array('log_eid' => $log_eid));
    }

    private function finishLog()
    {
        $process_engine = $this->engine;

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

        $this->procobj->addToLog($log_eid, $params);
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

    private static function getProcessTimestamp() : string
    {
        // return the timestamp as accurately as we can determine
        $time_exact = microtime(true);
        $time_rounded = floor($time_exact);
        $time_micropart = sprintf("%06d", ($time_exact - $time_rounded) * 1000000);
        $date = new \DateTime(date('Y-m-d H:i:s.' . $time_micropart, (int)$time_rounded));
        return ($date->format("Y-m-d H:i:s.u"));
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

/*
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
*/
}

