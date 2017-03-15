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


class StreamReader
{
    private $stream_info = false;
    private $reader = false;

    function __destruct()
    {
        $this->close();
    }

    public static function create($stream)
    {
        // TODO: StreamReader is designed to work right now with database services;
        // the function calls rely on specific service functions rather than the service
        // interface, so the reader won't work on some type of services

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

        // create an appropriate reader based on the mime type
        $mime_type = $stream_info['mime_type'];
        switch ($mime_type)
        {
            default:
                $object->reader = StreamFileReader::create($stream_info);
                break;

            case \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE:
                $object->reader = StreamTableReader::create($stream_info);
                break;
        }

        if ($object->isOk() === false)
            return false;

        return $object;
    }

    public function read($length = 1024)
    {
        if ($this->isOk() === false)
            return false;

        return $this->reader->read($length);
    }

    public function readRow()
    {
        if ($this->isOk() === false)
            return false;

        return $this->reader->readRow();
    }

    public function close()
    {
        if ($this->isOk() === false)
            return;

        $this->reader->close();
        $this->reader = false;
    }

    private function isOk()
    {
        if ($this->reader === false)
            return false;

        return true;
    }
}



// stream file reader implementation
class StreamFileReader
{
    private $stream_info = false;
    private $service = false;
    private $iterator = false;
    private $read_buffer = '';

    function __destruct()
    {
        $this->close();
    }

    public static function create($stream_info)
    {
        $object = new static;
        $object->stream_info = $stream_info;

        $service = $object->getService();
        if ($service === false)
            return false;

        return $object;
    }

    public function close()
    {
        if ($this->service !== false)
            $this->service->close();

        $this->service = false;
        $this->iterator = false;
        $this->read_buffer = '';
    }

    public function read($length)
    {
        if ($this->iterator === false)
        {
            if (!isset($this->stream_info['path']))
                return false;
            $path = $this->stream_info['path'];

            $this->iterator = $this->getService()->openFile($path);
            if (!$this->iterator)
                return false;

            $this->read_buffer = '';
        }

        $read_chunk = $this->iterator->read($length);
        return $read_chunk;
    }

    public function readRow()
    {
        if ($this->iterator === false)
        {
            if (!isset($this->stream_info['path']))
                return false;
            $path = $this->stream_info['path'];

            $this->iterator = $this->getService()->openFile($path);
            if (!$this->iterator)
                return false;

            $this->read_buffer = '';
        }

        // read content into the buffer until we have a row
        $amount_to_read = 1024;
        $max_row_size = 8192; // set max row size to 2^13

        while (true)
        {
            // if we can get a row from the buffer, return it; note:
            // getRowFromBuffer() removes this chunk from the buffer
            $row = self::getRowFromBuffer($this->read_buffer);
            if ($row !== false)
                return $row;

            // we couldn't get a row from the buffer, so try to fill
            // the buffer with more data
            $read_chunk = $this->iterator->read($amount_to_read);
            if (!$read_chunk)
            {
                // if the length of the buffer is empty, we've returned
                // everything that can be returned, and nothing more
                // is available; return false to indicate there's nothing
                // left to read
                if (strlen($this->read_buffer) === 0)
                    return false;

                // we couldn't find anything more to read, which means
                // whatever is in the buffer is the last row
                $row = $this->read_buffer;
                $this->read_buffer = '';
                return $row;
            }

            $this->read_buffer .= $read_chunk;

            // if the size of the buffer is bigger than the max row size,
            // return the portion of the buffer equal to the max row size
            if (strlen($this->read_buffer) > $max_row_size)
            {
                $row = substr($this->read_buffer, 0, $max_row_size);
                $this->read_buffer = substr($this->read_buffer, $max_row_size + 1);
                return $row;
            }
        }
    }

