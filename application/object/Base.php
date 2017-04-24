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


declare(strict_types=1);
namespace Flexio\Object;


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Abstract.php';

class Base implements IObject
{
    private $model;
    private $eid;
    private $eid_type;

    // properties for derived classes
    protected $eid_status;
    protected $properties;

    public function __construct()
    {
        $this->setType(\Model::TYPE_UNDEFINED);
    }

    public function __toString()
    {
        $object = array(
            'eid' => $this->getEid(),
            'eid_type' => $this->getType()
        );
        return json_encode($object);
    }

    public static function create(array $properties = null) : \Flexio\Object\Base
    {
        $object = new static();
        $model = \Flexio\Object\Store::getModel();
        $local_eid = $model->create($object->getType(), $properties);

        $object->setModel($model);
        $object->setEid($local_eid);
        $object->clearCache();

        // set the default rights; give the owner everything; give group members
        // everything except the ability to change rights
        $acl = \Flexio\Object\Acl::create();

        $acl->add(\Flexio\Base\Action::TYPE_READ_RIGHTS, \Flexio\Base\User::MEMBER_OWNER);
        $acl->add(\Flexio\Base\Action::TYPE_WRITE_RIGHTS, \Flexio\Base\User::MEMBER_OWNER);

        $acl->add(\Flexio\Base\Action::TYPE_READ, \Flexio\Base\User::MEMBER_OWNER);
        $acl->add(\Flexio\Base\Action::TYPE_WRITE, \Flexio\Base\User::MEMBER_OWNER);
        $acl->add(\Flexio\Base\Action::TYPE_DELETE, \Flexio\Base\User::MEMBER_OWNER);
        $acl->add(\Flexio\Base\Action::TYPE_EXECUTE, \Flexio\Base\User::MEMBER_OWNER);

        $acl->add(\Flexio\Base\Action::TYPE_READ, \Flexio\Base\User::MEMBER_GROUP);
        $acl->add(\Flexio\Base\Action::TYPE_WRITE, \Flexio\Base\User::MEMBER_GROUP);
        $acl->add(\Flexio\Base\Action::TYPE_DELETE, \Flexio\Base\User::MEMBER_GROUP);
        $acl->add(\Flexio\Base\Action::TYPE_EXECUTE, \Flexio\Base\User::MEMBER_GROUP);

        $object->rights($acl);

        return $object;
    }

