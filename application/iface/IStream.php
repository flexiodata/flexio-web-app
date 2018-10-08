<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-12-11
 *
 * @package flexio
 * @subpackage IFace
 */


declare(strict_types=1);
namespace Flexio\IFace;


interface IStream
{
    public function getImpl();   // returns the object implementing interface; (internal helper)
    public function copyFrom(\Flexio\IFace\IStream $source);
    public function set(array $properties);
    public function get();
    public function setName(string $name);
    public function getName();
    public function setPath(string $path);
    public function getPath();
    public function setSize($size);
    public function getSize();
    public function isTable();
    public function getRowCount();
    public function setMimeType(string $mime_type);
    public function getMimeType();
    public function setStructure($structure);
    public function getStructure();
    public function getFileInfo();
    public function getReader() : \Flexio\IFace\IStreamReader;
    public function getWriter($mode = 'w+') : \Flexio\IFace\IStreamWriter;
}
