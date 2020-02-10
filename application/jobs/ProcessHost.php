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


class ProcessHost
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

    public static function create(\Flexio\Object\Process $procobj, \Flexio\Jobs\Process $engine) : \Flexio\Jobs\ProcessHost
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

    public function run(bool $background = true) : \Flexio\Jobs\ProcessHost
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
            \Flexio\System\Program::runInBackground("\Flexio\Jobs\ProcessHost::background_entry('$process_owner','$process_eid')");
            return $this;
        }
    }

    public static function background_entry($process_owner, $process_eid) : \Flexio\Jobs\ProcessHost
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

    private function run_internal() : \Flexio\Jobs\ProcessHost
    {
        // STEP 1: set the job status
        $this->procobj->set([
            'process_status' => \Flexio\Jobs\Process::STATUS_RUNNING,
            'started' => \Flexio\Base\Util::getCurrentTimestamp()
        ]);

        // STEP 2: signal the start of the process
        $this->signal(self::EVENT_STARTING, $this);

        // STEP 3: if we have an associative array, we have a top-level task, so simply
        // execute it; otherwise we have an array of tasks, so package them in a sequence job
        $task = $this->procobj->getTask();
        if (\Flexio\Base\Util::isAssociativeArray($task) === false)
            $task = array('op' => 'sequence', 'params' => array('items' => $task));
        $this->engine->execute($task);

        // STEP 4: signal the end of the process
        $this->signal(self::EVENT_FINISHING, $this);

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

        // save the process info and signal the end of the process
        $this->procobj->set($process_params);

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

    private function signal(string $event, \Flexio\Jobs\ProcessHost $process) : void
    {
        // get the handlers for this particular event
        $handlers = $this->handlers[$event] ?? array();

        try
        {
            // call the event handlers for the given event
            foreach ($handlers as $h)
            {
                $function = $h['function'];
                $metadata = $h['metadata'];
                call_user_func($function, $process, $metadata);
            }
        }
        catch (\Flexio\Base\Exception | \Exception | \Error $e)
        {
            \Flexio\Jobs\Process::setErrorFromException($process, $e);
        }
    }
}

