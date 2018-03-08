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


class Pipe extends \Flexio\Object\Base implements \Flexio\IFace\IObject
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

    public static function create(array $properties = null) : \Flexio\Object\Pipe
    {
        // if the task is set, make sure it's an object and then encode it as JSON for storage
        if (isset($properties) && isset($properties['task']))
        {
            if (!is_array($properties['task']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

            $properties['task'] = \Flexio\Jobs\Base::addEids($properties['task']);
            $properties['task'] = \Flexio\Jobs\Base::fixEmptyParams($properties['task']);
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
        $pipe_model = $object->getModel()->pipe;
        $local_eid = $pipe_model->create($properties);

        $object->setEid($local_eid);
        $object->clearCache();

        return $object;
    }

    public function delete() : \Flexio\Object\Pipe
    {
        $this->clearCache();
        $pipe_model = $this->getModel()->pipe;
        $pipe_model->delete($this->getEid());
        return $this;
    }

    public function set(array $properties) : \Flexio\Object\Pipe
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
        $res = $this->getModel()->assoc_range($this->getEid(), \Model::EDGE_HAS_PROCESS);

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

    public function getType() : string
    {
        return \Model::TYPE_PIPE;
    }

    public function setStatus(string $status) : \Flexio\Object\Base
    {
        $this->clearCache();
        $pipe_model = $this->getModel()->pipe;
        $result = $pipe_model->setStatus($this->getEid(), $status);
        return $this;
    }

    public function getStatus() : string
    {
        if ($this->eid_status !== false)
            return $this->eid_status;

        $pipe_model = $this->getModel()->pipe;
        $status = $pipe_model->getStatus($this->getEid());
        $this->eid_status = $status;

        return $status;
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
        if ($task === false)
        {
            $properties['task'] = array();
        }
         else
        {
            $properties['task'] = $task;
            $properties['task'] = \Flexio\Jobs\Base::fixEmptyParams($properties['task']);
        }

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
