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
        $create = json_decode('{
            "type": "flexio.create",
            "params": {
                "content_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
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
        }',true);



        // TEST: CalcField Job

        // BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.calc",
            "params": {
                "name": "field3",
                "type": "character",
                "expression": "upper(field1)"
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
        $expected = [["a","b","A"],["b","B","B"],["c","b","C"]];
        TestCheck::assertString('A.1', 'CalcField Job; check basic functionality',  $actual, $expected, $results);

        // BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.calc",
            "params": {
                "name": "field3",
                "type": "character",
                "expression": "concat(field1,\'.\',field2)"
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
        $expected = [["a","b","a.b"],["b","B","b.B"],["c","b","c.b"]];
        TestCheck::assertString('A.2', 'CalcField Job; check basic functionality',  $actual, $expected, $results);

        // BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.calc",
            "params": {
                "name": "field3",
                "type": "character",
                "expression": "substr(concat(field1,\'.\',field2),2,2)"
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
        $expected = [["a","b",".b"],["b","B",".B"],["c","b",".b"]];
        TestCheck::assertString('A.3', 'CalcField Job; check basic functionality',  $actual, $expected, $results);

        // BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.calc",
            "params": {
                "name": "field3",
                "type": "character",
                "expression": "lpad(field2,3,\'0\')"
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
        $expected = [["a","b","00b"],["b","B","00B"],["c","b","00b"]];
        TestCheck::assertString('A.4', 'CalcField Job; check basic functionality',  $actual, $expected, $results);
    }
}
