<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-12-16
 *
 * @package flexio
 * @subpackage Object
 */


declare(strict_types=1);
namespace Flexio\Object;


class Base
{
    private $model;
    private $eid;

    // properties for derived classes
    protected $properties;

    public function __construct()
    {
    }

    public function setEid(string $eid) : \Flexio\Object\Base
    {
        // only allow the eid to be set once
        if (!is_null($this->eid))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

        $this->eid = $eid;
        return $this;
    }

    public function getEid() : string
    {
        return $this->eid;
    }

    public function allows(string $user_eid, string $action) : bool
    {
        // get the allowed actions for the user in question
        $allowed_actions = $this->rights($user_eid);

        // if the action is in the list, it's allowed; otherwise, it isn't
        $allowed_actions = array_flip($allowed_actions);
        if (array_key_exists($action, $allowed_actions) === true)
            return true;

        return false;
    }

    public function rights(string $user_eid) : array
    {
        // CHECK: the requesting user must be active; if they're not,
        // don't allow them to do anything, unless they're the owner
        // requesting their own account info
        $user_status = $this->getModel()->user->getStatus($user_eid);
        if ($user_status !== \Model::STATUS_AVAILABLE)
        {
            // allow the owner to read their own info, even if they're pending; this case
            // is so that we can query for the user status in the interface for a pending user
            if ($user_eid === $this->getOwner())
            {
                return array(
                    \Flexio\Api\Action::TYPE_USER_READ
                );
            }

            // for everything else, don't allow a pending status
            return array();
        }

        // CHECK: see if the user is the team owner
        if ($user_eid === $this->getOwner())
            return self::getTeamOwnerRights();

        // CHECK: see if the user is a member of the owner team, and if they have
        // actively joined the team; if so, grant them "contributor" rights, which
        // allows them to perform CRUD operation on objects in the team, access the
        // team member list, but not perform other operations on the team or on
        // the owner user
        try
        {
            $member_info = $this->getModel()->teammember->get($user_eid, $this->getOwner());
            $member_status = $member_info['member_status'] ?? false;
            $member_role = $member_info['role'] ?? false;

            // members only have rights if they're active
            if ($member_status !== \Model::TEAM_MEMBER_STATUS_ACTIVE)
                return array();

            // see if the user is a team member with a user role
            if ($member_role === \Model::TEAM_ROLE_USER)
                return self::getTeamUserRights();

            // see if the user is a team member with a contributor role
            if ($member_role === \Model::TEAM_ROLE_CONTRIBUTOR)
                return self::getTeamContributorRights();

            // see if the user is a team member with an administrator role
            if ($member_role === \Model::TEAM_ROLE_ADMINISTRATOR)
                return self::getTeamAdministratorRights();
        }
        catch (\Exception $e)
        {
            // fall through
        }

        // CHECK: see if the user is a system administrator
        if ($this->getModel()->user->isAdministrator($user_eid) === true)
            return self::getSystemAdministratorRights();

        // action not allowed
        return array();
    }

    protected function getModel() : \Model
    {
        return \Flexio\System\System::getModel();
    }

