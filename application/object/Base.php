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
        // CHECK: the requesting user must be active; if they're not,
        // don't allow them to do anything, unless they're the owner
        // requesting their own account info
        $user_status = $this->getModel()->user->getStatus($user_eid);
        if ($user_status !== \Model::STATUS_AVAILABLE)
        {
            // allow the owner to read their own info, even if they're pending; this case
            // is so that we can query for the user status in the interface for a pending user
            if ($user_eid === $this->getOwner() && $action === \Flexio\Api\Action::TYPE_USER_READ)
                return true;

            // for everything else, don't allow a pending status
            return false;
        }

        // CHECK: if the user is the owner, allow them to do anything
        if ($user_eid === $this->getOwner())
            return true;

        // CHECK: see if the user is a member of the owner team, and if they have
        // actively joined the team; if so, grant them "contributor" rights, which
        // allows them to perform CRUD operation on objects in the team, access the
        // team member list, but not perform other operations on the team or on
        // the owner user
        try
        {
            $member_info = $this->getModel()->teammember->get($user_eid, $this->getOwner());
            if ($member_info['member_status'] === \Model::TEAM_MEMBER_STATUS_ACTIVE)
            {
                switch ($action)
                {
                    // TODO: action types are specified in the api layer; location
                    // of base rights implementation should be relocated since this
                    // is accessing a layer above the object layer
                    case \Flexio\Api\Action::TYPE_TEAMMEMBER_READ:
                    case \Flexio\Api\Action::TYPE_PIPE_CREATE:
                    case \Flexio\Api\Action::TYPE_PIPE_UPDATE:
                    case \Flexio\Api\Action::TYPE_PIPE_DELETE:
                    case \Flexio\Api\Action::TYPE_PIPE_READ:
                    case \Flexio\Api\Action::TYPE_CONNECTION_CREATE:
                    case \Flexio\Api\Action::TYPE_CONNECTION_UPDATE:
                    case \Flexio\Api\Action::TYPE_CONNECTION_DELETE:
                    case \Flexio\Api\Action::TYPE_CONNECTION_READ:
                    case \Flexio\Api\Action::TYPE_CONNECTION_CONNECT:
                    case \Flexio\Api\Action::TYPE_CONNECTION_DISCONNECT:
                    case \Flexio\Api\Action::TYPE_PROCESS_CREATE:
                    case \Flexio\Api\Action::TYPE_PROCESS_UPDATE:
                    case \Flexio\Api\Action::TYPE_PROCESS_DELETE:
                    case \Flexio\Api\Action::TYPE_PROCESS_READ:
                    case \Flexio\Api\Action::TYPE_STREAM_CREATE:
                    case \Flexio\Api\Action::TYPE_STREAM_UPDATE:
                    case \Flexio\Api\Action::TYPE_STREAM_DELETE:
                    case \Flexio\Api\Action::TYPE_STREAM_READ:
                        return true;
                }

                // TODO: in the future, limit rights by checking if the action
                // is in the list of rights
                // $rights = @json_decode($member_info['member_status'],true);
                // if (is_array($rights))
                // {
                //     foreach ($rights as $r)
                //     {
                //         if ($action === $r)
                //             return true;
                //     }
                // }
            }
        }
        catch (\Exception $e)
        {
            // fall through
        }

        // CHECK: if the user is a system administrator, allow access
        if ($this->getModel()->user->isAdministrator($user_eid) === true)
            return true;

        // action not allowed
        return false;
    }

    protected function getModel() : \Model
    {
        return \Flexio\System\System::getModel();
    }
}
