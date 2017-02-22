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
        // TEST: \Flexio\System\Util::filterChars() non-string input

        // BEGIN TEST
        $str = \Flexio\System\Util::filterChars(null, null);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Flexio\System\Util::filterChars() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterChars(null, 'a');
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.2', '\Flexio\System\Util::filterChars() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterChars('a', null);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.3', '\Flexio\System\Util::filterChars() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterChars(true, true);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.4', '\Flexio\System\Util::filterChars() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterChars(true, 'a');
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.5', '\Flexio\System\Util::filterChars() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterChars('a', true);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.6', '\Flexio\System\Util::filterChars() non-string input',  $actual, $expected, $results);



        // TEST: \Flexio\System\Util::filterChars() string input

        // BEGIN TEST
        $str = \Flexio\System\Util::filterChars('', '');
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Flexio\System\Util::filterChars() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterChars('a', '');
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.2', '\Flexio\System\Util::filterChars() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterChars('', 'a');
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.3', '\Flexio\System\Util::filterChars() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterChars('a', 'a');
        $actual = ($str == 'a' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.4', '\Flexio\System\Util::filterChars() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterChars('aa', 'a');
        $actual = ($str == 'aa' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.5', '\Flexio\System\Util::filterChars() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterChars('aba', 'a');
        $actual = ($str == 'aa' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.6', '\Flexio\System\Util::filterChars() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterChars('baab', 'a');
        $actual = ($str == 'aa' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.7', '\Flexio\System\Util::filterChars() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::filterChars('cbacabc', 'ab');
        $actual = ($str == 'baab' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.8', '\Flexio\System\Util::filterChars() string input',  $actual, $expected, $results);
    }
}
