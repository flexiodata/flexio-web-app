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


declare(strict_types=1);
namespace Flexio\Object;


class Token extends \Flexio\Object\Base implements \Flexio\IFace\IObject
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

        // generate an access code
        $properties['access_code'] = \Flexio\Base\Util::generateHandle();

        $object = new static();
        $token_model = $object->getModel()->token;
        $local_eid = $token_model->create($properties);

        $object->setEid($local_eid);
        $object->clearCache();
        return $object;
    }

    public function delete() : \Flexio\Object\Token
    {
        $this->clearCache();
        $token_model = $this->getModel()->token;
        $token_model->delete($this->getEid());
        return $this;
    }

    public function set(array $properties) : \Flexio\Object\Token
    {
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
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
            "eid_type" : "'.\Model::TYPE_TOKEN.'",
            "eid_status" : null,
            "user_eid": null,
            "access_code" : null,
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
