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
namespace Flexio\Api;


class Token
{
    public static function create(array $params, \Flexio\Api\Request $request) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $user_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the object and check the rights
        $user = \Flexio\Object\User::load($user_identifier);
        if ($user === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        if ($user->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // create the token object
        $token_properties = $params;
        $token_properties['user_eid'] = $user->getEid(); // TODO: use setOwner() like other objects?
        unset($token_properties['eid']);

        $token = \Flexio\Object\Token::create($token_properties);
        if ($token === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        // get the token properties
        return $token->get();
    }

    public static function delete(array $params, \Flexio\Api\Request $request) : bool
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $user_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // TODO: temporarily disabled; need to add notion of ownership to user object
        // before this will work
/*
        // load the user and check the rights
        $user = \Flexio\Object\User::load($user_identifier);
        if ($user === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        if ($user->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_DELETE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);
*/
        $token = \Flexio\Object\Token::load($user_identifier);
        if ($token !== false)
            $token->delete();

        return true;
    }

    public static function get(array $params, \Flexio\Api\Request $request) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $user_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the user and check the rights
        $user = \Flexio\Object\User::load($user_identifier);
        if ($user === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        if ($user->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $token = \Flexio\Object\Token::load($user_identifier);
        if ($token === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // get the properties; double check to make sure that secret code isn't returned
        $properties = $token->get();
        unset($properties['secret_code']);
        return $properties;
    }

    public static function listall(array $params, \Flexio\Api\Request $request) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
            'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $user_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the user and check the rights
        $user = \Flexio\Object\User::load($user_identifier);
        if ($user === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        if ($user->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        return $user->getTokens();
    }
}
