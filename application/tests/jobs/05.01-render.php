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
            "op": "render",
            "params": {
                "url": "https://www.flex.io",
                "width": 300,
                "height": 200
            }
        }
        ',true);


        // TEST: Render Job

        // BEGIN TEST
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = $process->getStdout()->getMimeType();
        $expected = 'image/png';
        TestCheck::assertString('A.1', 'Render; check basic functionality',  $actual, $expected, $results);
    }
}
