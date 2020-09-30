<?php
/**
 *
 * Copyright (c) 2020, Flex Research LLC. All rights reserved.
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
    "op": ""      // string; any valid job followed by any additional parameters
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('required' => true)
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
        // run the task
        $job = \Flexio\Jobs\Factory::getJobClass($task);
        $job::run($process, $task);
    }
}
