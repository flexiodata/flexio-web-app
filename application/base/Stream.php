<?php
/**
 *
 * Copyright (c) 2016, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams, Benjamin I. Williams
 * Created:  2016-04-20
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class FileReaderWriter implements \Flexio\IFace\IStreamReader,
                                  \Flexio\IFace\IStreamWriter
{
    private $stream = null;
    private $file = null;
    private $bytes_written = 0;

    function __destruct()
    {
        $this->close();
    }

    public static function create(\Flexio\Base\Stream $stream, string $mode) : \Flexio\Base\FileReaderWriter
    {
        $object = new static();
        $object->stream = $stream;
        $object->init($stream->getTempFilePath(), $mode);
        return $object;
    }

    public function init($fspath /* can be string or handle*/, $mode = 'r+') : bool
    {
        // make sure mode is allowed value
        $mode = str_replace('b', '', $mode);
        $idx = array_search($mode, ['r','r+', 'w', 'w+', 'a', 'a+','+']);
        if ($idx === false)
            return false; // unknown mode

        if (!is_string($fspath))
        {
            $this->file = $fspath;
            return true;
        }

        $exists = file_exists($fspath);
        if (IS_DEBUG())
            $this->file = fopen($fspath, $mode . 'b');
             else
            $this->file = @fopen($fspath, $mode . 'b');

        if ($exists)
            fseek($this->file, 0, SEEK_SET);

        return true;
    }

    public function read($length = 1024)
    {
        if ($this->isOk() === false)
            return false;

        if ($length <= 0)
            return '';

        $res = fread($this->file, $length);
        if ($res === false)
            return false;

        if ($length > 0 && strlen($res) == 0)
            return false;

        return $res;
    }

    public function readline()
    {
        if ($this->isOk() === false)
            return false;

        if ($this->file)
        {
            $line = fgets($this->file);
            if ($line === false)
                return false;
            return rtrim($line, "\r\n");
        }

        return false;
    }

    public function write($data)
    {
        if ($this->bytes_written == 0)
            fseek($this->file, 0, SEEK_END);

        $res = fwrite($this->file, $data);
        if ($res === false)
            return false;

        $this->bytes_written += $res;
    }

    public function close() : bool
    {
        if ($this->isOk() === false)
            return true;

        if ($this->file)
        {
            fclose($this->file);
            $this->file = null;
        }

        return true;
    }

    private function isOk() : bool
    {
        if ($this->file !== null)
            return true;

        return false;
    }
}

class MemoryReaderWriter implements \Flexio\IFace\IStreamReader,
                                     \Flexio\IFace\IStreamWriter
{
    private $stream = null;
    private $offset = 0;

    function __destruct()
    {
        $this->close();
    }

    public static function create(\Flexio\Base\Stream $stream, string $mode) : \Flexio\Base\MemoryReaderWriter
    {
        $object = new static();
        $object->stream = $stream;
        return $object;
    }

    public function read($length = 1024)
    {
        if ($this->offset >= strlen($this->stream->buffer))
            return false;

        if ($length <= 0)
            return '';

        $str = substr($this->stream->buffer, $this->offset, $length);
        if ($str === false)
            return false;
        $this->offset += strlen($str);
        return $str;
    }

    public function readline()
    {
        $npos = strpos($this->stream->buffer, "\n", $this->offset);
        if ($npos === false)
        {
            return $this->read(strlen($this->stream->buffer));
        }
         else
        {
            $npos -= $this->offset;
            $line = $this->read($npos+1);
            if ($line === false)
                return false;
            return rtrim($line, "\r\n");
        }
    }

    public function write($data)
    {
        if (!is_string($data))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, 'Expected string value');

        $this->stream->buffer .= $data;
        return true;
    }

    public function close() : bool
    {
        return true;
    }
}

class BufferedReaderWriter implements \Flexio\IFace\IStreamReader,
                                      \Flexio\IFace\IStreamWriter
{
    private $stream;
    private $readerwriter;

    function __destruct()
    {
        $this->close();
    }

    public static function create(\Flexio\Base\Stream $stream, string $mode) : \Flexio\Base\BufferedReaderWriter
    {
        $object = new static();

        $object->stream = $stream;
        $tempfile_path = $stream->getTempFilePath();

        if (isset($tempfile_path))
            $object->readerwriter = \Flexio\Base\FileReaderWriter::create($stream, $mode);
             else
            $object->readerwriter = \Flexio\Base\MemoryReaderWriter::create($stream, $mode);

        return $object;
    }

    public function read($length = 1024)
    {
        return $this->readerwriter->read($length);
    }

    public function readline()
    {
        return $this->readerwriter->readline();
    }

    public function write($data)
    {
        if (!is_string($data))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, 'Expected string value');

        // if we're already writing using a storage file writer, use it
        if ($this->readerwriter instanceof \Flexio\Base\FileReaderWriter)
            return $this->readerwriter->write($data);

        // if we're using a memory writer, and within safe memory usage, write
        // out the data
        $curlen = $this->stream->getSize();
        $datalen = strlen($data);
        if ($curlen + $datalen <= 2000000)
            return $this->readerwriter->write($data);

        // if memory buffer is greater than set memory size, convert to using disk
        $temp_filename = \Flexio\Base\Util::generateRandomString(20);
        $temp_filename_full = \Flexio\System\System::getStoreTempFile($temp_filename);
        $this->stream->setTempFilePath($temp_filename_full);
        $this->readerwriter = \Flexio\Base\FileReaderWriter::create($this->stream, 'w+');

        // copy the existing data and clear it from the buffer
        $this->readerwriter->write($this->stream->buffer);
        $this->stream->buffer = '';

        // write out the additional data
        return $this->readerwriter->write($data);
    }

    public function close() : bool
    {
        if (!isset($this->readerwriter))
            return true;

        return $this->readerwriter->close();
    }
}