    private static function getTeamUserRights() : array
    {
        return array(

            // don't allow read/write/delete for users
            //\Flexio\Api\Action::TYPE_USER_CREATE,
            //\Flexio\Api\Action::TYPE_USER_UPDATE,
            //\Flexio\Api\Action::TYPE_USER_DELETE,
            ///\Flexio\Api\Action::TYPE_USER_READ,

            // don't allow read/write/delete for user credentials/tokens
            //\Flexio\Api\Action::TYPE_USER_CREDENTIAL_UPDATE,
            //\Flexio\Api\Action::TYPE_USER_CREDENTIAL_RESET,
            //\Flexio\Api\Action::TYPE_USER_AUTHKEY_CREATE,
            //\Flexio\Api\Action::TYPE_USER_AUTHKEY_DELETE,
            //\Flexio\Api\Action::TYPE_USER_AUTHKEY_READ,

            // don't allow read/write/delete for teams
            //\Flexio\Api\Action::TYPE_TEAM_CREATE,
            //\Flexio\Api\Action::TYPE_TEAM_UPDATE,
            //\Flexio\Api\Action::TYPE_TEAM_DELETE,
            //\Flexio\Api\Action::TYPE_TEAM_READ,

            // allow reading of team members, but don't allow any team management
            \Flexio\Api\Action::TYPE_TEAMMEMBER_READ,
            //\Flexio\Api\Action::TYPE_TEAMMEMBER_CREATE,
            //\Flexio\Api\Action::TYPE_TEAMMEMBER_UPDATE,
            //\Flexio\Api\Action::TYPE_TEAMMEMBER_DELETE,
            //\Flexio\Api\Action::TYPE_TEAMMEMBER_READ,
            //\Flexio\Api\Action::TYPE_TEAMMEMBER_SENDINVITATION,

            // allow read-only on pipes
            //\Flexio\Api\Action::TYPE_PIPE_CREATE,
            //\Flexio\Api\Action::TYPE_PIPE_UPDATE,
            //\Flexio\Api\Action::TYPE_PIPE_DELETE,
            \Flexio\Api\Action::TYPE_PIPE_READ,

            // allow read-only on connections
            //\Flexio\Api\Action::TYPE_CONNECTION_CREATE,
            //\Flexio\Api\Action::TYPE_CONNECTION_UPDATE,
            //\Flexio\Api\Action::TYPE_CONNECTION_DELETE,
            \Flexio\Api\Action::TYPE_CONNECTION_READ,
            //\Flexio\Api\Action::TYPE_CONNECTION_CONNECT,
            //\Flexio\Api\Action::TYPE_CONNECTION_DISCONNECT,

            // allow execute on processes (create/update are also needed for executing)
            \Flexio\Api\Action::TYPE_PROCESS_CREATE,
            \Flexio\Api\Action::TYPE_PROCESS_UPDATE,
            \Flexio\Api\Action::TYPE_PROCESS_DELETE,
            \Flexio\Api\Action::TYPE_PROCESS_READ,

            // allow read-only on streams
            //\Flexio\Api\Action::TYPE_STREAM_CREATE,
            //\Flexio\Api\Action::TYPE_STREAM_UPDATE,
            //\Flexio\Api\Action::TYPE_STREAM_DELETE,
            \Flexio\Api\Action::TYPE_STREAM_READ
        );
    }

