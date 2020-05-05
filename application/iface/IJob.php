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


interface IJob
{
    public static function validate(array $task) : array;
    public static function run(\Flexio\IFace\IProcess $process, array $task) : void;
}

