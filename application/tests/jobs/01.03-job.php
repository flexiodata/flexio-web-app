<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserveA.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-10-25
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
        $model = TestUtil::getModel();



        // TEST: Process mode constants

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::MODE_UNDEFINED;
        $expected = '';
        TestCheck::assertString('A.1', 'Process mode constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::MODE_BUILD;
        $expected = 'B';
        TestCheck::assertString('A.2', 'Process mode constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::MODE_RUN;
        $expected = 'R';
        TestCheck::assertString('A.3', 'Process mode constant',  $actual, $expected, $results);



        // TEST: Process status constants

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::STATUS_UNDEFINED;
        $expected = '';
        TestCheck::assertString('B.1', 'Process status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::STATUS_PENDING;
        $expected = 'S';
        TestCheck::assertString('B.2', 'Process status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::STATUS_WAITING;
        $expected = 'W';
        TestCheck::assertString('B.3', 'Process status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::STATUS_RUNNING;
        $expected = 'R';
        TestCheck::assertString('B.4', 'Process status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::STATUS_CANCELLED;
        $expected = 'X';
        TestCheck::assertString('B.5', 'Process status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::STATUS_PAUSED;
        $expected = 'P';
        TestCheck::assertString('B.6', 'Process status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::STATUS_FAILED;
        $expected = 'F';
        TestCheck::assertString('B.7', 'Process status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::STATUS_COMPLETED;
        $expected = 'C';
        TestCheck::assertString('B.8', 'Process status constant',  $actual, $expected, $results);



        // TEST: Process response constants

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::RESPONSE_NONE;
        $expected = 0;
        TestCheck::assertNumber('C.1', 'Process response constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::RESPONSE_NORMAL;
        $expected = 200;
        TestCheck::assertNumber('C.2', 'Process response constant',  $actual, $expected, $results);



        // TEST: Process log constants

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::LOG_TYPE_UNDEFINED;
        $expected = '';
        TestCheck::assertString('D.1', 'Log type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::LOG_TYPE_SYSTEM;
        $expected = 'P';
        TestCheck::assertString('D.2', 'Log type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::LOG_TYPE_USER;
        $expected = 'U';
        TestCheck::assertString('D.3', 'Log type constant',  $actual, $expected, $results);



        // TEST: Process event constants

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::EVENT_STARTING;
        $expected = 'process.starting';
        TestCheck::assertString('E.1', 'Event type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::EVENT_STARTING_TASK;
        $expected = 'process.starting.task';
        TestCheck::assertString('E.2', 'Event type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::EVENT_FINISHED;
        $expected = 'process.finished';
        TestCheck::assertString('E.3', 'Event type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::EVENT_FINISHED_TASK;
        $expected = 'process.finished.task';
        TestCheck::assertString('E.4', 'Event type constant',  $actual, $expected, $results);
    }
}
