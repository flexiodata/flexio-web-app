<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2016-04-14
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


class GoogleSheets implements \Flexio\IFace\IConnection,
                              \Flexio\IFace\IOAuthConnection,
                              \Flexio\IFace\IFileSystem
{
    private $authorization_uri = '';
    private $access_token = '';
    private $refresh_token = '';
    private $expires = 0;
    private $updated = '';

    public static function create(array $params = null) : \Flexio\Services\GoogleSheets
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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CONNECTION_FAILED);

        $base_path = $path;
        if ($base_path == '')
            $base_path = '/';

        if ($path == '' || $path == '/')
        {
            $files = [];

            $spreadsheets = $this->getSpreadsheets();

            foreach ($spreadsheets as $spreadsheet)
            {
                //$worksheets = $spreadsheet->getWorksheets();

                $fileinfo = array(
                    'id' => sha1($spreadsheet->spreadsheet_id),
                    'spreadsheet_id' => $spreadsheet->spreadsheet_id,
                    'name' => $spreadsheet->title,
                    'path' => '/' . $spreadsheet->title,
                    'size' => null, // size unknown
                    'modified' => $spreadsheet->updated,
                    'hash' => '', // TODO: available?
                    'type' => 'DIR'  // (count($worksheets) > 1 ? 'DIR':'FILE')
                );

               // if (count($worksheets) == 1)
               //     $fileinfo['worksheet_id'] = $worksheets[0]->worksheet_id;

                $files[] = $fileinfo;
            }

            return $files;
        }
         else
        {
            if ($path[0] == '/')
                $path = substr($path,1);
            $spreadsheet = $this->getSpreadsheetByTitle($path);

            if (!$spreadsheet)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE, "Cannot locate spreadsheet '$path'");
            $worksheets = $spreadsheet->getWorksheets();

            $files = [];
            foreach ($worksheets as $worksheet)
            {
                $fileinfo = array(
                    'id' => sha1($spreadsheet->spreadsheet_id . ';' . $worksheet->worksheet_id),
                    'spreadsheet_id' => $spreadsheet->spreadsheet_id,
                    'worksheet_id' => $worksheet->worksheet_id,
                    'name' => $worksheet->title,
                    'path' => '/' . $spreadsheet->title . '/' . $worksheet->title,
                    'size' => null,
                    'modified' => null,
                    'hash' => '', // TODO: available?
                    'type' => 'FILE'
                );

                $files[] = $fileinfo;
            }

            return $files;
        }
    }

    public function getFileInfo(string $path) : array
    {
        if (!$this->authenticated())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        $ids = $this->getIdsFromPath($path);
        if (isset($ids['spreadsheet_id']))
            $spreadsheet_id = $ids['spreadsheet_id'];
             else
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        $spreadsheet = $this->getSpreadsheetById($spreadsheet_id);
        if (!$spreadsheet)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        if (isset($ids['worksheet_title']))
        {
            $worksheet_title = $ids['worksheet_title'];
        }
         else
        {
            $worksheets = $spreadsheet->getWorksheets();
            if (count($worksheets) < 1)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
            $worksheet_title = $spreadsheet->worksheets[0]->title;
        }


        $arr = \Flexio\Base\File::splitPath($path);
        $name = '';
        if (count($arr) > 0)
            $name = $arr[count($arr)-1];

        $r = array('id'=> md5(strtolower(trim($path,'/'))) ?? null,
                   'name' => $name,
                   'size' => null,
                   'modified' => null,
                   'hash' => '', // TODO: available?
                   'type' => 'FILE');

        // figure out structure
        $url = "https://sheets.googleapis.com/v4/spreadsheets/".rawurlencode($spreadsheet_id)."/values/".rawurlencode($worksheet_title);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$this->access_token, 'GData-Version: 3.0']);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        $http_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $column_names = array();

        $result = @json_decode($result, true);

        if (isset($result['values']))
        {
            foreach ($result['values'] as $row)
            {
                while (count($row) > count($column_names))
                {
                    $column_names[] = self::stringFromColumnIndex(count($column_names)+1);
                }
            }

            if (count($column_names) == 0)
            {
                $column_names[] = 'a';
            }

            $r['structure'] = array();
            foreach ($column_names as $column)
            {
                $r['structure'][] = [ 'name' => $column, 'type' => 'text' ];
            }
        }

        return $r;
    }

    public function exists(string $path) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    private function internalCreateFile(string $path, array $properties = []) // TODO: add return type
    {
        // bust our spreadsheet list cache
        $this->spreadsheets = [];

        $title = trim($path, "/ \t\r\n");

        $postdata = json_encode(array(
            "properties" => [
                "title" => $title
            ]
        ));

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://sheets.googleapis.com/v4/spreadsheets");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer '.$this->access_token]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        $http_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);


        if ($http_response_code >= 400)
        {
            if ($http_response_code == 401)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAUTHORIZED, "Access unauthorized");
                 else
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::GENERAL, "Unable to create sheet");
        }

        $result = @json_decode($result, true);

        $spreadsheet_id = $result['spreadsheetId'] ?? '';
        if (strlen($spreadsheet_id) == 0)
            return false;

        $spreadsheet = new \Flexio\Services\GoogleSpreadsheet;
        $spreadsheet->access_token = $this->access_token;
        $spreadsheet->title = "Sheet1";
        $spreadsheet->spreadsheet_id = $spreadsheet_id;
        $spreadsheet->getWorksheets();

        return $spreadsheet;


