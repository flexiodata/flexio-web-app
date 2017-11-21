<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-11-20
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


require_once dirname(__DIR__) . '/base/StreamMemory.php';


class StreamMemoryReader implements \Flexio\Base\IStreamReader
{
    private $stream;
    private $offset;

    public function __construct()
    {
        $this->offset = 0;
    }

    public static function create(\Flexio\Base\StreamMemory $stream) : \Flexio\Base\StreamMemoryReader
    {
        $object = new static();
        $object->stream = $stream;
        return $object;
    }

    public function read($length = 1024)
    {
        $mime_type = $this->getStream()->getMimeType();
        switch ($mime_type)
        {
            default:
                $str = $this->readString($this->offset, $length);
                if ($str === false)
                    return false;
                $this->offset += strlen($str);
                return $str;

            case \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE:
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        }
    }

    public function readRow()
    {
        $mime_type = $this->getStream()->getMimeType();
        switch ($mime_type)
        {
            default:
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);

            case \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE:
                $rows = $this->readArrayRows($this->offset, 1);
                if ($rows === false)
                    return false;

                $this->offset++;
                return $rows[0];
        }
    }

    public function getRows(int $offset, int $limit)
    {
        if ($offset < 0)
            $offset = 0;
        if ($limit < 0)
            $limit = 0;

        // only implemented for table type streams
        $mime_type = $this->getStream()->getMimeType();
        if ($mime_type != \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);

        $result = $this->readArrayRows($offset, $limit);
        if ($result === false)
            return false;

        return $result;
    }

    public function close() : bool
    {
        return true;
    }

    private function getStream() : \Flexio\Base\StreamMemory
    {
        return $this->stream;
    }

    private function readString(int $offset, int $limit)
    {
        $buffer = $this->getStream()->buffer;
        if (!is_string($buffer))
            return false;

        if ($offset >= strlen($this->getStream()->buffer))
            return false;

        return substr($this->getStream()->buffer, $this->offset, $limit);
    }

    private function readArrayRows(int $start, int $limit)
    {
        $buffer = $this->getStream()->buffer;
        if (!is_array($buffer))
            return false;

        if ($start > count($buffer) - 1)
            return false;

        $rows = array_slice($buffer, $start, $limit);

        // data may be stored either with keys or as simple values; combine these
        // with the structure when returning the result

        $columns = $this->getStream()->getStructure()->enum();
        $result = array();
        foreach ($rows as $r)
        {
            $row_is_associative = \Flexio\Base\Util::isAssociativeArray($r);

            $idx = 0;
            $mapped_row = array();
            foreach ($columns as $c)
            {
                $column_name = $c['name'];
                if ($row_is_associative)
                {
                    if (array_key_exists($column_name, $r))
                        $mapped_row[$column_name] = $r[$column_name];
                         else
                        $mapped_row[$column_name] = null;
                }
                 else
                {
                    $mapped_row[$column_name] = $r[$idx] ?? null;
                }
                $idx++;
            }

            $result[] = $mapped_row;
        }

        return $result;
    }
}

