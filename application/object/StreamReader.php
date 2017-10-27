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


declare(strict_types=1);
namespace Flexio\Object;


require_once dirname(__DIR__) . '/object/Abstract.php';


class StreamMemoryReader implements \Flexio\Object\IStreamReader
{
    private $stream;

    public function __construct()
    {
    }

    public static function create(\Flexio\Object\StreamMemory $stream) : \Flexio\Object\StreamMemoryReader
    {
        $object = new static;
        $object->stream = $stream;
        return $object;
    }

    public function read($length = 1024)
    {
        return $this->getStream()->buffer;
    }

    public function readRow()
    {
        return $this->getStream()->buffer;
    }

    public function getRows(int $offset, int $limit)
    {
        return $this->getStream()->buffer;
    }

    public function getContent(int $start = 0, int $limit = PHP_INT_MAX, int $readsize = 1024 /* testing */)
    {
        if ($start < 0 )
            $start = 0;
        if ($limit < 0)
            $limit = 0;
        if ($readsize <= 0)
            $readsize = 1;

        return $this->getStream()->buffer;
    }

    public function close() : bool
    {
        return true;
    }

    private function getStream() : \Flexio\Object\StreamMemory
    {
        return $this->stream;
    }
}


class StreamReader implements \Flexio\Object\IStreamReader
{
    private $stream_info = false;
    private $reader = false;

    function __destruct()
    {
        $this->close();
    }

    public static function create(\Flexio\Object\Stream $stream) : \Flexio\Object\StreamReader
    {
        // TODO: StreamReader is designed to work right now with database services;
        // the function calls rely on specific service functions rather than the service
        // interface, so the reader won't work on some type of services

        // the stream parameter can either be a stream or stream properties;
        // the key information we need from the stream are the path, mime_type,
        // and structure for the source

        $object = new static;
        $stream_properties = $stream->get();

        $object->stream_info = array();
        $object->stream_info['connection_eid'] = $stream_properties['connection_eid'] ?? false;
        $object->stream_info['path'] = $stream_properties['path'] ?? false;
        $object->stream_info['mime_type'] = $stream_properties['mime_type'] ?? false;
        $object->stream_info['structure'] = $stream_properties['structure'] ?? false;

        // create an appropriate reader based on the mime type
        $mime_type = $object->stream_info['mime_type'];
        switch ($mime_type)
        {
            default:
                $object->reader = StreamFileReader::create($object->stream_info);
                break;

            case \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE:
                $object->reader = StreamTableReader::create($object->stream_info);
                break;
        }

        if ($object->isOk() === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

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

    public function getRows(int $offset, int $limit)
    {
        if ($this->isOk() === false)
            return false;

        return $this->reader->getRows($offset, $limit);
    }

    public function getContent(int $start = 0, int $limit = PHP_INT_MAX, int $readsize = 1024 /* testing */) // TODO: add function return type
    {
        if ($start < 0 )
            $start = 0;
        if ($limit < 0)
            $limit = 0;
        if ($readsize <= 0)
            $readsize = 1;

        $mime_type = $this->stream_info['mime_type'];
        if ($mime_type === \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE)
        {
            return $this->getRows($start,$limit);
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
            while (true)
            {
                $chunk = $this->read($readsize);
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

    public function close() : bool
    {
        if ($this->isOk() === false)
            return true;

        $this->reader->close();
        $this->reader = false;
        return true;
    }

    private function isOk() : bool
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

    public static function create(array $stream_info)
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
        //if ($this->service !== false)
        //    $this->service->close();

        $this->service = false;
        $this->iterator = false;
        $this->read_buffer = '';
    }

    public function read(int $length = 1024)
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

    public function getRows(int $offset, int $limit)
    {
        // TODO: possibly implement this here
        // this is not presently called as non-table streams
        // are handled manually by Stream.php content() see line ~216 there
        return null;
    }

    private static function getRowFromBuffer(&$buffer) // TODO: add input parameter types
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
    }

    private function getService()
    {
        if ($this->service !== false)
            return $this->service;

        $connection_eid = $this->stream_info['connection_eid'] ?? false;
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

    public static function create(array $stream_info)
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
        //if ($this->service !== false)
        //    $this->service->close();

        $this->service = false;
        $this->iterator = false;
        $this->read_buffer = '';
    }

    public function read(int $length)
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
                $mapped_row[$col['name']] = $row[$col['store_name']] ?? null;

            $this->read_buffer .= (self::arrayToCsv(array_values($mapped_row)));
        }

        if ($done)
        {
            $ret = $this->read_buffer;
            $this->read_buffer = '';
            return strlen($ret) > 0 ? $ret : false;
        }
         else
        {
            $ret = substr($this->read_buffer, 0, $length);
            $this->read_buffer = substr($this->read_buffer, $length);
            return $ret;
        }
    }

    public function getRows(int $offset, int $limit)
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

        $rows = $this->iterator->getRows($offset,$limit);
        if (!$rows)
            return false;

        $mapped_rows = array();
        foreach ($rows as $row)
        {
            $mapped_row = array();
            foreach ($structure as $col)
                $mapped_row[$col['name']] = $row[$col['store_name']] ?? null;

            $mapped_rows[] = $mapped_row;
        }

        return $mapped_rows;
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
            $mapped_row[$col['name']] = $row[$col['store_name']] ?? null;

        return $mapped_row;
    }

    private function getService()
    {
        if ($this->service !== false)
            return $this->service;

        $connection_eid = $this->stream_info['connection_eid'] ?? false;
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

    private static function arrayToCsv(array $arr)
    {
        $buffer = fopen('php://temp', 'r+');
        fputcsv($buffer, $arr, ',', '"', "\\");
        rewind($buffer);
        $output = fgets($buffer);
        fclose($buffer);

        return $output;
    }
}
