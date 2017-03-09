<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2016-04-14
 *
 * @package flexio
 * @subpackage Services
 */


namespace Flexio\Services;

require_once dirname(dirname(__DIR__)) . '/library/phpoauthlib/src/OAuth/bootstrap.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Abstract.php';

class GoogleSheets implements \Flexio\Services\IConnection
{
    ////////////////////////////////////////////////////////////
    // member variables
    ////////////////////////////////////////////////////////////

    private $is_ok = false;
    private $access_token = '';
    private $refresh_token = '';
    private $updated = '';
    private $expires = 0;


    ////////////////////////////////////////////////////////////
    // IConnection interface
    ////////////////////////////////////////////////////////////

    public static function create($params = null)
    {
        if (!isset($params))
            return new self;

        return self::initialize($params);
    }

    public function connect($params)
    {
        // TODO: implement
    }

    public function isOk()
    {
        return $this->is_ok;
    }

    public function close()
    {
        $this->is_ok = false;
        $this->access_token = '';
        $this->refresh_token = '';
        $this->expires = 0;
    }

    public function listObjects($path = '')
    {
        $spreadsheets = $this->getSpreadsheets();

        $base_path = $path;
        if ($base_path == '')
            $base_path = '/';

        if ($path == '' || $path == '/')
        {
            $files = [];
            foreach ($spreadsheets as $spreadsheet)
            {
                $worksheets = $spreadsheet->getWorksheets();

                $fileinfo = array(
                    'spreadsheet_id' => $spreadsheet->spreadsheet_id,
                    'name' => $spreadsheet->title,
                    'path' => '/' . $spreadsheet->title,
                    'size' => null, // size unknown
                    'modified' => $spreadsheet->updated,
                    'is_dir' => (count($worksheets) > 1 ? true:false),
                    'root' => 'googlesheets'
                );

                if (count($worksheets) == 1)
                    $fileinfo['worksheet_id'] = $worksheets[0]->worksheet_id;

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
                return null;
            $worksheets = $spreadsheet->getWorksheets();

            $files = [];
            foreach ($worksheets as $worksheet)
            {
                $fileinfo = array(
                    'spreadsheet_id' => $spreadsheet->spreadsheet_id,
                    'worksheet_id' => $worksheet->worksheet_id,
                    'name' => $worksheet->title,
                    'path' => '/' . $spreadsheet->title . '/' . $worksheet->title,
                    'size' => null,
                    'modified' => null,
                    'is_dir' => false,
                    'root' => 'googlesheets'
                );

                $files[] = $fileinfo;
            }

            return $files;
        }
    }

    public function exists($path)
    {
        // TODO: implement
        return false;
    }

    public function getInfo($path)
    {
        // TODO: implement
        return false;
    }

    public function read($params, $callback)
    {
        $path = isset_or($params['path'],'');

        $spreadsheet_id = null;
        $worksheet_id = null;

        $ids = $this->getIdsFromPath($path);
        if (isset($ids['spreadsheet_id']))
            $spreadsheet_id = $ids['spreadsheet_id'];
        if (isset($ids['worksheet_id']))
            $worksheet_id = $ids['worksheet_id'];

        // if we don't have a spreadsheet id, we cannot continue
        if (!isset($spreadsheet_id))
            return;

        // if we don't have a worksheet id, use the first one inside the spreadsheet
        if (!isset($worksheet_id))
        {
            $spreadsheet = $this->getSpreadsheetById($spreadsheet_id);
            if (!$spreadsheet)
                return;
            if (count($spreadsheet->worksheets) < 1)
                return;
            $worksheet_id = $spreadsheet->worksheets[0]->worksheet_id;
        }

        $this->readFile($spreadsheet_id, $worksheet_id, $callback);
    }

    public function write($params, $callback)
    {
        $path = isset_or($params['path'],'');
        $content_type = isset_or($params['content_type'], \Flexio\Base\ContentType::MIME_TYPE_STREAM);

        // TODO: implement
    }


    ////////////////////////////////////////////////////////////
    // Google Sheets API abstraction
    ////////////////////////////////////////////////////////////

    private $spreadsheets = [];

    // returns an array of GoogleSpreadsheet objects
    public function getSpreadsheets()
    {
        if (count($this->spreadsheets) > 0)
            return $this->spreadsheets;

        if (!$this->authenticated())
            return null;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://spreadsheets.google.com/feeds/spreadsheets/private/full");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$this->access_token, 'GData-Version: 3.0']);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);
        $result = curl_exec($ch);


        $doc = new \DOMDocument;
        if ($doc->loadXML($result))
        {
            $entries = $doc->getElementsByTagName("entry");
            foreach ($entries as $entry)
            {
                $titles = $entry->getElementsByTagName("title");
                if (count($titles) != 1)
                    continue;

                $title = $titles[0]->nodeValue;

                $ids = $entry->getElementsByTagName("id");
                if (count($ids) != 1)
                    continue;
                $id = $ids[0]->nodeValue;

                $id = \Flexio\Base\Util::afterLast($id, '/');


                $updateds = $entry->getElementsByTagName("updated");
                if (count($updateds) != 1)
                    continue;
                $updated = $updateds[0]->nodeValue;



                $spreadsheet = new \Flexio\Services\GoogleSpreadsheet;
                $spreadsheet->access_token = $this->access_token;
                $spreadsheet->title = $title;
                $spreadsheet->spreadsheet_id = $id;
                $spreadsheet->updated = $updated;
                $spreadsheet->getWorksheets($ch);
                $this->spreadsheets[] = $spreadsheet;
            }
        }

        curl_close($ch);

        return $this->spreadsheets;
    }

    public function getSpreadsheetByTitle($title)
    {
        $spreadsheets = $this->getSpreadsheets();
        foreach ($spreadsheets as $spreadsheet)
        {
            if (0 == strcasecmp($spreadsheet->title, $title))
                return $spreadsheet;
        }
        return false;
    }

    public function getSpreadsheetById($spreadsheet_id)
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

    public function getIdsFromPath($path)
    {
        if (strlen($path) == 0)
            return false;

        // strip off leading slash
        if ($path[0] == '/')
            $path = substr($path, 1);

        $parts = explode('/', $path);
        if (count($parts) < 1)
            return false;

        $objs = $this->listObjects('/'.$parts[0]);
        if (count($objs) < 1)
            return false;

        foreach ($objs as $obj)
        {
            if (count($parts) < 2 || $parts[1]=='' || 0 == strcasecmp($parts[1], $obj['name']))
            {
                return array('spreadsheet_id' => $obj['spreadsheet_id'],
                             'worksheet_id' => $obj['worksheet_id']);
            }
        }

        return false;
    }

    public function readFile($spreadsheet_id, $worksheet_id, $callback)
    {
        if (!$this->authenticated())
            return null;

        $buf = '';
        $info = array( 'currow' => 1, 'data' => [] );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://spreadsheets.google.com/feeds/cells/$spreadsheet_id/$worksheet_id/private/full");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$this->access_token, 'GData-Version: 3.0']);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $data) use (&$callback, &$buf, &$info) {
            $buf .= $data;

            if (strlen($buf) > 500000)
                self::processChunk($buf, $info, $callback, false);

            return strlen($data);
        });
        $result = curl_exec($ch);
        curl_close($ch);

