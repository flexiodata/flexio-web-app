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

    public static function getDirectories(string $path) : array
    {
        $fspath = self::getFsPath($path);

        $arr = array();
        if ($handle = opendir($fspath))
        {
            while (false !== ($file = readdir($handle))) {
                if (substr($file, 0, 1) != '.' && is_dir($fspath . DIRECTORY_SEPARATOR . $file))
                    $arr[] = $file;
            }
            closedir($handle);
        }

        return $arr;
    }

    public static function getObjects(string $path) : array
    {
        $fspath = self::getFsPath($path);

        $arr = array();
        if ($handle = opendir($fspath))
        {
            while (false !== ($file = readdir($handle))) {
                if (substr($file, 0, 1) != '.' && !is_dir($fspath . DIRECTORY_SEPARATOR . $file))
                    $arr[] = $file;
            }
            closedir($handle);
        }

        return $arr;
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
        if (@is_dir($fspath))
            return true;
        return false;
    }




    
    private static function getFsPath(string $path) : string
    {
        global $g_config;

        $str = $g_config->localfs_base_path . DIRECTORY_SEPARATOR . $path;

        if (DIRECTORY_SEPARATOR != '/')
            $str = str_replace('/', DIRECTORY_SEPARATOR, $str);

        return $str;
    }
}
