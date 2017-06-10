<?php
/**
 *
 * Copyright (c) 2013, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2013-10-30
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class Trash
{
    public static function add(array $params, string $requesting_user_eid = null) : bool
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'items' => array('type' => 'object', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $objects = self::filterEidItems($params['items']);
        if ($objects === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // check the rights for each object; make sure all of them have
        // delete privileges
        foreach ($objects as $eid)
        {
            // load the object
            $object = \Flexio\Object\Store::load($eid);
            if ($object === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

            // check the rights on the object
            if ($object->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_DELETE) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);
        }

        // loop through the objects and try to add them to the trash
        foreach ($objects as $eid)
        {
            $obj = \Flexio\Object\Store::load($eid);
            if ($obj === false)
                continue;

            if ($obj->getStatus() !== \Model::STATUS_AVAILABLE)
                continue;

            // if the item is a connection, delete it straight away;
            // if it's another object, send it to the trash so it can
            // can purged later or recovered
            if ($obj->getType() === \Model::TYPE_CONNECTION)
                $obj->setStatus(\Model::STATUS_DELETED);
                 else
                $obj->setStatus(\Model::STATUS_TRASH);
        }

        return true;
    }


    public static function restore(array $params, string $requesting_user_eid = null) : bool
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'items' => array('type' => 'object', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $objects = self::filterEidItems($params['items']);
        if ($objects === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // check the rights for each object; make sure all of them have
        // delete privileges (same privileges as what allowed them to be
        // put in trash so it can reversed)
        foreach ($objects as $eid)
        {
            // load the object
            $object = \Flexio\Object\Store::load($eid);
            if ($object === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

            // check the rights on the object
            if ($object->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_DELETE) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);
        }

        // loop through the objects and try to restore them from the trash
        foreach ($objects as $eid)
        {
            $obj = \Flexio\Object\Store::load($eid);
            if ($obj === false)
                continue;

            if ($obj->getStatus() !== \Model::STATUS_TRASH)
                continue;

            $obj->setStatus(\Model::STATUS_AVAILABLE);
        }

        return true;
    }

    public static function empty(array $params, string $requesting_user_eid = null) : bool
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'items' => array('type' => 'object', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $objects = self::filterEidItems($params['items']);
        if ($objects === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // check the rights for each object; make sure all of them have
        // delete privileges
        foreach ($objects as $eid)
        {
            // load the object
            $object = \Flexio\Object\Store::load($eid);
            if ($object === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

            // check the rights on the object
            if ($object->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_DELETE) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);
        }

        // loop through the objects and delete them from the trash
        foreach ($objects as $eid)
        {
            $obj = \Flexio\Object\Store::load($eid);
            if ($obj === false)
                continue;

            if ($obj->getStatus() !== \Model::STATUS_TRASH)
                continue;

            $obj->setStatus(\Model::STATUS_DELETED);
        }

        return true;
    }

    public static function listall(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // load the object
        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // get the pipes
        $filter = array('eid_type' => array(\Model::TYPE_PIPE), 'eid_status' => array(\Model::STATUS_TRASH));
        $pipes = $user->getObjects($filter);

        $result = array();
        foreach ($pipes as $p)
        {
            if ($p->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_READ) === false)
                continue;

            $result[] = $p->get();
        }

        return $result;
    }

    private static function filterEidItems(array $items) : array
    {
        $filtered_items = array();
        foreach ($items as $i)
        {
            // if we don't have a string, fail
            if (!is_string($i))
                return false;

            // if we have a string, but it isn't an eid, just ignore it
            if (!\Flexio\Base\Eid::isValid($i))
                continue;

            $filtered_items[] = $i;
        }

        return $filtered_items;
    }
}
