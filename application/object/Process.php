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

    public static function summary(array $filter) : array
    {
        $result = \Flexio\System\System::getModel()->process->summary($filter);
        return $result;
    }

    public static function summary_daily(array $filter) : array
    {
        $result = \Flexio\System\System::getModel()->process->summary_daily($filter);
        return $result;
    }

    public static function list(array $filter) : array
    {
        $object = new static();
        $process_model = $object->getModel()->process;
        $items = $process_model->list($filter);

        $objects = array();
        foreach ($items as $i)
        {
            $o = new static();
            $o->properties =self::formatProperties($i);
            $o->setEid($o->properties['eid']);
            $objects[] = $o;
        }

        return $objects;
    }

    public static function load(string $eid) : \Flexio\Object\Process
    {
        $object = new static();
        $process_model = $object->getModel()->process;
        $properties = $process_model->get($eid);

        $object->setEid($eid);
        $object->clearCache();
        $object->properties = self::formatProperties($properties);
        return $object;
    }

    public static function create(array $properties = null) : \Flexio\Object\Process
    {
        if (!isset($properties))
            $properties = array();

        // if the pipe info is set, make sure it's an object and then encode it as JSON for storage
        if (isset($properties) && isset($properties['pipe_info']))
        {
            if (!is_array($properties['pipe_info']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

            $properties['pipe_info'] = json_encode($properties['pipe_info']);
        }

        // if the task is set, make sure it's an object and then encode it as JSON for storage
        if (isset($properties) && isset($properties['task']))
        {
            if (!is_array($properties['task']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

            $properties['task'] = \Flexio\Jobs\Base::addEids($properties['task']);
            $properties['task'] = \Flexio\Jobs\Base::fixEmptyParams($properties['task']);
            $properties['task'] = \Flexio\Jobs\Base::flattenParams($properties['task']);
            $properties['task'] = json_encode($properties['task']);
        }

        // if no process mode is specified, run everything
        if (!isset($properties['process_mode']))
            $properties['process_mode'] = \Flexio\Jobs\Process::MODE_RUN;

        // if no process status is set, set a default
        if (!isset($properties['process_status']))
            $properties['process_status'] = \Flexio\Jobs\Process::STATUS_PENDING;

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

        // if the pipe info is set, make sure it's an object and then encode it as JSON for storage
        if (isset($properties) && isset($properties['pipe_info']))
        {
            if (!is_array($properties['pipe_info']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

            $properties['pipe_info'] = json_encode($properties['pipe_info']);
        }

        // if the task is set, make sure it's an object and then encode it as JSON for storage
        if (isset($properties) && isset($properties['task']))
        {
            if (!is_array($properties['task']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

            $properties['task'] = \Flexio\Jobs\Base::addEids($properties['task']);
            $properties['task'] = \Flexio\Jobs\Base::fixEmptyParams($properties['task']);
            $properties['task'] = \Flexio\Jobs\Base::flattenParams($properties['task']);
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

    public function setOwner(string $user_eid) : \Flexio\Object\Process
    {
        $properties = array('owned_by' => $user_eid);
        $this->set($properties);
        return $this;
    }

    public function getOwner() : string
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['owned_by']['eid'];
    }

    public function setStatus(string $status) : \Flexio\Object\Process
    {
        if ($status === \Model::STATUS_DELETED)
            return $this->delete();

        $this->clearCache();
        $process_model = $this->getModel()->process;
        $result = $process_model->set($this->getEid(), array('eid_status' => $status));
        return $this;
    }

    public function getStatus() : string
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['eid_status'];
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
        // if (!$this->properties)
        //    return false;
        //
        // return true;
    }

    private function clearCache() : void
    {
        $this->properties = null;
    }

    private function populateCache() : void
    {
        $process_model = $this->getModel()->process;
        $properties = $process_model->get($this->getEid());
        $this->properties = self::formatProperties($properties);
    }

    private static function formatProperties(array $properties) : array
    {
        $mapped_properties = \Flexio\Base\Util::mapArray(
            [
                "eid" => null,
                "eid_type" => null,
                "eid_status" =>  null,
                "parent" => null,
                "process_mode" => null,
                "task" => null,
                "started_by" => null,
                "started" => null,
                "finished" => null,
                "duration" => null,
                "process_info" => null,
                "process_status" => null,
                "owned_by" => null,
                "created" => null,
                "updated" => null
            ],
        $properties);

        // sanity check: if the data record is missing, then eid will be null
        if (!isset($mapped_properties['eid']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // get the pipe info
        $pipe_info = false;
        if (isset($mapped_properties['pipe_info']))
        {
            $pipe_info = @json_decode($mapped_properties['pipe_info'],true);
            if ($pipe_info !== false)
            {
                $mapped_properties['pipe_info'] = $pipe_info;
                $mapped_properties['pipe_info'] = $mapped_properties['pipe_info'];
            }
        }

        // expand the parent and owner info
        $mapped_properties['parent'] = array(
            'eid' => $properties['parent_eid'],
            'eid_type' => \Model::TYPE_PIPE,
            'eid_status' => $pipe_info['eid_status'] ?? "",
            'alias' => $pipe_info['alias'] ?? "",
            'name' => $pipe_info['name'] ?? "",
            'description' => $pipe_info['description'] ?? "",
            'deploy_mode' => $pipe_info['deploy_mode'] ?? "",
            'created' => $pipe_info['created'] ?? "",
            'updated' => $pipe_info['updated'] ?? ""
        );
        $mapped_properties['owned_by'] = array(
            'eid' => $properties['owned_by'],
            'eid_type' => \Model::TYPE_USER
        );

        // unpack the primary process task json
        if (isset($mapped_properties['task']))
        {
            $task = @json_decode($mapped_properties['task'],true);
            if ($task !== false)
            {
                $mapped_properties['task'] = $task;
                $mapped_properties['task'] = \Flexio\Jobs\Base::fixEmptyParams($mapped_properties['task']);
            }
        }

        // unpack the primary process process info json
        if (isset($mapped_properties['process_info']))
        {
            $process_info = @json_decode($mapped_properties['process_info'],true);
            if ($process_info !== false)
                $mapped_properties['process_info'] = $process_info;
        }

        return $mapped_properties;
    }
}

