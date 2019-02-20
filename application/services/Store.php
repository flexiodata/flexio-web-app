<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
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


class Store implements \Flexio\IFace\IConnection, \Flexio\IFace\IFileSystem
{

    private $owner_eid = '';

    public static function create(array $params) : \Flexio\Services\Store
    {
        if (!isset($params['owned_by']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        if (!\Flexio\Base\Eid::isValid($params['owned_by']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        $service = new self();
        $service->owner_eid = $params['owned_by'];
        return $service;
    }

    ////////////////////////////////////////////////////////////
    // IFileSystem interface
    ////////////////////////////////////////////////////////////

    public function getFlags() : int
    {
        return \Flexio\IFace\IFileSystem::FLAG_HAS_OPEN;
    }

    public function list(string $path = '', array $options = []) : array
    {
        if (!$this->authenticated())
            return array();

        $path = $path['path'] ?? (is_string($path) ? $path : '');
        if ($path == '')
            $path = '/';

        $folder_stream = $this->getStreamFromPath($path);
        if (!$folder_stream)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $entry =  $folder_stream->get();
        if ($entry['stream_type'] != \Flexio\Object\Stream::TYPE_DIRECTORY)
        {
            // not a folder -- single file listing
            return [
                [ 'id'=> $entry['eid'] ?? null,
                  'name' => $entry['name'],
                  'path' => $path,
                  'size' => $entry['size'] ?? '',
                  'modified' => $entry['updated'] ?? '',
                  'type' => ($entry['stream_type'] == \Flexio\Object\Stream::TYPE_DIRECTORY ? 'DIR' : 'FILE') ]
            ];
        }

        $files = [];
        $streams = $folder_stream->getChildStreams();
        foreach ($streams as $stream)
        {
            $entry = $stream->get();

            $fullpath = $path;
            if (substr($fullpath, -1) != '/')
                $fullpath .= '/';
            $fullpath .= $entry['name'];

            if ($entry['stream_type'] == \Flexio\Object\Stream::TYPE_DIRECTORY)
                $type = 'DIR';
            else if ($entry['mime_type'] == 'application/vnd.flexio.table')
                $type = 'TABLE';
            else
                $type = 'FILE';

            $files[] = array('id'=> $entry['eid'] ?? null,
                             'name' => $entry['name'],
                             'path' => $fullpath,
                             'size' => $entry['size'] ?? '',
                             'modified' => $entry['updated'] ?? '',
                             'type' => $type);
        }

        return $files;
    }

    public function authenticated() : bool
    {
        return true;
    }

    public function getFileInfo(string $path) : array
    {
        $path = $path['path'] ?? (is_string($path) ? $path : '');
        if ($path == '')
            $path = '/';

        $stream = $this->getStreamFromPath($path);
        if (!$stream)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        }

        $entry = $stream->get();

        if ($entry['stream_type'] == \Flexio\Object\Stream::TYPE_DIRECTORY)
            $type = 'DIR';
        else if ($entry['mime_type'] == 'application/vnd.flexio.table')
            $type = 'TABLE';
        else
            $type = 'FILE';

        $ret = [ 'id' => $entry['id'] ?? null,
                 'name' => $entry['name'],
                 'size' => $entry['size'] ?? '',
                 'modified' => $entry['updated'] ?? '',
                 'type' => $type ];

        if ($type == 'TABLE')
            $ret['structure'] = $entry['structure'];

        return $ret;
    }

    public function exists(string $path) : bool
    {
        $stream = $this->getStreamFromPath($path);
        if (!$stream)
            return false;

        return true;
    }

    public function createFile(string $path, array $properties = []) : bool
    {
        $path = $path['path'] ?? (is_string($path) ? $path : '');
        $path = trim($path, "/ \t\n\r\0\x0B");

        $last_slash = strrpos($path, '/');
        if ($last_slash === false)
        {
            $parent_stream = $this->getStreamFromPath('/');
            $name = $path;
        }
         else
        {
            $name = substr($path, $last_slash+1);
            $path = substr($path, 0, $last_slash);
            $parent_stream = $this->getStreamFromPath($path, true /* create directory structure if it doesn't already exist */);
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
                'stream_type' => \Flexio\Object\Stream::TYPE_FILE,
                'path' => \Flexio\Base\Util::generateRandomString(20),
                'owned_by' => $parent_stream->getOwner()
            ];

            if (isset($properties['mime_type']))
            {
                $stream_properties['mime_type'] = $properties['mime_type'];
            }

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
            if ($stream_properties['stream_type'] != \Flexio\Object\Stream::TYPE_FILE)
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

    public function createDirectory(string $path, array $properties = []) : bool
    {
        $stream = $this->getStreamFromPath($path);
        if ($stream)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED, "Object already exists");

        $parent_stream = $this->getStreamFromPath($path, true /* create directory structure if it doesn't already exist */);
        return $parent_stream ? true : false;
    }

    public function unlink(string $path) : bool
    {
        $stream = $this->getStreamFromPath($path);
        if (!$stream)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        $stream->delete();

        return false;
    }

    public function open($path) : \Flexio\Iface\IStream
    {
        $path = $path['path'] ?? (is_string($path) ? $path : '');
        $stream = $this->getStreamFromPath($path);
        if (!$stream)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        $info = $stream->get();
        if ($info['stream_type'] == \Flexio\Object\Stream::TYPE_DIRECTORY)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::OPEN_FAILED);

        return $stream;
    }

    public function read(array $params, callable $callback) // TODO: add return type
    {
        $path = $params['path'] ?? (is_string($params) ? $params : '');
        $stream = $this->getStreamFromPath($path);
        if (!$stream)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);


        $entry = $stream->get();
        if ($entry['mime_type'] == 'application/vnd.flexio.table')
        {
            $reader = $stream->getReader();
            while (($row = $reader->readRow()) !== false)
                $callback($row);
        }
         else
        {
            $reader = $stream->getReader();
            while (($buf = $reader->read(16384)) !== false)
                $callback($buf);
        }
    }

    public function write(array $params, callable $callback) // TODO: add return type
    {
        if (isset($params['structure']))
        {
            $callback = \Flexio\Services\Util::tableToCsvCallbackAdaptor($params['structure'], $callback);
        }

        $path = $params['path'] ?? (is_string($params) ? $params : '');
        $path = trim($path, "/ \t\n\r\0\x0B");

        $last_slash = strrpos($path, '/');
        if ($last_slash === false)
        {
            $name = $path;
            $parent_stream = $this->getStreamFromPath('/');
        }
         else
        {
            $name = substr($path, $last_slash+1);
            $path = substr($path, 0, $last_slash);
            $parent_stream = $this->getStreamFromPath($path, true /* create directory structure if it doesn't already exist */);
        }

        if (!$parent_stream)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

        // if the stream already exists, overwrite it
        $arr = $parent_stream->getChildStreams($name);
        $stream = $arr[0] ?? null;

        if ($stream !== null)
        {
            $info = $stream->get();
            if ($info['stream_type'] == \Flexio\Object\Stream::TYPE_DIRECTORY)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }

        if ($stream === null)
        {
            // stream doesn't exist yet; create one

            $stream = \Flexio\Object\Stream::create([
                'parent_eid' => $parent_stream->getEid(),
                'name' => $name,
                'stream_type' => \Flexio\Object\Stream::TYPE_FILE,
                'path' => \Flexio\Base\Util::generateRandomString(20),
                'owned_by' => $parent_stream->getOwner()
            ]);
        }

        if (!$stream)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

        $streamwriter = $stream->getWriter();

        while (($buf = $callback(16384)) !== false)
            $streamwriter->write($buf);
    }

    public function insert(array $params, array $rows /*an array of rows*/) // TODO: add return type
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

    public function getOwner() : string
    {
        return $this->owner_eid;
    }

    private function connect() : bool
    {
        return true;
    }

    private function getStreamFromPath(string $path, bool $create_dir_structure = false) : ?\Flexio\Object\Stream
    {
        $owner_user_eid = $this->getOwner();
        $user = false;
        try
        {
            $user = \Flexio\Object\User::load($owner_user_eid);
            if ($user->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        }
        catch (\Flexio\Base\Exception $e)
        {
            return null;
        }

        $stream = $user->getStoreRoot();
        if (!$stream)
            return null;

        if ($path == '/')
            return $stream; // return the root

        $parts = \Flexio\Base\File::splitPath($path);

        foreach ($parts as $part)
        {
            $arr = $stream->getChildStreams($part);
            $child = $arr[0] ?? null;
            if (is_null($child))
            {
                if ($create_dir_structure)
                {
                    $child = \Flexio\Object\Stream::create([
                        'parent_eid' => $stream->getEid(),
                        'name' => $part,
                        'stream_type' => \Flexio\Object\Stream::TYPE_DIRECTORY,
                        'path' => \Flexio\Base\Util::generateRandomString(20),
                        'owned_by' => $stream->getOwner()
                    ]);
                    if (!$child)
                        return null;
                }
                 else
                {
                    return null;
                }
            }

            $stream = $child;
        }

        return $stream;
    }
}
