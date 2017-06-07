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
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class Right
{
    public static function create(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // load the object and check the rights; ability to create/set rights determined by user rights
        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        if ($user->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_WRITE_RIGHTS) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // TODO: add the right

        return array();
    }

    public static function delete(array $params, string $requesting_user_eid = null) : bool
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $right_identifier = $params['eid'];

         // load the object and check the rights; ability to create/set rights determined by user rights
        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        if ($user->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_WRITE_RIGHTS) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // TODO: delete the right

        return true;
    }

    public static function get(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $right_identifier = $params['eid'];


        // get all the rights that a user has access to
        $objects = self::getObjectsForUser($requesting_user_eid);

        // if the rights are in the list, return the right info for that object;
        // otherwise the user doesn't have access



        return array();
    }

    public static function listall(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'objects' => array('type' => 'string', 'array' => true, 'required' => false),
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $object_filter_list = $params['objects'] ?? false;
        $objects = self::getObjectsForUser($requesting_user_eid);

        // if no object list is specified, return everything
        if ($object_filter_list === false)
            return $objects;

        // if an object list is specified, return the subset of matching objects
        if ($object_filter_list !== false)
        {
            $result = array();
            $object_filter_list = array_flip($object_filter_list);
            foreach ($objects as $o)
            {
                if (array_key_exists($o['object_eid'], $object_filter_list))
                    $result[] = $o;
            }

            return $result;
        }
    }

    private static function getObjectsForUser($user_eid)
    {
        // load the user and check the rights; TODO: should we only show
        // rights that the user has access to see?
        $user = \Flexio\Object\User::load($user_eid);
        if ($user === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        if ($user->allows($user_eid, \Flexio\Object\Action::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the rights
        $result = array();
        $rights = $user->getRightList();
        foreach ($rights as $r)
        {
            $result[] = $r->get();
        }

        return $result;
    }
}
