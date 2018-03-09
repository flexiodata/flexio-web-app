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


class Process extends \Flexio\Object\Base implements \Flexio\IFace\IObject
{
    public function __construct()
    {
    }

    public function __toString()
    {
        $object = array(
            'eid' => $this->getEid(),
            'eid_type' => $this->getType()
        );
        return json_encode($object);
    }

    public static function load(string $identifier)
    {
        $object = new static();
        $model = $object->getModel();

        // assume the identifier is an eid, and try to find out the type
        $eid = $identifier;
        $local_eid_type = $model->getType($identifier);

        if ($local_eid_type !== $object->getType())
        {
            // the input isn't an eid, so it must be an identifier; try
            // to find the eid from the identifier; if we can't find it,
            // we're done
            $eid = $model->getEidFromEname($identifier);
            if ($eid === false)
                return false;
        }

        $object->setEid($eid);
        $object->clearCache();

        // TODO: for now, don't allow objects that have been deleted
        // to be loaded; in general, we may want to move this to the
        // api layer, but previously, it's been in the model layer,
        // and we need to make sure the behavior is the same after the
        // model constraint is removed, and object loading is a good
        // location for this constraint
        if ($object->getStatus() === \Model::STATUS_DELETED)
            return false;

        return $object;
    }

    public static function create(array $properties = null) : \Flexio\Object\Process
    {
        // if the task is set, make sure it's an object and then encode it as JSON for storage
        if (isset($properties) && isset($properties['task']))
        {
            if (!is_array($properties['task']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

            $properties['task'] = \Flexio\Jobs\Base::addEids($properties['task']);
            $properties['task'] = \Flexio\Jobs\Base::fixEmptyParams($properties['task']);
            $properties['task'] = json_encode($properties['task']);
        }

        // if not process mode is specified, run everything
        if (!isset($properties['process_mode']))
            $properties['process_mode'] = \Flexio\Jobs\Process::MODE_RUN;

        $object = new static();
        $process_model = $object->getModel()->process;
        $local_eid = $process_model->create($properties);

        $object->setEid($local_eid);
        $object->clearCache();

        return $object;
    }

    public function delete() : \Flexio\Object\Process
    {
        $this->clearCache();
        $process_model = $this->getModel()->process;
        $process_model->delete($this->getEid());
        return $this;
    }

    public function set(array $properties) : \Flexio\Object\Process
    {
        // TODO: add properties check

        // if the task is set, make sure it's an object and then encode it as JSON for storage
        if (isset($properties) && isset($properties['task']))
        {
            if (!is_array($properties['task']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

            $properties['task'] = \Flexio\Jobs\Base::addEids($properties['task']);
            $properties['task'] = \Flexio\Jobs\Base::fixEmptyParams($properties['task']);
            $properties['task'] = json_encode($properties['task']);
        }

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

    public function getType() : string
    {
        return \Model::TYPE_PROCESS;
    }

    public function setStatus(string $status) : \Flexio\Object\Base
    {
        $this->clearCache();
        $process_model = $this->getModel()->process;
        $result = $process_model->setStatus($this->getEid(), $status);
        return $this;
    }

    public function getStatus() : string
    {
        if ($this->eid_status !== false)
            return $this->eid_status;

        $process_model = $this->getModel()->process;
        $status = $process_model->getStatus($this->getEid());
        $this->eid_status = $status;

        return $status;
    }

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

    public function getMode() : string
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['process_mode'];
    }

    public function addToLog($log_eid = null, array $params) : string
    {
        $eid = $this->getModel()->process->log($log_eid, $this->getEid(), $params);
        return $eid;
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
            {
                $entry['task'] = $task;
                $entry['task'] = \Flexio\Jobs\Base::fixEmptyParams($entry['task']);
            }

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
            {
                $properties['task'] = $task;
                $properties['task'] = \Flexio\Jobs\Base::fixEmptyParams($properties['task']);
            }
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
}

