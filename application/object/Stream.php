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
 * @subpackage Object
 */


declare(strict_types=1);
namespace Flexio\Object;


class Stream extends \Flexio\Object\Base implements \Flexio\IFace\IObject, \Flexio\IFace\IStream
{
    public function __construct()
    {
    }

    public function __toString()
    {
        $object = array(
            'eid' => $this->getEid(),
            'eid_type' => $this->getType()
        );
        return json_encode($object);
    }

    public static function list(array $filter) : array
    {
        $object = new static();
        $stream_model = $object->getModel()->stream;
        $items = $stream_model->list($filter);

        $objects = array();
        foreach ($items as $i)
        {
            $o = new static();
            $o->properties =self::formatProperties($i);
            $o->setEid($o->properties['eid']);
            $objects[] = $o;
        }

        return $objects;
    }

    public static function load(string $eid) : \Flexio\Object\Stream
    {
        $object = new static();
        $stream_model = $object->getModel()->stream;
        $properties = $stream_model->get($eid);

        $object->setEid($eid);
        $object->clearCache();
        $object->properties = self::formatProperties($properties);
        return $object;
    }

    public static function create(array $properties = null) : \Flexio\Object\Stream
    {
        if (!isset($properties))
            $properties = array();

        // structure is stored as json string; it needs to be validated and encoded
        $structure = null;

        if (isset($properties) && isset($properties['structure']))
        {
            // if the structure is set, make sure it's valid
            $structure = $properties['structure'];
            $structure_object = \Flexio\Base\Structure::create($structure);
            $structure = $structure_object->enum();
            $properties['structure'] = json_encode($structure);
        }

        // default path
        if (!isset($properties['path']))
            $properties['path'] = \Flexio\Base\Util::generateHandle();

        $object = new static();
        $stream_model = $object->getModel()->stream;
        $local_eid = $stream_model->create($properties);

        $object->setEid($local_eid);
        $object->clearCache();

        return $object;
    }

    public function delete() : \Flexio\Object\Stream
    {
        $this->clearCache();
        $stream_model = $this->getModel()->stream;

        $stream_model->delete($this->getEid());
        return $this;
    }

    public function set(array $properties) : \Flexio\Object\Stream
    {
        // TODO: add properties check

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

        $this->clearCache();
        $stream_model = $this->getModel()->stream;
        $stream_model->set($this->getEid(), $properties);
        return $this;
    }

    public function get() : array
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties;
    }

    public function getType() : string
    {
        return \Model::TYPE_STREAM;
    }

    public function setOwner(string $user_eid) : \Flexio\Object\Stream
    {
        $properties = array('owned_by' => $user_eid);
        $this->set($properties);
        return $this;
    }

    public function getOwner() : string
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['owned_by']['eid'];
    }

    public function setStatus(string $status) : \Flexio\Object\Stream
    {
        if ($status === \Model::STATUS_DELETED)
            return $this->delete();

        $this->clearCache();
        $stream_model = $this->getModel()->stream;
        $result = $stream_model->set($this->getEid(), array('eid_status' => $status));
        return $this;
    }

    public function getStatus() : string
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['eid_status'];
    }

    public function setName(string $name) : \Flexio\Object\Stream
    {
        $properties = array();
        $properties['name'] = $name;
        return $this->set($properties);
    }

    public function getName() : string
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['name'];
    }

    public function setPath(string $path) : \Flexio\Object\Stream
    {
        $properties = array();
        $properties['path'] = $path;
        return $this->set($properties);
    }

    public function getPath() : string
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['path'];
    }

    public function setSize($size) : \Flexio\Object\Stream // TODO: add input parameter types
    {
        $properties = array();
        $properties['size'] = $size;
        return $this->set($properties);
    }

    public function getSize() : ?int
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['size'];
    }

    public function setMimeType(string $mime_type) : \Flexio\Object\Stream
    {
        $properties = array();
        $properties['mime_type'] = $mime_type;
        return $this->set($properties);
    }

    public function getMimeType() : string
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['mime_type'];
    }

    public function setStructure($structure) : \Flexio\Object\Stream // TODO: add input parameter types
    {
        if (!($structure instanceof \Flexio\Base\Structure))
            $structure = \Flexio\Base\Structure::create($structure);

        $properties = array();
        $properties['structure'] = $structure->enum();
        return $this->set($properties);
    }

    public function getStructure() : \Flexio\Base\Structure
    {
        if ($this->isCached() === false)
            $this->populateCache();

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

    private function isCached() : bool
    {
        if (!$this->properties)
            return false;

        return true;
    }

    private function clearCache() : void
    {
        $this->properties = null;
    }

    private function populateCache() : void
    {
        $stream_model = $this->getModel()->stream;
        $properties = $stream_model->get($this->getEid());
        $this->properties = self::formatProperties($properties);
    }

    private static function formatProperties(array $properties) : array
    {
        $mapped_properties = \Flexio\Base\Util::mapArray(
            [
                "eid" => null,
                "eid_type" => null,
                "eid_status" => null,
                "stream_type" => null,
                "parent_eid" => null,
                "connection_eid" => null,
                "name" => null,
                "path" => null,
                "size" => null,
                "hash" => null,
                "mime_type" => null,
                "structure" => null,
                "file_created" => null,
                "file_modified" => null,
                "expires" => null,
                "owned_by" => null,
                "created_by" => null,
                "created" => null,
                "updated" => null
            ],
        $properties);

        // sanity check: if the data record is missing, then eid will be null
        if (!isset($mapped_properties['eid']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // unpack the structure json
        $structure = @json_decode($mapped_properties['structure'],true);
        if ($structure !== false)
            $mapped_properties['structure'] = $structure;

        // expand the user info
        $mapped_properties['owned_by'] = array(
            'eid' => $properties['owned_by'],
            'eid_type' => \Model::TYPE_USER
        );
        $mapped_properties['created_by'] = array(
            'eid' => $properties['created_by'],
            'eid_type' => \Model::TYPE_USER
        );

        return $mapped_properties;
    }
}

