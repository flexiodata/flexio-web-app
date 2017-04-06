<?php
/**
 *
 * Copyright (c) 2009-2017, Gold Prairie, Inc.  All rights reserved.
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

    public static function appendPath(string $path, string $part) : string
    {
        $result = $path;
        $len = strlen($result);
        if ($len == 0)
            return $part;
        if (substr($part, $len - 1, 1) != DIRECTORY_SEPARATOR)
            $result .= DIRECTORY_SEPARATOR;
        return $result . $part;
    }
}
