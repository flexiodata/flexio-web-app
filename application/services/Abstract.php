<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-10-28
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


interface IConnection
{
    public static function create(array $params = null);
    public function connect(array $params) : bool;
    public function isOk() : bool;
    public function close();
}

interface IFileSystem
{
    public function listObjects(string $path = '') : array;
    public function exists(string $path) : bool;

    public function read(array $params, callable $callback);
    public function write(array $params, callable $callback);
}
