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
        // TEST: \Flexio\Base\Util::mapArray() array input

        // BEGIN TEST
        $arr1 = array();
        $arr2 = array();
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = array();
        \Flexio\Tests\Check::assertArray('A.1', '\Flexio\Base\Util::mapArray(); array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr1 = array('key1' => null);
        $arr2 = array();
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = array('key1' => null);
        \Flexio\Tests\Check::assertArray('A.2', '\Flexio\Base\Util::mapArray(); array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr1 = array();
        $arr2 = array('key1' => null);
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = array();
        \Flexio\Tests\Check::assertArray('A.3', '\Flexio\Base\Util::mapArray(); array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr1 = array('key1' => null);
        $arr2 = array('key1' => 'value1');
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = array('key1' => 'value1');
        \Flexio\Tests\Check::assertArray('A.4', '\Flexio\Base\Util::mapArray(); array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr1 = array('key1' => 'a');
        $arr2 = array('key1' => 'b');
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = array('key1' => 'b');
        \Flexio\Tests\Check::assertArray('A.5', '\Flexio\Base\Util::mapArray(); array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr1 = array('key1' => 'b');
        $arr2 = array('key1' => 'a');
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = array('key1' => 'a');
        \Flexio\Tests\Check::assertArray('A.6', '\Flexio\Base\Util::mapArray(); array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr1 = array('key1' => null, 'key2' => null);
        $arr2 = array('key1' => 'value1');
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = array('key1' => 'value1', 'key2' => null);
        \Flexio\Tests\Check::assertArray('A.7', '\Flexio\Base\Util::mapArray(); array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr1 = array('key1' => null, 'key2' => null);
        $arr2 = array('key2' => 'value2');
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = array('key1' => null, 'key2' => 'value2');
        \Flexio\Tests\Check::assertArray('A.8', '\Flexio\Base\Util::mapArray(); array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr1 = array('key1' => null, 'key2' => null);
        $arr2 = array('key2' => 'value2', 'key1' => 'value1');
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = array('key1' => 'value1', 'key2' => 'value2');
        \Flexio\Tests\Check::assertArray('A.9', '\Flexio\Base\Util::mapArray(); array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr1 = array('key2' => null, 'key3' => null);
        $arr2 = array('key1' => 'value1', 'key2' => 'value2');
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = array('key2' => 'value2', 'key3' => null);
        \Flexio\Tests\Check::assertArray('A.10', '\Flexio\Base\Util::mapArray(); array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr1 = array('key2' => null, 'key3' => null);
        $arr2 = array('key3' => 'value3', 'key4' => 'value4');
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = array('key2' => null, 'key3' => 'value3');
        \Flexio\Tests\Check::assertArray('A.11', '\Flexio\Base\Util::mapArray(); array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr1 = array('key2' => null, 'key2' => null);
        $arr2 = array('key2' => 'value2', 'key3' => 'value3');
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = array('key2' => 'value2');
        \Flexio\Tests\Check::assertArray('A.12', '\Flexio\Base\Util::mapArray(); array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr1 = array('key2' => null, 'key3' => null);
        $arr2 = array('key2' => 1, 'key2' => 2);
        $actual = \Flexio\Base\Util::mapArray($arr1,$arr2);
        $expected = array('key2' => 2, 'key3' => null);
        \Flexio\Tests\Check::assertArray('A.13', '\Flexio\Base\Util::mapArray(); array input',  $actual, $expected, $results);
    }
}
