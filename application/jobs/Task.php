<?php
/**
 *
 * Copyright (c) 2020, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2020-02-16
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// DESCRIPTION:
{
    "op": "fail",  // string, required
    "code": "",    // string
    "message": ""  // string
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('required' => true,  'enum' => ['fail']),
        'code'       => array('required' => false, 'type' => 'string'),
        'message'    => array('required' => false, 'type' => 'string')
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
*/

class Task implements \Flexio\IFace\IJob
{
    private $properties = array();

    public static function validate(array $task) : array
    {
        try
        {
            $job = \Flexio\Jobs\Factory::getJobClass($task);
            return $job::validate($task);
        }
        catch (\Error $e)
        {
            $errors = array();
            $errors[] = array('code' => \Flexio\Base\Error::INVALID_SYNTAX, 'message' => 'Missing or invalid task operation or parameter.');
            return $errors;
        }
    }

    public static function run(\Flexio\IFace\IProcess $process, array $task) : void
    {
        // helper function for instantiating a job class from a task;
        // the task can take two form: 1. a non-associative array of
        // task arrays each with an "op" parameter, or 2. a single task
        // array with an "op" parameter; in the case of #1, the tasks
        // are packaged into a sequence job so that the output of each
        // task is passed to the input of the next task; in the case
        // of #2, the task is run as-is

        // example 1:
        // [
        //     [
        //         "op" => "read",
        //         "path" => "<path>"
        //     ],
        //     [
        //         "op" => "write",
        //         "path" => "<path>"
        //     ]
        // ]
        // example 2:
        // [
        //     "op" => "read",
        //     "path" => "<path>"
        // ]

        // if we have an array of tasks, package them up in a sequence job
        if (\Flexio\Base\Util::isAssociativeArray($task) === false)
            $task = array('op' => 'sequence', 'params' => array('items' => $task));

        // run the task
        $job = \Flexio\Jobs\Factory::getJobClass($task);
        $job::run($process, $task);
    }
}
