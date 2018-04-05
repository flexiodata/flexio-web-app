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
        $object = new static();
        $token_model = $object->getModel()->token;
        $items = $token_model->list($filter);

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

    public static function load(string $eid) : \Flexio\Object\Token
    {
        $object = new static();
        $token_model = $object->getModel()->token;

        $properties = $token_model->get($eid);
        if ($properties === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        $object->setEid($eid);
        $object->clearCache();
        $object->properties = self::formatProperties($properties);
        return $object;
    }

    public static function create(array $properties = null) : \Flexio\Object\Token
    {
        // the owned_by needs to be set and be a valid user
        if (!isset($properties['owned_by']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        $owned_by = $properties['owned_by'];
        $user = \Flexio\Object\User::load($owned_by);

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
        // TODO: add owned_by to list of properties and use those here?
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
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['eid_status'];
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
        $token_model = $this->getModel()->token;
        $local_properties = $token_model->get($this->getEid());
        $this->properties = self::formatProperties($local_properties);
        return true;
    }

    private static function formatProperties(array $properties) : array
    {
        $mapped_properties = \Flexio\Base\Util::mapArray(
            [
                "eid" => null,
                "eid_type" => null,
                "eid_status" => null,
                "user_eid" => null, // TODO: this is legacy for API consistency; remove when UI is updated to use owned_by
                "access_code" => null,
                "owned_by" => null,
                "created" => null,
                "updated" => null
            ],
        $properties);

        // sanity check: if the data record is missing, then eid will be null
        if (!isset($mapped_properties['eid']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // TODO: this is legacy for API consistency; remove when UI is updated to use owned_by
        $mapped_properties['user_eid'] = $mapped_properties['owned_by'];

        return $mapped_properties;
    }
}
