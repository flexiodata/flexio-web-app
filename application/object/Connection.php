<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-12-23
 *
 * @package flexio
 * @subpackage Object
 */


namespace Flexio\Object;


class Connection extends \Flexio\Object\Base
{
    // datastore locations
    const PROCESS_DATASTORE_1 = 'process.datastore.1';

    public function __construct()
    {
        $this->setType(\Model::TYPE_CONNECTION);
    }

    public static function getDatastoreConnectionEid()
    {
        $registry_model = \Flexio\Object\Store::getModel()->registry;
        $connection_eid = $registry_model->getString('', self::PROCESS_DATASTORE_1, false);

        if ($connection_eid === false)
            $connection_eid = self::createDatastoreConnection();

        if ($connection_eid === false)
            return false;

        if ($registry_model->setString('', self::PROCESS_DATASTORE_1, $connection_eid) === false)
            return false;

        return $connection_eid;
    }

    public function set($properties)
    {
        // TODO: add properties check

        // TODO: for now, don't forward model exception
        try
        {
            $this->clearCache();
            $connection_model = $this->getModel()->connection;
            $connection_model->set($this->getEid(), $properties);
        }
        catch (\Exception $e)
        {
        }

        return $this;
    }

    public function get()
    {
        if ($this->isCached() === true)
            return $this->properties;

        if ($this->populateCache() === true)
            return $this->properties;

        return false;
    }

    public function connect()
    {
        $properties = array();
        $properties['connection_status'] = \Model::CONNECTION_STATUS_UNAVAILABLE;

        $service = $this->getService();
        if (isset($service) && $service->isOk())
        {
            $res = $service->listObjects();
            if (is_array($res) === true)
                $properties['connection_status'] = \Model::CONNECTION_STATUS_AVAILABLE;
        }

        $this->clearCache(); // clear cache here since getService() populates the cache
        $connection_model = $this->getModel()->connection;
        $connection_model->set($this->getEid(), $properties);
        return $this;
    }

    public function disconnect()
    {
        $this->clearCache();

        // TODO: what values do we want to use to reset the params?
        $properties = array();
        $properties['connection_status'] = \Model::CONNECTION_STATUS_UNAVAILABLE;
        $properties['password'] = '';
        $properties['token'] = '';
        $properties['token_expires'] = \Flexio\System\System::getTimestamp(); // set the expiration to the current time

        $connection_model = $this->getModel()->connection;
        $connection_model->set($this->getEid(), $properties);
        return $this;
    }

    public function authenticate($params)
    {
        // TODO: this was moved from \Flexio\Api\Connection::authenticate() which functioned
        // somewhat like a static method so that everything was passed to the function;
        // now that this has been moved to the object, is there anyway to save some of
        // these in the object so we can make successive calls without sending everything?

        $service = isset_or($params['service'], '');
        $code = isset_or($params['code'], false);
        $state = isset_or($params['state'], false);

        // if we have a state variable, then unpack it
        if ($state !== false)
            $state = @json_decode(base64_decode($state),true);

        // set the service, based on either the supplied parameter or the
        // state variable if it exists
        if ($service === '')
            $service = isset_or($state['service'],'');

        // if the state param isn't initialized, pack up the service and eid
        if ($state === false)
        {
            $state = array(
                'service' => $service,
                'eid' => $this->getEid()
            );
            if (isset($params['redirect']))
                $state['redirect'] = $params['redirect'];
        }

        $auth_params = array();
        $auth_params['state'] = base64_encode(json_encode($state));
        if (isset($params['redirect']))
            $auth_params['redirect'] = $params['redirect'];
        if ($code !== false)
            $auth_params['code'] = $code;

        $response = null;
        switch ($service)
        {
            default:
                return false;

            case 'dropbox':
                $response = \Flexio\Services\Dropbox::create($auth_params);
                break;

            case 'googledrive':
                $response = \Flexio\Services\GoogleDrive::create($auth_params);
                break;

            case 'googlesheets':
                $response = \Flexio\Services\GoogleSheets::create($auth_params);
                break;
        }

        // if the service creation response is null, something went wrong
        if (is_null($response))
        {
            $properties = array();
            $properties['connection_status'] = \Model::CONNECTION_STATUS_ERROR;
            $this->set($properties);
            return false;
        }

        // if the service creation response is a string, then it's
        // an authorization uri, and we need to complete the process;
        // return the string
        if (is_string($response))
            return $response;

        // we're authenticated; get the token
        $service_object = $response;
        $tokens = $service_object->getTokens();

        $token_expires = null;
        if (!is_null($tokens['expires']) && $tokens['expires'] > 0)
            $token_expires = date("Y-m-d H:i:s", $tokens['expires']);

        $properties = array();
        $properties['token'] = $tokens['access_token'];
        $properties['refresh_token'] = $tokens['refresh_token'];
        $properties['token_expires'] = $token_expires;
        $properties['connection_status'] = \Model::CONNECTION_STATUS_AVAILABLE;
        $this->set($properties);

        return true;
    }

