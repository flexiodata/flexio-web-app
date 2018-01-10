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


interface IFileSystem
{
    const FLAG_RANDOM_ACCESS = 0x01; 
    
    public function getFlags() : int;

    public function list(string $path = '', array $options = []) : array;
    public function exists(string $path) : bool;

    public function createFile(string $path, array $properties = []) : bool;
    public function open($path) :  \Flexio\Iface\IStream;
    public function read(array $params, callable $callback);
    public function write(array $params, callable $callback);
}

