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

        // TODO: for now, don't allow any rights by default; change?

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

    public function getOwner() // TODO: add return type
    {
        $object_eid = $this->getEid();
        $result = $this->getModel()->assoc_range($object_eid, \Model::EDGE_OWNED_BY);

        // TODO: for now, return false; there are some objects (legacy?) that
        // don't have owners and when checking the owner for rights, we need
        // a graceful way of not granting access rather than throwing an exception
        if (count($result) === 0)
            return false;

        //if (count($result) === 0)
        //    throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

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

    public function getCreatedBy() // TODO: add return result
    {
        // TODO: deprecated; move this information over to an action log

        $object_eid = $this->getEid();
        $result = $this->getModel()->assoc_range($object_eid, \Model::EDGE_CREATED_BY);

        // TODO: see comment for similar logic in getOwner() above
        if (count($result) === 0)
            return false;

        //if (count($result) === 0)
        //    throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        return $result[0]['eid'];
    }

    public function allows(string $access_code, string $action) : bool
    {
        // note: like the status, read the rights fresh everytime to make
        // sure we have the most current information

        // get the rights for this object
        $rights = $this->getRights();

        // get all the allowed actions for the access code or any
        // public access type class
        $allowed_actions = array();
        foreach ($rights as $r)
        {
            $access_type = $r['access_type'];

            // merge any allowed actions for matching eid access codes;
            // TODO: when other access types are added, we'll need to add
            // a parameter to the function to qualify the access code; for
            // now, all access types are either eids or the public access
            // type category
            if ($access_type === \Model::ACCESS_CODE_TYPE_EID && $access_code === $r['access_code'])
                $allowed_actions = array_merge($allowed_actions, $r['actions']);

            if ($access_type === \Model::ACCESS_CODE_TYPE_CATEGORY && $access_code === \Flexio\Object\User::MEMBER_PUBLIC)
                $allowed_actions = array_merge($allowed_actions, $r['actions']);
        }

        // see if the requested action is allowed
        foreach ($allowed_actions as $allowed_action)
        {
            if ($action === $allowed_action)
                return true;
        }

        // action not allowed
        return false;
    }

    public function grant(string $access_code, string $access_type, array $actions) : \Flexio\Object\Base
    {
        $r = array();
        $r['actions'] = $actions;
        $r['access_code'] = $access_code;
        $r['access_type'] = $access_type;

        $rights = array();
        $rights[] = $r;

        $this->setRights($rights);
        return $this;
    }

    public function revoke(string $access_code, string $access_type, array $actions = null) : \Flexio\Object\Base
    {
        // if actions is null, revoke all rights for the given access code and
        // access type; otherwise, revoke the specified actions

        // get a list of rights
        $rights = $this->getRights();
        foreach ($rights as $r)
        {
            if ($access_code !== $r['access_code'])
                continue;
            if ($access_type !== $r['access_type'])
                continue;

            if (!is_array($actions))
            {
                // if actions is null, revoke all the rights
                $right_eid = $r['eid'];
                $this->getModel()->right->delete($right_eid);
            }
             else
            {
                // if actions is specified
                $new_actions = array();
                $actions_currently_allowed = $r['actions'];
                $actions_to_delete = array_flip($actions);

                foreach ($actions_currently_allowed as $a)
                {
                    if (array_key_exists($a, $actions_to_delete))
                        continue;

                    $new_actions[] = $a;
                }

                $right_eid = $r['eid'];
                $this->getModel()->right->set($right_eid, json_encode($new_actions));
            }
        }
    }

    public function setRights(array $rights) : \Flexio\Object\Base
    {
        foreach ($rights as $r)
        {
            // see if the rights already exists
            $right_eid = $r['eid'] ?? '';
            $right = \Flexio\Object\Right::load($right_eid);

            if ($right !== false)
            {
                // only allow right associated with this object to be updated
                // from this object
                $right_info = $right->get();
                if ($this->getEid() !== $right_info['object_eid'])
                    continue;

                // the right already exists, so update it, but don't allow the
                // object to be changed
                $rights_updated = array();
                $rights_updated['actions'] = $r['actions'];
                $right->set($rights_updated);
            }
             else
            {
                $rights_new = array();
                $rights_new['object_eid'] = $this->getEid();
                $rights_new['access_code'] = $r['access_code'];
                $rights_new['access_type'] = $r['access_type'];
                $rights_new['actions'] = $r['actions'];
                \Flexio\Object\Right::create($rights_new);
            }
        }

        die('moo!!!');
        return $this;
    }

    public function getRights() : array
    {
        $result = array();
        $rights_info = $this->getModel()->right->getInfoFromObjectEid($this->getEid());

        foreach ($rights_info as $r)
        {
            $right_eid = $r['eid'];
            $right = \Flexio\Object\Right::load($right_eid);
            if ($right === false)
                continue;

            // only show rights that are available; note: the right list will
            // return all rights, including ones that have been deleted, so
            // this check is important
            if ($right->getStatus() !== \Model::STATUS_AVAILABLE)
                continue;

            $result[] = $right->get();
        }

        return $result;
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

    private function getUserClass(string $identifier) : string
    {
        // DEPRECATED: part of old user-class-based (owner/group/public) rights
        // TODO: when no longer needed

        if ($this->isOwned($identifier) === true)
            return \Flexio\Object\User::MEMBER_OWNER;

        if ($this->isMember($identifier) === true)
            return \Flexio\Object\User::MEMBER_GROUP;

        // user is uknown; they're a public user
        return \Flexio\Object\User::MEMBER_PUBLIC;
    }

    private function isOwned(string $identifier) : bool
    {
        // DEPRECATED: part of old user-class-based (owner/group/public) rights
        // TODO: when no longer needed

        if (!\Flexio\Base\Eid::isValid($identifier))
            return false;

        if ($identifier === $this->getOwner())
            return true;

        return false;
    }

    private function isMember(string $identifier) : bool
    {
        // DEPRECATED: part of old user-class-based (owner/group/public) rights
        // TODO: when no longer needed

        if (!\Flexio\Base\Eid::isValid($identifier))
            return false;

        $user_eid = $identifier;
        $object_eid = $this->getEid();

        // note: in the following, we want to check if an object can be accessed by
        // anybody who's part of a project; this includes any object that's in a
        // project that's either owned or followed by the user in question; for
        // example, it includes:
        //     1) rights to userA for the project if they are the owner of the project
        //     2) rights to userA for the project if they are a follower of the project
        //     3) rights to userA for an object owned by userB in a project owned by userA and followed by userB
        //     4) rights to userA for an object owned by userB in a project followed by userA and owned by userB

        // see if the object is followed or owned by the user directly
        $search_path = "$object_eid->(".\Model::EDGE_FOLLOWED_BY.",".\Model::EDGE_OWNED_BY.")->$user_eid";
        $followers = \Flexio\Object\Search::exec($search_path);
        if (count($followers) > 0)
            return true;

        // see if the object is a member of a project followed or owned by the user
        $search_path = "$object_eid->(".\Model::EDGE_MEMBER_OF.")->(".\Model::TYPE_PROJECT.")".
                                  "->(".\Model::EDGE_FOLLOWED_BY.",".\Model::EDGE_OWNED_BY.")->$user_eid";
        $followers = \Flexio\Object\Search::exec($search_path);
        if (count($followers) > 0)
            return true;

        // see if the object is a resource that's a member of an object
        // that's a member of a project followed or owned by the user
        $search_path = "$object_eid->(".\Model::EDGE_MEMBER_OF.")->(".\Model::TYPE_PIPE.",".\Model::TYPE_CONNECTION.")".
                                  "->(".\Model::EDGE_MEMBER_OF.")->(".\Model::TYPE_PROJECT.")".
                                  "->(".\Model::EDGE_FOLLOWED_BY.",".\Model::EDGE_OWNED_BY.")->$user_eid";
        $followers = \Flexio\Object\Search::exec($search_path);
        if (count($followers) > 0)
            return true;

        // we can't find a path where the object is being followed by the
        // given user
        return false;
    }
}
