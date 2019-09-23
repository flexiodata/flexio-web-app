<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2018-05-14
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


class Gmail implements \Flexio\IFace\IConnection,
                       \Flexio\IFace\IOAuthConnection
{
    // connection info
    private $authorization_uri = '';
    private $access_token = '';
    private $refresh_token = '';
    private $expires = 0;

    public static function create(array $params = null) : \Flexio\Services\Gmail
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
        return $this->get();
    }

    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    public function retrieveEmailAddress() : string
    {
        if (!$this->authenticated())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CONNECTION_FAILED);

        $url = "https://www.googleapis.com/gmail/v1/users/me/profile";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$this->access_token));
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        $result = @json_decode($result, true);

        return $result['emailAddress'];
    }

    private function initialize(array $params = null) : bool
    {
        $client_id = $GLOBALS['g_config']->googledrive_client_id ?? '';
        $client_secret = $GLOBALS['g_config']->googledrive_client_secret ?? '';

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

        // STEP 3: if we have a code parameter, we have enough information
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
                $object->refresh_token = '';
            }

            return true;
        }


        // STEP 4: we don't have a code parameter, so we need more
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

    private static function createService($oauth_callback) : ?\OAuth\OAuth2\Service\Google
    {
        $client_id = $GLOBALS['g_config']->gmail_client_id ?? '';
        $client_secret = $GLOBALS['g_config']->gmail_client_secret ?? '';

        if (strlen($client_id) == 0 || strlen($client_secret) == 0)
            return null;

        $service_factory = new \OAuth\ServiceFactory();
        $storage = new \OAuth\Common\Storage\Memory();

        $credentials = new \OAuth\Common\Consumer\Credentials(
            $client_id,
            $client_secret,
            $oauth_callback
        );

        // instantiate the google service using the credentials,
        // http client and storage mechanism for the token
        $service = $service_factory->createService('google', $credentials, $storage, array('gmail', 'gmail_imap_smtp'));
        if (!isset($service))
            return null;

        // we want offline access (permanent until the owner revokes it) for running jobs
        $service->setAccessType('offline');
        return $service;
    }
}

