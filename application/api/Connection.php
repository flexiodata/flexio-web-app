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
                'parent_eid'        => array('type' => 'string', 'required' => false),
                'name'              => array('type' => 'identifier',  'required' => false),
                'title'             => array('type' => 'string', 'required' => false),
                'icon'              => array('type' => 'string', 'required' => false),
                'description'       => array('type' => 'string', 'required' => false),
                'connection_type'   => array('type' => 'string', 'required' => false),
                'connection_mode'   => array('type' => 'string', 'required' => false),
                'connection_status' => array('type' => 'string', 'required' => false),
                'connection_info'   => array('type' => 'object', 'required' => false),
                'setup_template'    => array('type' => 'object', 'required' => false),
                'setup_config'      => array('type' => 'object', 'required' => false)
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

        // if we're deleting a connection mount, delete associated pipes/connections;
        // use a job so we can switch over to running this in the background in case
        // the delete takes time
        $connection_info = $connection->get();
        $connection_mode = $connection_info['connection_mode'];
        if ($connection_mode === \Model::CONNECTION_MODE_FUNCTION)
        {
            $process_engine = \Flexio\Jobs\Process::create();
            $process_engine->setOwner($connection->getOwner());
            $process_engine->queue('\Flexio\Jobs\Mount::delete', array('connection_eid' => $connection->getEid()));
            $process_engine->run(false /*true: run in background*/);
        }

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
                'parent_eid'        => array('type' => 'string', 'required' => false),
                'name'              => array('type' => 'identifier',  'required' => false),
                'title'             => array('type' => 'string', 'required' => false),
                'icon'              => array('type' => 'string', 'required' => false),
                'description'       => array('type' => 'string', 'required' => false),
                'connection_type'   => array('type' => 'string', 'required' => false),
                'connection_mode'   => array('type' => 'string', 'required' => false),
                'connection_status' => array('type' => 'string', 'required' => false),
                'connection_info'   => array('type' => 'object', 'required' => false),
                'setup_template'    => array('type' => 'object', 'required' => false),
                'setup_config'      => array('type' => 'object', 'required' => false)
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
                'eid_status'  => array(
                    'required' => false,
                    'array' => true, // explode parameter into array, each element of which must satisfy type/enum
                    'type' => 'string',
                    'default' => \Model::STATUS_AVAILABLE,
                    'enum' => [\Model::STATUS_AVAILABLE, \Model::STATUS_PENDING]),
                'parent_eid'  => array(
                    'required' => false,
                    'array' => true, // explode parameter into array, each element of which must satisfy type/enum
                    'type' => 'eid'),
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

        $filter = array('owned_by' => $owner_user_eid);
        $filter = array_merge($validated_query_params, $filter); // only allow items for owner
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

    public static function init(\Flexio\Api\Request $request) : void
    {
        // gets information from a configuration file for a mount;
        // used to load initial information needed into order to create
        // the configuration wizard for a mount

        $query_params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($query_params, array(
                'q' => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_query_params = $validator->getParams();
        $path = $validated_query_params['q'] ?? '';

        // load the object
        $owner_user = \Flexio\Object\User::load($owner_user_eid);

        // check the rights on the object
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_STREAM_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the content
        $content = '';
        $vfs = new \Flexio\Services\Vfs($owner_user_eid);
        $vfs->read($path, function($data) use (&$content) {
            $content .= $data;
        });

        // parse the yaml for the config file and return the result
        $result = \Flexio\Base\Yaml::parse($content);
        if ($result === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function sync(\Flexio\Api\Request $request) : void
    {
        // syncs pipes in a mounted a connection with the source files in the connection

        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $connection_eid = $request->getObjectFromUrl();

        // TODO: re-enable tracking
        //$request->track(\Flexio\Api\Action::TYPE_CONNECTION_SYNC);

        // load the object; make sure the eid is associated with the owner
        // as an additional check
        $connection = \Flexio\Object\Connection::load($connection_eid);
        if ($owner_user_eid !== $connection->getOwner())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // check the rights on the object
        if ($connection->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        //if ($connection->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_CONNECTION_SYNC) === false)
        //    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);
        // TODO: for now, use pipe update right; add a right for connection sync?
        if ($connection->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_PIPE_UPDATE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // if the process is created with a request from an api token, it's
        // triggered with an api; if there's no api token, it's triggered
        // from a web session, in which case it's triggered by the UI;
        // TODO: this will work until we allow processes to be created from
        // public pipes that don't require a token
        $triggered_by = strlen($request->getToken()) > 0 ? \Model::PROCESS_TRIGGERED_API : \Model::PROCESS_TRIGGERED_INTERFACE;

        // create a new process object for storing process info
        $process_properties = array(
            'parent_eid' => $connection->getEid(),
            'task' => array(), // TODO: first instance where we have a job without concrete task info
            'triggered_by' => $triggered_by,
            'owned_by' => $owner_user_eid,
            'created_by' => $requesting_user_eid
        );
        $process_store = \Flexio\Object\Process::create($process_properties);

        // create a new process engine for running a process
        $process_engine = \Flexio\Jobs\Process::create();
        $process_engine->queue('\Flexio\Jobs\Mount::import', array('connection_eid' => $connection_eid, 'triggered_by' => $triggered_by));

        // create a process host to connect the store/engine and run the process
        $process_host = \Flexio\Jobs\ProcessHost::create($process_store, $process_engine);
        $process_host->run(false /*true: run in background*/);

        // return information about the process
        //$request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        //\Flexio\Api\Response::sendContent($process_store->get());


        // if we're not running in background mode, use the following to return
        // the pipe information

        // get the pipes for the connection
        $filter = array('owned_by' => $owner_user_eid, 'eid_status' => \Model::STATUS_AVAILABLE, 'parent_eid' => $connection->getEid());
        $pipes = \Flexio\Object\Pipe::list($filter);

        // return the info for the pipes that were just added
        $result = array();
        foreach ($pipes as $p)
        {
            if ($p->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_PIPE_READ) === false)
                continue;

            $properties = $p->get();

            // TODO: remove 'task' from pipe list to prepare for getting pipe
            // content from a separate call; leave in pipe item get() function
            unset($properties['task']);
            $result[] = $properties;
        }

        // return the result
        // TODO: re-enable tracking once we know what we want to store
        //$request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        //$request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    private static function cleanProperties(array $properties) : array
    {
        // TODO: clean setup_config?

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
