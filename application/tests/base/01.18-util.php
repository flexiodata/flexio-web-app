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
        // TEST: \Flexio\Base\Util::formatNumber() integer input; configuration dependent, so just test the most basic functionality

        // BEGIN TEST
        $str = \Flexio\Base\Util::formatNumber(0);
        $actual = ($str == '0' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Base\Util::formatNumber() integer input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::formatNumber(1,0);
        $actual = ($str == '1' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\Util::formatNumber() integer input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::formatNumber(-1,0);
        $actual = ($str == '-1' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Base\Util::formatNumber() integer input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::formatNumber(999,0);
        $actual = ($str == '999' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.4', '\Flexio\Base\Util::formatNumber() integer input',  $actual, $expected, $results);

        // BEGIN TEST
        //$str = \Flexio\Base\Util::formatNumber(1000,0); // configuration dependent
        //$actual = ($str == '1000' ? true : false);
        //$expected = true;
        //\Flexio\Tests\Check::assertBoolean('A.5', '\Flexio\Base\Util::formatNumber() integer input',  $actual, $expected, $results);



        // TEST: \Flexio\Base\Util::formatNumber() non-integer input

        // BEGIN TEST
        $str = \Flexio\Base\Util::formatNumber(0.1);
        $actual = ($str == '0' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Base\Util::formatNumber() non-integer input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::formatNumber(-0.1);
        $actual = ($str == '0' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.2', '\Flexio\Base\Util::formatNumber() non-integer input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::formatNumber(0.6);
        $actual = ($str == '1' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.3', '\Flexio\Base\Util::formatNumber() non-integer input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::formatNumber(-0.6);
        $actual = ($str == '-1' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.4', '\Flexio\Base\Util::formatNumber() non-integer input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::formatNumber(2.5,0);
        $actual = ($str == '3' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.5', '\Flexio\Base\Util::formatNumber() non-integer input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::formatNumber(-2.5,0);
        $actual = ($str == '-3' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.6', '\Flexio\Base\Util::formatNumber() non-integer input',  $actual, $expected, $results);
    }
}
