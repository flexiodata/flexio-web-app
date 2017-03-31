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


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: \Flexio\Base\Util::mapArray() non-array input

        // BEGIN TEST
        $arr1 = array();
        $arr2 = false;
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = false;
        TestCheck::assertBoolean('A.1', '\Flexio\Base\Util::mapArray(); non-array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr1 = false;
        $arr2 = array();
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = false;
        TestCheck::assertBoolean('A.2', '\Flexio\Base\Util::mapArray(); non-array input',  $actual, $expected, $results);



        // TEST: \Flexio\Base\Util::mapArray() array input

        // BEGIN TEST
        $arr1 = array();
        $arr2 = array();
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = array();
        TestCheck::assertArray('B.1', '\Flexio\Base\Util::mapArray(); array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr1 = array('key1' => null);
        $arr2 = array();
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = array('key1' => null);
        TestCheck::assertArray('B.2', '\Flexio\Base\Util::mapArray(); array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr1 = array();
        $arr2 = array('key1' => null);
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = array();
        TestCheck::assertArray('B.3', '\Flexio\Base\Util::mapArray(); array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr1 = array('key1' => null);
        $arr2 = array('key1' => 'value1');
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = array('key1' => 'value1');
        TestCheck::assertArray('B.4', '\Flexio\Base\Util::mapArray(); array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr1 = array('key1' => 'a');
        $arr2 = array('key1' => 'b');
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = array('key1' => 'b');
        TestCheck::assertArray('B.5', '\Flexio\Base\Util::mapArray(); array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr1 = array('key1' => 'b');
        $arr2 = array('key1' => 'a');
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = array('key1' => 'a');
        TestCheck::assertArray('B.6', '\Flexio\Base\Util::mapArray(); array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr1 = array('key1' => null, 'key2' => null);
        $arr2 = array('key1' => 'value1');
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = array('key1' => 'value1', 'key2' => null);
        TestCheck::assertArray('B.7', '\Flexio\Base\Util::mapArray(); array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr1 = array('key1' => null, 'key2' => null);
        $arr2 = array('key2' => 'value2');
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = array('key1' => null, 'key2' => 'value2');
        TestCheck::assertArray('B.8', '\Flexio\Base\Util::mapArray(); array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr1 = array('key1' => null, 'key2' => null);
        $arr2 = array('key2' => 'value2', 'key1' => 'value1');
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = array('key1' => 'value1', 'key2' => 'value2');
        TestCheck::assertArray('B.9', '\Flexio\Base\Util::mapArray(); array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr1 = array('key2' => null, 'key3' => null);
        $arr2 = array('key1' => 'value1', 'key2' => 'value2');
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = array('key2' => 'value2', 'key3' => null);
        TestCheck::assertArray('B.10', '\Flexio\Base\Util::mapArray(); array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr1 = array('key2' => null, 'key3' => null);
        $arr2 = array('key3' => 'value3', 'key4' => 'value4');
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = array('key2' => null, 'key3' => 'value3');
        TestCheck::assertArray('B.11', '\Flexio\Base\Util::mapArray(); array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr1 = array('key2' => null, 'key2' => null);
        $arr2 = array('key2' => 'value2', 'key3' => 'value3');
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = array('key2' => 'value2');
        TestCheck::assertArray('B.12', '\Flexio\Base\Util::mapArray(); array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr1 = array('key2' => null, 'key3' => null);
        $arr2 = array('key2' => 1, 'key2' => 2);
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = array('key2' => 2, 'key3' => null);
        TestCheck::assertArray('B.13', '\Flexio\Base\Util::mapArray(); array input',  $actual, $expected, $results);
    }
}
