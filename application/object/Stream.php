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


class Stream extends \Flexio\Object\Base implements \Flexio\IFace\IObject, \Flexio\IFace\IStream
{
    // TODO: add tests for these constants
    const TYPE_DIRECTORY = 'SD';
    const TYPE_FILE = 'SF';

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

    public static function load(string $eid) : \Flexio\Object\Stream
    {
        $object = new static();
        $stream_model = $object->getModel()->stream;

        $status = $stream_model->getStatus($eid);
        if ($status === \Model::STATUS_UNDEFINED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // TODO: for now, don't allow objects that have been deleted
        // to be loaded; in general, we may want to move this to the
        // api layer, but previously, it's been in the model layer,
        // and we need to make sure the behavior is the same after the
        // model constraint is removed, and object loading is a good
        // location for this constraint
        if ($status == \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        $object->setEid($eid);
        $object->clearCache();
        return $object;
    }

    public static function create(array $properties = null) : \Flexio\Object\Stream
    {
        if (is_null($properties))
        {
            $properties = [];
        }

        // structure is stored as json string; it needs to be validated
        // and encoded
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

    // copies a streams properties to $dest, overwriting $dest's properties
    public function copyFrom(\Flexio\IFace\IStream $source)
    {
        $sourceimpl = $source->getImpl();

        if (!($sourceimpl instanceof \Flexio\Object\Stream))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED, "copy may only be used on stream objects of the same type");

        $properties = $source->get();
        unset($properties['eid']);
        unset($properties['created']);
        unset($properties['updated']);
        $this->set($properties);
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
        $stream_model = $this->getModel()->stream;
        return $stream_model->getOwner($this->getEid());
    }

    public function setStatus(string $status) : \Flexio\Object\Stream
    {
        $this->clearCache();
        $stream_model = $this->getModel()->stream;
        $result = $stream_model->setStatus($this->getEid(), $status);
        return $this;
    }

    public function getStatus() : string
    {
        if ($this->eid_status !== false)
            return $this->eid_status;

        $stream_model = $this->getModel()->stream;
        $status = $stream_model->getStatus($this->getEid());
        $this->eid_status = $status;

        return $status;
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

    public function getSize() // TODO: add return type (size can be null)
    {
        $local_file_info = $this->getFileInfo();
        return $local_file_info['size'];
    }

    public function getRowCount() : int
    {
        $reader = $this->getReader();
        return $reader->getRowCount();
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

    public function isTable() : bool
    {
        if ($this->isCached() === false)
            $this->populateCache();
        return count($this->properties['structure']) > 0 ? true : false;
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

    public function getChildStreams($name = null) : array
    {
        // return an array of \Flexio\Object\Stream objects that have a
        // parent_eid = this object's eid;
        // if name is specified, the result set will be additionally filtered with name

        $where = [ 'parent_eid' => $this->getEid() ];
        if (!is_null($name))
            $where['name'] = $name;

        $arr = $this->getModel()->stream->queryStreams($where);

        $results = [];
        foreach ($arr as $a)
        {
            $object = new static();
            $object->properties = $a;
            $structure = @json_decode($a['structure'],true);
            if (is_null($structure))
                $structure = [];
            $object->properties['structure'] = $structure;
            $object->setEid($a['eid']);
            $results[] = $object;
        }

        return $results;
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

    public function getWriter() : \Flexio\IFace\IStreamWriter
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
            return $file->getWriter();
        }
        catch (\Flexio\Base\Exception $e)
        {
            if ($e->getCode() == \Flexio\Base\Error::NOT_FOUND)
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
        if ($this->properties === false)
            return false;

        return true;
    }

    private function clearCache() : bool
    {
        $this->eid_status = false;
        $this->properties = false;
        return true;
    }

    private function populateCache() : bool
    {
        $local_properties = $this->getProperties();
        $this->properties = $local_properties;
        $this->eid_status = $local_properties['eid_status'];
        return true;
    }

    private $storagefs = null;
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
                if (!is_writable($storage_root . DIRECTORY_SEPARATOR . 'streams'))
                {
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS, "Cannot write to streams store directory");
                }
            }

            $this->storagefs = \Flexio\Services\StorageFs::create(['base_path' => $storage_root . DIRECTORY_SEPARATOR . 'streams']);
        }

        return $this->storagefs;
    }

    private function getProperties() : array
    {
        $query = '
        {
            "eid" : null,
            "eid_type" : "'.\Model::TYPE_STREAM.'",
            "eid_status" : null,
            "stream_type" : null,
            "parent_eid" : null,
            "name" : null,
            "path" : null,
            "size" : null,
            "hash" : null,
            "mime_type" : null,
            "structure" : null,
            "file_created" : null,
            "file_modified" : null,
            "connection_eid" :  null,
            "expires" : null,
            "created" : null,
            "updated" : null
        }
        ';

        $query = json_decode($query);
        $properties = \Flexio\Object\Query::exec($this->getEid(), $query);

        // sanity check: if the data record is missing, then eid will be null
        if (!$properties || ($properties['eid'] ?? null) === null)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // unpack the structure json
        $structure = @json_decode($properties['structure'],true);
        if ($structure !== false)
            $properties['structure'] = $structure;

        return $properties;
    }

    private static function convertStoreNameToName(array $row, array $structure) : array
    {
        $mapped_row = array();
        foreach ($structure as $col)
            $mapped_row[$col['name']] = $row[$col['store_name']] ?? null;

        return $mapped_row;
    }

    private static function convertNameToStoreName(array $row, array $structure) : array
    {
        $mapped_row = array();
        foreach ($structure as $col)
            $mapped_row[$col['store_name']] = $row[$col['name']] ?? null;

        return $mapped_row;
    }
}

