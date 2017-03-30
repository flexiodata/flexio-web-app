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
        $task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
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
                "type": "flexio.calc",
                "params": {
                    "name": "field3",
                    "type": "character",
                    "expression": "${formula}"
                }
            }
        ]
        ',true);



        // TEST: CalcField Job

        // BEGIN TEST
        $params = [
            "formula" => "upper(field1)"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = [["a","b","A"],["b","B","B"],["c","b","C"]];
        TestCheck::assertString('A.1', 'CalcField Job; check basic functionality',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "formula" => "concat(field1,'.',field2)"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = [["a","b","a.b"],["b","B","b.B"],["c","b","c.b"]];
        TestCheck::assertString('A.2', 'CalcField Job; check basic functionality',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "formula" => "substr(concat(field1,'.',field2),2,2)"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = [["a","b",".b"],["b","B",".B"],["c","b",".b"]];
        TestCheck::assertString('A.3', 'CalcField Job; check basic functionality',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "formula" => "lpad(field2,3,'0')"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = [["a","b","00b"],["b","B","00B"],["c","b","00b"]];
        TestCheck::assertString('A.4', 'CalcField Job; check basic functionality',  $actual, $expected, $results);
    }
}
