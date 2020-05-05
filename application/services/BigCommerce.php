<?php
/**
 *
 * Copyright (c) 2020, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2020-02-21
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


class BigCommerce implements \Flexio\IFace\IConnection,
                             \Flexio\IFace\IOAuthConnection
{
    // connection info
    private $authorization_uri = '';
    private $access_token = '';
    private $refresh_token = '';
    private $expires = 0;

    public static function create(array $params = null) : \Flexio\Services\CapsuleCRM
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
        // see here for more information about using bigcommerce oauth2:
        // TODO: https://developer.bigcommerce.com/api-docs/getting-started/building-apps-bigcommerce/building-apps

        $client_id = $GLOBALS['g_config']->bigcommerce_client_id ?? '';
        $client_secret = $GLOBALS['g_config']->bigcommerce__client_secret ?? '';

        if (strlen($client_id) == 0 || strlen($client_secret) == 0)
            return false;

        if (isset($params['access_token']))
        {
            // if we have an access token, initialize the variables
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
                // access token is expired, get a new one
                $auth_token_url = ''; // TODO: fill out

                $post_data = array(
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $params['refresh_token'] ?? ''
                );
                $post_data = json_encode($post_data, JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT);

                $headers = array();
                $headers[] = 'Accept: application/json';
                $headers[] = 'Content-Type: application/json';

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $auth_token_url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

                $result = curl_exec($ch);
                curl_close($ch);

                $obj = @json_decode($result, true);

                if ($obj === null)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, _("Could not read access token from service"));

                $this->access_token = $obj['access_token'];
                $this->refresh_token = $obj['refresh_token'];
                $this->expires = $obj['expires_in'] + time(); // set end-of-life expiration in epoch/unix time
                return true;
            }
        }
        else if (isset($params['code']))
        {
            // if we have a code parameter, we have enough information
            // to authenticate and get the token; do so and return the object
            $auth_token_url = ''; // TODO: fill out

            $post_data = array(
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri' => $params['redirect'] ?? '',
                'grant_type' => 'authorization_code',
                'code' => $params['code']
            );
            $post_data = json_encode($post_data, JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT);

            $headers = array();
            $headers[] = 'Accept: application/json';
            $headers[] = 'Content-Type: application/json';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $auth_token_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

            $result = curl_exec($ch);
            curl_close($ch);

            $obj = @json_decode($result, true);

            if ($obj === null)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, _("Could not read access token from service"));

            $this->access_token = $obj['access_token'];
            $this->refresh_token = $obj['refresh_token'];
            $this->expires = $obj['expires_in'] + time(); // set end-of-life expiration in epoch/unix time
            return true;
        }
        else
        {
            // we have nothing; we need to redirect to the service's authorization URL
            $query_params = array(
                'client_id' => $client_id,
                'redirect_uri' => $params['redirect'] ?? '',
                'state' => $params['state'] ?? '',
                'scope' => 'read',
                'response_type' => 'code'
            );
            $query_str = http_build_query($query_params);
            $this->authorization_uri = '' . $query_str; // TODO: fill out
            return false;
        }

        return false;
    }
}
