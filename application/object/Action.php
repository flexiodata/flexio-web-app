<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-04-17
 *
 * @package flexio
 * @subpackage Object
 */


declare(strict_types=1);
namespace Flexio\Object;


class Action extends \Flexio\Object\Base implements \Flexio\IFace\IObject
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
        $action_model = $object->getModel()->action;
        $items = $action_model->list($filter);

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

    public static function load(string $eid) : \Flexio\Object\Action
    {
        $object = new static();
        $action_model = $object->getModel()->action;

        $properties = $action_model->get($eid);
        if ($properties === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        $object->setEid($eid);
        $object->clearCache();
        $object->properties = self::formatProperties($properties);
        return $object;
    }

    public static function create(array $properties = null) : \Flexio\Object\Action
    {
        // if the params are set, make sure it's an object and then encode it as JSON for storage
        if (isset($properties) && isset($properties['request_params']))
        {
            if (!is_array($properties['request_params']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

            $properties['request_params'] = json_encode($properties['request_params']);
        }
        if (isset($properties) && isset($properties['response_params']))
        {
            if (!is_array($properties['response_params']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

            $properties['response_params'] = json_encode($properties['response_params']);
        }

        $object = new static();
        $action_model = $object->getModel()->action;
        $local_eid = $action_model->create($properties);

        $object->setEid($local_eid);
        $object->clearCache();
        return $object;
    }

    public function delete() : \Flexio\Object\Action
    {
        $this->clearCache();
        $action_model = $this->getModel()->action;
        $action_model->delete($this->getEid());
        return $this;
    }

    public function set(array $properties) : \Flexio\Object\Action
    {
        // TODO: add properties check

        // if the params are set, make sure it's an object and then encode it as JSON for storage
        if (isset($properties) && isset($properties['request_params']))
        {
            if (!is_array($properties['request_params']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

            $properties['request_params'] = json_encode($properties['request_params']);
        }
        if (isset($properties) && isset($properties['response_params']))
        {
            if (!is_array($properties['response_params']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

            $properties['response_params'] = json_encode($properties['response_params']);
        }

        $this->clearCache();
        $action_model = $this->getModel()->action;
        $action_model->set($this->getEid(), $properties);
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
        return \Model::TYPE_ACTION;
    }

    public function setOwner(string $user_eid) : \Flexio\Object\Action
    {
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function getOwner() : string
    {
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function setStatus(string $status) : \Flexio\Object\Action
    {
        $this->clearCache();
        $action_model = $this->getModel()->action;
        $result = $action_model->setStatus($this->getEid(), $status);
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
        $action_model = $this->getModel()->action;
        $local_properties = $action_model->get($this->getEid());
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
                "action_type" => null,
                "request_ip" => null,
                "request_user_agent" => null,
                "request_type" => null,
                "request_method" => null,
                "request_route" => null,
                "request_created_by" => null,
                "request_created" => null,
                "request_access_code" => null,
                "request_params" => null,
                "target_eid" => null,
                "target_eid_type" => null,
                "target_owned_by" => null,
                "response_type" => null,
                "response_code" => null,
                "response_params" => null,
                "response_created" => null,
                "request_created" => null,
                "duration" => null,
                "created" => null,
                "updated" => null
            ],
        $properties);

        // sanity check: if the data record is missing, then eid will be null
        if (!isset($mapped_properties['eid']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // unpack the request params
        if (isset($mapped_properties['request_params']))
        {
            $request_params = @json_decode($mapped_properties['request_params'],true);
            if ($request_params !== false)
            {
                $mapped_properties['request_params'] = $request_params;
                $mapped_properties['request_params'] = $mapped_properties['request_params']; // TODO: fix empty params
            }
        }

        // unpack the response params
        if (isset($mapped_properties['response_params']))
        {
            $response_params = @json_decode($mapped_properties['response_params'],true);
            if ($response_params !== false)
            {
                $mapped_properties['response_params'] = $response_params;
                $mapped_properties['response_params'] = $mapped_properties['response_params']; // TODO: fix empty params
            }
        }

        return $mapped_properties;
    }
}
