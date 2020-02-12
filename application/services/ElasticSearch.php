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


class ElasticSearch implements \Flexio\IFace\IConnection,
                               \Flexio\IFace\IFileSystem
{
    // connection info
    private $host = '';
    private $port = '';
    private $username = '';
    private $password = '';

    // state info
    private $authenticated = false;

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
        $service->initialize($host, $port, $username, $password);

        return $service;
    }

    ////////////////////////////////////////////////////////////
    // IConnection interface
    ////////////////////////////////////////////////////////////

    public function connect() : bool
    {
        $host = $this->host;
        $port = $this->port;
        $username = $this->username;
        $password = $this->password;

        if ($this->initialize($host, $port, $username, $password) === false)
            return false;

        return true;
    }

    public function disconnect() : void
    {
        // reset secret credentials and authentication flag
        $this->password = '';
        $this->authenticated = false;
    }

    public function authenticated() : bool
    {
        return $this->authenticated;
    }

    public function get() : array
    {
        $properties = array(
            'host'     => $this->host,
            'port'     => $this->port,
            'username' => $this->username,
            'password' => $this->password
        );

        return $properties;
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
        return $this->listIndexes();
    }

    public function getFileInfo(string $path) : array
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function exists(string $path) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
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
        $index = self::convertToValid($path);
        $this->deleteIndex($index);
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
    }

    public function write(array $params, callable $callback) // TODO: add return type
    {
        if (!isset($params['path']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

        // get the index name (path)
        $index = $params['path'];
        $index = self::convertToValid($index);

        // output the rows
        $buffer_size = 1000; // max rows to write at a time
        $rows_to_write = array();

        while (true)
        {
            $row = $callback();
            if ($row === false)
                break;

            $rows_to_write[] = $row;

            if (count($rows_to_write) <= $buffer_size)
                continue;

            $this->writeRows($index, $rows_to_write);
        }

        // write out whatever is left over
        $this->writeRows($index, $rows_to_write);
    }

    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    public function listIndexes() : array
    {
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
                                   'hash' => '', // TODO: available?
                                   'type' => 'FILE');
            }
        }

        return $indexes;
    }

    public function deleteIndex(string $index) : void
    {
        try
        {
            // write the content
            $url = $this->getHostUrlString() . '/' . urlencode($index) . '?ignore_unavailable=true'; // don't throw an exception if index doesn't exist
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
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED);
            if (isset($result['error']) && $result['error'] === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
            if (isset($result['errors']) && $result['errors'] === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED);
        }
    }

    public function createIndex(string $index, array $structure = null) : void
    {
        // create an index with the specified mapping

        // note: elasticsearch indexes can store different types of documents,
        // which are called a type; so separate indexes can be created for
        // individual document types, one index with multiple types, or some
        // combination; which configuration is used depends on factors such
        // as number of indexes and types; search can span multiple indexes
        // and types within an index; the type simply allows a handle to filter
        // a particular document type within an index if one index is used
        // with multiple document types; each type has a mapping (like a
        // structure) associated with the type that determines how the information
        // within the document type is indexed (e.g. string vs. numeric vs.
        // date); in our particular usage, we're working with small volumes of
        // data (<100k rows) where we're rebuilding the index each time we
        // load data into it and where we have one type of data within an index;
        // for our purposes, then we only need to expose the index name and
        // structure and create a placeholder document type to associate with
        // the single type of data being inserted

        // creating an index with a mapping has the following form:
        /*
        PUT <index>
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

        // see here for info indexes, types, and mappings:
        // https://www.elastic.co/blog/found-elasticsearch-mapping-introduction

        // get a default document type name and create a mapping from the structure
        // to associate with the rows of data being inserted into this index
        $doc_type = self::getDefaultDocTypeName();
        $index_mapping_info = array();
        if (isset($structure))
        {
            $index_mapping_info = array('mappings' => array($doc_type => array('properties' => null)));
            $doc_type_properties = array();

            foreach ($structure as $field)
            {
                $fieldname = $field['name'];
                $fieldtype = $field['type'];
                $doc_type_properties[$fieldname] = self::getMappingInfoFromStructureType($fieldtype);
            }

            $index_mapping_info['mappings'][$doc_type]['_all'] = array('enabled' => false);
            $index_mapping_info['mappings'][$doc_type]['properties'] = $doc_type_properties;
        }
        $index_mapping_info_string = json_encode($index_mapping_info);

        try
        {
            // create the index with the specified mapping
            $url = $this->getHostUrlString() . '/' . urlencode($index);
            $auth = $this->getBasicAuthString();
            $content_type = 'application/json';

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            //curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic '. $auth, 'Content-Type: '. $content_type ]); // disable authorization header for public test
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: '. $content_type ]);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            //curl_setopt($ch, CURLOPT_POSTFIELDS, $index_mapping_info_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            $result = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($result,true);

            if (!is_array($result))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);
            if (isset($result['error']) && $result['error'] === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
            if (isset($result['errors']) && $result['errors'] === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);
        }
    }

    public function writeRows(string $index, array $rows) : void
    {
        try
        {
            // create the post buffer for the bulk api endpoint
            $doc_type = self::getDefaultDocTypeName();
            $index_write_string = '';
            foreach ($rows as $r)
            {
                $index_write_string .= '{"index": {"_index": "' . $index . '", "_type": "' . $doc_type . '"}}';
                $index_write_string .= "\n";
                $index_write_string .= json_encode($r, 0); // encode json without returns (each row must be on one line)
                $index_write_string .= "\n";
            }
            $index_write_string .= "\n"; // payload must end with newline

            // write the content
            $url = $this->getHostUrlString() . '/_bulk?refresh=true'; // TODO: temporarily refresh immediately
            $auth = $this->getBasicAuthString();
            $content_type = 'application/x-ndjson'; // use ndjson for bulk operations

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            //curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic '. $auth, 'Content-Type: '. $content_type ]); // disable authorization header for public test
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: '. $content_type ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $index_write_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            $result = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($result,true);

            if (!is_array($result))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
            if (isset($result['error']) && $result['error'] === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
            if (isset($result['errors']) && $result['errors'] === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }
    }

    public function query(string $index, array $query) : array
    {
        try
        {
            $index_write_string = json_encode($query, JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT);

            // create the index with the specified mapping
            $url = $this->getHostUrlString() . '/' . urlencode($index) . '/_search?size=1000';
            $auth = $this->getBasicAuthString();
            $content_type = 'application/json';

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            //curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic '. $auth, 'Content-Type: '. $content_type ]); // disable authorization header for public test
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: '. $content_type ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $index_write_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

            $result = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($result,true);

            if (!is_array($result))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
            if (isset($result['error']) && $result['error'] === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
            if (isset($result['errors']) && $result['errors'] === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

            $rows = $result['hits']['hits'] ?? false;
            if (!is_array($rows))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

            $output = array();
            foreach ($rows as $r)
            {
                $output[] = $r['_source'];
            }

            return $output;
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }
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

    private static function getMappingInfoFromStructureType(string $structure_type) : array
    {
        switch ($structure_type)
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

    private static function getDefaultDocTypeName() : string
    {
        return 'row';
    }

    private static function convertToValid(string $name) : string
    {
        $name = ltrim($name,'/');
        return strtolower($name);
    }
}
