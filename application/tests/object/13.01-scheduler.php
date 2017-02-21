<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-11-03
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TODO: add additional tests for job schedule validation


        // TEST:: job scheduler validation template

        // BEGIN TEST
        $schema = \Flexio\Object\Scheduler::SCHEMA;
        $actual = \ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.1', 'Verify Scheduler schema format',  $actual, $expected, $results);



        // TEST:: job scheduler validation template

        // BEGIN TEST
        $schema = \Flexio\Object\Scheduler::SCHEMA;
        $schedule = <<<EOD
{
    "frequency": "",
    "timezone": "",
    "days": [
    ],
    "times": [
    ]
}
EOD;
        $actual = \ValidatorSchema::check(json_decode($schedule), $schema)->hasErrors();
        $expected = true;
        TestCheck::assertBoolean('B.1', 'Scheduler: invalid schedule formats should fail validation',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Object\Scheduler::SCHEMA;
        $schedule = <<<EOD
{
    "frequency": "hourly",
    "timezone": 0,
    "days": [
    ],
    "times": [
    ]
}
EOD;
        $actual = \ValidatorSchema::check(json_decode($schedule), $schema)->hasErrors();
        $expected = true;
        TestCheck::assertBoolean('B.2', 'Scheduler: invalid schedule formats should fail validation',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Object\Scheduler::SCHEMA;
        $schedule = <<<EOD
{
    "frequency": "hourly",
    "timezone": "UTC",
    "days": [
    ],
    "times": [
        {
            "hour": -1,
            "minute": 0
        }
    ]
}
EOD;
        $actual = \ValidatorSchema::check(json_decode($schedule), $schema)->hasErrors();
        $expected = true;
        TestCheck::assertBoolean('B.3', 'Scheduler: invalid schedule formats should fail validation',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Object\Scheduler::SCHEMA;
        $schedule = <<<EOD
{
    "frequency": "hourly",
    "timezone": "UTC",
    "days": [
    ],
    "times": [
    ]
}
EOD;
        $actual = \ValidatorSchema::check(json_decode($schedule), $schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('B.4', 'Scheduler: valid schedule formats should pass validation',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Object\Scheduler::SCHEMA;
        $schedule = <<<EOD
{
    "frequency": "daily",
    "timezone": "UTC",
    "days": [
        "mon",
        "tue",
        "wed",
        "thu",
        "fri"
    ],
    "times": [
        {
            "hour": 0,
            "minute": 0
        }
    ]
}
EOD;
        $actual = \ValidatorSchema::check(json_decode($schedule), $schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('B.5', 'Scheduler: valid schedule formats should pass validation',  $actual, $expected, $results);
    }
}
