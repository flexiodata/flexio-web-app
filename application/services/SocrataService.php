<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2015-11-09
 *
 * @package flexio
 * @subpackage Services
 */


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Abstract.php';


class SocrataService implements IConnection
{
    ////////////////////////////////////////////////////////////
    // member variables
    ////////////////////////////////////////////////////////////

    private $config = array();
    private $base_url = null;
    private $is_ok = false;


    ////////////////////////////////////////////////////////////
    // IConnection interface
    ////////////////////////////////////////////////////////////

    public static function create($params = null)
    {
        $service = new static();

        if (isset($params))
            $service->connect($params);

        return $service;
    }

    public function connect($params)
    {
        $validator = \Validator::getInstance();
        if (($params = $validator->check($params, array(
                'host' => array('type' => 'string', 'required' => true),
                'port' => array('type' => 'string', 'required' => true)
            ))) === false)
            return null;

        $this->initialize($params['host'], $params['port']);
        return $this->isOk();
    }

    public function isOk()
    {
        return $this->is_ok;
    }

    public function close()
    {
        $this->base_url = null;
        $this->is_ok = false;
    }

    public function listObjects($path = '')
    {
        // TODO: filter based on a specified path

        if (!$this->isOk())
        {
            // try to reconnect
            $this->connect($this->config);
            if (!$this->isOk())
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
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_SSLVERSION, 1);
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
                    'is_dir' => false
                )
            );
        }


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->base_url . "/browse?limit=30");
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $html = curl_exec($ch);
        curl_close($ch);


        $items = [];

        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($html);
        $xpath = new DOMXPath($doc);
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
                    'id' => $id,
                    'url' => $n['url'],
                    'is_dir' => false
                );
            }
        }

        return $tables;
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

    public function read($path, $callback)
    {
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
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);
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
                $comma_pos = \Util::json_strpos($buf, ',', $start);
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

    public function write($path, $callback)
    {
        // TODO: implement
    }

    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    public function describeTable($path)
    {
        if (!$this->isOk())
        {
            // try to reconnect
            $this->connect($this->config);
            if (!$this->isOk())
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
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $table_schema_json = curl_exec($ch);
        curl_close($ch);

        $table_schema = @json_decode($table_schema_json,true);
        if (!isset($table_schema['columns']) || !is_array($table_schema['columns']))
            return $this->fail(\Model::ERROR_INVALID_PARAMETER, _(''), __FILE__, __LINE__);

        // generate output structure
        $structure = \Flexio\Object\Structure::create();

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

    private static function convertRow(&$structure, $row)
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

    private function initialize($host, $port)
    {
        $this->close();

        $this->config = array('host' => $host,
                              'username' => $port);

        $url = $host;
        if (false === strpos($url, "://"))
        {
            if ((int)$port == 80)
                $url = "http://$url";
                 else
                $url = "https://$url";
        }

        if (substr($url, -1) == '/')
            $url = substr($url, 0, strlen($url)-1);

        $this->base_url = $url;
        $this->is_ok = true;
    }
}
