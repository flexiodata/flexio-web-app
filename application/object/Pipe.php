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


declare(strict_types=1);
namespace Flexio\Object;


class Pipe extends \Flexio\Object\Base
{
    public function __construct()
    {
        $this->setType(\Model::TYPE_PIPE);
    }

    public static function create(array $properties = null) : \Flexio\Object\Pipe
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
            if (\Flexio\Base\ValidatorSchema::check($schedule, self::SCHEDULE_SCHEMA)->hasErrors())
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

            $properties['schedule'] = json_encode($schedule);
        }

        $object = new static();
        $model = \Flexio\Object\Store::getModel();
        $local_eid = $model->create($object->getType(), $properties);

        $object->setModel($model);
        $object->setEid($local_eid);
        $object->clearCache();

        // set the default pipe rights
        $object->grant(\Flexio\Object\Action::TYPE_READ_RIGHTS, \Flexio\Object\User::MEMBER_OWNER);
        $object->grant(\Flexio\Object\Action::TYPE_WRITE_RIGHTS, \Flexio\Object\User::MEMBER_OWNER);
        $object->grant(\Flexio\Object\Action::TYPE_READ, \Flexio\Object\User::MEMBER_OWNER);
        $object->grant(\Flexio\Object\Action::TYPE_WRITE, \Flexio\Object\User::MEMBER_OWNER);
        $object->grant(\Flexio\Object\Action::TYPE_DELETE, \Flexio\Object\User::MEMBER_OWNER);
        $object->grant(\Flexio\Object\Action::TYPE_EXECUTE, \Flexio\Object\User::MEMBER_OWNER);
        $object->grant(\Flexio\Object\Action::TYPE_READ, \Flexio\Object\User::MEMBER_GROUP);
        $object->grant(\Flexio\Object\Action::TYPE_WRITE, \Flexio\Object\User::MEMBER_GROUP);
        // don't allow delete by default for group members for pipes
        $object->grant(\Flexio\Object\Action::TYPE_EXECUTE, \Flexio\Object\User::MEMBER_GROUP);

        return $object;
    }

    public function set(array $properties) : \Flexio\Object\Pipe
    {
        // TODO: add properties check

        // if a task parameter is set, we need to assign a client id to each element
        if (isset($properties) && isset($properties['task']))
            $properties['task'] = \Flexio\Object\Task::create($properties['task'])->get();

        // task and schedule are stored as a json string, so these need to be encoded
        if (isset($properties) && isset($properties['task']))
            $properties['task'] = json_encode($properties['task']);

        if (isset($properties) && isset($properties['schedule']))
        {
            $schedule = $properties['schedule'];
            if (\Flexio\Base\ValidatorSchema::check($schedule, self::SCHEDULE_SCHEMA)->hasErrors())
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

            $properties['schedule'] = json_encode($schedule);
        }

        $this->clearCache();
        $pipe_model = $this->getModel()->pipe;
        $pipe_model->set($this->getEid(), $properties);
        return $this;
    }

    public function setTask(array $task) : \Flexio\Object\Pipe
    {
        // shorthand for setting task info
        $properties = array();
        $properties['task'] = $task;
        return $this->set($properties);
    }

    public function getTask() : array
    {
        // shorthand for getting task info
        $local_properties = $this->get();
        return $local_properties['task'];
    }

    public function addTaskStep(array $task_step, int $index = null) : string
    {
        // get the current task array
        $task_array = $this->getTask();
        $task = \Flexio\Object\Task::create($task_array);

        // add a new task
        $task_eid = $task->addTaskStep($task_step, $index);
        $this->setTask($task->get());
        return $task_eid;
    }

    public function deleteTaskStep(string $task_eid) : \Flexio\Object\Pipe
    {
        $task_array = $this->getTask();
        $task = \Flexio\Object\Task::create($task_array);
        $task->deleteTaskStep($task_eid);
        $this->setTask($task->get());
        return $this;
    }

    public function setTaskStep(string $task_eid, array $task_step) : \Flexio\Object\Pipe
    {
        // get the current task array
        $task_array = $this->getTask();
        $task = \Flexio\Object\Task::create($task_array);
        $task->setTaskStep($task_eid, $task_step);
        $this->setTask($task->get());
        return $this;
    }

    public function getTaskStep(string $task_eid) // TODO: add function return type
    {
        $task_array = $this->getTask();
        $task = \Flexio\Object\Task::create($task_array);
        return $task->getTaskStep($task_eid);
    }

    public function setSchedule(array $schedule) : \Flexio\Object\Pipe
    {
        // make sure the schedule format is valid
        if (\Flexio\Base\ValidatorSchema::check($schedule, self::SCHEDULE_SCHEMA)->hasErrors())
            return $this;

        // shorthand for setting schedule info
        $properties = array();
        $properties['schedule'] = $schedule;
        return $this->set($properties);
    }

    public function getSchedule() // add function return type
    {
        // shorthand for getting schedule info
        $local_properties = $this->get();
        if (!isset($lcoal_properties['schedule']))
            return false;

        return $local_properties['schedule'];
    }

    public function addProcess(\Flexio\Object\Process $process) : \Flexio\Object\Pipe
    {
        $result = $this->getModel()->assoc_add($this->getEid(), \Model::EDGE_HAS_PROCESS, $process->getEid());
        $this->getModel()->assoc_add($process->getEid(), \Model::EDGE_PROCESS_OF, $this->getEid());
        return $this;
    }

    public function getProcesses() : array
    {
        $result = array();

        $assoc_filter = array('eid_status' => array(\Model::STATUS_AVAILABLE));
        $res = $this->getModel()->assoc_range($this->getEid(), \Model::EDGE_HAS_PROCESS, $assoc_filter);

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

    public function get() : array
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties;
    }

    private function isCached() : bool
    {
        if ($this->properties === false)
            return false;

        return true;
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
            "eid_type" : "'.\Model::TYPE_PIPE.'",
            "eid_status" : null,
            "ename" : null,
            "name" : null,
            "description" : null,
            "rights": null,
            "owned_by='.\Model::EDGE_OWNED_BY.'" : {
                "eid" : null,
                "eid_type" : "'.\Model::TYPE_USER.'",
                "user_name" : null,
                "first_name" : null,
                "last_name" : null,
                "email_hash" : null
            },
            "followed_by='.\Model::EDGE_FOLLOWED_BY.'" : [{
                "eid" : null,
                "eid_type" : "'.\Model::TYPE_USER.'",
                "user_name" : null,
                "first_name" : null,
                "last_name" : null,
                "email_hash" : null
            }],
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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // unpack the task json
        $task = @json_decode($properties['task'],true);
        if ($task !== false)
            $properties['task'] = $task;
             else
            $properties['task'] = array();

        // unpack the schedule json
        $schedule = @json_decode($properties['schedule'],true);
        if ($schedule !== false)
            $properties['schedule'] = $schedule;

        // TODO: deprecated
        $default_rights = array(
            'owner' => array(
                'read' => true,
                'write' => true,
                'execute' => true,
                'delete' => true
            ),
            'member' => array(
                'read' => true,
                'write' => true,
                'execute' => true,
                'delete' => false
            ),
            'public' => array(
                'read' => false,
                'write' => false,
                'execute' => false,
                'delete' => false
            )
        );

        // populate the rights node
        $properties['rights'] = $default_rights;

        // return the properties
        return $properties;
    }

    // schedule info
    const SCHEDULE_TEMPLATE = <<<EOD
    {
        "frequency": "",
        "timezone": "",
        "days": [],
        "times": [
            {
                "hour": 0,
                "minute": 0
            }
        ]
    }
EOD;
    const SCHEDULE_SCHEMA = <<<EOD
    {
        "type": "object",
        "required": ["frequency","timezone","days","times"],
        "properties": {
            "frequency": {
                "type": "string",
                "enum": ["one-minute","five-minutes","thirty-minutes","hourly","daily","weekly","monthly"]
            },
            "timezone": {
                "type": "string"
            },
            "days": {
                "type": "array",
                "items": {
                    "type": ["number","string"],
                    "enum": ["mon","tue","wed","thu","fri","sat","sun","last",1,15]
                }
            },
            "times": {
                "type": "array",
                "items": {
                    "type": "object",
                    "required": ["hour","minute"],
                    "properties": {
                        "hour": {
                            "type": "integer",
                            "minimum": 0,
                            "maximum": 24
                        },
                        "minute": {
                            "type": "integer",
                            "minimum": 0,
                            "maximum": 60
                        }
                    }
                }
            }
        }
    }
EOD;
}
