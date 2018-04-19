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
    public static function create(\Flexio\Api\Request $request)
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        $request->track(\Flexio\Api\Action::TYPE_USER_AUTHKEY_CREATE);

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

        $result = $token->get();
        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    public static function delete(\Flexio\Api\Request $request)
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $token_eid = $request->getObjectFromUrl();

        $request->track(\Flexio\Api\Action::TYPE_USER_AUTHKEY_DELETE);

        // load the object; make sure the eid is associated with the owner
        // as an additional check
        $token = \Flexio\Object\Token::load($token_eid);
        if ($owner_user_eid !== $token->getOwner())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($token->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($token->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_DELETE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $token->delete();

        $result = $token->get();
        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    public static function get(\Flexio\Api\Request $request)
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $token_eid = $request->getObjectFromUrl();

        // load the object; make sure the eid is associated with the owner
        // as an additional check
        $token = \Flexio\Object\Token::load($token_eid);
        if ($owner_user_eid !== $token->getOwner())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($token->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($token->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $result = $token->get();
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function list(\Flexio\Api\Request $request)
    {
        $query_params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($query_params, array(
                'start'       => array('type' => 'integer', 'required' => false),
                'tail'        => array('type' => 'integer', 'required' => false),
                'limit'       => array('type' => 'integer', 'required' => false),
                'created_min' => array('type' => 'date',    'required' => false),
                'created_max' => array('type' => 'date',    'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_query_params = $validator->getParams();

        // make sure the owner exists
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // get the tokens
        $result = array();

        $filter = array('owned_by' => $owner_user_eid, 'eid_status' => \Model::STATUS_AVAILABLE);
        $filter = array_merge($validated_query_params, $filter); // give precedence to fixed owner/status
        $tokens = \Flexio\Object\Token::list($filter);

        foreach ($tokens as $t)
        {
            if ($t->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
                continue;

            $result[] = $t->get();
        }

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }
}
