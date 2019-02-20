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

    public function allows(string $access_code, string $action) : bool
    {
        // if the user is the owner, allow them to do anything;
        // TODO: if the user interface ever allows rights to be limited
        // for the owner, this needs to be removed; right now, the UI allows
        // the owner to do everything, so this is an optimization

        // TODO: when other access types are added, we'll need to add
        // a parameter to the function to qualify the access code; for
        // now, all access types are either eids or the public access
        // type category

        if ($access_code === $this->getOwner())
            return true;

        // if the user is an administrator, allow access
        if ($this->getModel()->user->isAdministrator($access_code) === true)
            return true;

        // get the rights for this object
        $rights = $this->getRightsInfo();

        // get all the allowed actions for the access code or any
        // public access type class
        $allowed_actions = array();
        foreach ($rights as $r)
        {
            $access_type = $r['access_type'];

            // add any rights specific to this access code
            if ($access_type === \Model::ACCESS_CODE_TYPE_EID && $access_code === $r['access_code'])
            {
                $actions_to_add_to_allowed = array_flip($r['actions']);
                $allowed_actions = array_merge($allowed_actions, $actions_to_add_to_allowed);
            }

            // add any public rights regardless of the access code
            if ($access_type === \Model::ACCESS_CODE_TYPE_CATEGORY && \Flexio\Object\User::MEMBER_PUBLIC === $r['access_code'])
            {
                $actions_to_add_to_allowed = array_flip($r['actions']);
                $allowed_actions = array_merge($allowed_actions, $actions_to_add_to_allowed);
            }
        }

        if (array_key_exists($action, $allowed_actions) === true)
            return true;

        // action not allowed
        return false;
    }

    public function grant(string $access_code, string $access_type, array $actions) : \Flexio\Object\Base
    {
        // get the existing rights; if the access_code and access_type
        // match an entry that's already there, then add the existing
        // rights to the first matching entry; otherwise, create a new
        // rights entry

        $existing_rights_entry = false;
        $existing_rights = $this->getRightsInfo();
        foreach ($existing_rights as $r)
        {
            if ($access_code !== $r['access_code'])
                continue;
            if ($access_type !== $r['access_type'])
                continue;

            $existing_rights_entry = $r;
            break;
        }

        $updated_rights = array();
        if ($existing_rights_entry !== false)
        {
            // right exists; add on the actions
            $updated_rights_entry = array();
            $updated_rights_entry['eid'] = $existing_rights_entry['eid'];
            $updated_rights_entry['access_code'] = $existing_rights_entry['access_code'];
            $updated_rights_entry['access_type'] = $existing_rights_entry['access_type'];
            $updated_rights_entry['actions'] = array_unique(array_merge($existing_rights_entry['actions'], $actions));
            $updated_rights[] = $updated_rights_entry;
        }
         else
        {
            // right doesn't exist; create a new one
            $new_rights_entry = array();
            $new_rights_entry['access_code'] = $access_code;
            $new_rights_entry['access_type'] = $access_type;
            $new_rights_entry['actions'] = $actions;
            $updated_rights[] = $new_rights_entry;
        }

        $this->setRights($updated_rights);
        return $this;
    }

    public function revoke(string $access_code, string $access_type, array $actions = null) : \Flexio\Object\Base
    {
        // if actions is null, revoke all rights for the given access code and
        // access type; otherwise, revoke the specified actions

        // get a list of rights
        $rights = $this->getRightsInfo();
        foreach ($rights as $r)
        {
            if ($access_code !== $r['access_code'])
                continue;
            if ($access_type !== $r['access_type'])
                continue;

            if (!is_array($actions))
            {
                // if actions is null, revoke all the rights
                $right_eid = $r['eid'];
                $this->getModel()->right->delete($right_eid);
            }
             else
            {
                // if actions is specified
                $new_actions = array();
                $actions_currently_allowed = $r['actions'];
                $actions_to_delete = array_flip($actions);

                foreach ($actions_currently_allowed as $a)
                {
                    if (array_key_exists($a, $actions_to_delete))
                        continue;

                    $new_actions[] = $a;
                }

                $right_eid = $r['eid'];
                $this->getModel()->right->set($right_eid, json_encode($new_actions));
            }
        }
    }

    public function getRights() : array
    {
        // note: this is almost the same as getRightsInfo(), but adds user
        // info for us in the api; checking rights doesn't require this
        // info, so getRightsInfo() is used internally for speed

        $result = array();
        $rights = $this->getRightsInfo();
        foreach ($rights as $r)
        {
            $right_eid = $r['eid'];
            $right_object = \Flexio\Object\Right::load($right_eid);
            $result[] = $right_object->get();
        }

        return $result;
    }

    public function setRights(array $rights) : \Flexio\Object\Base
    {
        // note: set rights is explicit: it replaces actions with the specified
        // actions if a rights eid is supplied, otherwise it creates a new entry;
        // grant() is additive: it adds to what's already been granted; revoke()
        // is subtractive: it takes away from what's been granted

        foreach ($rights as $r)
        {
            // see if the rights already exists
            $right_eid = $r['eid'] ?? '';
            $right = false;

            try
            {
                $right = \Flexio\Object\Right::load($right_eid);
            }
            catch (\Flexio\Base\Exception $e)
            {
            }

            // if a right eid is specified and the object eid is the same
            // as the current object, then update the right on the current
            // object; otherwise, create a new right on the current object
            if ($right !== false)
            {
                // only allow right associated with this object to be updated
                // from this object
                $right_info = $right->get();
                if ($this->getEid() === $right_info['object_eid'])
                {
                    // the right already exists, so update it, but don't allow the
                    // object to be changed
                    $rights_updated = array();
                    $rights_updated['actions'] = $r['actions'];
                    $right->set($rights_updated);
                    continue;
                }

                // fall through
            }

            // create a new right
            $rights_updated = $r;
            $rights_updated['object_eid'] = $this->getEid(); // set the object eid and pass everything else on
            \Flexio\Object\Right::create($rights_updated);
        }

        return $this;
    }

    protected function getModel() : \Model
    {
        return \Flexio\System\System::getModel();
    }

    private function getRightsInfo() : array
    {
        $result = array();
        $rights_info = $this->getModel()->right->getInfoFromObjectEid($this->getEid());

        foreach ($rights_info as $r)
        {
            if ($r['eid_status'] !== \Model::STATUS_AVAILABLE)
                continue;

            $r['actions'] = json_decode($r['actions'],true);
            $result[] = $r;
        }

        return $result;
    }
}
