<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-06
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: \Flexio\Base\Util::filterArrayofArrays() array input

        // BEGIN TEST
        $arr = \Flexio\Base\Util::filterArrayofArrays(array(array('a'=>'','b'=>''),array('b'=>'','c'=>'')),array());
        $actual = (json_encode($arr) == '[[],[]]' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Flexio\Base\Util::filterArrayofArrays() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::filterArrayofArrays(array(array('a'=>'','b'=>''),array('b'=>'','c'=>'')),array('b'));
        $actual = (json_encode($arr) == '[{"b":""},{"b":""}]' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.2', '\Flexio\Base\Util::filterArrayofArrays() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::filterArrayofArrays(array(array('a'=>'','b'=>''),array('b'=>'','c'=>'')),array('b','c'));
        $actual = (json_encode($arr) == '[{"b":""},{"b":"","c":""}]' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.3', '\Flexio\Base\Util::filterArrayofArrays() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::filterArrayofArrays(array(array('a'=>'','b'=>''),array('b'=>'','c'=>'')),array('a','b'));
        $actual = (json_encode($arr) == '[{"a":"","b":""},{"b":""}]' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.4', '\Flexio\Base\Util::filterArrayofArrays() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::filterArrayofArrays(array(array('a'=>'','b'=>''),array('b'=>'','c'=>'')),array('a','b','c'));
        $actual = (json_encode($arr) == '[{"a":"","b":""},{"b":"","c":""}]' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.5', '\Flexio\Base\Util::filterArrayofArrays() array input',  $actual, $expected, $results);
    }
}
