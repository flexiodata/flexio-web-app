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
        // TEST: \Flexio\Base\Util::filterArray() array input

        // BEGIN TEST
        $arr = \Flexio\Base\Util::filterArray(array(),array());
        $actual = (json_encode($arr) == '[]' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Base\Util::filterArray() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::filterArray(array('a'=>'b','c'=>'d'),array());
        $actual = (json_encode($arr) == '[]' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\Util::filterArray() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::filterArray(array('a'=>'b','c'=>'d'),array('a'));
        $actual = (json_encode($arr) == '{"a":"b"}' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Base\Util::filterArray() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::filterArray(array('a'=>'b','c'=>'d'),array('c'));
        $actual = (json_encode($arr) == '{"c":"d"}' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.4', '\Flexio\Base\Util::filterArray() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::filterArray(array('a'=>'b','c'=>'d'),array('c','a'));
        $actual = (json_encode($arr) == '{"a":"b","c":"d"}' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.5', '\Flexio\Base\Util::filterArray() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::filterArray(array('c'=>'d',''=>'b'),array('','a'));
        $actual = (json_encode($arr) == '{"":"b"}' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.6', '\Flexio\Base\Util::filterArray() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::filterArray(array('1'=>'','3'=>'','4'=>'','7'=>'','7'=>'a'),array('3','4','4','4'));
        $actual = (json_encode($arr) == '{"3":"","4":""}' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.7', '\Flexio\Base\Util::filterArray() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::filterArray(array('3'=>'','7'=>'a','4'=>'','7'=>'a'),array('3','7'));
        $actual = (json_encode($arr) == '{"3":"","7":"a"}' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.8', '\Flexio\Base\Util::filterArray() array input',  $actual, $expected, $results);



        // TEST: \Flexio\Base\Util::filterArrayEmptyValues() array input

        // BEGIN TEST
        $arr = \Flexio\Base\Util::filterArrayEmptyValues(array());
        $actual = (json_encode($arr) == '[]' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Base\Util::filterArrayEmptyValues() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::filterArrayEmptyValues(array(false));
        $actual = (json_encode($arr) == '[]' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.2', '\Flexio\Base\Util::filterArrayEmptyValues() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::filterArrayEmptyValues(array(true));
        $actual = (json_encode($arr) == '[true]' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.3', '\Flexio\Base\Util::filterArrayEmptyValues() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::filterArrayEmptyValues(array(null));
        $actual = (json_encode($arr) == '[]' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.4', '\Flexio\Base\Util::filterArrayEmptyValues() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::filterArrayEmptyValues(array(''));
        $actual = (json_encode($arr) == '[]' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.5', '\Flexio\Base\Util::filterArrayEmptyValues() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::filterArrayEmptyValues(array(null,''));
        $actual = (json_encode($arr) == '[]' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.6', '\Flexio\Base\Util::filterArrayEmptyValues() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::filterArrayEmptyValues(array('a'));
        $actual = (json_encode($arr) == '["a"]' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.7', '\Flexio\Base\Util::filterArrayEmptyValues() array input',  $actual, $expected, $results);
    }
}
