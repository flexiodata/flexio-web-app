<?php
/**
 *
 * Copyright (c) 2015, Flex Research LLC. All rights reserved.
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


class Ftp implements \Flexio\IFace\IConnection,
                     \Flexio\IFace\IFileSystem
{
    // connection info
    private $host;
    private $username;
    private $password;

    // additional state info
    private $authenticated = false;
    private $connection = false;

    public static function create(array $params = null) : \Flexio\Services\Ftp
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'host'     => array('type' => 'string', 'required' => true),
                'username' => array('type' => 'string', 'required' => true),
                'password' => array('type' => 'string', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_params = $validator->getParams();
        $host = $validated_params['host'];
        $username = $validated_params['username'];
        $password = $validated_params['password'];

        $service = new self;
        $service->initialize($host, $username, $password);

        return $service;
    }

    ////////////////////////////////////////////////////////////
    // IConnection interface
    ////////////////////////////////////////////////////////////

    public function connect() : bool
    {
        $host = $this->host;
        $username = $this->username;
        $password = $this->password;

        if ($this->initialize($host, $username, $password) === false)
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
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
/*
        if (!$this->authenticated())
        {
            // try to reconnect
            $this->connect();
            if (!$this->authenticated())
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
                'hash' => '', // TODO: available?
                'type' => 'FILE'
            );

            $result[] = $entry;
        }

        return $result;
*/
    }

    public function getFileInfo(string $path) : array
    {
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function exists(string $path) : bool
    {
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function createFile(string $path, array $properties = []) : bool
    {
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function createDirectory(string $path, array $properties = []) : bool
    {
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function unlink(string $path) : bool
    {
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function open($path) : \Flexio\IFace\IStream
    {
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function read(array $params, callable $callback) // TODO: add return type
    {
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
/*
        $path = $params['path'] ?? '';

        if (!$this->authenticated())
        {
            // try to reconnect
            $this->connect();
            if (!$this->authenticated())
                return;
        }
*/
    }

    public function write(array $params, callable $callback) // TODO: add return type
    {
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
/*
        $path = $params['path'] ?? '';
        $content_type = $params['content_type'] ?? \Flexio\Base\ContentType::STREAM;

        if (!$this->authenticated())
        {
            // try to reconnect
            $this->connect();
            if (!$this->authenticated())
                return;
        }
*/
    }

    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    private function initialize(string $host, string $username, string $password) : bool
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->authenticated = false;

        if ($this->connection !== false)
            ftp_close($this->connection);

        $this->connection = false;

        $ftp = ftp_connect($host);
        if ($ftp === false)
            return false;
        if (ftp_login($ftp, $username, $password) === false)
            return false;

        $this->connection = $ftp;
        $this->authenticated = true;
        return true;
    }
}
