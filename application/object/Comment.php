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

        $object = new static();
        $comment_model = $object->getModel()->comment;
        $items = $comment_model->list($filter);

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

    public static function load(string $eid) : \Flexio\Object\Comment
    {
        $object = new static();
        $comment_model = $object->getModel()->comment;

        $status = $comment_model->getStatus($eid);
        if ($status === \Model::STATUS_UNDEFINED)
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
        $comment_model = $this->getModel()->comment;
        $local_properties = $comment_model->get($this->getEid());
        $this->properties = self::formatProperties($local_properties);
        return true;
    }

    private static function formatProperties(array $properties) : array
    {
/*
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
*/
        $mapped_properties = \Flexio\Base\Util::mapArray(
            [
                "eid" => null,
                "eid_type" => null,
                "eid_status" => null,
                "comment" => null,
                "replies" => null,
                "owned_by" => null,
                "created" => null,
                "updated" => null
            ],
        $properties);

        // sanity check: if the data record is missing, then eid will be null
        if (!isset($mapped_properties['eid']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // TODO: expand the replies and owner info
        $mapped_properties['replies'] = (object)array(); // placholder

        // expand the owner info
        $mapped_properties['owned_by'] = array(
            'eid' => $properties['owned_by'],
            'eid_type' => \Model::TYPE_USER
        );

        return $mapped_properties;

    }
}
