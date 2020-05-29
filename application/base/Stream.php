<?php
/**
 *
 * Copyright (c) 2016, Flex Research LLC. All rights reserved.
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
        if ($this->offset >= strlen($this->stream->buffer))
            return false;

        $str = substr($this->stream->buffer, $this->offset, $length);
        if ($str === false)
            return false;
        $this->offset += strlen($str);
        return $str;
    }

    public function readRow()
    {
        $npos = strpos($this->stream->buffer, "\n", $this->offset);

        if ($npos === false)
        {
            return $this->read(strlen($this->stream->buffer));
        }
            else
        {
            $npos -= $this->offset;
            $line = $this->read($npos+1);
            if ($line === false)
                return false;
            return rtrim($line, "\r\n");
        }
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
        if (!is_string($data))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, 'Expected string value');

        $this->stream->buffer .= $data;
        $this->bytes_written += strlen($data);

        return true;
    }

    public function getBytesWritten() : int
    {
        return $this->bytes_written;
    }

    public function close() : bool
    {
        return true;
    }
}


class Stream implements \Flexio\IFace\IStream
{
    public $buffer = '';             // data buffer; use reader/writer to access

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
        $this->properties['created'] = null;
        $this->properties['updated'] = null;
    }

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

        return $object;
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

    public function getSize() : ?int
    {
        return $this->properties['size'];
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

    public function getReader() : \Flexio\IFace\IStreamReader
    {
        return \Flexio\Base\StreamReader::create($this);
    }

    public function getWriter($access = 'w+') : \Flexio\IFace\IStreamWriter
    {
        if ($access == 'w+' || $access == 'w')
            $this->buffer = '';

        return \Flexio\Base\StreamWriter::create($this);
    }
}

