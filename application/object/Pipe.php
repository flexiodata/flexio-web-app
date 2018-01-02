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
        // if the task is set, make sure it's an object and then encode it as JSON for storage
        if (isset($properties) && isset($properties['task']))
        {
            if (!is_array($properties['task']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

            $properties['task'] = json_encode($properties['task']);
        }

        // if the schedule is set, make sure it's valid and then encode it as JSON for storage
        if (isset($properties) && isset($properties['schedule']))
        {
            $schedule = $properties['schedule'];
            if (\Flexio\Base\ValidatorSchema::check($schedule, self::SCHEDULE_SCHEMA)->hasErrors())
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

            $properties['schedule'] = json_encode($schedule);
        }

        $object = new static();
        $model = $object->getModel();
        $local_eid = $model->create($object->getType(), $properties);

        $object->setEid($local_eid);
        $object->clearCache();

        return $object;
    }

    public function set(array $properties) : \Flexio\Object\Pipe
    {
        // TODO: add properties check

        // if the task is set, make sure it's an object and then encode it as JSON for storage
        if (isset($properties) && isset($properties['task']))
        {
            if (!is_array($properties['task']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

            $properties['task'] = json_encode($properties['task']);
        }

        // if the schedule is set, make sure it's valid and then encode it as JSON for storage
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

    public function setTasks(array $task) : \Flexio\Object\Pipe
    {
        // shorthand for setting task info
        $properties = array();
        $properties['task'] = $task;
        return $this->set($properties);
    }

    public function getTasks() : array
    {
        // shorthand for getting task info
        $local_properties = $this->get();
        return $local_properties['task'];
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

    public function getProcessList() : array
    {
        $result = array();

        $assoc_filter = array('eid_status' => array(\Model::STATUS_AVAILABLE));
        $res = $this->getModel()->assoc_range($this->getEid(), \Model::EDGE_HAS_PROCESS, $assoc_filter);

        foreach ($res as $item)
        {
            $object_eid = $item['eid'];
            $object = \Flexio\Object\Process::load($object_eid);
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

        // sanity check: if the data record is missing, then eid will be null
        if (!$properties || ($properties['eid'] ?? null) === null)
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
