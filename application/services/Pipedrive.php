<?php
/**
 *
 * Copyright (c) 2019, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   David Z. Williams
 * Created:  2019-11-21
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


class Pipedrive implements \Flexio\IFace\IConnection,
                         \Flexio\IFace\IOAuthConnection
{
    // connection info
    private $authorization_uri = '';
    private $api_base_uri = '';
    private $access_token = '';
    private $refresh_token = '';
    private $expires = 0;

    public static function create(array $params = null) : \Flexio\Services\Pipedrive
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
        $this->api_base_uri = '';
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
            'api_base_uri'  => $this->api_base_uri,
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
        $client_id = $GLOBALS['g_config']->pipedrive_client_id ?? '';
        $client_secret = $GLOBALS['g_config']->pipedrive_client_secret ?? '';

        if (strlen($client_id) == 0 || strlen($client_secret) == 0)
            return false;

        // see following for more information:
        // https://pipedrive.readme.io/docs/marketplace-oauth-authorization

        if (isset($params['access_token']))
        {
            // if we have an access token, initialize the variables
            $curtime = time();
            $expires = $params['expires'] ?? 0;
            if ($curtime < $expires)
            {
                // access token is valid (not expired); use it
                $this->api_base_uri = $params['api_base_uri'] ?? '';
                $this->access_token = $params['access_token'] ?? '';
                $this->refresh_token = $params['refresh_token'] ?? '';
                $this->expires = $expires;

                return true;
            }
             else
            {
                // access token is expired, get a new one
                $auth_token_url = 'https://oauth.pipedrive.com/oauth/token';

                $post_data = array(
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $params['refresh_token'] ?? ''
                );
                $post_data = http_build_query($post_data);

                $headers = array();
                $headers[] = 'Authorization: Basic ' . base64_encode($client_id . ':' . $client_secret);
                $headers[] = 'Content-Type: application/x-www-form-urlencoded';

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

                $this->api_base_uri = $params['api_base_uri'] ?? '';
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

            $api_base_uri = $params['api_base_uri'] ?? '';
            $auth_token_url = 'https://oauth.pipedrive.com/oauth/token';

            $post_data = array(
                'grant_type' => 'authorization_code',
                'code' => $params['code'],
                'redirect_uri' => $params['redirect'] ?? ''
            );
            $post_data = http_build_query($post_data);

            $headers = array();
            $headers[] = 'Authorization: Basic ' . base64_encode($client_id . ':' . $client_secret);
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';

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

            $this->api_base_uri = $params['api_base_uri'] ?? '';
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
                'state' => $params['state'] ?? ''
            );
            $query_str = http_build_query($query_params);

            $this->authorization_uri = 'https://oauth.pipedrive.com/oauth/authorize?' . $query_str;
            $this->api_base_uri = $params['api_base_uri'] ?? '';
            return false;
        }

        return false;
    }
}
