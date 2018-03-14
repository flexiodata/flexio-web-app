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
        // make sure we have a filter some kind
        foreach ($filter as $key => $value)
        {
            if (isset($filter['eid'])) break;
            if (isset($filter['owned_by'])) break;

            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }

        // TODO: load object info here; pass on model info for now
        $object = new static();
        $action_model = $object->getModel()->action;
        return $action_model->list($filter);
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
        $this->properties = $this->getProperties();
        return true;
    }

    private function getProperties() : array
    {
        $action_model = $this->getModel()->action;
        $properties = $action_model->get($this->getEid());

        // sanity check: if the data record is missing, then eid will be null
        if (!$properties || ($properties['eid'] ?? null) === null)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // return the properties
        return $properties;
    }
}
