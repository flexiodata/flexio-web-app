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


class SftpCapture
{
    function stream_open($path, $mode, $options, &$opened_path)
    {
        return true;
    }

    function stream_stat()
    {
        return [ 'size' => 0 ];
    }

    function stream_write($data)
    {
        $len = strlen($data);
        $GLOBALS['g_sftp_callback']($data);
        return $len;
    }
}


class Sftp implements \Flexio\IFace\IConnection, \Flexio\IFace\IFileSystem
{
    private $host;
    private $username;
    private $password;
    private $connection = false;
    private $base_path = '';
    private $authenticated = false;

    public static function create(array $params = null) : \Flexio\Services\Sftp
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'host'      => array('type' => 'string', 'required' => true),
                'username'  => array('type' => 'string', 'required' => true),
                'password'  => array('type' => 'string', 'required' => true),
                'base_path' => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $host = $validated_params['host'];
        $username = $validated_params['username'];
        $password = $validated_params['password'];
        $base_path = $validated_params['base_path'] ?? '';

        $service = new self;
        if ($service->initialize($host, $username, $password, $base_path) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        return $service;
    }

    public function authenticated() : bool
    {
        return $this->authenticated;
    }

    private static function mergePath(string $basepath, string $path) : string
    {
        if ($path === '')
            $path = '/';

        if ($basepath === '')
            return $path;

        $parts = explode('/', trim($path,'/'));
        $out = [];
        foreach ($parts as $part)
        {
            if ($part === '.' || $part == '')
                continue;
            if ($part === '..')
            {
                array_pop($out);
                continue;
            }

            $out[] = $part;
        }

        $path = implode('/', $out);

        $res = $basepath;
        if (substr($res, -1) == '/')
            $res = substr($res, 0, strlen($res)-1);

        if (strlen($path) == 0 || $path == '/')
            return $res;

        if ($path[0] == '/')
            return $res . $path;
             else
            return $res . '/' . $path;
    }

    private function getFullPath(string $path) : string
    {
        return self::mergePath($this->base_path, $path);
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

        $files = $this->connection->rawlist($this->getFullPath($path));
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
                'modified' => '',
                'type' => ($info['type'] === NET_SFTP_TYPE_DIRECTORY) ? 'DIR' : 'FILE'
            );

            $result[] = $entry;
        }

        return $result;
    }

    public function getFileInfo(string $path) : array
    {
        if (!$this->checkConnect())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CONNECTION_FAILED);

        $info = $this->connection->lstat($this->getFullPath($path));

        if (!isset($info['type']))
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NOT_FOUND);
        }

        $arr = \Flexio\Base\File::splitBasePathAndName($path);
        $base = $arr['base'];
        $name = $arr['name'];

        return [
            'name' => $name,
            'path' => $path,
            'size' => $info['size'] ?? null,
            'modified' => date('c', ($info['mtime'] ?? time())),
            'type' => (($info['type'] ?? 0) == 2) ? 'DIR' : 'FILE'
        ];
    }

    public function exists(string $path) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return false;
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

        if ($this->connection->file_exists($this->getFullPath($path)))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED, "Object already exists");

        $this->connection->mkdir($this->getFullPath($path), -1, true);
        return $this->isDirectory($path);
    }

    public function unlink(string $path) : bool
    {
        if (!$this->checkConnect())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CONNECTION_FAILED);

        if ($this->isDirectory($path))
        {
            return $this->connection->delete($this->getFullPath($path), true /*recursive*/);
        }
         else
        {
            return $this->connection->delete($this->getFullPath($path), false);
        }
    }

    public function open($path) : \Flexio\IFace\IStream
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }


    private function isDirectory(string $path) : bool
    {
        if (!$this->checkConnect())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CONNECTION_FAILED);

        $info = $this->connection->lstat($this->getFullPath($path));
        $type = $info['type'] ?? 0;
        return ($type == 2 ? true : false);
    }

    public function read(array $params, callable $callback)
    {
        if (!$this->checkConnect())
            return false;

        $GLOBALS['g_sftp_callback'] = $callback;
        stream_wrapper_register('sftpcapture', 'Flexio\Services\SftpCapture') or die("Failed to register protocol");
        $fp = fopen("sftpcapture://", "wb");

        $path = $params['path'] ?? '';
        $res = $this->connection->get($this->getFullPath($path), $fp);

        fclose($fp);
        stream_wrapper_unregister('sftpcapture');

        if ($res === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NOT_FOUND);
    }

    public function write(array $params, callable $callback)
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
            $this->connection->mkdir($this->getFullPath($folder), -1, true);
        }

        $this->connection->put($this->getFullPath($path), function($length) use (&$callback) {
            $res = $callback($length);
            if ($res === false) return null; // SFTP.php expects null upon eof
            return $res;
        }, \phpseclib\Net\SFTP::SOURCE_CALLBACK);
    }

    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

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

    private function connect() : bool
    {
        $host = $this->host;
        $username = $this->username;
        $password = $this->password;

        if ($this->initialize($host, $username, $password) === false)
            return false;

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
