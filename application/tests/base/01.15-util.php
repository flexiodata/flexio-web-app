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
        // TEST: \Flexio\Base\Util::removeArrayKeys() array input

        // BEGIN TEST
        $arr = \Flexio\Base\Util::removeArrayKeys(array('a'=>'','b'=>'','c'=>''),array(''));
        $actual = (json_encode($arr) == '{"a":"","b":"","c":""}' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Base\Util::removeArrayKeys() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::removeArrayKeys(array('a'=>'','b'=>'','c'=>''),array('a','b','c'));
        $actual = (json_encode($arr) == '[]' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\Util::removeArrayKeys() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::removeArrayKeys(array('a'=>'','b'=>'','c'=>''),array('c','a'));
        $actual = (json_encode($arr) == '{"b":""}' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Base\Util::removeArrayKeys() array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::removeArrayKeys(array('a'=>'x','b'=>'y','c'=>'z'),array('b'));
        $actual = (json_encode($arr) == '{"a":"x","c":"z"}' ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.4', '\Flexio\Base\Util::removeArrayKeys() array input',  $actual, $expected, $results);
    }
}
