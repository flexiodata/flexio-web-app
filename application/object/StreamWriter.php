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
    private $writer = false;
    private $bytes_written = 0; // total bytes written

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

        $object = new static;

        $stream_info = array();
        if (($stream instanceof \Flexio\Object\Stream))
            $stream_info = $stream->get();

        if (is_array($stream))
        {
            $stream_info['connection_eid'] = isset_or($stream['connection_eid'], false);
            $stream_info['path'] = isset_or($stream['path'], false);
            $stream_info['mime_type'] = isset_or($stream['mime_type'], false);
            $stream_info['structure'] = isset_or($stream['structure'], false);
        }

        // write out data using datastore conventions by default (included a
        // row identifier and use store_name, a safe fieldname convention, for
        // the fieldnames)
        $stream_info['datastore_mode'] = $datastore_mode;

        // create an appropriate writer based on the mime type
        $mime_type = $stream_info['mime_type'];
        switch ($mime_type)
        {
            default:
                $object->writer = StreamFileWriter::create($stream_info);
                break;

            case \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE:
                $object->writer = StreamTableWriter::create($stream_info);
                break;
        }

        if ($object->isOk() === false)
            return false;

        return $object;
    }

    public function write($data)
    {
        if ($this->isOk() === false)
            return false;

        // data being written should be either a string or an array;
        // get a representative string of the content so we can find
        // out the size of the data that was written
        if (is_string($data))
            $content_str = $data;
        if (is_array($data))
            $content_str = implode('', $data);

        // increment the total bytes written
        $this->bytes_written += strlen($content_str);

        // write the data
        return $this->writer->write($data);
    }

    public function getBytesWritten()
    {
        // returns the total amount written
        return $this->bytes_written;
    }

    public function close()
    {
        if ($this->isOk() === false)
            return;

        $this->writer->close();
    }

    private function isOk()
    {
        if ($this->writer === false)
            return false;

        return true;
    }
}



// stream file writer implementation
class StreamFileWriter
{
    private $stream_info = false;
    private $service = false;
    private $inserter = false;

    public static function create($stream_info)
    {
        $object = new static;
        $object->stream_info = $stream_info;

        if (!isset($object->stream_info['path']))
            return false;

        $path = $object->stream_info['path'];
        $mime_type = isset_or($object->stream_info['mime_type'], \Flexio\Base\ContentType::MIME_TYPE_NONE);

        $service = $object->getService();
        if ($service === false)
            return false;

        // create the file
        if (!$service->createFile($path, $mime_type))
            return false;

        return $object;
    }

    public function write($data)
    {
        // data needs to be a string
        if (!is_string($data))
            return false;

        if (!isset($this->stream_info['path']))
            return false;
        $path = $this->stream_info['path'];

        if ($this->inserter === false)
        {
            $inserter = $this->getService()->openFile($path);
            if (!$inserter)
                return false;

            $this->inserter = $inserter;
        }

        $result = $this->inserter->write($data);
        if ($result === false)
            return false;

        return true;
    }

    public function close()
    {
        if ($this->service !== false)
            $this->service->close();

        $this->inserter = false;
        $this->service = false;
    }

    private function getService()
    {
        if ($this->service !== false)
            return $this->service;

        $connection_eid = isset_or($this->stream_info['connection_eid'], false);
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
        return $this->service;
    }
}



// stream table writer implementation
class StreamTableWriter
{
    private $stream_info = false;
    private $service = false;
    private $inserter = false;

    public static function create($stream_info)
    {
        $object = new static;
        $object->stream_info = $stream_info;

        if (!isset($object->stream_info['path']))
            return false;

        $path = $object->stream_info['path'];
        $mime_type = isset_or($object->stream_info['mime_type'], \Flexio\Base\ContentType::MIME_TYPE_NONE);
        $structure = isset_or($object->stream_info['structure'], false);

        $store_structure = array();
        if ($object->getDatastoreMode() === true)
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
        if ($object->getDatastoreMode() === true)
            $output_fieldname_mode = 'store_name';

        foreach ($structure as $column)
        {
            $store_column = $column;
            $store_column['name'] = $store_column[$output_fieldname_mode];
            unset($store_column[$output_fieldname_mode]);

            $store_structure[] = $store_column;
        }

        $service = $object->getService();
        if ($service === false)
            return false;

        // create the table
        if (!$service->createTable($path, $store_structure))
            return false;

        return $object;
    }