    private static function getRowFromBuffer(&$buffer)
    {
        $pos = strpos($buffer, "\n");
        if ($pos !== false)
        {
            $line = substr($buffer, 0, $pos + 1);
            $buffer = substr($buffer, $pos + 1);
            return $line;
        }

        // look for a CR (\r), but it can't be the last character of the
        // buffer, because it may be part of a \r\n split between two buffers

        $pos = strpos($buffer, "\r");
        if ($pos !== false && $pos < strlen($buffer)-1)
        {
            $line = substr($buffer, 0, $pos + 1);
            $buffer = substr($buffer, $pos + 1);
            return $line;
        }

        return false;

        /*
        // this is the old version of this code; it has some problems;
        // first, the first check is unecessary because the second check
        // does the same thing.  The problem with the final check is if
        // a \r\n is split up between two buffer reads; that will effectively
        // insert a blank line into the data
        // datadatadata\r<buffer-end>\ndatadatadatadata
        //
        // solution: go with a simpler variant that only handles \r\n and \n
        // old-style mac formats no longer supported

        // check for various types of newlines and return
        // the line if we can find one of them

        $cr = strpos($buffer, "\r\n");
        if ($cr !== false)
        {
            $line = substr($buffer, 0, $cr + 2);
            $buffer = substr($buffer, $cr + 2);
            return $line;
        }

        $cr = strpos($buffer, "\n");
        if ($cr !== false)
        {
            $line = substr($buffer, 0, $cr + 1);
            $buffer = substr($buffer, $cr + 1);
            return $line;
        }

        $cr = strpos($buffer, "\r");
        if ($cr !== false)
        {
            $line = substr($buffer, 0, $cr + 1);
            $buffer = substr($buffer, $cr + 1);
            return $line;
        }

        // no lines found
        return false;
        */
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



// stream table reader implementation
class StreamTableReader
{
    private $stream_info = false;
    private $service = false;
    private $iterator = false;
    private $read_buffer = '';

    function __destruct()
    {
        $this->close();
    }

    public static function create($stream_info)
    {
        $object = new static;
        $object->stream_info = $stream_info;

        $service = $object->getService();
        if ($service === false)
            return false;

        return $object;
    }

    public function close()
    {
        if ($this->service !== false)
            $this->service->close();

        $this->service = false;
        $this->iterator = false;
        $this->read_buffer = '';
    }

    public function read($length)
    {
        if (!isset($this->stream_info['path']))
            return false;
        $path = $this->stream_info['path'];

        if (!isset($this->stream_info['structure']))
            return false;
        $structure = $this->stream_info['structure'];

        if ($this->iterator === false)
        {
            $this->iterator = $this->getService()->query(['table' => $path]);
            if (!$this->iterator)
                return false;

            $this->read_buffer = '';
            $header_row = array_column($structure, 'name');
            $this->read_buffer .= (self::arrayToCsv(array_values($header_row)));
        }

        $done = false;
        while (strlen($this->read_buffer) < $length)
        {
            // get the rows
            $row = $this->iterator->fetchRow();
            if (!$row)
            {
                $done = true;
                break;
            }

            $mapped_row = array();
            foreach ($structure as $col)
                $mapped_row[$col['name']] = isset_or($row[$col['store_name']], null);

            $this->read_buffer .= (self::arrayToCsv(array_values($mapped_row)));
        }

        if ($done)
        {
            $ret = $this->read_buffer;
            $this->read_buffer = '';
            return $ret;
        }
         else
        {
            $ret = substr($this->read_buffer, 0, $length);
            $this->read_buffer = substr($this->read_buffer, $length);
            return $ret;
        }
    }

    public function readRow()
    {
        if (!isset($this->stream_info['path']))
            return false;
        $path = $this->stream_info['path'];

        if (!isset($this->stream_info['structure']))
            return false;
        $structure = $this->stream_info['structure'];

        if ($this->iterator === false)
        {
            $this->iterator = $this->getService()->query(['table' => $path]);
            if (!$this->iterator)
                return false;

            $this->read_buffer = '';
        }

        $row = $this->iterator->fetchRow();
        if (!$row)
            return false;

        $mapped_row = array();
        foreach ($structure as $col)
            $mapped_row[$col['name']] = isset_or($row[$col['store_name']], null);

        return $mapped_row;
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

    private static function arrayToCsv($arr)
    {
        $buffer = fopen('php://temp', 'r+');
        fputcsv($buffer, $arr, ',', '"', "\\");
        rewind($buffer);
        $output = fgets($buffer);
        fclose($buffer);

        return $output;
    }
}



// experimental stream table writer implementation (JSON format)
class StreamTableJsonReader
{
    private $stream_info = false;
    private $service = false;
    private $iterator = false;
    private $read_buffer = '';

    function __destruct()
    {
        $this->close();
    }

    public static function create($stream_info)
    {
        $object = new static;
        $object->stream_info = $stream_info;

        $service = $object->getService();
        if ($service === false)
            return false;

        return $object;
    }

    public function close()
    {
        if ($this->service !== false)
            $this->service->close();

        $this->service = false;
        $this->iterator = false;
        $this->read_buffer = '';
    }

    public function read($length)
    {
        if (!isset($this->stream_info['path']))
            return false;
        $path = $this->stream_info['path'];

        if (!isset($this->stream_info['structure']))
            return false;
        $structure = $this->stream_info['structure'];

        if ($this->iterator === false)
        {
            $this->iterator = $this->getService()->query(['table' => $path]);
            if (!$this->iterator)
                return false;

            $this->read_buffer = '';
            $header_row = array_column($structure, 'name');
            $this->read_buffer .= (self::arrayToCsv(array_values($header_row)));
        }

        $done = false;
        while (strlen($this->read_buffer) < $length)
        {
            // get the rows
            $row = $this->iterator->fetchRow();
            if (!$row)
            {
                $done = true;
                break;
            }

            // data is stored in json format in the content field; unpack it
            $row = @json_decode($row['xdcontent'],true);

            // map the stored name to the name
            $mapped_row = array();
            foreach ($structure as $col)
                $mapped_row[$col['name']] = isset_or($row[$col['store_name']], null);

            $this->read_buffer .= (self::arrayToCsv(array_values($mapped_row)));
        }

        if ($done)
        {
            $ret = $this->read_buffer;
            $this->read_buffer = '';
            return $ret;
        }
         else
        {
            $ret = substr($this->read_buffer, 0, $length);
            $this->read_buffer = substr($this->read_buffer, $length);
            return $ret;
        }
    }

    public function readRow()
    {
        if (!isset($this->stream_info['path']))
            return false;
        $path = $this->stream_info['path'];

        if (!isset($this->stream_info['structure']))
            return false;
        $structure = $this->stream_info['structure'];

        if ($this->iterator === false)
        {
            $this->iterator = $this->getService()->query(['table' => $path]);
            if (!$this->iterator)
                return false;

            $this->read_buffer = '';
        }

        $row = $this->iterator->fetchRow();
        if (!$row)
            return false;

        // data is stored in json format in the content field; unpack it
        $row = @json_decode($row['xdcontent'],true);

        // map the stored name to the name
        $mapped_row = array();
        foreach ($structure as $col)
            $mapped_row[$col['name']] = isset_or($row[$col['store_name']], null);

        return $mapped_row;
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

    private static function arrayToCsv($arr)
    {
        $buffer = fopen('php://temp', 'r+');
        fputcsv($buffer, $arr, ',', '"', "\\");
        rewind($buffer);
        $output = fgets($buffer);
        fclose($buffer);

        return $output;
    }
}
