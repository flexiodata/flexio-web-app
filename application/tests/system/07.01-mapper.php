<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-07-22
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: check individual values with no schema

        // BEGIN TEST
        $data = null;
        $actual = \Mapper::flatten($data);
        $expected = '
        [
            {
                "field" : null
            }
        ]
        ';
        TestCheck::assertArray('A.1', '\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = false;
        $actual = \Mapper::flatten($data);
        $expected = '
        [
            {
                "field" : false
            }
        ]
        ';
        TestCheck::assertArray('A.2', '\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = true;
        $actual = \Mapper::flatten($data);
        $expected = '
        [
            {
                "field" : true
            }
        ]
        ';
        TestCheck::assertArray('A.3', '\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = -1;
        $actual = \Mapper::flatten($data);
        $expected = '
        [
            {
                "field" : -1
            }
        ]
        ';
        TestCheck::assertArray('A.4', '\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = -0.1;
        $actual = \Mapper::flatten($data);
        $expected = '
        [
            {
                "field" : -0.1
            }
        ]
        ';
        TestCheck::assertArray('A.5', '\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = 0;
        $actual = \Mapper::flatten($data);
        $expected = '
        [
            {
                "field" : 0
            }
        ]
        ';
        TestCheck::assertArray('A.6', '\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = 0.1;
        $actual = \Mapper::flatten($data);
        $expected = '
        [
            {
                "field" : 0.1
            }
        ]
        ';
        TestCheck::assertArray('A.7', '\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = 1;
        $actual = \Mapper::flatten($data);
        $expected = '
        [
            {
                "field" : 1
            }
        ]
        ';
        TestCheck::assertArray('A.8', '\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '';
        $actual = \Mapper::flatten($data);
        $expected = '
        [
            {
                "field" : ""
            }
        ]
        ';
        TestCheck::assertArray('A.9', '\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = 'a';
        $actual = \Mapper::flatten($data);
        $expected = '
        [
            {
                "field" : "a"
            }
        ]
        ';
        TestCheck::assertArray('A.10', '\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = 'A';
        $actual = \Mapper::flatten($data);
        $expected = '
        [
            {
                "field" : "A"
            }
        ]
        ';
        TestCheck::assertArray('A.11', '\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = 'aB';
        $actual = \Mapper::flatten($data);
        $expected = '
        [
            {
                "field" : "aB"
            }
        ]
        ';
        TestCheck::assertArray('A.12', '\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);


        // BEGIN TEST
        $data = '[]';
        $actual = \Mapper::flatten($data);
        $expected = '
        [
        ]
        ';
        TestCheck::assertArray('A.13', '\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '{}';
        $actual = \Mapper::flatten($data);
        $expected = '
        [
            {
            }
        ]
        ';
        TestCheck::assertArray('A.14', '\Mapper::flatten(); values are a single field with a single row and a default field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            {
                "key" : "value"
            }
        ]
        ';
        $schema = null;
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key" : "value"
            }
        ]
        ';
        TestCheck::assertArray('A.15', '\Mapper::flatten(); schema input needs to be null or an object; return false otherwise',  $actual, $expected, $results);



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
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key" : "value"
            }
        ]
        ';
        TestCheck::assertArray('B.1', '\Mapper::flatten(); schema input needs to be null or an object; return false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            {
                "key" : "value"
            }
        ]
        ';
        $schema = false;
        $actual = \Mapper::flatten($data, $schema);
        $expected =  false;
        TestCheck::assertBoolean('B.2', '\Mapper::flatten(); schema input needs to be null or an object; return false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            {
                "key" : "value"
            }
        ]
        ';
        $schema = 1;
        $actual = \Mapper::flatten($data, $schema);
        $expected =  false;
        TestCheck::assertBoolean('B.3', '\Mapper::flatten(); schema input needs to be null or an object; return false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            {
                "key" : "value"
            }
        ]
        ';
        $schema = 'a';
        $actual = \Mapper::flatten($data, $schema);
        $expected =  false;
        TestCheck::assertBoolean('B.4', '\Mapper::flatten(); schema input needs to be null or an object; return false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            {
                "key" : "value"
            }
        ]
        ';
        $schema = '[]';
        $actual = \Mapper::flatten($data, $schema);
        $expected =  false;
        TestCheck::assertBoolean('B.5', '\Mapper::flatten(); schema input needs to be null or an object; return false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            {
                "key" : "value"
            }
        ]
        ';
        $schema = '{}';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key" : "value"
            }
        ]
        ';
        TestCheck::assertArray('B.6', '\Mapper::flatten(); schema input needs to be null or an object; return false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            {
                "key" : "value"
            }
        ]
        ';
        $schema = array();
        $actual = \Mapper::flatten($data, $schema);
        $expected =  false;
        TestCheck::assertBoolean('B.7', '\Mapper::flatten(); schema input needs to be null or an object; return false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            {
                "key" : "value"
            }
        ]
        ';
        $schema = new stdClass();
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key" : "value"
            }
        ]
        ';
        TestCheck::assertArray('B.8', '\Mapper::flatten(); schema input needs to be null or an object; return false otherwise',  $actual, $expected, $results);
    }
}
