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
    public static function create(\Flexio\Api\Request $request) : array
    {
        $params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'parent_eid'        => array('type' => 'identifier', 'required' => false),
                'eid_status'        => array('type' => 'string',  'required' => false),
                'ename'             => array('type' => 'identifier', 'required' => false),
                'name'              => array('type' => 'string',  'required' => false),
                'description'       => array('type' => 'string',  'required' => false),
                'connection_type'   => array('type' => 'string',  'required' => false),
                'connection_status' => array('type' => 'string',  'required' => false),
                'connection_info'   => array('type' => 'object',  'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $project_identifier = isset($validated_params['parent_eid']) ? $validated_params['parent_eid'] : false;

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
        $connection = \Flexio\Object\Connection::create($validated_params);

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
        $properties = self::maskProperties($connection->get());

        // coerce an empty connection_info array() from [] into object {}

        if (isset($properties['connection_info']['headers']) && is_array($properties['connection_info']['headers']) && count($properties['connection_info']['headers'])==0)
            $properties['connection_info']['headers'] = (object)$properties['connection_info']['headers'];

        if (isset($properties['connection_info']) && is_array($properties['connection_info']) && count($properties['connection_info'])==0)
            $properties['connection_info'] = (object)$properties['connection_info'];

        return $properties;
    }

    public static function delete(\Flexio\Api\Request $request) : bool
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $connection_identifier = $validated_params['eid'];

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

    public static function set(\Flexio\Api\Request $request) : array
    {
        $params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid'               => array('type' => 'identifier', 'required' => true),
                'eid_status'        => array('type' => 'string',  'required' => false),
                'ename'             => array('type' => 'identifier', 'required' => false),
                'name'              => array('type' => 'string',  'required' => false),
                'description'       => array('type' => 'string',  'required' => false),
                'connection_type'   => array('type' => 'string',  'required' => false),
                'connection_status' => array('type' => 'string',  'required' => false),
                'connection_info'    => array('type' => 'object', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $connection_identifier = $validated_params['eid'];

        // load the object
        $connection = \Flexio\Object\Connection::load($connection_identifier);
        if ($connection === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($connection->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // temporary measure to not set masked items; caller should not make calls to 'set' with masked items
        if (isset($validated_params['connection_info']['access_token']) && $validated_params['connection_info']['access_token'] == '*****')
            unset($validated_params['connection_info']['access_token']);
        if (isset($validated_params['connection_info']['refresh_token']) && $validated_params['connection_info']['refresh_token'] == '*****')
            unset($validated_params['connection_info']['refresh_token']);
        if (isset($validated_params['connection_info']['password']) && $validated_params['connection_info']['password'] == '*****')
            unset($validated_params['connection_info']['password']);
        if (isset($validated_params['connection_info']['expires']) && $validated_params['connection_info']['expires'] == '*****')
            unset($validated_params['connection_info']['expires']);

        // set the properties
        $connection->set($validated_params);

        // get the $connection properties
        $properties = self::maskProperties($connection->get());
        return $properties;
    }

    public static function get(\Flexio\Api\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $connection_identifier = $validated_params['eid'];

        // load the object
        $connection = \Flexio\Object\Connection::load($connection_identifier);
        if ($connection === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($connection->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the connection properties
        $properties = self::maskProperties($connection->get());
        return $properties;
    }

    public static function listall(\Flexio\Api\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

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

    public static function describe(\Flexio\Api\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true),
                'q' => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $connection_identifier = $validated_params['eid'];
        $path = $validated_params['q'] ?? '';

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

        $result = $service->listObjects($path);
        if (!is_array($result))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        return $result;
    }

    public static function connect(\Flexio\Api\Request $request) : array
    {
        $params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $connection_identifier = $validated_params['eid'];

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
        $properties = self::maskProperties($connection->get());
        return $properties;
    }

    public static function disconnect(\Flexio\Api\Request $request) : array
    {
        $params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $connection_identifier = $validated_params['eid'];

        // load the object
        $connection = \Flexio\Object\Connection::load($connection_identifier);
        if ($connection === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
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
        $connection_info['expires'] = "*****";

        $properties['connection_info'] = $connection_info;
        return $properties;
    }
}
