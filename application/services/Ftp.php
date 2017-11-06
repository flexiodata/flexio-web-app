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


declare(strict_types=1);
namespace Flexio\Services;


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Abstract.php';

class Ftp implements \Flexio\Services\IConnection, \Flexio\Services\IFileSystem
{
    ////////////////////////////////////////////////////////////
    // member variables
    ////////////////////////////////////////////////////////////

    private $host;
    private $username;
    private $password;
    private $connection = false;
    private $is_ok = false;

    ////////////////////////////////////////////////////////////
    // IConnection interface
    ////////////////////////////////////////////////////////////

    public static function create(array $params = null) : \Flexio\Services\Ftp
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
        if ($service->initialize($host, $username, $password) == false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        return $service;
    }

    ////////////////////////////////////////////////////////////
    // IFileSystem interface
    ////////////////////////////////////////////////////////////

    public function list(string $path = '') : array
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);

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
                'size' => $info['size'] ?? '',
                'modified' => '',
                'is_dir' => '',
                'root' => '/ftp'
            );

            $result[] = $entry;
        }
*/
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
            $this->connect();
            if (!$this->isOk())
                return;
        }

        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function write(array $params, callable $callback)
    {
        $path = $params['path'] ?? '';
        $content_type = $params['content_type'] ?? \Flexio\Base\ContentType::MIME_TYPE_STREAM;

        if (!$this->isOk())
        {
            // try to reconnect
            $this->connect();
            if (!$this->isOk())
                return;
        }

        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
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
            ftp_close($this->connection);

        $this->connection = false;
        $this->is_ok = false;

        $ftp = ftp_connect($host);
        if ($ftp === false)
            return false;
        if (ftp_login($ftp, $username, $password) === false)
            return false;

        $this->connection = $ftp;
        $this->is_ok = true;
        return true;
    }

    private function isOk() : bool
    {
        return $this->is_ok;
    }
}
