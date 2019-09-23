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


class Box implements \Flexio\IFace\IConnection,
                     \Flexio\IFace\IOAuthConnection,
                     \Flexio\IFace\IFileSystem
{
    // connection info
    private $authorization_uri = '';
    private $access_token = '';
    private $refresh_token = '';
    private $expires = 0;
    private $base_path = '';

    public static function create(array $params = null) : \Flexio\Services\Box
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
        return $this->get();
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

        $fileinfo = $this->internalGetFileInfo($path);
        if (!isset($fileinfo['id']) || !isset($fileinfo['content_type']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        if ($fileinfo['content_type'] != \Flexio\Base\ContentType::FLEXIO_FOLDER)
        {
            $arr = $this->getFileInfo($path);
            $arr['path'] = $path;
            return [ $arr ];
        }

        $entries = $this->getFolderItems($fileinfo['id'], 'name,type,size,modified_at,sha1');

        $files = [];

        foreach ($entries as $entry)
        {
            $fullpath = $path;
            if (substr($fullpath, -1) != '/')
                $fullpath .= '/';
            $fullpath .= $entry['name'];

            $files[] = array('id'=> $entry['id'] ?? null,
                             'name' => $entry['name'],
                             'path' => $fullpath,
                             'size' => $entry['size'] ?? '',
                             'modified' => $entry['modified_at'] ?? '',
                             'hash' => $entry['sha1'] ?? '',
                             'type' => ($entry['type'] == 'folder' ? 'DIR' : 'FILE'));
        }

        return $files;
    }

    public function getFileInfo(string $path) : array
    {
        if (!$this->authenticated())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CONNECTION_FAILED);

        $info = $this->internalGetFileInfo($path);
        if (!isset($info['id']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        $fileid = $info['id'];
        if (($info['content_type'] ?? '') == \Flexio\Base\ContentType::FLEXIO_FOLDER)
        {
            $type = 'DIR';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.box.com/2.0/folders/" . $fileid . "?fields=modified_at,name");
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$this->access_token]);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            $info = @json_decode($result, true);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $content_type = '';
        }
         else
        {
            $type = 'FILE';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.box.com/2.0/files/" . $fileid . "?fields=modified_at,name,size,sha1");
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$this->access_token]);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            $info = @json_decode($result, true);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $content_type = \Flexio\Base\ContentType::getMimeType($info['name']);
        }

        if (($info['code'] ?? '') === 'not_found')
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        if (!isset($info['name']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::GENERAL);

        $result = [
            'name' => $info['name'],
            'size' => $info['size'] ?? null,
            'modified' => $info['modified_at'] ?? '',
            'hash' => $info['sha1'] ?? '',
            'type' => $type,
            'content_type' => $content_type
        ];

        return $result;
    }

    public function internalGetFileInfo(string $path) : ?array
    {
        $remote_path = $this->getRemotePath($path);

        if (is_null($remote_path) || $remote_path == '' || $remote_path == '/')
        {
            return [ 'id' => '0', 'content_type' => \Flexio\Base\ContentType::FLEXIO_FOLDER ];
        }


        $parts = explode('/', trim($remote_path,'/'));
        $file_limit = 1000;

        $current_id = 0; // stores the current folder id; 0 = root
        $current_content_type = \Flexio\Base\ContentType::FLEXIO_FOLDER;

        foreach ($parts as $p)
        {
            //$p = str_replace("'", "\\'", $p);
            $p = urlencode($p); // necessary for files/folders with spaces
            $items = $this->getFolderItems($current_id);

            $found = false;
            foreach ($items as $item)
            {
                if ($item['name'] == $p)
                {
                    $current_id = $item['id'];
                    if ($item['type'] == 'folder')
                        $current_content_type = \Flexio\Base\ContentType::FLEXIO_FOLDER;
                         else
                        $current_content_type = 'application/octet-stream';
                    $found = true;
                    break;
                }
            }

            if (!$found)
                return null;
        }


        return array('id' => $current_id, 'content_type' => $current_content_type);
    }

    public function exists(string $path) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return false;
    }

    public function createFile(string $path, array $properties = []) : bool
    {
        $this->write([ 'path' => $path ], function($length) { return false; });
        return true;
    }

    public function createDirectory(string $path, array $properties = []) : bool
    {
        if ($this->getFileId($path))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED, "Object already exists");

        if ($this->createFolderStructure($path) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        return true;
    }

    public function unlink(string $path) : bool
    {
        if (!$this->authenticated())
            return false;

        $fileid = $this->getFileId($path);
        if ($fileid === null || $fileid === '0' || $fileid === 0)
            return false;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.box.com/2.0/files/" . $fileid);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$this->access_token));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($httpcode >= 200 && $httpcode <= 299) ? true : false;
    }

    public function open($path) : \Flexio\IFace\IStream
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function read(array $params, callable $callback) // TODO: add return type
    {
        if (!$this->authenticated())
            return false;

        $path = $params['path'] ?? '';
        if (strlen($path) == 0)
            return false;

        $fileid = $this->getFileId($path);

        $http_response_code = false;
        $error_payload = '';

        $ch = curl_init();

        $filename = rawurlencode($path);
        curl_setopt($ch, CURLOPT_URL, "https://api.box.com/2.0/files/$fileid/content");
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$this->access_token));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
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

        if ($http_response_code >= 400 || $result !== true)
        {
            $error_object = @json_decode($error_payload, true);
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }
    }

    private function internalCreateFolder($parentid, $name) // TODO: add return type
    {
        $postdata = json_encode(array(
            'name' => $name,
            'parent' => [ 'id' => $parentid ]
        ));

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.box.com/2.0/folders");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer '.$this->access_token]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        $result = @json_decode($result, true);
        $fileid = $result['id'] ?? '';
        if (strlen($fileid) == 0)
            return false;

        return $fileid;
    }

    private function createFolderStructure($path) // TODO: add return type
    {
        $folder = trim($path,'/');
        if ($folder == '')
            return '0';

        $parts = explode('/',$folder);

        $path = '';
        $parentid = '0';
        for ($i = 0; $i < count($parts); ++$i)
        {
            if (strlen($parts[$i]) == 0)
                continue;

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

    public function write(array $params, callable $callback) // TODO: add return type
    {
        if (!$this->authenticated())
            return false;

        if (isset($params['structure']))
        {
            $callback = \Flexio\Services\Util::tableToCsvCallbackAdaptor($params['structure'], $callback);
        }

        $path = $params['path'] ?? '';
        $content_type = $params['content_type'] ?? \Flexio\Base\ContentType::STREAM;


        $info = $this->internalGetFileInfo($path);
        if (($info['content_type'] ?? '') == \Flexio\Base\ContentType::FLEXIO_FOLDER)
        {
            // destination path is a folder
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }


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

        $fileid = $this->getFileId($fullpath);


        if ($fileid !== null)
        {
            // file already exists -- update content
            $box_args = null;
            $url = "https://upload.box.com/api/2.0/files/$fileid/content";
        }
         else
        {
            // file doesn't exist yet -- create new file
            $box_args = json_encode(['name' => $filename, 'parent' => ['id' => $folderid]]);
            $url = "https://upload.box.com/api/2.0/files/content";
        }

        $buf = '';
        $boundary = "---------------------------2523643".time()."1927533";
        $content_type = "multipart/form-data; boundary=$boundary";
        if (isset($box_args))
        {
            $buf .= "--$boundary\r\n".
                    "Content-Disposition: form-data; name=\"attributes\"\r\n".
                    "\r\n".
                    "$box_args\r\n";
        }

        $buf .= "--$boundary\r\n".
                "Content-Disposition: form-data; name=\"file\"; filename=\"$filename\"\r\n".
                "Content-Type: application/octet-stream\r\n".
                "\r\n";

        $stream = \Flexio\Base\Stream::create();
        $writer = $stream->getWriter();
        $writer->write($buf);
        while (($data = $callback(32768)) !== false)
        {
            $writer->write($data);
        }

        $buf = "\r\n--$boundary--";
        $writer->write($buf);
        $total_payload_size = $writer->getBytesWritten();
        $writer->close();
        unset($writer);

        $reader = $stream->getReader();

        // upload/write the file
        $ch = curl_init();

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

        return true;
    }

    private function getRemotePath(string $path) : string
    {
        return \Flexio\Services\Util::mergePath($this->base_path, $path);
    }

    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    private function getFolderItems($folder_id, $fields = null) : array
    {
        if (!$this->authenticated())
            return array();

        $url = "https://api.box.com/2.0/folders/$folder_id/items?usemarker=true&limit=1000";
        if (isset($fields))
            $url .= '&fields='.$fields;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$this->access_token));
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        $result = @json_decode($result, true);
        if (!isset($result['entries']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        return $result['entries'];
    }

    private function getFileId(string $path) : ?string
    {
        $info = $this->internalGetFileInfo($path);
        if (!isset($info['id']))
            return null;
        return $info['id'];
    }

    private function initialize(array $params = null) : bool
    {
        $client_id = $GLOBALS['g_config']->box_client_id ?? '';
        $client_secret = $GLOBALS['g_config']->box_client_secret ?? '';

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
                $token->setEndOfLife($expires);

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

                $this->base_path = $params['base_path'] ?? '';

                return false;
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

    private static function createService($oauth_callback) : ?\OAuth\OAuth2\Service\Box // TODO: add parameter type
    {
        $client_id = $GLOBALS['g_config']->box_client_id ?? '';
        $client_secret = $GLOBALS['g_config']->box_client_secret ?? '';

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
        $service = $service_factory->createService('box', $credentials, $storage, array());
        if (!isset($service))
            return null;

        // we want offline access (permanent until the owner revokes it) for running jobs
        //$service->setAccessType('offline');
        return $service;
    }
}
