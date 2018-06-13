<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
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
    private $access_token = '';
    private $refresh_token = '';
    private $expires = 0;

    private $bucket = 'testsuite';

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
        if (!$this->authenticated())
            return array();

        if (strlen($this->bucket) == 0)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
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

/*
        $file_limit = 1000; // limit return results to 1000; max is 1000, default is 100
        $folder = $path;

        $fileinfo = $this->internalGetFileInfo($path);
        if (!isset($fileinfo['id']) || !isset($fileinfo['content_type']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        if ($fileinfo['content_type'] != 'application/vnd.google-apps.folder')
        {
            $arr = $this->getFileInfo($path);
            $arr['path'] = $path;
            return [ $arr ];
        }

        $folderid = $fileinfo['id'];
*/

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
                'name' => $file,
                'path' => $full_path,
                'size' => (int)$row['size'],
                'modified' => $row['updated'],
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
        if (!$this->authenticated())
            return false;

        $fileid = $this->getFileId($path);
        if ($fileid === null)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NOT_FOUND);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/drive/v3/files/" . $fileid . "?fields=id%2Ckind%2CmimeType%2CmodifiedTime%2Cname%2Csize");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$this->access_token]);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $info = @json_decode($result, true);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!isset($info['name']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::GENERAL);

        return [
            'name' => $info['name'],
            'size' => $info['size'] ?? null,
            'modified' => $info['modifiedTime'] ?? '',
            'type' => (($info['mimeType']??'') == 'application/vnd.google-apps.folder' ? 'DIR' : 'FILE')
        ];
    }

    private function internalGetFileInfo(string $path)
    {
        if (is_null($path) || $path == '' || $path == '/')
        {
            return [ 'id' => 'root', 'content_type' => 'application/vnd.google-apps.folder' ];
        }


        $path = trim($path, '/'); 
        while (false !== strpos($path,'//'))
            $path = str_replace('//','/',$path);
        $parts = explode('/', $path);
        $file_limit = 1000;

        $ch = curl_init();

        $current_id = 'root'; // stores the current folder id
        $current_content_type = 'application/octet-stream';

        foreach ($parts as $p)
        {
            $p = str_replace("'", "\\'", $p);
            $p = urlencode($p); // necessary for files/folders with spaces
            $url = "https://www.googleapis.com/drive/v3/files?maxResults=$file_limit&fields=files(id%2Ckind%2CmimeType%2CmodifiedTime%2Cname%2Csize)&q='$current_id'+in+parents+and+name='$p'+and+trashed=false";

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$this->access_token]);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            $result = curl_exec($ch);
            $result = @json_decode($result,true);

            if (!isset($result['files'][0]['id']))
                return null;
            $current_id = $result['files'][0]['id'];
            $current_content_type = $result['files'][0]['mimeType'];
        }

        curl_close($ch);

        return array('id' => $current_id, 'content_type' => $current_content_type);
    }

    public function exists(string $path) : bool
    {
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
        $this->write([ 'path' => $path ], function($length) { return false; });
        return true;
    }

    public function createDirectory(string $path, array $properties = []) : bool
    {
        if (!$this->authenticated())
            return false;

        $folderid = $this->getFileId($path);
        if (is_null($folderid) || strlen($folderid) == 0)
        {
            $folderid = $this->createFolderStructure($path);
            if (is_null($folderid) || strlen($folderid) == 0)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);
            return true;
        }

        throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED, "Object already exists");
    }

    public function unlink(string $path) : bool
    {
        if (!$this->authenticated())
            return false;

        $fileid = $this->getFileId($path);
        if ($fileid === null || $fileid === 'root' || strlen($fileid) == 0)
            return false;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/drive/v3/files/" . $fileid);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$this->access_token]);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($httpcode >= 200 && $httpcode <= 299) ? true : false;
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

    private function internalCreateFolder($parentid, $name)
    {
        $postdata = json_encode(array(
            'name' => $name,
            'parents' => [ $parentid ],
            'mimeType' => 'application/vnd.google-apps.folder'
        ));

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/drive/v3/files");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer '.$this->access_token]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        $result = curl_exec($ch);

        curl_close($ch);

        $result = @json_decode($result, true);
        $fileid = $result['id'] ?? '';
        if (strlen($fileid) == 0)
            return false;

        return $fileid;
    }

    private function createFolderStructure($path)
    {
        $folder = trim($path,'/');
        if ($folder == '')
            return 'root';

        $parts = explode('/',$folder);

        $path = '';
        $parentid = 'root';
        for ($i = 0; $i < count($parts); ++$i)
        {
            $path .= ('/'.$parts[$i]);

            $folderid = $this->getFileId($path);

            if (!$folderid)
            {
                $folderid = $this->internalCreateFolder($parentid, $parts[$i]);
                if (!$folderid)
                    return false;
            }

            $parentid = $folderid;
        }

        return $folderid;
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
        $content_type = $params['content_type'] ?? \Flexio\Base\ContentType::STREAM;
        $content_type = "text/plain";

        $bucket = '';
        $bucket_path = '';
        if (!$this->getPathParts($path, $bucket, $bucket_path))
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }

        $bucket_path = trim($bucket_path,'/');
        $bucket_path = ltrim($bucket_path, '/');
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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NOT_FOUND, "Google Cloud Storage Bucket not found");

        if ($httpcode < 200 || $httpcode >= 300)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, "Could not upload to GCS");

        return true;

/*
        $encoded_path = rawurlencode($bucket_path);
        $url = "https://www.googleapis.com/upload/storage/v1/b/" . urlencode($bucket) . "/o?uploadType=resumable&name=" . $encoded_path;


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$this->access_token]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '');
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $result = curl_exec($ch);
        $http_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $location = '';
        $result = str_replace(["\r\n","\r","\n"], "\n", $result);
        $lines = explode("\n", $result);
        foreach ($lines as $line)
        {
            if (strtolower(substr($line, 0, 9)) == "location:")
            {
                $location = trim(substr($line, 9));
                break;
            }
        }

        if (strlen($location) == 0)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, "Could not retrieve GCS upload endpoint");
        }
*/




        /*
        // put the file

        $total_written = 0;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $location);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/octet-stream','Authorization: Bearer '.$this->access_token]);
        curl_setopt($ch, CURLOPT_UPLOAD, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_READFUNCTION, function($ch, $fp, $length) use (&$reader) {
            $data = $reader->read($length);
            if ($data === false)
                return '';
            return $data;
        });
        $result = curl_exec($ch);
        curl_close($ch);

        var_dump($result);
        die();

        $result = @json_decode($result,true);
        $file_size = $result['fileSize'] ?? -1;

        return ($file_size == $total_written ? true : false);
        */
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
        $client_id = $GLOBALS['g_config']->googlecloudstorage_client_id ?? '';
        $client_secret = $GLOBALS['g_config']->googlecloudstorage_client_secret ?? '';

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
        }
         else
        {
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
