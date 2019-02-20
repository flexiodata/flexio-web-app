<?php
/**
 *
 * Copyright (c) 2009-2017, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams; David Z. Williams
 * Created:  2017-04-06
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class File
{
    public static function matchPath(string $str, string $pattern, bool $case_sensitive) : bool
    {
        return fnmatch($pattern, $str, $case_sensitive ? 0 : FNM_CASEFOLD);  // FNM_CASEFOLD triggers caseless match
    }

    public static function splitPath(string $path) : array
    {
        $path = trim($path, '/');
        if (strlen($path) == 0)
            return [];
        $urlpos = strpos($path, '://');
        if ($urlpos !== false)
        {
            $urlpart = substr($path, 0, $urlpos+3);
            $path = substr($path, $urlpos+3);
        }
        while (false !== strpos($path,'//'))
            $path = str_replace('//','/',$path);
        $path = trim($path, '/');
        $parts = explode('/', $path);
        if ($urlpos !== false)
        {
            array_unshift($parts, $urlpart);
        }
        return $parts;
    }

    public static function splitBasePathAndName(string $path) : array
    {
        $parts = \Flexio\Base\File::splitPath($path);

        $name = (count($parts) > 0) ? array_pop($parts) : '';
        $base = implode('/', $parts);
        $base = str_replace(':///', '://', $base);

        if (substr($path,0,1) == '/')
        {
            $base = '/' . $base;
        }

        return [ 'base' => $base, 'name' => $name ];
    }

    public static function getFilename(string $filename) : string
    {
        // pathinfo will parse paths differently, depending on the
        // platform being run on; test which type of parsing is being
        // used and convert the filename over to that

        $sample_path = '/etc/temp.txt';
        $sample_filename = pathinfo($sample_path, PATHINFO_FILENAME);
        $is_linux = ($sample_filename === 'temp');

        if ($is_linux === true)
            $filename = str_replace("\\", "/", $filename); // parse using linux-style paths
             else
            $filename = str_replace("/", "\\", $filename); // parse using windows-style paths

        return pathinfo($filename, PATHINFO_FILENAME);
    }

    public static function getFileExtension(string $filename) : string
    {
        // pathinfo will parse paths differently, depending on the
        // platform being run on; test which type of parsing is being
        // used and convert the filename over to that

        $sample_path = '/etc/temp.txt';
        $sample_filename = pathinfo($sample_path, PATHINFO_EXTENSION);
        $is_linux = $sample_filename === 'txt';

        if ($is_linux === true)
            $filename = str_replace("\\", "/", $filename); // parse using linux-style paths
             else
            $filename = str_replace("/", "\\", $filename); // parse using windows-style paths

        return pathinfo($filename, PATHINFO_EXTENSION);
    }

    public static function createTempFile(string $prefix = '', string $extension = 'tmp') : string
    {
        $fname = tempnam(sys_get_temp_dir(), $prefix);
        $new_name = str_replace('.tmp', '_tmp', $fname) . ".$extension";
        rename($fname, $new_name);
        return $new_name;
    }

    // shorthand for getting a temporary file
    public static function getTempFilename(string $ext = null) : string
    {
        $temp_file = tempnam(sys_get_temp_dir(), 'fx_');
        if (isset($ext) && strlen($ext) > 0)
        {
            @unlink($temp_file);
            $temp_file .= ('.' . $ext);

            $fp = fopen($temp_file, "w");
            fclose($fp);

            chmod($temp_file, 0600);
        }

        return $temp_file;
    }

    public static function appendPath(string $path, string $part, string $delimiter = DIRECTORY_SEPARATOR) : string
    {
        $result = $path;
        $len = strlen($result);
        if ($len == 0)
            return $part;
        if (substr($part, $len - 1, 1) != $delimiter)
            $result .= $delimiter;
        return $result . $part;
    }

    public static function read($file_name) : string
    {
        $f = @fopen($file_name, 'rb');
        if (!$f)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $buf = '';
        while (!feof($f))
        {
            $buf .= fread($f, 65535);
        }

        fclose($f);
        return $buf;
    }
}
