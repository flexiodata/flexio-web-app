<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-03-04
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


require_once dirname(dirname(__DIR__)) . '/library/phpoauthlib/src/OAuth/bootstrap.php';

class GoogleDrive implements \Flexio\IFace\IFileSystem
{
    private $is_ok = false;
    private $access_token = '';
    private $refresh_token = '';
    private $expires = 0;

    public static function create(array $params = null) // TODO: fix dual return types which is used for Oauth
    {
        if (!isset($params))
            return new self;

        return self::initialize($params);
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

        $file_limit = 1000; // limit return results to 1000; max is 1000, default is 100
        $folder = $path;

        $folderid = $this->getFileId($folder);
        if (!$folderid)
            return array();

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/drive/v3/files?maxResults=$file_limit&fields=files(id%2Ckind%2CmimeType%2CmodifiedTime%2Cname%2Csize)&q='$folderid'+in+parents+and+trashed=false");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$this->access_token]);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result,true);
        if (isset($result['files']))
            $result = $result['files'];
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
            $full_path .= $file;

            $f = array(
                'name' => $file,
                'path' => $full_path,
                'size' => null,
                'modified' => null,
                'is_dir' => ($row['mimeType'] == 'application/vnd.google-apps.folder' ? true : false),
                'root' => 'googledrive'
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
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
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
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function read(array $params, callable $callback)
    {
        $path = $params['path'] ?? '';

        if (!$this->authenticated())
            return false;

        $fileinfo = $this->getFileInfo($path);
        if (!isset($fileinfo['id']) || $fileinfo['id'] == '' || $fileinfo['id'] == 'root')
            return false; // bad filename / fileid
        $fileid = $fileinfo['id'];
        $filetype = $fileinfo['content_type'];

        if ($fileinfo['content_type'] == 'application/vnd.google-apps.spreadsheet')
        {
            $sheets = \Flexio\Services\GoogleSheets::create(array(
                            'access_token' => $this->access_token,
                            'refresh_token' => $this->refresh_token,
                            'expires' => $this->expires));
            return $sheets->read(array('spreadsheet_id' => $fileid), $callback);
        }

        $http_response_code = false;
        $error_payload = '';

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/drive/v3/files/$fileid?alt=media");
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
        $path = $params['path'] ?? '';
        $content_type = $params['content_type'] ?? \Flexio\Base\ContentType::STREAM;

        $folder = trim($path,'/');
        while (false !== strpos($folder,'//'))
            $folder = str_replace('//','/',$folder);
        $parts = explode('/',$folder);

        $filename = array_pop($parts);
        $folder = '/' . join('/',$parts);

        $folderid = $this->getFileId($folder);
        if (is_null($folderid) || strlen($folderid) == 0)
            return false; // bad folderid

        // see if the file already exists by getting its id
        $fileid = $this->getFileId($folder . '/' . $filename);

        // if the file doesn't already exist, create the file (otherwise overwrite it)
        if (!$fileid)
        {
            $postdata = json_encode(array(
                "name" => $filename,
                "parents" => [ $folderid ]
            ));

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/drive/v3/files");
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer '.$this->access_token]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);

            curl_close($ch);


            $result = @json_decode($result, true);
            $fileid = $result['id'] ?? '';
            if (strlen($fileid) == 0)
                return false;
        }


        // put the file

        $total_written = 0;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/upload/drive/v2/files/$fileid?uploadType=media");
       // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/octet-stream','Authorization: Bearer '.$this->access_token]);
        curl_setopt($ch, CURLOPT_UPLOAD, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_READFUNCTION, function($ch, $fp, $length) use (&$callback, &$total_written) {
            $res = $callback($length);
            if ($res === false) return '';
            $total_written += strlen($res);
            return $res;
        });
        $result = curl_exec($ch);
        curl_close($ch);

        $result = @json_decode($result,true);
        $file_size = $result['fileSize'] ?? -1;

        return ($file_size == $total_written ? true : false);
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

    private function authenticated() : bool
    {
        if (strlen($this->access_token) > 0)
            return true;

        return false;
    }

    public function getFileId(string $path)  // TODO: set function return type   (: ?string)
    {
        $info = $this->getFileInfo($path);
        if (!$info)
            return $info;
        return $info['id'];
    }

    public function getFileInfo(string $path)  // TODO: set function return type   (: ?string)
    {
        if (is_null($path) || $path == '' || $path == '/')
        {
            return array('id' => 'root', 'content_type' => \Flexio\Base\ContentType::FLEXIO_FOLDER);
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
            $url = "https://www.googleapis.com/drive/v3/files?maxResults=$file_limit&q='$current_id'+in+parents+and+name='$p'+and+trashed=false";

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

    private function connect() : bool
    {
        return true;
    }

    private function isOk() : bool
    {
        return $this->is_ok;
    }

    private static function initialize(array $params)
    {
        $client_id = $GLOBALS['g_config']->googledrive_client_id ?? '';
        $client_secret = $GLOBALS['g_config']->googledrive_client_secret ?? '';

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

            $expires = $params['expires'] ?? null;
            if (is_null($expires))
                $expires = 0;
            if (!is_int($expires))
                $expires = strtotime($expires);
            if ($expires == 0)
                $expires = $curtime + 3600; // default

            if ($curtime < $expires)
            {
                // access token is valid (not expired); use it
                $object = new self;
                $object->access_token = $params['access_token'];
                $object->refresh_token = $params['refresh_token'] ?? '';
                $object->expires = $expires;
                $object->is_ok = true;
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
                if (isset($params['expires']) && !is_null($params['expires']) && $params['expires'] > 0)
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
                $object->access_token = $token->getAccessToken();
                $object->refresh_token = $token->getRefreshToken();
                $object->expires = $token->getEndOfLife();
                $object->is_ok = true;
                if (is_null($object->refresh_token)) $object->refresh_token = '';
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
            $object->is_ok = true;
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

    private static function createService($oauth_callback) // TODO: set parameter/return type
    {
        $client_id = $GLOBALS['g_config']->googledrive_client_id ?? '';
        $client_secret = $GLOBALS['g_config']->googledrive_client_secret ?? '';

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
        $service = $service_factory->createService('google', $credentials, $storage, array('googledrive', 'spreadsheets', 'documentslist'));
        if (!isset($service))
            return null;

        // we want offline access (permanent until the owner revokes it) for running jobs
        $service->setAccessType('offline');
        return $service;
    }
}