    public static function load(string $identifier)
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
        $object->clearCache();
        return $object;
    }

    public function copy() : \Flexio\Object\Base
    {
        // get the parameters, reset the ename so the create doesn't
        // fail on account of a duplicate name
        $properties = $this->get();
        unset($properties['ename']);

        // call the create function on the derived class
        $object = static::create($properties);
        return $object;
    }

    public function delete() : \Flexio\Object\Base
    {
        $this->clearCache();
        $this->getModel()->delete($this->getEid());
        return $this;
    }

    public function set(array $properties) : \Flexio\Object\Base
    {
        $this->clearCache();

        if (isset($properties['eid_status']))
        {
            $status = $properties['eid_status'];
            $result = $this->getModel()->setStatus($this->getEid(), $status);
        }

        return $this;
    }

    public function get() : array
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties;
    }

    public function setEid(string $eid) : \Flexio\Object\Base
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

    public function setType(string $eid_type) : \Flexio\Object\Base
    {
        // only allow the eid_type to be set once
        if (!is_null($this->eid_type))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

        $this->eid_type = $eid_type;
        return $this;
    }

    public function getType() : string
    {
        return $this->eid_type;
    }

    public function setStatus(string $status) : \Flexio\Object\Base
    {
        $this->clearCache();
        $result = $this->getModel()->setStatus($this->getEid(), $status);
        return $this;
    }

    public function getStatus() : string
    {
        if ($this->eid_status !== false)
            return $this->eid_status;

        $status = $this->getModel()->getStatus($this->getEid());
        $this->eid_status = $status;

        return $status;
    }

    public function setOwner(string $user_eid) : \Flexio\Object\Base
    {
        // TODO: remove previous owner, if any

        // TODO: do we want to do more checking? have to be careful because
        // system and public users don't follow normal eid convention

        $object_eid = $this->getEid();
        $this->getModel()->assoc_add($user_eid, \Model::EDGE_OWNS, $object_eid);
        $this->getModel()->assoc_add($object_eid, \Model::EDGE_OWNED_BY, $user_eid);
        return $this;
    }

    public function getOwner() : string
    {
        $object_eid = $this->getEid();
        $result = $this->getModel()->assoc_range($object_eid, \Model::EDGE_OWNED_BY);

        if (count($result) === 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        return $result[0]['eid'];
    }

    public function setCreatedBy(string $user_eid) : \Flexio\Object\Base
    {
        // TODO: deprecated; move this information over to an action log

        // TODO: remove previous created by, if any

        // TODO: do we want to do more checking? have to be careful because
        // system and public users don't follow normal eid convention

        $object_eid = $this->getEid();
        $this->getModel()->assoc_add($user_eid, \Model::EDGE_CREATED, $object_eid);
        $this->getModel()->assoc_add($object_eid, \Model::EDGE_CREATED_BY, $user_eid);
        return $this;
    }

    public function getCreatedBy() : string
    {
        // TODO: deprecated; move this information over to an action log

        $object_eid = $this->getEid();
        $result = $this->getModel()->assoc_range($object_eid, \Model::EDGE_CREATED_BY);

        if (count($result) === 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        return $result[0]['eid'];
    }

    public function allows(string $user_eid, string $action) : bool
    {
        // note: like the status, read the rights fresh everytime to make
        // sure we have the most current information

        // get the rights for this object
        $rights = $this->getModel()->getRights($this->getEid());
        if ($rights === false)
            return false;

        $rights = json_decode($rights,true);
        if ($rights === false)
            return false;

        $acl = \Flexio\Object\Acl::create($rights);
        $acl_items = $acl->get();

        if (!isset($acl_items[$action]))
            return false;

        // the action is in the list of allowed actions; see if the action is public
        // first to avoid having to do a user lookup
        $owner_allowed = false;
        $group_allowed = false;

        $user_list = $acl_items[$action];
        foreach ($user_list as $user)
        {
            // public rights exist for the action in question; we're done
            if ($user === \Flexio\Object\User::MEMBER_PUBLIC)
                return true;

            if ($user === \Flexio\Object\User::MEMBER_OWNER)
                $owner_allowed = true;

            if ($user === \Flexio\Object\User::MEMBER_GROUP)
                $group_allowed = true;
        }

        // public rights don't exist for the action in question; see if the owner
        // is allowed next since it's a faster lookup than group membership
        if ($owner_allowed === true && $this->getOwner() === $user_eid)
            return true;

        if ($group_allowed === true)
        {
            // TODO: lookup if the user is a member of the group; return true
            // if they are and false otherwise
            return false;
        }

        // user isn't the owner or part of the group
        return false;
    }

    public function rights(\Flexio\Object\Acl $acl = null) : \Flexio\Object\Base
    {
        // if rights aren't specified, create a default set of rights without
        // any users/actions specified; this allows us to reset the rights
        if (!isset($acl))
            $acl = \Flexio\Object\Acl::create();

        // save the rights
        $rights = json_encode($acl->get());
        $this->getModel()->setRights($this->getEid(), $rights);
        return $this;
    }

    protected function setModel($model) : \Flexio\Object\Base // TODO: set parameter type
    {
        $this->model = $model;
        return $this;
    }

    protected function getModel() : \Model
    {
        return $this->model;
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
        $this->properties = $local_properties;
        $this->eid_status = $local_properties['eid_status'];
        return true;
    }

    private function getProperties() : array
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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // return the properties
        return $properties;
    }
}
