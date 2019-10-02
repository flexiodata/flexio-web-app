<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
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


class GoogleDrive implements \Flexio\IFace\IConnection,
                             \Flexio\IFace\IOAuthConnection,
                             \Flexio\IFace\IFileSystem
{
    // connection info
    private $authorization_uri = '';
    private $access_token = '';
    private $refresh_token = '';
    private $expires = 0;
    private $base_path = '';

    public static function create(array $params = null) : \Flexio\Services\GoogleDrive
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


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/drive/v3/files?pageSize=$file_limit&fields=files(id%2Ckind%2CmimeType%2CmodifiedTime%2Cname%2Csize)&q='$folderid'+in+parents+and+trashed=false");
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
                'id' => sha1($row['id']),
                'name' => $file,
                'path' => $full_path,
                'size' => null,
                'modified' => null,
                'hash' => '', // TODO: available?
                'type' => ($row['mimeType'] == 'application/vnd.google-apps.folder' ? 'DIR' : 'FILE')
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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        $fileid = $this->getFileId($path);
        if ($fileid === null)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        return [
            'id' => $info['id'],
            'name' => $info['name'],
            'size' => $info['size'] ?? null,
            'modified' => $info['modifiedTime'] ?? '',
            'hash' => '', // TODO: available?
            'type' => (($info['mimeType']??'') == 'application/vnd.google-apps.folder' ? 'DIR' : 'FILE'),
            'content_type' => ($info['mimeType'] ?? 'application/octet-stream')
        ];
    }

    private function internalGetFileInfo(string $path)
    {
        $remote_path = $this->getRemotePath($path);

        if (is_null($remote_path) || $remote_path == '' || $remote_path == '/')
        {
            return [ 'id' => 'root', 'content_type' => 'application/vnd.google-apps.folder' ];
        }

        $parts = explode('/', trim($remote_path,'/'));
        $file_limit = 1000;

        $ch = curl_init();

        $current_id = 'root'; // stores the current folder id
        $current_content_type = 'application/octet-stream';

        foreach ($parts as $p)
        {
            $p = str_replace("'", "\\'", $p);
            $p = urlencode($p); // necessary for files/folders with spaces
            $url = "https://www.googleapis.com/drive/v3/files?pageSize=$file_limit&fields=files(id%2Ckind%2CmimeType%2CmodifiedTime%2Cname%2Csize)&q='$current_id'+in+parents+and+name='$p'+and+trashed=false";

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
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
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

        $fileinfo = $this->internalGetFileInfo($path);
        if (!isset($fileinfo['id']) || $fileinfo['id'] == '' || $fileinfo['id'] == 'root')
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

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

        $folder = trim($path,'/');
        while (false !== strpos($folder,'//'))
            $folder = str_replace('//','/',$folder);
        $parts = explode('/',$folder);

        $filename = array_pop($parts);
        $folder = '/' . join('/',$parts);

        if (strlen($filename) == 0)
            return false;

        $folderid = $this->getFileId($folder);
        if (is_null($folderid) || strlen($folderid) == 0)
        {
            $folderid = $this->createFolderStructure($folder);
            if (is_null($folderid) || strlen($folderid) == 0)
                return false; // bad folderid
        }

        // see if the file already exists by getting its id
        $fullpath = $folder;
        if (substr($fullpath, -1) != '/')
            $fullpath .= '/';
        $fullpath .= $filename;


        $info = $this->internalGetFileInfo($fullpath);
        if (($info['content_type'] ?? '') == 'application/vnd.google-apps.folder')
        {
            // destination path is a folder
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }

        $fileid = $info['id'] ?? null;


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

    private function getRemotePath(string $path) : string
    {
        return \Flexio\Services\Util::mergePath($this->base_path, $path);
    }

    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    public function getFileId(string $path) // TODO: add return type (: ?string)
    {
        $info = $this->internalGetFileInfo($path);
        if (!isset($info['id']))
            return null;
        return $info['id'];
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


        // STEP 1: set the non-oauth params
        $this->base_path = $params['base_path'] ?? '';

        // STEP 2: if we have an access token and it's not expired, create an object
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
                $this->refresh_token = '';
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
