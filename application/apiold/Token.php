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
namespace Flexio\Api1;


class Token
{
    public static function create(\Flexio\Api1\Request $request) : array
    {
        $params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $user_identifier = $validated_params['eid'];

        // load the object and check the rights; note: user rights govern user tokens
        $user = \Flexio\Object\User::load($user_identifier);
        if ($user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($user->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // create the token object
        $token_properties = array();
        $token_properties['owned_by'] = $user->getEid();
        $token_properties['created_by'] = $requesting_user_eid;
        $token = \Flexio\Object\Token::create($token_properties);
        return $token->get();
    }

    public static function delete(\Flexio\Api1\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $token_identifier = $validated_params['eid'];

        // load the user and check the rights; note: user rights govern user tokens
        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($user->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false) // use write, since it's like changing a user property
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $token = \Flexio\Object\Token::load($token_identifier);
        if ($token->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        $token->delete();

        $result = array();
        $result['eid'] = $token->getEid();
        $result['eid_type'] = $token->getType();
        $result['eid_status'] = $token->getStatus();
        return $result;
    }

    public static function get(\Flexio\Api1\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $token_identifier = $validated_params['eid'];

        // load the user and check the rights; note: user rights govern user tokens
        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($user->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the properties
        $token = \Flexio\Object\Token::load($token_identifier);
        if ($token->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        $properties = $token->get();
        return $properties;
    }

    public static function list(\Flexio\Api1\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $user_identifier = $validated_params['eid'];

        // load the user and check the rights; note: user rights govern user tokens
        $user = \Flexio\Object\User::load($user_identifier);
        if ($user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($user->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the tokens
        $result = array();

        $filter = array('owned_by' => $user->getEid(), 'eid_status' => \Model::STATUS_AVAILABLE);
        $tokens = \Flexio\Object\Token::list($filter);

        foreach ($tokens as $t)
        {
            $result[] = $t->get();
        }

        return $result;
    }
}
