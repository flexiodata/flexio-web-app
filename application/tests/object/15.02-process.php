<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-03-30
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
        // SETUP
        $model = TestUtil::getModel();


        // TEST: Process::cancel()

        // BEGIN TEST
        $object = \Flexio\Object\Process::create();
        $model->process->setProcessStatus($object->getEid(), \Flexio\Jobs\Process::STATUS_RUNNING);
        $actual = $object->cancel()->getProcessStatus();
        $expected = \Flexio\Jobs\Process::STATUS_CANCELLED;
        TestCheck::assertString('A.1', 'Process::cancel(); make sure process status is set to cancelled',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Process::create();
        $model->process->setProcessStatus($object->getEid(), \Flexio\Jobs\Process::STATUS_FAILED);
        $actual = $object->cancel()->getProcessStatus();
        $expected = \Flexio\Jobs\Process::STATUS_FAILED;
        TestCheck::assertString('A.2', 'Process::cancel(); if a job has already finished, don\'t allow it to be cancelled',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Process::create();
        $model->process->setProcessStatus($object->getEid(), \Flexio\Jobs\Process::STATUS_COMPLETED);
        $actual = $object->cancel()->getProcessStatus();
        $expected = \Flexio\Jobs\Process::STATUS_COMPLETED;
        TestCheck::assertString('A.3', 'Process::cancel(); if a job has already finished, don\'t allow it to be cancelled',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Process::create();
        $model->process->setProcessStatus($object->getEid(), \Flexio\Jobs\Process::STATUS_PAUSED);
        $actual = $object->cancel()->getProcessStatus();
        $expected = \Flexio\Jobs\Process::STATUS_CANCELLED;
        TestCheck::assertString('A.4', 'Process::cancel(); if a job has already finished, don\'t allow it to be cancelled',  $actual, $expected, $results);



        // TEST: Process::pause()

        // BEGIN TEST
        $object = \Flexio\Object\Process::create();
        $model->process->setProcessStatus($object->getEid(), \Flexio\Jobs\Process::STATUS_RUNNING);
        $actual = $object->pause()->getProcessStatus();
        $expected = \Flexio\Jobs\Process::STATUS_PAUSED;
        TestCheck::assertString('B.1', 'Process::pause(); if a running job is paused, make sure process status is set to paused',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Process::create();
        $model->process->setProcessStatus($object->getEid(), \Flexio\Jobs\Process::STATUS_FAILED);
        $actual = $object->pause()->getProcessStatus();
        $expected = \Flexio\Jobs\Process::STATUS_FAILED;
        TestCheck::assertString('B.2', 'Process::pause(); only jobs that are running can be paused',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Process::create();
        $model->process->setProcessStatus($object->getEid(), \Flexio\Jobs\Process::STATUS_CANCELLED);
        $actual = $object->pause()->getProcessStatus();
        $expected = \Flexio\Jobs\Process::STATUS_CANCELLED;
        TestCheck::assertString('B.3', 'Process::pause(); only jobs that are running can be paused',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Process::create();
        $model->process->setProcessStatus($object->getEid(), \Flexio\Jobs\Process::STATUS_COMPLETED);
        $actual = $object->pause()->getProcessStatus();
        $expected = \Flexio\Jobs\Process::STATUS_COMPLETED;
        TestCheck::assertString('B.4', 'Process::pause(); only jobs that are running can be paused',  $actual, $expected, $results);



        // TEST: check status on process success or failure

        // BEGIN TEST
        $task =
        [
            [
                "op" => "application/bad-job-definition",
                "params" => (object)[]
            ]
        ];
        $process = \Flexio\Object\Process::create(["task" => $task]);
        $engine = \Flexio\Jobs\StoredProcess::create($process)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Flexio\Jobs\Process::STATUS_FAILED;
        TestCheck::assertString('C.1', 'Basic Process; make sure the task status is properly set when a process fails',  $actual, $expected, $results);

        // BEGIN TEST
        $task =
        [
            [
                "op" => "create",
                "params" => [
                    "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                    "columns" => [
                        [ "name" => "f1", "type" => "d", "width" => 10, "scale" => 0 ]
                    ],
                    "content" => "bad content"
                ]
            ]
        ];
        $process = \Flexio\Object\Process::create(["task" => $task]);
        $engine = \Flexio\Jobs\StoredProcess::create($process)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Flexio\Jobs\Process::STATUS_FAILED;
        TestCheck::assertString('C.2', 'Basic Process; make sure the task status is properly set when a process fails',  $actual, $expected, $results);

        // BEGIN TEST
        $task =
        [
            [
                "op" => "nop",
                "params" => (object)[
                ]
            ]
        ];
        $process = \Flexio\Object\Process::create(["task" => $task]);
        $engine = \Flexio\Jobs\StoredProcess::create($process)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Flexio\Jobs\Process::STATUS_COMPLETED;
        TestCheck::assertString('C.3', 'Basic Process; make sure the task status is properly set when a process succeeds',  $actual, $expected, $results);
    }
}
