<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-09-12
 *
 * @package flexio
 * @subpackage Object
 */


namespace Flexio\Object;


class StreamWriter
{
    private $stream_info = false;
    private $service = false;
    private $table_inserter = false;
    private $file_inserter = false;
    private $datastore_mode = false;
    private $bytes_written = false; // total bytes written


    function __construct()
    {
    }

    function __destruct()
    {
        $this->close();
    }

    public static function create($stream, $datastore_mode = true)
    {
        // TODO: StreamWriter is designed to work right now with database services;
        // the function calls rely on specific service functions rather than the service
        // interface, so the reader will won't work on some type of services

        // the stream parameter can either be a stream or stream properties;
        // the key information we need from the stream are the path, mime_type,
        // and structure for the source

        $object = new self;

        if (($stream instanceof \Flexio\Object\Stream))
            $object->stream_info = $stream->get();

        if (is_array($stream))
        {
            $object->stream_info = array();
            $object->stream_info['connection_eid'] = isset_or($stream['connection_eid'], false);
            $object->stream_info['path'] = isset_or($stream['path'], false);
            $object->stream_info['mime_type'] = isset_or($stream['mime_type'], false);
            $object->stream_info['structure'] = isset_or($stream['structure'], false);
        }

        // write out data using datastore conventions by default (included a
        // row identifier and use store_name, a safe fieldname convention, for
        // the fieldnames)
        $object->setDatastoreMode($datastore_mode);

        // create the table, check the service, etc
        $object->init();
        if ($object->isOk() === false)
            return false;

        return $object;
    }

    public function init()
    {
        $this->close();

        $this->initService();
        if ($this->isOk() === false)
            return false;

        // TODO: check for existence of table/file; open it if it
        // already exists, but make sure the basic mime type matches
        // (e.g. table or not table)

        $mime_type = $this->getMimeType();
        if ($mime_type === \ContentType::MIME_TYPE_FLEXIO_TABLE)
            return $this->createTable();
             else
            return $this->createFile();
    }

    public function write($data)
    {
        if ($this->isOk() === false)
            return false;

        // data being written should be either a string or an array;
        // get a representative string of the content so we can find
        // out the size of the data that was written
        if ($this->bytes_written === false)
            $this->bytes_written = 0;

        if (is_string($data))
            $content_str = $data;
        if (is_array($data))
            $content_str = implode('', $data);

        // increment the total bytes written
        $this->bytes_written += strlen($content_str);

        // write the data
        $mime_type = $this->getMimeType();
        if ($mime_type === \ContentType::MIME_TYPE_FLEXIO_TABLE)
            return $this->writeToTable($data);
             else
            return $this->writeToFile($data);
    }

    public function getBytesWritten()
    {
        // returns the total amount written
        return $this->bytes_written;
    }

    public function close()
    {
        // note: don't reset the stream item; this is
        // used to be able to reinitialized the other
        // members

        if ($this->service !== false)
            $this->service->close();

        if ($this->table_inserter !== false)
            $this->closeTable();

        if ($this->file_inserter !== false)
            $this->closeFile();

        $this->service = false;
        $this->table_inserter = false;
        $this->file_inserter = false;
    }

    private function createTable()
    {
        // make sure we have a path and a structure
        $path = $this->getPath();
        if ($path === false)
            return false;

        $structure = $this->getStructure();
        if ($structure === false)
            return false;

        $store_structure = array();
        if ($this->getDatastoreMode() === true)
        {
            $store_structure[] = array(
                'name' => 'xdrowid',
                'type' => 'bigserial',
                'width' => 18,
                'scale' => 0
            );
        }

        // add on the columns from the structure, using either the values
        // stored in the name or store_name as the fieldname, depending on
        // the output mode
        $output_fieldname_mode = 'name';
        if ($this->getDatastoreMode() === true)
            $output_fieldname_mode = 'store_name';

        foreach ($structure as $column)
        {
            $store_column = $column;
            $store_column['name'] = $store_column[$output_fieldname_mode];
            unset($store_column[$output_fieldname_mode]);

            $store_structure[] = $store_column;
        }

        // create the table
        if (!$this->service->createTable($path, $store_structure))
            return false;

        return true;
    }

