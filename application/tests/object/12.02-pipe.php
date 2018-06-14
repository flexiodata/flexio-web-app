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


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TODO: add additional tests for job schedule validation


        // TEST:: job scheduler validation template

        // BEGIN TEST
        $schema = \Flexio\Object\Pipe::SCHEDULE_SCHEMA;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.1', 'Verify Pipe scheduler schema format',  $actual, $expected, $results);



        // TEST:: job scheduler validation template

        // BEGIN TEST
        $schema = \Flexio\Object\Pipe::SCHEDULE_SCHEMA;
        $schedule = <<<EOD
{
    "frequency": "randomly",
    "timezone": "",
    "days": [
    ],
    "times": [
    ]
}
EOD;
        $actual = \Flexio\Base\ValidatorSchema::check(json_decode($schedule), $schema)->hasErrors();
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', 'Pipe: invalid schedule formats should fail validation',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Object\Pipe::SCHEDULE_SCHEMA;
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
        $actual = \Flexio\Base\ValidatorSchema::check(json_decode($schedule), $schema)->hasErrors();
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.2', 'Pipe: invalid schedule formats should fail validation',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Object\Pipe::SCHEDULE_SCHEMA;
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
        $actual = \Flexio\Base\ValidatorSchema::check(json_decode($schedule), $schema)->hasErrors();
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.3', 'Pipe: invalid schedule formats should fail validation',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Object\Pipe::SCHEDULE_SCHEMA;
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
        $actual = \Flexio\Base\ValidatorSchema::check(json_decode($schedule), $schema)->hasErrors();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.4', 'Pipe: invalid schedule formats should pass validation',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Object\Pipe::SCHEDULE_SCHEMA;
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
        $actual = \Flexio\Base\ValidatorSchema::check(json_decode($schedule), $schema)->hasErrors();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.5', 'Pipe: valid schedule formats should pass validation',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Object\Pipe::SCHEDULE_SCHEMA;
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
        $actual = \Flexio\Base\ValidatorSchema::check(json_decode($schedule), $schema)->hasErrors();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.6', 'Pipe: valid schedule formats should pass validation',  $actual, $expected, $results);
    }
}
