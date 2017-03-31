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
 * @subpackage Jobs
 */


namespace Flexio\Jobs;


interface IJob
{
    public static function create(\Flexio\Object\Process $process = null, array $properties = null);
    public function getProcess();
    public function getType() : string;
    public function getProperties() : array;

    public function getInput() : \Flexio\Object\Collection;
    public function getOutput() : \Flexio\Object\Collection;

    public function run();
}
