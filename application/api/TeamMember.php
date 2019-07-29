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

        $request->track(\Flexio\Api\Action::TYPE_TEAMMEMBER_CREATE);

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'member' => array('type' => 'string', 'required' => true),
                'member_status' => array('type' => 'string', 'required' => false,
                                         'default' => \Model::TEAM_MEMBER_STATUS_PENDING,
                                         'enum' => [\Model::TEAM_MEMBER_STATUS_PENDING, \Model::TEAM_MEMBER_STATUS_ACTIVE]),
                'rights' => array('type' => 'object', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();

        // rights are stored as a json string
        $rights = '[]';
        if (isset($validated_post_params) && isset($validated_post_params['rights']))
            $rights = json_encode($validated_post_params['rights']);

        // check the rights on the owner
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_TEAMMEMBER_CREATE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the team member user eid to add; logic is as follows:
        // 1. member param is an existing user eid; invite the member
        // 2. member param is an existing username; invite the member
        // 3. member param is an existing email; invite the member
        // 4. member param is a valid email address; create a placeholder user and invite the member
        // 5. member param is something else; fail
        $member_param = $validated_post_params['member'];
        $member_user_eid = self::getMemberEidFromParam($member_param);

        if ($member_user_eid === false)
        {
            // user doesn't exist based on anything supplied; see if we have an email address
            // and if so invite the user in; otherwise fail
            if (\Flexio\Base\Email::isValid($member_param) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

            // member param is the email
            $email = $member_param;

            // note: following logic is paralleled when creating a new user
            // in the user api implementation, except for the initial user
            // parameters which are minimal here

            // create the user
            $new_user_info = array();
            $new_user_info['email'] = $email;
            $new_user_info['eid_status'] = (\Flexio\Api\User::REQUIRE_VERIFICATION === true ? \Model::STATUS_PENDING : \Model::STATUS_AVAILABLE);
            $new_user_info['verify_code'] = (\Flexio\Api\User::REQUIRE_VERIFICATION === true ? \Flexio\Base\Util::generateHandle() : '');
            $user = \Flexio\Object\User::create($new_user_info);

            // if appropriate, create an a default api token
            if (\Flexio\Api\User::CREATE_DEFAULT_TOKEN === true)
            {
                $token_properties = array();
                $token_properties['owned_by'] = $user->getEid();
                \Flexio\Object\Token::create($token_properties);
            }

            // if appropriate, create default examples
            if (\Flexio\Api\User::CREATE_DEFAULT_EXAMPLES === true)
                \Flexio\Object\Store::createExampleObjects($user->getEid());

            $member_user_eid = $user->getEid();
        }

        // get the requesting user info and member info, which we'll need for
        // sending an email; do this ea
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        $member_user = \Flexio\Object\User::load($member_user_eid);

        // if the team member has already been added for whatever reason,
        // throw an error
        if (self::isTeamMember($member_user_eid, $owner_user_eid))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

        // add the team member
        $member_properties = array();
        $member_properties['member_eid'] = $member_user_eid;
        $member_properties['rights'] = $rights;
        $member_properties['owned_by'] = $owner_user_eid;
        $member_properties['created_by'] = $requesting_user_eid;
        \Flexio\System\System::getModel()->teammember->create($member_properties);

        // if appropriate, send an invite email
        if (\Flexio\Api\User::SEND_INVITE_EMAIL === true)
        {
            $owner_user_info = $owner_user->get();
            $requesting_user_info = $requesting_user->get();
            $member_user_info = $member_user->get();

            $email_params = array(
                'email'       => $member_user_info['email'],
                'verify_code' => $member_user->getVerifyCode(),
                'from_name'   => $requesting_user_info['first_name'],
                'from_email'  => $requesting_user_info['email'],
                'object_name' => $owner_user_info['username']
            );
            \Flexio\Api\Message::sendTeamInvitationEmail($email_params);
        }

        // get the result of creating
        $result = self::getMemberInfo($member_user_eid, $owner_user_eid);
        $result = self::formatProperties($result);

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

        $request->track(\Flexio\Api\Action::TYPE_TEAMMEMBER_DELETE);

        // check the rights on the owner
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_TEAMMEMBER_DELETE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // don't allow users to remove themselves from their own team
        if ($owner_user_eid === $member_user_eid)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // remove the team member
        $result = \Flexio\System\System::getModel()->teammember->delete($member_user_eid, $owner_user_eid);
        if ($result === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // TODO: what should we return? normally we have an object; but in this
        // case we only deleted a relationship
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
                'member_status' => array('type' => 'string', 'required' => false,
                                         'enum' => [\Model::TEAM_MEMBER_STATUS_PENDING, \Model::TEAM_MEMBER_STATUS_ACTIVE]),
                'rights'        => array('type' => 'object', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();

        // rights are stored as a json string
        if (isset($validated_post_params) && isset($validated_post_params['rights']))
            $validated_post_params['rights'] = json_encode($validated_post_params['rights']);

        // check the rights on the owner; ability to update a member is governed
        // currently by user write privileges
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_TEAMMEMBER_UPDATE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // update the team member
        \Flexio\System\System::getModel()->teammember->set($member_user_eid, $owner_user_eid, $validated_post_params);

        // get the team member info
        $result = self::getMemberInfo($member_user_eid, $owner_user_eid);
        $result = self::formatProperties($result);

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
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_TEAMMEMBER_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the team member info
        $result = self::getMemberInfo($member_user_eid, $owner_user_eid);
        $result = self::formatProperties($result);

        // return the result
        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
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
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_TEAMMEMBER_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the team members
        $result = array();

        $filter = array('owned_by' => $owner_user_eid);
        $filter = array_merge($validated_query_params, $filter); // give precedence to fixed owner/status
        $teammembers = \Flexio\System\System::getModel()->teammember->list($filter);

        foreach ($teammembers as $t)
        {
            // TODO: check permissions for each item?
            $result[] = self::formatProperties($t);
        }

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function sendinvitation(\Flexio\Api\Request $request) : void
    {
        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $member_user_eid = $request->getObjectFromUrl();

        $request->track(\Flexio\Api\Action::TYPE_TEAMMEMBER_SENDINVITATION);
        $request->setRequestParams($post_params);

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();

        // check the rights on the owner; ability to send an invitation is governed
        // currently by user write privileges, which are the same rights used
        // when initially inviting/adding the member
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_TEAMMEMBER_SENDINVITATION) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);
        $owner_user_info = $owner_user->get();

        // get the requesting user/member info; as a sanity check, make sure they
        // haven't been deleted; TODO: is this needed for the requesting user?
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        $requesting_user_info = $requesting_user->get();

        $member_user = \Flexio\Object\User::load($member_user_eid);
        if ($member_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        $member_user_info = $member_user->get();

        // send the email
        $email_params = array(
            'email'       => $member_user_info['email'] ?? '',
            'verify_code' => $member_user->getVerifyCode(),
            'from_name'   => $requesting_user_info['first_name'],
            'from_email'  => $requesting_user_info['email'],
            'object_name' => $owner_user_info['username'] ?? ''
        );
        \Flexio\Api\Message::sendTeamInvitationEmail($email_params);

        // send the response; TODO: what should we send?
        $result = array();
        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    public static function processjoin(\Flexio\Api\Request $request) : void
    {
        // special call for joining a team that doesn't require permissions
        // because it's a simple acceptance and allows a user to join a team
        // without having to be logged in as a particular user
        $owner_user_eid = $request->getOwnerFromUrl();
        $post_params = $request->getPostParams();

        $request->track(\Flexio\Api\Action::TYPE_TEAMMEMBER_UPDATE);
        $request->setRequestParams($post_params);

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'member' => array('type' => 'email', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();
        $email = $validated_post_params['member'];

        // make sure the owner exists
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // get the eid for the user joining; if the user is silent, fail silently so as
        // not reveal information about the team to the public caller
        $member_user_eid = \Flexio\Object\User::getEidFromEmail($email);
        if ($member_user_eid !== false)
        {
            // update the team member
            $updated_member_info = array();
            $updated_member_info['member_status'] = \Model::TEAM_MEMBER_STATUS_ACTIVE;
            \Flexio\System\System::getModel()->teammember->set($member_user_eid, $owner_user_eid, $updated_member_info);
        }

        // public call, so don't return any info about the member joining
        $result = array();
        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    private static function getMemberEidFromParam(string $param) // TODO: add return type
    {
        if (\Flexio\System\System::getModel()->user->exists($param))
            return $param; // param is an eid

        $eid = \Flexio\System\System::getModel()->user->getEidFromIdentifier($param);
        if ($eid !== false)
            return $eid; // param is a username or email; return eid from info

        return false;
    }

    private static function getMemberInfo(string $member_user_eid, string $owner_user_eid) : ?array
    {
        $result = \Flexio\System\System::getModel()->teammember->get($member_user_eid, $owner_user_eid);
        return $result;
    }

    private static function isTeamMember(string $member_user_eid, string $owner_user_eid) : bool
    {
        try
        {
            $result = self::getMemberInfo($member_user_eid, $owner_user_eid);
            if ($result)
                return true;
        }
        catch (\Exception $e)
        {
            // fall through
        }

        return false;
    }

    private static function formatProperties(array $properties) : array
    {
        // sanity check: if the data record is missing, then owned_by (eid is
        // normally used) will be null
        if (!isset($properties['owned_by']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // get the user info for the member
        $user_info = array();
        try
        {
            $user_info = \Flexio\Object\User::load($properties['member_eid'])->get();
        }
        catch (\Exception $e)
        {
        }

        $rights = $properties['rights'] ?? '[]';
        $rights = @json_decode($rights, true);
        if ($rights === false)
            $rights = array();

        // return the member info
        $member_properties = array();
        $member_properties['eid'] = $properties['member_eid'] ?? '';
        $member_properties['eid_type'] = \Model::TYPE_USER;
        $member_properties['eid_status'] = $user_info['eid_status'] ?? '';
        $member_properties['username'] = $user_info['username'] ?? '';
        $member_properties['first_name'] = $user_info['first_name'] ?? '';
        $member_properties['last_name'] = $user_info['last_name'] ?? '';
        $member_properties['email'] = $user_info['email'] ?? '';
        $member_properties['email_hash'] = $user_info['email_hash'] ?? '';
        $member_properties['rights'] = $rights;
        $member_properties['member_status'] = $properties['member_status'] ?? '';
        $member_properties['member_of'] = array(
            'eid' => $properties['owned_by'],
            'eid_type' => \Model::TYPE_USER
        );
        $member_properties['invited'] = $properties['created']; // created date of relationship is the date the member was invited
        $member_properties['created'] = $user_info['created'];
        $member_properties['updated'] = $user_info['updated'];

        return $member_properties;
    }
}
