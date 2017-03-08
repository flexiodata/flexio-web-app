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
        // TEST: \Flexio\Base\Util::filterChars() non-string input

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterChars(null, null);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Flexio\Base\Util::filterChars() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterChars(null, 'a');
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.2', '\Flexio\Base\Util::filterChars() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterChars('a', null);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.3', '\Flexio\Base\Util::filterChars() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterChars(true, true);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.4', '\Flexio\Base\Util::filterChars() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterChars(true, 'a');
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.5', '\Flexio\Base\Util::filterChars() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterChars('a', true);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.6', '\Flexio\Base\Util::filterChars() non-string input',  $actual, $expected, $results);



        // TEST: \Flexio\Base\Util::filterChars() string input

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterChars('', '');
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Flexio\Base\Util::filterChars() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterChars('a', '');
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.2', '\Flexio\Base\Util::filterChars() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterChars('', 'a');
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.3', '\Flexio\Base\Util::filterChars() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterChars('a', 'a');
        $actual = ($str == 'a' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.4', '\Flexio\Base\Util::filterChars() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterChars('aa', 'a');
        $actual = ($str == 'aa' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.5', '\Flexio\Base\Util::filterChars() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterChars('aba', 'a');
        $actual = ($str == 'aa' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.6', '\Flexio\Base\Util::filterChars() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterChars('baab', 'a');
        $actual = ($str == 'aa' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.7', '\Flexio\Base\Util::filterChars() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterChars('cbacabc', 'ab');
        $actual = ($str == 'baab' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.8', '\Flexio\Base\Util::filterChars() string input',  $actual, $expected, $results);
    }
}
