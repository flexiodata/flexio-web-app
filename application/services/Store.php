<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2017-12-07
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Abstract.php';

class Store implements \Flexio\Services\IConnection, \Flexio\Services\IFileSystem
{
    public static function create(array $params = null) : \Flexio\Services\Store
    {
        $service = new self();
        return $service;
    }

    ////////////////////////////////////////////////////////////
    // IFileSystem interface
    ////////////////////////////////////////////////////////////

    public function list(string $path = '') : array
    {
        if (!$this->isOk())
            return array();

        $folder = trim($path,'/');
        $folder = explode('/', $folder);
        $folder = $folder[0] ?? '';
        if ($folder == '')
            return array();

        $result = array();
        if (!$this->isOk())
            return $result;

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
        $path = $params['path'] ?? (is_string($params) ? $params : '');
        $stream = $this->getStreamFromPath($path);
        if (!$stream)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);            

        $reader = $stream->getReader();
        while (($buf = $reader->read(16384)) !== false)
            $callback($buf);
    }

    public function write(array $params, callable $callback)
    {
        $path = $params['path'] ?? (is_string($params) ? $params : '');
        $path = trim($path, "/ \t\n\r\0\x0B");

        $last_slash = strrpos('/', $path);
        if ($last_slash === false)
        {
            $parent_stream = $this->getRootStream();
            $name = $path;
        }
         else
        {
            $name = substr($path, $last_slash+1);
            $path = substr($path, 0, $last_slash);
            $parent_stream = $this->getStreamFromPath($path);
        }

        throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);            
    }

    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    private function connect() : bool
    {
        return true;
    }

    private function isOk() : bool
    {
        return true;
    }


    private function getRootStream() // : ?\Flexio\Object\Stream
    {
        return \Flexio\Object\Store::load(['stream_type' => 'SR']);
    }


    private function getStreamFromPath(string $path) // : ?\Flexio\Object\Stream
    {
        $path = trim($path, "/ \t\n\r\0\x0B");
        if (strlen($path) == 0)
            return null;
        
        $parts = explode('/', $path);

        $stream = $this->getRootStream();
        if (!$stream)
            return null;
        
        foreach ($parts as $part)
        {
            $arr = $stream->getChildStreams($part);
            $stream = $arr[0] ?? null;
            if (is_null($stream))
                return null;
        }

        return $stream;
    }
}