/*
        $path = trim($path, "/ \t\r\n");

        // creates a new spreadsheet via the google docs v3 api

        $postdata = json_encode(array(
            "name" => $path,
            "mimeType" => "application/vnd.google-apps.spreadsheet"
            //  "parents" => [ $folderid ]
        ));

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/drive/v3/files");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer '.$this->access_token]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        $http_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_response_code >= 400)
        {
            if ($http_response_code == 401)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAUTHORIZED, "Access unauthorized");
                 else
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::GENERAL, "Unable to create sheet");
        }

        $result = @json_decode($result, true);

        $spreadsheet_id = $result['id'] ?? '';
        if (strlen($spreadsheet_id) == 0)
            return false;

        $spreadsheet = new \Flexio\Services\GoogleSpreadsheet;
        $spreadsheet->access_token = $this->access_token;
        $spreadsheet->title = "Sheet1";
        $spreadsheet->spreadsheet_id = $spreadsheet_id;
        $spreadsheet->getWorksheets();

        return $spreadsheet;
*/
    }

    public function createFile(string $path, array $properties = []) : bool
    {
        $spreadsheet = $this->internalCreateFile($path, $properties);
        return $spreadsheet ? true : false;
    }

    public function createDirectory(string $path, array $properties = []) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function unlink(string $path) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function open($path) : \Flexio\IFace\IStream
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function read(array $params, callable $callback) // TODO: add return type
    {
        $spreadsheet_id = null;
        $worksheet_title = null;

        if (isset($params['spreadsheet_id']))
        {
            $spreadsheet_id = $params['spreadsheet_id'];
            if (isset($params['worksheet_title']))
                $worksheet_title = $params['worksheet_title'];
        }
         else
        {
            $path = $params['path'] ?? '';

            $ids = $this->getIdsFromPath($path);
            if (isset($ids['spreadsheet_id']))
                $spreadsheet_id = $ids['spreadsheet_id'];
            if (isset($ids['worksheet_title']))
                $worksheet_title = $ids['worksheet_title'];
        }

        // if we don't have a spreadsheet id, we cannot continue
        if (!isset($spreadsheet_id))
            return;

        // if we don't have a worksheet id, use the first one inside the spreadsheet
        if (!isset($worksheet_title))
        {
            $spreadsheet = $this->getSpreadsheetById($spreadsheet_id);
            if (!$spreadsheet)
                return;
            $worksheets = $spreadsheet->getWorksheets();
            if (count($worksheets) < 1)
                return;
            $worksheet_title = $spreadsheet->worksheets[0]->title;
        }

        $this->readFile($spreadsheet_id, $worksheet_title, $callback);
    }

    public function write(array $params, callable $callback) // TODO: add return type
    {
        $spreadsheet = null;
        $worksheet = null;

        if (isset($params['spreadsheet_id']))
        {
            $spreadsheet_id = $params['spreadsheet_id'];

            $spreadsheet = $this->getSpreadsheetById($spreadsheet_id);
            if (!$spreadsheet)
                return; // throw

            if (isset($params['worksheet_title']))
            {
                $worksheets = $spreadsheet->getWorksheets();
                foreach ($worksheets as $w)
                {
                    if ($w->title == $params['worksheet_title'])
                        $worksheet = $w;
                }
                if (!$worksheet)
                    return; // throw
            }

        }
         else if (isset($params['path']))
        {
            $path = $params['path'] ?? '';

            $spreadsheet = $this->internalCreateFile($path);
            if (!$spreadsheet)
                return; // throw
            $worksheets = $spreadsheet->getWorksheets();
            if (count($worksheets) < 1)
                return; // throw
            $worksheet= $spreadsheet->worksheets[0];
        }
         else
        {
            // throw
            return;
        }

        $worksheet->startInsert([]);

        while (($row = $callback()) !== false)
        {
            $worksheet->insertRow($row);
        }

        $worksheet->finishInsert();
    }

    public function insert(array $params, array $rows /*an array of rows*/) // TODO: add return type
    {
        $spreadsheet_id = null;
        $worksheet_title = null;
        $worksheet = null;

        if (isset($params['spreadsheet_id']))
        {
            $spreadsheet_id = $params['spreadsheet_id'];
            $spreadsheet = $this->getSpreadsheetById($spreadsheet_id);
            if (!$spreadsheet)
                return; // throw
            if (isset($params['worksheet_title']))
            {
                $worksheets = $spreadsheet->getWorksheets();
                foreach ($worksheets as $w)
                {
                    if ($w->title == $params['worksheet_title'])
                        $worksheet = $w;
                }
                if (!$worksheet)
                    return; // throw
            }
        }
         else
        {
            $path = $params['path'] ?? '';

            $ids = $this->getIdsFromPath($path);
            if (isset($ids['spreadsheet_id']))
            {
                $spreadsheet_id = $ids['spreadsheet_id'];
                $spreadsheet = $this->getSpreadsheetById($spreadsheet_id);
                if (!$spreadsheet)
                    return; // throw
                $worksheets = $spreadsheet->getWorksheets();
                if (count($worksheets) < 1)
                    return; // throw
                $worksheet = $spreadsheet->worksheets[0];
            }
        }

        if (!$worksheet)
        {
            return; // throw
        }

        $worksheet->startInsert([]);

        foreach ($rows as $row)
        {
            $worksheet->insertRow($row);
        }

        $worksheet->finishInsert();

        return true;
    }

    ////////////////////////////////////////////////////////////
    // Google Sheets API abstraction
    ////////////////////////////////////////////////////////////

    private $spreadsheets = [];

    public function getSpreadsheets() // TODO: add return type
    {
        if (count($this->spreadsheets) > 0)
            return $this->spreadsheets;

        if (!$this->authenticated())
            return null;

        $file_limit = 1000; // limit return results to 1000; max is 1000, default is 100

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/drive/v3/files?pageSize=$file_limit&fields=files(id%2Cname%2CmodifiedTime)&q=mimeType%3D'application%2Fvnd.google-apps.spreadsheet'+and+trashed=false");
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


        $ch = curl_init();
        $files = array();
        foreach ($result as $row)
        {
            if (!isset($row['name']) || !isset($row['id']) || !isset($row['modifiedTime']))
                continue;

            $spreadsheet = new \Flexio\Services\GoogleSpreadsheet;
            $spreadsheet->access_token = $this->access_token;
            $spreadsheet->title = $row['name'];
            $spreadsheet->spreadsheet_id = $row['id'];
            $spreadsheet->updated = $row['modifiedTime'];
            //$spreadsheet->getWorksheets($ch);
            $this->spreadsheets[] = $spreadsheet;
        }
        curl_close($ch);

        return $this->spreadsheets;
    }

    public function getSpreadsheetByTitle(string $title) // TODO: add return type
    {
        $spreadsheets = $this->getSpreadsheets();
        foreach ($spreadsheets as $spreadsheet)
        {
            if (0 == strcasecmp($spreadsheet->title, $title)) // TODO: add return type
                return $spreadsheet;
        }
        return false;
    }

    public function getSpreadsheetById(string $spreadsheet_id) // TODO: add return type
    {
        $spreadsheets = $this->getSpreadsheets();
        foreach ($spreadsheets as $spreadsheet)
        {
            if (0 == strcmp($spreadsheet->spreadsheet_id, $spreadsheet_id))
                return $spreadsheet;
        }
        return false;
    }

    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    public function getIdsFromPath(string $path) // TODO: add return type
    {
        if (strlen($path) == 0)
            return false;

        // strip off leading slash
        if ($path[0] == '/')
            $path = substr($path, 1);

        $parts = explode('/', $path);
        if (count($parts) < 1)
            return false;

        $objs = $this->list('/'.$parts[0]);
        if (count($objs) < 1)
            return false;

        foreach ($objs as $obj)
        {
            if (count($parts) < 2 || $parts[1]=='' || 0 == strcasecmp($parts[1], $obj['name']))
            {
                return array('spreadsheet_id' => $obj['spreadsheet_id'],
                             'worksheet_id' => $obj['worksheet_id'],
                             'worksheet_title' => $obj['name']);
            }
        }

        return false;
    }

    public function readFile(string $spreadsheet_id, string $worksheet_title, callable $callback) // TODO: add return type
    {
        $url = "https://sheets.googleapis.com/v4/spreadsheets/".rawurlencode($spreadsheet_id)."/values/".rawurlencode($worksheet_title);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$this->access_token, 'GData-Version: 3.0']);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        $http_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);


        curl_close($ch);


        $column_names = array();

        $result = @json_decode($result, true);

        if (isset($result['values']))
        {
            foreach ($result['values'] as $row)
            {
                while (count($row) > count($column_names))
                {
                    $column_names[] = self::stringFromColumnIndex(count($column_names)+1);
                }
            }

            foreach ($result['values'] as $row)
            {
                if (count($row) < count($column_names))
                    $row = array_pad($row, count($column_names), '');

                $row = array_combine($column_names, $row);

                $callback($row);
            }
        }

        return true;
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

        // STEP 2: if we have a code parameter, we have enough information
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

        // STEP 3: we don't have a code parameter, so we need more
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

    public static function stringFromColumnIndex(int $idx) : string
    {
        // takes a numeric index and converts it to a suitable
        // spreadsheet column (lowercase):
        //   1 -> a
        //   2 -> b

        if ($idx < 1)
            return '';

        // zero-based calculation
        $idx = $idx - 1;
        for($str = ''; $idx >= 0; $idx = intval($idx/26) - 1)
            $str = chr($idx%26 + 0x41) . $str;

        return strtolower($str);
    }
}


