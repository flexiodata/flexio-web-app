<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-06-23
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


class ElasticSearch implements \Flexio\IFace\IConnection, \Flexio\IFace\IFileSystem
{
    private $authenticated = false;
    private $host = '';
    private $port = '';
    private $user = '';
    private $password = '';

    public static function create(array $params = null) : \Flexio\Services\ElasticSearch
    {

        if (isset($params['port']))
        $params['port'] = (string)$params['port'];

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'host'     => array('type' => 'string', 'required' => true),
                'port'     => array('type' => 'string', 'required' => true),
                'username' => array('type' => 'string', 'required' => true),
                'password' => array('type' => 'string', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_params = $validator->getParams();
        $host = $validated_params['host'];
        $port = intval($validated_params['port']);
        $username = $validated_params['username'];
        $password = $validated_params['password'];

        $service = new self;
        if ($service->initialize($host, $port, $username, $password) === false)
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
        // note: right now, the list is flat; path isn't used

        if (!$this->authenticated())
            return array();

        // get the indexes
        $url = $this->getHostUrlString() . '/_stats';
        $auth = $this->getBasicAuthString();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic '. $auth]); // disable authorization header for public test
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        $result = curl_exec($ch);
        $result = json_decode($result, true);

        $indexes = [];

        if (isset($result['indices']))
        {
            $indices = $result['indices'];
            foreach ($indices as $index_name => $index_info)
            {
                // only show indices that aren't hidden
                //if (substr($index_name, 0, 1) === '.')
                //    continue;

                // TODO: include other information from the stats
                $indexes[] = array('name' => $index_name,
                                   'path' => '/' . $index_name,
                                   'size' => null,
                                   'modified' => null,
                                   'type' => 'FILE');
            }
        }

        return $indexes;
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
        $params = array('path' => $path);
        $this->deleteIndex($params);
        return true;
    }

    public function open($path) : \Flexio\IFace\IStream
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function read(array $params, callable $callback) // TODO: add return type
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return false;
    }

    public function write(array $params, callable $callback) // TODO: add return type
    {
        // in elasticsearch, index endpoints follow form:  <host>:port/index/type
        // TODO: for now, set default type to 'rows'; should be based on path somehow

        // TODO: for now, only allow output to tables
        //$content_type = $params['content_type'] ?? \Flexio\Base\ContentType::STREAM;
        //if ($content_type !== \Flexio\Base\ContentType::FLEXIO_TABLE)
        //    return false;

        // delete the index if it's there
        $this->deleteIndex($params);

        // create the index
        if ($this->createIndex($params) === false)
            return false;

        // make sure the index and type are valid
        $index = $params['path'] ?? '';
        $type = self::getDefaultTypeName();

        $index = self::convertToValid($index);
        $type = self::convertToValid($type);

        // output the rows
        $buffer_size = 1000; // max rows to write at a time
        $rows_to_write = array();

        while (true)
        {
            $row = $callback($buffer_size);
            if ($row === false)
                break;

            $rows_to_write[] = $row;

            if (count($rows_to_write) <= $buffer_size)
                continue;

            $result = $this->writeRows($index, $type, $rows_to_write);
            if ($result === false)
                return false;  // error occurred; TODO: throw exception?

            $rows_to_write = array();
        }

        // write out whatever's left over
        $result = $this->writeRows($index, $type, $rows_to_write);
        if ($result === false)
            return false;  // error occurred; TODO: throw exception?

        $rows_to_write = array();
        return true;
    }

    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    public function deleteIndex(array $params) : bool
    {
        $index = $params['path'] ?? '';
        $index = self::convertToValid($index);

        try
        {
            // write the content
            $url = $this->getHostUrlString() . '/' . urlencode($index);
            $auth = $this->getBasicAuthString();
            $content_type = 'application/json';

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            //curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic '. $auth, 'Content-Type: '. $content_type ]); // disable authorization header for public test
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: '. $content_type ]);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            $result = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($result,true);

            if (!is_array($result))
                return false;
            if (isset($result['errors']) && $result['errors'] !== false)
                return false;

            return true;
        }
        catch (\Exception $e)
        {
            return false;
        }
    }

