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


class StoredProcess
{
    // events are passed in a callback function along along with a reference to the process
    public const EVENT_STARTING       = 'process.starting';
    public const EVENT_FINISHING      = 'process.finishing';

    private $engine;            // instance of \Flexio\Jobs\Process
    private $procobj;           // instance of \Flexio\Object\Process -- used only during execution phase
    private $handlers = [];     // array of callbacks invoked for each event

    public function __construct()
    {
    }

    public static function create(\Flexio\Object\Process $procobj, \Flexio\Jobs\Process $engine) : \Flexio\Jobs\StoredProcess
    {
        $object = new static();
        $object->procobj = $procobj;
        $object->engine = $engine;
        $object->engine->setOwner($procobj->getOwner());
        return $object;
    }

    public function getStore() : \Flexio\Object\Process
    {
        return $this->procobj;
    }

    public function getEngine() : \Flexio\Jobs\Process
    {
        return $this->engine;
    }

    public function run(bool $background = true) : \Flexio\Jobs\StoredProcess
    {
        // run the job
        if ($background === false)
        {
            return $this->run_internal();
        }
         else
        {
            // TODO: add serialization checks

            // pack up the info for this object and store it temporarily in the
            // registry so we can get it in the background process
            $process_owner = $this->engine->getOwner();
            $process_eid = $this->procobj->getEid();
            $process_info = json_encode(array(
                "engine" => serialize($this->engine),
                "procobj" => serialize($this->procobj),
                "handlers" => serialize($this->handlers)
            ));
            \Flexio\System\System::getModel()->registry->setString($process_owner, $process_eid, $process_info);
            \Flexio\System\Program::runInBackground("\Flexio\Jobs\StoredProcess::background_entry('$process_owner','$process_eid')");
            return $this;
        }
    }

    public static function background_entry($process_owner, $process_eid) : \Flexio\Jobs\StoredProcess
    {
        // TODO: add unserialization checks

        // create an empty process object
        $stored_process_object = new static();

        // get the process info from the registry and delete the registry entry
        $process_info = \Flexio\System\System::getModel()->registry->getString($process_owner, $process_eid);
        \Flexio\System\System::getModel()->registry->deleteEntryByName($process_owner, $process_eid);

        // reserialize the object info
        $process_info = @json_decode($process_info, true);
        $stored_process_object->engine = unserialize($process_info['engine']);
        $stored_process_object->procobj = unserialize($process_info['procobj']);
        $stored_process_object->handlers = unserialize($process_info['handlers']);

        return $stored_process_object->run_internal();
    }

