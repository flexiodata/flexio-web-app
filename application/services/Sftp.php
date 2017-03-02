<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-10-28
 *
 * @package flexio
 * @subpackage Services
 */


namespace Flexio\Services;


if (!isset($GLOBALS['phpseclib_included']))
{
    $GLOBALS['phpseclib_included'] = true;
    set_include_path(get_include_path() . PATH_SEPARATOR . (dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'phpseclib'));
}


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Abstract.php';

class Sftp implements \Flexio\Services\IConnection
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
        if (($params = \Flexio\System\Validator::getInstance()->check($params, array(
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
            $this->connection->disconnect();

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

        // TODO: handle subdirectories
        $files = $this->connection->rawlist($base_path);
        if (!is_array($files))
            return array();

        $result = array();
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
                'is_dir' => ($info['type'] === NET_SFTP_TYPE_DIRECTORY),
                'root' => '/sftp'
            );

            $result[] = $entry;
        }

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

        $this->connection->getWithCallback($path, function($type, $data) use (&$callback) {
            if ($type == 'data')
            {
                $length = strlen($data);
                $callback($data);
                return $length;
            }
        });
    }

    public function write($path, $callback)
    {
        if (!$this->isOk())
        {
            // try to reconnect
            $this->connect($this->config);
            if (!$this->isOk())
                return;
        }

        $this->connection->put($path, function($length) use (&$callback) {
            $res = $callback($length);
            if ($res === false) return null; // SFTP.php expects null upon eof
            return $res;
        }, \phpseclib\Net\SFTP::SOURCE_CALLBACK);
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

        $sftp = new \phpseclib\Net\SFTP($host);
        if (!$sftp->login($username, $password))
            return false;

        $this->connection = $sftp;
        $this->is_ok = true;
    }
}
