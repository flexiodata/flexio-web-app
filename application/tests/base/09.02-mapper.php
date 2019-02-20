<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-07-22
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
        // TEST: top-level object with single key with value of different types

        // BEGIN TEST
        $data = '
        {
            "key1" : null
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
	        {
                "key1" : null
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.1', '\Flexio\Base\Mapper::flatten(); a top-level object with single key maps to an array with a single row with one field',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" : true
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
	        {
                "key1" : true
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.2', '\Flexio\Base\Mapper::flatten(); a top-level object with single key maps to an array with a single row with one field',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" : false
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
	        {
                "key1" : false
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.3', '\Flexio\Base\Mapper::flatten(); a top-level object with single key maps to an array with a single row with one field',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" : -1
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
	        {
                "key1" : -1
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.4', '\Flexio\Base\Mapper::flatten(); a top-level object with single key maps to an array with a single row with one field',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" : -0.1
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
	        {
                "key1" : -0.1
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.5', '\Flexio\Base\Mapper::flatten(); a top-level object with single key maps to an array with a single row with one field',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" : 0
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
	        {
                "key1" : 0
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.6', '\Flexio\Base\Mapper::flatten(); a top-level object with single key maps to an array with a single row with one field',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" : 0.1
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
	        {
                "key1" : 0.1
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.7', '\Flexio\Base\Mapper::flatten(); a top-level object with single key maps to an array with a single row with one field',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" : 1
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
	        {
                "key1" : 1
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.8', '\Flexio\Base\Mapper::flatten(); a top-level object with single key maps to an array with a single row with one field',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" : ""
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
	        {
                "key1" : ""
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.9', '\Flexio\Base\Mapper::flatten(); a top-level object with single key maps to an array with a single row with one field',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" : "value"
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
	        {
                "key1" : "value"
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.10', '\Flexio\Base\Mapper::flatten(); a top-level object with single key maps to an array with a single row with one field',  $actual, $expected, $results);



        // TEST: top-level object with multiple keys

        // BEGIN TEST
        $data = '
        {
            "key1" : "value1",
            "key2" : "value2"
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : "value1",
                "key2" : "value2"
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('B.1', '\Flexio\Base\Mapper::flatten(); a top-level object with multiple keys maps to an array with a single row and multiple fields',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" : null,
            "key2" : false
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : null,
                "key2" : false
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('B.2', '\Flexio\Base\Mapper::flatten(); a top-level object with multiple keys maps to an array with a single row and multiple fields',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" : false,
            "key2" : null
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : false,
                "key2" : null
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('B.3', '\Flexio\Base\Mapper::flatten(); a top-level object with multiple keys maps to an array with a single row and multiple fields',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" : 0,
            "key2" : ""
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : 0,
                "key2" : ""
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('B.4', '\Flexio\Base\Mapper::flatten(); a top-level object with multiple keys maps to an array with a single row and multiple fields',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" : "",
            "key2" : 0
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : "",
                "key2" : 0
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('B.5', '\Flexio\Base\Mapper::flatten(); a top-level object with multiple keys maps to an array with a single row and multiple fields',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" : "a",
            "key2" : true
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : "a",
                "key2" : true
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('B.6', '\Flexio\Base\Mapper::flatten(); a top-level object with multiple keys maps to an array with a single row and multiple fields',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" : true,
            "key2" : "a"
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : true,
                "key2" : "a"
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('B.7', '\Flexio\Base\Mapper::flatten(); a top-level object with multiple keys maps to an array with a single row and multiple fields',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" : "ab",
            "key2" : 1
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : "ab",
                "key2" : 1
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('B.8', '\Flexio\Base\Mapper::flatten(); a top-level object with multiple keys maps to an array with a single row and multiple fields',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" : 1,
            "key2" : "ab"
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : 1,
                "key2" : "ab"
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('B.9', '\Flexio\Base\Mapper::flatten(); a top-level object with multiple keys maps to an array with a single row and multiple fields',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" : null,
            "key2" : true,
            "key3" : false,
            "key4" : -1,
            "key5" : -0.1,
            "key6" : 0,
            "key7" : 0.1,
            "key8" : 1,
            "key9" : "",
            "key10" : "a"
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : null,
                "key2" : true,
                "key3" : false,
                "key4" : -1,
                "key5" : -0.1,
                "key6" : 0,
                "key7" : 0.1,
                "key8" : 1,
                "key9" : "",
                "key10" : "a"
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('B.10', '\Flexio\Base\Mapper::flatten(); a top-level object with multiple keys maps to an array with a single row and multiple fields',  $actual, $expected, $results);



        // TEST: top-level object with keys that are strings having numbers

        // BEGIN TEST
        $data = '
        {
            "0" : "value",
            "1" : "value",
            "2" : "value",
            "3" : "value"
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
	        {
                "0" : "value",
                "1" : "value",
                "2" : "value",
                "3" : "value"
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('C.1', '\Flexio\Base\Mapper::flatten(); make sure that objects with keys that are strings having numbers are mapped correctly',  $actual, $expected, $results);



        // TEST: top-level object with multiple keys and values of different cases

        // BEGIN TEST
        $data = '
        {
            "key1" : "VALUE",
            "key2" : "Value",
            "key3" : "valuE",
            "key4" : "value"
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
	        {
                "key1" : "VALUE",
                "key2" : "Value",
                "key3" : "valuE",
                "key4" : "value"
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('D.1', '\Flexio\Base\Mapper::flatten(); make sure case is preserved when mapping objects',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "KEY" : "value",
            "Key" : "value",
            "keY" : "value",
            "key" : "value"
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
	        {
                "KEY" : "value",
                "Key" : "value",
                "keY" : "value",
                "key" : "value"
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('D.2', '\Flexio\Base\Mapper::flatten(); make sure case is preserved when mapping objects',  $actual, $expected, $results);



        // TEST: top-level object with duplicate keys are merged

        // BEGIN TEST
        $data = '
        {
            "key" : "a",
            "key" : "b"
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
	        {
                "key" : "b"
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('E.1', '\Flexio\Base\Mapper::flatten(); keys are merged using the last value for a duplicated key',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key" : "b",
            "key" : "a"
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
	        {
                "key" : "a"
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('E.2', '\Flexio\Base\Mapper::flatten(); keys are merged using the last value for a duplicated key',  $actual, $expected, $results);
    }
}
