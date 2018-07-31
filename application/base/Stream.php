<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-04-20
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class StreamReader implements \Flexio\IFace\IStreamReader
{
    private $stream;
    private $offset;

    public function __construct()
    {
        $this->offset = 0;
    }

    public static function create(\Flexio\Base\Stream $stream) : \Flexio\Base\StreamReader
    {
        $object = new static();
        $object->stream = $stream;
        return $object;
    }

    public function read($length = 1024)
    {
        if ($this->stream->isTable())
        {
            // this class not used for tables -- see StorageFileReaderWriter
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        }
         else
        {
            if ($this->offset >= strlen($this->stream->buffer))
                return false;

            $str = substr($this->stream->buffer, $this->offset, $length);
            if ($str === false)
                return false;
            $this->offset += strlen($str);
            return $str;
        }
    }

    public function readRow()
    {
        if ($this->stream->isTable())
        {
            // this class not used for tables -- see StorageFileReaderWriter
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        }
         else
        {
            $linelen = strpos($this->stream->buffer, "\n");
            if ($linelen === false)
            {
                return $this->read(strlen($this->stream->buffer));
            }
             else
            {
                $line = $this->read($linelen);
                if ($line === false)
                    return false;
                $this->read(1); // for the \n
                return rtrim($line, "\r");
            }
        }
    }

    public function getRows(int $offset, int $limit)
    {
        // this class not used for tables -- see StorageFileReaderWriter
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function close() : bool
    {
        return true;
    }
}




class StreamWriter implements \Flexio\IFace\IStreamWriter
{
    private $stream;
    private $bytes_written;
    private $storagefs_writer = null;
    public $memory_table_writer = null;

    public function __construct()
    {
        $this->bytes_written = 0;
    }

    public static function create(\Flexio\Base\Stream $stream) : \Flexio\Base\StreamWriter
    {
        $object = new static();
        $object->stream = $stream;
        return $object;
    }

    public function write($data)
    {
        if ($this->storagefs_writer)
            return $this->storagefs_writer->write($data);

        if ($this->stream->isTable())
        {
            if ($this->bytes_written > 1000000)
            {
                $this->memory_table_writer->close();
                $this->memory_table_writer = null;

                $this->storagefs_writer = $this->stream->switchToDiskStorage($this);
                return $this->storagefs_writer->write($data);
            }

            $this->memory_table_writer->write($data);
            $this->bytes_written += strlen(serialize($data));
        }
         else
        {
            if (!is_string($data))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, 'Expected string value');

            $curlen = strlen($this->stream->buffer);
            $datalen = strlen($data);
            if ($curlen + $datalen > 2000000) // if memory buffer is greater than 2MB, convert to a disk stream
            {
                $this->storagefs_writer = $this->stream->switchToDiskStorage($this);
                return $this->storagefs_writer->write($data);
            }

            $this->stream->buffer .= $data;
            $this->bytes_written += strlen($data);
        }

        return true;
    }

    public function getBytesWritten() : int
    {
        if ($this->storagefs_writer)
            return $this->storagefs_writer->getBytesWritten();

        return $this->bytes_written;
    }

    public function close() : bool
    {
        if ($this->storagefs_writer)
            return $this->storagefs_writer->close();

        if ($this->memory_table_writer)
            return $this->memory_table_writer->close();

        return true;
    }
}


class Stream implements \Flexio\IFace\IStream
{
    public $buffer = '';             // data buffer; use reader/writer to access
    private $storagefs = null;
    private $storagefs_path = null;
    private $memory_db = null;

    // properties
    private $properties;

    public function __construct()
    {
        //$this->id = \Flexio\Base\Util::generateRandomString(5);

        $this->buffer = '';

        // note: default values match model defaults
        $this->properties = array();
        $this->properties['name'] = '';
        $this->properties['path'] = '';
        $this->properties['size'] = null;
        $this->properties['hash'] = '';
        $this->properties['mime_type'] = '';
        $this->properties['structure'] = array();
        $this->properties['file_created'] = null;
        $this->properties['file_modified'] = null;
        $this->properties['connection_eid'] = '';
        $this->properties['created'] = null;
        $this->properties['updated'] = null;
    }

    public function getImpl() { return $this; }

