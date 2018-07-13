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

    public static function getEidFromName(string $owner, string $alias)
    {
        $object = new static();
        $pipe_model = $object->getModel()->pipe;
        return $pipe_model->getEidFromName($owner, $alias);
    }

    public static function list(array $filter) : array
    {
        $object = new static();
        $pipe_model = $object->getModel()->pipe;
        $items = $pipe_model->list($filter);

        $objects = array();
        foreach ($items as $i)
        {
            $o = new static();
            $local_properties = self::formatProperties($i);
            $o->properties = $local_properties;
            $o->setEid($local_properties['eid']);
            $objects[] = $o;
        }

        return $objects;
    }

    public static function load(string $eid) : \Flexio\Object\Pipe
    {
        $object = new static();
        $pipe_model = $object->getModel()->pipe;

        $properties = $pipe_model->get($eid);
        if ($properties === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        $object->setEid($eid);
        $object->clearCache();
        $object->properties = self::formatProperties($properties);
        return $object;
    }

    public static function create(array $properties = null) : \Flexio\Object\Pipe
    {
        if (!isset($properties))
            $properties = array();

        // if the interface is set, make sure it's an object and then encode it as JSON for storage
        if (isset($properties) && isset($properties['interface']))
        {
            if (!is_array($properties['interface']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

            $properties['interface'] = json_encode($properties['interface']);
        }

        // if the task is set, make sure it's an object and then encode it as JSON for storage
        if (isset($properties) && isset($properties['task']))
        {
            if (!is_array($properties['task']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

            $properties['task'] = \Flexio\Jobs\Base::addEids($properties['task']);
            $properties['task'] = \Flexio\Jobs\Base::fixEmptyParams($properties['task']);
            $properties['task'] = \Flexio\Jobs\Base::flattenParams($properties['task']);
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

        // if the schedule status is set, make sure it's valid; otherwise default to 'inactive'
        if (isset($properties) && isset($properties['schedule_status']))
        {
            switch ($properties['schedule_status'])
            {
                default:
                    $properties['schedule_status'] = \Model::PIPE_STATUS_INACTIVE;
                    break;

                case \Model::PIPE_STATUS_INACTIVE:
                case \Model::PIPE_STATUS_ACTIVE:
                    // leave what's there
                    break;
            }
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

        // if the interface is set, make sure it's an object and then encode it as JSON for storage
        if (isset($properties) && isset($properties['interface']))
        {
            if (!is_array($properties['interface']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

            $properties['interface'] = json_encode($properties['interface']);
        }

        // if the task is set, make sure it's an object and then encode it as JSON for storage
        if (isset($properties) && isset($properties['task']))
        {
            if (!is_array($properties['task']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

            $properties['task'] = \Flexio\Jobs\Base::addEids($properties['task']);
            $properties['task'] = \Flexio\Jobs\Base::fixEmptyParams($properties['task']);
            $properties['task'] = \Flexio\Jobs\Base::flattenParams($properties['task']);
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

        // if the schedule status is set, make sure it's valid; otherwise default to 'inactive'
        if (isset($properties) && isset($properties['schedule_status']))
        {
            switch ($properties['schedule_status'])
            {
                default:
                    $properties['schedule_status'] = \Model::PIPE_STATUS_INACTIVE;
                    break;

                case \Model::PIPE_STATUS_INACTIVE:
                case \Model::PIPE_STATUS_ACTIVE:
                    // leave what's there
                    break;
            }
        }

        $this->clearCache();
        $pipe_model = $this->getModel()->pipe;
        $pipe_model->set($this->getEid(), $properties);
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
        return \Model::TYPE_PIPE;
    }

    public function setInterface(array $interface) : \Flexio\Object\Pipe
    {
        // shorthand for setting task info
        $properties = array();
        $properties['interface'] = $interface;
        return $this->set($properties);
    }

    public function getInterface() : array
    {
        // shorthand for getting task info
        $local_properties = $this->get();
        return $local_properties['interface'];
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

    public function setOwner(string $user_eid) : \Flexio\Object\Pipe
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

    public function setStatus(string $status) : \Flexio\Object\Pipe
    {
        $this->clearCache();
        $pipe_model = $this->getModel()->pipe;
        $result = $pipe_model->setStatus($this->getEid(), $status);
        return $this;
    }

    public function getStatus() : string
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['eid_status'];
    }

    private function isCached() : bool
    {
        if ($this->properties === false)
            return false;

        return true;
    }

    private function clearCache() : bool
    {
        $this->properties = false;
        return true;
    }

    private function populateCache() : bool
    {
        $pipe_model = $this->getModel()->pipe;
        $local_properties = $pipe_model->get($this->getEid());
        $this->properties = self::formatProperties($local_properties);
        return true;
    }

    private static function formatProperties(array $properties) : array
    {
        $mapped_properties = \Flexio\Base\Util::mapArray(
            [
                "eid" => null,
                "eid_type" => null,
                "eid_status" => null,
                "alias" => null,
                "name" => null,
                "description" => null,
                "interface" => null,
                "task" => null,
                "schedule" => null,
                "schedule_status" => null,
                "owned_by" => null,
                "created" => null,
                "updated" => null
            ],
        $properties);

        // sanity check: if the data record is missing, then eid will be null
        if (!isset($mapped_properties['eid']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // expand the owner info
        $mapped_properties['owned_by'] = array(
            'eid' => $properties['owned_by'],
            'eid_type' => \Model::TYPE_USER
        );

        // unpack the interface json
        if (isset($mapped_properties['interface']))
        {
            $interface = @json_decode($mapped_properties['interface'],true);
            if ($interface === false)
            {
                $mapped_properties['interface'] = array();
            }
            else
            {
                $mapped_properties['interface'] = $interface;
            }
        }

        // unpack the task json
        $task = @json_decode($mapped_properties['task'],true);
        if ($task === false)
        {
            $mapped_properties['task'] = array();
        }
         else
        {
            $mapped_properties['task'] = $task;
            $mapped_properties['task'] = \Flexio\Jobs\Base::fixEmptyParams($mapped_properties['task']);
        }

        // unpack the schedule json
        $schedule = @json_decode($mapped_properties['schedule'],true);
        if ($schedule !== false)
            $mapped_properties['schedule'] = $schedule;

        return $mapped_properties;
    }

    // schedule info
    public const SCHEDULE_TEMPLATE = <<<EOD
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
    public const SCHEDULE_SCHEMA = <<<EOD
    {
        "type": "object",
        "required": ["frequency","timezone","days","times"],
        "properties": {
            "frequency": {
                "type": "string",
                "enum": ["", "one-minute","five-minutes","thirty-minutes","hourly","daily","weekly","monthly"]
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
