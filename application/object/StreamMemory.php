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
 * @subpackage Object
 */


declare(strict_types=1);
namespace Flexio\Object;


require_once dirname(__DIR__) . '/object/Abstract.php';
require_once dirname(__DIR__) . '/object/StreamReader.php';
require_once dirname(__DIR__) . '/object/StreamWriter.php';


class StreamMemory implements \Flexio\Object\IStream
{
    // data buffer; use reader/writer to access
    public $buffer;

    // properties
    private $properties;

    public function __construct()
    {
        $this->buffer = false;

        $this->properties = array();
        $this->properties['name'] = null;
        $this->properties['path'] = null;
        $this->properties['size'] = null;
        $this->properties['hash'] = null;
        $this->properties['mime_type'] = null;
        $this->properties['structure'] = array();
        $this->properties['file_created'] = null;
        $this->properties['file_modified'] = null;
        $this->properties['connection_eid'] = null;
        $this->properties['created'] = '';
        $this->properties['updated'] = '';
    }

    public static function create(array $properties = null) : \Flexio\Object\StreamMemory
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

        return $object;
    }

    public function copy() : \Flexio\Object\StreamMemory
    {
        $object = new static();
        $object->properties = $this->properties;
        $object->buffer = $this->buffer;
        return $object;
    }

    public function set(array $properties) : \Flexio\Object\StreamMemory
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

    public function setName(string $name) : \Flexio\Object\StreamMemory
    {
        $properties = array();
        $properties['name'] = $name;
        return $this->set($properties);
    }

    public function getName() : string
    {
        return $this->properties['name'];
    }

    public function setPath(string $path) : \Flexio\Object\StreamMemory
    {
        $properties = array();
        $properties['path'] = $path;
        return $this->set($properties);
    }

    public function getPath() : string
    {
        return $this->properties['path'];
    }

    public function setSize($size) : \Flexio\Object\StreamMemory // TODO: add input parameter types
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

    public function setMimeType(string $mime_type) : \Flexio\Object\StreamMemory
    {
        $properties = array();
        $properties['mime_type'] = $mime_type;
        return $this->set($properties);
    }

    public function getMimeType() : string
    {
        return $this->properties['mime_type'];
    }

    public function setStructure($structure) : \Flexio\Object\StreamMemory // TODO: add input parameter types
    {
        if (!($structure instanceof \Flexio\Base\Structure))
            $structure = \Flexio\Base\Structure::create($structure);

        $properties = array();
        $properties['structure'] = $structure;
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

    public function content(int $start = 0, int $limit = PHP_INT_MAX, int $readsize = 1024 /* testing */) // TODO: add function return type
    {
        // TODO: this implementation parallels the implementation of the storage-based stream object,
        // giving us consistent behavior; however, we could simplify this implementation to work
        // directly on the buffer, although we don't know how much it would help efficiency since

        if ($start < 0 )
            $start = 0;
        if ($limit < 0)
            $limit = 0;
        if ($readsize <= 0)
            $readsize = 1;

        $mime_type = $this->getMimeType();
        if ($mime_type === \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE)
        {
            return $this->getReader()->getRows($start,$limit);
        }
        else
        {
            // read table content
            $offset1 = 0;
            $offset2 = 0;

            // the starting and ending position we want
            $range1 = $start;
            $range2 = $start + $limit;

            $result = '';
            $reader = $this->getReader();

            while (true)
            {
                $chunk = $reader->read($readsize);
                if ($chunk === false)
                    break;

                $offset2 = $offset1 + strlen($chunk);

                // if we haven't reached the part we want, keep reading
                if ($offset2 < $range1)
                {
                    $offset1 = $offset2;
                    continue;
                }

                // if we're past the part we want, we're done
                if ($offset1 > $range2)
                    break;

                // case 1: chunk read is contained entirely in the range we want
                if ($offset1 >= $range1 && $offset2 <= $range2)
                    $result .= $chunk;

                // case 2: chunk read covers the range we want
                if ($offset1 < $range1 && $offset2 > $range2)
                    $result .= substr($chunk, $range1 - $offset1, $range2 - $range1);

                // case 3: chunk read covers first part of the range we want
                if ($offset1 < $range1 && $offset2 <= $range2)
                    $result .= substr($chunk, $range1 - $offset1);

                // case 4: chunk read covers second part of the range we want
                if ($offset1 >= $range1 && $offset2 > $range2)
                    $result .= substr($chunk, 0, $range2 - $offset1);

                // set the new starting offset position
                $offset1 = $offset2;
            }

            return $result;
        }
    }

    // copies a streams properties to $dest, overwriting $dest's properties
    public function copyOver(\Flexio\Object\IStream $dest)
    {
        $properties = $this->get();
        unset($properties['eid']);
        unset($properties['created']);
        unset($properties['updated']);
        $dest->set($properties);
    }

    public function getReader() : \Flexio\Object\IStreamReader
    {
        return \Flexio\Object\StreamMemoryReader::create($this);
    }

    public function getWriter() : \Flexio\Object\IStreamWriter
    {
        return \Flexio\Object\StreamMemoryWriter::create($this, true);
    }

    public function bufferToString()
    {
        if ($this->buffer === false)
            return false;

        // returns the buffer as a string that's safe for storing
        if ($this->getMimeType() === \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE)
        {
            $buffer = json_encode($this->buffer);
            return base64_encode($buffer);
        }
         else
        {
            return base64_encode($this->buffer);
        }
    }

    public function bufferFromString(string $str)
    {
        // restores the buffer from a string
        if ($this->getMimeType() === \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE)
        {
            $buffer = base64_decode($str);
            $temp = json_decode($buffer, true);
            if ($temp === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
            $this->buffer = $temp;
        }
         else
        {
            $this->buffer = base64_decode($str);
        }
    }
}

