<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
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

    public static function getEidFromName(string $owner, string $name)
    {
        $object = new static();
        $pipe_model = $object->getModel()->pipe;
        return $pipe_model->getEidFromName($owner, $name);
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
            $o->properties =self::formatProperties($i);
            $o->setEid($o->properties['eid']);
            $objects[] = $o;
        }

        return $objects;
    }

    public static function load(string $eid) : \Flexio\Object\Pipe
    {
        $object = new static();
        $pipe_model = $object->getModel()->pipe;
        $properties = $pipe_model->get($eid);

        $object->setEid($eid);
        $object->clearCache();
        $object->properties = self::formatProperties($properties);
        return $object;
    }

    public static function create(array $properties = null) : \Flexio\Object\Pipe
    {
        if (!isset($properties))
            $properties = array();

        // if the task is set, make sure it's an object and then encode it as JSON for storage
        if (isset($properties) && isset($properties['task']))
        {
            if (!is_array($properties['task']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

            $properties['task'] = \Flexio\Jobs\Base::fixEmptyParams($properties['task']);
            $properties['task'] = \Flexio\Jobs\Base::flattenParams($properties['task']);
            $properties['task'] = json_encode($properties['task']);
        }

        // if the examples is set, encode it as JSON for storage
        if (isset($properties) && isset($properties['examples']))
            $properties['examples']= json_encode($properties['examples']);

        // if the examples is set, encode it as JSON for storage
        if (isset($properties) && isset($properties['params']))
            $properties['params']= json_encode($properties['params']);

        // if the deploy mode is set, make sure it's valid; otherwise default to 'build'
        if (isset($properties) && isset($properties['deploy_mode']))
        {
            switch ($properties['deploy_mode'])
            {
                default:
                    $properties['deploy_mode'] = \Model::PIPE_DEPLOY_MODE_BUILD;
                    break;

                case \Model::PIPE_DEPLOY_MODE_BUILD:
                case \Model::PIPE_DEPLOY_MODE_RUN:
                    // leave what's there
                    break;
            }
        }

        // if the schedule is set, make sure it's valid and then encode it as JSON for storage
        if (isset($properties) && isset($properties['schedule']))
        {
            $schedule = $properties['schedule'];
            if (\Flexio\Base\Schedule::isValid($schedule) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

            $properties['schedule'] = json_encode($schedule);
        }


        // TODO: clean up the following overly-verbose implementation:

        // if the schedule status is set, make sure it's valid; otherwise default to 'inactive'
        if (isset($properties) && isset($properties['deploy_schedule']))
        {
            switch ($properties['deploy_schedule'])
            {
                default:
                    $properties['deploy_schedule'] = \Model::PIPE_DEPLOY_STATUS_INACTIVE;
                    break;

                case \Model::PIPE_DEPLOY_STATUS_INACTIVE:
                case \Model::PIPE_DEPLOY_STATUS_ACTIVE:
                    // leave what's there
                    break;
            }
        }

        if (isset($properties) && isset($properties['deploy_email']))
        {
            switch ($properties['deploy_email'])
            {
                default:
                    $properties['deploy_email'] = \Model::PIPE_DEPLOY_STATUS_INACTIVE;
                    break;

                case \Model::PIPE_DEPLOY_STATUS_INACTIVE:
                case \Model::PIPE_DEPLOY_STATUS_ACTIVE:
                    // leave what's there
                    break;
            }
        }

        if (isset($properties) && isset($properties['deploy_api']))
        {
            switch ($properties['deploy_api'])
            {
                default:
                    $properties['deploy_api'] = \Model::PIPE_DEPLOY_STATUS_INACTIVE;
                    break;

                case \Model::PIPE_DEPLOY_STATUS_INACTIVE:
                case \Model::PIPE_DEPLOY_STATUS_ACTIVE:
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

        // if the task is set, make sure it's an object and then encode it as JSON for storage
        if (isset($properties) && isset($properties['task']))
        {
            if (!is_array($properties['task']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

            $properties['task'] = \Flexio\Jobs\Base::fixEmptyParams($properties['task']);
            $properties['task'] = \Flexio\Jobs\Base::flattenParams($properties['task']);
            $properties['task'] = json_encode($properties['task']);
        }

        // if the examples is set, encode it as JSON for storage
        if (isset($properties) && isset($properties['examples']))
            $properties['examples']= json_encode($properties['examples']);

        // if the examples is set, encode it as JSON for storage
        if (isset($properties) && isset($properties['params']))
            $properties['params']= json_encode($properties['params']);

        // if the deploy mode is set, make sure it's valid; otherwise default to 'build'
        if (isset($properties) && isset($properties['deploy_mode']))
        {
            switch ($properties['deploy_mode'])
            {
                default:
                    $properties['deploy_mode'] = \Model::PIPE_DEPLOY_MODE_BUILD;
                    break;

                case \Model::PIPE_DEPLOY_MODE_BUILD:
                case \Model::PIPE_DEPLOY_MODE_RUN:
                    // leave what's there
                    break;
            }
        }

        // if the schedule is set, make sure it's valid and then encode it as JSON for storage
        if (isset($properties) && isset($properties['schedule']))
        {
            $schedule = $properties['schedule'];
            if (\Flexio\Base\Schedule::isValid($schedule) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

            $properties['schedule'] = json_encode($schedule);
        }

        // TODO: clean up the following overly-verbose implementation:

        // if the schedule status is set, make sure it's valid; otherwise default to 'inactive'
        if (isset($properties) && isset($properties['deploy_schedule']))
        {
            switch ($properties['deploy_schedule'])
            {
                default:
                    $properties['deploy_schedule'] = \Model::PIPE_DEPLOY_STATUS_INACTIVE;
                    break;

                case \Model::PIPE_DEPLOY_STATUS_INACTIVE:
                case \Model::PIPE_DEPLOY_STATUS_ACTIVE:
                    // leave what's there
                    break;
            }
        }

        if (isset($properties) && isset($properties['deploy_email']))
        {
            switch ($properties['deploy_email'])
            {
                default:
                    $properties['deploy_email'] = \Model::PIPE_DEPLOY_STATUS_INACTIVE;
                    break;

                case \Model::PIPE_DEPLOY_STATUS_INACTIVE:
                case \Model::PIPE_DEPLOY_STATUS_ACTIVE:
                    // leave what's there
                    break;
            }
        }

        if (isset($properties) && isset($properties['deploy_api']))
        {
            switch ($properties['deploy_api'])
            {
                default:
                    $properties['deploy_api'] = \Model::PIPE_DEPLOY_STATUS_INACTIVE;
                    break;

                case \Model::PIPE_DEPLOY_STATUS_INACTIVE:
                case \Model::PIPE_DEPLOY_STATUS_ACTIVE:
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
        if (\Flexio\Base\Schedule::isValid($schedule) === false)
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
        if ($status === \Model::STATUS_DELETED)
            return $this->delete();

        $this->clearCache();
        $pipe_model = $this->getModel()->pipe;
        $result = $pipe_model->set($this->getEid(), array('eid_status' => $status));
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
        if (!$this->properties)
            return false;

        return true;
    }

    private function clearCache() : void
    {
        $this->properties = null;
    }

    private function populateCache() : void
    {
        $pipe_model = $this->getModel()->pipe;
        $properties = $pipe_model->get($this->getEid());
        $this->properties = self::formatProperties($properties);
    }

    private static function formatProperties(array $properties) : array
    {
        $mapped_properties = \Flexio\Base\Util::mapArray(
            [
                "eid" => null,
                "eid_type" => null,
                "eid_status" => null,
                "parent" => null,
                "name" => null,
                "title" => null,
                "icon" => null,
                "description" => null,
                "examples" => null,
                "params" => null,
                "notes" => null,
                "task" => null,
                "schedule" => null,
                "deploy_mode" => null,
                "deploy_schedule" => null,
                "deploy_email" => null,
                "deploy_api" => null,
                "owned_by" => null,
                "created_by" => null,
                "created" => null,
                "updated" => null
            ],
        $properties);

        // sanity check: if the data record is missing, then eid will be null
        if (!isset($mapped_properties['eid']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // expand the parent connection info
        $mapped_properties['parent'] = array(
            'eid' => $properties['parent_eid'] ?? "",
            'eid_type' => \Model::TYPE_CONNECTION
        );

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

        // unpack the examples json
        $examples = @json_decode($mapped_properties['examples'],true);
        if ($examples !== false)
            $mapped_properties['examples'] = $examples;

        // unpack the params json
        $params = @json_decode($mapped_properties['params'],true);
        if ($params !== false)
            $mapped_properties['params'] = $params;

        // unpack the schedule json
        $schedule = @json_decode($mapped_properties['schedule'],true);
        if ($schedule !== false)
            $mapped_properties['schedule'] = $schedule;

        // expand the user info
        $mapped_properties['owned_by'] = array(
            'eid' => $properties['owned_by'],
            'eid_type' => \Model::TYPE_USER
        );
        $mapped_properties['created_by'] = array(
            'eid' => $properties['created_by'],
            'eid_type' => \Model::TYPE_USER
        );

        return $mapped_properties;
    }
}
