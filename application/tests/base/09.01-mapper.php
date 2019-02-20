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
        // TEST: check individual values with no schema

        // BEGIN TEST
        $data = null;
        $actual = \Flexio\Base\Mapper::flatten($data);
        $expected = '
        [
            {
                "field" : null
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.1', '\Flexio\Base\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = false;
        $actual = \Flexio\Base\Mapper::flatten($data);
        $expected = '
        [
            {
                "field" : false
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.2', '\Flexio\Base\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = true;
        $actual = \Flexio\Base\Mapper::flatten($data);
        $expected = '
        [
            {
                "field" : true
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.3', '\Flexio\Base\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = -1;
        $actual = \Flexio\Base\Mapper::flatten($data);
        $expected = '
        [
            {
                "field" : -1
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.4', '\Flexio\Base\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = -0.1;
        $actual = \Flexio\Base\Mapper::flatten($data);
        $expected = '
        [
            {
                "field" : -0.1
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.5', '\Flexio\Base\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = 0;
        $actual = \Flexio\Base\Mapper::flatten($data);
        $expected = '
        [
            {
                "field" : 0
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.6', '\Flexio\Base\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = 0.1;
        $actual = \Flexio\Base\Mapper::flatten($data);
        $expected = '
        [
            {
                "field" : 0.1
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.7', '\Flexio\Base\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = 1;
        $actual = \Flexio\Base\Mapper::flatten($data);
        $expected = '
        [
            {
                "field" : 1
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.8', '\Flexio\Base\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '';
        $actual = \Flexio\Base\Mapper::flatten($data);
        $expected = '
        [
            {
                "field" : ""
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.9', '\Flexio\Base\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = 'a';
        $actual = \Flexio\Base\Mapper::flatten($data);
        $expected = '
        [
            {
                "field" : "a"
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.10', '\Flexio\Base\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = 'A';
        $actual = \Flexio\Base\Mapper::flatten($data);
        $expected = '
        [
            {
                "field" : "A"
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.11', '\Flexio\Base\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = 'aB';
        $actual = \Flexio\Base\Mapper::flatten($data);
        $expected = '
        [
            {
                "field" : "aB"
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.12', '\Flexio\Base\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);


        // BEGIN TEST
        $data = '[]';
        $actual = \Flexio\Base\Mapper::flatten($data);
        $expected = '
        [
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.13', '\Flexio\Base\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '{}';
        $actual = \Flexio\Base\Mapper::flatten($data);
        $expected = '
        [
            {
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.14', '\Flexio\Base\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            {
                "key" : "value"
            }
        ]
        ';
        $schema = null;
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key" : "value"
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.15', '\Flexio\Base\Mapper::flatten(); schema input needs to be null or an object; return false otherwise',  $actual, $expected, $results);



        // TEST: check basic schema input

        // BEGIN TEST
        $data = '
        [
            {
                "key" : "value"
            }
        ]
        ';
        $schema = null;
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key" : "value"
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('B.1', '\Flexio\Base\Mapper::flatten(); schema input needs to be null or an object; return false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            {
                "key" : "value"
            }
        ]
        ';
        $schema = false;
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected =  false;
        \Flexio\Tests\Check::assertBoolean('B.2', '\Flexio\Base\Mapper::flatten(); schema input needs to be null or an object; return false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            {
                "key" : "value"
            }
        ]
        ';
        $schema = 1;
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected =  false;
        \Flexio\Tests\Check::assertBoolean('B.3', '\Flexio\Base\Mapper::flatten(); schema input needs to be null or an object; return false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            {
                "key" : "value"
            }
        ]
        ';
        $schema = 'a';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected =  false;
        \Flexio\Tests\Check::assertBoolean('B.4', '\Flexio\Base\Mapper::flatten(); schema input needs to be null or an object; return false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            {
                "key" : "value"
            }
        ]
        ';
        $schema = '[]';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected =  false;
        \Flexio\Tests\Check::assertBoolean('B.5', '\Flexio\Base\Mapper::flatten(); schema input needs to be null or an object; return false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            {
                "key" : "value"
            }
        ]
        ';
        $schema = '{}';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key" : "value"
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('B.6', '\Flexio\Base\Mapper::flatten(); schema input needs to be null or an object; return false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            {
                "key" : "value"
            }
        ]
        ';
        $schema = array();
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected =  false;
        \Flexio\Tests\Check::assertBoolean('B.7', '\Flexio\Base\Mapper::flatten(); schema input needs to be null or an object; return false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            {
                "key" : "value"
            }
        ]
        ';
        $schema = new \stdClass();
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key" : "value"
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('B.8', '\Flexio\Base\Mapper::flatten(); schema input needs to be null or an object; return false otherwise',  $actual, $expected, $results);
    }
}
