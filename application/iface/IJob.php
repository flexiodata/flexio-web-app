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


interface IJob
{
    public static function create(array $task);
    public function getProperties() : array;
    public function validate() : array;
    public function run(\Flexio\IFace\IProcess $process) : void;
}

