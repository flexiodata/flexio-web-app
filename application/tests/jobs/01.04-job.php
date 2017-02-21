<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-08-15
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: check status on process success or failure

        // BEGIN TEST
        $task =
        [
            [
                "type" => "application/bad-job-definition",
                "params" => (object)[]
            ]
        ];
        $process = \Flexio\Object\Process::create(["task" => $task])->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('A.1', 'Basic Process; make sure the task status is properly set when a process fails',  $actual, $expected, $results);

        // BEGIN TEST
        $task =
        [
            [
                "type" => "flexio.create",
                "params" => [
                    "columns" => [
                        [ "name" => "f1", "type" => "d", "width" => 10, "scale" => 0 ]
                    ]
                ]
            ]
        ];
        $process = \Flexio\Object\Process::create(["task" => $task])->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('A.2', 'Basic Process; make sure the task status is properly set when a process fails',  $actual, $expected, $results);

        // BEGIN TEST
        $task =
        [
            [
                "type" => "flexio.nop",
                "params" => (object)[
                ]
            ]
        ];
        $process = \Flexio\Object\Process::create(["task" => $task])->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_COMPLETED;
        TestCheck::assertString('A.3', 'Basic Process; make sure the task status is properly set when a process succeeds',  $actual, $expected, $results);
    }
}
