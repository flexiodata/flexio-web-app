<?php
/**
 *
 * Copyright (c) 2020, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2020-05-26
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class Resource implements \Flexio\IFace\IStream
{
    private $handle = null;        // resource handle
    private $properties = array(); // resource properties

    public function __construct()
    {
        $this->buffer = null;

        // note: default values match model defaults
        $this->properties = array();
        $this->properties['name'] = '';
        $this->properties['path'] = '';
        $this->properties['hash'] = '';
        $this->properties['mime_type'] = '';
        $this->properties['structure'] = array();
        $this->properties['file_created'] = null;
        $this->properties['file_modified'] = null;
        $this->properties['created'] = null;
        $this->properties['updated'] = null;
    }

    public static function create(array $properties = null) : \Flexio\Base\Resource
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

    public function set(array $properties) : \Flexio\Base\Resource
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
        $properties = $this->properties;
        $properties['size'] = $this->getSize();
        return $properties;
    }

    public function setName(string $name) : \Flexio\Base\Resource
    {
        $properties = array();
        $properties['name'] = $name;
        return $this->set($properties);
    }

    public function getName() : string
    {
        return $this->properties['name'];
    }

    public function setPath(string $path) : \Flexio\Base\Resource
    {
        $properties = array();
        $properties['path'] = $path;
        return $this->set($properties);
    }

    public function getPath() : string
    {
        return $this->properties['path'];
    }

    public function setSize($size) : \Flexio\Base\Resource // TODO: add input parameter types
    {
        // note: size is determined by buffer
        return $this;
    }

    public function getSize() : ?int
    {
        if (!isset($this->handle))
            return null;

        $stats = fstat($this->handle());
        return $stats['size'];
    }

    public function setMimeType(string $mime_type) : \Flexio\Base\Resource
    {
        $properties = array();
        $properties['mime_type'] = $mime_type;
        return $this->set($properties);
    }

    public function getMimeType() : string
    {
        return $this->properties['mime_type'];
    }

    public function setStructure($structure) : \Flexio\Base\Resource // TODO: add input parameter types
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
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function getWriter($access = 'w+') : \Flexio\IFace\IStreamWriter
    {
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function read(int $size = 2048)
    {
        if (!isset($this->handle))
            $this->open();

        if (!isset($this->handle))
            return false;

        if ($size <= 0)
            return '';

        return fread($this->handle, $size);
    }

    public function readline(int $size = null)
    {
        if (!isset($this->handle))
            $this->open();

        if (!isset($this->handle))
            return false;

        if (!isset($size))
            return fgets($this->handle);
             else
            return fgets($this->handle, $size);
    }

    public function write(string $data)
    {
        if (!isset($this->handle))
            $this->open();

        if (!isset($this->handle))
            return false;

        return fwrite($this->handle, $data);
    }

    public function rewind() : bool
    {
        if (!isset($this->handle))
            return false;

        return rewind($this->handle);
    }

    public function close() : void
    {
        if (!isset($this->handle))
            return;

        fclose($this->handle);
        $this->handle = null;
    }

    private function open() : void
    {
        $this->handle = fopen('php://memory','rw+');
    }
}

