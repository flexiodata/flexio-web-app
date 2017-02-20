<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-02-12
 *
 * @package flexio
 * @subpackage Services
 */


class DatastoreReader
{
    private $postgres = null;
    private $path = '';
    private $stream = null;
    private $iter = null;
    private $mime_type = '';
    private $buffer = '';
    private $structure = null;
    private $columns = null;
    private $fileinfo = null;

    public static function create($postgres, $path, $structure = null)
    {
        $reader = new self;
        $reader->postgres = $postgres;
        $reader->path = $path;
        $reader->structure = $structure;
        $reader->columns = null;
        return $reader;
    }

    public function describeTable()
    {
        // if the structure is cached, return it
        if ($this->structure)
            return $this->structure;

        // get the structure
        $struct = $this->postgres->describeTable($this->path);

        // remove the xdrowid field
        $structure_local = array();
        foreach ($struct as $s)
        {
            if (isset($s['name']) && $s['name'] != 'xdrowid')
                $structure_local[] = $s;
        }

        $this->structure = $structure_local;
        return $this->structure;
    }

    public function read($length)
    {
        if (!is_integer($length))
            return '';

        if (!$this->stream)
        {
            if ($this->isTable())
            {
                $structure = $this->describeTable($this->path);
                if (!$structure)
                    return '';

                $iter = $this->postgres->query(['table' => $this->path]);
                if (!$iter)
                    return '';

                $this->populateColumnNames();
                $this->stream = new CsvStream($iter, $this->columns);
            }
             else if ($this->isStream())
            {
                $this->stream = $this->postgres->openFile($this->path);
                if (!$this->stream)
                    return '';
            }
        }

        if ($length == 0)
            return '';

        return $this->stream->read($length);
    }

    public function fetchRow()
    {
        if (!isset($this->iter))
        {
            $this->iter = $this->postgres->query(['table' => $this->path]);
            if (!$this->iter)
                return null;

            $this->populateColumnNames();
        }

        $row = $this->iter->fetchRow();
        if (!$row)
            return null;

        $row = Util::mapArray($this->columns, $row);
        return $row;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getFileInfo()
    {
        if (is_null($this->fileinfo))
            $this->fileinfo = $this->postgres->getFileInfo($this->path);

        return $this->fileinfo;
    }

    public function getStreamSize()
    {
        // make sure stream is initialized
        $this->read(0);

        return $this->streamsize;
    }

    public function getStreamFilePointer()
    {
        // make sure it's initialized
        $this->read(0);

        if (!$this->stream)
            return null;

        return $this->stream->f;
    }

    public function close()
    {
        $this->stream = null;
    }

    public function quote($str)
    {
        return $this->postgres->getPDO()->quote($str);
    }

    private function isStream()
    {
        $finfo = $this->getFileInfo();
        if ($finfo && $finfo['type'] == 'stream')
            return true;
             else
            return false;
    }

    private function isTable()
    {
        $finfo = $this->getFileInfo();
        if ($finfo && $finfo['type'] == 'table')
            return true;
             else
            return false;
    }

    private function populateColumnNames()
    {
        if (isset($this->columns))
            return;

        $this->structure = $this->describeTable();
        foreach ($this->structure as $s)
        {
            $this->columns[$s['name']] = null;
        }
    }

    private function initialize()
    {
    }

    private function connect()
    {
        return true;
    }
}



class CsvStream
{
    public $iter = null;
    public $structure = null;
    public $buf = '';
    public $columns = null;

    public function __construct($iter, $columns)
    {
        $this->iter = $iter;
        $this->columns = $columns;

        // write out the first row
        $names = array_keys($columns);
        $this->buf .= (self::arrayToCsv($names) . "\r\n");
    }

    public function read($length)
    {
        $done = false;

        while (strlen($this->buf) < $length)
        {
            // get the rows
            $row = $this->iter->fetchRow();
            if (!$row)
            {
                $done = true;
                break;
            }

            $row = Util::mapArray($this->columns, $row);
            $this->buf .= (self::arrayToCsv(array_values($row)) . "\r\n");
        }

        if ($done)
        {
            $ret = $this->buf;
            $this->buf = '';
            return $ret;
        }
         else
        {
            $ret = substr($this->buf, 0, $length);
            $this->buf = substr($this->buf, $length);
            return $ret;
        }
    }

    private static function arrayToCsv($arr)
    {
        return join(',', $arr);
    }
}
