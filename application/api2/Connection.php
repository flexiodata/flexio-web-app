<?php
/**
 *
 * Copyright (c) 2013, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams, Aaron L. Williams
 * Created:  2013-04-22
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api2;


class Connection
{
    public static function create(\Flexio\Api2\Request $request) : array
    {
        $request->track(\Flexio\Api\Action::TYPE_CONNECTION_CREATED);

        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'eid_status'        => array('type' => 'string',  'required' => false),
                'ename'             => array('type' => 'identifier', 'required' => false),
                'name'              => array('type' => 'string',  'required' => false),
                'description'       => array('type' => 'string',  'required' => false),
                'connection_type'   => array('type' => 'string',  'required' => false),
                'connection_status' => array('type' => 'string',  'required' => false),
                'connection_info'   => array('type' => 'object',  'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_post_params = $validator->getParams();

        // check the rights on the owner; ability to create an object is governed
        // currently by user write privileges
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // create the object
        $connection_properties = $validated_post_params;
        $connection_properties['owned_by'] = $owner_user_eid;
        $connection_properties['created_by'] = $requesting_user_eid;
        $connection = \Flexio\Object\Connection::create($connection_properties);

        // grant default rights to the owner; TODO: also grant default rights
        // to the requesting user?
        $connection->grant($owner_user_eid, \Model::ACCESS_CODE_TYPE_EID,
            array(
                \Flexio\Object\Right::TYPE_READ_RIGHTS,
                \Flexio\Object\Right::TYPE_WRITE_RIGHTS,
                \Flexio\Object\Right::TYPE_READ,
                \Flexio\Object\Right::TYPE_WRITE,
                \Flexio\Object\Right::TYPE_DELETE
            )
        );

        // get the connection properties
        $properties = self::maskProperties($connection->get());

        // coerce an empty connection_info array() from [] into object {}

        if (isset($properties['connection_info']['headers']) && is_array($properties['connection_info']['headers']) && count($properties['connection_info']['headers'])==0)
            $properties['connection_info']['headers'] = (object)$properties['connection_info']['headers'];

        if (isset($properties['connection_info']) && is_array($properties['connection_info']) && count($properties['connection_info'])==0)
            $properties['connection_info'] = (object)$properties['connection_info'];

        $request->track();
        return $properties;
    }

    public static function delete(\Flexio\Api2\Request $request) : array
    {
        $request->track(\Flexio\Api\Action::TYPE_CONNECTION_DELETED);

        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $connection_eid = $request->getObjectFromUrl();

        // load the object; make sure the eid is associated with the owner
        // as an additional check
        $connection = \Flexio\Object\Connection::load($connection_eid);
        if ($owner_user_eid !== $connection->getOwner())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($connection->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($connection->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_DELETE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $connection->delete();

        $result = array();
        $result['eid'] = $connection->getEid();
        $result['eid_type'] = $connection->getType();
        $result['eid_status'] = $connection->getStatus();

        $request->track();
        return $result;
    }

    public static function set(\Flexio\Api2\Request $request) : array
    {
        $request->track(\Flexio\Api\Action::TYPE_CONNECTION_UPDATED);

        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $connection_eid = $request->getObjectFromUrl();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'eid_status'        => array('type' => 'string',  'required' => false),
                'ename'             => array('type' => 'identifier', 'required' => false),
                'name'              => array('type' => 'string',  'required' => false),
                'description'       => array('type' => 'string',  'required' => false),
                'connection_type'   => array('type' => 'string',  'required' => false),
                'connection_status' => array('type' => 'string',  'required' => false),
                'connection_info'    => array('type' => 'object', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_post_params = $validator->getParams();

        // load the object; make sure the eid is associated with the owner
        // as an additional check
        $connection = \Flexio\Object\Connection::load($connection_eid);
        if ($owner_user_eid !== $connection->getOwner())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($connection->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($connection->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // set the properties
        $connection->set($validated_post_params);

        // get the $connection properties
        $properties = self::maskProperties($connection->get());

        $request->track();
        return $properties;
    }

    public static function get(\Flexio\Api2\Request $request) : array
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $connection_eid = $request->getObjectFromUrl();

        // load the object; make sure the eid is associated with the owner
        // as an additional check
        $connection = \Flexio\Object\Connection::load($connection_eid);
        if ($owner_user_eid !== $connection->getOwner())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($connection->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($connection->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the connection properties
        $properties = self::maskProperties($connection->get());
        return $properties;
    }

    public static function list(\Flexio\Api2\Request $request) : array
    {
        $query_params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        // TODO: add other query string params?
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($query_params, array(
                'start'    => array('type' => 'integer', 'required' => false),
                'tail'     => array('type' => 'integer', 'required' => false),
                'limit'    => array('type' => 'integer', 'required' => false),
                'created_min' => array('type' => 'date', 'required' => false),
                'created_max' => array('type' => 'date', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_query_params = $validator->getParams();

        // make sure the owner exists
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // get the connections
        $result = array();

        $filter = array('owned_by' => $owner_user_eid, 'eid_status' => \Model::STATUS_AVAILABLE);
        $filter = array_merge($validated_query_params, $filter); // give precedence to fixed owner/status
        $connections = \Flexio\Object\Connection::list($filter);

        foreach ($connections as $c)
        {
            if ($c->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
                continue;

            $properties = self::maskProperties($c->get());

            // coerce [] -> {}
            if (isset($properties['connection_info']['headers']) && is_array($properties['connection_info']['headers']) && count($properties['connection_info']['headers'])==0)
                $properties['connection_info']['headers'] = (object)$properties['connection_info']['headers'];

            if (isset($properties['connection_info']) && is_array($properties['connection_info']) && count($properties['connection_info'])==0)
                $properties['connection_info'] = (object)$properties['connection_info'];

            $result[] = $properties;
        }

        return $result;
    }

    public static function connect(\Flexio\Api2\Request $request) : array
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $connection_eid = $request->getObjectFromUrl();

        // load the object; make sure the eid is associated with the owner
        // as an additional check
        $connection = \Flexio\Object\Connection::load($connection_eid);
        if ($owner_user_eid !== $connection->getOwner())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($connection->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($connection->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);
        if ($connection->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // try to connect
        $connection->connect();
        $properties = self::maskProperties($connection->get());
        return $properties;
    }

    public static function disconnect(\Flexio\Api2\Request $request) : array
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $connection_eid = $request->getObjectFromUrl();

        // load the object; make sure the eid is associated with the owner
        // as an additional check
        $connection = \Flexio\Object\Connection::load($connection_eid);
        if ($owner_user_eid !== $connection->getOwner())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($connection->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($connection->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // disconnect
        $connection->disconnect();
        $properties = self::maskProperties($connection->get());
        return $properties;
    }

    private static function maskProperties(array $properties) : array
    {
        // TODO: figure out a way to make public/private properties
        // on the object so we can use the full object internally,
        // but not expose these on the api

        if (!isset($properties['connection_info']))
            return $properties;

        if (!is_array($properties['connection_info']))
            return $properties;

        // remove tokens and passwords if they are set
        $connection_info = $properties['connection_info'];
        $connection_info['password'] = "*****";
        $connection_info['access_token'] = "*****";
        $connection_info['refresh_token'] = "*****";

        $properties['connection_info'] = $connection_info;
        return $properties;
    }
}
