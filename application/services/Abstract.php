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


namespace Flexio\Services;


interface IConnection
{
    public static function create($params = null);
    public function connect($params);
    public function isOk();
    public function close();

    public function listObjects($path);
    public function exists($path);
    public function getInfo($path);

    public function read($params, $callback);
    public function write($params, $callback);
}