    public function getService()
    {
        // get the connection info
        $connection_info = $this->get();
        if ($connection_info === false)
            return false;

        // load the services from the services store: TODO: should introduce
        // namespaces for services and other classes so that the services
        // store can be more readily distinguished from the object store
        $service = \Flexio\Services\Store::load($connection_info);
        if ($service === false)
            return false;

        // for some of the connections, refresh the token
        $connection_type = isset_or($connection_info['connection_type'],'');
        switch ($connection_type)
        {
            case \Model::CONNECTION_TYPE_GOOGLEDRIVE:
            case \Model::CONNECTION_TYPE_GOOGLESHEETS:
            {
                // if access token was refreshed (via refresh token), write it out
                // to the tbl_connection table so that we don't refresh the access token
                // in every subsequent call

                $tokens = $service->getTokens();
                if (isset($tokens['access_token']) && isset($tokens['expires']) && $tokens['access_token'] != $connection_info['token'])
                {
                    $token_expires = date("Y-m-d H:i:s", $tokens['expires']);

                    $connection_params = array();
                    $connection_params['token'] = $tokens['access_token'];
                    $connection_params['token_expires'] = $token_expires;
                    $this->getModel()->connection->set($this->getEid(), $connection_params);
                }
            }
            break;
        }

        return $service;
    }

    private function isCached()
    {
        if ($this->properties === false)
            return false;

        return true;
    }

    private function clearCache()
    {
        $this->eid_status = false;
        $this->properties = false;
    }

    private function populateCache()
    {
        // get the properties
        $local_properties = $this->getProperties();
        if ($local_properties === false)
            return false;

        // save the properties
        $this->properties = $local_properties;
        $this->eid_status = $local_properties['eid_status'];
        return true;
    }

    private function getProperties()
    {
        $query = '
        {
            "eid" : null,
            "eid_type" : null,
            "eid_status" : null,
            "ename" : null,
            "name" : null,
            "description" : null,
            "host" : null,
            "port" : null,
            "username" : null,
            "database" : null,
            "password" : null,
            "token" : null,
            "refresh_token" : null,
            "token_expires" : null,
            "connection_type" : null,
            "connection_status" : null,
            "project='.\Model::EDGE_MEMBER_OF.'" : {
                "eid" : null,
                "eid_type" : "'.\Model::TYPE_PROJECT.'",
                "name" : null,
                "description" : null
            },
            "owned_by='.\Model::EDGE_OWNED_BY.'" : {
                "eid" : null,
                "eid_type" : "'.\Model::TYPE_USER.'",
                "user_name" : null,
                "first_name" : null,
                "last_name" : null,
                "email_hash" : null
            },
            "created_by='.\Model::EDGE_CREATED_BY.'" : {
                "eid" : null,
                "eid_type" : "'.\Model::TYPE_USER.'",
                "user_name" : null,
                "first_name" : null,
                "last_name" : null,
                "email_hash" : null
            },
            "created" : null,
            "updated" : null
        }
        ';

        // execute the query
        $query = json_decode($query);
        $properties = \Flexio\Object\Query::exec($this->getEid(), $query);
        if (!$properties)
            return false;

        return $properties;
    }

    private static function createDatastoreConnection()
    {
        $dbconfig = \Model::getDatabaseConfig();

        // if we don't have a datastore configuration, we can't create a default connection;
        // TODO: we'll want to add some ability to pull from a pool of available datastore
        // so we can have multiple servers; but right now, we just have one
        if (!isset($dbconfig['datastore_dbname']) || strlen($dbconfig['datastore_dbname']) === 0)
            return false;

        $params = array('host'            => $dbconfig['datastore_host'],
                        'port'            => $dbconfig['datastore_port'],
                        'database'        => $dbconfig['datastore_dbname'],
                        'username'        => $dbconfig['datastore_username'],
                        'password'        => $dbconfig['datastore_password'],
                        'connection_type' => \Model::CONNECTION_TYPE_POSTGRES
                        );

        $connection_eid = \Flexio\Object\Store::getModel()->create(\Model::TYPE_CONNECTION, $params);
        if ($connection_eid === false)
            return false;

        return $connection_eid;
    }

    private static function createDatastore($host, $port, $database, $username, $password)
    {
        // note: this function isn't used right now, but is here for reference

        $params = array();
        $params['host'] = $host;
        $params['port'] = $port;
        $params['database'] = $database;
        $params['username'] = $username;
        $params['password'] = $password;

        $db = \Flexio\Services\Postgres::create($params);
        if (!$db)
            return false;   // service not available

        try
        {
            $qdb = \Flexio\Base\DbUtil::quoteIdentifierIfNecessary($database);
            $db->execute("CREATE DATABASE $qdb ENCODING 'UTF8'");
        }
        catch (\Exception $e)
        {
            return false;
        }

        return true;
    }
}
