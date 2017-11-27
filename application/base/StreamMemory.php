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


require_once dirname(__DIR__) . '/base/Abstract.php';





class StreamMemoryReader implements \Flexio\Base\IStreamReader
{
    private $stream;
    private $offset;

    public function __construct()
    {
        $this->offset = 0;
    }

    public static function create(\Flexio\Base\StreamMemory $stream) : \Flexio\Base\StreamMemoryReader
    {
        $object = new static();
        $object->stream = $stream;
        return $object;
    }

    public function read($length = 1024)
    {
        if ($this->stream->is_table)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        }
         else
        {
            $str = $this->readString($this->offset, $length);
            if ($str === false)
                return false;
            $this->offset += strlen($str);
            return $str;
        }
    }

    public function readRow()
    {
        if ($this->stream->is_table)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        }
         else
        {
            $rows = $this->readArrayRows($this->offset, 1);
            if ($rows === false)
                return false;

            $this->offset++;
            return $rows[0];
        }
    }

    public function getRows(int $offset, int $limit)
    {
        if ($offset < 0)
            $offset = 0;
        if ($limit < 0)
            $limit = 0;

        // only implemented for table type streams
        $mime_type = $this->getStream()->getMimeType();
        if ($mime_type != \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);

        $result = $this->readArrayRows($offset, $limit);
        if ($result === false)
            return false;

        return $result;
    }

    public function close() : bool
    {
        return true;
    }

    private function getStream() : \Flexio\Base\StreamMemory
    {
        return $this->stream;
    }

    private function readString(int $offset, int $limit)
    {
        if (!is_string($this->getStream()->buffer))
            return false;

        if ($offset >= strlen($this->getStream()->buffer))
            return false;

        return substr($this->getStream()->buffer, $this->offset, $limit);
    }

    private function readArrayRows(int $start, int $limit)
    {
        $buffer = $this->getStream()->buffer;
        if (!is_array($buffer))
            return false;

        if ($start > count($buffer) - 1)
            return false;

        $rows = array_slice($buffer, $start, $limit);

        // data may be stored either with keys or as simple values; combine these
        // with the structure when returning the result

        $columns = $this->getStream()->getStructure()->enum();
        $result = array();
        foreach ($rows as $r)
        {
            $row_is_associative = \Flexio\Base\Util::isAssociativeArray($r);

            $idx = 0;
            $mapped_row = array();
            foreach ($columns as $c)
            {
                $column_name = $c['name'];
                if ($row_is_associative)
                {
                    if (array_key_exists($column_name, $r))
                        $mapped_row[$column_name] = $r[$column_name];
                         else
                        $mapped_row[$column_name] = null;
                }
                 else
                {
                    $mapped_row[$column_name] = $r[$idx] ?? null;
                }
                $idx++;
            }

            $result[] = $mapped_row;
        }

        return $result;
    }
}




class StreamMemoryWriter implements \Flexio\Base\IStreamWriter
{
    private $stream;
    private $bytes_written;

    public function __construct()
    {
        $this->bytes_written = 0;
    }

    public static function create(\Flexio\Base\StreamMemory $stream) : \Flexio\Base\StreamMemoryWriter
    {
        $object = new static();
        $object->stream = $stream;
        return $object;
    }

    public function write($data)
    {
        if ($this->stream->is_table)
        {
            $this->writeArray($data);
        }
         else
        {
            $this->writeString($data);
        }
    }

    public function getBytesWritten() : int
    {
        return $this->bytes_written;
    }

    public function close() : bool
    {
        return true;
    }

    private function getStream() : \Flexio\Base\StreamMemory
    {
        return $this->stream;
    }

    private function writeString(string $data)
    {
        $bytes_written = 0;
        if (is_string($this->getStream()->buffer) === false) // initialize buffer
            $this->getStream()->buffer = '';

        $this->getStream()->buffer .= $data;
        $this->bytes_written += strlen($data);
        return true;
    }

    private function writeArray(array $data)
    {
        $bytes_written = 0;
        if (is_array($this->getStream()->buffer) === false) // initialize buffer
            $this->getStream()->buffer = array();

        array_push($this->getStream()->buffer, $data);

        // set the bytes written
        $content_str = implode('', $data);
        return true;
    }
}


class StreamMemory implements \Flexio\Base\IStream
{
    // data buffer; use reader/writer to access
    public $is_table = false;
    public $buffer = null;

    // properties
    private $properties;

    public function __construct()
    {
        $this->buffer = false;

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

    public static function create(array $properties = null) : \Flexio\Base\StreamMemory
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

    private function prepareStorage()
    {
        if (isset($this->properties['structure']) && is_array($this->properties['structure']) && count($this->properties['structure']) > 0)
            $this->is_table = true;
    }

    public function copy(\Flexio\Base\IStream $source) : \Flexio\Base\StreamMemory
    {
        // copies all the properties of another stream into the current stream,
        // including the buffer
        $sourceimpl = $source->getImpl();

        if (!($sourceimpl instanceof \Flexio\Base\StreamMemory))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED, "copy may only be used on stream objects of the same type");

        $this->buffer = $sourceimpl->buffer;

        $properties = $source->get();
        unset($properties['eid']);
        unset($properties['created']);
        unset($properties['updated']);
        $this->set($properties);

        return $this;
    }

    public function set(array $properties) : \Flexio\Base\StreamMemory
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

        $this->prepareStorage();
        return $this;
    }

    public function get() : array
    {
        return $this->properties;
    }

    public function setName(string $name) : \Flexio\Base\StreamMemory
    {
        $properties = array();
        $properties['name'] = $name;
        return $this->set($properties);
    }

    public function getName() : string
    {
        return $this->properties['name'];
    }

    public function setPath(string $path) : \Flexio\Base\StreamMemory
    {
        $properties = array();
        $properties['path'] = $path;
        return $this->set($properties);
    }

    public function getPath() : string
    {
        return $this->properties['path'];
    }

    public function setSize($size) : \Flexio\Base\StreamMemory // TODO: add input parameter types
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

    public function setMimeType(string $mime_type) : \Flexio\Base\StreamMemory
    {
        $properties = array();
        $properties['mime_type'] = $mime_type;
        return $this->set($properties);
    }

    public function getMimeType() : string
    {
        return $this->properties['mime_type'];
    }

    public function setStructure($structure) : \Flexio\Base\StreamMemory // TODO: add input parameter types
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

    public function getReader() : \Flexio\Base\IStreamReader
    {
        return \Flexio\Base\StreamMemoryReader::create($this);
    }

    public function getWriter() : \Flexio\Base\IStreamWriter
    {
        return \Flexio\Base\StreamMemoryWriter::create($this, true);
    }
}

