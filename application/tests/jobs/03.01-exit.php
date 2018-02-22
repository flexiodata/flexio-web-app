<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-11-27
 *
 * @package flexio
 * @subpackage Tests
 */


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TODO: placeholder job to test basic functionality; fill out tests

        // SETUP
        $task = json_decode('
        {
            "op": "exit",
            "params": {
                "code": 404
            }
        }
        ',true);


        // TEST: Exit Job

        // BEGIN TEST
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = $process->getResponseCode();
        $expected = 404;
        \Flexio\Tests\Check::assertNumber('A.1', 'Exit; check basic functionality',  $actual, $expected, $results);
    }
}
