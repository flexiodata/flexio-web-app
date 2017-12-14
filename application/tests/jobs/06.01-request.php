<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-06-07
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
                "type": "flexio.request",
                "params": {
                    "method": "get",
                    "url": "https://raw.githubusercontent.com/flexiodata/examples/master/functions/hello-world.py"
                }
            }
        ]
        ',true);


        // TEST: Request Job

        // BEGIN TEST
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = $process->getStdout()->getReader()->read(27);
        $expected = 'def flexio_handler(context)';
        TestCheck::assertString('A.1', 'Request; check basic functionality',  $actual, $expected, $results);
    }
}
