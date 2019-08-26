<?php
/**
 *
 * Copyright (c) 2013, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams, Aaron L. Williams
 * Created:  2013-04-22
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class Connection
{
    public static function create(\Flexio\Api\Request $request) : void
    {
        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        $request->track(\Flexio\Api\Action::TYPE_CONNECTION_CREATE);
        $cleaned_post_params = self::cleanProperties($post_params); // don't store sensitive info
        $request->setRequestParams($cleaned_post_params);

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'eid_status'        => array('type' => 'string', 'required' => false),
                'name'              => array('type' => 'identifier',  'required' => false),
                'title'             => array('type' => 'string', 'required' => false),
                'description'       => array('type' => 'string', 'required' => false),
                'connection_type'   => array('type' => 'string', 'required' => false),
                'connection_mode'   => array('type' => 'string', 'required' => false),
                'connection_status' => array('type' => 'string', 'required' => false),
                'connection_info'   => array('type' => 'object', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();

        // check the rights on the owner
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_CONNECTION_CREATE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // create the object
        $connection_properties = $validated_post_params;
        $connection_properties['owned_by'] = $owner_user_eid;
        $connection_properties['created_by'] = $requesting_user_eid;
        $connection = \Flexio\Object\Connection::create($connection_properties);

        // return the result
        $properties = $connection->get();
        $result = self::cleanProperties($properties);
        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    public static function delete(\Flexio\Api\Request $request) : void
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $connection_eid = $request->getObjectFromUrl();

        $request->track(\Flexio\Api\Action::TYPE_CONNECTION_DELETE);

        // load the object; make sure the eid is associated with the owner
        // as an additional check
        $connection = \Flexio\Object\Connection::load($connection_eid);
        if ($owner_user_eid !== $connection->getOwner())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // check the rights on the object
        if ($connection->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($connection->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_CONNECTION_DELETE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // delete the object
        $connection->delete();

        // return the result
        $properties = $connection->get();
        $result = self::cleanProperties($properties);
        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    public static function set(\Flexio\Api\Request $request) : void
    {
        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $connection_eid = $request->getObjectFromUrl();

        $request->track(\Flexio\Api\Action::TYPE_CONNECTION_UPDATE);
        $cleaned_post_params = self::cleanProperties($post_params); // don't store sensitive info
        $request->setRequestParams($cleaned_post_params);

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'eid_status'        => array('type' => 'string', 'required' => false),
                'name'              => array('type' => 'identifier',  'required' => false),
                'title'             => array('type' => 'string', 'required' => false),
                'description'       => array('type' => 'string', 'required' => false),
                'connection_type'   => array('type' => 'string', 'required' => false),
                'connection_mode'   => array('type' => 'string', 'required' => false),
                'connection_status' => array('type' => 'string', 'required' => false),
                'connection_info'   => array('type' => 'object', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();

        // load the object; make sure the eid is associated with the owner
        // as an additional check
        $connection = \Flexio\Object\Connection::load($connection_eid);
        if ($owner_user_eid !== $connection->getOwner())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // check the rights on the object
        if ($connection->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($connection->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_CONNECTION_UPDATE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // set the properties
        $connection->set($validated_post_params);

        // return the result
        $properties = $connection->get();
        $result = self::cleanProperties($properties);
        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    public static function get(\Flexio\Api\Request $request) : void
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $connection_eid = $request->getObjectFromUrl();

        // load the object; make sure the eid is associated with the owner
        // as an additional check
        $connection = \Flexio\Object\Connection::load($connection_eid);
        if ($owner_user_eid !== $connection->getOwner())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // check the rights on the object
        if ($connection->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($connection->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_CONNECTION_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // return the result
        $properties = $connection->get();
        $result = self::cleanProperties($properties);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function list(\Flexio\Api\Request $request) : void
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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_query_params = $validator->getParams();

        // make sure the owner exists
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // return the result
        $result = array();

        $filter = array('owned_by' => $owner_user_eid, 'eid_status' => \Model::STATUS_AVAILABLE);
        $filter = array_merge($validated_query_params, $filter); // give precedence to fixed owner/status
        $connections = \Flexio\Object\Connection::list($filter);

        foreach ($connections as $c)
        {
            if ($c->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_CONNECTION_READ) === false)
                continue;

            $properties = $c->get();
            $result[] = self::cleanProperties($properties);
        }

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function connect(\Flexio\Api\Request $request) : void
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $connection_eid = $request->getObjectFromUrl();

        $request->track(\Flexio\Api\Action::TYPE_CONNECTION_CONNECT);

        // load the object; make sure the eid is associated with the owner
        // as an additional check
        $connection = \Flexio\Object\Connection::load($connection_eid);
        if ($owner_user_eid !== $connection->getOwner())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // check the rights on the object
        if ($connection->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($connection->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_CONNECTION_CONNECT) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // try to connect
        try
        {
            $connection->connect();
        }
        catch (\Flexio\Base\Exception $e)
        {
        }

        // make sure the connection status is active, or else throw an exception
        $properties = $connection->get();
        $connection_status = $properties['connection_status'];
        if ($connection_status !== \Model::CONNECTION_STATUS_AVAILABLE)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CONNECTION_FAILED);

        $result = self::cleanProperties($properties);
        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    public static function disconnect(\Flexio\Api\Request $request) : void
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $connection_eid = $request->getObjectFromUrl();

        $request->track(\Flexio\Api\Action::TYPE_CONNECTION_DISCONNECT);

        // load the object; make sure the eid is associated with the owner
        // as an additional check
        $connection = \Flexio\Object\Connection::load($connection_eid);
        if ($owner_user_eid !== $connection->getOwner())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // check the rights on the object
        if ($connection->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($connection->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_CONNECTION_DISCONNECT) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // disconnect
        $connection->disconnect();

        // return the result
        $properties = $connection->get();
        $result = self::cleanProperties($properties);
        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    private static function cleanProperties(array $properties) : array
    {
        if (!isset($properties['connection_info']))
            return $properties;

        if (!is_array($properties['connection_info']))
            return $properties;

        // remove tokens and passwords if they are set
        $connection_info = $properties['connection_info'];

        if (isset($connection_info['password']))
            $connection_info['password'] = "*****";

        if (isset($connection_info['access_token']))
            $connection_info['access_token'] = "*****";

        if (isset($connection_info['refresh_token']))
            $connection_info['refresh_token'] = "*****";

        $properties['connection_info'] = $connection_info;

        // coerce [] -> {}; TODO: handle objects as objects rather than key/value arrays for coercion isn't necessary?
        if (isset($properties['connection_info']['headers']) && is_array($properties['connection_info']['headers']) && count($properties['connection_info']['headers'])==0)
            $properties['connection_info']['headers'] = (object)$properties['connection_info']['headers'];

        if (isset($properties['connection_info']) && is_array($properties['connection_info']) && count($properties['connection_info'])==0)
            $properties['connection_info'] = (object)$properties['connection_info'];

        return $properties;
    }
}