    private static function getTeamContributorRights() : array
    {
        return array(

            // don't allow read/write/delete for users
            //\Flexio\Api\Action::TYPE_USER_CREATE,
            //\Flexio\Api\Action::TYPE_USER_UPDATE,
            //\Flexio\Api\Action::TYPE_USER_DELETE,
            ///\Flexio\Api\Action::TYPE_USER_READ,

            // don't allow read/write/delete for user credentials/tokens
            //\Flexio\Api\Action::TYPE_USER_CREDENTIAL_UPDATE,
            //\Flexio\Api\Action::TYPE_USER_CREDENTIAL_RESET,
            //\Flexio\Api\Action::TYPE_USER_AUTHKEY_CREATE,
            //\Flexio\Api\Action::TYPE_USER_AUTHKEY_DELETE,
            //\Flexio\Api\Action::TYPE_USER_AUTHKEY_READ,

            // don't allow read/write/delete for teams
            //\Flexio\Api\Action::TYPE_TEAM_CREATE,
            //\Flexio\Api\Action::TYPE_TEAM_UPDATE,
            //\Flexio\Api\Action::TYPE_TEAM_DELETE,
            //\Flexio\Api\Action::TYPE_TEAM_READ,

            // allow reading of team members, but don't allow any team management
            \Flexio\Api\Action::TYPE_TEAMMEMBER_READ,
            //\Flexio\Api\Action::TYPE_TEAMMEMBER_CREATE,
            //\Flexio\Api\Action::TYPE_TEAMMEMBER_UPDATE,
            //\Flexio\Api\Action::TYPE_TEAMMEMBER_DELETE,
            //\Flexio\Api\Action::TYPE_TEAMMEMBER_READ,
            //\Flexio\Api\Action::TYPE_TEAMMEMBER_SENDINVITATION,

            // allow read/write/delete on pipes
            \Flexio\Api\Action::TYPE_PIPE_CREATE,
            \Flexio\Api\Action::TYPE_PIPE_UPDATE,
            \Flexio\Api\Action::TYPE_PIPE_DELETE,
            \Flexio\Api\Action::TYPE_PIPE_READ,

            // allow read/write/delete on connections
            \Flexio\Api\Action::TYPE_CONNECTION_CREATE,
            \Flexio\Api\Action::TYPE_CONNECTION_UPDATE,
            \Flexio\Api\Action::TYPE_CONNECTION_DELETE,
            \Flexio\Api\Action::TYPE_CONNECTION_READ,
            \Flexio\Api\Action::TYPE_CONNECTION_CONNECT,
            \Flexio\Api\Action::TYPE_CONNECTION_DISCONNECT,

            // allow read/write/delete/execute on processes
            \Flexio\Api\Action::TYPE_PROCESS_CREATE,
            \Flexio\Api\Action::TYPE_PROCESS_UPDATE,
            \Flexio\Api\Action::TYPE_PROCESS_DELETE,
            \Flexio\Api\Action::TYPE_PROCESS_READ,

            // allow read/write/delete on streams
            \Flexio\Api\Action::TYPE_STREAM_CREATE,
            \Flexio\Api\Action::TYPE_STREAM_UPDATE,
            \Flexio\Api\Action::TYPE_STREAM_DELETE,
            \Flexio\Api\Action::TYPE_STREAM_READ
        );
    }

    private static function getTeamAdministratorRights() : array
    {
        return array(

            // don't allow read/write/delete for users
            //\Flexio\Api\Action::TYPE_USER_CREATE,
            //\Flexio\Api\Action::TYPE_USER_UPDATE,
            //\Flexio\Api\Action::TYPE_USER_DELETE,
            ///\Flexio\Api\Action::TYPE_USER_READ,

            // don't allow read/write/delete for user credentials/tokens
            //\Flexio\Api\Action::TYPE_USER_CREDENTIAL_UPDATE,
            //\Flexio\Api\Action::TYPE_USER_CREDENTIAL_RESET,
            //\Flexio\Api\Action::TYPE_USER_AUTHKEY_CREATE,
            //\Flexio\Api\Action::TYPE_USER_AUTHKEY_DELETE,
            //\Flexio\Api\Action::TYPE_USER_AUTHKEY_READ,

            // don't allow read/write/delete for teams
            //\Flexio\Api\Action::TYPE_TEAM_CREATE,
            //\Flexio\Api\Action::TYPE_TEAM_UPDATE,
            //\Flexio\Api\Action::TYPE_TEAM_DELETE,
            //\Flexio\Api\Action::TYPE_TEAM_READ,

            // allow management of team members
            \Flexio\Api\Action::TYPE_TEAMMEMBER_CREATE,
            \Flexio\Api\Action::TYPE_TEAMMEMBER_UPDATE,
            \Flexio\Api\Action::TYPE_TEAMMEMBER_DELETE,
            \Flexio\Api\Action::TYPE_TEAMMEMBER_READ,
            \Flexio\Api\Action::TYPE_TEAMMEMBER_SENDINVITATION,
            \Flexio\Api\Action::TYPE_TEAMMEMBER_JOINTEAM,

            // allow read/write/delete on pipes
            \Flexio\Api\Action::TYPE_PIPE_CREATE,
            \Flexio\Api\Action::TYPE_PIPE_UPDATE,
            \Flexio\Api\Action::TYPE_PIPE_DELETE,
            \Flexio\Api\Action::TYPE_PIPE_READ,

            // allow read/write/delete on connections
            \Flexio\Api\Action::TYPE_CONNECTION_CREATE,
            \Flexio\Api\Action::TYPE_CONNECTION_UPDATE,
            \Flexio\Api\Action::TYPE_CONNECTION_DELETE,
            \Flexio\Api\Action::TYPE_CONNECTION_READ,
            \Flexio\Api\Action::TYPE_CONNECTION_CONNECT,
            \Flexio\Api\Action::TYPE_CONNECTION_DISCONNECT,

            // allow read/write/delete/execute on processes
            \Flexio\Api\Action::TYPE_PROCESS_CREATE,
            \Flexio\Api\Action::TYPE_PROCESS_UPDATE,
            \Flexio\Api\Action::TYPE_PROCESS_DELETE,
            \Flexio\Api\Action::TYPE_PROCESS_READ,

            // allow read/write/delete on streams
            \Flexio\Api\Action::TYPE_STREAM_CREATE,
            \Flexio\Api\Action::TYPE_STREAM_UPDATE,
            \Flexio\Api\Action::TYPE_STREAM_DELETE,
            \Flexio\Api\Action::TYPE_STREAM_READ
        );
    }

