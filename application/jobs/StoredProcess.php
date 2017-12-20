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

    public function __construct()
    {
    }

    public static function create(\Flexio\Object\Process $procobj) : \Flexio\Jobs\StoredProcess
    {
        $object = new static();
        $object->procobj = $procobj;
        $object->engine = \Flexio\Jobs\Process::create();
        $object->engine->setTasks($this->procobj->getTasks());
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
            //\Flexio\Jobs\StoredProcess::background_entry($this->procobj->getEid());
        }
         else
        {
            return $this->run_internal();
            //return \Flexio\Jobs\StoredProcess::background_entry($this->procobj->getEid());
        }

        return $this;
    }

    public static function background_entry($process_eid) : \Flexio\Jobs\StoredProcess
    {
        $proc = \Flexio\Jobs\StoredProcess::load($process_eid);
        return $proc->run_internal();
    }

    private function run_internal() : \Flexio\Jobs\StoredProcess
    {
        // STEP 2: add the environment variables in
        $environment_variables = $this->getEnvironmentParams();
        $user_variables = $this->getParams();
        $this->engine->setParams(array_merge($user_variables, $environment_variables));

        // STEP 3: get events for logging, if necessary
        $this->engine->addEventHandler([$this->procobj, 'handleEvent']);

        // STEP 4: execute the job
        $this->engine->execute();

        // STEP 6: save final job output and status
        $process_params = array();
        $process_params['finished'] = self::getProcessTimestamp();
        $process_params['process_status'] = $this->hasError() ? \Flexio\Jobs\Process::STATUS_FAILED : \Flexio\Jobs\Process::STATUS_COMPLETED;
        $process_params['cache_used'] = 'N';
        $this->procobj->set($process_params);

        //var_dump($this->procobj->get());

        return $this;
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
}

