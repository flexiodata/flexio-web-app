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


class StorageFileReader implements \Flexio\Base\IStreamReader
{
    private $fspath = null;
    private $file = null;

    function __destruct()
    {
        $this->close();
    }

    public static function create($fspath) : \Flexio\Object\StreamReader
    {
        $object = new static();
        $object->fspath = $fspath;

        if (IS_DEBUG())
            $this->file = fopen($fspath, 'rb');
             else
            $this->file = @fopen($fspath, 'rb');

        if ($this->file === false)
            return false;
        
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
        return $this->reader->getRowCount();
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
            return false;

        return true;
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

    public function open(string $path) : \Flexio\Base\IStreamReader
    {

    }


    public function createDirectory(string $path) : bool
    {
        $fspath = self::getFsPath($path);

        return @mkdir($fspath, 0750, true) ? true : false;
    }

    public function createFile(string $path, string $import_file) : bool
    {
        $fspath = self::getFsPath($path);

        return copy($import_file, $fspath);
    }

    public function deleteFile(string $path) : bool
    {
        $fspath = self::getFsPath($path);

        // TODO: implement
        return false;
    }

    public function list($path)
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

        if (@file_exists($fspath))
            return true;
        else if (@is_dir($fspath))
            return true;
        else
            return false;
    }



    private function getFsPath(string $path) : string
    {
        $str = $this->base_path . DIRECTORY_SEPARATOR . $path;

        if (DIRECTORY_SEPARATOR != '/')
            $str = str_replace('/', DIRECTORY_SEPARATOR, $str);

        // resolve all .. and .
        $str = realpath($str);

        // make sure the path is still under the base path
        if (substr($str, 0, strlen($this->base_path)) != $this->base_path)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, "specified path does not fall within filesystem scope");

        return $str;
    }
}
