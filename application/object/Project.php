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


class Project extends \Flexio\Object\Base
{
    public function __construct()
    {
        $this->setType(\Model::TYPE_PROJECT);
    }

    public static function create(array $properties = null) : \Flexio\Object\Project
    {
        $object = new static();
        $model = $object->getModel();
        $local_eid = $model->create($object->getType(), $properties);

        $object->setEid($local_eid);
        $object->clearCache();

        return $object;
    }

    public function set(array $properties) : \Flexio\Object\Project
    {
        // TODO: add properties check

        $this->clearCache();
        $project_model = $this->getModel()->project;
        $project_model->set($this->getEid(), $properties);
        return $this;
    }

    public function get() : array
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties;
    }

    public function addPipe(\Flexio\Object\Pipe $object) : \Flexio\Object\Project
    {
        $this->clearCache();

        $project_eid = $this->getEid();
        $object_eid = $object->getEid();

        $result1 = $this->getModel()->assoc_add($project_eid, \Model::EDGE_HAS_MEMBER, $object_eid);
        $result2 = $this->getModel()->assoc_add($object_eid, \Model::EDGE_MEMBER_OF, $project_eid);

        return $this;
    }

    public function addConnection(\Flexio\Object\Connection $object) : \Flexio\Object\Project
    {
        $this->clearCache();

        $project_eid = $this->getEid();
        $object_eid = $object->getEid();

        $result1 = $this->getModel()->assoc_add($project_eid, \Model::EDGE_HAS_MEMBER, $object_eid);
        $result2 = $this->getModel()->assoc_add($object_eid, \Model::EDGE_MEMBER_OF, $project_eid);

        return $this;
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
            "eid_type" : "'.\Model::TYPE_PROJECT.'",
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

        // return the properties
        return $properties;
    }
}
