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
                'rights' => array('type' => 'object', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $rights = $params['rights'];

        // validate the rights
        $object_rights_to_add = array();
        foreach ($rights as $r)
        {
            $object_eid = $r['object_eid'] ?? false;
            $access_code = $r['access_code'] ?? false;
            $actions = $r['actions'] ?? false;

            if (!is_string($object_eid))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
            if (!is_string($access_code))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
            if (!is_array($actions))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

            // make sure we're allowed to modify the rights
            $object = \Flexio\Object\Store::load($object_eid);
            if ($object === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

            if ($object->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_WRITE_RIGHTS) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

            // right now, the access_code should be either a user eid or an email;
            // load the user
            $user = \Flexio\Object\User::load($access_code);
            if ($user === false)
                $user = $this->inviteUser($access_code); // user doesn't exist; invite them
            $access_code = $user->getEid();

            $object_rights_to_add[] = array(
                'object' => $object,
                'access_code' => $access_code,
                'actions' => $actions
            );
        }

        // add the rights after we've validated all the parameters
        $object_eids_with_rights_added = array();
        foreach ($object_rights_to_add as $o)
        {
            $object = $o['object'];
            $access_code = $o['access_code'];
            $actions = $o['actions'];

            $object->grant($access_code, \Model::ACCESS_CODE_TYPE_EID, $actions);

            $object_eid = $object->getEid();
            $object_eids_with_rights_added[$object_eid] = $object;
        }

        // return the rights for the objects affects
        $result = array();
        foreach ($object_eids_with_rights_added as $object_eid => $object)
        {
            $result[] = $object->getRights();
        }

        return $result;
    }

    public static function set(array $params, string $requesting_user_eid = null) : bool
    {
        // note: only allow the actions to be changed; don't allow the object
        // or the user to be changed since this would allow a user to simply
        // change the object and/or user to give themselves rights to something
        // else

        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true),
                'actions' => array('type' => 'object', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $right_eid = $params['eid'];
        $actions = $params['actions'];

        // make sure we're allowed to modify the rights
        $right = \Flexio\Object\Right::load($right_eid);
        if ($right === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        $right_info = $right->get();
        $object_eid = $right_info['object_eid'];

        $object = \Flexio\Object\Store::load($object_eid);
        if ($object === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        if ($object->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_WRITE_RIGHTS) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $new_rights = array(array('eid' => $right_eid, 'actions' => $actions));
        $object->setRights($new_rights);

        return true;
    }

    public static function delete(array $params, string $requesting_user_eid = null) : bool
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $right_eid = $params['eid'];

        // make sure we're allowed to modify the rights
        $right = \Flexio\Object\Right::load($right_eid);
        if ($right === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        $right_info = $right->get();
        $object_eid = $right_info['object_eid'];

        $object = \Flexio\Object\Store::load($object_eid);
        if ($object === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        if ($object->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_WRITE_RIGHTS) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $right->delete();
        return true;
    }

    public static function get(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $right_eid = $params['eid'];

        // make sure we're allowed to modify the rights
        $right = \Flexio\Object\Right::load($right_eid);
        if ($right === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        $right_info = $right->get();
        $object_eid = $right_info['object_eid'];

        $object = \Flexio\Object\Store::load($object_eid);
        if ($object === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        if ($object->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_WRITE_RIGHTS) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        return $right->get();
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

        //if ($user->allows($user_eid, \Flexio\Object\Action::TYPE_READ) === false)
        //    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the rights
        $result = array();
        $rights = $user->getRightList();
        return $rights;
    }

    private function inviteUser($email) : bool
    {
        // user doesn't exist; create a user
        $user_email = $identifier;
        $username = \Flexio\Base\Util::generateHandle(); // default username
        $password = \Flexio\Base\Util::generateHandle();
        $verify_code = \Flexio\Base\Util::generateHandle(); // code to verify user's email address

        $new_user_info = array('user_name' => $username,
                                'email' => $user_email,
                                'eid_status' => \Model::STATUS_PENDING,
                                'password' => $password,
                                'verify_code' => $verify_code,
                                'first_name' => '',
                                'last_name' => '',
                                'full_name' => '',
                                'send_email' => false,
                                'create_examples' => false,
                                'require_verification' => true); // require verification to give user a chance to fill out their info

        // if the user isn't invited, create the user; if something went wrong, move on
        $user_info = \Flexio\Api\User::create($new_user_info, $requesting_user_eid);
        if (!isset($user_info) || $user_info === false)
            return false;

        $user_eid = $user_info['eid'];

        // add an invitation association
        \Flexio\System\System::getModel()->assoc_add($requesting_user_eid, \Model::EDGE_INVITED, $user_eid);
        \Flexio\System\System::getModel()->assoc_add($user_eid, \Model::EDGE_INVITED_BY, $requesting_user_eid);

        // TODO: send out the invitation

        return true;
    }
}
