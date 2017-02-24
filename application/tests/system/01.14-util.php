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
        // TEST: \Flexio\System\Util::filterArray() array input

        // BEGIN TEST
        $arr = \Flexio\System\Util::filterArray(array(),array());
        $actual = (json_encode($arr) == '[]' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Flexio\System\Util::filterArray() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\System\Util::filterArray(array('a'=>'b','c'=>'d'),array());
        $actual = (json_encode($arr) == '[]' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.2', '\Flexio\System\Util::filterArray() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\System\Util::filterArray(array('a'=>'b','c'=>'d'),array('a'));
        $actual = (json_encode($arr) == '{"a":"b"}' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.3', '\Flexio\System\Util::filterArray() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\System\Util::filterArray(array('a'=>'b','c'=>'d'),array('c'));
        $actual = (json_encode($arr) == '{"c":"d"}' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.4', '\Flexio\System\Util::filterArray() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\System\Util::filterArray(array('a'=>'b','c'=>'d'),array('c','a'));
        $actual = (json_encode($arr) == '{"a":"b","c":"d"}' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.5', '\Flexio\System\Util::filterArray() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\System\Util::filterArray(array('c'=>'d',''=>'b'),array('','a'));
        $actual = (json_encode($arr) == '{"":"b"}' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.6', '\Flexio\System\Util::filterArray() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\System\Util::filterArray(array('1'=>'','3'=>'','4'=>'','7'=>'','7'=>'a'),array('3','4','4','4'));
        $actual = (json_encode($arr) == '{"3":"","4":""}' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.7', '\Flexio\System\Util::filterArray() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\System\Util::filterArray(array('3'=>'','7'=>'a','4'=>'','7'=>'a'),array('3','7'));
        $actual = (json_encode($arr) == '{"3":"","7":"a"}' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.8', '\Flexio\System\Util::filterArray() array input',  $actual, $expected, $results);



        // TEST: \Flexio\System\Util::filterArrayEmptyValues() array input

        // BEGIN TEST
        $arr = \Flexio\System\Util::filterArrayEmptyValues(array());
        $actual = (json_encode($arr) == '[]' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Flexio\System\Util::filterArrayEmptyValues() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\System\Util::filterArrayEmptyValues(array(false));
        $actual = (json_encode($arr) == '[]' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.2', '\Flexio\System\Util::filterArrayEmptyValues() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\System\Util::filterArrayEmptyValues(array(true));
        $actual = (json_encode($arr) == '[true]' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.3', '\Flexio\System\Util::filterArrayEmptyValues() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\System\Util::filterArrayEmptyValues(array(null));
        $actual = (json_encode($arr) == '[]' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.4', '\Flexio\System\Util::filterArrayEmptyValues() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\System\Util::filterArrayEmptyValues(array(''));
        $actual = (json_encode($arr) == '[]' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.5', '\Flexio\System\Util::filterArrayEmptyValues() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\System\Util::filterArrayEmptyValues(array(null,''));
        $actual = (json_encode($arr) == '[]' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.6', '\Flexio\System\Util::filterArrayEmptyValues() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\System\Util::filterArrayEmptyValues(array('a'));
        $actual = (json_encode($arr) == '["a"]' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.7', '\Flexio\System\Util::filterArrayEmptyValues() array input',  $actual, $expected, $results);
    }
}
