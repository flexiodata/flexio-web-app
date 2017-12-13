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

        $path = $path['path'] ?? (is_string($path) ? $path : '');
        if ($path == '')
            $path = '/';
        
        $folder_stream = $this->getStreamFromPath($path);
        if (!$folder_stream)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $files = [];
        $streams = $folder_stream->getChildStreams();
        foreach ($streams as $stream)
        {
            $entry = $stream->get();

            $fullpath = $path;
            if (substr($fullpath, -1) != '/')
                $fullpath .= '/';
            $fullpath .= $entry['name'];

            $files[] = array('id'=> $entry['eid'] ?? null,
                             'name' => $entry['name'],
                             'path' => $fullpath,
                             'size' => $entry['size'] ?? '',
                             'modified' => $entry['updated'] ?? '',
                             'is_dir' => ($entry['stream_type'] == 'SD' ? true : false));
        }

        return $files;
    }

    public function exists(string $path) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return false;
    }

    public function createFile(string $path, array $properties = []) : bool
    {
        $path = $path['path'] ?? (is_string($path) ? $path : '');
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
            $stream_properties = [
                'parent_eid' => $parent_stream->getEid(),
                'name' => $name,
                'stream_type' => 'SF',
                'path' => \Flexio\Base\Util::generateRandomString(20)
            ];

            if (isset($properties['structure']) && count($properties['structure']) > 0)
            {
                $stream_properties['structure'] = $properties['structure'];
                $stream_properties['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
            }

            $stream = \Flexio\Object\Stream::create($stream_properties);

            if (!$stream)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }
         else
        {
            // make sure the existant stream has a type of file (SF) as opposed to directory (SD)
            $stream_properties = $stream->get();
            if ($stream_properties['stream_type'] != 'SF')
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

            // stream doesn't exist yet; create one
            if (isset($properties['structure']) && count($properties['structure']) > 0)
            {
                $stream_properties = [ 'structure' => $properties['structure'],
                                       'mime_type' => \Flexio\Base\ContentType::FLEXIO_TABLE ];
                $stream->set($stream_properties);
            }
        }

        $streamwriter = $stream->getWriter();

        return true;
    }

    public function open($path) :  \Flexio\Iface\IStream
    {
        $path = $path['path'] ?? (is_string($path) ? $path : '');
        $stream = $this->getStreamFromPath($path);
        if (!$stream)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NOT_FOUND);
        return $stream;
    }

    public function read(array $params, callable $callback)
    {
        $path = $params['path'] ?? (is_string($params) ? $params : '');
        $stream = $this->getStreamFromPath($path);
        if (!$stream)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NOT_FOUND);

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

    


    public function insert(array $params, array $rows)  // $rows is an array of rows
    {
        $path = $params['path'] ?? (is_string($params) ? $params : '');
        $path = trim($path, "/ \t\n\r\0\x0B");

        $stream = $this->getStreamFromPath($path);

        if (!$stream)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

        $inserter = $stream->getInserter();

        foreach ($rows as $row)
        {
            $inserter->write($row);
        }

        return true;
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
