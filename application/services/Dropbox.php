<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
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


class Dropbox implements \Flexio\IFace\IConnection,
                         \Flexio\IFace\IOAuthConnection,
                         \Flexio\IFace\IFileSystem
{
    // connection info
    private $authorization_uri = '';
    private $access_token = '';
    private $refresh_token = ''; // note: service doesn't use refresh tokens; here for consistency
    private $expires = 0;        // note: service doesn't user refresh tokens; tokens are usable until revocation
    private $base_path = '';

    public static function create(array $params = null) : \Flexio\Services\Dropbox
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
            'base_path'     => $this->base_path,
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
    // IFileSystem interface
    ////////////////////////////////////////////////////////////

    public function getFlags() : int
    {
        return 0;
    }

    public function list(string $path = '', array $options = []) : array
    {
        if (!$this->authenticated())
            return array();

        $file_limit = 10000; // limit return results to 10000

        $remote_path = $this->getRemotePath($path);

        $postdata = json_encode([
            "path" => $remote_path,
            "recursive" => false
        ]);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.dropboxapi.com/2/files/list_folder");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer '.$this->access_token));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $result = json_decode($result, true);

        $files = [];

        if (isset($result['error_summary']))
        {
            curl_close($ch);
            if (substr($result['error_summary'], 0, 15) == 'path/not_folder')
            {
                $arr = $this->getFileInfo($path);
                $arr['path'] = $path;
                return [ $arr ];
            }
            else if (substr($result['error_summary'], 0, 14) == 'path/not_found')
            {
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE, "The path could not be found");
            }
        }

        while (true)
        {
            if (isset($result['entries']))
            {
                foreach ($result['entries'] as $entry)
                {
                    $fullpath = '';
                    if (substr($path,0,1) != '/')
                        $fullpath .= '/';
                    $fullpath .= $path;
                    if (substr($fullpath, -1) != '/')
                        $fullpath .= '/';
                    $fullpath .= $entry['name'];

                    $files[] = array('id'=> $entry['id'] ?? null,
                                     'name' => $entry['name'],
                                     'path' => $fullpath,
                                     'size' => $entry['size'] ?? null,
                                     'modified' => $entry['client_modified'] ?? '',
                                     'hash' => $entry['content_hash'] ?? '',
                                     'type' => ($entry['.tag'] == 'folder' ? 'DIR' : 'FILE'));
                }
            }


            if (isset($result['has_more']) && isset($result['cursor']) && $result['has_more'])
            {
                $postdata = json_encode([
                    "cursor" => $result['cursor']
                ]);

                curl_setopt($ch, CURLOPT_URL, "https://api.dropboxapi.com/2/files/list_folder/continue");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer '.$this->access_token));
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                $result = json_decode($result, true);
            }
             else
            {
                break;
            }
        }

        curl_close($ch);

        return $files;
    }

    public function getFileInfo(string $path) : array
    {
        if (!$this->authenticated())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        $remote_path = $this->getRemotePath($path);

        $postdata = json_encode([
            "path" => $remote_path
        ]);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.dropboxapi.com/2/files/get_metadata");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer '.$this->access_token));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $result = json_decode($result, true);
        curl_close($ch);

        if (isset($result['error_summary']))
        {
            if (substr($result['error_summary'], 0, 14) == 'path/not_found')
            {
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE, "The path could not be found");
            }
            else
            {
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::GENERAL);
            }
        }


        $entry = $result;
        $content_type = \Flexio\Base\ContentType::getMimeType($remote_path);

        $result = array('id'=> $entry['id'] ?? null,
                     'name' => $entry['name'],
                     'size' => $entry['size'] ?? null,
                     'modified' => $entry['client_modified'] ?? '',
                     'hash' => $entry['content_hash'] ?? '',
                     'type' => ($entry['.tag'] == 'folder' ? 'DIR' : 'FILE'),
                     'content_type' => $content_type);

        return $result;
    }

    public function exists(string $path) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function createFile(string $path, array $properties = []) : bool
    {
        $this->write([ 'path' => $path ], function($length) { return false; });
        return true;
    }

    public function createDirectory(string $path, array $properties = []) : bool
    {
        $error_summary = '';
        if (!$this->internalCreateFolder($path, $error_summary))
        {
            if (substr($error_summary, 0, 14) == 'path/conflict/')
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED, "Object already exists");
                 else
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);
        }

        return true;
    }

    public function unlink(string $path) : bool
    {
        $remote_path = $this->getRemotePath($path);

        $postdata = json_encode(array('path' => $remote_path));

        // download the file
        $ch = curl_init();

        $filename = rawurlencode($path);
        curl_setopt($ch, CURLOPT_URL, "https://api.dropboxapi.com/2/files/delete_v2");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$this->access_token, "Content-Type: application/json"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);

        $result = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($httpcode >= 200 && $httpcode <= 299) ? true : false;
    }

    private function internalCreateFolder(string $path, string &$error_summary) : bool
    {
        $remote_path = $this->getRemotePath($path);

        $postdata = json_encode(array('path' => $remote_path, 'autorename' => false));

        // download the file
        $ch = curl_init();

        $filename = rawurlencode($path);
        curl_setopt($ch, CURLOPT_URL, "https://api.dropboxapi.com/2/files/create_folder_v2");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$this->access_token, "Content-Type: application/json"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);

        $result = curl_exec($ch);
        $result = @json_decode($result, true);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $error_summary = '';
        if (isset($result['error_summary']))
        {
            $error_summary = $result['error_summary'];
        }

        return ($httpcode >= 200 && $httpcode <= 299) ? true : false;
    }

    public function open($path) : \Flexio\IFace\IStream
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function read(array $params, callable $callback) // TODO: add return type
    {
        $path = $params['path'] ?? '';

        $remote_path = $this->getRemotePath($path);

        $dropbox_args = json_encode(array('path' => $remote_path));

        $http_response_code = false;
        $error_payload = '';

        // download the file
        $ch = curl_init();

        $filename = rawurlencode($path);
        curl_setopt($ch, CURLOPT_URL, "https://content.dropboxapi.com/2/files/download");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$this->access_token, "Dropbox-API-Arg: $dropbox_args", "Content-Type: "));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $data) use (&$callback, &$http_response_code, &$error_payload) {
            if ($http_response_code === false)
            {
                $http_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            }
            if ($http_response_code >= 400)
            {
                if (strlen($error_payload) < 65536)
                    $error_payload .= $data;
                return strlen($data);
            }
             else
            {
                $length = strlen($data);
                $callback($data);
                return $length;
            }
        });

        $result = curl_exec($ch);
        curl_close($ch);



        if (strlen($error_payload) > 0)
        {
            $result = @json_decode($error_payload, true);
            if (isset($result['error']))
            {
                $error_summary = $result['error_summary'] ?? '';
                if (substr($error_summary, 0, 14) == 'path/not_found')
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE, "The path could not be found");
                     else
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
            }
        }
    }

    public function write(array $params, callable $callback) // TODO: add return type
    {
        if (isset($params['structure']))
        {
            $callback = \Flexio\Services\Util::tableToCsvCallbackAdaptor($params['structure'], $callback);
        }

        $path = $params['path'] ?? '';
        $remote_path = $this->getRemotePath($path);

        $content_type = $params['content_type'] ?? \Flexio\Base\ContentType::STREAM;
        $filename = rawurlencode($remote_path);

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
                                          'commit' => array('path' => $remote_path, 'mode' => 'overwrite')));
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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }

        return true;
    }

    private function getRemotePath(string $path) : string
    {
        $merged_path = \Flexio\Services\Util::mergePath($this->base_path, $path);
        if (substr($merged_path, 0, 1) != '/')
            $merged_path = '/' . $merged_path;
        if ($merged_path == '/')
            return '';
        return $merged_path;
    }

    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    private function initialize(array $params = null) : bool
    {
        $client_id = $GLOBALS['g_config']->dropbox_client_id ?? '';
        $client_secret = $GLOBALS['g_config']->dropbox_client_secret ?? '';

        if (strlen($client_id) == 0 || strlen($client_secret) == 0)
            return false;

        // TODO: handle service error info

        // note: returns an authenticated object, an authorization uri,
        // or null if there's not enough information to move forward

        // if we have an access token, we can create the object with the
        // token; if we don't have an access token, we have to go through
        // an authentication process to get it; if we're beginning the
        // initialize process, the following will return a string with the
        // authentication url; when initialization is complete the following
        // will return an object with a serialized access token


        // STEP 1: set the non-oauth params
        $this->base_path = $params['base_path'] ?? '';

        // STEP 2: if we have an access token, create an object
        // from the access token and return it
        if (isset($params['access_token']))
        {
            $this->access_token = $params['access_token'];
            return true;
        }

        // STEP 3: instantiate the service
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
        $oauth = $service_factory->createService('dropbox', $credentials, $storage, array());
        if (!isset($oauth))
            return false;

        // STEP 3: if we have a code parameter, we have enough information
        // to authenticate and get the token; do so and return the object
        if (isset($params['code']))
        {
            $token = $oauth->requestAccessToken($params['code']);
            $this->access_token = $token->getAccessToken();
            return true;
        }

        // we have state info, return the state information so we can
        // get a code and complete the process
        $additional_params = array(
            'state' => $params['state'] ?? ''
        );

        $this->authorization_uri = $oauth->getAuthorizationUri($additional_params)->getAbsoluteUri();
        return false;
    }
}
