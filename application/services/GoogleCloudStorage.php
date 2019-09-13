<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2018-06-09
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


class GoogleCloudStorage implements \Flexio\IFace\IConnection, \Flexio\IFace\IFileSystem
{
    private $authorization_uri = '';
    private $access_token = '';
    private $refresh_token = '';
    private $expires = 0;
    private $bucket = '';
    private $base_path = '';

    public static function create(array $params = null) : \Flexio\Services\GoogleCloudStorage
    {
        $obj = new self;
        $obj->initialize($params);
        return $obj;
    }

    public function authenticated() : bool
    {
        if (strlen($this->access_token) > 0)
            return true;

        return false;
    }

    public function getAuthorizationUri() : string
    {
        return $this->authorization_uri;
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

        if (strlen($this->bucket) == 0)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }

        // list on single file
        $arr = $this->getFileInfo($path);

        if (($arr['type'] ?? '') == 'FILE')
        {
            $arr['path'] = $path;
            return [ $arr ];
        }

        /*
        if (strlen($this->bucket) == 0 && ($path == '' || $path == '/'))
        {
            return $this->listBuckets();
        }
        */

        $bucket = '';
        $bucket_path = '';
        if (!$this->getPathParts($path, $bucket, $bucket_path))
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }

        $bucket_path = trim($bucket_path,'/');
        if (strlen($bucket_path) > 0)
            $bucket_path .= '/';
        $bucket_path_len = strlen($bucket_path);



        $ch = curl_init();

        $url = "https://www.googleapis.com/storage/v1/b/" . urlencode($bucket) . "/o";
        if (strlen($bucket_path) > 0)
        {
            $url .= "?prefix=" . rawurlencode($bucket_path);
        }


        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$this->access_token]);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        $result = curl_exec($ch);
        curl_close($ch);


        $result = json_decode($result,true);
        if (isset($result['items']))
            $result = $result['items'];
             else
            $result = array();

        $base_path = $path;
        if ($base_path == '')
            $base_path = '/';

        $files = array();
        foreach ($result as $row)
        {
            $file = $row['name'];

            if ($file == $bucket_path)
                continue;
            if (substr($file, 0, $bucket_path_len) == $bucket_path)
                $file = substr($file, $bucket_path_len);

            $full_path = $base_path;
            if (substr($full_path, -1) != '/')
                $full_path .= '/';

            $full_path .= ltrim($file,'/');
            $full_path = rtrim($full_path, '/');

            $f = array(
                'name' => trim($file,'/'),
                'path' => $full_path,
                'size' => (int)$row['size'],
                'modified' => $row['updated'],
                'hash' => '', // TODO: available?
                'type' => (substr($file, -1) == '/' ? 'DIR' : 'FILE')
            );

            if (isset($row['size']))
                $f['size'] = (int)$row['size'];
            if (isset($row['modifiedTime']))
                $f['modified'] = $row['modifiedTime'];
            $files[] = $f;
        }

        return $files;
    }

    public function listBuckets(string $path = '', array $options = []) : array
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/storage/v1/b");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$this->access_token]);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        $result = curl_exec($ch);
        curl_close($ch);


        $result = json_decode($result,true);

        if (isset($result['items']))
            $result = $result['items'];
             else
            $result = array();

        $base_path = $path;
        if ($base_path == '')
            $base_path = '/';

        $files = array();
        foreach ($result as $row)
        {
            $file = $row['name'];

            $full_path = $base_path;
            if (substr($full_path, -1) != '/')
                $full_path .= '/';

            $full_path .= ltrim($file,'/');
            $full_path = rtrim($full_path, '/');

            $f = array(
                'name' => $file,
                'path' => $full_path,
                'size' => (int)$row['size'],
                'modified' => $row['updated'],
                'hash' => '', // TODO: available?
                'type' => (substr($file, -1) == '/' ? 'DIR' : 'FILE')
            );

            if (isset($row['size']))
                $f['size'] = (int)$row['size'];
            if (isset($row['modifiedTime']))
                $f['modified'] = $row['modifiedTime'];
            $files[] = $f;
        }

        return $files;
    }

    public function getFileInfo(string $path) : array
    {
        while (false !== strpos($path,'//'))
            $path = str_replace('//','/',$path);
        $path = ltrim($path,'/');

        if (!$this->authenticated())
            return false;

        $bucket = '';
        $bucket_path = '';
        if (!$this->getPathParts($path, $bucket, $bucket_path))
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }

        $bucket_path = trim($bucket_path,'/');
        $bucket_path_len = strlen($bucket_path);

        $http_response_code = false;
        $error_payload = '';


        $ch = curl_init();
        for ($i = 0; $i < 2; ++$i)
        {
            $encoded_path = rawurlencode($bucket_path . ($i == 0 ? '/':''));
            $url = "https://www.googleapis.com/storage/v1/b/" . urlencode($bucket) . "/o/" . $encoded_path . "";

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);  // 30 seconds connection timeout
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $this->access_token]);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            $result = curl_exec($ch);
            $http_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($http_response_code >= 200 && $http_response_code <= 299)
                break;
        }
        curl_close($ch);

        $info = @json_decode($result,true);

        if (isset($info['name']))
        {
            $plain_filename = $info['name'];
            $plain_filename = trim($plain_filename, '/');
            $sl = strrpos($plain_filename,'/');
            if ($sl !== false)
                $plain_filename = substr($plain_filename, $sl+1);

            return [
                'name' => $plain_filename,
                'size' => $info['size'] ?? null,
                'modified' => $info['updated'] ?? '',
                'hash' => '', // TODO: available?
                'type' => (substr($info['name'], -1) == '/' ? 'DIR' : 'FILE')
            ];
        }
         else
        {
            // perhaps it's a directory without a directory 'file'

            $ch = curl_init();

            $url = "https://www.googleapis.com/storage/v1/b/" . urlencode($bucket) . "/o";

            $bucket_path = rtrim($bucket_path,'/') . '/';
            $url .= "?prefix=" . rawurlencode($bucket_path);

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$this->access_token]);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            $result = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($result,true);
            if (isset($result['items']) && count($result['items']) > 0)
            {
                $plain_filename = $path;
                $plain_filename = trim($plain_filename, '/');
                $sl = strrpos($plain_filename,'/');
                if ($sl !== false)
                    $plain_filename = substr($plain_filename, $sl+1);

                return [
                    'name' => $plain_filename,
                    'size' => null,
                    'modified' => null,
                    'hash' => '', // TODO: available?
                    'type' => 'DIR'
                ];
            }
             else
            {
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
            }
        }
    }

    public function exists(string $path) : bool
    {
        $info = null;

        try
        {
            $info = $this->getFileInfo($path);
        }
        catch (\Exception $e)
        {
            return false;
        }

        return isset($info);
    }

    public function open($path) : \Flexio\IFace\IStream
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
        if (!$this->authenticated())
            return false;

        // GCS directories are created by adding an object with a '/' as the last character
        if (substr($path,-1) != '/')
            $path .= '/';

        if ($this->exists($path))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED, "Object already exists");

        if (!$this->write([ 'path' => $path ], function($length) { return false; }))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        return true;
    }

    public function unlink(string $path) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function read(array $params, callable $callback)
    {
        $path = $params['path'] ?? '';

        if (!$this->authenticated())
            return false;

        $bucket = '';
        $bucket_path = '';
        if (!$this->getPathParts($path, $bucket, $bucket_path))
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }

        $bucket_path = trim($bucket_path,'/');
        $bucket_path = ltrim($bucket_path, '/');
        $bucket_path_len = strlen($bucket_path);


        $http_response_code = false;
        $error_payload = '';


        $encoded_path = rawurlencode($bucket_path);
        $url = "https://www.googleapis.com/storage/v1/b/" . urlencode($bucket) . "/o/" . $encoded_path . "?alt=media";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);  // 30 seconds connection timeout
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $this->access_token]);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
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
        $http_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_response_code >= 400 || $result !== true)
        {
            $error_object = @json_decode($error_payload, true);
            $message = $error_object['error']['message'] ?? '';

            $exception_message = "Read failed";
            if ($http_response_code >= 400)
                $exception_message .= ". HTTP Code: $http_response_code";
            if (strlen($message) > 0)
                $exception_message .= ". Google API Error Message: $message";

            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED, $exception_message);
        }
    }

    public function write(array $params, callable $callback)
    {
        if (!$this->authenticated())
            return false;

        if (isset($params['structure']))
        {
            $callback = \Flexio\Services\Util::tableToCsvCallbackAdaptor($params['structure'], $callback);
        }

        $path = $params['path'] ?? '';
        while (false !== strpos($path,'//'))
            $path = str_replace('//','/',$path);

        $content_type = $params['content_type'] ?? \Flexio\Base\ContentType::STREAM;
        $content_type = "text/plain";


        $dest_is_folder = false;
        try
        {
            $info = $this->getFileInfo($path);
            if (isset($info['type']) && $info['type'] == 'DIR')
                $dest_is_folder = true;
        }
        catch (\Exception $e)
        {
        }
        if ($dest_is_folder)
        {
            // destination path is a folder
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }




        $bucket = '';
        $bucket_path = '';
        if (!$this->getPathParts($path, $bucket, $bucket_path))
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }

        $bucket_path = trim($bucket_path,'/');
        $bucket_path = ltrim($bucket_path, '/');

        if (substr($path, -1) == '/')
        {
            // caller wants to create a directory
            $bucket_path .= '/';
        }

        $bucket_path_len = strlen($bucket_path);


        // store a copy, because GCS needs to know content length

        $stream = \Flexio\Base\Stream::create();
        $writer = $stream->getWriter();
        while (($data = $callback(32768)) !== false)
        {
            $writer->write($data);
        }

        $total_payload_size = $writer->getBytesWritten();
        $writer->close();
        unset($writer);

        $reader = $stream->getReader();


        // upload/write the file
        $ch = curl_init();

        $encoded_path = rawurlencode($bucket_path);
        $url = "https://www.googleapis.com/upload/storage/v1/b/" . urlencode($bucket) . "/o?uploadType=media&name=" . $encoded_path;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Length: " . $total_payload_size, 'Content-Type: '.$content_type, 'Authorization: Bearer '.$this->access_token));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_READFUNCTION, function($ch, $fp, $length) use (&$reader) {
            $data = $reader->read($length);
            if ($data === false)
                return '';
            return $data;
        });
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode == 404)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE, "Google Cloud Storage Bucket not found");

        if ($httpcode < 200 || $httpcode >= 300)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, "Could not upload to GCS");

        return true;
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

    private function connect() : bool
    {
        return true;
    }

    private function initialize(array $params = null) : bool
    {
        $client_id = $GLOBALS['g_config']->googlecloudstorage_client_id ?? '';
        $client_secret = $GLOBALS['g_config']->googlecloudstorage_client_secret ?? '';

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

                $this->bucket = $params['bucket'] ?? '';
                $this->base_path = $params['base_path'] ?? '';

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

                $this->bucket = $params['bucket'] ?? '';
                $this->base_path = $params['base_path'] ?? '';

                return $this;
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
            if (is_null($object->refresh_token))
            {
                $object->refresh_token = '';
            }

            $this->bucket = $params['bucket'] ?? '';
            $this->base_path = $params['base_path'] ?? '';

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
        $client_id = $GLOBALS['g_config']->googlecloudstorage_client_id ?? '';
        $client_secret = $GLOBALS['g_config']->googlecloudstorage_client_secret ?? '';

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
        $service = $service_factory->createService('google', $credentials, $storage, array('cloudstorage'));
        if (!isset($service))
            return null;

        // we want offline access (permanent until the owner revokes it) for running jobs
        $service->setAccessType('offline');
        return $service;
    }


    private function getPathParts(string $full_path, &$bucket, &$path) : bool
    {
        if (strlen($this->bucket) > 0)
        {
            $bucket = $this->bucket;
            $path = $full_path;

            if (strlen($this->base_path) > 0 && $this->base_path != '/')
                $path = $this->base_path . '/' . $path;

            while (false !== strpos($path,'//'))
                $path = str_replace('//','/',$path);
        }
         else
        {
            if (strlen($this->base_path) > 0 && $this->base_path != '/')
                $full_path = $this->base_path . '/' . $full_path;

            while (false !== strpos($full_path, '//'))
                $full_path = str_replace('//','/', $full_path);

            $has_leading_slash = (substr($full_path, 0, 1) == '/');
            $has_trailing_slash = (substr($full_path, -1) == '/');

            $full_path = trim($full_path,'/');

            $parts = \Flexio\Base\File::splitPath($path);

            if (count($parts) == 0)
                return false;

            $bucket = array_shift($parts);

            if (count($parts) == 0)
            {
                $path = '/';
            }
             else
            {
                $path = ($has_leading_slash?'/':'') . implode('/',$path) . ($has_trailing_slash?'/':'');
            }
        }

        return true;
    }

}
