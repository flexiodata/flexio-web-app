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
        $buffer = '';
    }

    public static function create(array $properties = null) : \Flexio\Object\StreamMemory
    {
        // structure is stored as json string; it needs to be validated
        // and encoded
        if (isset($properties) && isset($properties['structure']))
        {
            // if the structure is set, make sure it's valid
            $structure = $properties['structure'];
            $structure_object = \Flexio\Base\Structure::create($structure);
            $structure = $structure_object->enum();
            $properties['structure'] = json_encode($structure);
        }

        // if a connection isn't specified, add a default connection to
        // the datastore
        if (!isset($properties['connection_eid']))
        {
            // default path
            if (!isset($properties['path']))
                $properties['path'] = \Flexio\Base\Util::generateHandle();

            $properties['connection_eid'] = false;
        }

        $object = new static();
        $object->properties = $properties;
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
            $structure = $structure_object->enum();
            $this->properties['structure'] = json_encode($structure);
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

    public function content(int $start = 0, int $limit = PHP_INT_MAX, int $readsize = 1024 /* testing */) // TODO: add function return type
    {
        $streamreader = $this->getReader();
        return $streamreader->getContent($start, $limit, $readsize);
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
}

