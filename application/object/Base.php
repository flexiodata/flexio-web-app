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
        // CHECK: the requesting user must be active
        $user_status = $this->getModel()->user->getStatus($user_eid);
        if ($user_status !== \Model::STATUS_AVAILABLE)
            return false;

        // CHECK: if the user is the owner, allow them to do anything
        if ($user_eid === $this->getOwner())
            return true;

        // CHECK: see if the user is a member of the owner team, and if they have
        // actively joined the team
        try
        {
            $member_info = $this->getModel()->teammember->get($user_eid, $this->getOwner());
            if ($member_info['member_status'] === \Model::TEAM_MEMBER_STATUS_ACTIVE)
            {
                // for now, grant all rights to a team member
                return true;

                // TODO: in the future, limit rights by checking if the action
                // is in the list of rights

                $rights = @json_decode($member_info['member_status'],true);
                if (is_array($rights))
                {
                    foreach ($rights as $r)
                    {
                        if ($action === $r)
                            return true;
                    }
                }
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
