<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-12-19
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class Follower
{
    public static function create(array $params, string $requesting_user_eid) : array
    {
        // TODO: this function is a bit outdated in the conventions
        // it uses; should be updated to be more consistent with other
        // api functions
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid'     => array('type' => 'identifier', 'required' => true),
                'users'   => array('type' => 'object', 'required' => true),
                'message' => array('type' => 'string', 'required' => false)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $object_identifier = $params['eid'];
        $users = $params['users'];
        $message = $params['message'] ?? '';

        // make sure the users values are all strings
        foreach ($users as $value)
        {
            if (!is_string($value))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
        }

        // load the object
        $object = \Flexio\Object\Store::load($object_identifier);
        if ($object === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // make sure the object isn't a user
        if ($object->getType() === \Model::TYPE_USER)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // check the rights on the object
        if ($object->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the object properties
        $object_properties = $object->get();

        // the first parameter 'users' is an arrray of strings that are either eids
        // or email addresses; make sure these are strings
        $user_eids = array();
        foreach ($users as $identifier)
        {
            $user_eid = false;
            $user_email = false;

            // try to the load the user
            $user = \Flexio\Object\User::load($identifier);
            if ($user !== false)
            {
                $user_eid = $user->getEid();
                if ($user_eid === $requesting_user_eid) // requesting user should already be a member
                    continue;

                $user_info = $user->get();
                $user_email = $user_info['email'];
            }
             else
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
                                       'create_sample_project' => false,
                                       'require_verification' => true); // require verification to give user a chance to fill out their info

                // if the user isn't invited, create the user; if something went wrong, move on
                $user_info = \Flexio\Api\User::create($new_user_info, $requesting_user_eid);
                if (!isset($user_info) || $user_info === false)
                    continue;

                $user_eid = $user_info['eid'];

                // add an invitation association
                \Flexio\System\System::getModel()->assoc_add($requesting_user_eid, \Model::EDGE_INVITED, $user_eid);
                \Flexio\System\System::getModel()->assoc_add($user_eid, \Model::EDGE_INVITED_BY, $requesting_user_eid);

            }

            // send out the invite
            $email_params = array();

            // if the user hasn't been verified yet (either just created or previously created
            // but not verified), then add the verification code
            if ($user_info['eid_status'] == \Model::STATUS_PENDING)
                $email_params['verify_code'] = $user_info['verify_code'];

            // get the full name of the sender
            $sender_name = \Flexio\System\System::getCurrentUserFirstName();
            $last_name = \Flexio\System\System::getCurrentUserLastName();
            if (strlen($last_name) > 0)
                $sender_name = $sender_name . ' ' . $last_name;

            $message_type = \Flexio\Object\Message::TYPE_EMAIL_SHARE;
            $from_name = $sender_name;
            $from_email = \Flexio\System\System::getCurrentUserEmail();

            $object_name = $object_properties['name'];
            $email_params['email'] = $user_email;
            $email_params['from_name'] = $from_name;
            $email_params['from_email'] = $from_email;
            $email_params['object_name'] = $object_name;
            $email_params['object_eid'] = $object->getEid();
            $email_params['message'] = $message;

            $email = \Flexio\Object\Message::create($message_type, $email_params);
            $email->send();

            // regardless of whether or not they're a new user, add a sharing association
            \Flexio\System\System::getModel()->assoc_add($requesting_user_eid, \Model::EDGE_SHARED_WITH, $user_eid);
            \Flexio\System\System::getModel()->assoc_add($user_eid, \Model::EDGE_SHARED_FROM, $requesting_user_eid);

            // add the users to the list to invite
            $user_eids[] = $user_eid;
        }

        // iterate through each of the users and add permissions for them
        $result = array();
        foreach ($user_eids as $user_eid)
        {
            $user = \Flexio\Object\User::load($user_eid);
            if ($user === false)
                continue;

            $user_info = $user->get();

            \Flexio\System\System::getModel()->assoc_add($object->getEid(), \Model::EDGE_FOLLOWED_BY, $user->getEid());
            \Flexio\System\System::getModel()->assoc_add($user->getEid(), \Model::EDGE_FOLLOWING, $object->getEid());

            // TODO: for now, use the same result structure as the followers
            // call; perhaps consider building this into the basic user info
            $info = array();
            $info['eid'] = $user_info['eid'];
            $info['eid_type'] = $user_info['eid_type'];
            $info['eid_status'] = $user_info['eid_status'];
            $info['user_name'] = $user_info['user_name'];
            $info['user_group'] = 'shared';
            $info['first_name'] = $user_info['first_name'];
            $info['last_name'] = $user_info['last_name'];
            $info['email'] = $user_info['email'];
            $info['email_hash'] = $user_info['email_hash'];
            $info['created'] = $user_info['created'];
            $result[] = $info;
        }

        return $result;
    }

    public static function delete(array $params, string $requesting_user_eid) : bool
    {
        // TODO: make unfollow work with multiple users; same as share
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'parent_eid' => array('type' => 'identifier', 'required' => true),
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $parent_identifier = $params['parent_eid'];
        $user_identifier = $params['eid'];

        // load the object
        $object = \Flexio\Object\Store::load($parent_identifier);
        if ($object === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // load the user
        $user = \Flexio\Object\Store::load($user_identifier);
        if ($user === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // don't allow the owner to be deleted by anybody, including themselves
        if ($object->getOwner() === $user->getEid())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // for all other users, check the rights on the object
        if ($object->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        \Flexio\System\System::getModel()->assoc_delete($object->getEid(), \Model::EDGE_FOLLOWED_BY, $user->getEid());
        \Flexio\System\System::getModel()->assoc_delete($user->getEid(), \Model::EDGE_FOLLOWING, $object->getEid());

        return true;
    }

    public static function listall(array $params, string $requesting_user_eid) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $object_identifier = $params['eid'];

        // load the object
        $object = \Flexio\Object\Store::load($object_identifier);
        if ($object === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($object->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get a list of the user eids associated with this object; this
        // is comprised of the owner, and the users that have had this
        // object shared with them
        $result = array();

        // TODO: for now, use the same result structure as the share
        // call; perhaps consider building this into the basic user info
        $owned_by = \Flexio\System\System::getModel()->assoc_range($object->getEid(), \Model::EDGE_OWNED_BY, [\Model::STATUS_AVAILABLE]);
        foreach ($owned_by as $item)
        {
            $user_eid = $item['eid'];
            $user = \Flexio\Object\User::load($user_eid);
            if ($user === false)
                continue;

            $i = $user->get();
            $info['eid'] = $i['eid'];
            $info['eid_type'] = $i['eid_type'];
            $info['eid_status'] = $i['eid_status'];
            $info['user_name'] = $i['user_name'];
            $info['user_group'] = 'owner';
            $info['first_name'] = $i['first_name'];
            $info['last_name'] = $i['last_name'];
            $info['email'] = $i['email'];
            $info['email_hash'] = $i['email_hash'];
            $info['created'] = $i['created'];
            $result[] = $info;
        }

        $shared_with = \Flexio\System\System::getModel()->assoc_range($object->getEid(), \Model::EDGE_FOLLOWED_BY, [\Model::STATUS_AVAILABLE]);
        foreach ($shared_with as $item)
        {
            $user_eid = $item['eid'];
            $user = \Flexio\Object\User::load($user_eid);
            if ($user === false)
                continue;

            $i = $user->get();
            $info['eid'] = $i['eid'];
            $info['eid_type'] = $i['eid_type'];
            $info['eid_status'] = $i['eid_status'];
            $info['user_name'] = $i['user_name'];
            $info['user_group'] = 'shared';
            $info['first_name'] = $i['first_name'];
            $info['last_name'] = $i['last_name'];
            $info['email'] = $i['email'];
            $info['email_hash'] = $i['email_hash'];
            $info['created'] = $i['created'];
            $result[] = $info;
        }

        return $result;
    }
}
