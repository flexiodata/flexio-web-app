<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-05
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
        // TEST: \Flexio\Base\Util::lpad()

        // BEGIN TEST
        $str = \Flexio\Base\Util::lpad('', 0);
        $actual = ($str === '');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Base\Util::lpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::lpad('', 5);
        $actual = ($str === '     ');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\Util::lpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::lpad('', 5, '0');
        $actual = ($str === '00000');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Base\Util::lpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::lpad('a', 1, '0');
        $actual = ($str === 'a');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.4', '\Flexio\Base\Util::lpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::lpad('a', 5, '0');
        $actual = ($str === '0000a');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.5', '\Flexio\Base\Util::lpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::lpad('a', 0, '0');
        $actual = ($str === 'a');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.6', '\Flexio\Base\Util::lpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::lpad('a', -1, '0');
        $actual = ($str === 'a');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.7', '\Flexio\Base\Util::lpad() test for testing string padding',  $actual, $expected, $results);



        // TEST: \Flexio\Base\Util::rpad()

        // BEGIN TEST
        $str = \Flexio\Base\Util::rpad('', 0);
        $actual = ($str === '');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Base\Util::rpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::rpad('', 5);
        $actual = ($str === '     ');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.2', '\Flexio\Base\Util::rpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::rpad('', 5, '0');
        $actual = ($str === '00000');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.3', '\Flexio\Base\Util::rpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::rpad('a', 1, '0');
        $actual = ($str === 'a');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.4', '\Flexio\Base\Util::rpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::rpad('a', 5, '0');
        $actual = ($str === 'a0000');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.5', '\Flexio\Base\Util::rpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::rpad('a', 0, '0');
        $actual = ($str === 'a');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.6', '\Flexio\Base\Util::rpad() test for testing string padding',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::rpad('a', -1, '0');
        $actual = ($str === 'a');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.7', '\Flexio\Base\Util::rpad() test for testing string padding',  $actual, $expected, $results);
    }
}
