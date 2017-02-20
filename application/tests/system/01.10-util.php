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
        // TEST: Util::filterChars() non-string input

        // BEGIN TEST
        $str = Util::filterChars(null, null);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.1', 'Util::filterChars() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = Util::filterChars(null, 'a');
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.2', 'Util::filterChars() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = Util::filterChars('a', null);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.3', 'Util::filterChars() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = Util::filterChars(true, true);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.4', 'Util::filterChars() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = Util::filterChars(true, 'a');
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.5', 'Util::filterChars() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = Util::filterChars('a', true);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.6', 'Util::filterChars() non-string input',  $actual, $expected, $results);



        // TEST: Util::filterChars() string input

        // BEGIN TEST
        $str = Util::filterChars('', '');
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.1', 'Util::filterChars() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = Util::filterChars('a', '');
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.2', 'Util::filterChars() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = Util::filterChars('', 'a');
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.3', 'Util::filterChars() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = Util::filterChars('a', 'a');
        $actual = ($str == 'a' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.4', 'Util::filterChars() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = Util::filterChars('aa', 'a');
        $actual = ($str == 'aa' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.5', 'Util::filterChars() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = Util::filterChars('aba', 'a');
        $actual = ($str == 'aa' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.6', 'Util::filterChars() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = Util::filterChars('baab', 'a');
        $actual = ($str == 'aa' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.7', 'Util::filterChars() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = Util::filterChars('cbacabc', 'ab');
        $actual = ($str == 'baab' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.8', 'Util::filterChars() string input',  $actual, $expected, $results);
    }
}
