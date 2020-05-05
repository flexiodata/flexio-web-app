<?php
/**
 *
 * Copyright (c) 2020, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2020-02-14
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
        // TEST: get next scheduled time for one-minute frequency

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "one-minute", "timezone": "", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-01-01 00:00:00', $schedule);
        $expected = '2020-01-01 00:01:00';
        \Flexio\Tests\Check::assertString('A.1', 'Schedule; get next scheduled time',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "one-minute", "timezone": "UTC", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-02-03 01:02:03', $schedule);
        $expected = '2020-02-03 01:03:03';
        \Flexio\Tests\Check::assertString('A.2', 'Schedule; get next scheduled time',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "one-minute", "timezone": "UTC", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-01-01 23:59:59', $schedule);
        $expected = '2020-01-02 00:00:59';
        \Flexio\Tests\Check::assertString('A.3', 'Schedule; get next scheduled time',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "one-minute", "timezone": "CST", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-01-01 00:00:00', $schedule);
        $expected = '2020-01-01 06:01:00';
        \Flexio\Tests\Check::assertString('A.4', 'Schedule; get next scheduled time',  $actual, $expected, $results);


        // TEST: get next scheduled time for five-minute frequency

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "five-minutes", "timezone": "", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-01-01 00:00:00', $schedule);
        $expected = '2020-01-01 00:05:00';
        \Flexio\Tests\Check::assertString('B.1', 'Schedule; get next scheduled time',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "five-minutes", "timezone": "UTC", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-02-03 01:02:03', $schedule);
        $expected = '2020-02-03 01:07:03';
        \Flexio\Tests\Check::assertString('B.2', 'Schedule; get next scheduled time',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "five-minutes", "timezone": "UTC", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-01-01 23:59:59', $schedule);
        $expected = '2020-01-02 00:04:59';
        \Flexio\Tests\Check::assertString('B.3', 'Schedule; get next scheduled time',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "five-minutes", "timezone": "CST", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-01-01 00:00:00', $schedule);
        $expected = '2020-01-01 06:05:00';
        \Flexio\Tests\Check::assertString('B.4', 'Schedule; get next scheduled time',  $actual, $expected, $results);


        // TEST: get next scheduled time for fifteen-minute frequency

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "fifteen-minutes", "timezone": "", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-01-01 00:00:00', $schedule);
        $expected = '2020-01-01 00:15:00';
        \Flexio\Tests\Check::assertString('C.1', 'Schedule; get next scheduled time',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "fifteen-minutes", "timezone": "UTC", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-02-03 01:02:03', $schedule);
        $expected = '2020-02-03 01:17:03';
        \Flexio\Tests\Check::assertString('C.2', 'Schedule; get next scheduled time',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "fifteen-minutes", "timezone": "UTC", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-01-01 23:59:59', $schedule);
        $expected = '2020-01-02 00:14:59';
        \Flexio\Tests\Check::assertString('C.3', 'Schedule; get next scheduled time',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "fifteen-minutes", "timezone": "CST", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-01-01 00:00:00', $schedule);
        $expected = '2020-01-01 06:15:00';
        \Flexio\Tests\Check::assertString('C.4', 'Schedule; get next scheduled time',  $actual, $expected, $results);


        // TEST: get next scheduled time for thirty-minute frequency

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "thirty-minutes", "timezone": "", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-01-01 00:00:00', $schedule);
        $expected = '2020-01-01 00:30:00';
        \Flexio\Tests\Check::assertString('D.1', 'Schedule; get next scheduled time',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "thirty-minutes", "timezone": "UTC", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-02-03 01:02:03', $schedule);
        $expected = '2020-02-03 01:32:03';
        \Flexio\Tests\Check::assertString('D.2', 'Schedule; get next scheduled time',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "thirty-minutes", "timezone": "UTC", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-01-01 23:59:59', $schedule);
        $expected = '2020-01-02 00:29:59';
        \Flexio\Tests\Check::assertString('D.3', 'Schedule; get next scheduled time',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "thirty-minutes", "timezone": "CST", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-01-01 00:00:00', $schedule);
        $expected = '2020-01-01 06:30:00';
        \Flexio\Tests\Check::assertString('D.4', 'Schedule; get next scheduled time',  $actual, $expected, $results);


        // TEST: get next scheduled time for hourly frequency

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "hourly", "timezone": "", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-01-01 00:00:00', $schedule);
        $expected = '2020-01-01 01:00:00';
        \Flexio\Tests\Check::assertString('E.1', 'Schedule; get next scheduled time',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "hourly", "timezone": "UTC", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-02-03 01:02:03', $schedule);
        $expected = '2020-02-03 02:02:03';
        \Flexio\Tests\Check::assertString('E.2', 'Schedule; get next scheduled time',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "hourly", "timezone": "UTC", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-01-01 23:59:59', $schedule);
        $expected = '2020-01-02 00:59:59';
        \Flexio\Tests\Check::assertString('E.3', 'Schedule; get next scheduled time',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "hourly", "timezone": "CST", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-01-01 00:00:00', $schedule);
        $expected = '2020-01-01 07:00:00';
        \Flexio\Tests\Check::assertString('E.4', 'Schedule; get next scheduled time',  $actual, $expected, $results);


        // TEST: get next scheduled time for daily frequency

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "daily", "timezone": "", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-01-01 00:00:00', $schedule);
        $expected = '2020-01-02 00:00:00';
        \Flexio\Tests\Check::assertString('F.1', 'Schedule; get next scheduled time',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "daily", "timezone": "UTC", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-02-03 01:02:03', $schedule);
        $expected = '2020-02-04 01:02:03';
        \Flexio\Tests\Check::assertString('F.2', 'Schedule; get next scheduled time',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "daily", "timezone": "UTC", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-01-01 23:59:59', $schedule);
        $expected = '2020-01-02 23:59:59';
        \Flexio\Tests\Check::assertString('F.3', 'Schedule; get next scheduled time',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "daily", "timezone": "CST", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-01-01 00:00:00', $schedule);
        $expected = '2020-01-02 06:00:00';
        \Flexio\Tests\Check::assertString('F.4', 'Schedule; get next scheduled time',  $actual, $expected, $results);


        // TEST: get next scheduled time for weekly frequency

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "weekly", "timezone": "", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-01-01 00:00:00', $schedule);
        $expected = '2020-01-08 00:00:00';
        \Flexio\Tests\Check::assertString('F.1', 'Schedule; get next scheduled time',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "weekly", "timezone": "UTC", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-02-03 01:02:03', $schedule);
        $expected = '2020-02-10 01:02:03';
        \Flexio\Tests\Check::assertString('F.2', 'Schedule; get next scheduled time',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "weekly", "timezone": "UTC", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-01-01 23:59:59', $schedule);
        $expected = '2020-01-08 23:59:59';
        \Flexio\Tests\Check::assertString('F.3', 'Schedule; get next scheduled time',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('{"frequency": "weekly", "timezone": "CST", "days": [], "times": []}',true);
        $actual = \Flexio\Base\Schedule::getNextScheduledTime('2020-01-01 00:00:00', $schedule);
        $expected = '2020-01-08 06:00:00';
        \Flexio\Tests\Check::assertString('F.4', 'Schedule; get next scheduled time',  $actual, $expected, $results);
    }
}
