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

    public static function createDirectory(string $path) : bool
    {
        $fspath = self::getFsPath($path);

        return @mkdir($fspath, 0750, true) ? true : false;
    }

    public static function createFile(string $path, string $import_file) : bool
    {
        $fspath = self::getFsPath($path);

        return copy($import_file, $fspath);
    }

    public static function deleteFile(string $path) : bool
    {
        $fspath = self::getFsPath($path);

        // TODO: implement
        return false;
    }

    public static function list($path)
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

    public static function exists(string $path) : bool
    {
        $fspath = self::getFsPath($path);

        if (@file_exists($fspath))
            return true;
        else if (@is_dir($fspath))
            return true;
        else
            return false;
    }



    private static function getFsPath(string $path) : string
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
