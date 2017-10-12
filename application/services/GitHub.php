<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
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


require_once dirname(dirname(__DIR__)) . '/library/phpoauthlib/src/OAuth/bootstrap.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Abstract.php';

class GitHub implements \Flexio\Services\IConnection
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
        // returns the directory tree for all repositories the user
        // has access to, starting with the repository as the root

        if (!$this->authenticated())
            return array();

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

        // STEP 1: list the respositories:
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

        // STEP 2: get the folders/files for a repository
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

        $files = array();
        return $files;
    }

    public function exists(string $path) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return false;
    }

    public function getInfo(string $path) : array
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return array();
    }

    public function read(array $params, callable $callback)
    {
        // STEP 1: get the contents of a file in a directory
        // Request: GET https://api.github.com/repos/:owner/:repo/contents/:path
        // Returns an object containing the base64 content; note: API works with files up to 1MB
        // {
        //    "type": "file",
        //    "encoding": "base64",
        //    "size": 5362,
        //    "name": "README.md",
        //    "path": "README.md",
        //    "content": "encoded content ...",
        //    "sha": "3d21ec53a331a6f037a91c368710b99387d012c1",
        //    "url": "https://api.github.com/repos/octokit/octokit.rb/contents/README.md",
        //    "git_url": "https://api.github.com/repos/octokit/octokit.rb/git/blobs/3d21ec53a331a6f037a91c368710b99387d012c1",
        //    "html_url": "https://github.com/octokit/octokit.rb/blob/master/README.md",
        //    "download_url": "https://raw.githubusercontent.com/octokit/octokit.rb/master/README.md",
        //    "_links": {
        //      "git": "https://api.github.com/repos/octokit/octokit.rb/git/blobs/3d21ec53a331a6f037a91c368710b99387d012c1",
        //      "self": "https://api.github.com/repos/octokit/octokit.rb/contents/README.md",
        //      "html": "https://github.com/octokit/octokit.rb/blob/master/README.md"
        //    }
        // }
    }

    public function write(array $params, callable $callback)
    {
        // File Create:
        // Request: PUT https://api.github.com/repos/:owner/:repo/contents/:path
        // Params:
        // path     string  Required.  The content path.
        // message  string  Required.  The commit message.
        // content  string  Required.  The new file content, Base64 encoded.
        // branch   string  The branch name. Default: the repository’s default branch (usually master)

        // File Update:
        // PUT https://api.github.com/repos/:owner/:repo/contents/:path
    }


    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    public function getTokens() : array
    {
        return [ 'access_token' => $this->access_token,
                 'refresh_token' => '',
                 'expires' => 0  ];
    }

    private function getFolderItems($owner, $repository, $folder)
    {
        if (!$this->authenticated())
        return array();

        $url = "https://api.github.com/repos/$owner/$repository/contents/$folder";

        $folder_items = array();

        $finished = false;
        while (true)
        {
            if ($finished === true)
                break;

            // TODO: limit rate of requests

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Token '.$this->access_token));
            curl_setopt($ch, CURLOPT_USERAGENT, 'Flex.io'); // user agent required for GitHub
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $result = curl_exec($ch);
            curl_close($ch);

            $result = @json_decode($result, true);
            if (!is_array($result))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

            foreach ($result as $entry)
            {
                $folder_items[] = array('id'=> null, // TODO: available?
                                        'name' => $entry['name'],
                                        'path' => $entry['path'],
                                        'size' => $entry['size'] ?? '',
                                        'modified' => '',  // TODO: available?
                                        'is_dir' => ($entry['type'] == 'dir' ? true : false));
            }

/*
function HandleHeaderLine( $curl, $header_line ) {
    echo "<br>YEAH: ".$header_line; // or do whatever
    return strlen($header_line);
}


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://www.google.com");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADERFUNCTION, "HandleHeaderLine");
$body = curl_exec($ch);
*/


            // TODO: get the link header info and make another request
            $finished = true;
        }

        return $folder_items;
    }

    private function authenticated() : bool
    {
        if (strlen($this->access_token) > 0)
            return true;

        return false;
    }

    private static function initialize(array $params)
    {
        $client_id = $GLOBALS['g_config']->github_client_id ?? '';
        $client_secret = $GLOBALS['g_config']->github_client_secret ?? '';

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

        // instantiate the github service using the credentials,
        // http client and storage mechanism for the token
        $service = $service_factory->createService('GitHub', $credentials, $storage, array());
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
