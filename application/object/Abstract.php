<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-10-29
 *
 * @package flexio
 * @subpackage Object
 */


declare(strict_types=1);
namespace Flexio\Object;


interface IObject
{
    public static function create(array $properties = null);
    public static function load(string $identifier);
    public function copy();

    public function delete();
    public function set(array $properties);
    public function get();

    public function getEid();
    public function getType();

    public function setStatus(string $status);
    public function getStatus();

    public function setOwner(string $user_eid);
    public function getOwner();
    public function getFollowers();

    public function allows(string $access_code, string $action);
    public function grant(string $access_code, string $access_type, array $actions);
    public function revoke(string $access_code, string $access_type, array $actions = null);

    public function setRights(array $rights);
    public function getRights();
}

interface IStream
{
    public function getImpl();   // returns the object implementing interface; (internal helper)
    public function copy();
    public function copyOver(\Flexio\Object\IStream $dest);
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
    public function getReader() : \Flexio\Object\IStreamReader;
    public function getWriter() : \Flexio\Object\IStreamWriter;
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

