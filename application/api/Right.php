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
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        return self::getObjectsForUser($requesting_user_eid);
    }

    private static function getObjectsForUser($user_eid)
    {
        // find all objects owned or followed by the user
        $assoc_filter = array('eid_status' => \Model::STATUS_AVAILABLE);
        $objects_owned = $this->getModel()->assoc_range($user_eid, \Model::EDGE_OWNS, $assoc_filter);
        $objects_followed = $this->getModel()->assoc_range($user_eid, \Model::EDGE_FOLLOWING, $assoc_filter);
        $objects = array_merge($objects_owned, $objects_followed);

        $res = array();
        foreach ($objects as $object_info)
        {
            $object_eid = $object_info['eid'];
            $object = \Flexio\Object\Store::load($object_eid);
            if ($object === false)
                continue;

            // TODO: right now, report all rights for an owner or a follower;
            // when rights move over to listing specific rights per user,
            // only the rights associated with the user should be viewed,
            // and these should be granted when the object is shared

            $object_subset = array();
            $object_subset['eid'] = $object->getEid();
            $object_subset['eid_type'] = $object->getType();
            $object_rights = $object->getRights();

            $res[] = $object;
        }

        return $res;
    }
}
