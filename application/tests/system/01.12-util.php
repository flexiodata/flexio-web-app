<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-05
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: \Flexio\System\Util::filterDigits() non-string input

        // BEGIN TEST
        $str = \Flexio\System\Util::filterDigits(null);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Flexio\System\Util::filterDigits() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterDigits(true);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.2', '\Flexio\System\Util::filterDigits() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterDigits(1);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.3', '\Flexio\System\Util::filterDigits() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterDigits('123',null);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.4', '\Flexio\System\Util::filterDigits() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterDigits('123',1);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.5', '\Flexio\System\Util::filterDigits() non-string input',  $actual, $expected, $results);



        // TEST: \Flexio\System\Util::filterDigits() string input

        // BEGIN TEST
        $str = \Flexio\System\Util::filterDigits('');
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Flexio\System\Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterDigits('1');
        $actual = ($str == '1' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.2', '\Flexio\System\Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterDigits('a');
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.3', '\Flexio\System\Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterDigits('aaa');
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.4', '\Flexio\System\Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterDigits('10');
        $actual = ($str == '10' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.5', '\Flexio\System\Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterDigits('-10');
        $actual = ($str == '10' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.6', '\Flexio\System\Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterDigits('10.5');
        $actual = ($str == '105' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.7', '\Flexio\System\Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterDigits('10.5');
        $actual = ($str == '105' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.8', '\Flexio\System\Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterDigits('10i');
        $actual = ($str == '10' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.9', '\Flexio\System\Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterDigits('(10%)');
        $actual = ($str == '10' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.10', '\Flexio\System\Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterDigits('a1sd;fl3kj');
        $actual = ($str == '13' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.11', '\Flexio\System\Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterDigits('10.5','.');
        $actual = ($str == '10.5' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.12', '\Flexio\System\Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterDigits('-10.5','.-');
        $actual = ($str == '-10.5' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.13', '\Flexio\System\Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterDigits(' (333)-333-3333 ','-');
        $actual = ($str == '333-333-3333' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.14', '\Flexio\System\Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterDigits('10.1.1.1','.');
        $actual = ($str == '10.1.1.1' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.15', '\Flexio\System\Util::filterDigits() string input',  $actual, $expected, $results);
    }
}
