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
        // if the user is the owner, allow them to do anything;
        // TODO: if the user interface ever allows rights to be limited
        // for the owner, this needs to be removed; right now, the UI allows
        // the owner to do everything, so this is an optimization

        // TODO: when other access types are added, we'll need to add
        // a parameter to the function to qualify the access code; for
        // now, all access types are either eids or the public access
        // type category

        if ($user_eid === $this->getOwner())
            return true;

        // if the user is an administrator, allow access
        if ($this->getModel()->user->isAdministrator($user_eid) === true)
            return true;

        // action not allowed
        return false;
    }

    public function grant(string $user_eid, array $actions) : \Flexio\Object\Base
    {
        // DEPRECATED:
        return $this;
    }

    public function getRights() : array
    {
        // DEPRECATED:
        return array();
    }

    public function setRights(array $rights) : \Flexio\Object\Base
    {
        // DEPRECATED:
        return $this;
    }

    protected function getModel() : \Model
    {
        return \Flexio\System\System::getModel();
    }
}
