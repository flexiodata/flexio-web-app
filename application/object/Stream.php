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


require_once dirname(__DIR__) . '/base/Abstract.php';

class Stream extends \Flexio\Object\Base implements \Flexio\Base\IStream
{
    // TODO: add tests for these constants
    const TYPE_DIRECTORY = 'SD';
    const TYPE_FILE = 'SF';

    public function __construct()
    {
        $this->setType(\Model::TYPE_STREAM);
    }

    public function getImpl() { return $this; }

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
        $model = $object->getModel();
        $local_eid = $model->create($object->getType(), $properties);

        $object->setEid($local_eid);
        $object->clearCache();


        // create empty store file
        $create_params = [];
        if (isset($structure) && count($structure) > 0)
        {
            $create_params['structure'] = $structure;
        }

        $storagefs = $object->getStorageFs();
        $storagefs->createFile($properties['path'], $create_params);

        return $object;
    }

    // copies a streams properties to $dest, overwriting $dest's properties
    public function copy(\Flexio\Base\IStream $source)
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
            $object->set($a);
            $results[] = $object;
        }

        return $results;
    }

    public function getReader() : \Flexio\Base\IStreamReader
    {
        //return \Flexio\Object\StreamReader::create($this);
        $storagefs = $this->getStorageFs();

        $file = $storagefs->open($this->getPath());
        return $file->getReader();
    }

    public function getWriter() : \Flexio\Base\IStreamWriter
    {
        //return \Flexio\Object\StreamWriter::create($this, true);
        $storagefs = $this->getStorageFs();

        $file = $storagefs->open($this->getPath());
        return $file->getWriter();
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
        if ($properties === false)
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

