<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams; Aaron L. Williams
 * Created:  2015-03-03
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


require_once dirname(dirname(__DIR__)) . '/library/phpoauthlib/src/OAuth/bootstrap.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Abstract.php';

class Dropbox implements \Flexio\Services\IConnection
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

        $file_limit = 10000; // limit return results to 10000

        // strip any trailing slash
        if (substr($path, -1) == '/')
            $path = substr($path, 0, strlen($path)-1);

        $postdata = json_encode(array(
            "path" => $path
        ));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.dropboxapi.com/2/files/list_folder");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer '.$this->access_token));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result, true);

        $files = [];


        if (isset($result['entries']))
        {

            foreach ($result['entries'] as $entry)
            {
                $fullpath = $path;
                if (substr($fullpath, -1) != '/')
                    $fullpath .= '/';
                $fullpath .= $entry['name'];

                $files[] = array('id'=> $entry['id'] ?? null,
                                 'name' => $entry['name'],
                                 'path' => $fullpath,
                                 'size' => $entry['size'] ?? '',
                                 'modified' => $entry['client_modified'] ?? '',
                                 'is_dir' => ($entry['.tag'] == 'folder' ? true : false));
            }
        }

        return $files;
    }

    public function exists(string $path) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return false;
    }

    public function read(array $params, callable $callback)
    {
        $path = $params['path'] ?? '';


        $dropbox_args = json_encode(array('path' => $path));

        // download the file
        $ch = curl_init();

        $filename = rawurlencode($path);
        curl_setopt($ch, CURLOPT_URL, "https://content.dropboxapi.com/2/files/download");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$this->access_token, "Dropbox-API-Arg: $dropbox_args", "Content-Type: "));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $data) use (&$callback) {
            $length = strlen($data);
            $callback($data);
            return $length;
        });

        $result = curl_exec($ch);
        curl_close($ch);
    }

    public function write(array $params, callable $callback)
    {
        $path = $params['path'] ?? '';
        $content_type = $params['content_type'] ?? \Flexio\Base\ContentType::MIME_TYPE_STREAM;
        $filename = rawurlencode($path);

        // upload/write the file
        $ch = curl_init();

        $dropbox_args = json_encode(array('close' => false));
        curl_setopt($ch, CURLOPT_URL, "https://content.dropboxapi.com/2/files/upload_session/start");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$this->access_token, "Dropbox-API-Arg: $dropbox_args", "Content-Type: application/octet-stream", "Content-Length: 0" ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        $result = curl_exec($ch);

        $result = @json_decode($result, true);
        $session_id = $result['session_id'] ?? '';
        if (strlen($session_id) == 0)
            return false; // TODO: throw exception?

        $offset = 0;
        while (true)
        {
            $buf = $callback(65536);

            if ($buf === false)
                break;

            $buflen = strlen($buf);

            if ($buflen > 0)
            {
                $dropbox_args = json_encode(array('close' => false, 'cursor' => array('session_id' => $session_id, 'offset' => $offset)));
                curl_setopt($ch, CURLOPT_URL, "https://content.dropboxapi.com/2/files/upload_session/append_v2");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $buf);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$this->access_token, "Dropbox-API-Arg: $dropbox_args", "Content-Type: application/octet-stream", "Content-Length: $buflen" ));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

                $result = curl_exec($ch);
                $offset += $buflen;

                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($httpcode != 200 && $httpcode != 201 && $httpcode != 202)
                    return false; // bad status code; TODO: throw exception?
            }
        }


        $dropbox_args = json_encode(array('cursor' => array('session_id' => $session_id, 'offset' => $offset),
                                          'commit' => array('path' => $path, 'mode' => 'overwrite')));
        curl_setopt($ch, CURLOPT_URL, "https://content.dropboxapi.com/2/files/upload_session/finish");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$this->access_token, "Dropbox-API-Arg: $dropbox_args", "Content-Type: application/octet-stream", "Content-Length: 0" ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        $result = curl_exec($ch);
        $result = @json_decode($result, true);

        curl_close($ch);

        if (isset($result['error']))
        {
            // error occurred
            $msg = $result['error_summary'] ?? '';
            return false;  // error occurred; TODO: throw exception?
        }

        return true;

/*
        $dropbox_args = json_encode(array('path' => $path, 'mode' => 'overwrite'));

        // upload/write the file
        $ch = curl_init();

        $filename = rawurlencode($path);
        curl_setopt($ch, CURLOPT_URL, "https://content.dropboxapi.com/2/files/upload");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$this->access_token, "Dropbox-API-Arg: $dropbox_args", "Content-Type: application/octet-stream", "Content-Length: 500" ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_READFUNCTION, function($ch, $fp, $length) use (&$callback) {
            $res = $callback($length);
            if ($res === false) return '';
            return $res;
        });
        $result = curl_exec($ch);

        curl_close($ch);
*/

    }


    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    public function getTokens() : array
    {
        return [ 'access_token' => $this->access_token,
                 'refresh_token' => '',           // dropbox doesn't use refresh tokens
                 'expires' => 0  ];               // dropbox tokens are usable until revocation
    }

    private function authenticated() : bool
    {
        if (strlen($this->access_token) > 0)
            return true;

        return false;
    }

    private static function initialize(array $params)
    {
        $client_id = $GLOBALS['g_config']->dropbox_client_id ?? '';
        $client_secret = $GLOBALS['g_config']->dropbox_client_secret ?? '';

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

        // instantiate the dropbox service using the credentials,
        // http client and storage mechanism for the token
        $service = $service_factory->createService('dropbox', $credentials, $storage, array());
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
