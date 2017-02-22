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
        // TEST: \Flexio\System\Util::lpad()

        // BEGIN TEST
        $str = \Flexio\System\Util::lpad('', 0);
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Flexio\System\Util::lpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::lpad('', 5);
        $actual = ($str === '     ');
        $expected = true;
        TestCheck::assertBoolean('A.2', '\Flexio\System\Util::lpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::lpad('', 5, '0');
        $actual = ($str === '00000');
        $expected = true;
        TestCheck::assertBoolean('A.3', '\Flexio\System\Util::lpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::lpad('a', 1, '0');
        $actual = ($str === 'a');
        $expected = true;
        TestCheck::assertBoolean('A.4', '\Flexio\System\Util::lpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::lpad('a', 5, '0');
        $actual = ($str === '0000a');
        $expected = true;
        TestCheck::assertBoolean('A.5', '\Flexio\System\Util::lpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::lpad('a', 0, '0');
        $actual = ($str === 'a');
        $expected = true;
        TestCheck::assertBoolean('A.6', '\Flexio\System\Util::lpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::lpad('a', -1, '0');
        $actual = ($str === 'a');
        $expected = true;
        TestCheck::assertBoolean('A.7', '\Flexio\System\Util::lpad() test for testing string padding',  $actual, $expected, $results);



        // TEST: \Flexio\System\Util::rpad()

        // BEGIN TEST
        $str = \Flexio\System\Util::rpad('', 0);
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Flexio\System\Util::rpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::rpad('', 5);
        $actual = ($str === '     ');
        $expected = true;
        TestCheck::assertBoolean('B.2', '\Flexio\System\Util::rpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::rpad('', 5, '0');
        $actual = ($str === '00000');
        $expected = true;
        TestCheck::assertBoolean('B.3', '\Flexio\System\Util::rpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::rpad('a', 1, '0');
        $actual = ($str === 'a');
        $expected = true;
        TestCheck::assertBoolean('B.4', '\Flexio\System\Util::rpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::rpad('a', 5, '0');
        $actual = ($str === 'a0000');
        $expected = true;
        TestCheck::assertBoolean('B.5', '\Flexio\System\Util::rpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::rpad('a', 0, '0');
        $actual = ($str === 'a');
        $expected = true;
        TestCheck::assertBoolean('B.6', '\Flexio\System\Util::rpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::rpad('a', -1, '0');
        $actual = ($str === 'a');
        $expected = true;
        TestCheck::assertBoolean('B.7', '\Flexio\System\Util::rpad() test for testing string padding',  $actual, $expected, $results);
    }
}
