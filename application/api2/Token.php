<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-10-24
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api2;


class Token
{
    public static function create(\Flexio\Api2\Request $request) : array
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        // check the rights on the owner; ability to create a token is governed
        // currently by user write privileges
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // create the token object
        $token_properties = array();
        $token_properties['owned_by'] = $owner_user_eid;
        $token_properties['created_by'] = $requesting_user_eid;
        $token = \Flexio\Object\Token::create($token_properties);
        return $token->get();
    }

    public static function delete(\Flexio\Api2\Request $request) : array
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $token_eid = $request->getObjectFromUrl();

        // check the rights on the owner; ability to delete a token is governed
        // currently by user write privileges
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $token = \Flexio\Object\Token::load($token_eid);
        if ($token->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        $token->delete();

        $result = array();
        $result['eid'] = $token->getEid();
        $result['eid_type'] = $token->getType();
        $result['eid_status'] = $token->getStatus();
        return $result;
    }

    public static function get(\Flexio\Api2\Request $request) : array
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $token_eid = $request->getObjectFromUrl();

        // check the rights on the owner; ability to read a token is governed
        // currently by user read privileges
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the properties
        $token = \Flexio\Object\Token::load($token_eid);
        if ($token->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        $properties = $token->get();
        return $properties;
    }

    public static function list(\Flexio\Api2\Request $request) : array
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        // check the rights on the owner; ability to read token info is governed
        // currently by user read privileges
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the tokens
        $result = array();

        $filter = array('owned_by' => $owner_user_eid, 'eid_status' => \Model::STATUS_AVAILABLE);
        $tokens = \Flexio\Object\Token::list($filter);

        foreach ($tokens as $t)
        {
            $result[] = $t->get();
        }

        return $result;
    }
}
