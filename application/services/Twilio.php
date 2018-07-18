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


class Twilio implements \Flexio\IFace\IConnection, \Flexio\IFace\IFileSystem
{
    private $authenticated = false;
    private $username = '';
    private $password = '';
    private $pagesize = 200; // rows to request per request; 200 is maximum allowed per request
    private $request_throttle = 250; // milliseconds to wait between requests; pipeline deals allows up to 5 requests per second

    public static function create(array $params = null) : \Flexio\Services\Twilio
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'username' => array('type' => 'string', 'required' => true),
                'password' => array('type' => 'string', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_params = $validator->getParams();
        $username = $validated_params['username'];
        $password = $validated_params['password'];

        $service = new self;
        if ($service->initialize($username, $password) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        return $service;
    }

    public function authenticated() : bool
    {
        return $this->authenticated;
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

        $objects = array();

        $definitions = $this->getDefinitions();
        foreach ($definitions as $d)
        {
            $objects[] = array(
                'name' => $d['name'],
                'path' => '/' . $d['path'],
                'size' => null,
                'modified' => null,
                'type' => 'FILE'
            );
        }

        return $objects;
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

    public function createDirectory(string $path, array $properties = []) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
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

    public function read(array $params, callable $callback) // TODO: add return type
    {
        if (!$this->authenticated())
            return false;

        $path = $params['path'] ?? '';
        $path = self::cleanPath($path);

        // TODO: only read the buffer amount
        // TODO: limit the request rate

        $page = null;
        while (true)
        {
            // TODO: just return some of the data now
            $rows = $this->fetchData($path, $page);
            $callback(json_encode($rows));
            break;
/*
            if ($rows === false)
                break;
            if (count($rows) === 0)
                break;

            foreach ($rows as $r)
            {
                $callback($r);
            }

            usleep($this->request_throttle*1000);
*/
        }

        return true;
    }

    public function write(array $params, callable $callback) // TODO: add return type
    {
        $path = $params['path'] ?? '';
        $content_type = $params['content_type'] ?? \Flexio\Base\ContentType::STREAM;

        // TODO: implement
    }

    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    public function describeTable(string $path) // TODO: add return type
    {
        if (!$this->authenticated())
            return false;

        $path = self::cleanPath($path);

        // load the definition for the given path
        $definition = $this->lookupDefinition($path);
        if ($definition === false)
            return false;

        $structure = $definition['output'];
        return $structure;
    }

    private function fetchData(string $path, int &$page = null) // TODO: add return type
    {
        if (!isset($page))
            $page = 1;

        // load the definition for the given path
        $definition = $this->lookupDefinition($path);
        if ($definition === false)
            return false;

        $location = $definition['location'];
        $content_root = $definition['content_root'];

        $username = $this->username;
        $password = $this->password;
        $apiauth = "$username:$password";
        $request = str_replace('{username}', $username, $location);
        //$request .= "?PageSize=$this->pagesize";
        //$request .= "&Page=$page";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
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

    private function map($content_root, $apidata, $structure) // TODO: add return type; TODO: add parameter type
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

    private function getRowData($content_root, $apidata) // TODO: add return type; TODO: add parameter type
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

    private function connect() : bool
    {
        $username = $this->username;
        $password = $this->password;

        if ($this->initialize($username, $password) === false)
            return false;

        return false;
    }

    private function initialize(string $username, string $password) : bool
    {
        // TODO: test api key

        $this->username = $username;
        $this->password = $password;
        $this->authenticated = true;
        return true;
    }

    private function lookupDefinition(string $path) // TODO: add return type
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
            "name": "calls",
            "location" : "https://api.twilio.com/2010-04-01/Accounts/{username}/Calls.json",
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
            "name": "messages",
            "location" : "https://api.twilio.com/2010-04-01/Accounts/{username}/Messages.json",
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

    private static function cleanPath(string $path) : string
    {
        $path = trim(strtolower($path));
        $path = trim($path, '/');
        return $path;
    }
}
