<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-02-02
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Abstract.php';

class Twilio implements \Flexio\Services\IConnection
{
    ////////////////////////////////////////////////////////////
    // member variables
    ////////////////////////////////////////////////////////////

    private $is_ok = false;
    private $apikey = '';
    private $apitoken = '';
    private $pagesize = 200; // rows to request per request; 200 is maximum allowed per request
    private $request_throttle = 250; // milliseconds to wait between requests; pipeline deals allows up to 5 requests per second


    ////////////////////////////////////////////////////////////
    // IConnection interface
    ////////////////////////////////////////////////////////////

    public static function create(array $params = null) : \Flexio\Services\Twilio
    {
        $service = new self;

        if (isset($params))
            $service->connect($params);

        return $service;
    }

    public function connect(array $params) : bool
    {
        $this->close();

        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'key'   => array('type' => 'string', 'required' => true),
                'token' => array('type' => 'string', 'required' => true)
            ))->getParams()) === false)
            return false;

        $apikey = $params['key'];
        $apitoken = $params['token'];
        $this->initialize($apikey, $apitoken);
        return $this->isOk();
    }

    public function isOk() : bool
    {
        return $this->is_ok;
    }

    public function close()
    {
        $this->is_ok = false;
        $this->apikey = '';
        $this->apitoken = '';
    }

    public function listObjects(string $path = '') : array
    {
        if (!$this->isOk())
            return array();

        $objects = array();

        $definitions = $this->getDefinitions();
        foreach ($definitions as $d)
        {
            $objects[] = array(
                'name' => $d['name'],
                'path' => $d['path'],
                'size' => null,
                'modified' => null,
                'is_dir' => false,
                'root' => 'twilio'
            );
        }

        return $objects;
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
        $path = $params['path'] ?? '';

        if (!$this->isOk())
            return false;

        // TODO: only read the buffer amount
        // TODO: limit the request rate

        $page = null;
        while (true)
        {
            $rows = $this->fetchData($path, $page);
            if ($rows === false)
                break;
            if (count($rows) === 0)
                break;

            foreach ($rows as $r)
            {
                $callback($r);
            }

            usleep($this->request_throttle*1000);
        }

        return true;
    }

    public function write(array $params, callable $callback)
    {
        $path = $params['path'] ?? '';
        $content_type = $params['content_type'] ?? \Flexio\Base\ContentType::MIME_TYPE_STREAM;

        // TODO: implement
    }


    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    public function describeTable(string $path)
    {
        if (!$this->isOk())
            return false;

        $path = self::cleanPath($path);

        // load the definition for the given path
        $definition = $this->lookupDefinition($path);
        if ($definition === false)
            return false;

        $structure = $definition['output'];
        return $structure;
    }

    private function fetchData(string $path, int &$page = null)
    {
        $path = self::cleanPath($path);
        if (!isset($page))
            $page = 1;

        // load the definition for the given path
        $definition = $this->lookupDefinition($path);
        if ($definition === false)
            return false;

        $location = $definition['location'];
        $content_root = $definition['content_root'];

        $apikey = $this->apikey;
        $apitoken = $this->apitoken;
        $apiauth = "$apikey:$apitoken";
        $request = str_replace('{key}', $apikey, $location);
        $request .= "&Page=$page";
        $request .= "?PageSize=$this->pagesize";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $apiauth);
        $result = curl_exec($ch);
        curl_close($ch);

        if ($result === false)
            return false;

        // return rows with uniform keys (flatten will flatten but not guarantee each row has
        // the same keys)
        $structure = $this->describeTable($path);
        $rows = $this->map($content_root, $result, $structure);
        $page = $page+1;

        return $rows;
    }

    private function map($content_root, $apidata, $structure) // TODO: add function parameters/return types
    {
        $result = array();

        $apidata = json_decode($apidata);
        $rows = self::getRowData($content_root, $apidata);
        if (!is_array($rows))
            return $result;

        $keys = array();
        foreach ($structure as $s)
        {
            $keys[] = $s['name'];
        }

        foreach ($rows as $r)
        {
            $r = \Flexio\Base\Mapper::flatten($r, null, '_');
            $r = $r[0];

            $output_row = array();
            foreach ($keys as $k)
            {
                $output_row[$k] = null;
                if (isset($r[$k]))
                    $output_row[$k] = $r[$k];
            }

            $result[] = $output_row;
        }

        return $result;
    }

    private function getRowData($content_root, $apidata) // TODO: add function parameters/return types
    {
        // set the current path to the root apidata object; find out the
        // path to the data node
        $currentpath = $apidata;
        $datapath = explode('.', $content_root);

        foreach ($datapath as $node)
        {
            // skip the root node
            if ($node === '$')
                continue;

            // if we can't find the current path, return an empty set of rows
            if (!isset($currentpath->$node))
                return array();

            // update the current path
            $currentpath = $currentpath->$node;
        }

        // we found the specified path
        return $currentpath;
    }

    private function initialize(string $apikey, string $apitoken)
    {
        // TODO: test api key

        $this->close();
        $this->apikey = $apikey;
        $this->apitoken = $apitoken;
        $this->is_ok = true;
    }

    private function lookupDefinition(string $path)
    {
        $definitions = $this->getDefinitions();
        foreach ($definitions as $d)
        {
            if ($d['path'] === $path)
                return $d;
        }

        return false;
    }

    private function getDefinitions() : array
    {
        // note: "content_root" is the the json path where the data in the return
        // result is located; so "$.calls" means the data is stored in the "entries"
        // node in the root object

        $definitions = array();

        $definitions[] = '
        {
            "path": "calls",
            "name": "Calls",
            "location" : "https://api.twilio.com/2010-04-01/Accounts/{key}/Calls.json",
            "content_root": "$.calls",
            "output" : [
                { "name" : "sid",              "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "date_created",     "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "date_updated",     "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "parent_call_sid",  "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "account_sid",      "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "to_num",           "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "to_formatted",     "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "from_num",         "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "from_formatted",   "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "phone_number_sid", "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "status",           "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "start_time",       "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "end_time",         "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "duration",         "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "price",            "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "price_unit",       "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "direction",        "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "answered_by",      "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "annotation",       "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "api_version",      "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "forwarded_from",   "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "group_sid",        "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "caller_name",      "type" : "character", "width" : 50, "scale" : 0 }
            ],
            "input" : {
                "calls" : [
                    {
                        "sid": null,
                        "date_created": null,
                        "date_updated": null,
                        "parent_call_sid": null,
                        "account_sid": null,
                        "to_num=to": null,
                        "to_formatted": null,
                        "from_num=from": null,
                        "from_formatted": null,
                        "phone_number_sid": null,
                        "status": null,
                        "start_time": null,
                        "end_time": null,
                        "duration": null,
                        "price": null,
                        "price_unit": null,
                        "direction": null,
                        "answered_by": null,
                        "annotation": null,
                        "api_version": null,
                        "forwarded_from": null,
                        "group_sid": null,
                        "caller_name": null
                    }
                ]
            }
        }
        ';

        $definitions[] = '
        {
            "path": "messages",
            "name": "Messages",
            "location" : "https://api.twilio.com/2010-04-01/Accounts/{key}/Messages.json",
            "content_root": "$.messages",
            "output" : [
                { "name" : "sid",           "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "date_created",  "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "date_updated",  "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "date_sent",     "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "to_num",        "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "from_num",      "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "body",          "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "num_segments",  "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "num_media",     "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "direction",     "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "error_code",    "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "error_message", "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "price",         "type" : "character", "width" : 50, "scale" : 0 },
                { "name" : "status",        "type" : "character", "width" : 50, "scale" : 0 }

            ],
            "input" : {
                "messages" : [
                    {
                       "body": null,
                       "num_segments": null,
                       "num_media": null,
                       "date_created": null,
                       "date_sent": null,
                       "date_updated": null,
                       "direction": null,
                       "error_code": null,
                       "error_message": null,
                       "from_num=from": null,
                       "price": null,
                       "sid": null,
                       "status": null,
                       "to_num=to": null
                    }
                ]
            }
        }
        ';

        $result = array();
        foreach ($definitions as $d)
        {
            $result[] = json_decode($d,true);
        }

        return $result;
    }

    private static function cleanPath($path)
    {
        $path = trim(strtolower($path));
        return $path;
    }
}
