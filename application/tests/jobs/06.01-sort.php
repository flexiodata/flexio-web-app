<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-10-01
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $task = \Flexio\Object\Task::create('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                    "columns": [
                        { "name": "field1", "type": "character", "width": 3, "scale": 0 }
                    ],
                    "rows": [
                        ["a"],
                        ["b"],
                        ["c"]
                    ]
                }
            },
            {
                "type": "flexio.sort",
                "params": {
                    "order": "${order}"
                }
            }
        ]
        ')->get();



        // TEST: Sort Job; missing parameters

        // BEGIN TEST
        $params = [
            "order" => null
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('A.1', 'Sort Job; fail when a job definition is invalid',  $actual, $expected, $results);



        // TEST: Sort Job; missing column parameters

        // BEGIN TEST
        $params = [
            "order" => null
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.1', 'Sort Job; fail when there are no order columns',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "order" => [
                [
                    "expression" => null
                ]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.2', 'Sort Job; fail when there\'s an invalid expression',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "order" => [
                [
                    "expression" => ""
                ]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.3', 'Sort Job; fail when there\'s an invalid expression',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "order" => [
                [
                    "expression" => "c"
                ]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.4', 'Sort Job; fail when there\'s an invalid expression',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "order" => [
                [
                    "expression" => "field1",
                    "direction" => null
                ]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.5', 'Sort Job; fail when there\'s an invalid direction',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "order" => [
                [
                    "expression" => "field1",
                    "direction" => "d"
                ]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.6', 'Sort Job; fail when there\'s an invalid direction',  $actual, $expected, $results);



        // TEST: Sort Job; basic functional test

        // BEGIN TEST
        $params = [
            "order" => [
                [
                    "expression" => "field1",
                    "direction" => "asc"
                ]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = [["a"],["b"],["c"]];
        TestCheck::assertString('C.1', 'Sort Job; make sure data is ordered correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "order" => [
                [
                    "expression" => "field1",
                    "direction" => "desc"
                ]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = [["c"],["b"],["a"]];
        TestCheck::assertString('C.2', 'Sort Job; make sure data is ordered correctly',  $actual, $expected, $results);
    }
}
