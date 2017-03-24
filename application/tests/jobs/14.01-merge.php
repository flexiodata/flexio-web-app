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


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TODO: placeholder job to test basic functionality; fill out tests


        // SETUP
        $task = \Flexio\Object\Task::create('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                    "columns": [
                        { "name": "field1", "type": "character", "width": 3, "scale": 0 },
                        { "name": "field2", "type": "character", "width": 3, "scale": 0 }
                    ],
                    "rows": [
                        ["a","b"],
                        ["b","B"],
                        ["c","b"]
                    ]
                }
            },
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                    "columns": [
                        { "name": "field1", "type": "character", "width": 3, "scale": 0 },
                        { "name": "field3", "type": "character", "width": 3, "scale": 0 }
                    ],
                    "rows": [
                        ["d","e"]
                    ]
                }
            },
            {
                "type": "flexio.merge",
                "params": {
                }
            }
        ]
        ')->get();



        // TEST: Merge Job

        // BEGIN TEST
        $params = [
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = [["a","b",null],["b","B",null],["c","b",null],["d",null,"e"]];
        TestCheck::assertArray('A.1', 'Merge Job; check basic functionality',  $actual, $expected, $results);
    }
}