    private static function getTeamOwnerRights() : array
    {
        return array(

            // allow read/write/delete for users
            \Flexio\Api\Action::TYPE_USER_CREATE,
            \Flexio\Api\Action::TYPE_USER_UPDATE,
            \Flexio\Api\Action::TYPE_USER_DELETE,
            \Flexio\Api\Action::TYPE_USER_READ,

            // allow read/write/delete for user credentials/tokens
            \Flexio\Api\Action::TYPE_USER_CREDENTIAL_UPDATE,
            \Flexio\Api\Action::TYPE_USER_CREDENTIAL_RESET,
            \Flexio\Api\Action::TYPE_USER_AUTHKEY_CREATE,
            \Flexio\Api\Action::TYPE_USER_AUTHKEY_DELETE,
            \Flexio\Api\Action::TYPE_USER_AUTHKEY_READ,

            // allow read/write/delete for teams
            \Flexio\Api\Action::TYPE_TEAM_CREATE,
            \Flexio\Api\Action::TYPE_TEAM_UPDATE,
            \Flexio\Api\Action::TYPE_TEAM_DELETE,
            \Flexio\Api\Action::TYPE_TEAM_READ,

            // allow management of team members
            \Flexio\Api\Action::TYPE_TEAMMEMBER_CREATE,
            \Flexio\Api\Action::TYPE_TEAMMEMBER_UPDATE,
            \Flexio\Api\Action::TYPE_TEAMMEMBER_DELETE,
            \Flexio\Api\Action::TYPE_TEAMMEMBER_READ,
            \Flexio\Api\Action::TYPE_TEAMMEMBER_SENDINVITATION,
            \Flexio\Api\Action::TYPE_TEAMMEMBER_JOINTEAM,

            // allow read/write/delete on pipes
            \Flexio\Api\Action::TYPE_PIPE_CREATE,
            \Flexio\Api\Action::TYPE_PIPE_UPDATE,
            \Flexio\Api\Action::TYPE_PIPE_DELETE,
            \Flexio\Api\Action::TYPE_PIPE_READ,

            // allow read/write/delete on connections
            \Flexio\Api\Action::TYPE_CONNECTION_CREATE,
            \Flexio\Api\Action::TYPE_CONNECTION_UPDATE,
            \Flexio\Api\Action::TYPE_CONNECTION_DELETE,
            \Flexio\Api\Action::TYPE_CONNECTION_READ,
            \Flexio\Api\Action::TYPE_CONNECTION_CONNECT,
            \Flexio\Api\Action::TYPE_CONNECTION_DISCONNECT,

            // allow read/write/delete/execute on processes
            \Flexio\Api\Action::TYPE_PROCESS_CREATE,
            \Flexio\Api\Action::TYPE_PROCESS_UPDATE,
            \Flexio\Api\Action::TYPE_PROCESS_DELETE,
            \Flexio\Api\Action::TYPE_PROCESS_READ,

            // allow read/write/delete on streams
            \Flexio\Api\Action::TYPE_STREAM_CREATE,
            \Flexio\Api\Action::TYPE_STREAM_UPDATE,
            \Flexio\Api\Action::TYPE_STREAM_DELETE,
            \Flexio\Api\Action::TYPE_STREAM_READ
        );
    }

