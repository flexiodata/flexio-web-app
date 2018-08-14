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
    public const TYPE_UNDEFINED        = '';                 // undefined
    public const TYPE_READ             = 'object.read';      // ability to read the properties of an object
    public const TYPE_WRITE            = 'object.write';     // ability to write the properties of an object
    public const TYPE_DELETE           = 'object.delete';    // ability to delete an object
    public const TYPE_EXECUTE          = 'object.execute';   // ability to run a process object
    public const TYPE_READ_RIGHTS      = 'rights.read';      // ability to see rights
    public const TYPE_WRITE_RIGHTS     = 'rights.write';     // ability to change rights

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
        $object = new static();
        $right_model = $object->getModel()->right;
        $items = $right_model->list($filter);

        $objects = array();
        foreach ($items as $i)
        {
            $o = new static();
            $o->properties =self::formatProperties($i);
            $o->setEid($o->properties['eid']);
            $objects[] = $o;
        }

        return $objects;
    }

    public static function load(string $eid) : \Flexio\Object\Right
    {
        $object = new static();
        $right_model = $object->getModel()->right;
        $properties = $right_model->get($eid);

        $object->setEid($eid);
        $object->clearCache();
        $object->properties = self::formatProperties($properties);
        return $object;
    }

    public static function create(array $properties = null) : \Flexio\Object\Right
    {
        if (!isset($properties))
            $properties = array();

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
        // TODO: add owned_by to list of properties and use those here?
        $right_model = $this->getModel()->right;
        return $right_model->getOwner($this->getEid());
    }

    public function setStatus(string $status) : \Flexio\Object\Right
    {
        if ($status === \Model::STATUS_DELETED)
            return $this->delete();

        $this->clearCache();
        $right_model = $this->getModel()->right;
        $result = $right_model->set($this->getEid(), array('eid_status' => $status));
        return $this;
    }

    public function getStatus() : string
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['eid_status'];
    }

    private function isCached() : bool
    {
        // always return the latest rights
        return false;

        // note: following is normal cache behavior
        // if (!$this->properties)
        //    return false;
        //
        // return true;
    }

    private function clearCache() : void
    {
        $this->properties = null;
    }

    private function populateCache() : void
    {
        $right_model = $this->getModel()->right;
        $properties = $right_model->get($this->getEid());
        $this->properties = self::formatProperties($properties);
    }

    private static function formatProperties(array $properties) : array
    {
        $mapped_properties = \Flexio\Base\Util::mapArray(
            [
                "eid" => null,
                "eid_type" => null,
                "eid_status" => null,
                "access_code" => null,
                "access_type" => null,
                "actions" => null,
                "user" => null,
                "object_eid" => null,
                "created" => null,
                "updated" => null
            ],
        $properties);

        // sanity check: if the data record is missing, then eid will be null
        if (!isset($mapped_properties['eid']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // unpack the actions object
        $mapped_properties['actions'] = json_decode($mapped_properties['actions'],true);

        // populate the user info if possible
        try
        {
            $user = \Flexio\Object\User::load($properties['access_code']);
            $user_info = $user->get();
            $info['eid'] = $user_info['eid'];
            $info['eid_type'] = $user_info['eid_type'];
            $info['eid_status'] = $user_info['eid_status'];
            $info['username'] = $user_info['username'];
            $info['first_name'] = $user_info['first_name'];
            $info['last_name'] = $user_info['last_name'];
            $info['email'] = $user_info['email'];
            $info['email_hash'] = $user_info['email_hash'];
            $mapped_properties['user'] = $info;
        }
        catch (\Flexio\Base\Exception $e)
        {
        }

        return $mapped_properties;
    }
}
