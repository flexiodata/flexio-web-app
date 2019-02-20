<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-06
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
        // TEST: \Flexio\Base\Util::formatDate() input delimiter; note: formats date in ISO8601 format

        // BEGIN TEST
        $str = \Flexio\Base\Util::formatDate('20150501');
        $actual = ($str == '2015-05-01T00:00:00+0000' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Base\Util::formatDate() input delimiter',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::formatDate('2015-05-01');
        $actual = ($str == '2015-05-01T00:00:00+0000' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\Util::formatDate() input delimiter',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::formatDate('2015/05/01');
        $actual = ($str == '2015-05-01T00:00:00+0000' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Base\Util::formatDate() input delimiter',  $actual, $expected, $results);



        // TEST: \Flexio\Base\Util::formatDate() input date

        // BEGIN TEST
        $str = \Flexio\Base\Util::formatDate('0000-00-00');
        $actual = ($str == '-0001-11-30T00:00:00+0000' ? true : false); // this is existing behavior; test is for point-of-reference
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Base\Util::formatDate() input date',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::formatDate('2015-05-30');
        $actual = ($str == '2015-05-30T00:00:00+0000' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.2', '\Flexio\Base\Util::formatDate() input date',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::formatDate('2015-12-01');
        $actual = ($str == '2015-12-01T00:00:00+0000' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.3', '\Flexio\Base\Util::formatDate() input date',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::formatDate('1999-05-01');
        $actual = ($str == '1999-05-01T00:00:00+0000' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.4', '\Flexio\Base\Util::formatDate() input date',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::formatDate('99-05-01');
        $actual = ($str == '1999-05-01T00:00:00+0000' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.5', '\Flexio\Base\Util::formatDate() input date',  $actual, $expected, $results);



        // TEST: \Flexio\Base\Util::formatDate() input time

        // BEGIN TEST
        $str = \Flexio\Base\Util::formatDate('0001-01-01 00:00:00');
        $actual = ($str == '0001-01-01T00:00:00+0000' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.1', '\Flexio\Base\Util::formatDate() input time',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::formatDate('0001-01-01 01:00:00');
        $actual = ($str == '0001-01-01T01:00:00+0000' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.2', '\Flexio\Base\Util::formatDate() input time',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::formatDate('0001-01-01 13:00:00');
        $actual = ($str == '0001-01-01T13:00:00+0000' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.3', '\Flexio\Base\Util::formatDate() input time',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::formatDate('0001-01-01 00:59:00');
        $actual = ($str == '0001-01-01T00:59:00+0000' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.4', '\Flexio\Base\Util::formatDate() input time',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::formatDate('0001-01-01 00:00:59');
        $actual = ($str == '0001-01-01T00:00:59+0000' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.5', '\Flexio\Base\Util::formatDate() input time',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::formatDate('1970-01-01 00:00:00');
        $actual = ($str == '1970-01-01T00:00:00+0000' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.6', '\Flexio\Base\Util::formatDate() input time',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::formatDate('1970-01-01 00:00:01');
        $actual = ($str == '1970-01-01T00:00:01+0000' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.7', '\Flexio\Base\Util::formatDate() input time',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::formatDate('1969-12-31 23:59:59');
        $actual = ($str == '1969-12-31T23:59:59+0000' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.8', '\Flexio\Base\Util::formatDate() input time',  $actual, $expected, $results);
    }
}
