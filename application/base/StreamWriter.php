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


class StreamMemoryWriter implements \Flexio\Base\IStreamWriter
{
    private $stream;
    private $bytes_written;

    public function __construct()
    {
        $this->bytes_written = 0;
    }

    public static function create(\Flexio\Base\StreamMemory $stream) : \Flexio\Base\StreamMemoryWriter
    {
        $object = new static();
        $object->stream = $stream;
        return $object;
    }

    public function write($data)
    {
        // write the data, depending on the type
        $bytes_written = 0;
        $mime_type = $this->getStream()->getMimeType();
        switch ($mime_type)
        {
            default:
                $this->writeString($data, $bytes_written);
                break;

            case \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE:
                $this->writeArray($data, $bytes_written);
                break;
        }

        // increment the total bytes written
        $this->bytes_written += $bytes_written;
    }

    public function getBytesWritten() : int
    {
        return $this->bytes_written;
    }

    public function close() : bool
    {
        return true;
    }

    private function getStream() : \Flexio\Base\StreamMemory
    {
        return $this->stream;
    }

    private function writeString(string $data, int &$bytes_written)
    {
        $bytes_written = 0;
        if (is_string($this->getStream()->buffer) === false) // initialize buffer
            $this->getStream()->buffer = '';

        $this->getStream()->buffer .= $data;
        $bytes_written = strlen($data);
        return true;
    }

    private function writeArray(array $data, int &$bytes_written)
    {
        $bytes_written = 0;
        if (is_array($this->getStream()->buffer) === false) // initialize buffer
            $this->getStream()->buffer = array();

        array_push($this->getStream()->buffer, $data);

        // set the bytes written
        $content_str = implode('', $data);
        $bytes_written = strlen($content_str);
        return true;
    }
}
