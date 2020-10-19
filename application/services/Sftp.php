<?php
/**
 *
 * Copyright (c) 2016, Flex Research LLC. All rights reserved.
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


class SftpCapture
{
    function stream_open($path, $mode, $options, &$opened_path) : bool
    {
        return true;
    }

    function stream_stat() : array
    {
        return [ 'size' => 0 ];
    }

    function stream_write($data) // TODO: add return type
    {
        $len = strlen($data);
        $GLOBALS['g_sftp_callback']($data);
        return $len;
    }
}


class Sftp implements \Flexio\IFace\IConnection,
                      \Flexio\IFace\IFileSystem
{
    // connection info
    private $host;
    private $username;
    private $password;
    private $base_path = '';

    // state info
    private $authenticated = false;
    private $connection = false;

    public static function create(array $params = null) : \Flexio\Services\Sftp
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'host'      => array('type' => 'string', 'required' => true),
                'username'  => array('type' => 'string', 'required' => true),
                'password'  => array('type' => 'string', 'required' => true),
                'base_path' => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_params = $validator->getParams();
        $host = $validated_params['host'];
        $username = $validated_params['username'];
        $password = $validated_params['password'];
        $base_path = $validated_params['base_path'] ?? '';

        $service = new self;
        $service->initialize($host, $username, $password, $base_path);

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
        $base_path = $this->base_path;

        if ($this->initialize($host, $username, $password, $base_path) === false)
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
            'password' => $this->password,
            'base_path' => $this->base_path
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
        if (!$this->authenticated())
        {
            // try to reconnect
            $this->connect();
            if (!$this->authenticated())
                return array();
        }

        $list_path = $path;
        if ($list_path == '')
            $list_path = '/';

        $files = $this->connection->rawlist($this->getRemotePath($path));
        if (!is_array($files))
        {
            $arr = $this->getFileInfo($path);
            $arr['path'] = $path;
            return [ $arr ];
        }

        $result = array();
        foreach ($files as $file => $info)
        {
            if ($file == '.' || $file == '..')
                continue;

            $full_path = $list_path;
            if (substr($full_path, -1) != '/')
                $full_path .= '/';
            $full_path .= $file;

            $entry = array(
                'name' => $file,
                'path' => $full_path,
                'size' => $info['size'] ?? null,
                'modified' => date('c', ($info['mtime'] ?? time())),
                'hash' => '', // TODO: available?
                'type' => ($info['type'] === NET_SFTP_TYPE_DIRECTORY) ? 'DIR' : 'FILE'
            );

            $result[] = $entry;
        }

        return $result;
    }

    public function getFileInfo(string $path) : array
    {
        if (!$this->checkConnect())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        $info = $this->connection->lstat($this->getRemotePath($path));

        if (!isset($info['type']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        $arr = \Flexio\Base\File::splitBasePathAndName($path);
        $base = $arr['base'];
        $name = $arr['name'];

        return [
            'name' => $name,
            'path' => $path,
            'size' => $info['size'] ?? null,
            'modified' => date('c', ($info['mtime'] ?? time())),
            'hash' => '', // TODO: available?
            'type' => (($info['type'] ?? 0) == 2) ? 'DIR' : 'FILE'
        ];
    }

    public function exists(string $path) : bool
    {
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function createFile(string $path, array $properties = []) : bool
    {
        while (false !== strpos($path,'//'))
            $path = str_replace('//','/',$path);

        $this->write([ 'path' => $path ], function($length) { return false; });
        return true;
    }

    public function createDirectory(string $path, array $properties = []) : bool
    {
        if (!$this->checkConnect())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CONNECTION_FAILED);

        if ($this->connection->file_exists($this->getRemotePath($path)))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED, "Object already exists");

        $this->connection->mkdir($this->getRemotePath($path), -1, true);
        return $this->isDirectory($path);
    }

    public function unlink(string $path) : bool
    {
        if (!$this->checkConnect())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CONNECTION_FAILED);

        if ($this->isDirectory($path))
        {
            return $this->connection->delete($this->getRemotePath($path), true /*recursive*/);
        }
         else
        {
            return $this->connection->delete($this->getRemotePath($path), false);
        }
    }

    public function open($path) : \Flexio\IFace\IStream
    {
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    private function isDirectory(string $path) : bool
    {
        if (!$this->checkConnect())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CONNECTION_FAILED);

        $info = $this->connection->lstat($this->getRemotePath($path));
        $type = $info['type'] ?? 0;
        return ($type == 2 ? true : false);
    }

    public function read(array $params, callable $callback) // TODO: add return type
    {
        if (!$this->checkConnect())
            return false;

        $GLOBALS['g_sftp_callback'] = $callback;
        stream_wrapper_register('sftpcapture', 'Flexio\Services\SftpCapture') or die("Failed to register protocol");
        $fp = fopen("sftpcapture://", "wb");

        $path = $params['path'] ?? '';
        $res = $this->connection->get($this->getRemotePath($path), $fp);

        fclose($fp);
        stream_wrapper_unregister('sftpcapture');

        if ($res === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
    }

    public function write(array $params, callable $callback) // TODO: add return type
    {
        if (!$this->checkConnect())
            return false;

        if (isset($params['structure']))
        {
            $callback = \Flexio\Services\Util::tableToCsvCallbackAdaptor($params['structure'], $callback);
        }

        $path = $params['path'] ?? '';
        $content_type = $params['content_type'] ?? \Flexio\Base\ContentType::STREAM;

        // if the target path is a folder, throw an exception
        if ($this->isDirectory($path))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

        $folder = trim($path,'/');
        while (false !== strpos($folder,'//'))
            $folder = str_replace('//','/',$folder);
        $parts = explode('/',$folder);

        $filename = array_pop($parts);
        $folder = '/' . join('/',$parts);

        if (strlen($filename) == 0)
            return false;

        if (!$this->isDirectory($folder))
        {
            $this->connection->mkdir($this->getRemotePath($folder), -1, true);
        }

        $this->connection->put($this->getRemotePath($path), function($length) use (&$callback) {
            $res = $callback($length);
            if ($res === false) return null; // SFTP.php expects null upon eof
            return $res;
        }, \phpseclib\Net\SFTP::SOURCE_CALLBACK);
    }

    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    private function getRemotePath(string $path) : string
    {
        return \Flexio\Services\Util::mergePath($this->base_path, $path);
    }

    private function checkConnect() : bool
    {
        if (!$this->authenticated())
        {
            // try to reconnect
            $this->connect();
            if (!$this->authenticated())
                return false;
        }
        return true;
    }

    private function initialize(string $host, string $username, string $password, string $base_path) : bool
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->base_path = $base_path;
        $this->authenticated = false;

        if ($this->connection !== false)
            $this->connection->disconnect();

        $this->connection = false;

        if (strlen(trim($host)) == 0)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CONNECTION_FAILED, "Missing host name");
        }

        $sftp = new \phpseclib\Net\SFTP($host);
        if (!@$sftp->login($username, $password)) {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CONNECTION_FAILED, "Could not connect to remote host " . $host);
        }

        $this->connection = $sftp;
        $this->authenticated = true;
        return true;
    }
}
