<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-03-31
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

        $script = <<<EOD
import flexioext
flexioext.output.content_type = "text/plain"
flexioext.output.stream.write("Hello, world.")
EOD;
        $task = json_decode('
        [
            {
                "type": "flexio.execute",
                "params": {
                    "lang": "python",
                    "code": "'.base64_encode($script).'"
                }
            }
        ]
        ',true);



        // TEST: Execute Job
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $result = TestUtil::getProcessResult($process,0,50);
        $actual = is_array($result) && isset($result[0]) ? $result[0] : '';
        $expected = 'Hello, world.';
        TestCheck::assertString('A.1', 'Execute Job; check basic functionality',  $actual, $expected, $results, TestCheck::FLAG_ERROR_SUPPRESS);
    }
}
