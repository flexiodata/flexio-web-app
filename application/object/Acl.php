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

    public static function create(array $acl = null) : \Flexio\Object\Acl
    {
        // note: user acl is a list of enumerated rights in the form
        // {
        //    "action.<type1>" : [
        //        "owner",
        //        "group",
        //        "public"
        //    ],
        //    "action.<type2>" : [
        //        "owner"
        //        "public"
        //    ],
        //    ...
        // }

        // if the acl is null, start with an empty set of rights
        if (!isset($acl))
        {
            $this->acl = array();
            return $this;
        }

        // make sure the acl list we're using to serialize the object
        // is valid
        foreach ($acl as $action => $user_list)
        {
            if (\Flexio\Object\Action::isValid($action) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

            if (is_array($user_list) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

            foreach ($user_list as $user)
            {
                if (\Flexio\Object\User::isValidType($user) === false)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
            }
        }

        return (new static);
    }

    public function add(string $action, string $user) : \Flexio\Object\Acl
    {
        // make sure the action and user are valid
        if (\Flexio\Object\Action::isValid($action) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        if (\Flexio\Object\User::isValidType($user_type) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // if the action isn't set, set the action and the user
        if (!isset($this->acl[$action]))
        {
            $this->acl[$action] = array($user);
            return $this;
        }

        // if the user is already set on the object, we're done
        foreach ($this->acl[$action] as $user_in_question)
        {
            if ($user === $user_in_question)
                return $this;
        }

        // we couldn't find the user; add the user to the list of
        // allowed users
        $users_allowed = $this->acl[$action];
        $users_allowed[] = $user;
        $this->acl[$action] = $users_allowed;
        return $this;
    }

    public function remove(string $action, string $user) : \Flexio\Object\Acl
    {
        // if the action isn't set, there's nothing to remove
        if (!isset($this->acl[$action]))
            return $this;

        // cycle through the users; if the user matches one of the users
        // currently allowed, remove the user
        $users_allowed = array();
        foreach ($this->acl[$action] as $user_in_question)
        {
            if ($user === $user_in_question)
                continue;

            $users_allowed[] = $user_in_question;
        }

        // update the users allowed for the action
        $this->acl[$action] = $users_allowed;
        return $this;
    }

    public function get() : array
    {
        return $this->acl;
    }
}

