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


declare(strict_types=1);
namespace Flexio\Services;



spl_autoload_register(function ($class) {
    $class = ltrim($class, '\\');
    if (strpos($class, 'phpseclib\\') === 0)
    {
        $class = str_replace('\\', '/', $class);
        $class = dirname(dirname(__DIR__)) . '/library/phpseclib/' . $class . '.php';
        if (file_exists($class))
        {
            require_once $class;
            return true;
        }
        return false;
    }
});


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

    public static function create(array $params = null) : \Flexio\Services\Sftp
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
        if (($validator->check($params, array(
                'host' => array('type' => 'string', 'required' => true),
                'username' => array('type' => 'string', 'required' => true),
                'password' => array('type' => 'string', 'required' => true)
            ))->hasErrors()) === true)
            return false;

        $validated_params = $validator->getParams();
        $this->initialize($validated_params['host'], $validated_params['username'], $validated_params['password']);
        return $this->isOk();
    }

    public function isOk() : bool
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

    public function listObjects(string $path = '') : array
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
                'size' => $info['size'] ?? '',
                'modified' => '',
                'is_dir' => ($info['type'] === NET_SFTP_TYPE_DIRECTORY),
                'root' => '/sftp'
            );

            $result[] = $entry;
        }

        return $result;
    }

    public function exists(string $path) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return false;
    }

    public function read(array $params, callable $callback)
    {
        $path = $params['path'] ?? '';

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

    public function write(array $params, callable $callback)
    {
        $path = $params['path'] ?? '';
        $content_type = $params['content_type'] ?? \Flexio\Base\ContentType::MIME_TYPE_STREAM;

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

    private function initialize(string $host, string $username, string $password)
    {
        $this->close();

        $this->config = array('host' => $host,
                              'username' => $username,
                              'password' => $password);

        $sftp = new \phpseclib\Net\SFTP($host);
        if (!$sftp->login($username, $password))
            return;

        $this->connection = $sftp;
        $this->is_ok = true;
    }
}
