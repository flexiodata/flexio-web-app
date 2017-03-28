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


namespace Flexio\Object;


class Object extends \Flexio\Object\Base
{
    public function __construct()
    {
        $this->setType(\Model::TYPE_OBJECT);
    }

    public function set($properties)
    {
        $this->clearCache();

        if (isset($properties['eid_status']))
        {
            $status = $properties['eid_status'];
            $this->getModel()->setStatus($this->getEid(), $status);
        }

        return $this;
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
            "eid_type" : null,
            "eid_status" : null,
            "created" : null,
            "updated" : null
        }
        ';

        // execute the query
        $query = json_decode($query);
        $properties = \Flexio\Object\Query::exec($this->getEid(), $query);
        if (!$properties)
            return false;

        // return the properties
        return $properties;
    }
}
