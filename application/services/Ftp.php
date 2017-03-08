<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-03-26
 *
 * @package flexio
 * @subpackage Services
 */


namespace Flexio\Services;


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Abstract.php';

class Ftp implements \Flexio\Services\IConnection
{
    ////////////////////////////////////////////////////////////
    // member variables
    ////////////////////////////////////////////////////////////

    private $config = array();
    private $connection = false;
    private $is_ok = false;


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
        $this->close();
        if (($params = \Flexio\Base\Validator::getInstance()->check($params, array(
                'host' => array('type' => 'string', 'required' => true),
                'username' => array('type' => 'string', 'required' => true),
                'password' => array('type' => 'string', 'required' => true)
            ))) === false)
            return false;

        $this->initialize($params['host'], $params['username'], $params['password']);
        return $this->isOk();
    }

    public function isOk()
    {
        return $this->is_ok;
    }

    public function close()
    {
        if ($this->connection !== false)
            ftp_close($this->connection);

        $this->connection = false;
        $this->is_ok = false;
    }

    public function listObjects($path = '')
    {
        if (!$this->isOk())
        {
            // try to reconnect
            $this->connect($this->config);
            if (!$this->isOk())
                return array();
        }

        $base_path = $path;
        if ($base_path == '')
            $base_path = '/';

        $files = ftp_rawlist($this->connection, $path);
        if (!is_array($files))
            return array();

        $result = array();

        // TODO: $files is a non-keyed array and needs to be parsed
        // to split into appropriate info
/*
        foreach ($files as $file => $info)
        {
            if ($file == '.' || $file == '..')
                continue;

            $full_path = $base_path;
            if (substr($full_path, -1) != '/')
                $full_path .= '/';
            $full_path .= $file;

            $entry = array(
                'name' => $file,
                'path' => $full_path,
                'size' => isset_or($info['size'],''),
                'modified' => '',
                'is_dir' => '',
                'root' => '/ftp'
            );

            $result[] = $entry;
        }
*/
        return $result;
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
        if (!$this->isOk())
        {
            // try to reconnect
            $this->connect($this->config);
            if (!$this->isOk())
                return;
        }

        // TODO: FTP write implementation
    }

    public function write($params, $callback)
    {
        $path = isset_or($params['path'],'');
        $content_type = isset_or($params['content_type'], \Flexio\System\ContentType::MIME_TYPE_STREAM);

        if (!$this->isOk())
        {
            // try to reconnect
            $this->connect($this->config);
            if (!$this->isOk())
                return;
        }

        // TODO: FTP read implementation
    }


    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    private function initialize($host, $username, $password)
    {
        $this->close();

        $this->config = array('host' => $host,
                              'username' => $username,
                              'password' => $password);

        $ftp = ftp_connect($host);
        if ($ftp === false)
            return;
        if (ftp_login($ftp, $username, $password) === false)
            return;

        $this->connection = $ftp;
        $this->is_ok = true;
    }
}
