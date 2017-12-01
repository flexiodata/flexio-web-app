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


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Abstract.php';

class StoredProcess implements \Flexio\Jobs\IProcess
{
    private $engine;            // instance of \Flexio\Jobs\Process
    private $procobj;           // instance of \Flexio\Object\Process -- used only during execution phase
    private $owner = null;      // user eid owner (will be passed to \Flexio\Object\Process::setOwner())
    private $created_by = null; // created by eid ( "   "   "      "  "  )
    private $rights = null;     // rights         ( "   "   "      "  "  )
    private $assoc_pipe = null; // associated pipe object; if set, $assoc_pipe->addProcess() will be called with the process object
    private $stdout = null;

    public function __construct()
    {
        $this->engine = \Flexio\Jobs\Process::create();
        $this->procobj = null;
    }

    public static function create() : \Flexio\Jobs\StoredProcess
    {
        $object = new static();
        return $object;
    }

    public function addEventHandler($handler)
    {
        return $this->engine->addEventHandler($handler);
    }

    public function setMetadata(array $metadata)
    {
        return $this->engine->setMetadata($handler);
    }

    public function getMetadata() : array
    {
        return $this->engine->getMetadata();
    }

    public function addTasks(array $tasks)
    {
        return $this->engine->addTasks($tasks);
    }

    public function getTasks() : array
    {
        return $this->engine->getTasks();
    }

    public function setParams(array $arr)
    {
        return $this->engine->setParams($arr);
    }

    public function getParams() : array
    {
        return $this->engine->getParams();
    }

    public function addFile(string $name, \Flexio\Base\IStream $stream)
    {
        return $this->engine->addFile($name, $stream);
    }

    public function setStdin(\Flexio\Base\IStream $stream)
    {
        return $this->engine->setStdin($stream);
    }

    public function getStdin() : \Flexio\Base\IStream
    {
        return $this->engine->getStdin();
    }

    public function setStdout(\Flexio\Base\IStream $stream)
    {
        return $this->engine->setStdout($stream);
    }

    public function getStdout() : \Flexio\Base\IStream
    {
        return $this->engine->getStdout();
    }

    public function setResponseCode(int $code)
    {
        return $this->engine->setResponseCode($code);
    }

    public function getResponseCode() : int
    {
        return $this->engine->getResponseCode();
    }

    public function setError(string $code = '', string $message = null, string $file = null, int $line = null, string $type = null, array $trace = null)
    {
        return $this->engine->setError($code, $message, $file, $line, $type, $trace);
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

    public function execute()
    {
        // calling this function will execute the job locally without creating a
        // database process record, so no statistics will be serialized
        return $this->engine->execute();
    }


    // these functions proxy information to the internal \Flexio\Object\Process $procobj, once it has been created

    public function setOwner(string $user_eid) : \Flexio\Jobs\StoredProcess
    {
        $this->owner = $user_eid;
        return $this;
    }

    public function setCreatedBy(string $user_eid) : \Flexio\Jobs\StoredProcess
    {
        $this->created_by = $user_eid;
        return $this;
    }

    public function setRights(array $rights) : \Flexio\Jobs\StoredProcess
    {
        $this->rights = $rights;
        return $this;
    }

    public function setAssocPipe(\Flexio\Object\Pipe $pipe) : \Flexio\Jobs\StoredProcess
    {
        $this->assoc_pipe = $pipe;
        return $this;
    }





    public function run(bool $background = true) : \Flexio\Jobs\StoredProcess
    {
        // create a new process object
        $this->procobj = \Flexio\Object\Process::create([
            'process_mode' => \Flexio\Jobs\Process::STATUS_RUNNING,
            'task' => $this->engine->getTasks()
        ]);

        if (!is_null($this->owner))         $this->procobj->setOwner($this->owner);
        if (!is_null($this->created_by))    $this->procobj->setCreatedBy($this->created_by);
        if (!is_null($this->rights))        $this->procobj->setRights($this->rights);
        if (!is_null($this->assoc_pipe))    $this->assoc_pipe->addProcess($this->procobj);

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

            $input = [
                'params' => $this->engine->getParams(),
                'stdin' => $stdin->getEid()
            ];


            $this->procobj->set(['input' => $input]);

            \Flexio\System\Program::runInBackground("\Flexio\Object\Process::background_entry('$process_eid')");
        }
         else
        {
            return $this->run_internal();
        }

        $process_eid = $procobj->getEid();

        return $this;
    }

    public static function background_entry() : \Flexio\Jobs\StoredProcess
    {
        $proc = \Flexio\Object\StoredProcess::create();

        return $proc->run_internal();
    }

    private function run_internal() : \Flexio\Jobs\StoredProcess
    {
        // track what version of the task implementation we're using (more granular than task version,
        // which may or may not be updated with small logic changes)
        //$implementation_revision = \Flexio\System\System::getGitRevision();

        // set initial job status
        $this->procobj->set([
            'started' => self::getProcessTimestamp(),
            'process_status' => \Flexio\Jobs\Process::STATUS_RUNNING
            //'impl_revision' => $implementation_revision;
        ]);


        // STEP 1: get the process properties
        $current_process_properties = $this->procobj->get();
        $process_tasks = $current_process_properties['task'];

        // STEP 2: get the parameters and add the environment variables in
        $environment_variables = $this->getEnvironmentParams();
        $user_variables = $this->getParams();
        $variables = array_merge($user_variables, $environment_variables);

        // STEP 3: create the process engine and configure it
        $process_engine = \Flexio\Jobs\Process::create();
        $process_engine->addTasks($process_tasks);
        $process_engine->setParams($variables);
        $process_engine->getStdin()->copy($this->getStdin());
        $process_engine->addEventHandler([$this, 'handleEvent']);

        // STEP 4: execute the job
        $process_engine->execute();

        // STEP 5: patch through the response code and any error
        $this->response_code = $process_engine->getResponseCode();
        if ($process_engine->hasError())
            $this->error = $process_engine->getError();

        // STEP 6: save final job output and status
        $process_params = array();
        $process_params['finished'] = self::getProcessTimestamp();
        $process_params['process_status'] = $this->hasError() ? \Flexio\Jobs\Process::STATUS_FAILED : \Flexio\Jobs\Process::STATUS_COMPLETED;
        $process_params['cache_used'] = 'N';
        $this->procobj->set($process_params);

        // STEP 8: save the process output; TODO: saving stdout also writes to the database; could we consolidate with other writes for efficiency?
        $this->setStdout($process_engine->getStdout());

        return $this;
    }


    public function handleEvent(string $event, \Flexio\Jobs\IProcess $process_engine)
    {
        // if we're not in build mode, don't do anything with events
        if ($this->procobj->getMode() !== \Flexio\Jobs\Process::MODE_BUILD)
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


    private static function createStorableStream(\Flexio\Base\IStream $stream) : \Flexio\Object\Stream
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
            if ($stream->getMimeType() === \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE)
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

}
