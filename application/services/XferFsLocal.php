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


class XferFsLocal
{
    public static function isReady()
    {
        // filesystem is always ready
        return true;
    }

    public static function createDirectory($path)
    {
        $full = XferFsLocal::makePath($path);

        return @mkdir($full, 0750, true) ? true : false;
    }

    public static function createFile($path, $import_file)
    {
        $full = XferFsLocal::makePath($path);

        return copy($import_file, $full);
    }

    public static function deleteFile($path)
    {
        $full = XferFsLocal::makePath($path);

        // TODO: implement
        return false;
    }

    public static function getDirectories($path)
    {
        $full = XferFsLocal::makePath($path);

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

    public static function getObjects($path)
    {
        $full = XferFsLocal::makePath($path);

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

    public static function getDirectoryListing($path, $initial_marker = null)
    {
        die("TODO: implement");
    }

    public static function fileExists($path)
    {
        $full = XferFsLocal::makePath($path);

        if (@file_exists($full))
            return true;
        if (@is_dir($full))
            return true;
        return false;
    }

    public static function exportFile($path, $local_path)
    {
        $full = XferFsLocal::makePath($path);
        return @copy($full, $local_path);
    }

    private static function makePath($path)
    {
        global $g_config;

        $str = $g_config->localfs_base_path . DIRECTORY_SEPARATOR . $path;

        if (DIRECTORY_SEPARATOR != '/')
            $str = str_replace('/', DIRECTORY_SEPARATOR, $str);

        return $str;
    }
}