    public static function create(array $properties = null) : \Flexio\Base\Stream
    {
        $object = new static();

        // structure must be valid
        if (isset($properties) && isset($properties['structure']))
        {
            // if the structure is set, make sure it's valid
            $structure = $properties['structure'];
            $structure_object = \Flexio\Base\Structure::create($structure);
            $object->properties['structure'] = $structure_object->enum();
        }

        // add a default path, but only if a connection eid, isn't specified; TODO:
        // this behavior is for consistency with the regular stream object; should
        // uncouple the default path creation from the connection eid
        if (!isset($properties['connection_eid']))
        {
            // default path
            if (!isset($properties['path']))
                $object->properties['path'] = \Flexio\Base\Util::generateHandle();
        }

        if (isset($properties['name']))
            $object->properties['name'] = $properties['name'];
        if (isset($properties['path']))
            $object->properties['path'] = $properties['path'];
        if (isset($properties['size']))
            $object->properties['size'] = $properties['size'];
        if (isset($properties['hash']))
            $object->properties['hash'] = $properties['hash'];
        if (isset($properties['mime_type']))
            $object->properties['mime_type'] = $properties['mime_type'];
        if (isset($properties['file_created']))
            $object->properties['file_created'] = $properties['file_created'];
        if (isset($properties['file_modified']))
            $object->properties['file_modified'] = $properties['file_modified'];

        $object->prepareStorage();

        return $object;
    }

    public function isTable() : bool
    {
        if (($this->properties['mime_type'] ?? '') == \Flexio\Base\ContentType::FLEXIO_TABLE)
            return true;
        return count($this->properties['structure']) > 0 ? true : false;
    }

    private $structure_stamp = '';
    private function prepareStorage()
    {
        if ($this->isTable())
        {
            // start out with memory table

            $structure_stamp = md5(serialize($this->properties['structure']));
            if ($structure_stamp != $this->structure_stamp)
            {
                $this->structure_stamp = $structure_stamp;
                $this->memory_db = $this->getStorageFs()->createFile('', [ 'structure' => $this->properties['structure'], 'memory' => true ]);
            }
        }
    }

