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
    // TODO: acl is a helper class that translates between the method of rights
    // management used in the api and that at the object/model level; it's in
    // the object folder instead of the api folder because rights properties
    // are returned in the object get() function and it's more convenient to
    // keep this here than adding the rights node at the api level; however,
    // moving the rights node to the api level may be the appropriate course,
    // in which case this class should be moved to the api level; the long-term
    // solution may be to expose rights in the UI in a true ACL list, in which
    // case this translation functionality may not be needed

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
                if (\Flexio\Object\Action::isValidType($action) === false)
                    return false;

                if (is_bool($allowed) === false)
                    return false;
            }
        }

        return true;
    }

    public static function apply(\Flexio\Object\Base $object, array $acl) : bool
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

        // the acl is specified, so make sure it's valid
        if (self::isValid($acl) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // start with the default and then set any permissions with the specified values
        $updated_acl = self::getDefault();
        foreach ($acl as $user => $action_list)
        {
            foreach ($action_list as $action => $allowed)
            {
                if ($allowed === true)
                    $object->grant($action, $user);
                     else
                    $object->revoke($action, $user);
            }
        }

        return true;
    }

    public static function enum(\Flexio\Object\Base $object) : array
    {
        // start with a default set of rights
        $result = self::getDefault();

        // get the rights and update the results from the default values
        $object_rights = $object->getRights();
        foreach ($object_rights as $r)
        {
            $access_code = $r['access_code']; // access code is the user category, eid, token, etc for which the rights are granted
            $action = $r['action']; // action is the right that's granted

            // if we can't find the access code and action, move on; the acl
            // list only shows acls that are explicitly exposed
            if (!isset($result[$access_code][$action]))
                continue;

            $result[$access_code][$action] = true; // if a right is granted, update the value
        }

        return $result;
    }

    private static function getDefault() : array
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