    public function write($data)
    {
        // data needs to be an array
        if (!is_array($data))
            return false;

        if (!isset($this->stream_info['path']))
            return false;
        $path = $this->stream_info['path'];

        if (!isset($this->stream_info['structure']))
            return false;
        $structure = $this->stream_info['structure'];

        // don't allow xdrowid values to overwrite default internal values
        unset($data['xdrowid']);

        if ($this->inserter === false)
        {
            // get the column names to use when inserting values (either the ones stored
            // in the structure 'name' field or the structure 'store_name' field) depending
            // on the output mode
            $output_fieldname_mode = 'name';
            if ($this->getDatastoreMode() === true)
                $output_fieldname_mode = 'store_name';

            // get the fields from the structure using the store name
            $flds = array_column($structure, $output_fieldname_mode);
            $this->inserter = $this->getService()->bulkInsert($path);
            if ($this->inserter !== false)
            {
                $result = $this->inserter->startInsert($flds);
                if ($result === false)
                    return false;
            }
        }

        if ($this->inserter !== false)
        {
            if (array_key_exists('0', $data))
            {
                // array is sequential, non-associative
                $result = $this->inserter->insertRow($data);
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

                $result = $this->inserter->insertRow($mapped_row);
                if ($result === false)
                    return false;
            }
        }

        return true;
    }

    public function close()
    {
        if ($this->inserter !== false)
            $this->inserter->finishInsert();

        if ($this->service !== false)
            $this->service->close();

        $this->inserter = false;
        $this->service = false;
    }

    private function getService()
    {
        if ($this->service !== false)
            return $this->service;

        $connection_eid = isset_or($this->stream_info['connection_eid'], false);
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
        return $this->service;
    }

    private function getDatastoreMode()
    {
        $datastore_mode = false;
        if (!isset($this->stream_info['datastore_mode']))
            return false;

        if ($this->stream_info['datastore_mode'] === true)
            return true;

        return false;
    }
}




// experimental stream table writer implementation (JSON format)
class StreamTableJsonWriter
{
    private $stream_info = false;
    private $service = false;
    private $inserter = false;

    public static function create($stream_info)
    {
        $object = new static;
        $object->stream_info = $stream_info;

        if (!isset($object->stream_info['path']))
            return false;

        $path = $object->stream_info['path'];
        $mime_type = isset_or($object->stream_info['mime_type'], \Flexio\Base\ContentType::MIME_TYPE_NONE);

        $store_structure = array();
        if ($object->getDatastoreMode() === true)
        {
            $store_structure[] = array(
                'name' => 'xdrowid',
                'type' => 'bigserial',
                'width' => 18,
                'scale' => 0
            );
        }

        $store_structure[] = array(
            'name' => 'xdcontent',
            'type' => 'text'
        );

        $service = $object->getService();
        if ($service === false)
            return false;

        // create the table
        if (!$service->createTable($path, $store_structure))
            return false;

        return $object;
    }

    public function write($data)
    {
        // data needs to be an array
        if (!is_array($data))
            return false;

        if (!isset($this->stream_info['path']))
            return false;
        $path = $this->stream_info['path'];

        if (!isset($this->stream_info['structure']))
            return false;
        $structure = $this->stream_info['structure'];

        if ($this->inserter === false)
        {
            // prepare the insert; we'll be inserting into the content field
            $flds = array('xdcontent');
            $this->inserter = $this->getService()->bulkInsert($path);
            if ($this->inserter !== false)
            {
                $result = $this->inserter->startInsert($flds);
                if ($result === false)
                    return false;
            }
        }

        if ($this->inserter !== false)
        {
            if (array_key_exists('0', $data))
            {
                // array is sequential, non-associative; use the structure
                // as the default row and add the values

                $row = array();
                $idx = 0;
                foreach ($structure as $s)
                {
                    $key= $s['name'];
                    if ($this->getDatastoreMode() === true)
                        $key = $s['store_name'];

                    $value = null;
                    if (isset($data[$idx]))
                        $value = $data[$idx];

                    $row[$key] = $value;
                    $idx++;
                }

                $row = array(json_encode($row));
                $result = $this->inserter->insertRow($row);
                if ($result === false)
                    return false;
            }
             else
            {
                // array is associative; if we're in the datastore mode, exchange
                // the keys in the data with the store name keys; otherwise, just
                // insert the data
                $row = array();
                foreach ($structure as $s)
                {
                    $original_key = $s['name'];
                    $stored_key = $original_key;

                    if ($this->getDatastoreMode() === true)
                        $stored_key = $s['store_name'];

                    $value = null;
                    if (isset($data[$original_key]))
                        $value = $data[$original_key];

                    $row[$stored_key] = $value;
                }

                $row = array(json_encode($row));
                $result = $this->inserter->insertRow($row);
                if ($result === false)
                    return false;
            }
        }

        return true;
    }

    public function close()
    {
        if ($this->inserter !== false)
            $this->inserter->finishInsert();

        if ($this->service !== false)
            $this->service->close();

        $this->inserter = false;
        $this->service = false;
    }

    private function getService()
    {
        if ($this->service !== false)
            return $this->service;

        $connection_eid = isset_or($this->stream_info['connection_eid'], false);
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
        return $this->service;
    }

    private function getDatastoreMode()
    {
        $datastore_mode = false;
        if (!isset($this->stream_info['datastore_mode']))
            return false;

        if ($this->stream_info['datastore_mode'] === true)
            return true;

        return false;
    }
}