    private static function getSystemAdministratorRights() : array
    {
        return array(

            // allow read/write/delete for users
            \Flexio\Api\Action::TYPE_USER_CREATE,
            \Flexio\Api\Action::TYPE_USER_UPDATE,
            \Flexio\Api\Action::TYPE_USER_DELETE,
            \Flexio\Api\Action::TYPE_USER_READ,

            // allow read/write/delete for user credentials/tokens
            \Flexio\Api\Action::TYPE_USER_CREDENTIAL_UPDATE,
            \Flexio\Api\Action::TYPE_USER_CREDENTIAL_RESET,
            \Flexio\Api\Action::TYPE_USER_AUTHKEY_CREATE,
            \Flexio\Api\Action::TYPE_USER_AUTHKEY_DELETE,
            \Flexio\Api\Action::TYPE_USER_AUTHKEY_READ,

            // allow read/write/delete for teams
            \Flexio\Api\Action::TYPE_TEAM_CREATE,
            \Flexio\Api\Action::TYPE_TEAM_UPDATE,
            \Flexio\Api\Action::TYPE_TEAM_DELETE,
            \Flexio\Api\Action::TYPE_TEAM_READ,

            // allow management of team members
            \Flexio\Api\Action::TYPE_TEAMMEMBER_CREATE,
            \Flexio\Api\Action::TYPE_TEAMMEMBER_UPDATE,
            \Flexio\Api\Action::TYPE_TEAMMEMBER_DELETE,
            \Flexio\Api\Action::TYPE_TEAMMEMBER_READ,
            \Flexio\Api\Action::TYPE_TEAMMEMBER_SENDINVITATION,
            \Flexio\Api\Action::TYPE_TEAMMEMBER_JOINTEAM,

            // allow read/write/delete on pipes
            \Flexio\Api\Action::TYPE_PIPE_CREATE,
            \Flexio\Api\Action::TYPE_PIPE_UPDATE,
            \Flexio\Api\Action::TYPE_PIPE_DELETE,
            \Flexio\Api\Action::TYPE_PIPE_READ,

            // allow read/write/delete on connections
            \Flexio\Api\Action::TYPE_CONNECTION_CREATE,
            \Flexio\Api\Action::TYPE_CONNECTION_UPDATE,
            \Flexio\Api\Action::TYPE_CONNECTION_DELETE,
            \Flexio\Api\Action::TYPE_CONNECTION_READ,
            \Flexio\Api\Action::TYPE_CONNECTION_CONNECT,
            \Flexio\Api\Action::TYPE_CONNECTION_DISCONNECT,

            // allow read/write/delete/execute on processes
            \Flexio\Api\Action::TYPE_PROCESS_CREATE,
            \Flexio\Api\Action::TYPE_PROCESS_UPDATE,
            \Flexio\Api\Action::TYPE_PROCESS_DELETE,
            \Flexio\Api\Action::TYPE_PROCESS_READ,

            // allow read/write/delete on streams
            \Flexio\Api\Action::TYPE_STREAM_CREATE,
            \Flexio\Api\Action::TYPE_STREAM_UPDATE,
            \Flexio\Api\Action::TYPE_STREAM_DELETE,
            \Flexio\Api\Action::TYPE_STREAM_READ,

            // allow reading system info
            \Flexio\Api\Action::TYPE_SYSTEM_READ
        );
    }
}
