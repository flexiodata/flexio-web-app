<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-06
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: \Flexio\System\Util::formatNumber() integer input; configuration dependent, so just test the most basic functionality

        // BEGIN TEST
        $str = \Flexio\System\Util::formatNumber(0);
        $actual = ($str == '0' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Flexio\System\Util::formatNumber() integer input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::formatNumber(1,0);
        $actual = ($str == '1' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.2', '\Flexio\System\Util::formatNumber() integer input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::formatNumber(-1,0);
        $actual = ($str == '-1' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.3', '\Flexio\System\Util::formatNumber() integer input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::formatNumber(999,0);
        $actual = ($str == '999' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.4', '\Flexio\System\Util::formatNumber() integer input',  $actual, $expected, $results);

        // BEGIN TEST
        //$str = \Flexio\System\Util::formatNumber(1000,0); // configuration dependent
        //$actual = ($str == '1000' ? true : false);
        //$expected = true;
        //TestCheck::assertBoolean('A.5', '\Flexio\System\Util::formatNumber() integer input',  $actual, $expected, $results);



        // TEST: \Flexio\System\Util::formatNumber() non-integer input

        // BEGIN TEST
        $str = \Flexio\System\Util::formatNumber(0.1);
        $actual = ($str == '0' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Flexio\System\Util::formatNumber() non-integer input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::formatNumber(-0.1);
        $actual = ($str == '0' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.2', '\Flexio\System\Util::formatNumber() non-integer input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::formatNumber(0.6);
        $actual = ($str == '1' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.3', '\Flexio\System\Util::formatNumber() non-integer input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::formatNumber(-0.6);
        $actual = ($str == '-1' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.4', '\Flexio\System\Util::formatNumber() non-integer input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::formatNumber(2.5,0);
        $actual = ($str == '3' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.5', '\Flexio\System\Util::formatNumber() non-integer input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::formatNumber(-2.5,0);
        $actual = ($str == '-3' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.6', '\Flexio\System\Util::formatNumber() non-integer input',  $actual, $expected, $results);
    }
}
