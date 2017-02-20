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


class TokenApi
{
    public static function create($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $user_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the object and check the rights
        $user = \Flexio\Object\User::load($user_identifier);
        if ($user === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        if ($user->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_WRITE) === false)
            return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);

        // create the token object
        $token_properties = $params;
        $token_properties['user_eid'] = $user->getEid(); // TODO: use setOwner() like other objects?
        unset($token_properties['eid']);

        $token = \Flexio\Object\Token::create($token_properties);
        if ($token === false)
            return $request->getValidator()->fail(Api::ERROR_CREATE_FAILED);

        // get the token properties
        return $token->get();
    }

    public static function delete($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $user_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // TODO: temporarily disabled; need to add notion of ownership to user object
        // before this will work
/*
        // load the user and check the rights
        $user = \Flexio\Object\User::load($user_identifier);
        if ($user === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        if ($user->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_DELETE) === false)
            return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);
*/
        $token = \Flexio\Object\Token::load($user_identifier);
        if ($token !== false)
            $token->delete();

        return true;
    }

    public static function get($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $user_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the user and check the rights
        $user = \Flexio\Object\User::load($user_identifier);
        if ($user === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        if ($user->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
            return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);

        $token = \Flexio\Object\Token::load($user_identifier);
        if ($token === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        // get the properties; double check to make sure that secret code isn't returned
        $properties = $token->get();
        unset($properties['secret_code']);
        return $properties;
    }

    public static function listall($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
            'eid' => array('type' => 'identifier', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $user_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the user and check the rights
        $user = \Flexio\Object\User::load($user_identifier);
        if ($user === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        if ($user->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
            return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);

        $token_items = System::getModel()->token->getInfoFromUserEid($user->getEid());
        if ($token_items === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        $res = array();
        foreach ($token_items as $item)
        {
            // only show tokens that are available; note: token list will return
            // all tokens, including ones that have been deleted, so this check
            // is important
            if (!$item || $item['eid_status'] != Model::STATUS_AVAILABLE)
                continue;

            // double-check to make sure we're not returning the secret code
            unset($item['secret_code']);

            $res[] = $item;
        }

        return $res;
    }
}
