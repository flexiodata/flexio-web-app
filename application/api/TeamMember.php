<?php
/**
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-07-16
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class TeamMember
{
    public static function create(\Flexio\Api\Request $request) : void
    {
        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        $request->track(\Flexio\Api\Action::TYPE_TEAMMEMBER_ADD);

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'member' => array('type' => 'string', 'required' => true),
                'rights' => array('type' => 'object', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();

        // check the rights on the owner; ability to add a member is governed
        // currently by user write privileges
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the team member user eid to add; logic is as follows:
        // 1. member param is an existing user eid; invite the member
        // 2. member param is an existing username; invite the member
        // 3. member param is an existing email; invite the member
        // 4. member param is a valid email address; create a placeholder user and invite the member
        // 5. member param is something else; fail
        $member_user_eid = self::getMemberEidFromParam($member_param);
        if ($member_user_eid === false)
        {
            // user doesn't exist based on anything supplied; see if we have an email address
            // and if so invite the user in; otherwise fail
            if (\Flexio\Base\Email::isValid($member_param) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

            // TODO: add the user
        }

        // if for whatever reason, we still don't have a valid member, throw an exception
        if ($member_user_eid === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

        // create the object
        $member_properties = array();
        $member_properties['member_eid'] = $member_user_eid;
        $member_properties['rights'] = $validated_post_params['rights'] ?? array();
        $member_properties['owned_by'] = $owner_user_eid;
        $member_properties['created_by'] = $requesting_user_eid;
        \Flexio\System\System::getModel()->teammember->create($member_properties);

        // get the result of creating
        $result = \Flexio\System\System::getModel()->teammember->get($owner_user_eid, $member_user_eid);

        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    public static function delete(\Flexio\Api\Request $request) : void
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $member_user_eid = $request->getObjectFromUrl();

        $request->track(\Flexio\Api\Action::TYPE_TEAMMEMBER_REMOVE);

        // check the rights on the owner; ability to remove a member is governed
        // currently by user write privileges
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // remove the team member
        \Flexio\System\System::getModel()->teammember->delete($member_user_eid, $owner_user_eid);

        // TODO: get the result of deleting
        $result = array();
        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    public static function set(\Flexio\Api\Request $request) : void
    {
        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $member_user_eid = $request->getObjectFromUrl();

        $request->track(\Flexio\Api\Action::TYPE_TEAMMEMBER_UPDATE);
        $request->setRequestParams($post_params);

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'member_status' => array('type' => 'string', 'required' => false),
                'rights'        => array('type' => 'object', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();

        // check the rights on the owner; ability to update a member is governed
        // currently by user write privileges
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // update the team member
        \Flexio\System\System::getModel()->teammember->set($member_user_eid, $owner_user_eid, $validated_post_params);

        // TODO: get the result of updating
        $result = array();

        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    public static function get(\Flexio\Api\Request $request) : void
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $member_user_eid = $request->getObjectFromUrl();

        // check the rights on the owner; ability to get a member is governed
        // currently by user read privileges
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the team member rights, etc; unpack the rights, which are stored as a json string;
        // TODO: unpack the user info
        $result = \Flexio\System\System::getModel()->teammember->get($member_user_eid, $owner_user_eid);
        $result['rights'] = @json_decode($result['rights'], true);

        // return the result
        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    public static function list(\Flexio\Api\Request $request) : void
    {
        $query_params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($query_params, array(
                'start'       => array('type' => 'integer', 'required' => false),
                'tail'        => array('type' => 'integer', 'required' => false),
                'limit'       => array('type' => 'integer', 'required' => false),
                'created_min' => array('type' => 'date',    'required' => false),
                'created_max' => array('type' => 'date',    'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_query_params = $validator->getParams();

        // check the rights on the owner; ability to get a member is governed
        // currently by user read privileges
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the team members
        $result = array();

        $filter = array('owned_by' => $owner_user_eid, 'eid_status' => \Model::STATUS_AVAILABLE);
        $filter = array_merge($validated_query_params, $filter); // give precedence to fixed owner/status
        $teammembers = \Flexio\System\System::getModel()->teammember->list($filter);

        foreach ($teammembers as $t)
        {
            // TODO: check permissions for each item?

            $item = $t;
            $item['rights'] = @json_decode($item['rights'], true);
            $result[] = $item;
        }

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    private static function getMemberEidFromParam(string $param) // TODO: add return type
    {
        if (\Flexio\System\System::getModel()->user->exists($param))
            return $param; // param is an eid

        $eid = \Flexio\System\System::getModel()->user->getEidFromIdentifier($member_param);
        if ($eid !== false)
            return $eid; // param is a username or email; return eid from info

        return false;
    }
}
