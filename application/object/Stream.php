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


require_once dirname(__DIR__) . '/services/Abstract.php';

class Stream extends \Flexio\Object\Base
{
    public function __construct()
    {
        $this->setType(\Model::TYPE_STREAM);
    }

    public static function create(array $properties = null) : \Flexio\Object\Stream
    {
        // structure is stored as json string; it needs to be validated
        // and encoded
        if (isset($properties) && isset($properties['structure']))
        {
            // if the structure is set, make sure it's valid
            $structure = $properties['structure'];
            $structure_object = \Flexio\Object\Structure::create($structure);
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

            $default_connection_eid = \Flexio\Object\Connection::getDatastoreConnectionEid();
            if (\Flexio\Base\Eid::isValid($default_connection_eid))
                $properties['connection_eid'] = $default_connection_eid;
        }

        $object = new static();
        $model = \Flexio\Object\Store::getModel();
        $local_eid = $model->create($object->getType(), $properties);

        $object->setModel($model);
        $object->setEid($local_eid);
        $object->setRights();
        $object->clearCache();
        return $object;
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
            $structure_object = \Flexio\Object\Structure::create($structure);
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
        if (!($structure instanceof \Flexio\Object\Structure))
            $structure = \Flexio\Object\Structure::create($structure);

        $properties = array();
        $properties['structure'] = $structure->enum();
        return $this->set($properties);
    }

    public function getStructure() : \Flexio\Object\Structure
    {
        if ($this->isCached() === false)
            $this->populateCache();

        $s = $this->properties['structure'];
        $structure = \Flexio\Object\Structure::create($s);
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

    public function content(int $start, int $limit, array $columns = null, bool $metadata = false) // TODO: add function return type
    {
        // returns the requested content for the given stream

        if ($this->isCached() === false)
            $this->populateCache();

        $local_properties = $this->properties;
        $mime_type = $local_properties['mime_type'];
        $connection_eid = $local_properties['connection_eid'];
        $path = $local_properties['path'];

        $connection = \Flexio\Object\Connection::load($connection_eid);
        if ($connection === false)
            return false;

        $service = $connection->getService();
        if (!$service)
            return false;

        $start = (int)$start;
        $limit = (int)$limit;
        if ($start < 0 )
            $start = 0;
        if ($limit < 0)
            $limit = 0;
        $metadata = \toBoolean($metadata);

        // if a list of columns is specified, get the list
        $columns_specified = false;
        if (isset($columns))
            $columns_specified = array_flip(array_values($columns));

        // get the structure for the output; if columns are specified, only
        // include the columns in the list
        $output_structure = array();

        $structure = $this->getStructure()->enum();
        foreach ($structure as $col)
        {
            $column_name = $col['name'];
            if ($columns_specified !== false)
            {
                if (array_key_exists($column_name, $columns_specified) === false)
                    continue;
            }

            $output_structure[] = $col;
        }

        if ($mime_type !== \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE)
        {
            $handle = $service->openFile($path);

            if (!$handle)
                return false;

            if ($start > 0)
                $handle->seek($start);

            $content = $handle->read($limit);
            return $content;
        }

        $iter = $service->query(array('table' => $path));
        if (!$iter)
            return false;

        $content = $iter->getRows($start, $limit);

        $result = array();
        $result['success'] = true;
        $result['total_count'] = $iter->row_count;
        if (IS_DEBUG())
        {
            $result['table'] = $path;
        }

        if ($metadata === true)
            $result['columns'] = $output_structure;

        $result['rows'] = array();
        foreach ($content as $row)
        {
            // map the data from the service that's stored using the store_name
            // back to the object structure that references the data with the
            // logical name
            $result['rows'][] = self::convertStoreNameToName($row, $output_structure);
        }

        return $result;
    }

    public function getService()
    {
        $stream_info = $this->get();
        $connection_eid = $stream_info['connection_eid'];

        $connection = \Flexio\Object\Connection::load($connection_eid);
        if ($connection === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        return $connection->getService();
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

