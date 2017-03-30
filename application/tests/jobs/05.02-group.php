<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-09-11
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



        // TEST: Group Job; aggregate functions with no group parameters

        // BEGIN TEST
        $params = [
            "group" => [],
            "columns" => [
                ["name" => "row_count", "function" => "count", "expression" => ""]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[[20]]';
        TestCheck::assertArray('A.1', 'Group Job; aggregate functions with no group parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "group" => null,
            "columns" => [
                ["name" => "row_count", "function" => "count", "expression" => ""]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[[20]]';
        TestCheck::assertArray('A.2', 'Group Job; aggregate functions with no group parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "group" => null,
            "columns" => [
                ["name" => "row_count", "function" => "count", "expression" => "char_1a"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[[20]]';
        TestCheck::assertArray('A.3', 'Group Job; aggregate functions with no group parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "group" => null,
            "columns" => [
                ["name" => "row_count", "function" => "count", "expression" => "non_existent_field"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('A.4', 'Group Job; aggregate function on non-existent field with no group parameters should fail',  $actual, $expected, $results);



        // TEST: Group Job; aggregate functions on char column with no group parameters

        // BEGIN TEST
        $params = [
            "group" => null,
            "columns" => [
                ["name" => "count_char_1d", "function" => "count", "expression" => "char_1d"],
                ["name" => "min_char_1d", "function" => "min", "expression" => "char_1d"],
                ["name" => "max_char_1d", "function" => "max", "expression" => "char_1d"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[[15,"a","E"]]';
        TestCheck::assertArray('B.1', 'Group Job; aggregate functions on char column with no group parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "group" => null,
            "columns" => [
                ["name" => "sum_char_1d", "function" => "sum", "expression" => "char_1d"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.2', 'Group Job; sum() aggregate function not allowed on char fields',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "group" => null,
            "columns" => [
                ["name" => "avg_char_1d", "function" => "avg", "expression" => "char_1d"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.3', 'Group Job; avg() aggregate function not allowed on char fields',  $actual, $expected, $results);



        // TEST: Group Job; aggregate functions on numeric column with no group parameters

        // BEGIN TEST
        $params = [
            "group" => null,
            "columns" => [
                ["name" => "count_num_1d", "function" => "count", "expression" => "num_1d"],
                ["name" => "min_num_1d", "function" => "min", "expression" => "num_1d"],
                ["name" => "max_num_1d", "function" => "max", "expression" => "num_1d"],
                ["name" => "avg_num_1d", "function" => "avg", "expression" => "num_1d"],
                ["name" => "sum_num_1d", "function" => "sum", "expression" => "num_1d"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[[15,"-4","5","15","1"]]';
        TestCheck::assertArray('C.1', 'Group Job; aggregate functions on numeric column with no group parameters',  $actual, $expected, $results);



        // TEST: Group Job; aggregate functions on double column with no group parameters

        // BEGIN TEST
        $params = [
            "group" => null,
            "columns" => [
                ["name" => "count_num_2d", "function" => "count", "expression" => "num_2d"],
                ["name" => "min_num_2d", "function" => "min", "expression" => "num_2d"],
                ["name" => "max_num_2d", "function" => "max", "expression" => "num_2d"],
                ["name" => "sum_num_2d", "function" => "sum", "expression" => "num_2d"],
                ["name" => "avg_num_2d", "function" => "avg", "expression" => "num_2d"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[[15,"-4","5","15","1"]]';
        TestCheck::assertArray('D.1', 'Group Job; aggregate functions on double column with no group parameters',  $actual, $expected, $results);



        // TEST: Group Job; aggregate functions on double column with no group parameters

        // BEGIN TEST
        $params = [
            "group" => null,
            "columns" => [
                ["name" => "count_num_3d", "function" => "count", "expression" => "num_3d"],
                ["name" => "min_num_3d", "function" => "min", "expression" => "num_3d"],
                ["name" => "max_num_3d", "function" => "max", "expression" => "num_3d"],
                ["name" => "sum_num_3d", "function" => "sum", "expression" => "num_3d"],
                ["name" => "avg_num_3d", "function" => "avg", "expression" => "num_3d"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[[15,"-4","5","15","1"]]';
        TestCheck::assertArray('E.1', 'Group Job; aggregate functions on integer column with no group parameters',  $actual, $expected, $results);



        // TEST: Group Job; aggregate functions on date column with no group parameters

        // BEGIN TEST
        $params = [
            "group" => null,
            "columns" => [
                ["name" => "count_date_1d", "function" => "count", "expression" => "date_1d"],
                ["name" => "min_date_1d", "function" => "min", "expression" => "date_1d"],
                ["name" => "max_date_1d", "function" => "max", "expression" => "date_1d"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[[15,"2000-12-28","2001-01-06"]]';
        TestCheck::assertArray('F.1', 'Group Job; aggregate functions on date column with no group parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "group" => null,
            "columns" => [
                ["name" => "sum_date_1d", "function" => "sum", "expression" => "date_1d"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('F.2', 'Group Job; sum() aggregate function not allowed on date fields',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "group" => null,
            "columns" => [
                ["name" => "avg_date_1d", "function" => "avg", "expression" => "date_1d"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('F.3', 'Group Job; avg() aggregate function not allowed on date fields',  $actual, $expected, $results);



        // TEST: Group Job; aggregate functions on datetime column with no group parameters

        // BEGIN TEST
        $params = [
            "group" => null,
            "columns" => [
                ["name" => "count_date_2d", "function" => "count", "expression" => "date_2d"],
                ["name" => "min_date_2d", "function" => "min", "expression" => "date_2d"],
                ["name" => "max_date_2d", "function" => "max", "expression" => "date_2d"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[[15,"2000-12-28 01:01:01","2001-01-06 01:01:01"]]';
        TestCheck::assertArray('G.1', 'Group Job; aggregate functions on datetime column with no group parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "group" => null,
            "columns" => [
                ["name" => "sum_date_2d", "function" => "sum", "expression" => "date_2d"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('G.2', 'Group Job; sum() aggregate function not allowed on datetime fields',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "group" => null,
            "columns" => [
                ["name" => "avg_date_2d", "function" => "avg", "expression" => "date_2d"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('G.3', 'Group Job; avg() aggregate function not allowed on datetime fields',  $actual, $expected, $results);



        // TEST: Group Job; aggregate functions on boolean column with no group parameters

        // BEGIN TEST
        $params = [
            "group" => null,
            "columns" => [
                ["name" => "count_bool_1d", "function" => "count", "expression" => "bool_1d"],
                ["name" => "min_bool_1d", "function" => "min", "expression" => "bool_1d"],
                ["name" => "max_bool_1d", "function" => "max", "expression" => "bool_1d"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["15",false,true]]';
        TestCheck::assertArray('H.1', 'Group Job; aggregate functions on boolean column with no group parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "group" => null,
            "columns" => [
                ["name" => "sum_bool_1d", "function" => "sum", "expression" => "bool_1d"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('H.2', 'Group Job; sum() aggregate function not allowed on boolean fields',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "group" => null,
            "columns" => [
                ["name" => "avg_bool_1d", "function" => "avg", "expression" => "bool_1d"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('H.3', 'Group Job; avg() aggregate function not allowed on boolean fields',  $actual, $expected, $results);
    }
}
