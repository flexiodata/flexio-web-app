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
        // TEST: Process::cancel()

        // BEGIN TEST
        $object = \Flexio\Object\Process::create();
        $pstatus1 = $object->setProcessStatus(\Flexio\Jobs\Process::STATUS_RUNNING)->getProcessStatus();
        $pstatus2 = $object->cancel()->getProcessStatus();
        $actual = ($pstatus1 === \Flexio\Jobs\Process::STATUS_RUNNING && $pstatus2 === \Flexio\Jobs\Process::STATUS_CANCELLED);
        $expected = true;
        TestCheck::assertBoolean('A.1', 'Process::cancel(); make sure process status is set to cancelled',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Process::create();
        $pstatus1 = $object->setProcessStatus(\Flexio\Jobs\Process::STATUS_FAILED)->getProcessStatus();
        $pstatus2 = $object->cancel()->getProcessStatus();
        $actual = ($pstatus1 === \Flexio\Jobs\Process::STATUS_FAILED && $pstatus2 === \Flexio\Jobs\Process::STATUS_FAILED);
        $expected = true;
        TestCheck::assertBoolean('A.2', 'Process::cancel(); if a job has already finished, don\'t allow it to be cancelled',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Process::create();
        $pstatus1 = $object->setProcessStatus(\Flexio\Jobs\Process::STATUS_COMPLETED)->getProcessStatus();
        $pstatus2 = $object->cancel()->getProcessStatus();
        $actual = ($pstatus1 === \Flexio\Jobs\Process::STATUS_COMPLETED && $pstatus2 === \Flexio\Jobs\Process::STATUS_COMPLETED);
        $expected = true;
        TestCheck::assertBoolean('A.3', 'Process::cancel(); if a job has already finished, don\'t allow it to be cancelled',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Process::create();
        $pstatus1 = $object->setProcessStatus(\Flexio\Jobs\Process::STATUS_PAUSED)->getProcessStatus();
        $pstatus2 = $object->cancel()->getProcessStatus();
        $actual = ($pstatus1 === \Flexio\Jobs\Process::STATUS_PAUSED && $pstatus2 === \Flexio\Jobs\Process::STATUS_CANCELLED);
        $expected = true;
        TestCheck::assertBoolean('A.4', 'Process::cancel(); if a job has already finished, don\'t allow it to be cancelled',  $actual, $expected, $results);



        // TEST: Process::pause()

        // BEGIN TEST
        $object = \Flexio\Object\Process::create();
        $pstatus1 = $object->setProcessStatus(\Flexio\Jobs\Process::STATUS_RUNNING)->getProcessStatus();
        $pstatus2 = $object->pause()->getProcessStatus();
        $actual = ($pstatus1 === \Flexio\Jobs\Process::STATUS_RUNNING && $pstatus2 === \Flexio\Jobs\Process::STATUS_PAUSED);
        $expected = true;
        TestCheck::assertBoolean('B.1', 'Process::pause(); if a running job is paused, make sure process status is set to paused',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Process::create();
        $pstatus1 = $object->setProcessStatus(\Flexio\Jobs\Process::STATUS_FAILED)->getProcessStatus();
        $pstatus2 = $object->pause()->getProcessStatus();
        $actual = ($pstatus1 === \Flexio\Jobs\Process::STATUS_FAILED && $pstatus2 === \Flexio\Jobs\Process::STATUS_FAILED);
        $expected = true;
        TestCheck::assertBoolean('B.2', 'Process::pause(); only jobs that are running can be paused',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Process::create();
        $pstatus1 = $object->setProcessStatus(\Flexio\Jobs\Process::STATUS_CANCELLED)->getProcessStatus();
        $pstatus2 = $object->pause()->getProcessStatus();
        $actual = ($pstatus1 === \Flexio\Jobs\Process::STATUS_CANCELLED && $pstatus2 === \Flexio\Jobs\Process::STATUS_CANCELLED);
        $expected = true;
        TestCheck::assertBoolean('B.3', 'Process::pause(); only jobs that are running can be paused',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Process::create();
        $pstatus1 = $object->setProcessStatus(\Flexio\Jobs\Process::STATUS_COMPLETED)->getProcessStatus();
        $pstatus2 = $object->pause()->getProcessStatus();
        $actual = ($pstatus1 === \Flexio\Jobs\Process::STATUS_COMPLETED && $pstatus2 === \Flexio\Jobs\Process::STATUS_COMPLETED);
        $expected = true;
        TestCheck::assertBoolean('B.4', 'Process::pause(); only jobs that are running can be paused',  $actual, $expected, $results);



        // TEST: setting the process status

        // BEGIN TEST
        $object = \Flexio\Object\Process::create();
        $pstatus1 = $object->setProcessStatus(\Flexio\Jobs\Process::STATUS_WAITING)->getProcessStatus();
        $pstatus2 = $object->setProcessStatus(\Flexio\Jobs\Process::STATUS_UNDEFINED)->getProcessStatus();
        $actual = ($pstatus1 === \Flexio\Jobs\Process::STATUS_WAITING && $pstatus2 === \Flexio\Jobs\Process::STATUS_UNDEFINED);
        $expected = true;
        TestCheck::assertBoolean('C.1', 'Process::setProcessStatus(); make sure the process status can be reset if needed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Process::create();
        $pstatus1 = $object->setProcessStatus(\Flexio\Jobs\Process::STATUS_UNDEFINED)->getProcessStatus();
        $pstatus2 = $object->setProcessStatus(\Flexio\Jobs\Process::STATUS_WAITING)->getProcessStatus();
        $actual = ($pstatus1 === \Flexio\Jobs\Process::STATUS_UNDEFINED && $pstatus2 === \Flexio\Jobs\Process::STATUS_WAITING);
        $expected = true;
        TestCheck::assertBoolean('C.2', 'Process::setProcessStatus(); make sure process status is set',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Process::create();
        $pstatus1 = $object->setProcessStatus(\Flexio\Jobs\Process::STATUS_UNDEFINED)->getProcessStatus();
        $pstatus2 = $object->setProcessStatus(\Flexio\Jobs\Process::STATUS_RUNNING)->getProcessStatus();
        $actual = ($pstatus1 === \Flexio\Jobs\Process::STATUS_UNDEFINED && $pstatus2 === \Flexio\Jobs\Process::STATUS_RUNNING);
        $expected = true;
        TestCheck::assertBoolean('C.3', 'Process::setProcessStatus(); make sure process status is set',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Process::create();
        $pstatus1 = $object->setProcessStatus(\Flexio\Jobs\Process::STATUS_UNDEFINED)->getProcessStatus();
        $pstatus2 = $object->setProcessStatus(\Flexio\Jobs\Process::STATUS_CANCELLED)->getProcessStatus();
        $actual = ($pstatus1 === \Flexio\Jobs\Process::STATUS_UNDEFINED && $pstatus2 === \Flexio\Jobs\Process::STATUS_CANCELLED);
        $expected = true;
        TestCheck::assertBoolean('C.4', 'Process::setProcessStatus(); make sure process status is set',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Process::create();
        $pstatus1 = $object->setProcessStatus(\Flexio\Jobs\Process::STATUS_UNDEFINED)->getProcessStatus();
        $pstatus2 = $object->setProcessStatus(\Flexio\Jobs\Process::STATUS_PAUSED)->getProcessStatus();
        $actual = ($pstatus1 === \Flexio\Jobs\Process::STATUS_UNDEFINED && $pstatus2 === \Flexio\Jobs\Process::STATUS_PAUSED);
        $expected = true;
        TestCheck::assertBoolean('C.5', 'Process::setProcessStatus(); make sure process status is set',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Process::create();
        $pstatus1 = $object->setProcessStatus(\Flexio\Jobs\Process::STATUS_UNDEFINED)->getProcessStatus();
        $pstatus2 = $object->setProcessStatus(\Flexio\Jobs\Process::STATUS_COMPLETED)->getProcessStatus();
        $actual = ($pstatus1 === \Flexio\Jobs\Process::STATUS_UNDEFINED && $pstatus2 === \Flexio\Jobs\Process::STATUS_COMPLETED);
        $expected = true;
        TestCheck::assertBoolean('C.6', 'Process::setProcessStatus(); make sure process status is set',  $actual, $expected, $results);
    }
}
