<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2018-03-02
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;



class TableToCsvCallbackAdaptor
{
    private $table_data_callback;
    private $fp = false;

    function __construct(array $structure, callable $table_data_callback)
    {
        $this->table_data_callback = $table_data_callback;

        // write out field header
        $this->fp = fopen('php://memory', 'w');
        $field_names = array_column($structure, 'name');
        fputcsv($this->fp, $field_names);
    }

    function __invoke($length) // TODO: add return type
    {
        if ($this->fp === false)
            return false;

        while (ftell($this->fp) < $length)
        {
            $row = ($this->table_data_callback)();
            if ($row === false)
            {
                fseek($this->fp, 0);
                $contents = stream_get_contents($this->fp);
                fclose($this->fp);
                $this->fp = false;
                return $contents;
            }

            $row = array_values($row);
            foreach ($row as &$v)
            {
                if ($v === true)
                    $v = 'true';
                else if ($v === false)
                    $v = 'false';
            }
            fputcsv($this->fp, $row);
        }

        fseek($this->fp, 0);
        $contents = stream_get_contents($this->fp);
        fclose($this->fp);

        $this->fp = fopen('php://memory', 'w');
        fwrite($this->fp, substr($contents, $length));
        return substr($contents, 0, $length);
    }
}

class Util
{
    public static function tableToCsvCallbackAdaptor(array $structure, callable $table_data_callback)
    {
        return new TableToCsvCallbackAdaptor($structure, $table_data_callback);
    }

    public static function mergePath(string $basepath, string $path) : string
    {
        if ($path === '')
            $path = '/';

        if ($basepath === '/' || $basepath === '')
        {
            while (false !== strpos($path,'//'))
                $path = str_replace('//','/',$path);
            return rtrim($path, '/');
        }

        $parts = explode('/', trim($path,'/'));
        $out = [];
        foreach ($parts as $part)
        {
            if ($part === '.' || $part == '')
                continue;
            if ($part === '..')
            {
                array_pop($out);
                continue;
            }

            $out[] = $part;
        }

        $path = implode('/', $out);

        $res = $basepath;
        if (substr($res, -1) == '/')
            $res = substr($res, 0, strlen($res)-1);

        if (strlen($path) == 0 || $path == '/')
            return $res;

        if ($path[0] == '/')
            return $res . $path;
             else
            return $res . '/' . $path;
    }

}
