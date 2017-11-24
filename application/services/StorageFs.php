<?php
/**
 *
 * Copyright (c) 2013, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2013-01-10
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


class StorageFileReaderWriter implements \Flexio\Base\IStreamReader, \Flexio\Base\IStreamWriter
{
    private $fspath = null;
    private $file = null;

    function __destruct()
    {
        $this->close();
    }

    public static function create($fspath) : \Flexio\Services\StorageFileReaderWriter
    {
        $object = new static();
        $object->fspath = $fspath;

        $mode = file_exists($fspath) ? 'r+b' : 'w+b';

        if (IS_DEBUG())
            $object->file = fopen($fspath, $mode);
             else
            $object->file = @fopen($fspath, $mode);
        
        return $object;
    }

    public function read($length = 1024)
    {
        if ($this->isOk() === false)
            return false;

        return fread($this->file, $length);
    }

    public function readRow()
    {
        if ($this->isOk() === false)
            return false;

        return false;
    }

    public function getRows(int $offset, int $limit)
    {
        if ($this->isOk() === false)
            return false;

        return false; // $this->reader->getRows($offset, $limit);
    }

    public function getRowCount() : int
    {
        return 0;
       // return $this->reader->getRowCount();
    }

    public function close() : bool
    {
        if ($this->isOk() === false)
            return true;

        fclose($this->file);
        $this->file = null;
        return true;
    }

    private function isOk() : bool
    {
        if ($this->file !== null)
            return true;

        return false;
    }


    // \Flexio\Base\IStreamWriter

    private $first_write = true;
    private $bytes_written = 0;

    public function write($data)
    {
        if ($this->bytes_written == 0)
        {
            fseek($this->file, 0, SEEK_END);
        }

        $res = fwrite($this->file, $data);
        var_dump($res);

        if ($res === false)
        {
            return false; // error
        }

        $this->bytes_written += $res;
    }

    public function getBytesWritten()
    {
        return $this->bytes_written;
    }
}



class StorageFs
{
    private $base_path = null;

    public static function create(array $params) : \Flexio\Services\StorageFs
    {
        $service = new self;
        if ($service->initialize($params) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        return $service;
    }

    private function initialize($params)
    {
        if (!isset($params['base_path']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER, "'base_path' must be specified in StorageFs::create()");
        if (!is_dir($params['base_path']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER, "'base_path' does not exist");

        $this->base_path = $params['base_path'];

        return true;
    }

    public function createFile(string $path) : bool
    {
        $fspath = self::getFsPath($path);

        if (IS_DEBUG())
            $f = fopen($fspath, 'w+');
             else
            $f = @fopen($fspath, 'w+');
        
        if (!$f)
        {
            return false;
        }

        fclose($f);
        return true;
    }

    public function open(string $path) : \Flexio\Services\StorageFileReaderWriter
    {
        $fspath = self::getFsPath($path);

        if (!@file_exists($fspath))
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT, "File '$path' does not exist");
        }
        
        return StorageFileReaderWriter::create($fspath);
    }


    public function createDirectory(string $path) : bool
    {
        $fspath = self::getFsPath($path);

        return @mkdir($fspath, 0750, true) ? true : false;
    }



    public function deleteFile(string $path) : bool
    {
        $fspath = self::getFsPath($path);

        // TODO: implement
        return false;
    }

    public function move(string $old_path, string $new_path) : bool
    {
        $old_fspath = self::getFsPath($old_path);
        $new_fspath = self::getFsPath($new_path);

        if (!@file_exists($old_fspath))
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT, "File '$old_fspath' does not exist");
        }

        if (@file_exists($new_fspath))
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::OBJECT_ALREADY_EXISTS, "Target file '$new_fspath' already exists");
        }

        if (!@rename($old_fspath, $new_fspath))
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, "Unable to rename file");
        }

        return true;
    }

    public function list(string $path)
    {
        $fspath = self::getFsPath($path);

        $arr = array();
        if ($handle = opendir($fspath))
        {
            while (false !== ($file = readdir($handle)))
            {
                $combined = $path;
                if (substr($combined, -1) != '/')
                    $combined .= '/';
                $combined .= $file;

                $isdir = is_dir($fspath . DIRECTORY_SEPARATOR . $file);

                $f = array(
                    'name' => $file,
                    'path' => $combined,
                    'type' => ($isdir ? 'DIR' : 'FILE'),
                    'size' => null,
                    'modified' => null,
                    'is_dir' => $isdir
                );

                $arr[] = $file;
            }
            closedir($handle);
        }

        return $arr;
    }

    public function exists(string $path) : bool
    {
        $fspath = self::getFsPath($path);

        if (IS_DEBUG())
        {
            if (file_exists($fspath))
                return true;
            else if (is_dir($fspath))
                return true;
            else
                return false;
        }
         else
        {
            if (@file_exists($fspath))
                return true;
            else if (@is_dir($fspath))
                return true;
            else
                return false;
        }
    }



    private function getFsPath(string $path) : string
    {
        // trim off trailing/preceding slashes and whitespace
        $path = trim($path, "\ \t\n\r\0\x0B");

        foreach (explode('/', $path) as $part)
        {
            if ($part == '..')
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, "relative paths not allowed");
        }

        $str = $this->base_path . DIRECTORY_SEPARATOR . $path;

        if (DIRECTORY_SEPARATOR != '/')
            $str = str_replace('/', DIRECTORY_SEPARATOR, $str);
 
        return $str;
    }
}
