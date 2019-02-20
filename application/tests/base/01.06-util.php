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
        // TEST: \Flexio\Base\Util::beforeFirst()

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeFirst('', '');
        $actual = ($str === '');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Base\Util::beforeFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeFirst('a', '');
        $actual = ($str === 'a');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\Util::beforeFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeFirst('', 'a');
        $actual = ($str === '');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Base\Util::beforeFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeFirst('a', 'a');
        $actual = ($str === '');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.4', '\Flexio\Base\Util::beforeFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeFirst('aa', 'a');
        $actual = ($str === '');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.5', '\Flexio\Base\Util::beforeFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeFirst('aab', 'b');
        $actual = ($str === 'aa');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.6', '\Flexio\Base\Util::beforeFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeFirst('aabab', 'ab');
        $actual = ($str === 'a');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.7', '\Flexio\Base\Util::beforeFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeFirst('aab', 'c');
        $actual = ($str === 'aab');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.8', '\Flexio\Base\Util::beforeFirst() test for substring extraction',  $actual, $expected, $results);



        // TEST: \Flexio\Base\Util::afterFirst()

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterFirst('', '');
        $actual = ($str === '');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Base\Util::afterFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterFirst('a', '');
        $actual = ($str === 'a');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.2', '\Flexio\Base\Util::afterFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterFirst('', 'a');
        $actual = ($str === '');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.3', '\Flexio\Base\Util::afterFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterFirst('a', 'a');
        $actual = ($str === '');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.4', '\Flexio\Base\Util::afterFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterFirst('aab', 'a');
        $actual = ($str === 'ab');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.5', '\Flexio\Base\Util::afterFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterFirst('aab', 'a');
        $actual = ($str === 'ab');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.6', '\Flexio\Base\Util::afterFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterFirst('ababa', 'ab');
        $actual = ($str === 'aba');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.7', '\Flexio\Base\Util::afterFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterFirst('aab', 'c');
        $actual = ($str === 'aab');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.8', '\Flexio\Base\Util::afterFirst() test for substring extraction',  $actual, $expected, $results);



        // TEST: \Flexio\Base\Util::beforeLast()

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeLast('', '');
        $actual = ($str === '');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.1', '\Flexio\Base\Util::beforeLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeLast('a', '');
        $actual = ($str === 'a');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.2', '\Flexio\Base\Util::beforeLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeLast('', 'a');
        $actual = ($str === '');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.3', '\Flexio\Base\Util::beforeLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeLast('a', 'a');
        $actual = ($str === '');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.4', '\Flexio\Base\Util::beforeLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeLast('aa', 'a');
        $actual = ($str === 'a');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.5', '\Flexio\Base\Util::beforeLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeLast('abb', 'b');
        $actual = ($str === 'ab');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.6', '\Flexio\Base\Util::beforeLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeLast('ababa', 'ab');
        $actual = ($str === 'ab');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.7', '\Flexio\Base\Util::beforeLast() test for substring extraction',  $actual, $expected, $results);

        $str = \Flexio\Base\Util::beforeLast('aab', 'c');
        $actual = ($str === 'aab');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.8', '\Flexio\Base\Util::beforeLast() test for substring extraction',  $actual, $expected, $results);



        // TEST: \Flexio\Base\Util::afterLast()

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterLast('', '');
        $actual = ($str === '');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.1', '\Flexio\Base\Util::afterLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterLast('a', '');
        $actual = ($str === 'a');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.2', '\Flexio\Base\Util::afterLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterLast('', 'a');
        $actual = ($str === '');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.3', '\Flexio\Base\Util::afterLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterLast('a', 'a');
        $actual = ($str === '');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.4', '\Flexio\Base\Util::afterLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterLast('aa', 'a');
        $actual = ($str === '');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.5', '\Flexio\Base\Util::afterLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterLast('aab', 'a');
        $actual = ($str === 'b');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.6', '\Flexio\Base\Util::afterLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterLast('ababa', 'ab');
        $actual = ($str === 'a');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.7', '\Flexio\Base\Util::afterLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterLast('aab', 'c');
        $actual = ($str === 'aab');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.8', '\Flexio\Base\Util::afterLast() test for substring extraction',  $actual, $expected, $results);
    }
}
