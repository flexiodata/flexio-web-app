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
        // TEST: schedule base properties

        // BEGIN TEST
        $schedule = json_decode('
        {
            "timezone": "",
            "days": [],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.1', 'Schedule; make sure base properties are there',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "days": [],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.2', 'Schedule; make sure base properties are there',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "timezone": "",
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', 'Schedule; make sure base properties are there',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "timezone": "",
            "days": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.4', 'Schedule; make sure base properties are there',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.5', 'Schedule; make sure base properties are there',  $actual, $expected, $results);


        // TEST: schedule frequency values

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": null,
            "timezone": "",
            "days": [],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.1', 'Schedule; make sure frequency is valid',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('B.2', 'Schedule; make sure frequency is valid',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('B.3', 'Schedule; make sure frequency is valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "one-minute",
            "timezone": "",
            "days": [],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.4', 'Schedule; make sure frequency is valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "ONE-MINUTE",
            "timezone": "",
            "days": [],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.5', 'Schedule; make sure frequency is valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "minutes",
            "timezone": "",
            "days": [],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.6', 'Schedule; make sure frequency is valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "five-minutes",
            "timezone": "",
            "days": [],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.7', 'Schedule; make sure frequency is valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "five-min",
            "timezone": "",
            "days": [],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.8', 'Schedule; make sure frequency is valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "fifteen-minutes",
            "timezone": "",
            "days": [],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.9', 'Schedule; make sure frequency is valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "twenty-minutes",
            "timezone": "",
            "days": [],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.10', 'Schedule; make sure frequency is valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "thirty-minutes",
            "timezone": "",
            "days": [],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.11', 'Schedule; make sure frequency is valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "hourly",
            "timezone": "",
            "days": [],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.12', 'Schedule; make sure frequency is valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "daily",
            "timezone": "",
            "days": [],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.13', 'Schedule; make sure frequency is valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "monthly",
            "timezone": "",
            "days": [],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.14', 'Schedule; make sure frequency is valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "yearly",
            "timezone": "",
            "days": [],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.15', 'Schedule; make sure frequency is valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": ["daily", "monthly"],
            "timezone": "",
            "days": [],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.16', 'Schedule; make sure frequency is valid',  $actual, $expected, $results);


        // TEST: schedule timezone values

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "timezone": null,
            "days": [],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.1', 'Schedule; make sure timezone is valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "timezone": 0,
            "days": [],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.2', 'Schedule; make sure timezone is valid',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('C.3', 'Schedule; make sure timezone is valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "timezone": "invalid",
            "days": [],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.4', 'Schedule; make sure timezone is valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "timezone": "utc",
            "days": [],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.5', 'Schedule; make sure timezone is valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "timezone": "UTC",
            "days": [],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.6', 'Schedule; make sure timezone is valid',  $actual, $expected, $results);


        // TEST: schedule day values

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "timezone": "",
            "days": null,
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.1', 'Schedule; make sure days are valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "timezone": "",
            "days": "mon",
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.2', 'Schedule; make sure days are valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "timezone": "",
            "days": ["mon"],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.3', 'Schedule; make sure days are valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "timezone": "",
            "days": ["monday"],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.4', 'Schedule; make sure days are valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "timezone": "",
            "days": ["tue"],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.5', 'Schedule; make sure days are valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "timezone": "",
            "days": ["wed"],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.6', 'Schedule; make sure days are valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "timezone": "",
            "days": ["thu"],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.7', 'Schedule; make sure days are valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "timezone": "",
            "days": ["fri"],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.8', 'Schedule; make sure days are valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "timezone": "",
            "days": ["sat"],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.9', 'Schedule; make sure days are valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "timezone": "",
            "days": ["sun"],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.10', 'Schedule; make sure days are valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "timezone": "",
            "days": [0],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.11', 'Schedule; make sure days are valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "timezone": "",
            "days": [1],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.12', 'Schedule; make sure days are valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "timezone": "",
            "days": [5],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.13', 'Schedule; make sure days are valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "timezone": "",
            "days": [10],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.14', 'Schedule; make sure days are valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "timezone": "",
            "days": [15],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.15', 'Schedule; make sure days are valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "timezone": "",
            "days": [30],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.16', 'Schedule; make sure days are valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "timezone": "",
            "days": ["mon","tue"],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.17', 'Schedule; make sure days are valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "",
            "timezone": "",
            "days": ["mon","bad"],
            "times": []
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.18', 'Schedule; make sure days are valid',  $actual, $expected, $results);


        // TEST: schedule various combinations

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "daily",
            "timezone": "UTC",
            "days": [],
            "times": [{"hour": 0}]
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.1', 'Schedule; make sure times are valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "daily",
            "timezone": "UTC",
            "days": [],
            "times": [{"minute": 0}]
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.2', 'Schedule; make sure times are valid',  $actual, $expected, $results);

        // BEGIN TEST
        $schedule = json_decode('
        {
            "frequency": "daily",
            "timezone": "UTC",
            "days": [],
            "times": [{"hour": 0,"minute": 0}]
        }
        ');
        $actual = \Flexio\Base\Schedule::isValid($schedule);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.3', 'Schedule; make sure times are valid',  $actual, $expected, $results);


        // TEST: schedule various combinations

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
        \Flexio\Tests\Check::assertBoolean('F.1', 'Schedule; make sure various combinations are valid',  $actual, $expected, $results);
    }
}