    public function createIndex(array $params) : bool
    {
        // create an index with the specified mapping

        // TODO: for now, only allow output to tables
        //$content_type = $params['content_type'] ?? \Flexio\Base\ContentType::STREAM;
        //if ($content_type !== \Flexio\Base\ContentType::FLEXIO_TABLE)
        //    return false;

        // make sure the index and type are valid
        $index = $params['path'] ?? '';
        $type = self::getDefaultTypeName();

        $index = self::convertToValid($index);
        $type = self::convertToValid($type);

        // get the structure
        $structure = $params['structure'] ?? false;
        if (!is_array($structure))
            return false;

        // build the api json payload; payload has the following form
        /*
        PUT <table>
        {
            "mappings": {
                "<type>": {
                    "properties": {
                        "<field1>": {
                            "type": text|integer|double|date|boolean
                            <extra info>
                        },
                        "<field2>": {
                            "type": text|integer|double|date|boolean
                            <extra info>
                        }
                    }
                }
            }
        }
        */

        $type_info = array('mappings' => array($type => array('properties' => null)));
        $type_property_list = array();

        foreach ($structure as $field)
        {
            $fieldname = $field['name'];
            $fieldtype = $field['type'];
            $type_property_list[$fieldname] = self::getIndexTypeInfo($fieldtype);
        }

        $type_info['mappings'][$type]['_all'] = array('enabled' => false);
        $type_info['mappings'][$type]['properties'] = $type_property_list;
        $buf = json_encode($type_info);

        // set the index info
        try
        {
            // write the content
            $url = $this->getHostUrlString() . '/' . urlencode($index);
            $auth = $this->getBasicAuthString();
            $content_type = 'application/json';

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            //curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic '. $auth, 'Content-Type: '. $content_type ]); // disable authorization header for public test
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: '. $content_type ]);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $buf);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            $result = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($result,true);

            if (!is_array($result))
                return false;
            if (isset($result['errors']) && $result['errors'] !== false)
                return false;

            return true;
        }
        catch (\Exception $e)
        {
            return false;
        }
    }

    private static function getIndexTypeInfo(string $type) : array
    {
        switch ($type)
        {
            default:
            case \Flexio\Base\Structure::TYPE_TEXT:
            case \Flexio\Base\Structure::TYPE_CHARACTER:
            case \Flexio\Base\Structure::TYPE_WIDECHARACTER:
                $info = array(
                    'type' => 'text',
                    'index' => true,
                    'store' => false
                );
                return $info;

            case \Flexio\Base\Structure::TYPE_NUMERIC:
            case \Flexio\Base\Structure::TYPE_DOUBLE:
                $info = array(
                    'type' => 'double',
                    'coerce' => true,
                    'doc_values' => true,
                    'ignore_malformed' => false,
                    'index' => true,
                    'store' => false
                );
                return $info;

            case \Flexio\Base\Structure::TYPE_INTEGER:
                $info = array(
                    'type' => 'integer',
                    'coerce' => true,
                    'doc_values' => true,
                    'ignore_malformed' => false,
                    'index' => true,
                    'store' => false
                );
                return $info;

            case \Flexio\Base\Structure::TYPE_DATE:
            case \Flexio\Base\Structure::TYPE_DATETIME:
                $info = array(
                    'type' => 'date',
                    'doc_values' => true,
                    'format' => 'strict_date_optional_time||epoch_millis',
                    'ignore_malformed' => false,
                    'index' => true,
                    'store' => false
                );
                return $info;

            case \Flexio\Base\Structure::TYPE_BOOLEAN:
                $info = array(
                    'type' => 'boolean',
                    'doc_values' => true,
                    'index' => true,
                    'store' => false
                );
                return $info;
        }
    }

    private function writeRows(string $index, string $type, array $rows) : bool
    {
        try
        {
            // create the post buffer for the bulk api endpoint
            $buf = '';
            foreach ($rows as $r)
            {
                $buf .= '{"index": {"_index": "' . $index . '", "_type": "' . $type . '"}}';
                $buf .= "\n";
                $buf .= json_encode($r, 0); // encode json without returns (each row must be on one line)
                $buf .= "\n";
            }
            $buf .= "\n"; // payload must end with newline

            // write the content
            $url = $this->getHostUrlString() . '/_bulk';
            $auth = $this->getBasicAuthString();
            $content_type = 'application/x-ndjson'; // use ndjson for bulk operations

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            //curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic '. $auth, 'Content-Type: '. $content_type ]); // disable authorization header for public test
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: '. $content_type ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $buf);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            $result = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($result,true);

            if (!is_array($result))
                return false;
            if (isset($result['errors']) && $result['errors'] !== false)
                return false;

            return true;
        }
        catch (\Exception $e)
        {
            return false;
        }
    }

    private function connect() : bool
    {
        $host = $this->host;
        $port = $this->port;
        $user = $this->user;
        $password = $this->password;

        if ($this->initialize($host, $port, $username, $password) === false)
            return false;

        return true;
    }

    private function initialize(string $host, int $port, string $username, string $password) : bool
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->authenticated = false;

        if ($this->testConnection() === false)
            return false;

        $this->authenticated = true;
        return true;
    }

    private function testConnection() : bool
    {
        // test the connection
        try
        {
            $url = $this->getHostUrlString();
            $auth = $this->getBasicAuthString();

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            //curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic '. $auth]); // disable authorization header for public test
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            $result = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($result,true);

            if (!is_array($result))
                return false;
            if (!isset($result['version']))
                return false;

            $version = $result['version'];
            if (!isset($version['number']))
                return false;

            // TODO: do we want/need to require a minimum version?

            return true;
        }
        catch (\Exception $e)
        {
            return false;
        }
    }

    private function getHostUrlString() : string
    {
        return $this->host . ':' . (string)$this->port;
    }

    private function getBasicAuthString() : string
    {
        return base64_encode($this->username . ':' . $this->password);
    }

    private function convertToValid(string $name) : string
    {
        $name = ltrim($name,'/');
        return strtolower($name);
    }

    private function getDefaultTypeName() : string
    {
        return 'rows';
    }
}
