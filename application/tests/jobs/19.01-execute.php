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
def flexio_handler(input,output):
    output.content_type = "text/plain"
    output.write("Hello, world.")
EOD;
        $task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "name": "table",
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                    "columns": [
                        { "name": "c1", "type": "character", "width": 3 }
                    ],
                    "content": [
                        {"c1" : "a"},
                        {"c1" : "b"}
                    ]
                }
            },
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
        $actual = $process->getStdout()->content(0,50);
        $expected = 'Hello, world.';
        TestCheck::assertString('A.1', 'Execute Job; check basic functionality',  $actual, $expected, $results);
    }
}
