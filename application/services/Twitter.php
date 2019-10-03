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

    public function getUserInfo() : array
    {
        return array(
            'username' => '',
            'first_name' => '',
            'last_name' => '',
            'email' => ''
        );
    }

    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    private function initialize(array $params) : bool
    {
        $client_id = $GLOBALS['g_config']->twitter_client_id ?? '';
        $client_secret = $GLOBALS['g_config']->twitter_client_secret ?? '';

        if (strlen($client_id) == 0 || strlen($client_secret) == 0)
            return false;

        $oauth_callback = '';
        if (isset($params['redirect']))
            $oauth_callback = $params['redirect'];

        if (isset($params['state']))
            $oauth_callback .= "?state=" . $params['state'];

        // 'code' = 'oauth_verifier'
        if (isset($_SESSION['twitter_oauth_token']) && isset($_SESSION['twitter_oauth_token_secret']) && isset($params['code']))
        {
            // create TwitterOAuth object
            $twitteroauth = new \Abraham\TwitterOAuth\TwitterOAuth($client_id, $client_secret, $_SESSION['twitter_oauth_token'], $_SESSION['twitter_oauth_token_secret']);

            unset($_SESSION['twitter_oauth_token']);
            unset($_SESSION['twitter_oauth_token_secret']);

            // request user token
            $token = $twitteroauth->oauth(
                'oauth/access_token', [
                    'oauth_verifier' => $params['code']
                ]
            );

            $this->access_token = $token['oauth_token'];
            $this->refresh_token = $token['oauth_token_secret'];
            $this->expires = 2147483640; // "never"

            return true;
        }
        else if (isset($params['access_token']) && strlen($params['access_token']) > 0 && isset($params['refresh_token']) && strlen($params['refresh_token']) > 0)
        {
            $this->access_token = $token['access_token'];
            $this->refresh_token = $token['refresh_token'];
            $this->expires = 2147483640; // "never"
            return true;
        }
        else
        {
            // create TwitterOAuth object
            $twitteroauth = new \Abraham\TwitterOAuth\TwitterOAuth($client_id, $client_secret);

            // request token of application
            $request_token = $twitteroauth->oauth(
                'oauth/request_token', [
                    'oauth_callback' => $oauth_callback
                ]
            );

            // throw exception if something gone wrong
            if($twitteroauth->getLastHttpCode() != 200) {
                throw new \Exception('There was a problem performing this request');
            }

            // save token of application to session
            $_SESSION['twitter_oauth_token'] = $request_token['oauth_token'];
            $_SESSION['twitter_oauth_token_secret'] = $request_token['oauth_token_secret'];

            // generate the URL to make request to authorize our application
            $url = $twitteroauth->url(
                'oauth/authorize', [
                    'oauth_token' => $request_token['oauth_token']
                ]
            );

            $this->authorization_uri = $url;

            return false;
        }
    }

    private static function createService($oauth_callback)
    {
        $client_id = $GLOBALS['g_config']->twitter_client_id ?? '';
        $client_secret = $GLOBALS['g_config']->twitter_client_secret ?? '';

        if (strlen($client_id) == 0 || strlen($client_secret) == 0)
            return null;

        //$service = new \Abraham\TwitterOAuth\TwitterOAuth($client_id, $client_secret, $access_token, $access_token_secret);
        $service = new \Abraham\TwitterOAuth\TwitterOAuth($client_id, $client_secret);
        return $service;

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
