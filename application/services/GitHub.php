<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams; Aaron L. Williams
 * Created:  2017-10-11
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


require_once dirname(dirname(__DIR__)) . '/library/phpoauthlib/src/OAuth/bootstrap.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Abstract.php';

class GitHub implements \Flexio\Services\IConnection
{
    ////////////////////////////////////////////////////////////
    // member variables
    ////////////////////////////////////////////////////////////

    private $is_ok = false;
    private $access_token = '';


    ////////////////////////////////////////////////////////////
    // IConnection interface
    ////////////////////////////////////////////////////////////

    public static function create(array $params = null) // TODO: fix dual return types which is used for Oauth
    {
        if (!isset($params))
            return new self;

        return self::initialize($params);
    }

    public function connect(array $params) : bool
    {
        return true;
    }

    public function isOk() : bool
    {
        return $this->is_ok;
    }

    public function close()
    {
        $this->is_ok = false;
        $this->access_token = '';
    }

    public function listObjects(string $path = '') : array
    {
        if (!$this->authenticated())
            return array();

        $files = array();
        return $files;
    }

    public function exists(string $path) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return false;
    }

    public function getInfo(string $path) : array
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return array();
    }

    public function read(array $params, callable $callback)
    {
    }

    public function write(array $params, callable $callback)
    {
    }


    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    public function getTokens() : array
    {
        return [ 'access_token' => $this->access_token,
                 'refresh_token' => '',
                 'expires' => 0  ];
    }

    private function authenticated() : bool
    {
        if (strlen($this->access_token) > 0)
            return true;

        return false;
    }

    private static function initialize(array $params)
    {
        $client_id = $GLOBALS['g_config']->github_client_id ?? '';
        $client_secret = $GLOBALS['g_config']->github_client_secret ?? '';

        if (strlen($client_id) == 0 || strlen($client_secret) == 0)
            return null;

        // TODO: handle service error info

        // note: returns an authenticated object, an authorization uri,
        // or null if there's not enough information to move forward

        // if we have an access token, we can create the object with the
        // token; if we don't have an access token, we have to go through
        // an authentication process to get it; if we're beginning the
        // initialize process, the following will return a string with the
        // authentication url; when initialization is complete the following
        // will return an object with a serialized access token

        // STEP 1: if we have an access token, create an object
        // from the access token and return it
        if (isset($params['access_token']))
        {
            $object = new self;
            $object->access_token = $params['access_token'];
            $object->is_ok = true;
            return $object;
        }

        // STEP 2: instantiate the service
        $service_factory = new \OAuth\ServiceFactory();
        $storage = new \OAuth\Common\Storage\Memory();

        // setup the credentials for the requests
        $oauth_callback = '';
        if (isset($params['redirect']))
            $oauth_callback = $params['redirect'];

        $credentials = new \OAuth\Common\Consumer\Credentials(
            $client_id,
            $client_secret,
            $oauth_callback
        );

        // instantiate the github service using the credentials,
        // http client and storage mechanism for the token
        $service = $service_factory->createService('GitHub', $credentials, $storage, array());
        if (!isset($service))
            return null;

        // STEP 3: if we have a code parameter, we have enough information
        // to authenticate and get the token; do so and return the object
        if (isset($params['code']))
        {
            $object = new self;
            $token = $service->requestAccessToken($params['code']);
            $object->access_token = $token->getAccessToken();
            $object->is_ok = true;
            return $object;
        }

        // we have state info, return the state information so we can
        // get a code and complete the process
        $additional_params = array(
            'state' => $params['state'] ?? ''
        );

        return $service->getAuthorizationUri($additional_params)->getAbsoluteUri();
    }
}
