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


class Test
{
    public function run(&$results)
    {
        // TEST: \Util::filterDigits() non-string input

        // BEGIN TEST
        $str = \Util::filterDigits(null);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.1', 'Util::filterDigits() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterDigits(true);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.2', 'Util::filterDigits() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterDigits(1);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.3', 'Util::filterDigits() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterDigits('123',null);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.4', 'Util::filterDigits() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterDigits('123',1);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.5', 'Util::filterDigits() non-string input',  $actual, $expected, $results);



        // TEST: \Util::filterDigits() string input

        // BEGIN TEST
        $str = \Util::filterDigits('');
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.1', 'Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterDigits('1');
        $actual = ($str == '1' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.2', 'Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterDigits('a');
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.3', 'Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterDigits('aaa');
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.4', 'Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterDigits('10');
        $actual = ($str == '10' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.5', 'Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterDigits('-10');
        $actual = ($str == '10' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.6', 'Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterDigits('10.5');
        $actual = ($str == '105' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.7', 'Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterDigits('10.5');
        $actual = ($str == '105' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.8', 'Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterDigits('10i');
        $actual = ($str == '10' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.9', 'Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterDigits('(10%)');
        $actual = ($str == '10' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.10', 'Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterDigits('a1sd;fl3kj');
        $actual = ($str == '13' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.11', 'Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterDigits('10.5','.');
        $actual = ($str == '10.5' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.12', 'Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterDigits('-10.5','.-');
        $actual = ($str == '-10.5' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.13', 'Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterDigits(' (333)-333-3333 ','-');
        $actual = ($str == '333-333-3333' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.14', 'Util::filterDigits() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterDigits('10.1.1.1','.');
        $actual = ($str == '10.1.1.1' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.15', 'Util::filterDigits() string input',  $actual, $expected, $results);
    }
}
