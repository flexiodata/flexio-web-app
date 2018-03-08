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
        $this->setType(\Model::TYPE_RIGHT);
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
        $user = \Flexio\Object\User::load($properties['access_code']);
        if ($user !== false)
        {
            $user_info = $user->get();

            if ($user_info !== false)
            {
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
        }

        // return the properties
        return $properties;
    }
}