    private function createFile()
    {
        $path = $this->getPath();
        if ($path === false)
            return false;

        $mime_type = $this->getMimeType();
        if ($mime_type === false)
            $mime_type = ContentType::MIME_TYPE_NONE;

        if (!$this->service->createFile($path, $mime_type))
            return false;

        return true;
    }

    private function writeToTable($data)
    {
        // data needs to be an array
        if (!is_array($data))
            return false;

        $path = $this->getPath();
        if ($path === false)
            return false;

        $structure = $this->getStructure();
        if ($structure === false)
            return false;

        // don't allow xdrowid values to overwrite default internal values
        unset($data['xdrowid']);

        if ($this->table_inserter === false)
        {
            // get the column names to use when inserting values (either the ones stored
            // in the structure 'name' field or the structure 'store_name' field) depending
            // on the output mode
            $output_fieldname_mode = 'name';
            if ($this->getDatastoreMode() === true)
                $output_fieldname_mode = 'store_name';

            // get the fields from the structure using the store name
            $flds = array_column($structure, $output_fieldname_mode);
            $this->table_inserter = $this->service->bulkInsert($path);
            if ($this->table_inserter !== false)
            {
                $result = $this->table_inserter->startInsert($flds);
                if ($result === false)
                    return false;
            }
        }

        if ($this->table_inserter !== false)
        {
            if (isset($data[0]))
            {
                // array is sequential, non-associative
                $result = $this->table_inserter->insertRow($data);
                if ($result === false)
                    return false;
            }
             else
            {
                // array is associative -- map the data being inserted with
                // field names to the internal store fields that were set up
                // in the bulk insert using the index of the structure
                $mapped_row = array();
                foreach ($structure as $fields)
                {
                    $data_value_to_insert = isset_or($data[$fields['name']], null);
                    $mapped_row[] = $data_value_to_insert;
                }

                $result = $this->table_inserter->insertRow($mapped_row);
                if ($result === false)
                    return false;
            }
        }

        return true;
    }

    private function writeToFile($data)
    {
        // data needs to be a string
        if (!is_string($data))
            return false;

        $path = $this->getPath();
        if ($path === false)
            return false;

        if ($this->file_inserter === false)
        {
            $file_inserter = $this->service->openFile($path);
            if (!$file_inserter)
                return false;

            $this->file_inserter = $file_inserter;
        }

        $result = $this->file_inserter->write($data);
        if ($result === false)
            return false;

        return true;
    }

    private function initService()
    {
        $connection_eid = $this->getConnection();
        $connection = \Flexio\Object\Connection::load($connection_eid);
        if ($connection === false)
            return false;

        $connection_info = $connection->get();
        if ($connection_info === false)
            return false;

        $service = $connection->getService();
        if (!$service)
            return false;

        $this->service = $service;
        return true;
    }

    private function closeTable()
    {
        if ($this->table_inserter !== false)
            $result = $this->table_inserter->finishInsert();

        $this->table_inserter = false;
    }

    private function closeFile()
    {
        $this->file_inserter = false;
    }

    private function getConnection()
    {
        return $this->stream_info['connection_eid'];
    }

    private function getPath()
    {
        return $this->stream_info['path'];
    }

    private function getMimeType()
    {
        return $this->stream_info['mime_type'];
    }

    private function getStructure()
    {
        return $this->stream_info['structure'];
    }

    private function setDatastoreMode($mode)
    {
        // $mode is true/false
        $this->datastore_mode = $mode;
    }

    private function getDatastoreMode()
    {
        return $this->datastore_mode;
    }

    private function isOk()
    {
        if ($this->service === false)
            return false;

        return true;
    }
}

