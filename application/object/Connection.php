<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
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
    public function __construct()
    {
    }

    public function __toString()
    {
        $object = array(
            'eid' => $this->getEid(),
            'eid_type' => $this->getType()
        );
        return json_encode($object);
    }

    public static function getEidFromName(string $owner, string $name)
    {
        $object = new static();
        $connection_model = $object->getModel()->connection;
        return $connection_model->getEidFromName($owner, $name);
    }

    public static function list(array $filter) : array
    {
        $object = new static();
        $connection_model = $object->getModel()->connection;
        $items = $connection_model->list($filter);

        $objects = array();
        foreach ($items as $i)
        {
            $o = new static();
            $o->properties =self::formatProperties($i);
            $o->setEid($o->properties['eid']);
            $objects[] = $o;
        }

        return $objects;
    }

    public static function load(string $eid) : \Flexio\Object\Connection
    {
        $object = new static();
        $connection_model = $object->getModel()->connection;
        $properties = $connection_model->get($eid);

        $object->setEid($eid);
        $object->clearCache();
        $object->properties = self::formatProperties($properties);
        return $object;
    }

    public static function create(array $properties = null) : \Flexio\Object\Connection
    {
        if (!isset($properties))
            $properties = array();

        // connection and setup info is stored as an encrypted json string, so this need to be encoded;
        // encryption will happen elsewhere
        if (isset($properties) && isset($properties['connection_info']))
            $properties['connection_info'] = json_encode($properties['connection_info']);
        if (isset($properties) && isset($properties['setup_template']))
            $properties['setup_template'] = json_encode($properties['setup_template']);
        if (isset($properties) && isset($properties['setup_config']))
            $properties['setup_config'] = json_encode($properties['setup_config']);

        $object = new static();
        $connection_model = $object->getModel()->connection;
        $local_eid = $connection_model->create($properties);

        $object->setEid($local_eid);
        $object->clearCache();

        return $object;
    }

    public function delete() : \Flexio\Object\Connection
    {
        // delete the connection
        $this->clearCache();
        $connection_model = $this->getModel()->connection;
        $connection_model->delete($this->getEid());
        return $this;
    }

    public function resetConnection() : \Flexio\Object\Connection
    {
        // TODO: test function
        $properties = json_encode(array('connection_info' => array()));
        $this->clearCache();
        $connection_model = $this->getModel()->connection;
        $connection_model->set($this->getEid(), $properties);
        return $this;
    }

    public function set(array $properties) : \Flexio\Object\Connection
    {
        // TODO: add properties check

        // handle replacing/adding partial parameters in the connection_info

        // TODO: temporarily disable setting partial connection_info; if
        // connection_info is passed, all the relevant info needs to be
        // passed; this is because we now have a keyring connection type
        // and so we need to be able to pass arbitrary lists of keys/values
        // without them accumulating

        // TODO: temporarily re-enable because disabling caused oauth
        // connection types to fail
        $properties = $this->getConnectionPropertiesToUpdate($properties);

        // encode the connection and setup info
        if (isset($properties) && isset($properties['connection_info']))
            $properties['connection_info'] = json_encode($properties['connection_info']);
        if (isset($properties) && isset($properties['setup_template']))
            $properties['setup_template'] = json_encode($properties['setup_template']);
        if (isset($properties) && isset($properties['setup_config']))
            $properties['setup_config'] = json_encode($properties['setup_config']);

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

    public function getType() : string
    {
        return \Model::TYPE_CONNECTION;
    }

    public function setOwner(string $user_eid) : \Flexio\Object\Connection
    {
        $properties = array('owned_by' => $user_eid);
        $this->set($properties);
        return $this;
    }

    public function getOwner() : string
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['owned_by']['eid'];
    }

    public function setStatus(string $status) : \Flexio\Object\Connection
    {
        if ($status === \Model::STATUS_DELETED)
            return $this->delete();

        $this->clearCache();
        $connection_model = $this->getModel()->connection;
        $result = $connection_model->set($this->getEid(), array('eid_status' => $status));
        return $this;
    }

    public function getStatus() : string
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['eid_status'];
    }

    public function connect() : \Flexio\Object\Connection
    {
        $properties = array();
        $properties['connection_status'] = \Model::CONNECTION_STATUS_UNAVAILABLE;

        $connected = $this->getService()->connect();
        if ($connected === true)
            $properties['connection_status'] = \Model::CONNECTION_STATUS_AVAILABLE;

        $this->set($properties);
        return $this;
    }

    public function disconnect() : \Flexio\Object\Connection
    {
        $this->clearCache();

        // get the service, disconnect, and get the update connection info
        // that has the secret credentials reset
        $service = $this->getService();
        $service->disconnect();
        $connection_info = $service->get();
        $connection_info = json_encode($connection_info);

        // update the connection info
        $properties = array();
        $properties['connection_status'] = \Model::CONNECTION_STATUS_UNAVAILABLE;
        $properties['expires'] = \Flexio\System\System::getTimestamp(); // set the expiration to the current time
        $properties['connection_info'] = $connection_info;
        $this->set($properties);

        return $this;
    }

    public function getService() // TODO: add function return type
    {
        // get the connection properties
        $connection_properties = $this->get();

        // load the services from the services store; for connections,
        // make sure the service also has the connection interface
        $connection_type = $connection_properties['connection_type'];
        $connection_info = $connection_properties['connection_info'];
        $service = \Flexio\Services\Factory::create($connection_type, $connection_info);
        if (!($service instanceof \Flexio\IFace\IConnection))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        // for oauth services, the access token may have been refreshed via
        // a refresh token, so these should be saved so that the access token
        // isn't refreshed in every subsequent call
        if ($service instanceof \Flexio\IFace\IOAuthConnection)
        {
            $tokens = $service->getTokens();
            $connection_info = $connection_properties['connection_info'];

            $info_changed = false;
            if (!isset($connection_info))
            {
                $info_changed = true;
            }
            else
            {
                if (($connection_info['access_token'] ?? false) !== $tokens['access_token'])
                    $info_changed = true;
                if (($connection_info['refresh_token'] ?? false) !== $tokens['refresh_token'])
                    $info_changed = true;
                if (($connection_info['expires'] ?? false) !== $tokens['expires'])
                    $info_changed = true;
            }

            if ($info_changed === true)
                $this->set([ 'connection_info' => $tokens]);
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

        // handle special case of parent_eid (input is parent_eid, but return type
        // in properties is an object with the key of 'parent')
        if (isset($connection_properties_current['parent']['eid']))
        {
            $connection_properties_current['parent_eid'] = $connection_properties_current['parent']['eid'];
            unset($connection_properties_current['parent']);
        }

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
                // if we're not on the connection object, simply update the value
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
        if (!$this->properties)
            return false;

        return true;
    }

    private function clearCache() : void
    {
        $this->properties = null;
    }

    private function populateCache() : void
    {
        $connection_model = $this->getModel()->connection;
        $properties = $connection_model->get($this->getEid());
        $this->properties = self::formatProperties($properties);
    }

    private static function formatProperties(array $properties) : array
    {
        $mapped_properties = \Flexio\Base\Util::mapArray(
            [
                "eid" => null,
                "eid_type" => null,
                "eid_status" => null,
                "parent" => null,
                "name" => null,
                "title" => null,
                "icon" => null,
                "description" => null,
                "connection_type" => null,
                "connection_mode" => null,
                "connection_status" => null,
                "connection_info" => null,
                "setup_template" => null,
                "setup_config" => null,
                "expires" => null,
                "owned_by" => null,
                "created_by" => null,
                "created" => null,
                "updated" => null
            ],
        $properties);

        // sanity check: if the data record is missing, then eid will be null
        if (!isset($mapped_properties['eid']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // expand the parent connection info
        $mapped_properties['parent'] = array(
            'eid' => $properties['parent_eid'] ?? "",
            'eid_type' => \Model::TYPE_CONNECTION
        );

        // unpack the connection info json
        $connection_info = @json_decode($mapped_properties['connection_info'],true);
        if ($connection_info !== false)
            $mapped_properties['connection_info'] = $connection_info;

        // unpack the setup template json
        $setup_template = @json_decode($mapped_properties['setup_template'],true);
        if ($setup_template !== false)
            $mapped_properties['setup_template'] = $setup_template;

        // unpack the setup config json
        $setup_config = @json_decode($mapped_properties['setup_config'],true);
        if ($setup_config !== false)
            $mapped_properties['setup_config'] = $setup_config;

        // expand the user info
        $mapped_properties['owned_by'] = array(
            'eid' => $properties['owned_by'],
            'eid_type' => \Model::TYPE_USER
        );
        $mapped_properties['created_by'] = array(
            'eid' => $properties['created_by'],
            'eid_type' => \Model::TYPE_USER
        );

        return $mapped_properties;
    }
}
