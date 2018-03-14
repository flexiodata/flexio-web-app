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
        $token_model = $object->getModel()->token;
        return $token_model->list($filter);
    }

    public static function load(string $eid) : \Flexio\Object\Token
    {
        $object = new static();
        $token_model = $object->getModel()->token;

        $status = $token_model->getStatus($eid);
        if ($status === \Model::STATUS_UNDEFINED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        $object->setEid($eid);
        $object->clearCache();
        return $object;
    }

    public static function create(array $properties = null) : \Flexio\Object\Token
    {
        // the user_eid needs to be set and be a valid user
        if (!isset($properties['user_eid']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        $user_eid = $properties['user_eid'];
        $user = \Flexio\Object\User::load($user_eid);

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

    public function getType() : string
    {
        return \Model::TYPE_TOKEN;
    }

    public function setOwner(string $user_eid) : \Flexio\Object\Stream
    {
        $properties = array('owned_by' => $user_eid);
        $this->set($properties);
        return $this;
    }

    public function getOwner() : string
    {
        $token_model = $this->getModel()->token;
        return $token_model->getOwner($this->getEid());
    }

    public function setStatus(string $status) : \Flexio\Object\Token
    {
        $this->clearCache();
        $token_model = $this->getModel()->token;
        $result = $token_model->setStatus($this->getEid(), $status);
        return $this;
    }

    public function getStatus() : string
    {
        if ($this->properties !== false && isset($this->properties['eid_status']))
            return $this->properties['eid_status'];

        $token_model = $this->getModel()->token;
        $status = $token_model->getStatus($this->getEid());

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
