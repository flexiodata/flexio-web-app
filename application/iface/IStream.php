<?php
/**
 *
 * Copyright (c) 2017, Flex Research LLC. All rights reserved.
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
    public function set(array $properties);
    public function get();
    public function setName(string $name);
    public function getName();
    public function setPath(string $path);
    public function getPath();
    public function setSize($size);
    public function getSize();
    public function setMimeType(string $mime_type);
    public function getMimeType();
    public function setStructure($structure);
    public function getStructure();
    public function getReader() : \Flexio\IFace\IStreamReader;
    public function getWriter($mode = 'w+') : \Flexio\IFace\IStreamWriter;
}