    private function run_internal() : \Flexio\Jobs\StoredProcess
    {
        // STEP 1: set the job status
        $this->procobj->set([
            'process_status' => \Flexio\Jobs\Process::STATUS_RUNNING,
            'started' => \Flexio\Base\Util::getCurrentTimestamp()
        ]);

        // STEP 2: if we have a valid owner, try to increment the active process count;
        // if we're unable to, then the user is as the maximum number of processes, so
        // a little and then try again
        $owner_eid = $this->engine->getOwner();
        if (\Flexio\Base\Eid::isValid($owner_eid) == false)
        {
            $this->signal(self::EVENT_STARTING, $this);
        }
         else
        {
            $current_attempt = 0;
            $max_attempts = 10*60*5; // allow 5-minutes-worth of attempts (at 10 per second)

            while (true)
            {
                $success = $this->procobj->incrementActiveProcessCount();
                if ($success == true)
                {
                    $this->signal(self::EVENT_STARTING, $this);
                    break;
                }

                usleep(100000); // sleep 100ms

                $current_attempt++;
                if ($current_attempt < $max_attempts)
                    continue;

                // fail if job doesn't finish within a certain number of attempts
                $process_info = array('error' => $this->engine->getError());
                $process_info_str = json_encode($process_info, JSON_PARTIAL_OUTPUT_ON_ERROR); // don't allow bad characters that may exist in debugging info to cause encoding to cause another failure

                $process_params = array();
                $process_params['finished'] = \Flexio\Base\Util::getCurrentTimestamp();
                $process_params['process_status'] = \Flexio\Jobs\Process::STATUS_FAILED;
                $process_params['process_info'] = $process_info_str;
                $this->procobj->set($process_params);
                $this->signal(self::EVENT_FINISHED, $this);

                return $this;
            }
        }

        // STEP 3: add the variables
        $mount_variables = $this->getMountParams();
        $user_variables = $this->engine->getParams();
        $this->engine->setParams(array_merge($user_variables, $mount_variables));

        // STEP 4: if we have an associative array, we have a top-level task, so simply
        // execute it; otherwise we have an array of tasks, so package them in a sequence job
        $task = $this->procobj->getTask();
        if (\Flexio\Base\Util::isAssociativeArray($task) === false)
            $task = array('op' => 'sequence', 'params' => array('items' => $task));
        $this->engine->execute($task);

        // STEP 5: save final job output and status; only save the status if it hasn't already been set
        $process_params = array();
        $process_params['finished'] = \Flexio\Base\Util::getCurrentTimestamp();

        $process_params['process_status'] = \Flexio\Jobs\Process::STATUS_COMPLETED;
        if ($this->engine->hasError())
        {
            $process_info = array('error' => $this->engine->getError());
            $process_info_str = json_encode($process_info, JSON_PARTIAL_OUTPUT_ON_ERROR); // don't allow bad characters that may exist in debugging info to cause encoding to cause another failure

            $process_params['process_status'] = \Flexio\Jobs\Process::STATUS_FAILED;
            $process_params['process_info'] = $process_info_str;
        }

        // if we're in build mode, create a storable stream to store the output
        if ($this->procobj->getMode() === \Flexio\Jobs\Process::MODE_BUILD)
        {
            $owned_by = $this->engine->getOwner();
            $storable_stdout = self::createStorableStream($this->engine->getStdout(), $owned_by);

            $storable_stream_info = array();
            $process_params['output'] = array('stream' => $storable_stdout->getEid());
        }

        // save the process info and signal the end of the process
        $this->procobj->set($process_params);
        $this->signal(self::EVENT_FINISHING, $this);

        // STEP 6: decrement the active process count
        if (\Flexio\Base\Eid::isValid($owner_eid))
            $this->procobj->decrementActiveProcessCount();

        return $this;
    }

    public function addEventHandler(string $event, string $handler, array $metadata = array()) : void
    {
        // if needed, initialize the array of handlers for a particular event
        if (!isset($this->handlers[$event]))
            $this->handlers[$event] = array();

        // add the event handler to the list
        $this->handlers[$event][] = array(
            'function' => $handler,
            'metadata' => $metadata
        );
    }

    private function signal(string $event, \Flexio\Jobs\StoredProcess $process) : void
    {
        // get the handlers for this particular event
        $handlers = $this->handlers[$event] ?? array();

        // call the event handlers for the given event
        foreach ($handlers as $h)
        {
            $function = $h['function'];
            $metadata = $h['metadata'];
            call_user_func($function, $process, $metadata);
        }
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
                            // be either strings or oauth connection object; if we don't have a
                            // connection object, simply pass on the value, otherwise, "dereference"
                            // the connection and pass on the connection info

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
                                $requesting_user_eid = $this->engine->getOwner();
                                $connection = \Flexio\Object\Connection::load($mount_item_eid);
                                if ($connection->getStatus() !== \Model::STATUS_AVAILABLE)
                                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
                                if ($connection->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_CONNECTION_READ) === false)
                                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

                                $service = $connection->getService(); // getting the service refreshes tokens
                                $connection_info = $service->get();
                                $connection_info = json_encode($connection_info);

                                $stream = \Flexio\Base\Stream::create();
                                $stream->setMimeType(\Flexio\Base\ContentType::FLEXIO_CONNECTION_INFO);
                                $stream->buffer = (string)$connection_info; // shortcut to speed it up -- can also use getWriter()->write((string)$v)

                                $mount_info[$key] = $stream;
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

    private static function generateTaskHash(string $implementation_version, array $task) : string
    {
        // note: currently, this isn't used, but it's here for reference in case we
        // need a way of referencing looking up tasks from a string

        // task hash should uniquely identify the result; use
        // a hash of:
        //     1. implementation version (git version)
        //     2. task type
        //     3. task parameters
        // leave out the job name or other identifiers, such as the
        // the task eid

        // if we dont' have an implementation version or an invalid implementation
        // version (git revision), don't cache anything
        if (strlen($implementation_version) < 40)
            return '';

        $encoded_task = json_encode($task);
        $hash = md5(
            $implementation_version .
            $encoded_task
        );

        return $hash;
    }
}