        // process last remaining chunk

        self::processChunk($buf, $info, $callback, true);

        return true;
    }

    private static function processChunk(&$buf, &$info, $callback, $last)
    {
        // fetch row and column count from the beginning of the XML stream
        if (!isset($info['rowcount']) || !isset($info['colcount']))
        {
            $idxc = strpos($buf, "</gs:colCount>");
            if ($idxc === false)
                return;
            $idxr = strpos($buf, "</gs:rowCount>");
            if ($idxr === false)
                return;
            $idx = max($idxc, $idxr);
            $xml = substr($buf, 0, $idx+14);
            $doc = new \DOMDocument;
            if (!$doc->loadXML($xml . '</feed>'))
            {
                $buf = '';
                return;
            }
            $e = $doc->getElementsByTagNameNS("http://schemas.google.com/spreadsheets/2006", "rowCount");
            if (!$e || $e->length != 1)
                return;
            $info['rowcount'] = $e[0]->textContent;
            $e = $doc->getElementsByTagNameNS("http://schemas.google.com/spreadsheets/2006", "colCount");
            if (!$e || $e->length != 1)
                return;
            $info['colcount'] = $e[0]->textContent;
        }


        if (!isset($info['prologue']))
        {
            $idx = strpos($buf, '<feed');
            if ($idx === false)
                return;
            $idx = strpos($buf, '>', $idx);
            if ($idx === false)
                return;

            $info['prologue'] = substr($buf, 0, $idx+1);
            $buf = substr($buf, $idx+1);
        }


        // find the closing </entry>

        $idx = strrpos($buf, "</entry>");
        if ($idx === false)
        {
            // can't find a closing entry; do nothing
            return;
        }


        $remainder = substr($buf, $idx+8);
        $buf = substr($buf, 0, $idx+8);

        // parse the buffer
        $doc = new \DOMDocument;
        if (!$doc->loadXML($info['prologue'] . $buf . (strrpos($buf,'</feed>')===false?'</feed>':'')))
        {
            //$buf = '';
            return;
        }


        $buf = $remainder;

        foreach ($doc->getElementsByTagName('entry') as $node)
        {
            $content = null;
            $row = null;
            $col = null;

            foreach ($node->childNodes as $c)
            {
                if ($c->tagName == 'content')
                {
                    $content = $c->textContent;
                }
                 else if ($c->tagName == 'gs:cell')
                {
                    $row = $c->hasAttribute('row') ? $c->getAttribute('row') : null;
                    $col = $c->hasAttribute('col') ? $c->getAttribute('col') : null;
                }
            }

            if (isset($row) && isset($col) && isset($content))
            {
                if (!isset($info['data'][$row]))
                    $info['data'][$row] = [];
                $info['data'][$row][$col] = $content;

                if ($row != $info['currow'])
                {
                    $idx = 0;
                    $row_data = $info['data'][$info['currow']];
                    $row_data_to_return = array();
                    foreach($row_data as $r)
                    {
                        $idx++;
                        $row_data_to_return[self::stringFromColumnIndex($idx)] = $r;
                    }

                    $callback($row_data_to_return);
                    unset($info['data'][$info['currow']]);
                    $info['currow'] = $row;
                }
            }
        }

        if ($last)
        {
            if (isset($info['data'][$info['currow']]))
            {
                $idx = 0;
                $row_data = $info['data'][$info['currow']];
                $row_data_to_return = array();
                foreach($row_data as $r)
                {
                    $idx++;
                    $row_data_to_return[self::stringFromColumnIndex($idx)] = $r;
                }

                $callback($row_data_to_return);
                unset($info['data'][$info['currow']]);
            }
        }
    }



    // creates a new spreadsheet via the google docs v3 api; returns file id
    // or false if something goes wrong

    public function createFile($name)
    {
        $postdata = json_encode(array(
            "name" => $name,
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

        curl_close($ch);

        $result = @json_decode($result, true);

        $spreadsheet_id = isset_or($result['id'],'');
        if (strlen($spreadsheet_id) == 0)
            return false;

        $spreadsheet = new \Flexio\Services\GoogleSpreadsheet;
        $spreadsheet->access_token = $this->access_token;
        $spreadsheet->title = $name;
        $spreadsheet->spreadsheet_id = $spreadsheet_id;
        $spreadsheet->getWorksheets();

        // if spreadsheets array is already populated, add it
        if (count($this->spreadsheets) > 0)
        {
            $this->spreadsheets[] = $spreadsheet;
        }

        $spreadsheet->worksheets[0]->setInfo(null, 10, 10);

        return $spreadsheet;
    }

    public function getTokens()
    {
        return [ 'access_token' => $this->access_token,
                 'refresh_token' => $this->refresh_token,
                 'expires' => $this->expires ];
    }

    private function authenticated()
    {
        if (strlen($this->access_token) > 0)
            return true;

        return false;
    }

    private static function initialize($params)
    {
        $client_id = isset_or($GLOBALS['g_config']->googledrive_client_id, '');
        $client_secret = isset_or($GLOBALS['g_config']->googledrive_client_secret, '');

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

            $expires = isset_or($params['expires'], null);
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
                $object->refresh_token = isset_or($params['refresh_token'],'');
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

                $access_token = isset_or($params['access_token'], null);
                if (!isset($params['refresh_token']) || strlen($params['refresh_token']) == 0)
                    return null; // refresh token is missing
                $refresh_token = $params['refresh_token'];

                $token = new \StdOAuth2Token($access_token, $refresh_token);
                if (isset($params['token_expires']) && !is_null($params['token_expires']) && $params['token_expires'] > 0)
                    $token->setEndOfLife($params['token_expires']);

                $token = $oauth->refreshAccessToken($token);
                if (!$token)
                    return null;

                $object = new self;
                $object->access_token = $token->getAccessToken();
                $object->refresh_token = $refresh_token;
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

    private static function createService($oauth_callback)
    {
        $client_id = isset_or($GLOBALS['g_config']->googledrive_client_id, '');
        $client_secret = isset_or($GLOBALS['g_config']->googledrive_client_secret, '');

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

    private static function stringFromColumnIndex($idx)
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

    public function getWorksheets($_ch = null)
    {
        if (count($this->worksheets) > 0)
            return $this->worksheets;

        if (!$_ch)
            $ch = curl_init();
             else
            $ch = $_ch;
        curl_setopt($ch, CURLOPT_URL, "https://spreadsheets.google.com/feeds/worksheets/".$this->spreadsheet_id."/private/full");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$this->access_token, 'GData-Version: 3.0']);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);
        $result = curl_exec($ch);
        if (!$_ch)
            curl_close($ch);

        $this->worksheets = [];

        $doc = new \DOMDocument;
        if ($doc->loadXML($result))
        {
            $entries = $doc->getElementsByTagName("entry");
            foreach ($entries as $entry)
            {
                $ids = $entry->getElementsByTagName("id");
                if (!isset($ids) || $ids->length != 1)
                    continue;
                $id = $ids[0]->nodeValue;

                $titles = $entry->getElementsByTagName("title");
                if (!isset($titles) || $titles->length != 1)
                    continue;
                $title = $titles[0]->nodeValue;


                $edit_link = '';
                $links = $entry->getElementsByTagName("link");
                foreach ($links as $link)
                {
                    if ($link->getAttribute('rel') == 'edit')
                    {
                        $edit_link = $link->getAttribute('href');
                    }
                }

                $e = $entry->getElementsByTagNameNS("http://schemas.google.com/spreadsheets/2006", "rowCount");
                if (!$e || $e->length != 1)
                    continue;
                $row_count = (int)$e[0]->textContent;
                $e = $entry->getElementsByTagNameNS("http://schemas.google.com/spreadsheets/2006", "colCount");
                if (!$e || $e->length != 1)
                    continue;
                $col_count = (int)$e[0]->textContent;


                $worksheet_id = \Flexio\Base\Util::afterLast($id, '/');

                $worksheet = new \Flexio\Services\GoogleWorksheet;
                $worksheet->access_token = $this->access_token;
                $worksheet->spreadsheet_id = $this->spreadsheet_id;
                $worksheet->worksheet_id = $worksheet_id;
                $worksheet->title = $title;
                $worksheet->row_count = $row_count;
                $worksheet->col_count = $col_count;
                $worksheet->edit_link = $edit_link;

                $this->worksheets[] = $worksheet;
            }
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


    public function setInfo($title, $rows, $cols)
    {
        $title = isset($title) ? $title : $this->title;
        $rows = isset($rows) ? (int)$rows : $this->row_count;
        $cols = isset($cols) ? (int)$cols : $this->col_count;

        $xml = <<<EOL
        <entry xmlns="http://www.w3.org/2005/Atom"
               xmlns:gs="http://schemas.google.com/spreadsheets/2006">
            <title type="text">$title</title>
            <gs:rowCount>$rows</gs:rowCount>
            <gs:colCount>$cols</gs:colCount>
        </entry>
EOL;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->edit_link);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/atom+xml', 'If-Match: *', 'Authorization: Bearer '.$this->access_token]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        curl_close($ch);

        $this->row_count = $rows;
        $this->col_count = $cols;
    }

    // $cells should be an array like this
    // [ { "row" => 1, "col" => 1, "value" => "contents" }, { ... } ]

    public function setCells($cells)
    {
        $spreadsheet_id = $this->spreadsheet_id;
        $worksheet_id = $this->worksheet_id;

        $id = 0;
        $entries = '';

        foreach ($cells as $cell)
        {
            if (!isset($cell['row']) || !isset($cell['col']) || !isset($cell['value']))
                continue;


            $row = (int)$cell['row'];
            $col = (int)$cell['col'];
            $value = htmlspecialchars($cell['value']);

            $id++;
            $rc = "R" . $row . 'C' . $col;

            $entries .= <<<EOL
<entry>
<batch:id>I$id</batch:id>
<batch:operation type="update"/>
<id>https://spreadsheets.google.com/feeds/cells/$spreadsheet_id/$worksheet_id/private/full/$rc</id>
<gs:cell row="$row" col="$col" inputValue="$value"/>
</entry>
EOL;
        }

        $xml = <<<EOL
            <feed xmlns="http://www.w3.org/2005/Atom"
                  xmlns:batch="http://schemas.google.com/gdata/batch"
                  xmlns:gs="http://schemas.google.com/spreadsheets/2006">
            <id>https://spreadsheets.google.com/feeds/cells/$spreadsheet_id/$worksheet_id/private/full</id>
            $entries
            </feed>
EOL;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://spreadsheets.google.com/feeds/cells/$spreadsheet_id/$worksheet_id/private/full/batch");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'If-Match: *', 'Authorization: Bearer '.$this->access_token]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        curl_close($ch);
    }

    public $insert_cols = 0;
    public $insert_cells = [];
    public $insert_row = 1;
    public $current_rowcount = 0;

    public function startInsert($fields)
    {
        $this->setInfo(null, 500, count($fields));
        $this->current_rowcount = 500;
        $this->insert_row = 1;

        $this->insertRow($fields);

        return true;
    }

    public function insertRow($row)
    {
        $colidx = 1;
        foreach ($row as $value)
        {
            $this->insert_cells[] = array('row' => $this->insert_row, 'col' => $colidx, 'value' => $value);
            ++$colidx;
        }

        $this->insert_row++;

        if (count($this->insert_cells) > 1000)
            $this->flush();
    }

    public function flush()
    {
        // if we need to extend the size of our spreadsheet, do so
        if ($this->insert_row > $this->current_rowcount)
        {
            $this->current_rowcount += 500;
            $this->setInfo(null, $this->current_rowcount, null);
        }

        $this->setCells($this->insert_cells);
        $this->insert_cells = [];
    }

    public function finishInsert()
    {
        $this->flush();
        $this->setInfo(null, $this->insert_row - 1, null);
    }
}
