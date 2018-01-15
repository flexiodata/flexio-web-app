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

class Box implements \Flexio\IFace\IFileSystem
{
    private $is_ok = false;
    private $access_token = '';
    private $refresh_token = '';
    private $expires = 0;
    private $folders = [];

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

        $fileinfo = $this->getFileInfo($path);
        if (!isset($fileinfo['id']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        if (!isset($fileinfo['content_type']) || $fileinfo['content_type'] != \Flexio\Base\ContentType::FLEXIO_FOLDER)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);  // not a folder


        $entries = $this->getFolderItems($fileinfo['id'], 'name,type,size,modified_at');

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
                                'is_dir' => ($entry['type'] == 'folder' ? true : false));
        }

        return $files;
    }

    public function getFileInfo(string $path) : array
    {
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }
    
    public function exists(string $path) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return false;
    }

    public function createFile(string $path, array $properties = []) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function open($path) : \Flexio\IFace\IStream
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function read(array $params, callable $callback)
    {
        if (!$this->authenticated())
            return false;

        $path = $params['path'] ?? '';
        if (strlen($path) == 0)
            return false;

        $fileid = $this->getFileId($path);

        // download the file
        $ch = curl_init();

        $filename = rawurlencode($path);
        curl_setopt($ch, CURLOPT_URL, "https://api.box.com/2.0/files/$fileid/content");
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$this->access_token));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
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
        if (!$this->authenticated())
            return false;

        // Box unfortunately requires a content size
        $size = $params['size'] ?? null;
        if (!isset($size))
            return false;

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


        if ($fileid !== false)
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


        // upload/write the file
        $ch = curl_init();

        $boundary = "---------------------------2523643".time()."1927533";
        $content_type = "multipart/form-data; boundary=$boundary";
        $content_finished = false;
        $total_written = 0;

        $header = fopen('php://memory', 'rw+');
        $buf = '';

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

        $header_len = strlen($buf);
        fwrite($header, $buf);
        fseek($header, 0);

        $footer = fopen('php://memory', 'rw+');
        $buf = "\r\n--$boundary--";
        $footer_len = strlen($buf);
        fwrite($footer, $buf);
        fseek($footer, 0);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Length: " . ($header_len + $size + $footer_len), 'Content-Type: '.$content_type, 'Authorization: Bearer '.$this->access_token));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_READFUNCTION, function($ch, $fp, $length) use (&$callback, &$total_written, &$header, &$footer, &$content_finished) {

            $res = '';
            if ($length > 0 && $header)
            {
                $chunk = fread($header, $length);
                $chunk_len = strlen($chunk);
                if ($chunk_len < $length)
                    $header = null;
                $res .= $chunk;
                $length -= $chunk_len;
            }


            if ($length > 0 && !$content_finished)
            {
                $chunk = $callback($length);
                if ($chunk === false)
                {
                    $content_finished = true;
                }
                else
                {
                    $chunk_len = strlen($chunk);
                    if ($chunk_len < $length)
                        $content_finished = true;
                    $length -= $chunk_len;
                    $res .= $chunk;
                }
            }


            if ($length > 0 && $content_finished && $footer)
            {
                $chunk = fread($footer, $length);
                $chunk_len = strlen($chunk);
                if ($chunk_len < $length)
                    $footer = null;
                $res .= $chunk;
                $length -= $chunk_len;
            }

            $total_written += strlen($res);
            return $res;
        });
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

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

    private function authenticated() : bool
    {
        if (strlen($this->access_token) > 0)
            return true;

        return false;
    }

    private function getFolderItems($folder_id, $fields = null)// : array
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

    private function getFileId(string $path)  // TODO: set function return type   (: ?string)
    {
        $info = $this->getFileInfo($path);
        if (!$info)
            return $info;
        return $info['id'];
    }

    private function getFileInfo(string $path)  // TODO: set function return type   (: ?string)
    {
        if (is_null($path) || $path == '' || $path == '/')
        {
            return array('id' => '0', 'content_type' => \Flexio\Base\ContentType::FLEXIO_FOLDER);
        }

        $path = trim($path, '/');
        while (false !== strpos($path,'//'))
            $path = str_replace('//','/',$path);
        $parts = explode('/', $path);
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
                return false;
        }


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
        $client_id = $GLOBALS['g_config']->box_client_id ?? '';
        $client_secret = $GLOBALS['g_config']->box_client_secret ?? '';

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
                $token->setEndOfLife($expires);

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
