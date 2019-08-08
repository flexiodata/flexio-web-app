<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-08-15
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
        // TEST: Process mode constants

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::MODE_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('A.1', 'Process mode constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::MODE_BUILD;
        $expected = 'B';
        \Flexio\Tests\Check::assertString('A.2', 'Process mode constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::MODE_RUN;
        $expected = 'R';
        \Flexio\Tests\Check::assertString('A.3', 'Process mode constant',  $actual, $expected, $results);



        // TEST: Process status constants

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::STATUS_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('B.1', 'Process status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::STATUS_PENDING;
        $expected = 'S';
        \Flexio\Tests\Check::assertString('B.2', 'Process status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::STATUS_WAITING;
        $expected = 'W';
        \Flexio\Tests\Check::assertString('B.3', 'Process status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::STATUS_RUNNING;
        $expected = 'R';
        \Flexio\Tests\Check::assertString('B.4', 'Process status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::STATUS_CANCELLED;
        $expected = 'X';
        \Flexio\Tests\Check::assertString('B.5', 'Process status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::STATUS_PAUSED;
        $expected = 'P';
        \Flexio\Tests\Check::assertString('B.6', 'Process status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::STATUS_FAILED;
        $expected = 'F';
        \Flexio\Tests\Check::assertString('B.7', 'Process status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::STATUS_COMPLETED;
        $expected = 'C';
        \Flexio\Tests\Check::assertString('B.8', 'Process status constant',  $actual, $expected, $results);



        // TEST: Process response constants

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::RESPONSE_NONE;
        $expected = 0;
        \Flexio\Tests\Check::assertNumber('C.1', 'Process response constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::RESPONSE_NORMAL;
        $expected = 200;
        \Flexio\Tests\Check::assertNumber('C.2', 'Process response constant',  $actual, $expected, $results);



        // TEST: Process event constants

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::EVENT_STARTING;
        $expected = 'process.starting';
        \Flexio\Tests\Check::assertString('D.1', 'Event type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::EVENT_STARTING_TASK;
        $expected = 'process.starting.task';
        \Flexio\Tests\Check::assertString('D.2', 'Event type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::EVENT_FINISHED;
        $expected = 'process.finished';
        \Flexio\Tests\Check::assertString('D.3', 'Event type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Process::EVENT_FINISHED_TASK;
        $expected = 'process.finished.task';
        \Flexio\Tests\Check::assertString('D.4', 'Event type constant',  $actual, $expected, $results);
    }
}
