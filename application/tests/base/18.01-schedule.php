<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-08-29
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
        // TEST:: schedule validation

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "randomly",
            "timezone": "",
            "days": [],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.1', 'Schedule; check schedule format validator',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "hourly",
            "timezone": 0,
            "days": [],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.2', 'Schedule; check schedule format validator',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "hourly",
            "timezone": "UTC",
            "days": [],
            "times": [{"hour": -1, "minute": 0}]
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', 'Schedule; check schedule format validator',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "timezone": "",
            "days": [],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.4', 'Schedule; check schedule format validator',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "hourly",
            "timezone": "UTC",
            "days": [],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.5', 'Schedule; check schedule format validator',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "daily",
            "timezone": "UTC",
            "days": ["mon","tue","wed","thu","fri"],
            "times": [{"hour": 0,"minute": 0}]
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.6', 'Schedule; check schedule format validator',  $actual, $expected, $results);
    }
}
