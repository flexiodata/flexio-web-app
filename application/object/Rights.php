<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-06-25
 *
 * @package flexio
 * @subpackage Object
 */


namespace Flexio\Object;


class Rights
{
    const ACTION_UNDEFINED        = '';                        // undefined
    const ACTION_READ             = 'action_read';             // read access
    const ACTION_WRITE            = 'action_write';            // write access
    const ACTION_DELETE           = 'action_delete';           // delete access
    const ACTION_EXECUTE          = 'action_execute';          // execute access

    const MEMBER_UNDEFINED = '';
    const MEMBER_OWNER     = 'O';
    const MEMBER_GROUP     = 'G';
    const MEMBER_PUBLIC    = 'P';

    public static function listall($user_eid, $object_eid)
    {
        // lists all operations the specified user can take on the
        // object in question

        // get the object type
        $object_type = \Flexio\Object\Store::getModel()->getType($object_eid);


        // TODO: temporarily allow read access to pipes for embedded pipes
        if ($object_type === \Model::TYPE_PIPE &&
              !self::issystem($object_eid, $user_eid) &&
              !self::isowned($object_eid, $user_eid) &&
              !self::ismember($object_eid, $user_eid))
        {
                $rights = array(
                    self::ACTION_READ => true,
                    self::ACTION_WRITE => false,
                    self::ACTION_DELETE => false
                );

                return self::response($user_eid, $rights);
        }

        // TODO: temporarily allow read/write access to processes
        if ($object_type === \Model::TYPE_PROCESS &&
              !self::issystem($object_eid, $user_eid) &&
              !self::isowned($object_eid, $user_eid) &&
              !self::ismember($object_eid, $user_eid))
        {
            $rights = array(
                self::ACTION_READ => true,
                self::ACTION_WRITE => true,
                self::ACTION_DELETE => false
            );

            return self::response($user_eid, $rights);
        }

        // RIGHTS TO SELF: if a user is referencing themselves, they have full rights
        if ($object_type === \Model::TYPE_USER && $user_eid === $object_eid)
        {
            $rights = array(
                self::ACTION_READ => true,
                self::ACTION_WRITE => true,
                self::ACTION_DELETE => true
            );

            return self::response($user_eid, $rights);
        }

        // RIGHTS BY SYSTEM: if the user is the system, then
        // the user automatically has full access to the object
        // RIGHTS BY OWNERSHIP: if the user owns the object, then
        // the user automatically has full access to the object
        if (self::issystem($object_eid, $user_eid) || self::isowned($object_eid, $user_eid))
        {
            $rights = array(
                self::ACTION_READ => true,
                self::ACTION_WRITE => true,
                self::ACTION_DELETE => true
            );

            return self::response($user_eid, $rights);
        }

        // RIGHTS BY MEMBERSHIP: if the user is a follower of the
        // object or an object of which the specified object is
        // associated, then the user has full access to the object
        // unless the object is a project type, in which case the
        // user doesn't have delete privileges
        if (self::ismember($object_eid, $user_eid))
        {
            $allow_delete = true;
            if ($object_type === \Model::TYPE_PROJECT)
                $allow_delete = false;

            $rights = array(
                self::ACTION_READ => true,
                self::ACTION_WRITE => true,
                self::ACTION_DELETE => $allow_delete
            );

            return self::response($user_eid, $rights);
        }

        // NO ACCESS: if at this point, we haven't returned,
        // the given user doesn't have rights
        $rights = array(
            self::ACTION_READ => false,
            self::ACTION_WRITE => false,
            self::ACTION_DELETE => false
        );
        return self::response($user_eid, $rights);
    }

    public static function issystem($object_eid, $user_eid)
    {
        // the system user has rights to everything
        if ($user_eid === \Flexio\Object\User::USER_SYSTEM)
            return true;
    }

    public static function isowned($object_eid, $user_eid)
    {
        // invalid objects can't owned; object can't be owned
        // by an invalid user
        if (!\Flexio\System\Eid::isValid($object_eid))
            return false;
        if (!\Flexio\System\Eid::isValid($user_eid))
            return false;

        // see if the object is owned by the user directly
        $search_path = "$object_eid->(".\Model::EDGE_OWNED_BY.")->$user_eid";
        $owners = \Flexio\Object\Search::exec($search_path);
        if (count($owners) > 0)
            return true;

        return false;
    }

    public static function ismember($object_eid, $user_eid)
    {
        // invalid objects or users can't be followed or follow
        if (!\Flexio\System\Eid::isValid($object_eid))
            return false;
        if (!\Flexio\System\Eid::isValid($user_eid))
            return false;

        // note: in the following, we want to check if an object can be accessed by
        // anybody who's part of a project; this includes any object that's in a
        // project that's either owned or followed by the user in question; for
        // example, it includes:
        //     1) rights to userA for the project if they are the owner of the project
        //     2) rights to userA for the project if they are a follower of the project
        //     3) rights to userA for an object owned by userB in a project owned by userA and followed by userB
        //     4) rights to userA for an object owned by userB in a project followed by userA and owned by userB

        // see if the object is followed or owned by the user directly
        $search_path = "$object_eid->(".\Model::EDGE_FOLLOWED_BY.",".\Model::EDGE_OWNED_BY.")->$user_eid";
        $followers = \Flexio\Object\Search::exec($search_path);
        if (count($followers) > 0)
            return true;

        // see if the object is a member of a project followed or owned by the user
        $search_path = "$object_eid->(".\Model::EDGE_MEMBER_OF.")->(".\Model::TYPE_PROJECT.")".
                                  "->(".\Model::EDGE_FOLLOWED_BY.",".\Model::EDGE_OWNED_BY.")->$user_eid";
        $followers = \Flexio\Object\Search::exec($search_path);
        if (count($followers) > 0)
            return true;

        // see if the object is a resource that's a member of an object
        // that's a member of a project followed or owned by the user
        $search_path = "$object_eid->(".\Model::EDGE_MEMBER_OF.")->(".\Model::TYPE_PIPE.",".\Model::TYPE_CONNECTION.")".
                                  "->(".\Model::EDGE_MEMBER_OF.")->(".\Model::TYPE_PROJECT.")".
                                  "->(".\Model::EDGE_FOLLOWED_BY.",".\Model::EDGE_OWNED_BY.")->$user_eid";
        $followers = \Flexio\Object\Search::exec($search_path);
        if (count($followers) > 0)
            return true;

        // see if the object is a comment on an object that's a member of a
        // project followed or owned by the user
        $search_path = "$object_eid->(".\Model::EDGE_COMMENT_ON.")->(".\Model::TYPE_PIPE.",".\Model::TYPE_CONNECTION.")".
                                  "->(".\Model::EDGE_MEMBER_OF.")->(".\Model::TYPE_PROJECT.")".
                                  "->(".\Model::EDGE_FOLLOWED_BY.",".\Model::EDGE_OWNED_BY.")->$user_eid";
        $followers = \Flexio\Object\Search::exec($search_path);
        if (count($followers) > 0)
            return true;

        // we can't find a path where the object is being followed by the
        // given user
        return false;
    }

    private static function response($user_eid, $rights_arr)
    {
        // packages up the specified user and rights into a
        // standard result
        $result = array();
        $result['eid'] = $user_eid;
        $result['eid_type'] = \Model::TYPE_USER;
        return array_merge($result, $rights_arr);
    }
}
