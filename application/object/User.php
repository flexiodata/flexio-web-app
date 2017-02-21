<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-03-30
 *
 * @package flexio
 * @subpackage Object
 */


namespace Flexio\Object;


class User extends \Flexio\Object\Base
{
    const USER_SYSTEM = 0;
    const USER_PUBLIC = 1;

    public function __construct()
    {
        $this->setType(\Model::TYPE_USER);
    }

    public static function create($properties = null)
    {
/*
        // TODO: find out what's wrong with this implementation

        // a unique username is required for users; if a username
        // isn't specified, then supply a default
        $username = \Util::generateHandle();
        $email= $username.'@flex.io';
        if (!isset($properties))
        {
            $properties = array('user_name' => $username, 'email' => $email);
        }
        if (is_array($properties) && !isset($properties['user_name']))
        {
            if (!isset($properties['user_name']))
                $properties['user_name'] = $username;
            if (!isset($properties['email']))
                $properties['email'] = $email;
        }

        parent::create($properties);
*/

        // a unique username is required for users; if a username
        // isn't specified, then supply a default
        $username = \Util::generateHandle();
        $email= $username.'@flex.io';
        if (!isset($properties))
        {
            $properties = array('user_name' => $username, 'email' => $email);
        }
        if (is_array($properties) && !isset($properties['user_name']))
        {
            if (!isset($properties['user_name']))
                $properties['user_name'] = $username;
            if (!isset($properties['email']))
                $properties['email'] = $email;
        }

        $object = new static();
        $model = \Flexio\Object\Store::getModel();

        $local_eid = $model->create($object->getType(), $properties);
        if ($local_eid === false)
            return false;

        $object->setModel($model);
        $object->setEid($local_eid);
        $object->setRights();
        $object->clearCache();
        return $object;
    }

    public static function load($identifier)
    {
        // note: \User::load() differs from other load implementations
        // in that a user can be loaded either by a unique eid, by
        // the user name, or the user email, all of which are unique
        // for a user

        $object = new static();
        $model = \Flexio\Object\Store::getModel();

        // assume the identifier is an eid, and try to find out the type
        $eid = $identifier;
        $local_eid_type = $model->getType($identifier);

        if ($local_eid_type !== $object->getType())
        {
            // the input isn't an eid, so it must be an identifier; try
            // to find the eid from the identifier; if we can't find it,
            // we're done
            $eid = $model->user->getEidFromIdentifier($identifier);
            if ($eid === false)
                return false;
        }

        // TODO: for now, don't allow objects that have been deleted
        // to be loaded; in general, we may want to move this to the
        // api layer, but previously, it's been in the model layer,
        // and we need to make sure the behavior is the same after the
        // model constraint is removed, and object loading is a good
        // location for this constraint
        if ($model->getStatus($eid) === \Model::STATUS_DELETED)
            return false;

        $object->setModel($model);
        $object->setEid($eid);
        $object->setRights();
        $object->clearCache();
        return $object;
    }

    public function copy()
    {
        // TODO: disable copying of user objects for now, since users can't
        // have the same username or email
        return false;
    }

    public function set($properties)
    {
        // TODO: add properties check
        $this->clearCache();
        $user_model = $this->getModel()->user;
        $user_model->set($this->getEid(), $properties);
        return $this;
    }

    public function get()
    {
        if ($this->isCached() === true)
            return $this->properties;

        if ($this->populateCache() === true)
            return $this->properties;

        return false;
    }

    public function getVerifyCode()
    {
        $properties = $this->get();
        return $properties['verify_code'];
    }

    public function checkPassword($password)
    {
        return $this->getModel()->user->checkUserPasswordByEid($this->getEid(), $password);
    }

    public function isAdministrator()
    {
        // TODO: for now, administrators is any user with an email address for flexio

        $user_info = $this->get();
        $user_email = $user_info['email'];
        if (preg_match('/^[a-z]+@flex\.io$/', $user_email))
            return true;

        return false;
    }

    private function isCached()
    {
        if ($this->properties === false)
            return false;

        return true;
    }

    private function clearCache()
    {
        $this->eid_status = false;
        $this->properties = false;
    }

    private function populateCache()
    {
        // get the properties
        $local_properties = $this->getProperties();
        if ($local_properties === false)
            return false;

        // save the properties
        $this->properties = $local_properties;
        $this->eid_status = $local_properties['eid_status'];
        return true;
    }

    private function getProperties()
    {
        $query = '
        {
            "eid" : null,
            "eid_type" : "'.\Model::TYPE_USER.'",
            "eid_status" : null,
            "ename" : null,
            "user_name" : null,
            "description" : null,
            "full_name" : null,
            "first_name" : null,
            "last_name" : null,
            "email" : null,
            "email_hash" : null,
            "phone" : null,
            "location_city" : null,
            "location_state" : null,
            "location_country" : null,
            "company_name" : null,
            "company_url" : null,
            "locale_language" : null,
            "locale_decimal" : null,
            "locale_thousands" : null,
            "locale_dateformat" : null,
            "timezone" : null,
            "owned_by='.\Model::EDGE_OWNED_BY.'" : {
                "eid" : null,
                "eid_type" : "'.\Model::TYPE_USER.'",
                "user_name" : null,
                "full_name" : null,
                "first_name" : null,
                "last_name" : null,
                "email_hash" : null
            },
            "created_by='.\Model::EDGE_CREATED_BY.'" : {
                "eid" : null,
                "eid_type" : "'.\Model::TYPE_USER.'",
                "full_name" : null,
                "user_name" : null,
                "first_name" : null,
                "last_name" : null,
                "email_hash" : null
            },
            "verify_code" : null,
            "config" : null,
            "created" : null,
            "updated" : null
        }
        ';

        $query = json_decode($query);
        $properties = \Flexio\Object\Query::exec($this->getEid(), $query);
        if (!$properties)
            return false;

        // unpack the config
        $config = $properties['config'];
        $config = @json_decode($config, true);
        if (!is_array($config))
            $config = new Object;
        $properties['config'] = $config;

        return $properties;
    }
}
