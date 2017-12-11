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


class Store implements \Flexio\IFace\IFileSystem
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

    public function createFile(string $path, array $properties = []) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
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
            $parent_stream = $this->getStreamFromPath('/');
            $name = $path;
        }
         else
        {
            $name = substr($path, $last_slash+1);
            $path = substr($path, 0, $last_slash);
            $parent_stream = $this->getStreamFromPath($path);
        }

        if (!$parent_stream)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

        // if the stream already exists, overwrite it
        $arr = $parent_stream->getChildStreams($name);
        $stream = $arr[0] ?? null;

        if ($stream === null)
        {
            // stream doesn't exist yet; create one

            $stream = \Flexio\Object\Stream::create([
                'parent_eid' => $parent_stream->getEid(),
                'name' => $name,
                'stream_type' => 'SF',
                'path' => \Flexio\Base\Util::generateRandomString(20)
            ]);
        }

        if (!$stream)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

        $streamwriter = $stream->getWriter();

        while (($buf = $callback(16384)) !== false)
            $streamwriter->write($buf);
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


    private function getStreamFromPath(string $path) // : ?\Flexio\Object\Stream
    {
        $current_user_eid = \Flexio\System\System::getCurrentUserEid();
        $user = \Flexio\Object\User::load($current_user_eid);
        if ($user === false)
            return $user;

        $stream = $user->getStoreRoot();
        if (!$stream)
            return null;

        if ($path == '/')
            return $stream; // return the root

        $path = trim($path, "/ \t\n\r\0\x0B");
        if (strlen($path) == 0)
            return null;

        $parts = explode('/', $path);
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
