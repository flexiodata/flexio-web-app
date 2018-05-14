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
    public static function create(\Flexio\Api\Request $request) : void
    {
        // TODO: the behavior of this API endpoint isn't yet finalized;
        // throw an exception until it's ready
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);


        // TODO: access rights are being granted to the user specified
        // in 'access_code' in the rights object; however, we're posting
        // to a path that has an owner in the root; does it make sense
        // to use the owner as the owner we're adding the rights for?
        // what about public pipes?

        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'rights'  => array('type' => 'object', 'required' => true),
                'message' => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_post_params = $validator->getParams();
        $rights = $validated_post_params['rights'];
        $message = $validated_post_params['message'] ?? '';

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
            if ($object->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
            if ($object->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE_RIGHTS) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

            // right now, the access_code should be either a user eid, email address,
            // or the 'public' category; load the user
            $object_rights_to_add = array();
            if ($access_code === \Flexio\Object\User::MEMBER_PUBLIC)
            {
                $object_rights_to_add[] = array(
                    'object' => $object,
                    'access_code' => \Flexio\Object\User::MEMBER_PUBLIC,
                    'access_type' => \Model::ACCESS_CODE_TYPE_CATEGORY,
                    'actions' => $actions
                );
            }
             else
            {
                // see if the access code is a valid user; if not, invite the user
                $user = false;
                try
                {
                    $user = \Flexio\Object\User::load($access_code);
                    if ($user->getStatus() === \Model::STATUS_DELETED)
                        throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
                }
                catch (\Flexio\Base\Exception $e)
                {
                }

                if ($user === false)
                {
                    // create a user
                    $user = self::createUser($access_code, $requesting_user_eid); // user doesn't exist; invite them
                    \Flexio\System\System::getModel()->assoc_add($requesting_user_eid, \Model::EDGE_INVITED, $user->getEid());
                    \Flexio\System\System::getModel()->assoc_add($user->getEid(), \Model::EDGE_INVITED_BY, $requesting_user_eid);
                }

                if ($user !== false)
                {
                    $object_rights_to_add[] = array(
                        'object' => $object,
                        'user' => $user,
                        'access_code' => $user->getEid(),
                        'access_type' => \Model::ACCESS_CODE_TYPE_EID,
                        'actions' => $actions
                    );

                    // regardless of whether or not they're a new user, add a sharing association
                    \Flexio\System\System::getModel()->assoc_add($requesting_user_eid, \Model::EDGE_SHARED_WITH, $user->getEid());
                    \Flexio\System\System::getModel()->assoc_add($user->getEid(), \Model::EDGE_SHARED_FROM, $requesting_user_eid);

                    // TODO: only send an email invite once if a user happens to be listed multiple time in the list;
                    // only send invites if a user isn't already in the list

                    self::sendInviteEmail($user->getEid(), $requesting_user_eid, $object->getEid(), $message);
                }
            }
        }

        // add the rights after we've validated all the parameters
        $object_eids_with_rights_added = array();
        foreach ($object_rights_to_add as $o)
        {
            $object = $o['object'];
            $access_code = $o['access_code'];
            $access_type = $o['access_type'];
            $actions = $o['actions'];

            $object->grant($access_code, $access_type, $actions);

            $object_eid = $object->getEid();
            $object_eids_with_rights_added[$object_eid] = $object;
        }

        // sync up the followers for each of the objects with rights affected;
        // return the objects with the effected rights
        $result = array();
        foreach ($object_eids_with_rights_added as $object_eid => $object)
        {
            $result[] = $object->getRights();
        }

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function set(\Flexio\Api\Request $request) : void
    {
        // TODO: the behavior of this API endpoint isn't yet finalized;
        // throw an exception until it's ready
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);


        // TODO: access rights are being granted to the user specified
        // in 'access_code' in the rights object; however, we're posting
        // to a path that has an owner in the root; does this make sense?

        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $right_eid = $request->getObjectFromUrl();

        // note: only allow the actions to be changed; don't allow the object
        // or the user to be changed since this would allow a user to simply
        // change the object and/or user to give themselves rights to something
        // else

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'actions' => array('type' => 'object', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_post_params = $validator->getParams();
        $actions = $validated_post_params['actions'];

        // make sure we're allowed to modify the rights
        $right = \Flexio\Object\Right::load($right_eid);
        if ($right->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        $right_info = $right->get();
        $object_eid = $right_info['object_eid'];

        $object = \Flexio\Object\Store::load($object_eid);
        if ($object->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($object->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE_RIGHTS) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $new_rights = array(array('eid' => $right_eid, 'actions' => $actions));
        $object->setRights($new_rights);

        $result = $right->get();
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function delete(\Flexio\Api\Request $request) : void
    {
        // TODO: the behavior of this API endpoint isn't yet finalized;
        // throw an exception until it's ready
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);


        // TODO: access rights are being granted to the user specified
        // in 'access_code' in the rights object; however, we're posting
        // to a path that has an owner in the root; does this make sense?

        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $right_eid = $request->getObjectFromUrl();

        // make sure we're allowed to modify the rights
        $right = \Flexio\Object\Right::load($right_eid);
        if ($right->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        $right_info = $right->get();
        $object_eid = $right_info['object_eid'];

        $object = \Flexio\Object\Store::load($object_eid);
        if ($object->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($object->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE_RIGHTS) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // delete the right
        $right->delete();

        $result = $right->get();
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function get(\Flexio\Api\Request $request) : void
    {
        // TODO: the behavior of this API endpoint isn't yet finalized;
        // throw an exception until it's ready
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);


        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $right_eid = $request->getObjectFromUrl();

        // make sure we're allowed to modify the rights
        $right = \Flexio\Object\Right::load($right_eid);
        if ($right->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        $right_info = $right->get();
        $object_eid = $right_info['object_eid'];

        $object = \Flexio\Object\Store::load($object_eid);
        if ($object->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($object->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE_RIGHTS) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $result = $right->get();
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function list(\Flexio\Api\Request $request) : void
    {
        // TODO: the behavior of this API endpoint isn't yet finalized;
        // throw an exception until it's ready
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);


        $query_params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($query_params, array(
                'objects'     => array('type' => 'string',  'required' => false, 'array' => true),
                'start'       => array('type' => 'integer', 'required' => false),
                'tail'        => array('type' => 'integer', 'required' => false),
                'limit'       => array('type' => 'integer', 'required' => false),
                'created_min' => array('type' => 'date',    'required' => false),
                'created_max' => array('type' => 'date',    'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_query_params = $validator->getParams();

        // TODO: get rights info from list query in \Flexio\Object\Rights

        $result = array();
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    private static function createUser(string $identifier, string $requesting_user_eid) : \Flexio\Object\User
    {
        // user doesn't exist; create a user
        $user_email = $identifier;
        $username = \Flexio\Base\Identifier::generate(); // default username
        $password = \Flexio\Base\Password::generate();
        $verify_code = \Flexio\Base\Util::generateHandle(); // code to verify user's email address

        $new_user_info = array('username' => $username,
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
        $new_user_request = \Flexio\Api\Request::create();
        $new_user_request->setRequestingUser($requesting_user_eid);
        $new_user_request->setPostParams($new_user_info);

        $user_info = \Flexio\Api\User::create($new_user_request);
        if (!isset($user_info) || $user_info === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        $user_eid = $user_info['eid'];
        return \Flexio\Object\User::load($user_eid);
    }

    private static function sendInviteEmail(string $invited_user_eid, string $requesting_user_eid, string $object_eid, string $message) : bool
    {
        $invited_user = false;
        $requesting_user = false;
        $object = false;

        try
        {
            $invited_user = \Flexio\Object\User::load($invited_user_eid);
            $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
            $object = \Flexio\Object\Store::load($object_eid);

            if ($invited_user->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
            if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
            if ($object->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        }
        catch (\Flexio\Base\Exception $e)
        {
            return false;
        }

        $invited_user_info = $invited_user->get();
        $to_email = $invited_user_info['email'];

        $requesting_user_info = $requesting_user->get();
        $first_name = $requesting_user_info['first_name'];
        $last_name = $requesting_user_info['last_name'];
        $from_name = $first_name . (strlen($last_name) > 0 ? (' ' . $last_name) : '');
        $from_email = $requesting_user_info['email'];

        $object_info = $object->get();
        $object_eid = $object_info['eid'];
        $object_name = isset($object_info['name']) ? $object_info['name'] : '';

        // send out the invite
        $email_params = array();

        // if the user hasn't been verified yet (either just created or previously created
        // but not verified), then add the verification code
        if ($invited_user_info['eid_status'] == \Model::STATUS_PENDING)
            $email_params['verify_code'] = $invited_user_info['verify_code'];

        // get the full name of the sender
        $email_params['email'] = $to_email;
        $email_params['from_name'] = $from_name;
        $email_params['from_email'] = $from_email;
        $email_params['object_name'] = $object_name;
        $email_params['object_eid'] = $object_eid;
        $email_params['message'] = $message;

        $email = \Flexio\Api\Message::create(\Flexio\Api\Message::TYPE_EMAIL_SHARE_PIPE, $email_params);
        $email->send();

        return true;
    }
}
