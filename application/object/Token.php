<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-10-24
 *
 * @package flexio
 * @subpackage Object
 */


namespace Flexio\Object;


class Token extends \Flexio\Object\Base
{
    public function __construct()
    {
        $this->setType(\Model::TYPE_TOKEN);
    }

    public static function create(array $properties = null) : \Flexio\Object\Token
    {
        // the user_eid needs to be set and be a valid user
        if (!isset($properties['user_eid']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        $user_eid = $properties['user_eid'];
        $user = \Flexio\Object\User::load($user_eid);
        if ($user === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        // generate an access code and a secret code
        $properties['access_code'] = \Flexio\Base\Util::generateHandle();
        $properties['secret_code'] = \Flexio\Base\Util::generateHandle();

        $object = new static();
        $model = \Flexio\Object\Store::getModel();
        $local_eid = $model->create($object->getType(), $properties);

        $object->setModel($model);
        $object->setEid($local_eid);
        $object->clearCache();
        return $object;
    }

    public function set(array $properties) : \Flexio\Object\Token
    {
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
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
            "eid_type" : "'.\Model::TYPE_TOKEN.'",
            "eid_status" : null,
            "user_eid": null,
            "access_code" : null,
            "secret_code" : null,
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