class Stream implements \Flexio\IFace\IStream
{
    // data storage variables
    public $buffer = '';            // data buffer
    private $tempfile_path = null;   // temp file path to convert buffer to if buffer gets too large

    // associated data properties
    private $properties = array();  // associated data properties

    public function __construct()
    {
        // note: default values match model defaults
        $this->properties = array();
        $this->properties['name'] = '';
        $this->properties['path'] = '';
        $this->properties['hash'] = '';
        $this->properties['mime_type'] = '';
        $this->properties['structure'] = array();
        $this->properties['file_created'] = null;
        $this->properties['file_modified'] = null;
        $this->properties['created'] = null;
        $this->properties['updated'] = null;
    }

    function __destruct()
    {
        $tempfile_path = $this->getTempFilePath();
        if (!isset($tempfile_path))
            return;

        if (file_exists($tempfile_path))
            @unlink($this->tempfile_path);
    }

    public static function create(array $properties = null) : \Flexio\Base\Stream
    {
        $object = new static();

        // structure must be valid
        if (isset($properties) && isset($properties['structure']))
        {
            // if the structure is set, make sure it's valid
            $structure = $properties['structure'];
            $structure_object = \Flexio\Base\Structure::create($structure);
            $object->properties['structure'] = $structure_object->enum();
        }

        if (isset($properties['name']))
            $object->properties['name'] = $properties['name'];
        if (isset($properties['path']))
            $object->properties['path'] = $properties['path'];
        if (isset($properties['hash']))
            $object->properties['hash'] = $properties['hash'];
        if (isset($properties['mime_type']))
            $object->properties['mime_type'] = $properties['mime_type'];
        if (isset($properties['file_created']))
            $object->properties['file_created'] = $properties['file_created'];
        if (isset($properties['file_modified']))
            $object->properties['file_modified'] = $properties['file_modified'];

        return $object;
    }

    public function set(array $properties) : \Flexio\Base\Stream
    {
        // TODO: add properties check

        // structure is stored as json string; it needs to be validated and encoded
        if (isset($properties['structure']))
        {
            // if the structure is set, make sure it's valid
            $structure = $properties['structure'];
            $structure_object = \Flexio\Base\Structure::create($structure);
            $this->properties['structure'] = $structure_object->enum();
        }

        if (isset($properties['name']))
            $this->properties['name'] = $properties['name'];
        if (isset($properties['path']))
            $this->properties['path'] = $properties['path'];
        if (isset($properties['hash']))
            $this->properties['hash'] = $properties['hash'];
        if (isset($properties['mime_type']))
            $this->properties['mime_type'] = $properties['mime_type'];
        if (isset($properties['file_created']))
            $this->properties['file_created'] = $properties['file_created'];
        if (isset($properties['file_modified']))
            $this->properties['file_modified'] = $properties['file_modified'];

        return $this;
    }

    public function get() : array
    {
        $properties = $this->properties;
        $properties['size'] = $this->getSize();
        return $properties;
    }

    public function setName(string $name) : \Flexio\Base\Stream
    {
        $properties = array();
        $properties['name'] = $name;
        return $this->set($properties);
    }

    public function getName() : string
    {
        return $this->properties['name'];
    }

    public function setPath(string $path) : \Flexio\Base\Stream
    {
        $properties = array();
        $properties['path'] = $path;
        return $this->set($properties);
    }

    public function getPath() : string
    {
        return $this->properties['path'];
    }

    public function setSize($size) : \Flexio\Base\Stream
    {
        // note: size is determined by buffer; don't allow it to be
        // set directly; currently on interface for use with with
        // \Flexio\Stream\Object
        return $this;
    }

    public function getSize() : ?int
    {
        $tempfile_path = $this->getTempFilePath();
        if (!isset($tempfile_path))
            return strlen($this->buffer);

        return filesize($tempfile_path);
    }

    public function setMimeType(string $mime_type) : \Flexio\Base\Stream
    {
        $properties = array();
        $properties['mime_type'] = $mime_type;
        return $this->set($properties);
    }

    public function getMimeType() : string
    {
        return $this->properties['mime_type'];
    }

    public function setStructure($structure) : \Flexio\Base\Stream
    {
        if (!($structure instanceof \Flexio\Base\Structure))
            $structure = \Flexio\Base\Structure::create($structure);

        $properties = array();
        $properties['structure'] = $structure->enum();
        return $this->set($properties);
    }

    public function getStructure() : \Flexio\Base\Structure
    {
        $s = $this->properties['structure'];
        $structure = \Flexio\Base\Structure::create($s);
        return $structure;
    }

    public function getReader() : \Flexio\IFace\IStreamReader
    {
        return \Flexio\Base\BufferedReaderWriter::create($this, 'r');
    }

    public function getWriter($access = 'w+') : \Flexio\IFace\IStreamWriter
    {
        return \Flexio\Base\BufferedReaderWriter::create($this, 'w+');
    }

    public function setTempFilePath(string $path) : void
    {
        $this->tempfile_path = $path;
    }

    public function getTempFilePath() : ?string
    {
        return $this->tempfile_path;
    }
}

