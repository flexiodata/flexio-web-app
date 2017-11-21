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
    public function addTasks(array $tasks);  // array of tasks to process; tasks are popped off the list; when there are no tasks left, the process is done
    public function getTasks();
    public function setParams(array $arr);   // variables that are used in the processing
    public function getParams();
    public function addStream(\Flexio\Base\IStream $stream); // streams that will be processed by jobs
    public function getStreams();
    public function clearStreams();
    public function setBuffer(\Flexio\Base\IStream $buffer); // stdin/stout buffer that will be processed by jobs; stdin is what's set initially; stdout is the final result
    public function getBuffer();
    public function setResponseCode(int $code);
    public function getResponseCode();
    public function setError(string $code = '', string $message = null, string $file = null, int $line = null, string $type = null, array $trace = null);
    public function getError();
    public function hasError();
    public function execute(callable $func = null);
}

interface IJob
{
    public static function create(array $properties = null);
    public function getProperties() : array;
    public function run(\Flexio\Object\Context &$context);
}
