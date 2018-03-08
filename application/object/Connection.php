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


declare(strict_types=1);
namespace Flexio\Object;


class Connection extends \Flexio\Object\Base implements \Flexio\IFace\IObject
{
/*
    // TODO: create migration script to remove this from the registry
    const PROCESS_DATASTORE_1 = 'process.datastore.1';
    $registry_model = \Flexio\System\System::getModel()->registry;
    $connection_eid = $registry_model->getString('', self::PROCESS_DATASTORE_1);
*/

    public function __construct()
    {
        $this->setType(\Model::TYPE_CONNECTION);
    }

    public static function create(array $properties = null) : \Flexio\Object\Connection
    {
        // connection info is stored as an encrypted json string, so this need to be encoded; encryption will happen elsewhere
        if (isset($properties) && isset($properties['connection_info']))
            $properties['connection_info'] = json_encode($properties['connection_info']);

        $object = new static();
        $connection_model = $object->getModel()->connection;
        $local_eid = $connection_model->create($properties);

        $object->setEid($local_eid);
        $object->clearCache();

        return $object;
    }

    public function delete() : \Flexio\Object\Connection
    {
        $this->clearCache();
        $connection_model = $this->getModel()->connection;
        $connection_model->delete($this->getEid());
        return $this;
    }

    public function set(array $properties) : \Flexio\Object\Connection
    {
        // TODO: add properties check

        // handle replacing/adding partial parameters in the connection_info
        $properties = $this->getConnectionPropertiesToUpdate($properties);

        // encode the connection info
        if (isset($properties) && isset($properties['connection_info']))
            $properties['connection_info'] = json_encode($properties['connection_info']);

        $this->clearCache();
        $connection_model = $this->getModel()->connection;
        $connection_model->set($this->getEid(), $properties);
        return $this;
    }

