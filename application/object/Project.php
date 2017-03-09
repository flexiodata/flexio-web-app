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


class Project extends \Flexio\Object\Base
{
    public function __construct()
    {
        $this->setType(\Model::TYPE_PROJECT);
    }

    public function set($properties)
    {
        // TODO: add properties check
        $this->clearCache();
        $project_model = $this->getModel()->project;
        $project_model->set($this->getEid(), $properties);
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

    public function addMember($object_eid)
    {
        if (!\Flexio\Base\Eid::isValid($object_eid))
            return false;

        $this->clearCache();

        $project_eid = $this->getEid();
        $result1 = $this->getModel()->assoc_add($project_eid, \Model::EDGE_HAS_MEMBER, $object_eid);
        $result2 = $this->getModel()->assoc_add($object_eid, \Model::EDGE_MEMBER_OF, $project_eid);

        if ($result1 === false || $result2 === false)
            return false;

        return true;
    }

    public function getMembers()
    {
        $result = array();

        $object_eid = $this->getEid();
        $res = $this->getModel()->assoc_range($object_eid, \Model::EDGE_HAS_MEMBER);

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
            "eid_type" : "'.\Model::TYPE_PROJECT.'",
            "eid_status" : null,
            "ename" : null,
            "name" : null,
            "description" : null,
            "follower_count" : null,
            "pipe_count" : null,
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
            "created" : null,
            "updated" : null
        }
        ';

        // execute the query
        $query = json_decode($query);
        $properties = \Flexio\Object\Query::exec($this->getEid(), $query);
        if (!$properties)
            return false;

        // populate the follower count
        $follower_count = $this->getModel()->assoc_count($this->getEid(),
                                                         \Model::EDGE_FOLLOWED_BY,
                                                         \Model::STATUS_AVAILABLE) + 1; // plus 1 to include owner
        $properties['follower_count'] = $follower_count;

        // populate the pipe count
        $pipe_count = 0;
        $objects = $this->getMembers();
        foreach ($objects as $o)
        {
            $object_status = $o->getStatus();
            $object_type = $o->getType();

            if ($object_status !== \Model::STATUS_AVAILABLE)
                continue;

            if ($object_type === \Model::TYPE_PIPE)
                $pipe_count++;
        }
        $properties['pipe_count'] = $pipe_count;

        // return the properties
        return $properties;
    }
}
