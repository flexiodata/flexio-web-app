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
namespace Flexio\Api;


class Connection
{
    public static function create(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'parent_eid'        => array('type' => 'identifier', 'required' => false),
                'eid_status'        => array('type' => 'string',  'required' => false),
                'ename'             => array('type' => 'identifier', 'required' => false),
                'name'              => array('type' => 'string',  'required' => false),
                'description'       => array('type' => 'string',  'required' => false),
                'host'              => array('type' => 'string',  'required' => false),
                'port'              => array('type' => 'integer', 'required' => false),
                'username'          => array('type' => 'string',  'required' => false),
                'password'          => array('type' => 'string',  'required' => false),
                'token'             => array('type' => 'string',  'required' => false),
                'database'          => array('type' => 'string',  'required' => false),
                'connection_type'   => array('type' => 'string',  'required' => false),
                'connection_status' => array('type' => 'string',  'required' => false)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $project_identifier = isset($params['parent_eid']) ? $params['parent_eid'] : false;

        // check rights
        $project = false;
        if ($project_identifier !== false)
        {
            $project = \Flexio\Object\Project::load($project_identifier);
            if ($project === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

            if ($project->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);
        }

        // create the object
        $connection = \Flexio\Object\Connection::create($params);

        $connection->setCreatedBy($requesting_user_eid);
        $connection->setOwner($requesting_user_eid);

        $connection->grant($requesting_user_eid, \Model::ACCESS_CODE_TYPE_EID,
            array(
                \Flexio\Object\Right::TYPE_READ_RIGHTS,
                \Flexio\Object\Right::TYPE_WRITE_RIGHTS,
                \Flexio\Object\Right::TYPE_READ,
                \Flexio\Object\Right::TYPE_WRITE,
                \Flexio\Object\Right::TYPE_DELETE
            )
        );

        // if a parent project is specified, add the object as a member of the project
        if ($project !== false)
            $project->addMember($connection);

        // get the connection properties
        $properties = self::filterProperties($connection->get());
        return $properties;
    }

    public static function delete(array $params, string $requesting_user_eid = null) : bool
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $connection_identifier = $params['eid'];

        // load the object
        $connection = \Flexio\Object\Connection::load($connection_identifier);
        if ($connection === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($connection->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_DELETE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $connection->delete();
        return true;
    }

    public static function set(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid'               => array('type' => 'identifier', 'required' => true),
                'eid_status'        => array('type' => 'string',  'required' => false),
                'ename'             => array('type' => 'identifier', 'required' => false),
                'name'              => array('type' => 'string',  'required' => false),
                'description'       => array('type' => 'string',  'required' => false),
                'host'              => array('type' => 'string',  'required' => false),
                'port'              => array('type' => 'integer', 'required' => false),
                'username'          => array('type' => 'string',  'required' => false),
                'password'          => array('type' => 'string',  'required' => false),
                'token'             => array('type' => 'string',  'required' => false),
                'database'          => array('type' => 'string',  'required' => false),
                'connection_type'   => array('type' => 'string',  'required' => false),
                'connection_status' => array('type' => 'string',  'required' => false)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $connection_identifier = $params['eid'];

        // load the object
        $connection = \Flexio\Object\Connection::load($connection_identifier);
        if ($connection === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($connection->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // set the properties
        $connection->set($params);

        // get the $connection properties
        $properties = self::filterProperties($connection->get());
        return $properties;
    }

    public static function get(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $connection_identifier = $params['eid'];

        // load the object
        $connection = \Flexio\Object\Connection::load($connection_identifier);
        if ($connection === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($connection->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the connection properties
        $properties = self::filterProperties($connection->get());
        return $properties;
    }

    public static function listall(array $params, string $requesting_user_eid = null) : array
    {
        // load the object
        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // get the connections
        $filter = array('eid_type' => array(\Model::TYPE_CONNECTION), 'eid_status' => array(\Model::STATUS_AVAILABLE));
        $connections = $user->getObjects($filter);

        $result = array();
        foreach ($connections as $c)
        {
            if ($c->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
                continue;

            $result[] = $c->get();
        }

        return $result;
    }

    public static function describe(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true),
                'q' => array('type' => 'string', 'required' => false)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $connection_identifier = $params['eid'];

        // load the object
        $connection = \Flexio\Object\Connection::load($connection_identifier);
        if ($connection === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($connection->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the connection items
        $service = $connection->getService();
        if (!$service)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        if (!$service->isOk())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        $path = $params['q'] ?? '';
        $result = $service->listObjects($path);

        if (!is_array($result))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        return $result;
    }

    public static function connect(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $connection_identifier = $params['eid'];

        // load the object
        $connection = \Flexio\Object\Connection::load($connection_identifier);
        if ($connection === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($connection->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);
        if ($connection->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // try to connect
        $connection->connect();
        $properties = self::filterProperties($connection->get());
        return $properties;
    }

    public static function disconnect(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $connection_identifier = $params['eid'];

        // load the object
        $connection = \Flexio\Object\Connection::load($connection_identifier);
        if ($connection === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($connection->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // disconnect
        $connection->disconnect();
        $properties = self::filterProperties($connection->get());
        return $properties;
    }

    private static function filterProperties(array $properties) : array
    {
        // TODO: figure out a way to make public/private properties
        // on the object so we can use the full object internally,
        // but not expose these on the api

        // remove tokens and passwords if they are set
        if (isset($properties['password']))
            unset($properties['password']);
        if (isset($properties['token']))
            unset($properties['token']);
        if (isset($properties['refresh_token']))
            unset($properties['refresh_token']);
        if (isset($properties['token_expires']))
            unset($properties['token_expires']);

        return $properties;
    }
}
