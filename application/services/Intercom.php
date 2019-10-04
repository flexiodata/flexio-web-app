<?php
/**
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-10-03
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


class Intercom implements \Flexio\IFace\IConnection,
                          \Flexio\IFace\IOAuthConnection
{
    // connection info
    private $authorization_uri = '';
    private $access_token = '';
    private $refresh_token = '';
    private $expires = 0;

    public static function create(array $params = null) : \Flexio\Services\Intercom
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
        // this implementation uses oauth2-intercom
        // see following for more information:
        // - https://github.com/intercom/oauth2-intercom#usage
        // - https://github.com/thephpleague/oauth2-client#usage
        // - https://github.com/thephpleague/oauth2-client/blob/master/docs/providers/thirdparty.md
        // - https://developers.intercom.com/building-apps/docs/setting-up-oauth

        // note: refresh tokens; intercom doesn't use refresh tokens

        $client_id = $GLOBALS['g_config']->intercom_client_id ?? '';
        $client_secret = $GLOBALS['g_config']->intercom_client_secret ?? '';

        if (strlen($client_id) == 0 || strlen($client_secret) == 0)
            return false;

        // STEP 1: if we have an access token, create an object
        // from the access token and return it
        if (isset($params['access_token']))
        {
            $this->access_token = $params['access_token'];
            return true;
        }

        // STEP 2: create the service
        $oauthprovider = new \Intercom\OAuth2\Client\Provider\Intercom([
            'clientId'          => $client_id,
            'clientSecret'      => $client_secret,
            'redirectUri'       => $params['redirect'] ?? null,
            'state'             => $params['state'] ?? null
        ]);

        // STEP 3: if we have a code parameter, we have enough information
        // to authenticate and get the token; do so and return the object
        if (isset($params['code']))
        {
            $token = $oauthprovider->getAccessToken('authorization_code', [
                'code' => $params['code']
            ]);
            $this->access_token = $token->getToken();
            return true;
        }

        // STEP 4: generate the URL to make request to authorize our application
        $url = $oauthprovider->getAuthorizationUrl();

        // TODO: for some reason, the state parameter isn't being set;
        // replace it manually

        $url_base = strtok($url,'?');
        $url_query = parse_url($url, PHP_URL_QUERY);

        $url_query_params = array();
        parse_str($url_query, $url_query_params);
        $url_query_params['state'] = $params['state'];
        $url = $url_base . '?' . http_build_query($url_query_params);

        $this->authorization_uri = $url;

        return false;
    }
}
