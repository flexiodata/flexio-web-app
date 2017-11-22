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
    public function setMetadata(array $metadata); // array of optional metadata info that can be used for passing info (such as info from the calling context) across callbacks
    public function getMetadata();
    public function addTasks(array $tasks);       // array of tasks to process; tasks are popped off the list; when there are no tasks left, the process is done
    public function getTasks();
    public function setParams(array $arr);        // variables that are used in the processing
    public function getParams();
    public function getStdin();
    public function getStdout();
    public function setResponseCode(int $code);
    public function getResponseCode();
    public function setError(string $code = '', string $message = null, string $file = null, int $line = null, string $type = null, array $trace = null);
    public function getError();
    public function hasError();
    public function execute($func = null);        // $func is a callback that is either or a callable or array type (in the instance of a call to a class method)
}

interface IJob
{
    public static function create(array $properties = null);
    public function getProperties() : array;
    public function run(\Flexio\Jobs\IProcess $process);
}
