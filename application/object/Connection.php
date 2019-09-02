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

        // connection info is stored as an encrypted json string, so this need to be encoded;
        // encryption will happen elsewhere
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

        try
        {
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
        }
        catch(\Flexio\Base\Exception $e)
        {
            // service couldn't be created, perhaps because of invalid credentials;
            // connection is unavailable
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
        // returning us valid initializated params;
        // see 08.04-connecxtion.php: "TODO: we need a better way of resetting credentials"

        $properties = array();
        $properties['connection_status'] = \Model::CONNECTION_STATUS_UNAVAILABLE;
        $properties['expires'] = \Flexio\System\System::getTimestamp(); // set the expiration to the current time

        $this->set($properties);
        return $this;
    }

    public function sync() : array
    {
        // syncs pipes in a mounted a connection with the source files in the connection

        // if the connection mode isn't a function/mount, we're done
        $connection_mode = $this->properties['connection_mode'];

        // TODO: uncomment; commented out for testing until mounts are exposed
        // in the UI
        //if ($connection_mode !== \Model::CONNECTION_MODE_FUNCTION)
        //    return array();

        // STEP 1: get the pipes associated with this connection
        $owner_user_eid = $this->getOwner(); // sanity check
        $connection_eid = $this->getEid();
        $filter = array('owned_by' => $owner_user_eid, 'parent_eid' => $connection_eid, 'eid_status' => \Model::STATUS_AVAILABLE);
        $pipe_model = $this->getModel()->pipe;
        $pipe_items = $pipe_model->list($filter);

        $pipe_item_info = array();
        foreach ($pipe_items as $item)
        {
            $item_info = array();
            $item_info['eid'] = $item['eid'];
            $item_info['name'] = $item['name'];
            $pipe_item_info[$item_info['name']] = $item_info;
        }

        // STEP 2: get the files in the connection
        $service = $this->getService();
        $connection_items = $service->list();

        $connection_item_info= array();
        foreach ($connection_items as $item)
        {
            $item_info = $item;

            // TODO: add constant for "FILE"
            if ($item_info['type'] !== "FILE")
                continue;

            // filter out items that aren't flexio tasks, or javascript/python scripts
            // TODO: adjust filter criteria?
            $name = $item_info['path'];
            $ext = strtolower(\Flexio\Base\File::getFileExtension($name));
            if ($ext !== 'flexio' && $ext !== 'py' && $ext !== 'js')
                continue;

            $connection_item_info[$item_info['name']] = $item_info;
        }

        // STEP 4: delete the pipes that are no longer in the connection
        // TODO: for now, delete all the pipes for the connection
        foreach ($pipe_item_info as $key => $value)
        {
            $pipe_model = $this->getModel()->pipe;
            $pipe_model->delete($value['eid']);
        }

        // STEP 5: create pipes for new items in the connection
        // TODO: for now, create all new pipes for the connection
        foreach ($connection_item_info as $key => $value)
        {
            $filenamebase = \Flexio\Base\File::getFilename($value['name']);
            $pipe_name = \Flexio\Base\Identifier::makeValid($filenamebase);

            $pipe_params = array();
            $pipe_params['parent_eid'] = $this->getEid();
            $pipe_params['name'] = $pipe_name;
            $pipe_params['description'] = "Created from '" . $value['name'] . "' in '" . $this->get()['name'] . "' connection.";
            $pipe_params['deploy_mode'] = 'R';
            $pipe_params['deploy_api'] = 'A';
            $pipe_params['deploy_schedule'] = 'I';
            $pipe_params['deploy_email'] = 'I';
            $pipe_params['deploy_ui'] = 'I';
            $pipe_params['owned_by'] = $this->get()['owned_by']['eid'];
            $pipe_params['created_by'] = $this->get()['created_by']['eid'];

            // get the file extension and set the language
            $language = false;
            $ext = strtolower(\Flexio\Base\File::getFileExtension($value['path']));
            if ($ext === 'flexio')
                $language = 'flexio'; // execute content as a JSON pipe
            if ($ext === 'py')
                $language = 'python';
            if ($ext === 'js')
                $language = 'nodejs';

            if ($language === false)
                continue;

            $content = '';
            $this->getService()->read(['path' => $value['path']], function($data) use (&$content) {
                $content .= $data;
            });

            // set the task info
            if ($language === 'flexio')
            {
                $task = array();
                $pipe = @json_decode($content,true);
                if (!is_null($pipe))
                    $task = $pipe['task'] ?? array();
                $pipe_params['task'] = $task;
            }
            else
            {
                $execute_job_params = array();
                $execute_job_params['op'] = 'execute'; // set the execute operation so this doesn't need to be supplied
                $execute_job_params['lang'] = $language; // TODO: set the language from the extension
                $execute_job_params['code'] = base64_encode($content); // encode the script

                $pipe_params['task'] = [
                    "op" => "sequence",
                    "items" => [
                        $execute_job_params
                    ]
                ];
            }

            // create the new pipe
            $item = \Flexio\Object\Pipe::create($pipe_params);
        }

        // STEP 6: update pipes whose content in the connection has changed
        // TODO: update pipes when pipes are no longer always deleted/recreated

        return $connection_item_info;
    }

    public function authenticateInit(array $params) : string
    {
        // a redirect url is required
        if (!isset($params['redirect']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $connection_info = $this->get();
        $connection_type = $connection_info['connection_type'];
        $redirect_url = $params['redirect'];

        // pack up the eid
        $state = array(
            'eid' => $this->getEid()
        );

        $auth_params = array();
        $auth_params['state'] = base64_encode(json_encode($state));
        $auth_params['redirect'] = $redirect_url;

        $response = null;
        switch ($connection_type)
        {
            default:
                break;

            case \Flexio\Services\Factory::TYPE_DROPBOX:
                $response = \Flexio\Services\Dropbox::create($auth_params);
                break;

            case \Flexio\Services\Factory::TYPE_BOX:
                $response = \Flexio\Services\Box::create($auth_params);
                break;

            case \Flexio\Services\Factory::TYPE_GITHUB:
                $response = \Flexio\Services\GitHub::create($auth_params);
                break;

            case \Flexio\Services\Factory::TYPE_GMAIL:
                $response = \Flexio\Services\Gmail::create($auth_params);
                break;

            case \Flexio\Services\Factory::TYPE_GOOGLEDRIVE:
                $response = \Flexio\Services\GoogleDrive::create($auth_params);
                break;

            case \Flexio\Services\Factory::TYPE_GOOGLESHEETS:
                $response = \Flexio\Services\GoogleSheets::create($auth_params);
                break;

            case \Flexio\Services\Factory::TYPE_GOOGLECLOUDSTORAGE:
                $response = \Flexio\Services\GoogleCloudStorage::create($auth_params);
                break;
        }

        // if the service creation response is null, something went wrong
        if (is_null($response))
        {
            $properties = array();
            $properties['connection_status'] = \Model::CONNECTION_STATUS_ERROR;
            $this->set($properties);
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CONNECTION_FAILED);
        }

        // TODO: right now, the service creation function for each of the services
        // can return multiple types; however, for this phase of the authentication,
        // the response should always be a string; should split out this phase of
        // the authentication into a separate function call for each service; for now,
        // because this function enforces a return type, downstream exception handling
        // will catch the error if the wrong type is returned
        return $response;
    }

    public function authenticateCallback(array $params) // TODO: add function return type
    {
        // TODO: is there anyway to save some of these in the object so we can make
        // successive calls without sending everything?

        // get the connection properties
        $connection_properties = $this->get();
        $connection_type = $connection_properties['connection_type'] ?? '';

        $code = $params['code'] ?? false;
        $state = $params['state'] ?? false;

        $auth_params = array();
        $auth_params['state'] = base64_encode(json_encode($state));
        if (isset($params['redirect']))
            $auth_params['redirect'] = $params['redirect'];
        if ($code !== false)
            $auth_params['code'] = $code;

        $response = null;
        switch ($connection_type)
        {
            default:
                return false;

            case \Flexio\Services\Factory::TYPE_DROPBOX:
                $response = \Flexio\Services\Dropbox::create($auth_params);
                break;

            case \Flexio\Services\Factory::TYPE_BOX:
                $response = \Flexio\Services\Box::create($auth_params);
                break;

            case \Flexio\Services\Factory::TYPE_GITHUB:
                $response = \Flexio\Services\GitHub::create($auth_params);
                break;

            case \Flexio\Services\Factory::TYPE_GMAIL:
                $response = \Flexio\Services\Gmail::create($auth_params);
                break;

            case \Flexio\Services\Factory::TYPE_GOOGLEDRIVE:
                $response = \Flexio\Services\GoogleDrive::create($auth_params);
                break;

            case \Flexio\Services\Factory::TYPE_GOOGLESHEETS:
                $response = \Flexio\Services\GoogleSheets::create($auth_params);
                break;

            case \Flexio\Services\Factory::TYPE_GOOGLECLOUDSTORAGE:
                $response = \Flexio\Services\GoogleCloudStorage::create($auth_params);
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

        if ($connection_type == 'gmail')
        {
            $properties['connection_info']['email'] = $service_object->retrieveEmailAddress();
        }

        $this->set($properties);

        return true;
    }

    public function getAccessToken()
    {
        $tokens = $this->getService()->getTokens();
        return $tokens['access_token'];
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
                "name" => null,
                "title" => null,
                "description" => null,
                "connection_type" => null,
                "connection_mode" => null,
                "connection_status" => null,
                "connection_info" => null,
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

        // unpack the connection info json
        $connection_info = @json_decode($mapped_properties['connection_info'],true);
        if ($connection_info !== false)
            $mapped_properties['connection_info'] = $connection_info;

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
