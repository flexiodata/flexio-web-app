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
        [
            {
                "type": "flexio.echo",
                "params": {
                    "msg": "hi"
                }
            }
        ]
        ',true);


        // TEST: Echo Job

        // BEGIN TEST
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getStdout()->getReader()->read();
        $expected = 'hi';
        TestCheck::assertString('A.1', 'Echo; check basic functionality',  $actual, $expected, $results);
    }
}
