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

    private $config = array();
    private $connection = false;
    private $is_ok = false;


    ////////////////////////////////////////////////////////////
    // IConnection interface
    ////////////////////////////////////////////////////////////

    public static function create(array $params = null) : \Flexio\Services\Ftp
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
            ftp_close($this->connection);

        $this->connection = false;
        $this->is_ok = false;
    }

    ////////////////////////////////////////////////////////////
    // IFileSystem interface
    ////////////////////////////////////////////////////////////

    public function listObjects(string $path = '') : array
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);

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
            $this->connect($this->config);
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
            $this->connect($this->config);
            if (!$this->isOk())
                return;
        }

        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
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

        $ftp = ftp_connect($host);
        if ($ftp === false)
            return;
        if (ftp_login($ftp, $username, $password) === false)
            return;

        $this->connection = $ftp;
        $this->is_ok = true;
    }
}