class GoogleSpreadsheet
{
    public $access_token;
    public $spreadsheet_id = '';
    public $title = '';
    public $worksheets = [];

    public function getWorksheets($_ch = null) : array
    {
        if (count($this->worksheets) > 0)
        {
            // use cached copy
            return $this->worksheets;
        }

        if (!$_ch)
            $ch = curl_init();
             else
            $ch = $_ch;
        curl_setopt($ch, CURLOPT_URL, "https://sheets.googleapis.com/v4/spreadsheets/".$this->spreadsheet_id."?fields=sheets.properties");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$this->access_token, 'GData-Version: 3.0']);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        $result = curl_exec($ch);
        $http_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (!$_ch)
            curl_close($ch);

        if ($http_response_code >= 400)
        {
            if ($http_response_code == 401)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAUTHORIZED, "Access unauthorized");
                 else
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE, "Cannot access sheet's worksheets");
        }

        $result = json_decode($result,true);
        if (isset($result['sheets']))
            $sheets = $result['sheets'];
             else
            $sheets = array();

        $this->worksheets = [];

        foreach ($sheets as $sheet)
        {
            if (!isset($sheet['properties']))
                continue;

            $worksheet = new \Flexio\Services\GoogleWorksheet;
            $worksheet->access_token = $this->access_token;
            $worksheet->spreadsheet_id = $this->spreadsheet_id;
            $worksheet->worksheet_id = $sheet['properties']['sheetId'];
            $worksheet->title = $sheet['properties']['title'];
            $worksheet->row_count = $sheet['properties']['sheetType'] == 'GRID' ? $sheet['properties']['gridProperties']['rowCount'] : 0;
            $worksheet->col_count = $sheet['properties']['sheetType'] == 'GRID' ? $sheet['properties']['gridProperties']['columnCount'] : 0;

            $this->worksheets[] = $worksheet;
        }


        return $this->worksheets;
    }
}


