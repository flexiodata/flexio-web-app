<?php
/**
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2019-09-06
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


class LinkedIn implements \Flexio\IFace\IConnection, \Flexio\IFace\IFileSystem
{
    private $access_token = '';
    private $refresh_token = '';
    private $expires = 0;
    private $base_path = '';

    public static function create(array $params = null) // TODO: add return type; TODO: fix dual return types which is used for Oauth
    {
        if (!isset($params))
            return new self;

        return self::initialize($params);
    }

    public function authenticated() : bool
    {
        if (strlen($this->access_token) > 0)
            return true;

        return false;
    }

    ////////////////////////////////////////////////////////////
    // IFileSystem interface
    ////////////////////////////////////////////////////////////

    public function getFlags() : int
    {
        return 0;
    }

    public function list(string $path = '', array $options = []) : array
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return [];
    }

    public function getFileInfo(string $path) : array
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return [];
    }


    public function exists(string $path) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return false;
    }

    public function open($path) : \Flexio\IFace\IStream
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function createFile(string $path, array $properties = []) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return false;
    }

    public function createDirectory(string $path, array $properties = []) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return false;
    }

    public function unlink(string $path) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return false;
    }

    public function read(array $params, callable $callback)
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return false;
    }

    public function write(array $params, callable $callback)
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return false;
    }

    private function getRemotePath(string $path) : string
    {
        return \Flexio\Services\Util::mergePath($this->base_path, $path);
    }



    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    public function getTokens() : array
    {
        return [ 'access_token' => $this->access_token,
                 'refresh_token' => $this->refresh_token,
                 'expires' => $this->expires ];
    }

    public function getFileId(string $path) // TODO: add return type (: ?string)
    {
        $info = $this->internalGetFileInfo($path);
        if (!isset($info['id']))
            return null;
        return $info['id'];
    }

    private function connect() : bool
    {
        return true;
    }

    private static function initialize(array $params)
    {
        $client_id = $GLOBALS['g_config']->linkedin_client_id ?? '';
        $client_secret = $GLOBALS['g_config']->linkedin_client_secret ?? '';

        if (strlen($client_id) == 0 || strlen($client_secret) == 0)
            return null;

        $oauth_callback = '';
        if (isset($params['redirect']))
            $oauth_callback = $params['redirect'];

        // TODO: handle service error info

        // note: returns an authenticated object, an authorization uri,
        // or null if there's not enough information to move forward

        // if we have an access token, we can create the object with the
        // token; if we don't have an access token, we have to go through
        // an authentication process to get it; if we're beginning the
        // initialize process, the following will return a string with the
        // authentication url; when initialization is complete the following
        // will return an object with a serialized access token

        // STEP 1: if we have an access token and it's not expired, create an object
        // from the access token and return it
        if (isset($params['access_token']) && strlen($params['access_token']) > 0)
        {
            $curtime = time();
            $expires = $params['expires'] ?? 0;
            if ($curtime < $expires)
            {
                // access token is valid (not expired); use it
                $object = new self;
                $object->access_token = $params['access_token'];
                $object->refresh_token = $params['refresh_token'] ?? '';
                $object->expires = $expires;

                $object->base_path = $params['base_path'] ?? '';

                return $object;
            }
             else
            {
                // access token is expired -- try to refresh it
                $oauth = self::createService($oauth_callback);
                if (!$oauth)
                    return null;

                $access_token = $params['access_token'] ?? null;
                if (!isset($params['refresh_token']) || strlen($params['refresh_token']) == 0)
                    return null; // refresh token is missing
                $refresh_token = $params['refresh_token'];

                $token = new \OAuth\OAuth2\Token\StdOAuth2Token($access_token, $refresh_token);
                if ($expires > 0)
                    $token->setEndOfLife($params['expires']);

                try
                {
                    $token = $oauth->refreshAccessToken($token);
                    if (!$token)
                        return null;
                }
                catch (\OAuth\Common\Http\Exception\TokenResponseException $e)
                {
                    // this happens when offline
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE, "Could not refresh access token");
                }

                $object = new self;
                $object->expires = $token->getEndOfLife();
                $object->access_token = $token->getAccessToken();
                $object->refresh_token = $token->getRefreshToken();
                if ($object->refresh_token === null || strlen($object->refresh_token) == 0)
                    $object->refresh_token = $refresh_token;

                $object->base_path = $params['base_path'] ?? '';

                return $object;
            }
        }


        $oauth = self::createService($oauth_callback);
        if (!$oauth)
            return null;

        // STEP 3: if we have a code parameter, we have enough information
        // to authenticate and get the token; do so and return the object
        if (isset($params['code']))
        {
            $token = $oauth->requestAccessToken($params['code']);
            if (!$token)
                return null;
            $object = new self;
            $object->access_token = $token->getAccessToken();
            $object->refresh_token = $token->getRefreshToken();
            $object->expires = $token->getEndOfLife();
            if (is_null($object->refresh_token)) $object->refresh_token = '';

            $object->base_path = $params['base_path'] ?? '';

            return $object;
        }


        // STEP 4: we don't have a code parameter, so we need more
        // information to authenticate; make sure we have state info,
        // or we don't have enough information to complete the process
        if (!isset($params['state']))
            return null;

        // we have state info, return the authorization uri so we can
        // get a code and complete the process
        $additional_params = array(
            'state' => $params['state'],
            'approval_prompt' => 'force'
        );

        return $oauth->getAuthorizationUri($additional_params)->getAbsoluteUri();
    }

    private static function createService($oauth_callback) // TODO: add return type; s
    {
        $client_id = $GLOBALS['g_config']->linkedin_client_id ?? '';
        $client_secret = $GLOBALS['g_config']->linkedin_client_secret ?? '';

        if (strlen($client_id) == 0 || strlen($client_secret) == 0)
            return null;

        $service_factory = new \OAuth\ServiceFactory();
        $storage = new \OAuth\Common\Storage\Memory();

        // setup the credentials for the requests
        $credentials = new \OAuth\Common\Consumer\Credentials(
            $client_id,
            $client_secret,
            $oauth_callback
        );

        // instantiate the google service using the credentials,
        // http client and storage mechanism for the token
        $service = $service_factory->createService('linkedin', $credentials, $storage, array());
        if (!isset($service))
            return null;

        return $service;
    }
}
