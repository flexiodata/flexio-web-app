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
    private $service = false;
    private $table_iterator = false;
    private $file_iterator = false;
    private $table_read_buffer = '';
    private $file_read_buffer = '';

    function __construct()
    {
    }

    function __destruct()
    {
        $this->close();
    }

    public static function create($stream)
    {
        // TODO: StreamReader is designed to work right now with database services;
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

        return true;
    }

    public function read($length = 1024)
    {
        // TODO: implement

        if ($this->isOk() === false)
            return false;

        $mime_type = $this->getMimeType();
        if ($mime_type === \Flexio\System\ContentType::MIME_TYPE_FLEXIO_TABLE)
            return $this->readFromTable($length);
             else
            return $this->readFromFile($length);
    }

    public function readRow()
    {
        if ($this->isOk() === false)
            return false;

        $mime_type = $this->getMimeType();
        if ($mime_type === \Flexio\System\ContentType::MIME_TYPE_FLEXIO_TABLE)
            return $this->readRowFromTable();
             else
            return $this->readRowFromFile();
    }

    public function close()
    {
        // note: don't reset the stream item; this is
        // used to be able to reinitialized the other
        // members

        if ($this->service !== false)
            $this->service->close();

        if ($this->table_iterator !== false)
            $this->closeTable();

        if ($this->file_iterator !== false)
            $this->closeFile();

        $this->service = false;
        $this->table_iterator = false;
        $this->file_iterator = false;
    }

    private function readFromTable($length)
    {
        $path = $this->getPath();
        if ($path === false)
            return false;

        $structure = $this->getStructure();
        if ($structure === false)
            return false;

        if ($this->table_iterator === false)
        {
            $this->table_iterator = $this->service->query(['table' => $path]);
            if (!$this->table_iterator)
                return false;

            $this->table_read_buffer = '';
            $header_row = array_column($structure, 'name');
            $this->table_read_buffer .= (self::arrayToCsv(array_values($header_row)));
        }

        $done = false;
        while (strlen($this->table_read_buffer) < $length)
        {
            // get the rows
            $row = $this->table_iterator->fetchRow();
            if (!$row)
            {
                $done = true;
                break;
            }

            $mapped_row = array();
            foreach ($structure as $col)
                $mapped_row[$col['name']] = isset_or($row[$col['store_name']], null);

            $this->table_read_buffer .= (self::arrayToCsv(array_values($mapped_row)));
        }

        if ($done)
        {
            $ret = $this->table_read_buffer;
            $this->table_read_buffer = '';
            return $ret;
        }
         else
        {
            $ret = substr($this->table_read_buffer, 0, $length);
            $this->table_read_buffer = substr($this->table_read_buffer, $length);
            return $ret;
        }
    }

    private function readFromFile($length)
    {
        if ($this->file_iterator === false)
        {
            $path = $this->getPath();
            if ($path === false)
                return false;

            $this->file_iterator = $this->service->openFile($path);
            if (!$this->file_iterator)
                return false;

            $this->file_read_buffer = '';
        }

        $read_chunk = $this->file_iterator->read($length);
        return $read_chunk;
    }

    private function readRowFromTable()
    {
        $path = $this->getPath();
        if ($path === false)
            return false;

        $structure = $this->getStructure();
        if ($structure === false)
            return false;

        if ($this->table_iterator === false)
        {
            $this->table_iterator = $this->service->query(['table' => $path]);
            if (!$this->table_iterator)
                return false;

            $this->table_read_buffer = '';
        }

        $row = $this->table_iterator->fetchRow();
        if (!$row)
            return false;

        $mapped_row = array();
        foreach ($structure as $col)
            $mapped_row[$col['name']] = isset_or($row[$col['store_name']], null);

        return $mapped_row;
    }

    private function readRowFromFile()
    {
        if ($this->file_iterator === false)
        {
            $path = $this->getPath();
            if ($path === false)
                return false;

            $this->file_iterator = $this->service->openFile($path);
            if (!$this->file_iterator)
                return false;

            $this->file_read_buffer = '';
        }

        // read content into the buffer until we have a row
        $amount_to_read = 1024;
        $max_row_size = 8192; // set max row size to 2^13

        while (true)
        {
            // if we can get a row from the buffer, return it; note:
            // getRowFromBuffer() removes this chunk from the buffer
            $row = self::getRowFromBuffer($this->file_read_buffer);
            if ($row !== false)
                return $row;

            // we couldn't get a row from the buffer, so try to fill
            // the buffer with more data
            $read_chunk = $this->file_iterator->read($amount_to_read);
            if (!$read_chunk)
            {
                // if the length of the buffer is empty, we've returned
                // everything that can be returned, and nothing more
                // is available; return false to indicate there's nothing
                // left to read
                if (strlen($this->file_read_buffer) === 0)
                    return false;

                // we couldn't find anything more to read, which means
                // whatever is in the buffer is the last row
                $row = $this->file_read_buffer;
                $this->file_read_buffer = '';
                return $row;
            }

            $this->file_read_buffer .= $read_chunk;

            // if the size of the buffer is bigger than the max row size,
            // return the portion of the buffer equal to the max row size
            if (strlen($this->file_read_buffer) > $max_row_size)
            {
                $row = substr($this->file_read_buffer, 0, $max_row_size);
                $this->file_read_buffer = substr($this->file_read_buffer, $max_row_size + 1);
                return $row;
            }
        }
    }

    private function getRowFromBuffer(&$buffer)
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
        $this->table_iterator = false;
        $this->table_read_buffer = '';
    }

    private function closeFile()
    {
        $this->file_iterator = false;
        $this->file_read_buffer = '';
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

    private function isOk()
    {
        if ($this->service === false)
            return false;

        return true;
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

