<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-05-30
 *
 * @package flexio
 * @subpackage Object
 */


declare(strict_types=1);
namespace Flexio\Object;


class Right extends \Flexio\Object\Base
{
    public function __construct()
    {
        $this->setType(\Model::TYPE_RIGHT);
    }

    public static function create(array $properties = null) : \Flexio\Object\Right
    {
        $object = new static();
        $model = \Flexio\Object\Store::getModel();
        $local_eid = $model->create($object->getType(), $properties);

        $object->setModel($model);
        $object->setEid($local_eid);
        $object->clearCache();
        return $object;
    }

    public function set(array $properties) : \Flexio\Object\Right
    {
        // TODO: add properties check

        // actions are stored as a json string, so this needs to be encoded
        if (isset($properties) && isset($properties['actions']))
            $properties['actions'] = json_encode($properties['actions']);

        $this->clearCache();
        $right_model = $this->getModel()->right;
        $right_model->set($this->getEid(), $properties);
        return $this;
    }

    public function get() : array
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties;
    }

    private function isCached() : bool
    {
        // always return the latest rights
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
            "eid_type" : "'.\Model::TYPE_RIGHT.'",
            "eid_status" : null,
            "object_eid": null,
            "access_type" : null,
            "access_code" : null,
            "actions" : null,
            "created" : null,
            "updated" : null
        }
        ';

        // execute the query
        $query = json_decode($query);
        $properties = \Flexio\Object\Query::exec($this->getEid(), $query);
        if (!$properties)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // unpack the actions object
        $properties['actions'] = json_decode($properties['actions'],true);

        // return the properties
        return $properties;
    }
}
