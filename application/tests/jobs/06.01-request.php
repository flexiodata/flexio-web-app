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
        {
            "op": "request",
            "params": {
                "method": "get",
                "url": "https://raw.githubusercontent.com/flexiodata/functions/master/python/hello-world.py"
            }
        }
        ',true);


        // TEST: Request Job

        // BEGIN TEST
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = $process->getStdout()->getReader()->read(27);
        $expected = 'def flexio_handler(context)';
        \Flexio\Tests\Check::assertString('A.1', 'Request; check basic functionality',  $actual, $expected, $results);
    }
}
