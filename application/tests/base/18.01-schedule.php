<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-08-30
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
        // TEST: schedule frequency constants

        // BEGIN TEST
        $actual = \Flexio\Base\Schedule::FREQ_ONE_MINUTE;
        $expected = 'one-minute';
        \Flexio\Tests\Check::assertString('A.1', 'Schedule constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Schedule::FREQ_FIVE_MINUTES;
        $expected = 'five-minutes';
        \Flexio\Tests\Check::assertString('A.2', 'Schedule constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Schedule::FREQ_FIFTEEN_MINUTES;
        $expected = 'fifteen-minutes';
        \Flexio\Tests\Check::assertString('A.3', 'Schedule constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Schedule::FREQ_THIRTY_MINUTES;
        $expected = 'thirty-minutes';
        \Flexio\Tests\Check::assertString('A.4', 'Schedule constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Schedule::FREQ_HOURLY;
        $expected = 'hourly';
        \Flexio\Tests\Check::assertString('A.5', 'Schedule constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Schedule::FREQ_DAILY;
        $expected = 'daily';
        \Flexio\Tests\Check::assertString('A.6', 'Schedule constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Schedule::FREQ_WEEKLY;
        $expected = 'weekly';
        \Flexio\Tests\Check::assertString('A.7', 'Schedule constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Schedule::FREQ_MONTHLY;
        $expected = 'monthly';
        \Flexio\Tests\Check::assertString('A.8', 'Schedule constant',  $actual, $expected, $results);


        // TEST: schedule day/time constants

        // BEGIN TEST
        $actual = \Flexio\Base\Schedule::DAY_MONDAY;
        $expected = 'mon';
        \Flexio\Tests\Check::assertString('B.1', 'Schedule constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Schedule::DAY_TUESDAY;
        $expected = 'tue';
        \Flexio\Tests\Check::assertString('B.2', 'Schedule constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Schedule::DAY_WEDNESDAY;
        $expected = 'wed';
        \Flexio\Tests\Check::assertString('B.3', 'Schedule constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Schedule::DAY_THURSDAY;
        $expected = 'thu';
        \Flexio\Tests\Check::assertString('B.4', 'Schedule constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Schedule::DAY_FRIDAY;
        $expected = 'fri';
        \Flexio\Tests\Check::assertString('B.5', 'Schedule constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Schedule::DAY_SATURDAY;
        $expected = 'sat';
        \Flexio\Tests\Check::assertString('B.6', 'Schedule constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Schedule::DAY_SUNDAY;
        $expected = 'sun';
        \Flexio\Tests\Check::assertString('B.7', 'Schedule constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Schedule::MONTH_FIRST;
        $expected = 1;
        \Flexio\Tests\Check::assertNumber('B.8', 'Schedule constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Schedule::MONTH_FIFTEENTH;
        $expected = 15;
        \Flexio\Tests\Check::assertNumber('B.9', 'Schedule constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Schedule::MONTH_LAST;
        $expected = 'last';
        \Flexio\Tests\Check::assertString('B.10', 'Schedule constant',  $actual, $expected, $results);
    }
}
