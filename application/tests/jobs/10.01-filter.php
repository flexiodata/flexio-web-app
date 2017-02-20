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
                    "columns": [
                        { "name": "field1", "type": "character", "width": 3, "scale": 0 },
                        { "name": "field2", "type": "character", "width": 3, "scale": 0 }
                    ],
                    "rows": [
                        ["a","b"],
                        ["b","b"],
                        ["c","b"]
                    ]
                }
            },
            {
                "type": "flexio.filter",
                "params": {
                    "where": "field1 = \'b\'"
                }
            }
        ]
        ')->get();



        // TEST: Filter Job

        // BEGIN TEST
        $params = [
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = [["b","b"]];
        TestCheck::assertString('A.1', 'Filter Job; check basic functionality',  $actual, $expected, $results);
    }
}
