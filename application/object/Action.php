<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-04-17
 *
 * @package flexio
 * @subpackage Object
 */


declare(strict_types=1);
namespace Flexio\Object;


class Action
{
    private $eid;
    protected $properties;

    public static function list(array $filter) : array
    {
        $object = new static();
        $action_model = $object->getModel()->action;
        $items = $action_model->list($filter);

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

    public static function create(array $properties = null) : \Flexio\Object\Action
    {
        $object = new static();
        $action_model = $object->getModel()->action;
        $local_eid = $action_model->create($properties);

        $object->setEid($local_eid);
        $object->clearCache();
        return $object;
    }

    public function set(array $properties) : \Flexio\Object\Action
    {
        // TODO: add properties check

        $this->clearCache();
        $action_model = $this->getModel()->action;
        $action_model->set($this->getEid(), $properties);
        return $this;
    }

    public function get() : array
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties;
    }

    public function setEid(string $eid) : \Flexio\Object\Action
    {
        // only allow the eid to be set once
        if (!is_null($this->eid))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

        $this->eid = $eid;
        return $this;
    }

    public function getEid() : string
    {
        return $this->eid;
    }

    protected function getModel() : \Model
    {
        return \Flexio\System\System::getModel();
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
        $action_model = $this->getModel()->action;
        $local_properties = $action_model->get($this->getEid());
        $this->properties = self::formatProperties($local_properties);
        return true;
    }

    private static function formatProperties(array $properties) : array
    {
        $action_model = $this->getModel()->action;
        $mapped_properties = $action_model->get($properties['eid']);

        // sanity check: if the data record is missing, then eid will be null
        if (!isset($mapped_properties['eid']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // return the properties
        return $mapped_properties;
    }
}
