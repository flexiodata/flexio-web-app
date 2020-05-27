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
    // TODO: add tests for these constants
    public const TYPE_DIRECTORY = 'SD';
    public const TYPE_FILE = 'SF';

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

    public function getImpl() { return $this; }

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

        // default stream type
        if (!isset($properties['stream_type']))
            $properties['stream_type'] = \Flexio\Object\Stream::TYPE_FILE;

        $object = new static();
        $stream_model = $object->getModel()->stream;
        $local_eid = $stream_model->create($properties);

        $object->setEid($local_eid);
        $object->clearCache();


        // create empty store file
        $create_params = [];
        if (isset($structure) && count($structure) > 0)
        {
            $create_params['structure'] = $structure;
        }

        if (strlen($properties['path']) > 0)
        {
            $storagefs = $object->getStorageFs();
            $storagefs->createFile($properties['path'], $create_params);
        }

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
        $old_path = $this->getPath();

        $storagefs = $this->getStorageFs();
        if ($storagefs->exists($old_path))
        {
            $storagefs->move($old_path, $path);
        }

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
        $local_file_info = $this->getFileInfo();
        return $local_file_info['size'];
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

    public function getFileInfo() : array
    {
        if ($this->isCached() === false)
            $this->populateCache();

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

    public function getChildStreams(string $name = null) : array
    {
        // return an array of \Flexio\Object\Stream objects that have a
        // parent_eid = this object's eid;
        // if name is specified, the result set will be additionally filtered with name

        // note this function will only return non-deleted child streams; that's what the query for eid_status does

        $where = [ 'parent_eid' => $this->getEid(), 'eid_status' => \Model::STATUS_AVAILABLE ];
        if (!is_null($name))
            $where['name'] = $name;

        $arr = self::list($where);
        return $arr;
    }

    public function getReader() : \Flexio\IFace\IStreamReader
    {
        if ($this->isCached() === false)
            $this->populateCache();

        //return \Flexio\Object\StreamReader::create($this);
        $storagefs = $this->getStorageFs();

        // pass along structure
        $props = [];
        if (count($this->properties['structure']) > 0)
            $props['structure'] = $this->properties['structure'];

        $file = $storagefs->open($this->getPath(), $props);
        return $file->getReader();
    }

    public function getWriter($access = 'w+') : \Flexio\IFace\IStreamWriter
    {
        if ($this->isCached() === false)
            $this->populateCache();

        //return \Flexio\Object\StreamWriter::create($this, true);
        $storagefs = $this->getStorageFs();

        // pass along structure
        $props = [];
        $structure = $this->getStructure()->enum();
        if (count($structure) > 0)
        {
            $props['structure'] = $structure;
        }

        try
        {
            $file = $storagefs->open($this->getPath(), $props);
            return $file->getWriter($access);
        }
        catch (\Flexio\Base\Exception $e)
        {
            if ($e->getCode() == \Flexio\Base\Error::UNAVAILABLE)
            {
                // underlying data file is missing -- recreate
                if (strlen($this->properties['path']) > 0)
                {
                    $create_params = [];
                    if (isset($this->properties['structure']) && count($this->properties['structure']) > 0)
                    {
                        $create_params['structure'] = $this->properties['structure'];
                    }

                    $file = $storagefs->createFile($this->getPath(), $create_params);
                    return $file->getWriter();
                }
            }

            throw $e;
        }
    }

    public function getInserter() : \Flexio\IFace\IStreamWriter
    {
        if ($this->isCached() === false)
            $this->populateCache();

        //return \Flexio\Object\StreamWriter::create($this, true);
        $storagefs = $this->getStorageFs();

        // pass along structure
        $props = [];
        $structure = $this->getStructure()->enum();
        if (count($structure) > 0)
        {
            $props['structure'] = $structure;
        }

        $file = $storagefs->open($this->getPath(), $props);
        return $file->getInserter();
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

    private $storagefs = null;
    private function getStorageFs() : \Flexio\Services\StorageFs
    {
        if ($this->storagefs === null)
        {
            $storage_stream_path = \Flexio\System\System::getStoreStreamPath();
            $this->storagefs = \Flexio\Services\StorageFs::create(['base_path' => $storage_stream_path]);
        }

        return $this->storagefs;
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