    public function switchToDiskStorage(\Flexio\Base\StreamWriter $writer) : \Flexio\IFace\IStreamWriter
    {
        $storagefs = $this->getStorageFs();

        $create_params = [];
        if ($this->isTable())
        {
            $create_params['structure'] = $this->properties['structure'];
        }

        $path = \Flexio\Base\Util::generateRandomString(20);

        $writer = null;
        $file = $storagefs->createFile($path, $create_params);

        if ($file)
        {
            $writer = $file->getWriter();
        }

        if (is_null($writer))
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, "Could not create temporary storage stream");
        }

        if ($this->memory_db)
        {
            $reader = $this->memory_db->getReader();
            while (($row = $reader->readRow()) !== false)
                $writer->write($row);
            $this->memory_db = null;
            $this->storagefs_path = $path;
        }
         else
        {
            $writer->write($this->buffer);
            $this->buffer = null;
            $this->storagefs_path = $path;
        }

        return $writer;
    }


    private function getStorageFs() : \Flexio\Services\StorageFs
    {
        if ($this->storagefs === null)
        {
            $storage_root = $GLOBALS['g_config']->storage_root ?? '';
            if (strlen($storage_root) == 0)
            {
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
            }

            if (IS_DEBUG())
            {
                if (!is_writable($storage_root . DIRECTORY_SEPARATOR . 'tmp'))
                {
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS, "Cannot write to tmp store directory");
                }
            }

            $this->storagefs = \Flexio\Services\StorageFs::create(['base_path' => $storage_root . DIRECTORY_SEPARATOR . 'tmp']);
        }

        return $this->storagefs;
    }


    public function copyFrom(\Flexio\IFace\IStream $source) : \Flexio\Base\Stream
    {
        // copies all the properties of another stream into the current stream,
        // including the buffer
        $sourceimpl = $source->getImpl();

        if (!($sourceimpl instanceof \Flexio\Base\Stream))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED, "copy may only be used on stream objects of the same type");

        $properties = $source->get();
        unset($properties['eid']);
        unset($properties['created']);
        unset($properties['updated']);
        $this->set($properties);

        $this->buffer = $sourceimpl->buffer;
        $this->storagefs = $sourceimpl->storagefs;
        $this->storagefs_path = $sourceimpl->storagefs_path;
        $this->memory_db = $sourceimpl->memory_db;

        return $this;
    }

    public function set(array $properties) : \Flexio\Base\Stream
    {
        // TODO: add properties check

        // structure is stored as json string; it needs to be validated
        // and encoded
        if (isset($properties['structure']))
        {
            // if the structure is set, make sure it's valid
            $structure = $properties['structure'];
            $structure_object = \Flexio\Base\Structure::create($structure);
            $this->properties['structure'] = $structure_object->enum();
        }

        if (isset($properties['name']))
            $this->properties['name'] = $properties['name'];
        if (isset($properties['path']))
            $this->properties['path'] = $properties['path'];
        if (isset($properties['size']))
            $this->properties['size'] = $properties['size'];
        if (isset($properties['hash']))
            $this->properties['hash'] = $properties['hash'];
        if (isset($properties['mime_type']))
            $this->properties['mime_type'] = $properties['mime_type'];
        if (isset($properties['file_created']))
            $this->properties['file_created'] = $properties['file_created'];
        if (isset($properties['file_modified']))
            $this->properties['file_modified'] = $properties['file_modified'];

        if ($this->isTable() && !$this->memory_db)
        {
            $this->prepareStorage();
        }

        return $this;
    }

    public function get() : array
    {
        return $this->properties;
    }

    public function setName(string $name) : \Flexio\Base\Stream
    {
        $properties = array();
        $properties['name'] = $name;
        return $this->set($properties);
    }

    public function getName() : string
    {
        return $this->properties['name'];
    }

    public function setPath(string $path) : \Flexio\Base\Stream
    {
        $properties = array();
        $properties['path'] = $path;
        return $this->set($properties);
    }

    public function getPath() : string
    {
        return $this->properties['path'];
    }

    public function setSize($size) : \Flexio\Base\Stream // TODO: add input parameter types
    {
        $properties = array();
        $properties['size'] = $size;
        return $this->set($properties);
    }

    public function getSize() // TODO: add return type (size can be null)
    {
        return $this->properties['size'];
    }

    public function getRowCount() : int
    {
        // TODO: populate from content
        return 0;
    }

    public function setMimeType(string $mime_type) : \Flexio\Base\Stream
    {
        $properties = array();
        $properties['mime_type'] = $mime_type;
        return $this->set($properties);
    }

    public function getMimeType() : string
    {
        return $this->properties['mime_type'];
    }

    public function setStructure($structure) : \Flexio\Base\Stream // TODO: add input parameter types
    {
        if (!($structure instanceof \Flexio\Base\Structure))
            $structure = \Flexio\Base\Structure::create($structure);

        $properties = array();
        $properties['structure'] = $structure->enum();
        return $this->set($properties);
    }

    public function getStructure() : \Flexio\Base\Structure
    {
        $s = $this->properties['structure'];
        $structure = \Flexio\Base\Structure::create($s);
        return $structure;
    }

    public function getFileInfo() : array
    {
        $info = array();
        $info['name'] = $this->properties['name'];
        $info['path'] = $this->properties['path'];
        $info['size'] = $this->properties['size'];
        $info['hash'] = $this->properties['hash'];
        $info['mime_type'] = $this->properties['mime_type'];
        $info['structure'] = $this->properties['structure'];
        $info['file_created'] = $this->properties['file_created'];
        $info['file_modified'] = $this->properties['file_modified'];

        return $info;
    }

    public function getReader() : \Flexio\IFace\IStreamReader
    {
        if ($this->memory_db)
        {
            return $this->memory_db->getReader();
        }
         else if (!is_null($this->storagefs_path))
        {
            $file = $this->getStorageFs()->open($this->storagefs_path);
            $file->setStructure($this->properties['structure']);
            return $file->getReader();
        }
         else
        {
            return \Flexio\Base\StreamReader::create($this);
        }
    }

    public function getWriter() : \Flexio\IFace\IStreamWriter
    {
        $this->buffer = '';

        $this->prepareStorage();

        if ($this->memory_db)
        {
            $writer = \Flexio\Base\StreamWriter::create($this);
            $writer->memory_table_writer = $this->memory_db->getWriter();
            return $writer;
        }
         else if (!is_null($this->storagefs_path))
        {
            $file = $this->getStorageFs()->open($this->storagefs_path);
            $file->setStructure($this->properties['structure']);
            return $file->getWriter();
        }
         else
        {
            return \Flexio\Base\StreamWriter::create($this);
        }
    }
}

