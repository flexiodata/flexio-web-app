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
 * @subpackage Services
 */


interface IJob
{
    public static function create($process = null, $properties = null);
    public function getProcess();
    public function getType();
    public function getProperties();

    public function getInput();
    public function getOutput();

    public function run();
}
