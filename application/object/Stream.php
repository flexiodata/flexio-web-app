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


require_once dirname(__DIR__) . '/base/StreamMemory.php';


class Stream extends \Flexio\Object\Base implements \Flexio\Base\IStream
{
    public function __construct()
    {
        $this->setType(\Model::TYPE_STREAM);
    }

    public function getImpl() { return $this; }

    public static function create(array $properties = null) : \Flexio\Object\Stream
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

        $object = new static();
        $model = $object->getModel();
        $local_eid = $model->create($object->getType(), $properties);

        $object->setEid($local_eid);
        $object->clearCache();


        // create empty store file
        $storagefs = $object->getStorageFs();
        $storagefs->createFile($properties['path']);


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

    public function content(int $start = 0, int $limit = PHP_INT_MAX, int $readsize = 1024 /* testing */) // TODO: add function return type
    {
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

    public function getReader() : \Flexio\Base\IStreamReader
    {
        //return \Flexio\Object\StreamReader::create($this);
        $storagefs = $this->getStorageFs();

        return $storagefs->open($this->getPath());
    }

    public function getWriter() : \Flexio\Base\IStreamWriter
    {
        //return \Flexio\Object\StreamWriter::create($this, true);
        $storagefs = $this->getStorageFs();

        $file = $storagefs->open($this->getPath());
        return $file;
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
            "name" : null,
            "path" : null,
            "size" : null,
            "hash" : null,
            "mime_type" : null,
            "structure" : null,
            "file_created" : null,
            "file_modified" : null,
            "connection_eid" :  null,
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

