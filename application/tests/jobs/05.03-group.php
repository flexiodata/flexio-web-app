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



        // TEST: Group Job; grouping on char columns

        // BEGIN TEST
        $params = [
            "group" => ["char_1a"],
            "columns" => [
                ["name" => "char_1a", "function" => "", "expression" => "char_1a"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["A"]]';
        TestCheck::assertArray('A.1', 'Group Job; grouping on single char column',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "group" => ["char_1d"],
            "columns" => [
                ["name" => "char_1d", "function" => "", "expression" => "char_1d"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["a"],["A"],["b"],["B"],["c"],["C"],["d"],["D"],["E"],[null]]';
        TestCheck::assertArray('A.2', 'Group Job; grouping on single char column',  $actual, $expected, $results);



        // TEST: Group Job; grouping on numeric columns

        // BEGIN TEST
        $params = [
            "group" => ["char_1a"],
            "columns" => [
                ["name" => "num_1a", "function" => "", "expression" => "num_1a"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["1"]]';
        TestCheck::assertArray('B.1', 'Group Job; grouping on single numeric column',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "group" => ["num_1d"],
            "columns" => [
                ["name" => "num_1d", "function" => "", "expression" => "num_1d"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["-4"],["-3"],["-2"],["-1"],["1"],["2"],["3"],["4"],["5"],[null]]';
        TestCheck::assertArray('B.2', 'Group Job; grouping on single numeric column',  $actual, $expected, $results);



        // TEST: Group Job; grouping on double columns

        // BEGIN TEST
        $params = [
            "group" => ["char_1a"],
            "columns" => [
                ["name" => "num_2a", "function" => "", "expression" => "num_2a"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["1"]]';
        TestCheck::assertArray('C.1', 'Group Job; grouping on single double column',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "group" => ["num_1d"],
            "columns" => [
                ["name" => "num_2d", "function" => "", "expression" => "num_2d"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["-4"],["-3"],["-2"],["-1"],["1"],["2"],["3"],["4"],["5"],[null]]';
        TestCheck::assertArray('C.2', 'Group Job; grouping on single double column',  $actual, $expected, $results);



        // TEST: Group Job; grouping on date columns

        // BEGIN TEST
        $params = [
            "group" => ["date_1a"],
            "columns" => [
                ["name" => "date_1a", "function" => "", "expression" => "date_1a"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["2001-01-01"]]';
        TestCheck::assertArray('D.1', 'Group Job; grouping on single date column',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "group" => ["date_1d"],
            "columns" => [
                ["name" => "date_1d", "function" => "", "expression" => "date_1d"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["2000-12-28"],["2000-12-29"],["2000-12-30"],["2000-12-31"],["2001-01-02"],["2001-01-03"],["2001-01-04"],["2001-01-05"],["2001-01-06"],[null]]';
        TestCheck::assertArray('D.2', 'Group Job; grouping on single date column',  $actual, $expected, $results);



        // TEST: Group Job; grouping on datetime columns

        // BEGIN TEST
        $params = [
            "group" => ["date_2a"],
            "columns" => [
                ["name" => "date_2a", "function" => "", "expression" => "date_2a"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["2001-01-01 01:01:01"]]';
        TestCheck::assertArray('E.1', 'Group Job; grouping on single datetime column',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "group" => ["date_2d"],
            "columns" => [
                ["name" => "date_2d", "function" => "", "expression" => "date_2d"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["2000-12-28 01:01:01"],["2000-12-29 01:01:01"],["2000-12-30 01:01:01"],["2000-12-31 01:01:01"],["2001-01-02 01:01:01"],["2001-01-03 01:01:01"],["2001-01-04 01:01:01"],["2001-01-05 01:01:01"],["2001-01-06 01:01:01"],[null]]';
        TestCheck::assertArray('E.2', 'Group Job; grouping on single datetime column',  $actual, $expected, $results);



        // TEST: Group Job; grouping on boolean columns

        // BEGIN TEST
        $params = [
            "group" => ["bool_1a"],
            "columns" => [
                ["name" => "bool_1a", "function" => "", "expression" => "bool_1a"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[[true]]';
        TestCheck::assertArray('F.1', 'Group Job; grouping on single boolean column',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "group" => ["bool_1d"],
            "columns" => [
                ["name" => "bool_1d", "function" => "", "expression" => "bool_1d"]
            ]
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[[false],[true],[null]]';
        TestCheck::assertArray('F.2', 'Group Job; grouping on single boolean column',  $actual, $expected, $results);
    }
}
