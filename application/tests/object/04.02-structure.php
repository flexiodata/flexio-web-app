<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-10-24
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();


        // TODO:
        //   - TEST: column entry parameter name adjustments; e.g. duplicates
        //   - TEST: make sure internal column storage names are set automatically and can't be overridden
        //   - TEST: push(), pop()


        // TEST: Structure::create(); no input

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::create()->get();
        $expected = array();
        TestCheck::assertArray('A.1', 'Structure::create(); if no input is specified, create an empty structure',  $actual, $expected, $results);



        // TEST: Structure::create(); column entries

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::create(null)->get();
        $expected = '
        [
        ]
        ';
        TestCheck::assertInArray('B.1', 'Structure::create(); if no input is specified, create an empty structure',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Flexio\Object\Structure::create(false);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('B.2', 'Structure::create(); if a non-array input is specified, throw an exception',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Flexio\Object\Structure::create(true);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('B.3', 'Structure::create(); if a non-array input is specified, throw an exception',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Flexio\Object\Structure::create(1);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('B.4', 'Structure::create(); if a non-array input is specified, throw an exception',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Flexio\Object\Structure::create('a');
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('B.5', 'Structure::create(); if a non-array input is specified, throw an exception',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Flexio\Object\Structure::create(new \stdClass());
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('B.6', 'Structure::create(); if a non-array input is specified, throw an exception',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
        ]
        ';
        TestCheck::assertInArray('B.7', 'Structure::create(); if an array is specified, don\'t create the structure if the array entries aren\'t valid columns',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            1,2,3
        ]
        ',true);
        $actual = '';
        try
        {
            \Flexio\Object\Structure::create($column_info);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('B.8', 'Structure::create(); if an array is specified, don\'t create the structure if the array entries aren\'t valid columns',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            [1]
        ]
        ',true);
        $actual = '';
        try
        {
            \Flexio\Object\Structure::create($column_info);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('B.9', 'Structure::create(); if an array is specified, don\'t create the structure if the array entries aren\'t valid columns',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {}
        ]
        ',true);
        $actual = '';
        try
        {
            \Flexio\Object\Structure::create($column_info);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('B.10', 'Structure::create(); if an array is specified, don\'t create the structure if the array entries aren\'t valid columns',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"text"}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"text"}
        ]
        ';
        TestCheck::assertInArray('B.11', 'Structure::create(); if an array is specified, don\'t create the structure if the array entries aren\'t valid columns',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"text"},
            {}
        ]
        ',true);
        $actual = '';
        try
        {
            \Flexio\Object\Structure::create($column_info);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('B.12', 'Structure::create(); if an array is specified, don\'t create the structure if the array entries aren\'t valid columns',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {},
            {"name":"field1", "type":"text"}
        ]
        ',true);
        $actual = '';
        try
        {
            \Flexio\Object\Structure::create($column_info);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('B.13', 'Structure::create(); if an array is specified, don\'t create the structure if the array entries aren\'t valid columns',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"}
        ]
        ';
        TestCheck::assertInArray('B.14', 'Structure::create(); make sure basic creation happens with text input',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"}
        ]
        ',true);
        TestCheck::assertInArray('B.15', 'Structure::create(); make sure basic creation happens with text input',  $actual, $expected, $results);



        // TEST: Structure::create(); column name entry checks

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name": true}
        ]
        ',true);
        $actual = '';
        try
        {
            \Flexio\Object\Structure::create($column_info);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('C.1', 'Structure::create(); make sure the name is populated and is a character',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":""}
        ]
        ',true);
        $actual = '';
        try
        {
            \Flexio\Object\Structure::create($column_info);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('C.2', 'Structure::create(); make sure the name is populated and is a character',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1"}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"text"}
        ]
        ';
        TestCheck::assertInArray('C.3', 'Structure::create(); if only the name is specified, set the type to text',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"Field 1"}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field 1"}
        ]
        ';
        TestCheck::assertInArray('C.4', 'Structure::create(); for the name, convert to lowercase, and preserve internal spaces',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"  Field 1  "}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field 1"}
        ]
        ';
        TestCheck::assertInArray('C.5', 'Structure::create(); for the name, trim leading/trailing spaces',  $actual, $expected, $results);



        // TEST: Structure::create(); column type entry checks

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1"}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"text"}
        ]
        ';
        TestCheck::assertInArray('D.1', 'Structure::create(); if only the name is specified, set the type to text',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type": false}
        ]
        ',true);
        $actual = '';
        try
        {
            \Flexio\Object\Structure::create($column_info);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('D.2', 'Structure::create(); if type is populated, make sure it\'s one of the allowed types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":""}
        ]
        ',true);
        $actual = '';
        try
        {
            \Flexio\Object\Structure::create($column_info);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('D.3', 'Structure::create(); if type is populated, make sure it\'s one of the allowed types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"char"}
        ]
        ',true);
        $actual = '';
        try
        {
            \Flexio\Object\Structure::create($column_info);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('D.4', 'Structure::create(); if type is populated, make sure it\'s one of the allowed types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"text"}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"text"}
        ]
        ';
        TestCheck::assertInArray('D.5', 'Structure::create(); if type is populated, make sure it\'s one of the allowed types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"character"}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"character"}
        ]
        ';
        TestCheck::assertInArray('D.6', 'Structure::create(); if type is populated, make sure it\'s one of the allowed types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"widecharacter"}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"widecharacter"}
        ]
        ';
        TestCheck::assertInArray('D.7', 'Structure::create(); if type is populated, make sure it\'s one of the allowed types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"numeric"}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"numeric"}
        ]
        ';
        TestCheck::assertInArray('D.8', 'Structure::create(); if type is populated, make sure it\'s one of the allowed types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"double"}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"double"}
        ]
        ';
        TestCheck::assertInArray('D.9', 'Structure::create(); if type is populated, make sure it\'s one of the allowed types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"integer"}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"integer"}
        ]
        ';
        TestCheck::assertInArray('D.10', 'Structure::create(); if type is populated, make sure it\'s one of the allowed types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"date"}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"date"}
        ]
        ';
        TestCheck::assertInArray('D.11', 'Structure::create(); if type is populated, make sure it\'s one of the allowed types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"datetime"}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"datetime"}
        ]
        ';
        TestCheck::assertInArray('D.12', 'Structure::create(); if type is populated, make sure it\'s one of the allowed types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"boolean"}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"boolean"}
        ]
        ';
        TestCheck::assertInArray('D.13', 'Structure::create(); if type is populated, make sure it\'s one of the allowed types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"Character"}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"character"}
        ]
        ';
        TestCheck::assertInArray('D.14', 'Structure::create(); type parameter should be case-insensitive and converted to a lowercase value',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"DATE"}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"date"}
        ]
        ';
        TestCheck::assertInArray('D.15', 'Structure::create(); type parameter should be case-insensitive and converted to a lowercase value',  $actual, $expected, $results);



        // TEST: Structure::create(); column width entry checks

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"character", "width":false}
        ]
        ',true);
        $actual = '';
        try
        {
            \Flexio\Object\Structure::create($column_info);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('E.1', 'Structure::create(); if width is populated, make sure it\'s an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"character", "width":""}
        ]
        ',true);
        $actual = '';
        try
        {
            \Flexio\Object\Structure::create($column_info);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('E.2', 'Structure::create(); if width is populated, make sure it\'s an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"character", "width":1.1}
        ]
        ',true);
        $actual = '';
        try
        {
            \Flexio\Object\Structure::create($column_info);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('E.3', 'Structure::create(); if width is populated, make sure it\'s an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"character", "width":1}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"character", "width":1}
        ]
        ';
        TestCheck::assertInArray('E.4', 'Structure::create(); if width is populated, make sure it\'s an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"text", "width":0}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"text", "width":null}
        ]
        ';
        TestCheck::assertInArray('E.5', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info =json_decode( '
        [
            {"name":"field1", "type":"text", "width":100000}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"text", "width":null}
        ]
        ';
        TestCheck::assertInArray('E.6', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"character", "width":0}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"character", "width":1}
        ]
        ';
        TestCheck::assertInArray('E.7', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"character", "width":100000}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"character", "width":255}
        ]
        ';
        TestCheck::assertInArray('E.8', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"widecharacter", "width":0}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"widecharacter", "width":1}
        ]
        ';
        TestCheck::assertInArray('E.9', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"widecharacter", "width":100000}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"widecharacter", "width":255}
        ]
        ';
        TestCheck::assertInArray('E.10', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"numeric", "width":0}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"numeric", "width":2}
        ]
        ';
        TestCheck::assertInArray('E.11', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"numeric", "width":100000}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"numeric", "width":18}
        ]
        ';
        TestCheck::assertInArray('E.12', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"double", "width":0}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"double", "width":8}
        ]
        ';
        TestCheck::assertInArray('E.13', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"double", "width":100000}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"double", "width":8}
        ]
        ';
        TestCheck::assertInArray('E.14', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"integer", "width":0}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"integer", "width":8}
        ]
        ';
        TestCheck::assertInArray('E.15', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"integer", "width":100000}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"integer", "width":8}
        ]
        ';
        TestCheck::assertInArray('E.16', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"date", "width":0}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"date", "width":8}
        ]
        ';
        TestCheck::assertInArray('E.17', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"date", "width":100000}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"date", "width":8}
        ]
        ';
        TestCheck::assertInArray('E.18', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"datetime", "width":0}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"datetime", "width":8}
        ]
        ';
        TestCheck::assertInArray('E.19', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"datetime", "width":100000}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"datetime", "width":8}
        ]
        ';
        TestCheck::assertInArray('E.20', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"boolean", "width":0}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"boolean", "width":1}
        ]
        ';
        TestCheck::assertInArray('E.21', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"boolean", "width":100000}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"boolean", "width":1}
        ]
        ';
        TestCheck::assertInArray('E.22', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);



        // TEST: Structure::create(); column scale entry checks

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"numeric", "width":10, "scale":false}
        ]
        ',true);
        $actual = '';
        try
        {
            \Flexio\Object\Structure::create($column_info);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('F.1', 'Structure::create(); if scale is populated, make sure it\'s an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"numeric", "width":10, "scale":""}
        ]
        ';
        $actual = '';
        try
        {
            \Flexio\Object\Structure::create($column_info);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('F.2', 'Structure::create(); if scale is populated, make sure it\'s an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"numeric", "width":10, "scale":1.1}
        ]
        ',true);
        $actual = '';
        try
        {
            \Flexio\Object\Structure::create($column_info);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('F.3', 'Structure::create(); if scale is populated, make sure it\'s an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"numeric", "width":10, "scale":1}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"numeric", "width":10, "scale":1}
        ]
        ';
        TestCheck::assertInArray('F.4', 'Structure::create(); if scale is populated, make sure it\'s an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"text", "width":1, "scale":-1}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"text", "width":null, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.5', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"text", "width":1, "scale":100000}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"text", "width":null, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.6', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"character", "width":1, "scale":0}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = json_decode('
        [
            {"name":"field1", "type":"character", "width":1, "scale":null}
        ]
        ',true);
        TestCheck::assertInArray('F.7', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"character", "width":1, "scale":100000}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"character", "width":1, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.8', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"widecharacter", "width":1, "scale":-1}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"widecharacter", "width":1, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.9', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"widecharacter", "width":1, "scale":100000}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"widecharacter", "width":1, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.10', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"numeric", "width":18, "scale":-1}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"numeric", "width":18, "scale":0}
        ]
        ';
        TestCheck::assertInArray('F.11', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"numeric", "width":18, "scale":100000}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"numeric", "width":18, "scale":12}
        ]
        ';
        TestCheck::assertInArray('F.12', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"double", "width":8, "scale":-1}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"double", "width":8, "scale":0}
        ]
        ';
        TestCheck::assertInArray('F.13', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"double", "width":8, "scale":100000}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"double", "width":8, "scale":12}
        ]
        ';
        TestCheck::assertInArray('F.14', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"integer", "width":8, "scale":-1}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"integer", "width":8, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.15', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"integer", "width":8, "scale":100000}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"integer", "width":8, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.16', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"date", "width":8, "scale":-1}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"date", "width":8, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.17', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"date", "width":8, "scale":100000}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"date", "width":8, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.18', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"datetime", "width":8, "scale":-1}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"datetime", "width":8, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.19', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"datetime", "width":8, "scale":100000}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"datetime", "width":8, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.20', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"boolean", "width":1, "scale":-1}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"boolean", "width":1, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.21', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"boolean", "width":1, "scale":100000}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"boolean", "width":1, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.22', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);



        // TEST: Structure::create(); column expression entry checks

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"character", "width":10, "expression":false}
        ]
        ',true);
        $actual = '';
        try
        {
            \Flexio\Object\Structure::create($column_info);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('G.1', 'Structure::create(); if expression is populated, make sure it\'s a string',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"character", "width":10, "expression":1}
        ]
        ',true);
        $actual = '';
        try
        {
            \Flexio\Object\Structure::create($column_info);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('G.2', 'Structure::create(); if expression is populated, make sure it\'s a string',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"character", "width":10, "expression":1.1}
        ]
        ',true);
        $actual = '';
        try
        {
            \Flexio\Object\Structure::create($column_info);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('G.3', 'Structure::create(); if expression is populated, make sure it\'s a string',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"character", "width":10, "expression":""}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"character", "width":10, "expression":""}
        ]
        ';
        TestCheck::assertInArray('G.4', 'Structure::create(); if expression is populated, make sure it\'s a string',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"character", "width":10, "expression":"abc"}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"character", "width":10, "expression":"abc"}
        ]
        ';
        TestCheck::assertInArray('G.5', 'Structure::create(); if expression is populated and a string, make sure case-sensitive expression is saved',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"character", "width":10, "expression":"aBC"}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"character", "width":10, "expression":"aBC"}
        ]
        ';
        TestCheck::assertInArray('G.6', 'Structure::create(); if expression is populated and a string, make sure case-sensitive expression is saved',  $actual, $expected, $results);



        // TEST: Structure::create(); column entry; make sure a suitable internal store_name is created from the input name

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"character", "width":10, "scale":0, "unknown":"value"}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "store_name":"field1"}
        ]
        ';
        TestCheck::assertInArray('H.1', 'Structure::create(); make sure a suitable internal store_name is created from the input name',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field 1", "type":"character", "width":10, "scale":0, "unknown":"value"}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field 1", "store_name":"field_1"}
        ]
        ';
        TestCheck::assertInArray('H.2', 'Structure::create(); make sure a suitable internal store_name is created from the input name',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field #", "type":"character", "width":10, "scale":0, "unknown":"value"}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field #", "store_name":"field_no"}
        ]
        ';
        TestCheck::assertInArray('H.3', 'Structure::create(); make sure a suitable internal store_name is created from the input name',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field %", "type":"character", "width":10, "scale":0, "unknown":"value"}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field %", "store_name":"field_pct"}
        ]
        ';
        TestCheck::assertInArray('H.3', 'Structure::create(); make sure a suitable internal store_name is created from the input name',  $actual, $expected, $results);



        // TEST: Structure::create(); column entry; make sure unknown parameters aren't saved

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"character", "width":10, "scale":0, "unknown":"value"}
        ]
        ',true);
        $columns = \Flexio\Object\Structure::create($column_info)->get();
        $actual = isset($columns['unknown']) === false;
        $expected = true;
        TestCheck::assertBoolean('I.1', 'Structure::create(); make sure unknown parameters aren\'t saved',  $actual, $expected, $results);



        // TEST: Structure::create(); column entry; multiple entries

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field1", "type":"character", "width":10, "scale":0},
            {"name":"field2", "type":"character", "width":10, "scale":0}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"character", "width":10, "scale":null},
            {"name":"field2", "type":"character", "width":10, "scale":null}
        ]
        ';
        TestCheck::assertInArray('J.1', 'Structure::create(); make sure multiple columns can be added',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field", "type":"character", "width":5, "scale":0},
            {"name":"field", "type":"character", "width":10, "scale":0}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field", "type":"character", "width":5, "scale":null},
            {"name":"field_1", "type":"character", "width":10, "scale":null}
        ]
        ';
        TestCheck::assertInArray('J.2', 'Structure::create(); make sure duplicate fields are properly incremented',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field", "type":"character", "width":5, "scale":0},
            {"name":"field", "type":"character", "width":10, "scale":0},
            {"name":"field_1", "type":"character", "width":15, "scale":0}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field", "type":"character", "width":5, "scale":null},
            {"name":"field_1", "type":"character", "width":10, "scale":null},
            {"name":"field_1_1", "type":"character", "width":15, "scale":null}
        ]
        ';
        TestCheck::assertInArray('J.3', 'Structure::create(); make sure duplicate fields are properly incremented',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"field", "type":"character", "width":5, "scale":0},
            {"name":"field_1", "type":"character", "width":10, "scale":0},
            {"name":"field", "type":"character", "width":15, "scale":0}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field", "type":"character", "width":5, "scale":null},
            {"name":"field_1", "type":"character", "width":10, "scale":null},
            {"name":"field_2", "type":"character", "width":15, "scale":null}
        ]
        ';
        TestCheck::assertInArray('J.4', 'Structure::create(); make sure duplicate fields are properly incremented',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"column 1", "type":"character", "width":5, "scale":0},
            {"name":"column 1", "type":"character", "width":10, "scale":0},
            {"name":"column ", "type":"character", "width":15, "scale":0}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"column 1", "store_name":"column_1", "width":5, "scale":null},
            {"name":"column 1_1", "store_name":"column_1_1", "width":10, "scale":null},
            {"name":"column", "store_name":"f_column", "width":15, "scale":null}
        ]
        ';
        TestCheck::assertInArray('J.5', 'Structure::create(); make sure duplicate fields are properly incremented',  $actual, $expected, $results);
    }
}
