<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-10-22
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
        // TEST: \Flexio\Base\Util::createDateRangeArray(); bad date input

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("2018-01-01", "");
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.1', '\Flexio\Base\Util::createDateRangeArray(); bad date input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("", "2018-01-01");
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.2', '\Flexio\Base\Util::createDateRangeArray(); bad date input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("2018-01-01", "bad");
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.3', '\Flexio\Base\Util::createDateRangeArray(); bad date input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("bad", "2018-01-01");
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.4', '\Flexio\Base\Util::createDateRangeArray(); bad date input',  $actual, $expected, $results);



        // TEST: \Flexio\Base\Util::createDateRangeArray(); basic date input

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("2018-01-01", "2018-01-01");
        $expected = ["2018-01-01" => 0];
        \Flexio\Tests\Check::assertArray('B.1', '\Flexio\Base\Util::createDateRangeArray(); basic date input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("2018-01-02", "2018-01-01");
        $expected = [];
        \Flexio\Tests\Check::assertArray('B.2', '\Flexio\Base\Util::createDateRangeArray(); basic date input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("2017-12-31", "2018-01-01");
        $expected = ["2017-12-31" => 0, "2018-01-01" => 0];
        \Flexio\Tests\Check::assertArray('B.3', '\Flexio\Base\Util::createDateRangeArray(); basic date input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("2018-01-01", "2018-01-02");
        $expected = ["2018-01-01" => 0, "2018-01-02" => 0];
        \Flexio\Tests\Check::assertArray('B.4', '\Flexio\Base\Util::createDateRangeArray(); basic date input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("2018-01-01", "2018-01-03");
        $expected = ["2018-01-01" => 0, "2018-01-02" => 0, "2018-01-03" => 0];
        \Flexio\Tests\Check::assertArray('B.5', '\Flexio\Base\Util::createDateRangeArray(); basic date input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("2016-02-01", "2016-03-01");
        $expected = [
            "2016-02-01" => 0, "2016-02-02" => 0, "2016-02-03" => 0, "2016-02-04" => 0, "2016-02-05" => 0, "2016-02-06" => 0, "2016-02-07" => 0,
            "2016-02-08" => 0, "2016-02-09" => 0, "2016-02-10" => 0, "2016-02-11" => 0, "2016-02-12" => 0, "2016-02-13" => 0, "2016-02-14" => 0,
            "2016-02-15" => 0, "2016-02-16" => 0, "2016-02-17" => 0, "2016-02-18" => 0, "2016-02-19" => 0, "2016-02-20" => 0, "2016-02-21" => 0,
            "2016-02-22" => 0, "2016-02-23" => 0, "2016-02-24" => 0, "2016-02-25" => 0, "2016-02-26" => 0, "2016-02-27" => 0, "2016-02-28" => 0,
            "2016-02-29" => 0, "2016-03-01" => 0
        ];
        \Flexio\Tests\Check::assertArray('B.6', '\Flexio\Base\Util::createDateRangeArray(); basic date input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("2018-01-01 23:23:59", "2018-01-02");
        $expected = ["2018-01-01" => 0, "2018-01-02" => 0];
        \Flexio\Tests\Check::assertArray('B.7', '\Flexio\Base\Util::createDateRangeArray(); basic date input',  $actual, $expected, $results);


        // TEST: \Flexio\Base\Util::createDateRangeArray(); date format handling

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("20180101", "20180102");
        $expected = ["2018-01-01" => 0, "2018-01-02" => 0];
        \Flexio\Tests\Check::assertArray('C.1', '\Flexio\Base\Util::createDateRangeArray(); date format handling',  $actual, $expected, $results);


        // TEST: \Flexio\Base\Util::createDateRangeArray(); datetime handling

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("2018-01-01 00:00:00", "2018-01-02 00:00:00");
        $expected = ["2018-01-01" => 0, "2018-01-02" => 0];
        \Flexio\Tests\Check::assertArray('D.1', '\Flexio\Base\Util::createDateRangeArray(); datetime handling',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("2018-01-01 00:00:01", "2018-01-02 00:00:00");
        $expected = ["2018-01-01" => 0, "2018-01-02" => 0];
        \Flexio\Tests\Check::assertArray('D.2', '\Flexio\Base\Util::createDateRangeArray(); datetime handling',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("2018-01-01 00:00:00", "2018-01-02 00:00:01");
        $expected = ["2018-01-01" => 0, "2018-01-02" => 0];
        \Flexio\Tests\Check::assertArray('D.3', '\Flexio\Base\Util::createDateRangeArray(); datetime handling',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("2018-01-01 23:59:59", "2018-01-02 00:00:00");
        $expected = ["2018-01-01" => 0, "2018-01-02" => 0];
        \Flexio\Tests\Check::assertArray('D.4', '\Flexio\Base\Util::createDateRangeArray(); datetime handling',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("2018-01-01 00:00:00", "2018-01-02 23:59:59");
        $expected = ["2018-01-01" => 0, "2018-01-02" => 0];
        \Flexio\Tests\Check::assertArray('D.5', '\Flexio\Base\Util::createDateRangeArray(); datetime handling',  $actual, $expected, $results);
    }
}
