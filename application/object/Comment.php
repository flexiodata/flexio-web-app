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


class Comment extends \Flexio\Object\Base
{
    public function __construct()
    {
        $this->setType(\Model::TYPE_COMMENT);
    }

    public static function create(array $properties = null) : \Flexio\Object\Comment
    {
        $object = new static();
        $model = \Flexio\Object\Store::getModel();
        $local_eid = $model->create($object->getType(), $properties);

        $object->setModel($model);
        $object->setEid($local_eid);
        $object->setRights();
        $object->clearCache();
        return $object;
    }

    public function set(array $properties) : \Flexio\Object\Comment
    {
        // TODO: add properties check

        $this->clearCache();
        $comment_model = $this->getModel()->comment;
        $comment_model->set($this->getEid(), $properties);
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
            "eid_type" : "'.\Model::TYPE_COMMENT.'",
            "eid_status" : null,
            "comment" : null,
            "replies='.\Model::EDGE_HAS_COMMENT.'" : [{
                "eid" : null,
                "eid_type" : "'.\Model::TYPE_COMMENT.'",
                "comment" : null,
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
            }],
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

        // return the properties
        return $properties;
    }
}
