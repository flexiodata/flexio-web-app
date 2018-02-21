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
                "op": "create",
                "params": {
                    "content_type": "'.\Flexio\Base\ContentType::FLEXIO_TABLE.'",
                    "columns": [
                        { "name": "field1", "type": "character", "width": 3, "scale": 0 },
                        { "name": "field2", "type": "character", "width": 3, "scale": 0 }
                    ],
                    "content": [
                        ["a","b"],
                        ["b","B"],
                        ["c","b"]
                    ]
                }
            },
            {
                "op": "limit",
                "params": {
                    "value": 1
                }
            }
        ]
        ',true);



        // TEST: Limit Job

        // BEGIN TEST
        $process = \Flexio\Jobs\Process::create()->execute($task[0])->execute($task[1]);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["a","b"]];
        TestCheck::assertString('A.1', 'Limit Job; check basic functionality',  $actual, $expected, $results);
    }
}
