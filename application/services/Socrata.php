<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2015-11-09
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


class Socrata implements \Flexio\IFace\IConnection, \Flexio\IFace\IFileSystem
{
    private $host;
    private $port;
    private $base_url = null;
    private $authenticated = false;

    public static function create(array $params = null) : \Flexio\Services\Socrata
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'host' => array('type' => 'string', 'required' => true),
                'port' => array('type' => 'string', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_params = $validator->getParams();
        $host = $validated_params['host'];
        $port = intval($validated_params['port']);

        $service = new self;
        if ($service->initialize($host, $port) === false)
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
        // TODO: filter based on a specified path

        if (!$this->authenticated())
        {
            // try to reconnect
            $this->connect();
            if (!$this->authenticated())
                return array();
        }

        // check if the url is fully-qualified with a table name;
        // if so, just return the info for the one table
        $parts = explode('/', $this->base_url);
        $id = array_pop($parts);
        if (strlen($id) == 9 && $id[4] == '-')
        {
            $url_parts = parse_url($this->base_url);

            $info_url = $url_parts['scheme'] . '://' . $url_parts['host'];
            if (isset($url_parts['port']))
                $info_url .= (':' . $url_parts['port']);

            $info_url .= "/views/$id.json";

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $info_url);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $table_schema_json = curl_exec($ch);
            curl_close($ch);

            $table_schema = @json_decode($table_schema_json,true);
            if (!isset($table_schema['name']))
                return null;

            $description = $table_schema['name'];

            return array(
                array(
                    'name' => "$description",
                    'path' => "/$description",
                    'size' => null,
                    'id' => $id,
                    'url' => $this->base_url,
                    'type' => 'DIR'
                )
            );
        }


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->base_url . "/browse?limit=30");
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $html = curl_exec($ch);
        curl_close($ch);


        $items = [];

        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);
        $nlist = $xpath->query("//a[@itemprop='url']");

        foreach ($nlist as $n)
        {
            $items[] = array(
                'url' => $n->getAttribute('href'),
                'description' => $n->textContent
            );
        }


        $tables = [];
        foreach ($items as $n)
        {
            if (preg_match("/\\/(?<id>[a-z0-9]{4}[-][a-z0-9]{4})/", $n['url'], $info))
            {
                $id = $info['id'];
                $description = $n['description'];

                $tables[] = array(
                    'name' => "$description",
                    'path' => "/$description",
                    'size' => null,
                    'modified' => null,
                    'hash' => '', // TODO: available?
                    'id' => $id,
                    'url' => $n['url'],
                    'type' => 'FILE'
                );
            }
        }

        return $tables;
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
        $path = $params['path'] ?? '';

        // url qualified with table name? if so, find out the real base path
        $baseurl = $this->base_url;
        $parts = explode('/', $baseurl);
        $id = array_pop($parts);
        if (strlen($id) == 9 && $id[4] == '-')
        {
            $url_parts = parse_url($baseurl);

            $baseurl = $url_parts['scheme'] . '://' . $url_parts['host'];
            if (isset($url_parts['port']))
                $baseurl .= (':' . $url_parts['port']);
        }


        $buf = '';
        $first = true;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "$baseurl/resource/$id.json");
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $data) use (&$callback, &$structure, &$first, &$buf) {

            $buf .= $data;

            if ($first)
            {
                $buf = trim($buf);
                if ($buf[0] == '[')
                    $buf = substr($buf, 1);
            }


            $start = 0;
            $rows = [];
            while (true)
            {
                $comma_pos = \Flexio\Base\Util::json_strpos($buf, ',', $start);
                if ($comma_pos === false)
                {
                    $buf = substr($buf, $start);
                    break;
                }

                $chunk = substr($buf, $start, $comma_pos - $start);


                // insert the row
                $row = json_decode($chunk,true);

                if (!is_null($row))
                {
                    $inserter_row = self::convertRow($structure, $row);
                    $callback($inserter_row);
                }


                $start = $comma_pos+1;
            }

            return strlen($data);
        });

        curl_exec($ch);

        // last chunk
        if (substr($buf, -1) == ']')
        {
            $chunk = substr($buf, 0, strlen($buf)-1);

            // insert the row
            $row = @json_decode($chunk,true);
            if (!is_null($row))
            {
                $inserter_row = self::convertRow($structure, $row);
                $callback($inserter_row);
            }
        }

        curl_close($ch);
    }

    public function write(array $params, callable $callback) // TODO: add return type
    {
        $path = $params['path'] ?? '';
        $content_type = $params['content_type'] ?? \Flexio\Base\ContentType::STREAM;

        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    public function describeTable(string $path) : array
    {
        if (!$this->authenticated())
        {
            // try to reconnect
            $this->connect();
            if (!$this->authenticated())
                return array();
        }

        // url qualified with table name? if so, find out the real base path
        $baseurl = $this->base_url;
        $parts = explode('/', $baseurl);
        $id = array_pop($parts);
        if (strlen($id) == 9 && $id[4] == '-')
        {
            $url_parts = parse_url($baseurl);

            $baseurl = $url_parts['scheme'] . '://' . $url_parts['host'];
            if (isset($url_parts['port']))
                $baseurl .= (':' . $url_parts['port']);
        }

        // get table columns
        $table_schema = null;
        $table_schema_json = '';

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "$baseurl/views/$id.json");
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $table_schema_json = curl_exec($ch);
        curl_close($ch);

        $table_schema = @json_decode($table_schema_json,true);
        if (!isset($table_schema['columns']) || !is_array($table_schema['columns']))
            return array();

        // generate output structure
        $structure = \Flexio\Base\Structure::create();

        foreach ($table_schema['columns'] as $col)
        {
            $scale = null;

            switch ($col['dataTypeName'])
            {
                default:
                case 'text':          $type = 'character'; break;
                case 'money':         $type = 'numeric';   break;
                case 'number':        $type = 'numeric';   break;
                case 'checkbox':      $type = 'boolean';   break;
                case 'calendar_date': $type = 'date';      break;
            }

            $structure->push(array(
                'name' =>          $col['fieldName'],
                'type' =>          $type,
                'width' =>         $col['width'],
                'scale' =>         $scale
            ));
        }

        return $structure->enum();
    }

    private static function convertRow(array &$structure, array $row) : array
    {
        $res = [];
        foreach ($structure as $col)
        {
            if (array_key_exists($col['name'], $row) && !is_array($row[$col['name']]))
                $res[] = $row[$col['name']];
                 else
                $res[] = null;
        }
        return $res;
    }

    private function connect() : bool
    {
        $host = $this->host;
        $port = $this->port;

        if ($this->initialize($host, $port) === false)
            return false;

        return true;
    }

    private function initialize(string $host, int $port) : bool
    {
        $this->host = $host;
        $this->port = $port;

        $url = $host;
        if (false === strpos($url, "://"))
        {
            if ($port == 80)
                $url = "http://$url";
                 else
                $url = "https://$url";
        }

        if (substr($url, -1) == '/')
            $url = substr($url, 0, strlen($url)-1);

        $this->base_url = $url;
        $this->authenticated = true;
        return true;
    }
}