    public function get() : array
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties;
    }

    public function connect() : \Flexio\Object\Connection
    {
        $properties = array();
        $properties['connection_status'] = \Model::CONNECTION_STATUS_UNAVAILABLE;

        $service = $this->getService();

        if ($service instanceof \Flexio\IFace\IConnection)
        {
            if ($service->authenticated())
                $properties['connection_status'] = \Model::CONNECTION_STATUS_AVAILABLE;
        }
         else
        {
            $properties['connection_status'] = \Model::CONNECTION_STATUS_AVAILABLE;
        }

        $this->set($properties);
        return $this;
    }

    public function disconnect() : \Flexio\Object\Connection
    {
        $this->clearCache();

        // TODO: what values do we want to use to reset the params?
        // if we want to reset anything within the connection_info, we need
        // to feed the info into the service, which should be responsible for
        // returning us valid initializated params
        $properties = array();
        $properties['connection_status'] = \Model::CONNECTION_STATUS_UNAVAILABLE;
        $properties['expires'] = \Flexio\System\System::getTimestamp(); // set the expiration to the current time

        $this->set($properties);
        return $this;
    }

    public function authenticate(array $params) // TODO: add function return type
    {
        // TODO: this was moved from \Flexio\Api\Connection::authenticate() which functioned
        // somewhat like a static method so that everything was passed to the function;
        // now that this has been moved to the object, is there anyway to save some of
        // these in the object so we can make successive calls without sending everything?

        $service = $params['service'] ?? '';
        $code = $params['code'] ?? false;
        $state = $params['state'] ?? false;

        // if we have a state variable, then unpack it
        if ($state !== false)
            $state = @json_decode(base64_decode($state),true);

        // set the service, based on either the supplied parameter or the
        // state variable if it exists
        if ($service === '')
            $service = $state['service'] ?? '';

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

            case 'box':
                $response = \Flexio\Services\Box::create($auth_params);
                break;

            case 'github':
                $response = \Flexio\Services\GitHub::create($auth_params);
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

        // DEBUG:
        // file_put_contents('/tmp/tokens.txt', "Tokens :" . json_encode($tokens)."\n", FILE_APPEND);

        $properties = array();
        $properties['connection_status'] = \Model::CONNECTION_STATUS_AVAILABLE;
        $properties['connection_info']['access_token'] = $tokens['access_token'];
        $properties['connection_info']['refresh_token'] = $tokens['refresh_token'];
        if (isset($tokens['expires']))
        {
            $properties['connection_info']['expires'] = $tokens['expires'];
        }
        $this->set($properties);

        return true;
    }

    public function getService() // TODO: add function return type
    {
        // get the connection properties
        $connection_properties = $this->get();

        // load the services from the services store
        $service = \Flexio\Services\Factory::create($connection_properties);
        if ($service === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        // for some of the connections, refresh the token
        $connection_type = $connection_properties['connection_type'] ?? '';
        switch ($connection_type)
        {
            case \Flexio\Services\Factory::TYPE_BOX:
            case \Flexio\Services\Factory::TYPE_GOOGLEDRIVE:
            case \Flexio\Services\Factory::TYPE_GOOGLESHEETS:
            {
                // if access token was refreshed (via refresh token), write it out
                // to the tbl_connection table so that we don't refresh the access token
                // in every subsequent call

                $tokens = $service->getTokens();

                $connection_info = $connection_properties['connection_info'];

                if (isset($tokens['access_token']) && isset($tokens['expires']) && isset($connection_info) &&
                    $tokens['access_token'] != $connection_info['access_token'])
                {
                    $connection_info['access_token'] = $tokens['access_token'];
                    $connection_info['refresh_token'] = $tokens['refresh_token'];
                    if (isset($tokens['expires']))
                    {
                        $connection_info['expires'] = $tokens['expires'];
                    }
                     else
                    {
                        unset($connection_info['expires']);
                    }

                    $this->set([ 'connection_info' => $connection_info]);

                    // DEBUG:
                    // file_put_contents('/tmp/tokens.txt', "Refresh:" . json_encode($tokens)."\n", FILE_APPEND);
                }
            }
            break;
        }

        return $service;
    }

    private function getConnectionPropertiesToUpdate(array $properties) : array
    {
        // takes a list of connection properties (including info) and returns
        // the key/values to set in the database; this function is aimed to
        // make it easier to set specific connection properties and specific
        // keys within the connection info

        // get a current list of connection properties
        $connection_properties_current = $this->get();
        $connection_info_current = $connection_properties_current['connection_info'];

        // iterate through the connection properties, and if they are specified
        // then include them in the connection properties to update
        $connection_properties_to_update = array();
        foreach ($properties as $key => $value)
        {
            // if we don't have a connection property, move on
            if (array_key_exists($key, $connection_properties_current) === false)
                continue;

            if ($key !== 'connection_info')
            {
                // if we're not on the the connection object, simply update the value
                $connection_properties_to_update[$key] = $value;
            }
             else
            {
                // if we're changing the connection_info, update the specific
                // values in this that are specified

                if (!is_array($value))
                    continue;

                $connection_info_updated = $connection_info_current; // initialize with entire set of values since we get one write for the whole payload
                foreach ($value as $connection_info_to_update_key => $connection_info_to_update_value)
                {
                    $connection_info_updated[$connection_info_to_update_key] = $connection_info_to_update_value;
                }

                $connection_properties_to_update[$key] = $connection_info_updated;
            }
        }

        return $connection_properties_to_update;
    }

    private function isCached() : bool
    {
        if ($this->properties === false)
            return false;

        return true;
    }

    private function clearCache() : bool
    {
        $this->eid_status = false;
        $this->properties = false;
        return true;
    }

    private function populateCache() : bool
    {
        // get the properties
        $local_properties = $this->getProperties();
        $this->properties = $local_properties;
        $this->eid_status = $local_properties['eid_status'];
        return true;
    }

    private function getProperties() : array
    {
        $query = '
        {
            "eid" : null,
            "eid_type" : null,
            "eid_status" : null,
            "ename" : null,
            "name" : null,
            "description" : null,
            "connection_type" : null,
            "connection_status" : null,
            "connection_info" : null,
            "expires" : null,
            "owned_by='.\Model::EDGE_OWNED_BY.'" : {
                "eid" : null,
                "eid_type" : "'.\Model::TYPE_USER.'",
                "user_name" : null,
                "first_name" : null,
                "last_name" : null,
                "email_hash" : null
            },
            "followed_by='.\Model::EDGE_FOLLOWED_BY.'" : [{
                "eid" : null,
                "eid_type" : "'.\Model::TYPE_USER.'",
                "user_name" : null,
                "first_name" : null,
                "last_name" : null,
                "email_hash" : null
            }],
            "created" : null,
            "updated" : null
        }
        ';

        // execute the query
        $query = json_decode($query);
        $properties = \Flexio\Object\Query::exec($this->getEid(), $query);

        // sanity check: if the data record is missing, then eid will be null
        if (!$properties || ($properties['eid'] ?? null) === null)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // unpack the connection info json
        $connection_info = @json_decode($properties['connection_info'],true);
        if ($connection_info !== false)
            $properties['connection_info'] = $connection_info;

        return $properties;
    }
}
