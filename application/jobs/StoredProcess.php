<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
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
    private $current_log_eid;

    public function __construct()
    {
    }

    public static function create(\Flexio\Object\Process $procobj) : \Flexio\Jobs\StoredProcess
    {
        $object = new static();
        $object->procobj = $procobj;
        $object->engine = \Flexio\Jobs\Process::create();
        $object->procmode = $procobj->getMode();
        $object->current_log_eid = null;
        $object->setOwner($procobj->getOwner());
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

    public function setOwner(string $owner_eid) : \Flexio\Jobs\StoredProcess
    {
        $this->engine->setOwner($owner_eid);
        return $this;
    }

    public function getOwner() : string
    {
        return $this->engine->getOwner();
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

    public function setLocalFile(int $fileno, \Flexio\IFace\IStream $stream)
    {
        return $this->engine->setLocalFile($fileno, $stream);
    }

    public function getLocalFile(int $fileno)
    {
        return $this->engine->getLocalFile($fileno);
    }

    public function getLocalFiles()
    {
        return $this->engine->getLocalFiles();
    }

    public function setLocalFiles($files)
    {
        return $this->engine->setLocalFiles($files);
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

    public function addLocalConnection(string $identifier, array $connection_properties) : void
    {
        $this->engine->addLocalConnection($identifier, $connection_properties);
    }

    public function getLocalConnection(string $identifier) : ?array
    {
        return $this->engine->getLocalConnection($identifier);
    }

    public function getLocalConnections() : array
    {
        return $this->engine->getLocalConnections();
    }

    public function getConnection(string $identifier) : ?array
    {
        return $this->engine->getConnection($identifier);
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

    public function setError(string $code = '', string $message = null, string $file = null, int $line = null, string $type = null, string $trace = null) : \Flexio\IFace\IProcess
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

    public function validate(array $task) : array
    {
        return $this->engine->validate($task);
    }

    public function execute(array $task) : \Flexio\Jobs\StoredProcess
    {
        // calling this function will execute the job locally without creating a
        // database process record, so no statistics will be serialized
        $this->engine->execute($task);
        return $this;
    }

    public function stop() : \Flexio\Jobs\StoredProcess
    {
        $this->engine->cancel();
        return $this;
    }

    public function isStopped() : bool
    {
        return $this->engine->isStopped();
    }

    public function signal(string $event, array $properties) : \Flexio\Jobs\StoredProcess
    {
        $this->engine->signal($event, $properties);
        return $this;
    }

    public function run(bool $background = true) : \Flexio\Jobs\StoredProcess
    {
        $this->procobj->set([
            'process_status' => \Flexio\Jobs\Process::STATUS_RUNNING,
            'started' => \Flexio\Base\Util::getCurrentTimestamp()
        ]);

        // run the job
        if ($background === true)
        {
            // job will run in background across process boundry; we'll serialize the
            // variables and streams here; they will be unserialized in the background
            // process in the static background_entry() function

            // stdin
            // filelist
            // params

            $owned_by = $this->getOwner();
            $storable_stdin = self::createStorableStream($this->engine->getStdin(), $owned_by);
            $input = [
                'params' => $this->engine->getParams(),
                'stream' => $storable_stdin->getEid()
            ];
            $this->procobj->set(['input' => $input]);

            // TODO: no need to set the output in background mode; if we're in the build mode
            //$storable_stdout = self::createStorableStream($this->engine->getStdout(), $owned_by);
            //$output = [
            //    'stream' => $storable_stdout->getEid()
            //];
            //$this->procobj->set(['output' => $output]);

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

    public function handleEvent(string $event, array $process_info)
    {
        // if we're not in build mode, don't do anything with events;
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
                break;

            case \Flexio\Jobs\Process::EVENT_FINISHED_TASK:
                $this->updateProcessInfo($process_info);
                break;
        }
    }

    private function run_internal() : \Flexio\Jobs\StoredProcess
    {
        // STEP 1: add the environment variables in
        $environment_variables = $this->getEnvironmentParams();
        $mount_variables = $this->getMountParams();

        $user_variables = $this->getParams();
        $this->setParams(array_merge($user_variables, $mount_variables, $environment_variables));

        // STEP 2: get events for logging, if necessary
        $this->addEventHandler([$this, 'handleEvent']);

        // STEP 3: if we have an associative array, we have a top-level task, so simply
        // execute it; otherwise we have an array of tasks, so package them in a sequence job
        $task = $this->procobj->getTask();
        if (\Flexio\Base\Util::isAssociativeArray($task) === false)
            $task = array('op' => 'sequence', 'params' => array('items' => $task));
        $this->execute($task);

        // STEP 4: save final job output and status; only save the status if the status if it hasn't already been set
        $process_params = array();
        $process_params['finished'] = \Flexio\Base\Util::getCurrentTimestamp();
        if ($this->isStopped() === false)
        {
            $process_params['process_status'] = \Flexio\Jobs\Process::STATUS_COMPLETED;
            if ($this->hasError())
            {
                $process_info = array('error' => $this->getError());
                $process_info_str = json_encode($process_info, JSON_PARTIAL_OUTPUT_ON_ERROR); // don't allow bad characters that may exist in debugging info to cause encoding to cause another failure

                $process_params['process_status'] = \Flexio\Jobs\Process::STATUS_FAILED;
                $process_params['process_info'] = $process_info_str;
            }

            // if we're in build mode, create a storable stream to store the output
            if ($this->procmode === \Flexio\Jobs\Process::MODE_BUILD)
            {
                $owned_by = $this->getOwner();
                $storable_stdout = self::createStorableStream($this->getStdout(), $owned_by);

                $storable_stream_info = array();
                $process_params['output'] = array('stream' => $storable_stdout->getEid());
            }
        }
        $this->procobj->set($process_params);

        return $this;
    }

    private function updateProcessInfo(array $process_info) : void
    {
        // read the process object record and proxy information to the
        // process engine

        if ($this->procobj->getStatus() === \Flexio\Jobs\Process::STATUS_CANCELLED)
            $this->cancel();

        // TODO: proxy other information
    }

    private static function createStorableStream(\Flexio\IFace\IStream $stream, string $owned_by) : \Flexio\Object\Stream
    {
        $properties['path'] = \Flexio\Base\Util::generateHandle();
        $properties['owned_by'] = $owned_by;
        $properties = array_merge($stream->get(), $properties);
        $storable_stream = \Flexio\Object\Stream::create($properties);

        // copy from the input stream to the storable stream
        $streamreader = $stream->getReader();
        $streamwriter = $storable_stream->getWriter();

        if ($stream->getMimeType() === \Flexio\Base\ContentType::FLEXIO_TABLE)
        {
            while (($row = $streamreader->readRow()) !== false)
                $streamwriter->write($row);
        }
         else
        {
            while (($data = $streamreader->read(32768)) !== false)
                $streamwriter->write($data);
        }

        return $storable_stream;
    }

    private function getMountParams() : array
    {
        // if the process was created from a pipe (parent_eid), and that
        // pipe is part of a connection (mount), then get the setup_config
        // and return them as the mount params

        try
        {
            $process_obj_info = $this->procobj->get();

            if (isset($process_obj_info['parent']) && isset($process_obj_info['parent']['eid']))
            {
                $parent_eid = $process_obj_info['parent']['eid'];

                $pipe = \Flexio\Object\Pipe::load($parent_eid);
                $pipe_info = $pipe->get();

                if (isset($pipe_info['parent']) && isset($pipe_info['parent']['eid']))
                {
                    $connection_eid = $pipe_info['parent']['eid'];
                    $connection = \Flexio\Object\Connection::load($connection_eid);
                    $connection_info = $connection->get();

                    $setup_config = $connection_info['setup_config'];
                    if (isset($setup_config))
                    {
                        $mount_info = array();
                        foreach ($setup_config as $key => $value)
                        {
                            // note: setup config is a set of key/values; currently, values can
                            // be either strings or oauth connection object; if we don't have an
                            // oauth connection object, simply pass on the value, otherwise,
                            // "dereference" the connection and pass on the oauth access token
                            // from the connection

                            // if we don't have an object identifier, simply pass on what's there
                            $mount_item_eid = $value['eid'] ?? false;
                            if ($mount_item_eid === false)
                            {
                                $mount_info[$key] = $value;
                                continue;
                            }

                            // if we have an eid, try to load the appropriate oauth service and
                            // get the access token
                            try
                            {
                                $connection = \Flexio\Object\Connection::load($mount_item_eid);
                                if ($connection->getStatus() === \Model::STATUS_DELETED)
                                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

                                $service = $connection->getService();
                                if (!($service instanceof \Flexio\IFace\IOAuthConnection))
                                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

                                $tokens = $service->getTokens();
                                $mount_info[$key] = $tokens['access_token'];
                            }
                            catch (\Flexio\Base\Exception $e)
                            {
                                // don't do anything
                            }
                        }

                        return $mount_info;
                    }
                }
            }

        }
        catch (\Exception $e)
        {
        }

        return array();
    }

    private function getEnvironmentParams() : array
    {
        // return a list of environment parameters;
        // TODO: determine list; for now, include current user information and time
        // TODO: do we want to "namespace" the variables? right now, variables are
        // limited to alphanumeric, but maybe we want to do something like:
        // "flexio.user_firstname", "flexio.user_lastname", etc

        $process_user_info = array();
        try
        {
            $process_user_eid = $this->getOwner();
            $process_user = \Flexio\Object\User::load($process_user_eid);
            $process_user_eid = $process_user->get();
        }
        catch (\Flexio\Base\Exception $e)
        {
        }

        $environment_params = array();
        $environment_params['process.user.firstname'] = $process_user_info['first_name'] ?? '';
        $environment_params['process.user.lastname'] = $process_user_info['last_name'] ?? '';
        $environment_params['process.user.email'] = $process_user_info['email'] ?? '';
        $environment_params['process.time.started'] = \Flexio\System\System::getTimestamp();
        $environment_params['process.time.unix'] = (string)time();

        return $environment_params;
    }
}

