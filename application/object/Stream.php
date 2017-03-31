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

    ////////////////////////////////////////////////////////////
    // IObject interface
    ////////////////////////////////////////////////////////////

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

    public function get()
    {
        if ($this->isCached() === true)
            return $this->properties;

        if ($this->populateCache() === true)
            return $this->properties;

        return false;
    }



    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    public function setName(string $name) : \Flexio\Object\Stream
    {
        $properties = array();
        $properties['name'] = $name;
        return $this->set($properties);
    }

    public function getName()
    {
        if ($this->isCached() === false)
            $this->populateCache();

        if ($this->isCached() === false)
            return false;

        return $this->properties['name'];
    }

    public function setPath(string $path) : \Flexio\Object\Stream
    {
        $properties = array();
        $properties['path'] = $path;
        return $this->set($properties);
    }

    public function getPath()
    {
        if ($this->isCached() === false)
            $this->populateCache();

        if ($this->isCached() === false)
            return false;

        return $this->properties['path'];
    }

    public function setSize($size) : \Flexio\Object\Stream // TODO: add input parameter types
    {
        $properties = array();
        $properties['size'] = $size;
        return $this->set($properties);
    }

    public function getSize()
    {
        $local_file_info = $this->getFileInfo();
        if ($local_file_info === false)
            return false;

        return $local_file_info['size'];
    }

    public function setMimeType(string $mime_type) : \Flexio\Object\Stream
    {
        $properties = array();
        $properties['mime_type'] = $mime_type;
        return $this->set($properties);
    }

    public function getMimeType()
    {
        if ($this->isCached() === false)
            $this->populateCache();

        if ($this->isCached() === false)
            return false;

        return $this->properties['mime_type'];
    }

    public function setStructure($structure) : \Flexio\Object\Stream // TODO: add input parameter types
    {
        if (!($structure instanceof \Flexio\Object\Structure))
            $structure = \Flexio\Object\Structure::create($structure);

        if ($structure === false)
            return $this;

        $properties = array();
        $properties['structure'] = $structure->enum();
        return $this->set($properties);
    }

    public function getStructure()
    {
        if ($this->isCached() === false)
            $this->populateCache();

        if ($this->isCached() === false)
            return false;

        $s = $this->properties['structure'];
        $structure = \Flexio\Object\Structure::create($s);
        return $structure;
    }

    public function getFileInfo()
    {
        if ($this->isCached() === false)
            $this->populateCache();

        if ($this->isCached() === false)
            return false;

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



    ////////////////////////////////////////////////////////////
    // read/write functions
    ////////////////////////////////////////////////////////////

    public function read($callback) // TODO: add input parameter types
    {
        $service = $this->getService();
        if ($service === false)
            return;

        $is_internal_table = false;
        if ($this->getMimeType() === \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE)
            $is_internal_table = true;
        $structure = $this->getStructure()->enum();

        $params = array();
        $params['path'] = $this->getPath();
        $service->read($params, function ($data) use (&$callback, &$is_internal_table, $structure) {

            // if we have an internal table, convert the data keys from
            // the store_name to the application name

            $data_to_write = false;
            if ($is_internal_table !== true)
            {
                $data_to_write = $data;
            }
             else
            {
                $data_to_write = array();
                foreach ($structure as $col)
                    $data_to_write[$col['name']] = $data[$col['store_name']] ?? null;
            }

            $callback($data_to_write);
        });
    }

    public function write($data) // TODO: add input parameter types
    {
        // TODO: make sure the output table

        $service = $this->getService();
        if ($service === false)
            return;

        $structure = $this->getStructure()->enum();
        $data_to_write = false;

        if ($this->getMimeType() !== \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE)
        {
            $data_to_write = $data;
        }
            else
        {
            $data_to_write = array();
            foreach ($structure as $col)
                $data_to_write[$col['name']] = $data[$col['store_name']] ?? null;
        }

        // TODO: this looks like a problem, since the service write function takes
        // parameters and a callback; is this legacy and unused?
        $service->write($data_to_write);
    }

    public function writePostContent()
    {
    }

    public function content($start, $limit, $columns = true, $metadata = false, $handle = 'create') // TODO: add input parameter types
    {
        // returns the requested content for the given stream

        if ($this->isCached() === false)
            $this->populateCache();

        if ($this->isCached() === false)
            return false;

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
        if (is_array($columns))
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

        $iter = false;
        if ($handle !== 'create')
        {
            $iter = $service->getIteratorFromHandle($handle);
            if (!$iter)
                return false;
        }
         else
        {
            $iter = $service->query(array('table' => $path));
            if (!$iter)
                return false;

            $handle = $iter->getHandle();
        }

        $iter = $service->query(array('table' => $path));
        if (!$iter)
            return false;

        $content = $iter->getRows($start, $limit);

        $result = array();
        $result['success'] = true;
        $result['handle'] = $handle;
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

    public function getConnection()
    {
        $stream_info = $this->get();
        $connection_eid = $stream_info['connection_eid'];

        $connection = \Flexio\Object\Connection::load($connection_eid);
        if ($connection === false)
            return false;

        return $connection;
    }

    public function getService()
    {
        $connection = $this->getConnection();
        if ($connection === false)
            return false;

        $service = $connection->getService();
        if (!$service)
            return false;

        return $service;
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
        if ($local_properties === false)
            return false;

        // save the properties
        $this->properties = $local_properties;
        $this->eid_status = $local_properties['eid_status'];
        return true;
    }

    private function getProperties()
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
            return false;

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

