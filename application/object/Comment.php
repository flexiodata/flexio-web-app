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


class Comment extends \Flexio\Object\Base implements \Flexio\IFace\IObject
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

    public static function list(array $filter) : array
    {
        // make sure we have a filter on one of the indexed fields
        foreach ($filter as $key => $value)
        {
            if (isset($filter['eid'])) break;
            if (isset($filter['owned_by'])) break;

            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }

        // TODO: load object info here; pass on model info for now
        $object = new static();
        $comment_model = $object->getModel()->comment;
        return $comment_model->list($filter);
    }

    public static function load(string $eid) : \Flexio\Object\Comment
    {
        $object = new static();
        $comment_model = $object->getModel()->comment;

        $status = $comment_model->getStatus($eid);
        if ($status === \Model::STATUS_UNDEFINED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // TODO: for now, don't allow objects that have been deleted
        // to be loaded; in general, we may want to move this to the
        // api layer, but previously, it's been in the model layer,
        // and we need to make sure the behavior is the same after the
        // model constraint is removed, and object loading is a good
        // location for this constraint
        if ($status == \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        $object->setEid($eid);
        $object->clearCache();
        return $object;
    }

    public static function create(array $properties = null) : \Flexio\Object\Comment
    {
        $object = new static();
        $comment_model = $object->getModel()->comment;
        $local_eid = $comment_model->create($properties);

        $object->setEid($local_eid);
        $object->clearCache();
        return $object;
    }

    public function delete() : \Flexio\Object\Comment
    {
        $this->clearCache();
        $comment_model = $this->getModel()->comment;
        $comment_model->delete($this->getEid());
        return $this;
    }

    public function set(array $properties) : \Flexio\Object\Comment
    {
        // TODO: add properties check

        $this->clearCache();
        $comment_model = $this->getModel()->comment;
        $comment_model->set($this->getEid(), $properties);
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
        return \Model::TYPE_COMMENT;
    }

    public function setOwner(string $user_eid) : \Flexio\Object\Comment
    {
        $properties = array('owned_by' => $user_eid);
        $this->set($properties);
        return $this;
    }

    public function getOwner() : string
    {
        $comment_model = $this->getModel()->comment;
        return $comment_model->getOwner($this->getEid());
    }

    public function setStatus(string $status) : \Flexio\Object\Comment
    {
        $this->clearCache();
        $comment_model = $this->getModel()->comment;
        $result = $comment_model->setStatus($this->getEid(), $status);
        return $this;
    }

    public function getStatus() : string
    {
        if ($this->properties !== false && isset($this->properties['eid_status']))
            return $this->properties['eid_status'];

        $comment_model = $this->getModel()->comment;
        $status = $comment_model->getStatus($this->getEid());

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
                "created" : null,
                "updated" : null
            }],
            "owned_by" : {
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
