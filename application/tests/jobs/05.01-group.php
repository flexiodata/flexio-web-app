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


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TODO: in output columns, check for existence of name, formula, and expression

        // SETUP
        $task = json_decode('
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
        ',true);



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



        // TEST: Group Job; output column names

        // BEGIN TEST
        $params = [
            "group" => ["char_1a"],
            "columns" => [
                ["name" => "select", "function" => "count", "expression" => ""]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_COMPLETED;
        TestCheck::assertString('C.1', 'Group Job; make sure output fieldnames don\'t have internal storage restrictions',  $actual, $expected, $results);

        // BEGIN TEST
        $task = json_decode('
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
        ',true);
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
        $task = json_decode('
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
        ',true);
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
        $task = json_decode('
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
        ',true);
        $params = [
            "group" => ["char_1a","char_1b"],
            "columns" => [
                ["name" => "char_1a", "function" => "", "expression" => "char_1a"],
                ["name" => "char_1b_1", "function" => "", "expression" => "char_1b"],
                ["name" => "char_1b_2", "function" => "", "expression" => "char_1b"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::removeKeys($process->getStdout());
        $expected = '
            [
                ["A","",""],
                ["A","A","A"]
            ]
        ';
        TestCheck::assertInArray('E.1', 'Group Job; make sure column names can be changed',  $actual, $expected, $results);
    }
}
