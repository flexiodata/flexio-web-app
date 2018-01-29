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


class Sftp implements \Flexio\IFace\IFileSystem
{
    private $host;
    private $username;
    private $password;
    private $connection = false;
    private $is_ok = false;

    public static function create(array $params = null) : \Flexio\Services\Sftp
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'host' => array('type' => 'string', 'required' => true),
                'username' => array('type' => 'string', 'required' => true),
                'password' => array('type' => 'string', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $host = $validated_params['host'];
        $username = $validated_params['username'];
        $password = $validated_params['password'];

        $service = new self;
        if ($service->initialize($host, $username, $password) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        return $service;
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
        if (!$this->isOk())
        {
            // try to reconnect
            $this->connect();
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
                'size' => $info['size'] ?? null,
                'modified' => '',
                'type' => ($info['type'] === NET_SFTP_TYPE_DIRECTORY) ? 'DIR' : 'FILE'
            );

            $result[] = $entry;
        }

        return $result;
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

    public function open($path) : \Flexio\IFace\IStream
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function read(array $params, callable $callback)
    {
        $path = $params['path'] ?? '';

        if (!$this->isOk())
        {
            // try to reconnect
            $this->connect();
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
        $content_type = $params['content_type'] ?? \Flexio\Base\ContentType::STREAM;

        if (!$this->isOk())
        {
            // try to reconnect
            $this->connect();
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

    private function connect() : bool
    {
        $host = $this->host;
        $username = $this->username;
        $password = $this->password;

        if ($this->initialize($host, $username, $password) === false)
            return false;

        return true;
    }

    private function initialize(string $host, string $username, string $password) : bool
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;

        if ($this->connection !== false)
            $this->connection->disconnect();

        $this->connection = false;
        $this->is_ok = false;

        if (strlen(trim($host)) == 0) {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CONNECTION_FAILED, "Missing host name");
        }

        $sftp = new \phpseclib\Net\SFTP($host);
        if (!@$sftp->login($username, $password)) {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CONNECTION_FAILED, "Could not connect to remote host " . $host);
        }

        $this->connection = $sftp;
        $this->is_ok = true;
        return true;
    }

    private function isOk() : bool
    {
        return $this->is_ok;
    }
}
