<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-10-21
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


        // TEST: Structure::enum(); no input

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $actual = \Flexio\Object\Structure::create($column_info)->enum();
        $expected = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('A.1', 'Structure::enum(); if no input is specified, return all the columns',  $actual, $expected, $results);



        // TEST: Structure::enum(); non-array inputs

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::create()->enum(null);
        $expected = array();
        TestCheck::assertInArray('B.1', 'Structure::enum(); if a non-array input is specified, return an empty array',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $actual = '';
        try
        {
            \Flexio\Object\Structure::create($column_info)->enum('a');
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('B.2', 'Structure::enum(); if a non-array input is specified, throw an exception',  $actual, $expected, $results);



        // TEST: Structure::enum(); matching column name

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"}
        ]
        ',true);
        $specified_columns = array();
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = array();
        TestCheck::assertInArray('C.1', 'Structure::enum(); return empty array if no columns are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
        ]
        ',true);
        $specified_columns = array('a');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = array();
        TestCheck::assertInArray('C.2', 'Structure::enum(); return empty array if no columns are available',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $specified_columns = array('a');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"a", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('C.3', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $specified_columns = array('b');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"b", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('C.4', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $specified_columns = array('c');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"c", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('C.5', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $specified_columns = array('a','b');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('C.6', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $specified_columns = array('a','c');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('C.7', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $specified_columns = array('b','c');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('C.8', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $specified_columns = array('a','b','c');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('C.9', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $specified_columns = array('c','a','b');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"c", "type":"character"},
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('C.10', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $specified_columns = array('x','a','y','b','c','z');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('C.11', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $specified_columns = array('c','x','a','y','b');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"c", "type":"character"},
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('C.12', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"aa", "type":"character"},
            {"name":"ab", "type":"character"},
            {"name":"ac", "type":"character"}
        ]
        ',true);
        $specified_columns = array('a');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = array();
        TestCheck::assertInArray('C.13', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"aa", "type":"character"},
            {"name":"ab", "type":"character"},
            {"name":"ac", "type":"character"}
        ]
        ',true);
        $specified_columns = array('b');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = array();
        TestCheck::assertInArray('C.14', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"aa", "type":"character"},
            {"name":"ab", "type":"character"},
            {"name":"ac", "type":"character"}
        ]
        ',true);
        $specified_columns = array('c');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = array();
        TestCheck::assertInArray('C.15', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"aa", "type":"character"},
            {"name":"ab", "type":"character"},
            {"name":"ac", "type":"character"}
        ]
        ',true);
        $specified_columns = array('aa');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"aa", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('C.16', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"aa", "type":"character"},
            {"name":"ab", "type":"character"},
            {"name":"ac", "type":"character"}
        ]
        ',true);
        $specified_columns = array('ab');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"ab", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('C.17', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"aa", "type":"character"},
            {"name":"ab", "type":"character"},
            {"name":"ac", "type":"character"}
        ]
        ',true);
        $specified_columns = array('ac');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"ac", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('C.18', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"aa", "type":"character"},
            {"name":"ab", "type":"character"},
            {"name":"ac", "type":"character"}
        ]
        ',true);
        $specified_columns = array('aab');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = array();
        TestCheck::assertInArray('C.19', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"aa", "type":"character"},
            {"name":"ab", "type":"character"},
            {"name":"ac", "type":"character"}
        ]
        ',true);
        $specified_columns = array('zac');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = array();
        TestCheck::assertInArray('C.20', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);



        // TEST: Structure::enum(); case insensitivity

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $specified_columns = array('A');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"a", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('D.1', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $specified_columns = array('B');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"b", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('D.2', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $specified_columns = array('C');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"c", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('D.3', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"aa", "type":"character"},
            {"name":"ab", "type":"character"},
            {"name":"ac", "type":"character"}
        ]
        ',true);
        $specified_columns = array('Aa');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"aa", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('D.4', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"aa", "type":"character"},
            {"name":"ab", "type":"character"},
            {"name":"ac", "type":"character"}
        ]
        ',true);
        $specified_columns = array('aB');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"ab", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('D.5', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"aa", "type":"character"},
            {"name":"ab", "type":"character"},
            {"name":"ac", "type":"character"}
        ]
        ',true);
        $specified_columns = array('AC');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"ac", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('D.6', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"A", "type":"character"},
            {"name":"B", "type":"character"},
            {"name":"C", "type":"character"}
        ]
        ',true);
        $specified_columns = array('a');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"a", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('D.7', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"A", "type":"character"},
            {"name":"B", "type":"character"},
            {"name":"C", "type":"character"}
        ]
        ',true);
        $specified_columns = array('b');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"b", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('D.8', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"A", "type":"character"},
            {"name":"B", "type":"character"},
            {"name":"C", "type":"character"}
        ]
        ',true);
        $specified_columns = array('c');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"c", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('D.9', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"AA", "type":"character"},
            {"name":"AB", "type":"character"},
            {"name":"AC", "type":"character"}
        ]
        ',true);
        $specified_columns = array('Aa');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"aa", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('D.10', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"AA", "type":"character"},
            {"name":"AB", "type":"character"},
            {"name":"AC", "type":"character"}
        ]
        ',true);
        $specified_columns = array('aB');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"ab", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('D.11', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"AA", "type":"character"},
            {"name":"AB", "type":"character"},
            {"name":"AC", "type":"character"}
        ]
        ',true);
        $specified_columns = array('AC');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"ac", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('D.12', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);



        // TEST: Structure::enum(); wildcard column selection

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"}
        ]
        ',true);
        $specified_columns = array('*');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"a", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('E.1', 'Structure::enum(); return columns satisfying wildcard conditions',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $specified_columns = array('*');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('E.2', 'Structure::enum(); return columns satisfying wildcard conditions',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"A", "type":"character"},
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"B", "type":"character"},
            {"name":"C", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $specified_columns = array('*');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('E.3', 'Structure::enum(); return columns satisfying wildcard conditions',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"c", "type":"character"},
            {"name":"a", "type":"character"},
            {"name":"C", "type":"character"},
            {"name":"B", "type":"character"},
            {"name":"A", "type":"character"},
            {"name":"b", "type":"character"}
        ]
        ',true);
        $specified_columns = array('*');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"c", "type":"character"},
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('E.4', 'Structure::enum(); return columns satisfying wildcard conditions',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $specified_columns = array('*','x','y');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('E.5', 'Structure::enum(); return columns satisfying wildcard conditions',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $specified_columns = array('x','y','*');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('E.6', 'Structure::enum(); return columns satisfying wildcard conditions',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"numeric"},
            {"name":"c", "type":"double"},
            {"name":"d", "type":"integer"},
            {"name":"e", "type":"date"},
            {"name":"f", "type":"datetime"},
            {"name":"g", "type":"boolean"}
        ]
        ',true);
        $specified_columns = array('*');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"numeric"},
            {"name":"c", "type":"double"},
            {"name":"d", "type":"integer"},
            {"name":"e", "type":"date"},
            {"name":"f", "type":"datetime"},
            {"name":"g", "type":"boolean"}
        ]
        ',true);
        TestCheck::assertInArray('E.7', 'Structure::enum(); return columns satisfying wildcard conditions',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"numeric"},
            {"name":"c", "type":"double"},
            {"name":"d", "type":"integer"},
            {"name":"e", "type":"date"},
            {"name":"f", "type":"datetime"},
            {"name":"g", "type":"boolean"}
        ]
        ',true);
        $specified_columns = array('/');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
        ]
        ',true);
        TestCheck::assertInArray('E.8', 'Structure::enum(); return columns satisfying wildcard conditions',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"abc", "type":"character"},
            {"name":"bca", "type":"character"},
            {"name":"Ab", "type":"character"},
            {"name":"efg", "type":"character"}
        ]
        ',true);
        $specified_columns = array('a[a-z]*');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"abc", "type":"character"},
            {"name":"ab", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('E.9', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"abc", "type":"character"},
            {"name":"bca", "type":"character"},
            {"name":"Ab", "type":"character"},
            {"name":"efg", "type":"character"}
        ]
        ',true);
        $specified_columns = array('*a');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
        ]
        ',true);
        TestCheck::assertInArray('E.10', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"abc", "type":"character"},
            {"name":"bca", "type":"character"},
            {"name":"Ab", "type":"character"},
            {"name":"efg", "type":"character"}
        ]
        ',true);
        $specified_columns = array('b[a-z]*');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"bca", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('E.11', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"abc", "type":"character"},
            {"name":"bca", "type":"character"},
            {"name":"Ab", "type":"character"},
            {"name":"efg", "type":"character"}
        ]
        ',true);
        $specified_columns = array('e[a-z]*','b[a-z]*');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"efg", "type":"character"},
            {"name":"bca", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('E.12', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"abc", "type":"character"},
            {"name":"bca", "type":"character"},
            {"name":"Ab", "type":"character"},
            {"name":"efg", "type":"character"}
        ]
        ',true);
        $specified_columns = array('b[A-Z]*','a[A-Z]*');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"bca", "type":"character"},
            {"name":"a", "type":"character"},
            {"name":"abc", "type":"character"},
            {"name":"ab", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('E.13', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"numeric"},
            {"name":"c", "type":"double"},
            {"name":"d", "type":"integer"},
            {"name":"e", "type":"date"},
            {"name":"f", "type":"datetime"},
            {"name":"g", "type":"boolean"}
        ]
        ',true);
        $specified_columns = array(':text');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"a", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('E.14', 'Structure::enum(); return columns satisfying wildcard conditions',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"numeric"},
            {"name":"c", "type":"double"},
            {"name":"d", "type":"integer"},
            {"name":"e", "type":"date"},
            {"name":"f", "type":"datetime"},
            {"name":"g", "type":"boolean"}
        ]
        ',true);
        $specified_columns = array(':number');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"b", "type":"numeric"},
            {"name":"c", "type":"double"},
            {"name":"d", "type":"integer"}
        ]
        ',true);
        TestCheck::assertInArray('E.15', 'Structure::enum(); return columns satisfying wildcard conditions',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"numeric"},
            {"name":"c", "type":"double"},
            {"name":"d", "type":"integer"},
            {"name":"e", "type":"date"},
            {"name":"f", "type":"datetime"},
            {"name":"g", "type":"boolean"}
        ]
        ',true);
        $specified_columns = array(':date');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"e", "type":"date"}
        ]
        ',true);
        TestCheck::assertInArray('E.16', 'Structure::enum(); return columns satisfying wildcard conditions',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"numeric"},
            {"name":"c", "type":"double"},
            {"name":"d", "type":"integer"},
            {"name":"e", "type":"date"},
            {"name":"f", "type":"datetime"},
            {"name":"g", "type":"boolean"}
        ]
        ',true);
        $specified_columns = array(':datetime');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"f", "type":"datetime"}
        ]
        ',true);
        TestCheck::assertInArray('E.17', 'Structure::enum(); return columns satisfying wildcard conditions',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"numeric"},
            {"name":"c", "type":"double"},
            {"name":"d", "type":"integer"},
            {"name":"e", "type":"date"},
            {"name":"f", "type":"datetime"},
            {"name":"g", "type":"boolean"}
        ]
        ',true);
        $specified_columns = array(':boolean');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"g", "type":"boolean"}
        ]
        ',true);
        TestCheck::assertInArray('E.18', 'Structure::enum(); return columns satisfying wildcard conditions',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"numeric"},
            {"name":"c", "type":"double"},
            {"name":"d", "type":"integer"},
            {"name":"e", "type":"date"},
            {"name":"f", "type":"datetime"},
            {"name":"g", "type":"boolean"}
        ]
        ',true);
        $specified_columns = array(':text',':number');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"numeric"},
            {"name":"c", "type":"double"},
            {"name":"d", "type":"integer"}
        ]
        ',true);
        TestCheck::assertInArray('E.19', 'Structure::enum(); return columns satisfying wildcard conditions',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"numeric"},
            {"name":"c", "type":"double"},
            {"name":"d", "type":"integer"},
            {"name":"e", "type":"date"},
            {"name":"f", "type":"datetime"},
            {"name":"g", "type":"boolean"}
        ]
        ',true);
        $specified_columns = array(':date',':datetime');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"e", "type":"date"},
            {"name":"f", "type":"datetime"}
        ]
        ',true);
        TestCheck::assertInArray('E.20', 'Structure::enum(); return columns satisfying wildcard conditions',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"numeric"},
            {"name":"c", "type":"double"},
            {"name":"d", "type":"integer"},
            {"name":"e", "type":"date"},
            {"name":"f", "type":"datetime"},
            {"name":"g", "type":"boolean"}
        ]
        ',true);
        $specified_columns = array(':character',':numeric',':double',':integer',':date',':datetime',':boolean');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"e", "type":"date"},
            {"name":"f", "type":"datetime"},
            {"name":"g", "type":"boolean"}
        ]
        ',true);
        TestCheck::assertInArray('E.21', 'Structure::enum(); return columns satisfying wildcard conditions',  $actual, $expected, $results);



        // TEST: Structure::enum(); unique output

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $specified_columns = array('a','a');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"a", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('F.1', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $specified_columns = array('b','b');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"b", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('F.2', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $specified_columns = array('c','c');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"c", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('F.3', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $specified_columns = array('A','a','b','B','C','c');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('F.4', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"a", "type":"character"}
        ]
        ',true);
        $specified_columns = array('a','b','c');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"a", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('F.5', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"b", "type":"character"},
            {"name":"b", "type":"character"}
        ]
        ',true);
        $specified_columns = array('a','b','c');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"b", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('F.6', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"c", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $specified_columns = array('a','b','c');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"c", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('F.7', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"A", "type":"character"},
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"B", "type":"character"},
            {"name":"C", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $specified_columns = array('a','b','c');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('F.8', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"A", "type":"character"},
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"B", "type":"character"},
            {"name":"C", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $specified_columns = array('*','a','b','c');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('F.9', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        $specified_columns = array('*','*');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = json_decode('
        [
            {"name":"a", "type":"character"},
            {"name":"b", "type":"character"},
            {"name":"c", "type":"character"}
        ]
        ',true);
        TestCheck::assertInArray('F.10', 'Structure::enum(); return specified columns that exist in the order they\'re listed',  $actual, $expected, $results);



        // TEST: Structure::enum(); make sure some types of columns are automatically
        // filtered from the list of selected columns

        // BEGIN TEST
        $column_info = json_decode('
        [
            {"name":"xdrowid", "type":"character"}
        ]
        ',true);
        $specified_columns = array('xdrowid');
        $actual = \Flexio\Object\Structure::create($column_info)->enum($specified_columns);
        $expected = array();
        TestCheck::assertInArray('G.1', 'Structure::enum(); filter out invalid column names',  $actual, $expected, $results);
    }
}
