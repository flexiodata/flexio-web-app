<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-08-25
 *
 * @package flexio
 * @subpackage Tests
 */


class Test
{
    public function run(&$results)
    {
        // TODO: basic tests for valid input:
        // 2. missing params (e.g. name, type)
        // 3. name variations
        // 4. type variations
        // 5. width variations
        // 6. scale variations
        // 7. invalid type param


        // SETUP
        $template_task = \Flexio\Object\Task::create()->push('
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "${name}", "type": "${type}", "width": "${width}", "scale": "${scale}" }
                    ]
                }
            }
        ')->get();



        // TEST: Table Creation; no columns

        // BEGIN TEST

        $local_task = \Flexio\Object\Task::create()->push('
            {
                "type": "flexio.create",
                "params": {
                    "name": "Test Table",
                    "columns": [
                    ]
                }
            }
        ')->get();
        $process = \Flexio\Object\Process::create()->setTask($local_task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('A.1', 'Table Creation; fail when a job definition is invalid',  $actual, $expected, $results);



        // TEST: Create; text column creation

        // BEGIN TEST
        $params = [
            "name" => "f1",
            "type" => "text",
            "width" => null, // unset the key in the task
            "scale" => null  // unset the key in the task
        ];
        $process = \Flexio\Object\Process::create()->setTask($template_task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnResult($process);
        $expected = '[{ "name": "f1", "type": "text" }]';
        TestCheck::assertInArray('B.1', 'Create Job; text column creation, no scale',  $actual, $expected, $results);



        // TEST: Create; character column creation

        // BEGIN TEST
        $params = [
            "name" => "f1",
            "type" => "character",
            "width" => 10,
            "scale" => null  // unset the key in the task
        ];
        $process = \Flexio\Object\Process::create()->setTask($template_task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnResult($process);
        $expected = '[{ "name": "f1", "type": "character", "width": 10 }]';
        TestCheck::assertInArray('C.1', 'Create Job; character column creation, no scale',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "name" => "f1",
            "type" => "character",
            "width" => 10,
            "scale" => null  // unset the key in the task
        ];
        $process = \Flexio\Object\Process::create()->setTask($template_task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnResult($process);
        $expected = '[{ "name": "f1", "type": "character", "width": 10 }]';
        TestCheck::assertInArray('C.2', 'Create Job; character column creation',  $actual, $expected, $results);

        // BEGIN TEST
        $params = [
            "name" => "f1",
            "type" => "character",
            "width" => 10,
            "scale" => 2
        ];
        $process = \Flexio\Object\Process::create()->setTask($template_task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnResult($process);
        $expected = '[{ "name": "f1", "type": "character", "width": 10 }]';
        TestCheck::assertInArray('C.3', 'Create Job; character column creation',  $actual, $expected, $results);



        // TEST: Create; numeric column creation

        // BEGIN TEST
        $params = [
            "name" => "f1",
            "type" => "numeric",
            "width" => 14,
            "scale" => 4
        ];
        $process = \Flexio\Object\Process::create()->setTask($template_task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnResult($process);
        $expected = '[{ "name": "f1", "type": "numeric", "width": 14, "scale": 4 }]';
        TestCheck::assertInArray('D.1', 'Create Job; numeric column creation',  $actual, $expected, $results);



        // TEST: Create; double column creation

        // BEGIN TEST
        $params = [
            "name" => "f1",
            "type" => "double",
            "width" => 14,
            "scale" => 6
        ];
        $process = \Flexio\Object\Process::create()->setTask($template_task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnResult($process);
        $expected = '[{ "name": "f1", "type": "double" }]';
        TestCheck::assertInArray('E.1', 'Create Job; double column creation',  $actual, $expected, $results);



        // TEST: Create; integer field creation

        // BEGIN TEST
        $params = [
            "name" => "f1",
            "type" => "integer",
            "width" => 5,
            "scale" => 2
        ];
        $process = \Flexio\Object\Process::create()->setTask($template_task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnResult($process);
        $expected = '[{ "name": "f1", "type": "integer" }]';
        TestCheck::assertInArray('F.1', 'Create Job; integer column creation',  $actual, $expected, $results);



        // TEST: Create; date field creation

        // BEGIN TEST
        $params = [
            "name" => "f1",
            "type" => "date",
            "width" => 5,
            "scale" => 2
        ];
        $process = \Flexio\Object\Process::create()->setTask($template_task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnResult($process);
        $expected = '[{ "name": "f1", "type": "date" }]';
        TestCheck::assertInArray('G.1', 'Create Job; date column creation',  $actual, $expected, $results);



        // TEST: Create; datetime field creation

        // BEGIN TEST
        $params = [
            "name" => "f1",
            "type" => "datetime",
            "width" => 5,
            "scale" => 2
        ];
        $process = \Flexio\Object\Process::create()->setTask($template_task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnResult($process);
        $expected = '[{ "name": "f1", "type": "datetime" }]';
        TestCheck::assertInArray('H.1', 'Create Job; datetime column creation', $actual, $expected, $results);



        // TEST: Create; boolean field creation

        // BEGIN TEST
        $params = [
            "name" => "f1",
            "type" => "boolean",
            "width" => 5,
            "scale" => 2
        ];
        $process = \Flexio\Object\Process::create()->setTask($template_task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnResult($process);
        $expected = '[{ "name": "f1", "type": "boolean" }]';
        TestCheck::assertInArray('H.1', 'Create Job; datetime column creation',  $actual, $expected, $results);




        // TEST: Create; basic test with multiple columns and no values

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()->push('
            {
                "type": "flexio.create",
                "params": {
                    "name": "Test Table",
                    "columns": [
                        { "name": "f1", "type": "character", "width": 10, "scale": 0 },
                        { "name": "f2", "type": "numeric",   "width": 10, "scale": 2 },
                        { "name": "f3", "type": "date",      "width": 4,  "scale": 0 }
                    ]
                }
            }
        ')->get();
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = Model::PROCESS_STATUS_COMPLETED;
        TestCheck::assertString('I.1', 'Table Creation; succeed when job definition is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()->push('
            {
                "type": "flexio.create",
                "params": {
                    "name": "Test Table",
                    "columns": [
                        { "name": "f1", "type": "character", "width": 10, "scale": 0 },
                        { "name": "f2", "type": "numeric",   "width": 10, "scale": 2 },
                        { "name": "f3", "type": "date",      "width": 4,  "scale": 0 }
                    ]
                }
            }
        ')->get();
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnResult($process);
        $expected = '
        [
            { "name": "f1", "type": "character", "width": 10 },
            { "name": "f2", "type": "numeric",   "width": 10, "scale": 2 },
            { "name": "f3", "type": "date" }
        ]
        ';
        TestCheck::assertInArray('I.2', 'Create Job; make sure structure is properly created',  $actual, $expected, $results);
    }
}
