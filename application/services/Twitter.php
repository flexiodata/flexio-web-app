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


class Twitter implements \Flexio\IFace\IConnection,
                         \Flexio\IFace\IOAuthConnection
{
    // connection info
    private $authorization_uri = '';
    private $access_token = '';
    private $refresh_token = '';
    private $expires = 0;

    public static function create(array $params = null) : \Flexio\Services\Twitter
    {
        $obj = new self;
        $obj->initialize($params);
        return $obj;
    }

    ////////////////////////////////////////////////////////////
    // IConnection interface
    ////////////////////////////////////////////////////////////

    public function connect() : bool
    {
        if ($this->authenticated() === false)
            return false;

        // TODO: check connection with basic api request

        return true;
    }

    public function disconnect() : void
    {
        // reset oauth credential info
        $this->authorization_uri = '';
        $this->access_token = '';
        $this->refresh_token = '';
        $this->expires = 0;
    }

    public function authenticated() : bool
    {
        if (strlen($this->access_token) > 0)
            return true;

        return false;
    }

    public function get() : array
    {
        $properties = array(
            'access_token'  => $this->access_token,
            'refresh_token' => $this->refresh_token,
            'expires'       => $this->expires
        );

        return $properties;
    }

    ////////////////////////////////////////////////////////////
    // OAuth interface
    ////////////////////////////////////////////////////////////

    public function getAuthorizationUri() : string
    {
        return $this->authorization_uri;
    }

    public function getTokens() : array
    {
        $properties = array(
            'access_token'  => $this->access_token,
            'refresh_token' => $this->refresh_token,
            'expires'       => $this->expires
        );

        return $properties;
    }

    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    public function getFileId(string $path) // TODO: add return type (: ?string)
    {
        $info = $this->internalGetFileInfo($path);
        if (!isset($info['id']))
            return null;
        return $info['id'];
    }

    private function initialize(array $params) : bool
    {
        $client_id = $GLOBALS['g_config']->twitter_client_id ?? '';
        $client_secret = $GLOBALS['g_config']->twitter_client_secret ?? '';

        if (strlen($client_id) == 0 || strlen($client_secret) == 0)
            return false;

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

                $this->access_token = $params['access_token'];
                $this->refresh_token = $params['refresh_token'] ?? '';
                $this->expires = $expires;

                return true;
            }
             else
            {
                // access token is expired -- try to refresh it
                $oauth = self::createService($oauth_callback);
                if (!$oauth)
                    return false;

                $access_token = $params['access_token'] ?? null;
                if (!isset($params['refresh_token']) || strlen($params['refresh_token']) == 0)
                    return false; // refresh token is missing
                $refresh_token = $params['refresh_token'];

                $token = new \OAuth\OAuth2\Token\StdOAuth2Token($access_token, $refresh_token);
                if ($expires > 0)
                    $token->setEndOfLife($params['expires']);

                try
                {
                    $token = $oauth->refreshAccessToken($token);
                    if (!$token)
                        return false;
                }
                catch (\OAuth\Common\Http\Exception\TokenResponseException $e)
                {
                    // this happens when offline
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE, "Could not refresh access token");
                }

                $this->expires = $token->getEndOfLife();
                $this->access_token = $token->getAccessToken();
                $this->refresh_token = $token->getRefreshToken();
                if ($this->refresh_token === null || strlen($this->refresh_token) == 0)
                    $this->refresh_token = $refresh_token;

                return true;
            }
        }

        $oauth = self::createService($oauth_callback);
        if (!$oauth)
            return false;

        // STEP 2: if we have a code parameter, we have enough information
        // to authenticate and get the token; do so and return the object
        if (isset($params['code']))
        {
            $token = $oauth->requestAccessToken($params['code']);
            if (!$token)
                return false;

            $this->access_token = $token->getAccessToken();
            $this->refresh_token = $token->getRefreshToken();
            $this->expires = $token->getEndOfLife();
            if (is_null($this->refresh_token))
            {
                $this->refresh_token = '';
            }

            return true;
        }

        // STEP 3: we don't have a code parameter, so we need more
        // information to authenticate; make sure we have state info,
        // or we don't have enough information to complete the process
        if (!isset($params['state']))
            return false;

        // we have state info, return the authorization uri so we can
        // get a code and complete the process
        $additional_params = array(
            'state' => $params['state'],
            'approval_prompt' => 'force'
        );

        $this->authorization_uri = $oauth->getAuthorizationUri($additional_params)->getAbsoluteUri();
        return false;
    }

    private static function createService($oauth_callback) : ?\OAuth\OAuth2\Service\Twitter
    {
        $client_id = $GLOBALS['g_config']->twitter_client_id ?? '';
        $client_secret = $GLOBALS['g_config']->twitter_client_secret ?? '';

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

        // instantiate the twitter service using the credentials,
        // http client and storage mechanism for the token
        $service = $service_factory->createService('twitter', $credentials, $storage, array());
        if (!isset($service))
            return null;

        return $service;
    }
}
