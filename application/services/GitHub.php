<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams; Aaron L. Williams
 * Created:  2017-10-11
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


class GitHub implements \Flexio\IFace\IConnection, \Flexio\IFace\IFileSystem
{
    private $authorization_uri = '';
    private $access_token = '';
    private $owner = '';
    private $repository = '';

    public static function create(array $params = null) : \Flexio\Services\Github
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
    }

    public function authenticated() : bool
    {
        if (strlen($this->access_token) > 0)
            return true;

        return false;
    }

    ////////////////////////////////////////////////////////////
    // OAuth interface; TODO: implement
    ////////////////////////////////////////////////////////////

    public function getAuthorizationUri() : string
    {
        return $this->authorization_uri;
    }

    public function getTokens() : array
    {
        return [ 'access_token' => $this->access_token,
                 'refresh_token' => '',
                 'expires' => 0  ];
    }

    ////////////////////////////////////////////////////////////
    // IFileSystem interface
    ////////////////////////////////////////////////////////////

    public function getFlags() : int
    {
        return 0;
    }

    public function list(string $full_path = '', array $options = []) : array
    {
        // returns the directory tree for all repositories the user
        // has access to, starting with the repository as the root;
        // also allows access to public repos

        try
        {
            $arr = $this->getFileInfo($full_path);
            if (($arr['type'] ?? '') == 'FILE')
            {
                $arr['path'] = $full_path;
                return [ $arr ];
            }
        }
        catch (\Exception $e)
        {
        }

        while (false !== strpos($full_path,'//'))
            $full_path = str_replace('//','/',$full_path);

        $owner_repository = '';
        $path = '';
        $result = $this->getPathParts($full_path, $owner_repository, $path);

        if ($result === false)
            return [];

        $path_parts = explode('/', $path);

        return $this->getFolderItems($owner_repository, $path);
    }

    public function getFileInfo(string $full_path) : array
    {
        while (false !== strpos($full_path,'//'))
            $full_path = str_replace('//','/',$full_path);

        $repository = '';
        $path = '';
        $result = $this->getPathParts($full_path, $repository, $path);
        if ($result === false)
            return [];

        $url = "https://api.github.com/repos/$repository/contents/$path";

        // build the request headers; allow requests to public repos
        $request_headers = [
            'Accept: application/vnd.github.v3+json',
            'User-Agent: Flex.io'
        ];
        if ($this->authenticated())
            $request_headers[] = 'Authorization: Token '.$this->access_token;

        $contents = '';
        $headers = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $data) use (&$headers) {
            $headers[] = $data;
            return strlen($data);
        });
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode == 404)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        }

        $entry = @json_decode($result);

        if ($entry === null)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }

        if (is_array($entry))
        {
            $trimmed_full_path = trim($full_path,'/');
            $name = $trimmed_full_path;
            $sl = strrpos($name, '/');
            if ($sl !== false)
                $name = substr($name, $sl+1);

            // item is a directory
            $res = array('id'=> md5($trimmed_full_path),
                         'name' => $name,
                         'size' => null,
                         'modified' => '',
                         'hash' => '', // TODO: available?
                         'type' => 'DIR');

            return $res;
        }

        $entry = (array)$entry;


        if ($httpcode >= 200 && $httpcode <= 299)
        {
            $res = array('id'=> md5($entry['git_url']),
                         'name' => $entry['name'],
                         'size' => $entry['size'] ?? null,
                         'modified' => '',
                         'hash' => $entry['sha'] ?? '',
                         'type' => 'FILE');

            return $res;
        }
         else
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::GENERAL);
        }
    }

    public function exists(string $path) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        //return false;
    }

    public function createFile(string $path, array $properties = []) : bool
    {
        $this->write([ 'path' => $path ], function($length) { return false; });
        return true;
    }

    public function createDirectory(string $path, array $properties = []) : bool
    {
        // git doesn't support folders; you have to create a directory with a file in it
        return true;

        // TODO: implement
        //throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function unlink(string $path) : bool
    {
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return false;
    }

    public function open($path) : \Flexio\IFace\IStream
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function read(array $params, callable $callback)
    {
        $full_path = $params['path'] ?? '';

        while (false !== strpos($full_path,'//'))
            $full_path = str_replace('//','/',$full_path);

        $repository = '';
        $path = '';
        $result = $this->getPathParts($full_path, $repository, $path);
        if ($result === false)
            return;

        $url = "https://api.github.com/repos/$repository/contents/$path";

        // build the request headers; allow requests to public repos
        $request_headers = [
            'Accept: application/vnd.github.v3+json',
            'User-Agent: Flex.io'
        ];
        if ($this->authenticated())
            $request_headers[] = 'Authorization: Token '.$this->access_token;

        $contents = '';
        $headers = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $data) use (&$headers) {
            $headers[] = $data;
            return strlen($data);
        });
        $result = curl_exec($ch);
        curl_close($ch);

        $result = @json_decode($result, true);
        if (!is_array($result))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $url = $result['download_url'];

        $contents = '';
        $headers = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $data) use (&$headers) {
            $headers[] = $data;
            return strlen($data);
        });
        $result = curl_exec($ch);
        curl_close($ch);

        $callback($result);
    }

    public function write(array $params, callable $callback)
    {
        // require authentication for writing to a repo
        if (!$this->authenticated())
            return array();

        if (isset($params['structure']))
        {
            $callback = \Flexio\Services\Util::tableToCsvCallbackAdaptor($params['structure'], $callback);
        }

        // File Create:
        // Request: PUT https://api.github.com/repos/:owner/:repo/contents/:path
        // Params:
        // path     string  Required.  The content path.
        // message  string  Required.  The commit message.
        // content  string  Required.  The new file content, Base64 encoded.
        // branch   string  The branch name. Default: the repository’s default branch (usually master)

        // File Update:
        // PUT https://api.github.com/repos/:owner/:repo/contents/:path


        $full_path = $params['path'] ?? '';

        while (false !== strpos($full_path,'//'))
            $full_path = str_replace('//','/',$full_path);

        $repository = '';
        $path = '';
        $result = $this->getPathParts($full_path, $repository, $path);
        if ($result === false)
            return;

        $url = "https://api.github.com/repos/$repository/contents/$path";

        // build the request headers; authentication required
        $request_headers = [
            'Accept: application/vnd.github.v3+json',
            'User-Agent: Flex.io',
            'Authorization: Token '.$this->access_token
        ];

        // get old blob id -- this is needed for replacing an existing file
        $contents = '';
        $headers = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $data) use (&$headers) {
            $headers[] = $data;
            return strlen($data);
        });
        $result = curl_exec($ch);
        curl_close($ch);

        $result = @json_decode($result, true);
        //var_dump($result);
        $blob_sha = $result['sha'] ?? '';




        // get entire file into a buffer
        $content = '';
        while (($chunk = $callback(16384)) !== false)
        {
            $content .= $chunk;
        }


        $params = [
            'path' => $path,
            'message' => 'Commit from flex.io',
            'content' => base64_encode($content)
        ];

        if (strlen($blob_sha) > 0)
        {
            $params['sha'] = $blob_sha;
        }
        $payload = json_encode($params);

        //var_dump($params);




        // put the file

        $total_written = 0;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        $result = curl_exec($ch);
        //var_dump($result);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode < 200 || $httpcode >= 300)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
    }

    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    private function getRepositories($username = null) : array
    {
        // General Request Notes:
        // Headers:
        //    Set Oauth Authorization:  'Authorization: Bearer token'
        //    Use Version 3 API using header:  'Accept: application/vnd.github.v3+json'
        //    User Agent Required:  'User-Agent: Flex.io'
        //    Follow redirects
        //    When sending non-URL parameters, specify content type header: 'Content-Type: application/json'
        // Pagination: iterate through pages, looking at the "Link" header (items paginated to 30 by default):
        //    Link: <https://api.github.com/resource?page=2>; rel="next", <https://api.github.com/resource?page=5>; rel="last"
        //    Possible values for "rel" are:
        //        next:  the link relation for the immediate next page of results.
        //        last:  the link relation for the last page of results.
        //        first: the link relation for the first page of results.
        //        prev:  the link relation for the immediate previous page of results.

        // Repository Info:
        // Request: GET https://api.github.com/user/repos
        // Returns an array of repository objects with "id" and "owner" in the main object:
        // [
        //    {
        //      "id": 1296269,
        //      "full_name": "octocat/octokit.rb",
        //      "owner": {
        //        "login": "octocat",
        //        "id": 1,
        //        ...
        //      }
        //    }
        //  ]

        // note: get the repositories for the user
        $url = "https://api.github.com/users/repos?per_page=100";

        // build the request headers; allow requests to public repos
        $request_headers = [
            'Accept: application/vnd.github.v3+json',
            'User-Agent: Flex.io'
        ];
        if ($this->authenticated())
            $request_headers[] = 'Authorization: Token '.$this->access_token;

        $repository_items = array();

        $finished = false;
        while (true)
        {
            if ($finished === true)
                break;

            // TODO: limit rate of requests

            $headers = array();
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $data) use (&$headers) {
                $headers[] = $data;
                return strlen($data);
            });
            $result = curl_exec($ch);
            curl_close($ch);

            $result = @json_decode($result, true);
            if (!is_array($result))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

            foreach ($result as $entry)
            {
                $repository_items[] = $entry;
            }

            $next_url = '';
            $finished = self::getNextPageUrl($headers, $url, $next_url);
            if ($finished === false)
                $url = $next_url;
        }

        return $repository_items;
    }

    private function getFolderItems(string $repository, string $folder) : array
    {
        // General Request Notes:
        // Headers:
        //    Set Oauth Authorization:  'Authorization: Bearer token'
        //    Use Version 3 API using header:  'Accept: application/vnd.github.v3+json'
        //    User Agent Required:  'User-Agent: Flex.io'
        //    Follow redirects
        //    When sending non-URL parameters, specify content type header: 'Content-Type: application/json'
        // Pagination: iterate through pages, looking at the "Link" header (items paginated to 30 by default):
        //    Link: <https://api.github.com/resource?page=2>; rel="next", <https://api.github.com/resource?page=5>; rel="last"
        //    Possible values for "rel" are:
        //        next:  the link relation for the immediate next page of results.
        //        last:  the link relation for the last page of results.
        //        first: the link relation for the first page of results.
        //        prev:  the link relation for the immediate previous page of results.

        // Folder Info
        // Request: GET https://api.github.com/repos/:owner/:repo/contents/:path
        // Use the "full_name" from STEP 1 for the ":owner/:repo":
        //      https://api.github.com/repos/octocat/octokit.rb/contents/:path
        // Returns an array of objects containing info about subdirectories and files;
        // note: API limits results to 1000 items in a directory; tree API supports
        // additional files; return from this API call looks like:
        // [
        //    {
        //      "type": "file",
        //      "size": 625,
        //      "name": "octokit.rb",
        //      "path": "lib/octokit.rb",
        //      "sha": "fff6fe3a23bf1c8ea0692b4a883af99bee26fd3b",
        //      ...
        //    },
        //    {
        //      "type": "dir",
        //      "size": 0,
        //      "name": "octokit",
        //      "path": "lib/octokit",
        //      "sha": "a84d88e7554fc1fa21bcbc4efae3c782a70d2b9d",
        //      ...
        //    }
        // ]

        // note: the repository is the full name of the repository, which is :owner/:repository
        $url = "https://api.github.com/repos/$repository/contents/$folder?per_page=100";

        // build the request headers; allow requests to public repos
        $request_headers = [
            'Accept: application/vnd.github.v3+json',
            'User-Agent: Flex.io'
        ];
        if ($this->authenticated())
            $request_headers[] = 'Authorization: Token '.$this->access_token;

        $folder_items = array();

        $finished = false;
        while (true)
        {
            if ($finished === true)
                break;

            // TODO: limit rate of requests

            $headers = array();
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $data) use (&$headers) {
                $headers[] = $data;
                return strlen($data);
            });
            $result = curl_exec($ch);
            curl_close($ch);

            $result = @json_decode($result, true);
            if (!is_array($result))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

            // check for result for non-existing path; if we can't find the existing path,
            // we're done
            if (isset($result['message']) && strtolower($result['message']) === "not found")
                break;

            // make sure we only have a directory array and not a file (files
            // return a key/value object; directories are indexed-based arrays)
            if (\Flexio\Base\Util::isAssociativeArray($result) === true)
                return array();

            foreach ($result as $entry)
            {
                $folder_items[] = array('id'=> null, // TODO: available?
                                        'name' => $entry['name'],
                                        'path' => $entry['path'],
                                        'size' => $entry['size'] ?? '',
                                        'modified' => '',  // TODO: available?
                                        'hash' => $entry['sha'] ?? '',
                                        'type' => ($entry['type'] == 'dir' ? 'DIR' : 'FILE'));
            }

            // find out if there's another page of results, and if so, get the url
            // for the next request and keep making requests
            // TODO: implement getNextPageUrl()
            $next_url = '';
            $finished = self::getNextPageUrl($headers, $url, $next_url);
            if ($finished === false)
                $url = $next_url;
        }

        return $folder_items;
    }

    private function initialize(array $params = null) : bool
    {
        $client_id = $GLOBALS['g_config']->github_client_id ?? '';
        $client_secret = $GLOBALS['g_config']->github_client_secret ?? '';

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

        // STEP 1: if we have an access token, create an object
        // from the access token and return it
        if (isset($params['access_token']))
        {
            $this->access_token = $params['access_token'];
            $this->owner = $params['owner'] ?? '';
            $this->repository = $params['repository'] ?? '';
            return true;
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

        // instantiate the github service using the credentials,
        // http client and storage mechanism for the token
        $oauth = $service_factory->createService('GitHub', $credentials, $storage, array('repo'));
        if (!isset($oauth))
            return false;

        // STEP 3: if we have a code parameter, we have enough information
        // to authenticate and get the token; do so and return the object
        if (isset($params['code']))
        {
            $token = $oauth->requestAccessToken($params['code']);
            $this->access_token = $token->getAccessToken();
            $this->owner = $params['owner'] ?? '';
            $this->repository = $param['repository'] ?? '';
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

    private function getPathParts(string $full_path, &$repository, &$path) : bool
    {
        // note: the base path for a repository is :owner/:repository
        $full_path = trim($full_path,'/');
        $path_parts = explode('/', $full_path);
        $path_parts = array_filter($path_parts, 'strlen');

        if (strlen($this->owner) == 0 && strlen($this->repository) == 0)
        {
            if (count($path_parts) < 2)
                return false;

            $repository = implode('/', array_splice($path_parts,0,2));
            $path = implode('/', $path_parts); // everything left over
            return true;
        }

        if (strlen($this->owner) > 0 && strlen($this->repository) == 0)
        {
            if (count($path_parts) < 2)
                return true;

            $repository = $this->owner . '/' . array_splice($path_parts,0,1)[0];
            $path = implode('/', $path_parts); // everything left over
            return true;
        }

        if (strlen($this->owner) > 0 && strlen($this->repository) > 0)
        {
            $repository = $this->owner . '/' . $this->repository;
            $path = trim($full_path,'/');
            return true;
        }

        return false;
    }

    private static function getNextPageUrl(array $headers, string $url, string &$next_url) : bool
    {
        // return true if there's another page of results; false otherwise
        // if there's another page of results, set's the next_url function
        // parameter to url to request for the next page of results

        // TODO: read through the headers and look for the link header;
        // if we don't find the link header, return false; if we find the
        // link header, see if there's a next page, and if there isn't,
        // return false, but if there is, then set the next_url to this url
        // and return true; if there isn't a next page, return false

        // example link header:
        //     Link: <https://api.github.com/resource?page=2>; rel="next", <https://api.github.com/resource?page=5>; rel="last"

        foreach ($headers as $h)
        {
            if (!preg_match('/link:/i', $h))
                continue;

            $matches = array();
            preg_match('/<(.+)>\s*;\s*rel="next"/i', $h, $matches);
            if (count($matches) < 2)
                break;

            // we have another link
            $next_url = $matches[1];
            return false;
        }

        // we're done
        return true;
    }
}
