<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-04-24
 *
 * @package flexio
 * @subpackage Object
 */


declare(strict_types=1);
namespace Flexio\Object;


class Acl
{
    private $acl = array();

    public static function isValid(array $acl) : bool
    {
        // make sure the acl list we're using to serialize the object is valid
        foreach ($acl as $user => $action_list)
        {
            if (\Flexio\Object\User::isValidType($user) === false)
                return false;

            if (is_array($action_list) === false)
                return false;

            foreach ($action_list as $action => $allowed)
            {
                if (\Flexio\Object\Action::isValid($action) === false)
                    return false;

                if (is_bool($allowed) === false)
                    return false;
            }
        }

        return true;
    }

    public static function create(array $acl = null) : \Flexio\Object\Acl
    {
        // note: user acl is a list of enumerated rights in the form
        // {
        //    "owner" : {
        //        "read": true,
        //        "write": true,
        //        "execute": true,
        //        "delete": true
        //    },
        //    "member" : {
        //        "read": true,
        //        "write": true,
        //        "execute": true,
        //        "delete": false
        //    },
        //    ...
        // }

        // if the acl is null, set the acl to the defaults
        if (!isset($acl))
        {
            $this->acl = $this->getDefault();
            return $this;
        }

        // the acl is specified, so make sure it's valid
        if (self::isValid($acl) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // start with the default and then set any permissions with the specified values
        $updated_acl = $this->getDefault();
        foreach ($acl as $user => $action_list)
        {
            foreach ($action_list as $action => $allowed)
            {
                $updated_acl[$user][$action] = $allowed;
            }
        }

        $this->acl = $updated_acl;
        return (new static);
    }

    public function add(string $action, string $user) : \Flexio\Object\Acl
    {
        // make sure the action and user are valid
        if (\Flexio\Object\Action::isValid($action) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        if (\Flexio\Object\User::isValidType($user_type) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $acl_local = $this->acl;
        $acl_local[$user][$action] = true;
        $this->acl = $acl_local;

        return $this;
    }

    public function remove(string $action, string $user) : \Flexio\Object\Acl
    {
        // make sure the action and user are valid
        if (\Flexio\Object\Action::isValid($action) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        if (\Flexio\Object\User::isValidType($user_type) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $acl_local = $this->acl;
        $acl_local[$user][$action] = false;
        $this->acl = $acl_local;

        return $this;
    }

    public function get() : array
    {
        return $this->acl;
    }

    private function getDefault() : array
    {
        // TODO: return actual acl
        return array(
            'owner' => array(
                'read' => false,
                'write' => false,
                'execute' => false,
                'delete' => false
            ),
            'member' => array(
                'read' => false,
                'write' => false,
                'execute' => false,
                'delete' => false
            ),
            'public' => array(
                'read' => false,
                'write' => false,
                'execute' => false,
                'delete' => false
            )
        );
    }
}

