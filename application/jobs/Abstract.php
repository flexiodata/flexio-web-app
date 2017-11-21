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


declare(strict_types=1);
namespace Flexio\Jobs;


interface IProcess
{
    public function addTasks(array $tasks);
    public function getTasks();
    public function setStdin(\Flexio\Base\IStream $stdin);
    public function getStdout();
    public function setResponseCode(int $code);
    public function getResponseCode();
    public function setError(string $code = '', string $message = null, string $file = null, int $line = null, string $type = null, array $trace = null);
    public function getError();
    public function hasError();
    public function setBuffer(\Flexio\Base\IStream $buffer);
    public function getBuffer();
    public function execute(callable $func = null);
}

interface IJob
{
    public static function create(array $properties = null);
    public function getProperties() : array;
    public function run(\Flexio\Object\Context &$context);
}
