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
        // TEST: \Flexio\System\Util::removeArrayKeys() array input

        // BEGIN TEST
        $arr = \Flexio\System\Util::removeArrayKeys(array('a'=>'','b'=>'','c'=>''),array(''));
        $actual = (json_encode($arr) == '{"a":"","b":"","c":""}' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Flexio\System\Util::removeArrayKeys() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\System\Util::removeArrayKeys(array('a'=>'','b'=>'','c'=>''),array('a','b','c'));
        $actual = (json_encode($arr) == '[]' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.2', '\Flexio\System\Util::removeArrayKeys() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\System\Util::removeArrayKeys(array('a'=>'','b'=>'','c'=>''),array('c','a'));
        $actual = (json_encode($arr) == '{"b":""}' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.3', '\Flexio\System\Util::removeArrayKeys() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\System\Util::removeArrayKeys(array('a'=>'x','b'=>'y','c'=>'z'),array('b'));
        $actual = (json_encode($arr) == '{"a":"x","c":"z"}' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.4', '\Flexio\System\Util::removeArrayKeys() array input',  $actual, $expected, $results);
    }
}
