<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-12-16
 *
 * @package flexio
 * @subpackage Object
 */


namespace Flexio\Object;


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Abstract.php';

class Base implements IObject
{
    private $model;
    private $eid;
    private $eid_type;
    private $rights;

    // properties for derived classes
    protected $eid_status;
    protected $properties;

/*
    private $data = [];
    private $dirty = [];

    public function __get($property)
    {
        if (array_key_exists($property, $this->data))
            return $this->data[$property];
    }

    public function __set($property, $value)
    {
        // if the property exists and has been changed, update it and mark it as dirty

        if (array_key_exists($property, $this->data) && $this->data[$property] !== $value)
        {
            $this->data[$property] = $value;
            $this->dirty[$property] = true;
        }

        return $this;
    }
*/

    public function __construct()
    {
        $this->setType(\Model::TYPE_UNDEFINED);
    }

    public static function create($properties = null)
    {
        $object = new static();
        $model = \Flexio\Object\Store::getModel();

        $local_eid = $model->create($object->getType(), $properties);
        if ($local_eid === false)
            return false;

        $object->setModel($model);
        $object->setEid($local_eid);
        $object->setRights();
        $object->clearCache();
        return $object;
    }

    public static function load($identifier)
    {
        $object = new static();
        $model = \Flexio\Object\Store::getModel();

        // assume the identifier is an eid, and try to find out the type
        $eid = $identifier;
        $local_eid_type = $model->getType($identifier);

        if ($local_eid_type !== $object->getType())
        {
            // the input isn't an eid, so it must be an identifier; try
            // to find the eid from the identifier; if we can't find it,
            // we're done
            $eid = $model->getEidFromEname($identifier);
            if ($eid === false)
                return false;
        }

        // TODO: for now, don't allow objects that have been deleted
        // to be loaded; in general, we may want to move this to the
        // api layer, but previously, it's been in the model layer,
        // and we need to make sure the behavior is the same after the
        // model constraint is removed, and object loading is a good
        // location for this constraint
        if ($model->getStatus($eid) === \Model::STATUS_DELETED)
            return false;

        $object->setModel($model);
        $object->setEid($eid);
        $object->setRights();
        $object->clearCache();
        return $object;
    }

    public function copy()
    {
        // get the parameters, reset the ename so the create doesn't
        // fail on account of a duplicate name
        $properties = $this->get();
        unset($properties['ename']);

        // call the create function on the derived class
        $object = static::create($properties);
        return $object;
    }

    public function delete()
    {
        $this->clearCache();
        $this->getModel()->delete($this->getEid());
        return $this;
    }

    public function set($properties)
    {
        $this->clearCache();

        if (isset($properties['eid_status']))
        {
            $status = $properties['eid_status'];
            $result = $this->getModel()->setStatus($this->getEid(), $status);
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

    public function setEid($eid)
    {
        // only allow the eid to be set once
        if (!is_null($this->eid))
            return false;

        $this->eid = $eid;
        return true;
    }

    public function getEid()
    {
        return $this->eid;
    }

    public function setType($eid_type)
    {
        // only allow the eid_type to be set once
        if (!is_null($this->eid_type))
            return false;

        $this->eid_type = $eid_type;
        return $this;
    }

    public function getType()
    {
        return $this->eid_type;
    }

    public function setStatus($status)
    {
        // TODO: for now, don't forward model exception
        try
        {
            $this->clearCache();
            $result = $this->getModel()->setStatus($this->getEid(), $status);
        }
        catch (\Exception $e)
        {
        }

        return $this;
    }

    public function getStatus()
    {
        if ($this->eid_status !== false)
            return $this->eid_status;

        $status = $this->getModel()->getStatus($this->getEid());
        $this->eid_status = $status;

        return $status;
    }

    public function setOwner($user_eid)
    {
        // TODO: remove previous owner, if any

        if ($user_eid === false)
            return false;

        // TODO: do we want to do more checking? have to be careful because
        // system and public users don't follow normal eid convention

        $object_eid = $this->getEid();
        $result1 = $this->getModel()->assoc_add($user_eid, \Model::EDGE_OWNS, $object_eid);
        $result2 = $this->getModel()->assoc_add($object_eid, \Model::EDGE_OWNED_BY, $user_eid);

        if ($result1 === false || $result2 === false)
            return false;

        return true;
    }

    public function getOwner()
    {
        $object_eid = $this->getEid();
        $result = $this->getModel()->assoc_range($object_eid, \Model::EDGE_OWNED_BY);

        if (count($result) === 0)
            return false;

        return $result[0]['eid'];
    }

    public function setCreatedBy($user_eid)
    {
        // TODO: remove previous created by, if any

        if ($user_eid === false)
            return false;

        // TODO: do we want to do more checking? have to be careful because
        // system and public users don't follow normal eid convention

        $object_eid = $this->getEid();
        $result1 = $this->getModel()->assoc_add($user_eid, \Model::EDGE_CREATED, $object_eid);
        $result2 = $this->getModel()->assoc_add($object_eid, \Model::EDGE_CREATED_BY, $user_eid);

        if ($result1 === false || $result2 === false)
            return false;

        return true;
    }

    public function getCreatedBy()
    {
        $object_eid = $this->getEid();
        $result = $this->getModel()->assoc_range($object_eid, \Model::EDGE_CREATED_BY);

        if (count($result) === 0)
            return false;

        return $result[0]['eid'];
    }

    public function allows($user_eid, $action_type)
    {
        // find out all operations the specified user can take on the
        // object in question
        if ($this->rights === false)
            $this->rights = \Flexio\Object\Rights::listall($user_eid, $this->getEid());

        // if the rights exist and are set to true, allow the action
        if (isset($this->rights[$action_type]) && $this->rights[$action_type] === true)
            return true;

        return false;
    }

    public function setRights($rights = array())
    {
        // TODO: set the rights

        // reset the rights cache
        $this->rights = false;
    }

    public function addComment($comment_eid)
    {
        // make sure we have a comment
        $comment = \Flexio\Object\Comment::load($comment_eid);
        if ($comment === false)
            return false;

        // add the comment association
        $object_eid = $this->getEid();
        $result1 = $this->getModel()->assoc_add($object_eid, \Model::EDGE_HAS_COMMENT, $comment_eid);
        $result2 = $this->getModel()->assoc_add($comment_eid, \Model::EDGE_COMMENT_ON, $object_eid);

        if ($result1 === false || $result2 === false)
            return false;

        return true;
    }

    public function getComments()
    {
        $result = array();

        $object_eid = $this->getEid();
        $res = $this->getModel()->assoc_range($object_eid, \Model::EDGE_HAS_COMMENT, [\Model::STATUS_AVAILABLE]);

        foreach ($res as $item)
        {
            $comment_eid = $item['eid'];
            $comment = \Flexio\Object\Comment::load($comment_eid);
            if ($comment === false)
                continue;

            $result[] = $comment;
        }

        return $result;
    }

    protected function setModel($model)
    {
        return $this->model = $model;
    }

    protected function getModel()
    {
        return $this->model;
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
