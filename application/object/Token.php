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
            $o->properties =self::formatProperties($i);
            $o->setEid($o->properties['eid']);
            $objects[] = $o;
        }

        return $objects;
    }

    public static function load(string $eid) : \Flexio\Object\Token
    {
        $object = new static();
        $token_model = $object->getModel()->token;
        $properties = $token_model->get($eid);

        $object->setEid($eid);
        $object->clearCache();
        $object->properties = self::formatProperties($properties);
        return $object;
    }

    public static function loadFromAccessCode(string $code) : \Flexio\Object\Token
    {
        $object = new static();

        $token_model = $object->getModel()->token;
        $properties = $token_model->getFromAccessCode($code);

        $object->setEid($properties['eid']);
        $object->clearCache();
        $object->properties = self::formatProperties($properties);

        return $object;
    }

    public static function create(array $properties = null) : \Flexio\Object\Token
    {
        if (!isset($properties))
            $properties = array();

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
        // TODO: add properties check

        $this->clearCache();
        $token_model = $this->getModel()->token;
        $token_model->set($this->getEid(), $properties);
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

    public function setOwner(string $user_eid) : \Flexio\Object\Token
    {
        $properties = array('owned_by' => $user_eid);
        $this->set($properties);
        return $this;
    }

    public function getOwner() : string
    {
        // TODO: owner info isn't unpacked; should make owned-by presentation consistent
        $token_model = $this->getModel()->token;
        return $token_model->get($this->getEid())['owned_by'];
    }

    public function setStatus(string $status) : \Flexio\Object\Token
    {
        if ($status === \Model::STATUS_DELETED)
            return $this->delete();

        $this->clearCache();
        $token_model = $this->getModel()->token;
        $result = $token_model->set($this->getEid(), array('eid_status' => $status));
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
        if (!$this->properties)
            return false;

        return true;
    }

    private function clearCache() : void
    {
        $this->properties = null;
    }

    private function populateCache() : void
    {
        $token_model = $this->getModel()->token;
        $properties = $token_model->get($this->getEid());
        $this->properties = self::formatProperties($properties);
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