class GoogleWorksheet
{
    public $access_token;
    public $spreadsheet_id;
    public $worksheet_id = '';
    public $title = '';
    public $row_count = 0;
    public $col_count = 0;
    public $edit_link = '';

    public $rows = [];

    public function startInsert($fields) : void // TODO: add parameter type
    {
        $this->ch = curl_init();
    }

    public function insertRow($row) : void // TODO: add parameter type
    {
        if (is_array($row))
        {
            $this->rows[] = array_values($row);
        }
         else
        {
            $this->rows[] = array(''.$row);
        }

        if (count($this->rows) > 500)
            $this->flush();
    }

    public function flush() : void
    {
        $postdata = json_encode(array(
            "values" => $this->rows
        ));
        $this->rows = [];


//die("https://sheets.googleapis.com/v4/spreadsheets/".$this->spreadsheet_id."/values/".$this->title.":append?valueInputOption=RAW");

        curl_setopt($this->ch, CURLOPT_URL, "https://sheets.googleapis.com/v4/spreadsheets/".$this->spreadsheet_id."/values/".$this->title.":append?valueInputOption=RAW");
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer '.$this->access_token]);
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        $result = curl_exec($this->ch);
        $http_response_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
    }

    public function finishInsert() : void
    {
        $this->flush();
        curl_close($this->ch);
    }
}
