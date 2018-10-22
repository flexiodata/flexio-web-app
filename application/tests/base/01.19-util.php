<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
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
        $actual = \Flexio\Base\Util::createDateRangeArray("20180101", "");
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.1', '\Flexio\Base\Util::createDateRangeArray(); bad date input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("", "20180101");
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.2', '\Flexio\Base\Util::createDateRangeArray(); bad date input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("20180101", "bad");
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.3', '\Flexio\Base\Util::createDateRangeArray(); bad date input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("bad", "20180101");
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.4', '\Flexio\Base\Util::createDateRangeArray(); bad date input',  $actual, $expected, $results);



        // TEST: \Flexio\Base\Util::createDateRangeArray(); basic date input

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("20180101", "20180101");
        $expected = [];
        \Flexio\Tests\Check::assertArray('B.1', '\Flexio\Base\Util::createDateRangeArray(); basic date input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("20180102", "20180101");
        $expected = [];
        \Flexio\Tests\Check::assertArray('B.2', '\Flexio\Base\Util::createDateRangeArray(); basic date input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("20171231", "20180101");
        $expected = ["20171231" => 0];
        \Flexio\Tests\Check::assertArray('B.3', '\Flexio\Base\Util::createDateRangeArray(); basic date input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("20180101", "20180102");
        $expected = ["20180101" => 0];
        \Flexio\Tests\Check::assertArray('B.4', '\Flexio\Base\Util::createDateRangeArray(); basic date input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("20180101", "20180103");
        $expected = ["20180101" => 0, "20180102" => 0];
        \Flexio\Tests\Check::assertArray('B.5', '\Flexio\Base\Util::createDateRangeArray(); basic date input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("20160201", "20160301");
        $expected = [
            "20160201" => 0, "20160202" => 0, "20160203" => 0, "20160204" => 0, "20160205" => 0, "20160206" => 0, "20160207" => 0,
            "20160208" => 0, "20160209" => 0, "20160210" => 0, "20160211" => 0, "20160212" => 0, "20160213" => 0, "20160214" => 0,
            "20160215" => 0, "20160216" => 0, "20160217" => 0, "20160218" => 0, "20160219" => 0, "20160220" => 0, "20160221" => 0,
            "20160222" => 0, "20160223" => 0, "20160224" => 0, "20160225" => 0, "20160226" => 0, "20160227" => 0, "20160228" => 0,
            "20160229" => 0
        ];
        \Flexio\Tests\Check::assertArray('B.6', '\Flexio\Base\Util::createDateRangeArray(); basic date input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("20180101 23:23:59", "20180102");
        $expected = ["20180101" => 0];
        \Flexio\Tests\Check::assertArray('B.7', '\Flexio\Base\Util::createDateRangeArray(); basic date input',  $actual, $expected, $results);



        // TEST: \Flexio\Base\Util::createDateRangeArray(); datetime handling

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("20180101 00:00:00", "20180102 00:00:00");
        $expected = ["20180101" => 0];
        \Flexio\Tests\Check::assertArray('C.1', '\Flexio\Base\Util::createDateRangeArray(); datetime handling',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("20180101 00:00:01", "20180102 00:00:00");
        $expected = ["20180101" => 0];
        \Flexio\Tests\Check::assertArray('C.2', '\Flexio\Base\Util::createDateRangeArray(); datetime handling',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("20180101 00:00:00", "20180102 00:00:01");
        $expected = ["20180101" => 0];
        \Flexio\Tests\Check::assertArray('C.3', '\Flexio\Base\Util::createDateRangeArray(); datetime handling',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("20180101 23:59:59", "20180102 00:00:00");
        $expected = ["20180101" => 0];
        \Flexio\Tests\Check::assertArray('C.4', '\Flexio\Base\Util::createDateRangeArray(); datetime handling',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createDateRangeArray("20180101 00:00:00", "20180102 23:59:59");
        $expected = ["20180101" => 0];
        \Flexio\Tests\Check::assertArray('C.5', '\Flexio\Base\Util::createDateRangeArray(); datetime handling',  $actual, $expected, $results);
    }
}
