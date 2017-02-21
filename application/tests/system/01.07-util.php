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
        // TEST: \Util::lpad()

        // BEGIN TEST
        $str = \Util::lpad('', 0);
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('A.1', 'Util::lpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::lpad('', 5);
        $actual = ($str === '     ');
        $expected = true;
        TestCheck::assertBoolean('A.2', 'Util::lpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::lpad('', 5, '0');
        $actual = ($str === '00000');
        $expected = true;
        TestCheck::assertBoolean('A.3', 'Util::lpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::lpad('a', 1, '0');
        $actual = ($str === 'a');
        $expected = true;
        TestCheck::assertBoolean('A.4', 'Util::lpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::lpad('a', 5, '0');
        $actual = ($str === '0000a');
        $expected = true;
        TestCheck::assertBoolean('A.5', 'Util::lpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::lpad('a', 0, '0');
        $actual = ($str === 'a');
        $expected = true;
        TestCheck::assertBoolean('A.6', 'Util::lpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::lpad('a', -1, '0');
        $actual = ($str === 'a');
        $expected = true;
        TestCheck::assertBoolean('A.7', 'Util::lpad() test for testing string padding',  $actual, $expected, $results);



        // TEST: \Util::rpad()

        // BEGIN TEST
        $str = \Util::rpad('', 0);
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('B.1', 'Util::rpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::rpad('', 5);
        $actual = ($str === '     ');
        $expected = true;
        TestCheck::assertBoolean('B.2', 'Util::rpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::rpad('', 5, '0');
        $actual = ($str === '00000');
        $expected = true;
        TestCheck::assertBoolean('B.3', 'Util::rpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::rpad('a', 1, '0');
        $actual = ($str === 'a');
        $expected = true;
        TestCheck::assertBoolean('B.4', 'Util::rpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::rpad('a', 5, '0');
        $actual = ($str === 'a0000');
        $expected = true;
        TestCheck::assertBoolean('B.5', 'Util::rpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::rpad('a', 0, '0');
        $actual = ($str === 'a');
        $expected = true;
        TestCheck::assertBoolean('B.6', 'Util::rpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::rpad('a', -1, '0');
        $actual = ($str === 'a');
        $expected = true;
        TestCheck::assertBoolean('B.7', 'Util::rpad() test for testing string padding',  $actual, $expected, $results);
    }
}
