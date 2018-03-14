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


class Right extends \Flexio\Object\Base implements \Flexio\IFace\IObject
{
    const TYPE_UNDEFINED        = '';                 // undefined
    const TYPE_READ             = 'object.read';      // ability to read the properties of an object
    const TYPE_WRITE            = 'object.write';     // ability to write the properties of an object
    const TYPE_DELETE           = 'object.delete';    // ability to delete an object
    const TYPE_EXECUTE          = 'object.execute';   // ability to run a process object
    const TYPE_READ_RIGHTS      = 'rights.read';      // ability to see rights
    const TYPE_WRITE_RIGHTS     = 'rights.write';     // ability to change rights

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

    public static function isValidType(string $action) : bool
    {
        switch ($action)
        {
            default:
            case self::TYPE_UNDEFINED:
                return false;

            case self::TYPE_READ:
            case self::TYPE_WRITE:
            case self::TYPE_DELETE:
            case self::TYPE_EXECUTE:
            case self::TYPE_READ_RIGHTS:
            case self::TYPE_WRITE_RIGHTS:
                return true;
        }
    }

    public static function list(array $filter) : array
    {
        // make sure we have a filter some kind
        foreach ($filter as $key => $value)
        {
            if (isset($filter['eid'])) break;
            if (isset($filter['owned_by'])) break;

            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }

        // TODO: load object info here; pass on model info for now
        $object = new static();
        $right_model = $object->getModel()->right;
        return $right_model->list($filter);
    }

    public static function load(string $eid) : \Flexio\Object\Right
    {
        $object = new static();
        $right_model = $object->getModel()->right;

        $status = $right_model->getStatus($eid);
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

    public static function create(array $properties = null) : \Flexio\Object\Right
    {
        // actions are stored as a json string, so this needs to be encoded
        if (isset($properties) && isset($properties['actions']))
            $properties['actions'] = json_encode($properties['actions']);

        $object = new static();
        $right_model = $object->getModel()->right;
        $local_eid = $right_model->create($properties);

        $object->setEid($local_eid);
        $object->clearCache();
        return $object;
    }

    public function delete() : \Flexio\Object\Right
    {
        $this->clearCache();
        $right_model = $this->getModel()->right;
        $right_model->delete($this->getEid());
        return $this;
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

    public function getType() : string
    {
        return \Model::TYPE_RIGHT;
    }

    public function setOwner(string $user_eid) : \Flexio\Object\Right
    {
        $properties = array('owned_by' => $user_eid);
        $this->set($properties);
        return $this;
    }

    public function getOwner() : string
    {
        $right_model = $this->getModel()->right;
        return $right_model->getOwner($this->getEid());
    }

    public function setStatus(string $status) : \Flexio\Object\Right
    {
        $this->clearCache();
        $right_model = $this->getModel()->right;
        $result = $right_model->setStatus($this->getEid(), $status);
        return $this;
    }

    public function getStatus() : string
    {
        if ($this->eid_status !== false)
            return $this->eid_status;

        $right_model = $this->getModel()->right;
        $status = $right_model->getStatus($this->getEid());
        $this->eid_status = $status;

        return $status;
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
            "access_code" : null,
            "access_type" : null,
            "actions" : null,
            "user" : null,
            "object=object_eid" : {
                "eid": null,
                "eid_type": null
            },
            "object_eid": null,
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

        // unpack the actions object
        $properties['actions'] = json_decode($properties['actions'],true);

        // populate the user info if possible
        try
        {
            $user = \Flexio\Object\User::load($properties['access_code']);
            $user_info = $user->get();
            $info['eid'] = $user_info['eid'];
            $info['eid_type'] = $user_info['eid_type'];
            $info['eid_status'] = $user_info['eid_status'];
            $info['user_name'] = $user_info['user_name'];
            $info['first_name'] = $user_info['first_name'];
            $info['last_name'] = $user_info['last_name'];
            $info['email'] = $user_info['email'];
            $info['email_hash'] = $user_info['email_hash'];
            $properties['user'] = $info;
        }
        catch (\Flexio\Base\Exception $e)
        {
        }

        // return the properties
        return $properties;
    }
}
