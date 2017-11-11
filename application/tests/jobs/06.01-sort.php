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


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
// TODO:
// sort tests are deprecated with new memory stream until sort can be reimplemented with the new
return;


        // SETUP
        $create = json_decode('{
            "type": "flexio.create",
            "params": {
                "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                "columns": [
                    { "name": "field1", "type": "character", "width": 3, "scale": 0 }
                ],
                "content": [
                    ["a"],
                    ["b"],
                    ["c"]
                ]
            }
        }',true);



        // TEST: Sort Job; basic functional test

        // BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.sort",
            "params": {
                "order": [
                    {"expression": "field1", "direction": "asc"}
                ]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
        $expected = [["a"],["b"],["c"]];
        TestCheck::assertString('C.1', 'Sort Job; make sure data is ordered correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.sort",
            "params": {
                "order": [
                    {"expression": "field1", "direction": "desc"}
                ]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
        $expected = [["c"],["b"],["a"]];
        TestCheck::assertString('C.2', 'Sort Job; make sure data is ordered correctly',  $actual, $expected, $results);



        // TEST: Sort Job; missing parameters

        // BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.sort",
            "params": {
                "order": "a"
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('A.1', 'Sort Job; fail when a job definition is invalid',  $actual, $expected, $results);



        // TEST: Sort Job; missing column parameters

        // BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.sort",
            "params": {
                "order": null
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.1', 'Sort Job; fail when there are no order columns',  $actual, $expected, $results);

        // BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.sort",
            "params": {
                "order": [
                    {"expression": null}
                ]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.2', 'Sort Job; fail when there\'s an invalid expression',  $actual, $expected, $results);

        // BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.sort",
            "params": {
                "order": [
                    {"expression": ""}
                ]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.3', 'Sort Job; fail when there\'s an invalid expression',  $actual, $expected, $results);

        // BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.sort",
            "params": {
                "order": [
                    {"expression": "c"}
                ]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.4', 'Sort Job; fail when there\'s an invalid expression',  $actual, $expected, $results);

        // BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.sort",
            "params": {
                "order": [
                    {"expression": "field1", "direction": null}
                ]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.5', 'Sort Job; fail when there\'s an invalid direction',  $actual, $expected, $results);

        // BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.sort",
            "params": {
                "order": [
                    {"expression": "field1", "direction": "d"}
                ]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.6', 'Sort Job; fail when there\'s an invalid direction',  $actual, $expected, $results);



        // TEST: Sort Job; basic functional test

        // BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.sort",
            "params": {
                "order": [
                    {"expression": "field1", "direction": "asc"}
                ]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
        $expected = [["a"],["b"],["c"]];
        TestCheck::assertString('C.1', 'Sort Job; make sure data is ordered correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.sort",
            "params": {
                "order": [
                    {"expression": "field1", "direction": "desc"}
                ]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
        $expected = [["c"],["b"],["a"]];
        TestCheck::assertString('C.2', 'Sort Job; make sure data is ordered correctly',  $actual, $expected, $results);
    }
}
