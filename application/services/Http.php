<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-10-29
 *
 * @package flexio
 * @subpackage Services
 */


namespace Flexio\Services;


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Abstract.php';

class Http implements \Flexio\Services\IConnection
{
    ////////////////////////////////////////////////////////////
    // member variables
    ////////////////////////////////////////////////////////////



    ////////////////////////////////////////////////////////////
    // IConnection interface
    ////////////////////////////////////////////////////////////

    public static function create($params = null)
    {
        $service = new self;

        if (isset($params))
            $service->connect($params);

        return $service;
    }

    public function connect($params)
    {
        return true;
    }

    public function isOk()
    {
        return true;
    }

    public function close()
    {
    }

    public function listObjects($path = '')
    {
        // TODO: show the files for a given URL?
        return array();
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
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Flex.io');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $data) use (&$callback) {
            $length = strlen($data);
            $callback($data);
            return $length;
        });
        $result = curl_exec($ch);
        curl_close($ch);
    }

    public function write($params, $callback)
    {
        $path = isset_or($params['path'],'');
        $content_type = isset_or($params['content_type'], \Flexio\Base\ContentType::MIME_TYPE_STREAM);

        // TODO: implement
    }

    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////


}
