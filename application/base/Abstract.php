<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2017-11-17
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Base;


interface IStream
{
    public function getImpl();   // returns the object implementing interface; (internal helper)
    public function copy();
    public function copyOver(\Flexio\Base\IStream $dest);
    public function set(array $properties);
    public function get();
    public function setName(string $name);
    public function getName();
    public function setPath(string $path);
    public function getPath();
    public function setSize($size);
    public function getSize();
    public function getRowCount();
    public function setMimeType(string $mime_type);
    public function getMimeType();
    public function setStructure($structure);
    public function getStructure();
    public function getFileInfo();
    public function content(int $start = 0, int $limit = PHP_INT_MAX, int $readsize = 1024 /* testing */);
    public function getReader() : \Flexio\Base\IStreamReader;
    public function getWriter() : \Flexio\Base\IStreamWriter;
}

interface IStreamReader
{
    public function read($length = 1024);
    public function readRow();
    public function getRows(int $offset, int $limit);
    public function close();
}

interface IStreamWriter
{
    public function write($data);
    public function getBytesWritten();
    public function close();
}

