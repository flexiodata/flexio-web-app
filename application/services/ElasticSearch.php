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
    private const MAX_INDEX_RESULT_WINDOW   = 1000000;     // maximum number of items that will be returned from a search without pagination
    private const MAX_INDEX_ROW_WRITE_LIMIT = 1000000; // maximum number of rows that can be written in the write loop; primarily to avoid hanging loops

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
 
        // AWS elasticsearch services uses IAM for access control; this is a mode
        // which is either on or off.  When on, the requests must be signed with
        // the Signature V4 algorithm; when off, HTTP basic authentication is used
        // HTTP basic authentication is not supported on AWS
        $use_aws_iam = (($params['type'] ?? '') == 'elasticsearch-aws');

        $service = new self;
        $service->initialize($host, $port, $username, $password, $use_aws_iam);

        return $service;
    }

    ////////////////////////////////////////////////////////////
    // IConnection interface
    ////////////////////////////////////////////////////////////

    public function connect() : bool
    {
        $use_aws_iam = $this->use_aws_iam;
        $host = $this->host;
        $port = $this->port;
        $username = $this->username;
        $password = $this->password;

        if ($this->initialize($host, $port, $username, $password, $use_aws_iam) === false)
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

        $total_row_count = 0;
        while (true)
        {
            $row = $callback();
            if ($row === false)
                break;

            $rows_to_write[] = $row;
            $total_row_count++;

            if ($total_row_count >= self::MAX_INDEX_ROW_WRITE_LIMIT)
                break;

            if (count($rows_to_write) <= $buffer_size)
                continue;

            $this->writeRows($index, $rows_to_write);
            $rows_to_write = array();
        }

        // write out whatever is left over
        if (count($rows_to_write) > 0)
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

        /*
        $url = $this->getHostUrlString() . '/_stats';
        $auth = $this->getBasicAuthString();

        $headers = array();
        $headers[] = 'Authorization: Basic ' . $auth;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        */

        $url = $this->getHostUrlString() . '/_stats';
        $request = new \GuzzleHttp\Psr7\Request('GET', $url);
        $response = $this->sendWithCredentials($request);

        $httpcode = $response->getStatusCode();
        $result = $response->getBody();
        if ($httpcode < 200 || $httpcode > 299)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

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

            $query_params = array(
                'ignore_unavailable' => 'true' // don't throw an exception if index doesn't exist
            );
            $query_str = http_build_query($query_params);



            /*

            $url = $this->getHostUrlString() . '/' . urlencode($index) . '?' . $query_str;
            $auth = $this->getBasicAuthString();
            $content_type = 'application/json';

            $headers = array();
            $headers[] = 'Authorization: Basic ' . $auth;
            $headers[] = 'Content-Type: ' . $content_type;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            $result = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            */

            $url = $this->getHostUrlString() . '/' . urlencode($index) . '?' . $query_str;
            $request = new \GuzzleHttp\Psr7\Request('DELETE', $url);
            $response = $this->sendWithCredentials($request);

            $httpcode = $response->getStatusCode();
            $result = $response->getBody();
            if ($httpcode < 200 || $httpcode > 299)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED);

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
        // see here for more info about index creation and settings:
        // https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-create-index.html
        // https://www.elastic.co/guide/en/elasticsearch/reference/current/index-modules.html

        // for now, disable mapping; rely instead on default mapping
        // that elasticsearch creates from json
        $mapping_enabled = false;

        try
        {
            // configure index
            $index_configuration = array();
            $index_configuration['settings'] = [
                'max_result_window' => self::MAX_INDEX_RESULT_WINDOW
            ];

            if ($mapping_enabled)
            {
                $index_mapping_info = self::getMappingFromStructure($structure);
                $index_configuration['mappings'] = $index_mapping_info['mappings'];
            }

            /*
            // create the index with the specified mapping
            $url = $this->getHostUrlString() . '/' . urlencode($index);
            $auth = $this->getBasicAuthString();
            $content_type = 'application/json';

            $headers = array();
            $headers[] = 'Authorization: Basic ' . $auth;
            $headers[] = 'Content-Type: ' . $content_type;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($index_configuration));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            $result = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            */

            $url = $this->getHostUrlString() . '/' . urlencode($index) . '?' . $query_str;
            $request = new \GuzzleHttp\Psr7\Request('PUT', $url, ['Content-Type' => 'application/json'], json_encode($index_configuration));
            $response = $this->sendWithCredentials($request);

            $httpcode = $response->getStatusCode();
            $result = $response->getBody();
            if ($httpcode < 200 || $httpcode > 299)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

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

    public function writeRows(string $index, array $rows, bool $refresh_immediately = false) : void
    {
        try
        {
            // create the post buffer for the bulk api endpoint
            $index_write_string = '';
            foreach ($rows as $r)
            {
                $index_write_string .= '{"index": {"_index": "' . $index . '"}}';
                $index_write_string .= "\n";
                $index_write_string .= json_encode($r, 0); // encode json without returns (each row must be on one line)
                $index_write_string .= "\n";
            }
            $index_write_string .= "\n"; // payload must end with newline

            // write the content
            $query_params = array(
                'refresh' => $refresh_immediately ? 'true' : 'false'
            );
            $query_str = http_build_query($query_params);

            /*
            $url = $this->getHostUrlString() . '/_bulk?' . $query_str;
            $auth = $this->getBasicAuthString();
            $content_type = 'application/x-ndjson'; // use ndjson for bulk operations

            $headers = array();
            $headers[] = 'Authorization: Basic ' . $auth;
            $headers[] = 'Content-Type: ' . $content_type;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $index_write_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            $result = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            */

            $url = $this->getHostUrlString() . '/_bulk?' . $query_str;
            $request = new \GuzzleHttp\Psr7\Request('POST', $url, ['Content-Type' => 'application/x-ndjson'], $index_write_string); // use ndjson for bulk operations
            $response = $this->sendWithCredentials($request);

            $httpcode = $response->getStatusCode();
            $result = $response->getBody();

            if ($httpcode < 200 || $httpcode > 299)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

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

    public function query(string $index, string $query) : array
    {
        try
        {
            $index_query_string = $query;

            $query_params = array(
                'size' => self::MAX_INDEX_RESULT_WINDOW
            );
            $query_str = http_build_query($query_params);

            /*
            $url = $this->getHostUrlString() . '/' . urlencode($index) . '/_search?' . $query_str;
            $auth = $this->getBasicAuthString();
            $content_type = 'application/json';

            $headers = array();
            $headers[] = 'Authorization: Basic ' . $auth;
            $headers[] = 'Content-Type: ' . $content_type;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $index_query_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            $result = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            */

            $url = $this->getHostUrlString() . '/' . urlencode($index) . '/_search?' . $query_str;
            $request = new \GuzzleHttp\Psr7\Request('POST', $url, ['Content-Type' => 'application/json'], $index_write_string);
            $response = $this->sendWithCredentials($request);

            $httpcode = $response->getStatusCode();
            $result = $response->getBody();
            if ($httpcode < 200 || $httpcode > 299)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

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

    public function info() : array
    {
        // TODO: parallels testConnection function; factor?
        try
        {
            /*
            $url = $this->getHostUrlString();
            $auth = $this->getBasicAuthString();

            $headers = array();
            $headers[] = 'Authorization: Basic ' . $auth;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            $result = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            */

            $url = $this->getHostUrlString();
            $request = new \GuzzleHttp\Psr7\Request('GET', $url);
            $response = $this->sendWithCredentials($request);

            $httpcode = $response->getStatusCode();
            $result = $response->getBody();
            if ($httpcode < 200 || $httpcode > 299)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_DATABASE);

            $result = json_decode($result,true);

            if (!is_array($result))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_DATABASE);
            if (!isset($result['version']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_DATABASE);

            $version = $result['version'];
            if (!isset($version['number']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_DATABASE);

            return $version;
        }
        catch (\Exception $e)
        {
            return array();
        }
    }

    private function initialize(string $host, int $port, string $username, string $password, bool $use_aws_iam = false) : bool
    {
        $this->use_aws_iam = $use_aws_iam;
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
    // TODO: parallels info() function; factor?

        // test the connection
        try
        {
            /*
            $url = $this->getHostUrlString();
            $auth = $this->getBasicAuthString();

            $headers = array();
            $headers[] = 'Authorization: Basic ' . $auth;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            $result = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            */

            $url = $this->getHostUrlString();
            $request = new \GuzzleHttp\Psr7\Request('GET', $url);
            $response = $this->sendWithCredentials($request);

            $httpcode = $response->getStatusCode();
            $result = $response->getBody();
            if ($httpcode < 200 || $httpcode > 299)
                return false;

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
        if (($this->use_aws_iam ?? false))
        {
            return 'https://' . $this->host;
        }

        return 'http://' . $this->host . ':' . (string)$this->port;
    }

    private function getBasicAuthString() : string
    {
        return base64_encode($this->username . ':' . $this->password);
    }

    private static function getMappingFromStructure(array $structure = null) : array
    {
        // see here for info indexes, types, and mappings:
        // https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping.html

        // example:
        /*
        PUT <index>
        {
            "mappings": {
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
        */

        if (!isset($structure))
            return array();

        // create a mapping from the structure
        $properties = array();
        foreach ($structure as $field)
        {
            $fieldname = $field['name'];
            $fieldtype = $field['type'];
            $properties[$fieldname] = self::getMappingInfoFromStructureType($fieldtype);
        }

        $index_mapping_info = array();
        //$index_mapping_info['mappings']['_all'] = array('enabled' => false);
        $index_mapping_info['mappings']['properties'] = $properties;

        return $index_mapping_info;
    }

    private static function getMappingInfoFromStructureType(string $structure_type) : array
    {
        switch ($structure_type)
        {
            default:
            case \Flexio\Base\Structure::TYPE_STRING:
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

    private static function convertToValid(string $name) : string
    {
        $name = ltrim($name,'/');
        return strtolower($name);
    }


    private function sendWithCredentials(\GuzzleHttp\Psr7\Request $request)
    {
        $client = new \GuzzleHttp\Client();
        $options = [];

        if (($this->use_aws_iam ?? false))
        {
            $credentials = new Aws\Credentials\Credentials($this->username, $this->password);
            $signer = new Aws\Signature\SignatureV4('es', 'us-east-1');
            $request = $signer->signRequest($request, $credentials);
        }
        else
        {
            $options = [ 'auth' => [ $this->username, $this->password ] ];
        }

        return $client->send($request, $options);
    }


}
