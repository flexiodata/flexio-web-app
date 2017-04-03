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


class XferFsLocal
{
    public static function isReady() : bool
    {
        // filesystem is always ready
        return true;
    }

    public static function createDirectory(string $path) : bool
    {
        $full = self::makePath($path);

        return @mkdir($full, 0750, true) ? true : false;
    }

    public static function createFile(string $path, string $import_file) : bool
    {
        $full = self::makePath($path);

        return copy($import_file, $full);
    }

    public static function deleteFile(string $path) : bool
    {
        $full = self::makePath($path);

        // TODO: implement
        return false;
    }

    public static function getDirectories(string $path) : array
    {
        $full = self::makePath($path);

        $arr = array();
        if ($handle = opendir($full))
        {
            while (false !== ($file = readdir($handle))) {
                if (substr($file, 0, 1) != '.' && is_dir($full . DIRECTORY_SEPARATOR . $file))
                    $arr[] = $file;
            }
            closedir($handle);
        }

        return $arr;
    }

    public static function getObjects(string $path) : array
    {
        $full = self::makePath($path);

        $arr = array();
        if ($handle = opendir($full))
        {
            while (false !== ($file = readdir($handle))) {
                if (substr($file, 0, 1) != '.' && !is_dir($full . DIRECTORY_SEPARATOR . $file))
                    $arr[] = $file;
            }
            closedir($handle);
        }

        return $arr;
    }

    public static function getDirectoryListing(string $path, string $initial_marker = null) : array
    {
        die("TODO: implement");
        return array();
    }

    public static function fileExists(string $path) : bool
    {
        $full = self::makePath($path);

        if (@file_exists($full))
            return true;
        if (@is_dir($full))
            return true;
        return false;
    }

    public static function exportFile(string $path, string $local_path) : bool
    {
        $full = self::makePath($path);
        return @copy($full, $local_path);
    }

    private static function makePath(string $path) : string
    {
        global $g_config;

        $str = $g_config->localfs_base_path . DIRECTORY_SEPARATOR . $path;

        if (DIRECTORY_SEPARATOR != '/')
            $str = str_replace('/', DIRECTORY_SEPARATOR, $str);

        return $str;
    }
}
