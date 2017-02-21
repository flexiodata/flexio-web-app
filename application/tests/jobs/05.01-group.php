<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-09-08
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TODO: in output columns, check for existence of name, formula, and expression


        // SETUP
        $task = \Flexio\Object\Task::create('
        [
            '.TestSample::getCreateSampleDataTask().',
            {
                "type": "flexio.group",
                "params": {
                    "group": "${group}",
                    "columns": "${columns}"
                }
            }
        ]
        ')->get();



        // TEST: Group Job; missing parameters

        // BEGIN TEST
        $params = [
            "group" => null,
            "columns" => null
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('A.1', 'Group Job; fail when a job definition is invalid',  $actual, $expected, $results);



        // TEST: Group Job; missing column parameters

        // BEGIN TEST
        $params = [
            "group" => ["char_1a"],
            "columns" => null
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.1', 'Group Job; fail when there are no output columns',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "group" => ["char_1a"],
            "columns" => []
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.2', 'Group Job; fail when there are no output columns',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "group" => ["char_1a"],
            "columns" => [
                ["function" => "count", "expression" => ""]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.3', 'Group Job; fail when there\'s no output column name',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "group" => ["char_1a"],
            "columns" => [
                ["name" => "", "function" => "count", "expression" => ""]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.4', 'Group Job; fail when there are no output columns',  $actual, $expected, $results);



        // TEST: Group Job; invalid or duplicate column name

        // BEGIN TEST
        $params = [
            "group" => ["char_1a"],
            "columns" => [
                ["name" => "select", "function" => "count", "expression" => ""]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('C.1', 'Group Job; fail when the column output is a bad name',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            '.TestSample::getCreateSampleDataTask().',
            {
                "type": "flexio.group",
                "params": {
                    "group": [
                        "char_1a"
                    ],
                    "columns": [
                        {"name": "char_1a", "function": "min", "expression": "char_1a"},
                        {"name": "char_1a", "function": "max", "expression": "char_1a"}
                    ]
                }
            }
        ]
        ';
        $params = [
            "group" => ["char_1a"],
            "columns" => [
                ["name" => "char_1a", "function" => "min", "expression" => "char_1a"],
                ["name" => "char_1a", "function" => "max", "expression" => "char_1a"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('C.2', 'Group Job; fail when there are two output columns of the same name',  $actual, $expected, $results);



        // TEST: Group Job; bad group parameters

        // BEGIN TEST
        $task = '
        [
            '.TestSample::getCreateSampleDataTask().',
            {
                "type": "flexio.group",
                "params": {
                    "group": [
                        ""
                    ],
                    "columns": [
                        {"name": "row_count", "function": "count", "expression": ""}
                    ]
                }
            }
        ]
        ';
        $params = [
            "group" => [""],
            "columns" => [
                ["name" => "row_count", "function" => "count", "expression" => ""]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('D.1', 'Group Job; fail when a group field doesn\'t exist',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "group" => ["non_existent_field"],
            "columns" => [
                ["name" => "row_count", "function" => "count", "expression" => ""]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('D.2', 'Group Job; fail when a group field doesn\'t exist',  $actual, $expected, $results);



        // TEST: Group Job; basic column renaming

        // BEGIN TEST
        $task = '
        [
            '.TestSample::getCreateSampleDataTask().',
            {
                "type": "flexio.group",
                "params": {
                    "group": [
                        "char_1a",
                        "char_1b"
                    ],
                    "columns": [
                        {"name": "char_1a", "function": "", "expression": "char_1a"},
                        {"name": "char_1b_1", "function": "", "expression": "char_1b"},
                        {"name": "char_1b_2", "function": "", "expression": "char_1b"}
                    ]
                }
            }
        ]
        ';
        $params = [
            "group" => ["char_1a","char_1b"],
            "columns" => [
                ["name" => "char_1a", "function" => "", "expression" => "char_1a"],
                ["name" => "char_1b_1", "function" => "", "expression" => "char_1b"],
                ["name" => "char_1b_2", "function" => "", "expression" => "char_1b"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "char_1a", "type": "character", "width": 10 },
                { "name": "char_1b_1", "type": "character", "width": 10 },
                { "name": "char_1b_2", "type": "character", "width": 10 }
            ],
            "rows": [
                ["A","",""],
                ["A","A","A"]
            ]
        }
        ';
        TestCheck::assertInArray('E.1', 'Group Job; make sure columns are renamed correctly',  $actual, $expected, $results);
    }
}
