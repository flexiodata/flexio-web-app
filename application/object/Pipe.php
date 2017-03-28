<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-03-30
 *
 * @package flexio
 * @subpackage Object
 */


namespace Flexio\Object;


class Pipe extends \Flexio\Object\Base
{
    public function __construct()
    {
        $this->setType(\Model::TYPE_PIPE);
    }

    public static function create($properties = null)
    {
        // if a task parameter is set, we need to assign a client id to each element
        if (isset($properties) && isset($properties['task']))
            $properties['task'] = \Flexio\Object\Task::create($properties['task'])->get();

        // task and schedule are stored as a json string, so these need to be encoded
        if (isset($properties) && isset($properties['task']))
            $properties['task'] = json_encode($properties['task']);

        if (isset($properties) && isset($properties['schedule']))
        {
            $schedule = $properties['schedule'];
            if (\Flexio\Base\ValidatorSchema::check($schedule, \Flexio\Object\Scheduler::SCHEMA)->hasErrors())
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

            $properties['schedule'] = json_encode($schedule);
        }

        $object = new static();
        $model = \Flexio\Object\Store::getModel();
        $local_eid = $model->create($object->getType(), $properties);

        $object->setModel($model);
        $object->setEid($local_eid);
        $object->setRights();
        $object->clearCache();
        return $object;
    }

    public function set($properties)
    {
        // if a task parameter is set, we need to assign a client id to each element
        if (isset($properties) && isset($properties['task']))
            $properties['task'] = \Flexio\Object\Task::create($properties['task'])->get();

        // task and schedule are stored as a json string, so these need to be encoded
        if (isset($properties) && isset($properties['task']))
            $properties['task'] = json_encode($properties['task']);

        if (isset($properties) && isset($properties['schedule']))
        {
            $schedule = $properties['schedule'];
            if (\Flexio\Base\ValidatorSchema::check($schedule, \Flexio\Object\Scheduler::SCHEMA)->hasErrors())
                return false;

            $properties['schedule'] = json_encode($schedule);
        }

        // TODO: add properties check

        // TODO: for now, don't forward model exception
        try
        {
            $this->clearCache();
            $pipe_model = $this->getModel()->pipe;
            $pipe_model->set($this->getEid(), $properties);
        }
        catch (\Exception $e)
        {
        }

        return $this;
    }

    public function setTask($task)
    {
        // shorthand for setting task info
        $properties = array();
        $properties['task'] = $task;
        return $this->set($properties);
    }

    public function getTask()
    {
        // shorthand for getting task info
        $local_properties = $this->get();
        if (!isset($local_properties['task']))
            return false;

        return $local_properties['task'];
    }

    public function addTaskStep($task_step, $index)
    {
        // get the current task array
        $task_array = $this->getTask();
        $task = \Flexio\Object\Task::load($task_array);

        // add a new task
        $task_eid = $task->addTaskStep($task_step, $index);
        $this->setTask($task->get());
        return $task_eid;
    }

    public function deleteTaskStep($task_eid)
    {
        $task_array = $this->getTask();
        $task = \Flexio\Object\Task::load($task_array);
        $task->deleteTaskStep($task_eid);
        $this->setTask($task->get());
        return $this;
    }

    public function setTaskStep($task_eid, $task_step)
    {
        // get the current task array
        $task_array = $this->getTask();
        $task = \Flexio\Object\Task::load($task_array);
        $task->setTaskStep($task_eid, $task_step);
        $this->setTask($task->get());
        return $this;
    }

    public function getTaskStep($task_eid)
    {
        $task_array = $this->getTask();
        $task = \Flexio\Object\Task::load($task_array);
        return $task->getTaskStep($task_eid);
    }

    public function setSchedule($schedule)
    {
        // make sure the schedule format is valid
        if (\Flexio\Base\ValidatorSchema::check($schedule, \Flexio\Object\Scheduler::SCHEMA)->hasErrors())
            return $this;

        // shorthand for setting schedule info
        $properties = array();
        $properties['schedule'] = $schedule;
        return $this->set($properties);
    }

    public function getSchedule()
    {
        // shorthand for getting schedule info
        $local_properties = $this->get();
        if (!isset($lcoal_properties['schedule']))
            return false;

        return $local_properties['schedule'];
    }

    public function addProcess($process)
    {
        $result = $this->getModel()->assoc_add($this->getEid(), \Model::EDGE_HAS_PROCESS, $process->getEid());
        $this->getModel()->assoc_add($process->getEid(), \Model::EDGE_PROCESS_OF, $this->getEid());
    }

    public function getProcesses()
    {
        $result = array();

        $object_eid = $this->getEid();
        $res = $this->getModel()->assoc_range($object_eid, \Model::EDGE_HAS_PROCESS, [\Model::STATUS_AVAILABLE]);

        foreach ($res as $item)
        {
            $object_eid = $item['eid'];
            $object = \Flexio\Object\Store::load($object_eid);
            if ($object === false)
                continue;

            $result[] = $object;
        }

        return $result;
    }

    public function get()
    {
        if ($this->isCached() === true)
            return $this->properties;

        if ($this->populateCache() === true)
            return $this->properties;

        return false;
    }

    private function isCached()
    {
        if ($this->properties === false)
            return false;

        return true;
    }

    private function clearCache()
    {
        $this->eid_status = false;
        $this->properties = false;
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
            "eid_type" : "'.\Model::TYPE_PIPE.'",
            "eid_status" : null,
            "ename" : null,
            "name" : null,
            "description" : null,
            "project='.\Model::EDGE_MEMBER_OF.'" : {
                "eid" : null,
                "eid_type" : "'.\Model::TYPE_PROJECT.'",
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
            "created_by='.\Model::EDGE_CREATED_BY.'" : {
                "eid" : null,
                "eid_type" : "'.\Model::TYPE_USER.'",
                "user_name" : null,
                "first_name" : null,
                "last_name" : null,
                "email_hash" : null
            },
            "task" : null,
            "schedule" : null,
            "schedule_status" : null,
            "created" : null,
            "updated" : null
        }
        ';

        // execute the query
        $query = json_decode($query);
        $properties = \Flexio\Object\Query::exec($this->getEid(), $query);
        if (!$properties)
            return false;

        // unpack the task json
        $task = @json_decode($properties['task'],true);
        if ($task !== false)
            $properties['task'] = $task;

        // unpack the schedule json
        $schedule = @json_decode($properties['schedule'],true);
        if ($schedule !== false)
            $properties['schedule'] = $schedule;

        // return the properties
        return $properties;
    }
}
