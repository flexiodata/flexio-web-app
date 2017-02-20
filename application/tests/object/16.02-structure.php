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


class Test
{
    public function run(&$results)
    {
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
        $actual = \Flexio\Object\Structure::create(null);
        $expected = false;
        TestCheck::assertBoolean('B.1', 'Structure::create(); if a non-array input is specified, return false',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::create(false);
        $expected = false;
        TestCheck::assertBoolean('B.2', 'Structure::create(); if a non-array input is specified, return false',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::create(true);
        $expected = false;
        TestCheck::assertBoolean('B.3', 'Structure::create(); if a non-array input is specified, return false',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::create(1);
        $expected = false;
        TestCheck::assertBoolean('B.4', 'Structure::create(); if a non-array input is specified, return false',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::create('a');
        $expected = false;
        TestCheck::assertBoolean('B.5', 'Structure::create(); if a non-array input is specified, return false',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::create(new stdClass());
        $expected = false;
        TestCheck::assertBoolean('B.6', 'Structure::create(); if a non-array input is specified, return false',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
        ]
        ';
        TestCheck::assertInArray('B.7', 'Structure::create(); if an array is specified, don\'t create the structure if the array entries aren\'t valid columns',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            1,2,3
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info);
        $expected = false;
        TestCheck::assertBoolean('B.8', 'Structure::create(); if an array is specified, don\'t create the structure if the array entries aren\'t valid columns',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            [1]
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info);
        $expected = false;
        TestCheck::assertBoolean('B.9', 'Structure::create(); if an array is specified, don\'t create the structure if the array entries aren\'t valid columns',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info);
        $expected = false;
        TestCheck::assertBoolean('B.10', 'Structure::create(); if an array is specified, don\'t create the structure if the array entries aren\'t valid columns',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"text"}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"text"}
        ]
        ';
        TestCheck::assertInArray('B.11', 'Structure::create(); if an array is specified, don\'t create the structure if the array entries aren\'t valid columns',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"text"},
            {}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info);
        $expected = false;
        TestCheck::assertBoolean('B.12', 'Structure::create(); if an array is specified, don\'t create the structure if the array entries aren\'t valid columns',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {},
            {"name":"field1", "type":"text"}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info);
        $expected = false;
        TestCheck::assertBoolean('B.13', 'Structure::create(); if an array is specified, don\'t create the structure if the array entries aren\'t valid columns',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"}
        ]
        ';
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
        $column_info = '
        [
            {"name": true}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info);
        $expected = false;
        TestCheck::assertBoolean('C.1', 'Structure::create(); make sure the name is populated and is a character',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":""}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info);
        $expected = false;
        TestCheck::assertBoolean('C.2', 'Structure::create(); make sure the name is populated and is a character',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1"}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"text"}
        ]
        ';
        TestCheck::assertInArray('C.3', 'Structure::create(); if only the name is specified, set the type to text',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"Field 1"}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field 1"}
        ]
        ';
        TestCheck::assertInArray('C.4', 'Structure::create(); for the name, convert to lowercase, and preserve internal spaces',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"  Field 1  "}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field 1"}
        ]
        ';
        TestCheck::assertInArray('C.5', 'Structure::create(); for the name, trim leading/trailing spaces',  $actual, $expected, $results);



        // TEST: Structure::create(); column type entry checks

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1"}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"text"}
        ]
        ';
        TestCheck::assertInArray('D.1', 'Structure::create(); if only the name is specified, set the type to text',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type": false}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info);
        $expected = false;
        TestCheck::assertBoolean('D.2', 'Structure::create(); if type is populated, make sure it\'s one of the allowed types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":""}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info);
        $expected = false;
        TestCheck::assertBoolean('D.3', 'Structure::create(); if type is populated, make sure it\'s one of the allowed types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"char"}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info);
        $expected = false;
        TestCheck::assertBoolean('D.4', 'Structure::create(); if type is populated, make sure it\'s one of the allowed types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"text"}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"text"}
        ]
        ';
        TestCheck::assertInArray('D.5', 'Structure::create(); if type is populated, make sure it\'s one of the allowed types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"character"}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"character"}
        ]
        ';
        TestCheck::assertInArray('D.6', 'Structure::create(); if type is populated, make sure it\'s one of the allowed types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"widecharacter"}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"widecharacter"}
        ]
        ';
        TestCheck::assertInArray('D.7', 'Structure::create(); if type is populated, make sure it\'s one of the allowed types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"numeric"}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"numeric"}
        ]
        ';
        TestCheck::assertInArray('D.8', 'Structure::create(); if type is populated, make sure it\'s one of the allowed types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"double"}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"double"}
        ]
        ';
        TestCheck::assertInArray('D.9', 'Structure::create(); if type is populated, make sure it\'s one of the allowed types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"integer"}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"integer"}
        ]
        ';
        TestCheck::assertInArray('D.10', 'Structure::create(); if type is populated, make sure it\'s one of the allowed types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"date"}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"date"}
        ]
        ';
        TestCheck::assertInArray('D.11', 'Structure::create(); if type is populated, make sure it\'s one of the allowed types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"datetime"}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"datetime"}
        ]
        ';
        TestCheck::assertInArray('D.12', 'Structure::create(); if type is populated, make sure it\'s one of the allowed types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"boolean"}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"boolean"}
        ]
        ';
        TestCheck::assertInArray('D.13', 'Structure::create(); if type is populated, make sure it\'s one of the allowed types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"Character"}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"character"}
        ]
        ';
        TestCheck::assertInArray('D.14', 'Structure::create(); type parameter should be case-insensitive and converted to a lowercase value',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"DATE"}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"date"}
        ]
        ';
        TestCheck::assertInArray('D.15', 'Structure::create(); type parameter should be case-insensitive and converted to a lowercase value',  $actual, $expected, $results);



        // TEST: Structure::create(); column width entry checks

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"character", "width":false}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info);
        $expected = false;
        TestCheck::assertBoolean('E.1', 'Structure::create(); if width is populated, make sure it\'s an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"character", "width":""}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info);
        $expected = false;
        TestCheck::assertBoolean('E.2', 'Structure::create(); if width is populated, make sure it\'s an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"character", "width":1.1}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info);
        $expected = false;
        TestCheck::assertBoolean('E.3', 'Structure::create(); if width is populated, make sure it\'s an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"character", "width":1}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"character", "width":1}
        ]
        ';
        TestCheck::assertInArray('E.4', 'Structure::create(); if width is populated, make sure it\'s an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"text", "width":0}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"text", "width":null}
        ]
        ';
        TestCheck::assertInArray('E.5', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"text", "width":100000}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"text", "width":null}
        ]
        ';
        TestCheck::assertInArray('E.6', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"character", "width":0}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"character", "width":1}
        ]
        ';
        TestCheck::assertInArray('E.7', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"character", "width":100000}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"character", "width":255}
        ]
        ';
        TestCheck::assertInArray('E.8', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"widecharacter", "width":0}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"widecharacter", "width":1}
        ]
        ';
        TestCheck::assertInArray('E.9', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"widecharacter", "width":100000}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"widecharacter", "width":255}
        ]
        ';
        TestCheck::assertInArray('E.10', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"numeric", "width":0}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"numeric", "width":2}
        ]
        ';
        TestCheck::assertInArray('E.11', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"numeric", "width":100000}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"numeric", "width":18}
        ]
        ';
        TestCheck::assertInArray('E.12', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"double", "width":0}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"double", "width":8}
        ]
        ';
        TestCheck::assertInArray('E.13', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"double", "width":100000}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"double", "width":8}
        ]
        ';
        TestCheck::assertInArray('E.14', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"integer", "width":0}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"integer", "width":8}
        ]
        ';
        TestCheck::assertInArray('E.15', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"integer", "width":100000}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"integer", "width":8}
        ]
        ';
        TestCheck::assertInArray('E.16', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"date", "width":0}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"date", "width":8}
        ]
        ';
        TestCheck::assertInArray('E.17', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"date", "width":100000}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"date", "width":8}
        ]
        ';
        TestCheck::assertInArray('E.18', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"datetime", "width":0}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"datetime", "width":8}
        ]
        ';
        TestCheck::assertInArray('E.19', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"datetime", "width":100000}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"datetime", "width":8}
        ]
        ';
        TestCheck::assertInArray('E.20', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"boolean", "width":0}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"boolean", "width":1}
        ]
        ';
        TestCheck::assertInArray('E.21', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"boolean", "width":100000}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"boolean", "width":1}
        ]
        ';
        TestCheck::assertInArray('E.22', 'Structure::create(); adjust integer width values so they are appropriate for the type',  $actual, $expected, $results);



        // TEST: Structure::create(); column scale entry checks

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"numeric", "width":10, "scale":false}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info);
        $expected = false;
        TestCheck::assertBoolean('F.1', 'Structure::create(); if scale is populated, make sure it\'s an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"numeric", "width":10, "scale":""}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info);
        $expected = false;
        TestCheck::assertBoolean('F.2', 'Structure::create(); if scale is populated, make sure it\'s an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"numeric", "width":10, "scale":1.1}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info);
        $expected = false;
        TestCheck::assertBoolean('F.3', 'Structure::create(); if scale is populated, make sure it\'s an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"numeric", "width":10, "scale":1}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"numeric", "width":10, "scale":1}
        ]
        ';
        TestCheck::assertInArray('F.4', 'Structure::create(); if scale is populated, make sure it\'s an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"text", "width":1, "scale":-1}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"text", "width":null, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.5', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"text", "width":1, "scale":100000}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"text", "width":null, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.6', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"character", "width":1, "scale":0}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"character", "width":1, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.7', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"character", "width":1, "scale":100000}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"character", "width":1, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.8', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"widecharacter", "width":1, "scale":-1}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"widecharacter", "width":1, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.9', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"widecharacter", "width":1, "scale":100000}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"widecharacter", "width":1, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.10', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"numeric", "width":18, "scale":-1}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"numeric", "width":18, "scale":0}
        ]
        ';
        TestCheck::assertInArray('F.11', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"numeric", "width":18, "scale":100000}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"numeric", "width":18, "scale":12}
        ]
        ';
        TestCheck::assertInArray('F.12', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"double", "width":8, "scale":-1}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"double", "width":8, "scale":0}
        ]
        ';
        TestCheck::assertInArray('F.13', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"double", "width":8, "scale":100000}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"double", "width":8, "scale":12}
        ]
        ';
        TestCheck::assertInArray('F.14', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"integer", "width":8, "scale":-1}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"integer", "width":8, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.15', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"integer", "width":8, "scale":100000}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"integer", "width":8, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.16', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"date", "width":8, "scale":-1}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"date", "width":8, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.17', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"date", "width":8, "scale":100000}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"date", "width":8, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.18', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"datetime", "width":8, "scale":-1}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"datetime", "width":8, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.19', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"datetime", "width":8, "scale":100000}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"datetime", "width":8, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.20', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"boolean", "width":1, "scale":-1}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"boolean", "width":1, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.21', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"boolean", "width":1, "scale":100000}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"boolean", "width":1, "scale":null}
        ]
        ';
        TestCheck::assertInArray('F.22', 'Structure::create(); adjust integer scale values so they are appropriate for the type',  $actual, $expected, $results);



        // TEST: Structure::create(); column expression entry checks

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"character", "width":10, "expression":false}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info);
        $expected = false;
        TestCheck::assertBoolean('G.1', 'Structure::create(); if expression is populated, make sure it\'s a string',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"character", "width":10, "expression":1}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info);
        $expected = false;
        TestCheck::assertBoolean('G.2', 'Structure::create(); if expression is populated, make sure it\'s a string',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"character", "width":10, "expression":1.1}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info);
        $expected = false;
        TestCheck::assertBoolean('G.3', 'Structure::create(); if expression is populated, make sure it\'s a string',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"character", "width":10, "expression":""}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"character", "width":10, "expression":""}
        ]
        ';
        TestCheck::assertInArray('G.4', 'Structure::create(); if expression is populated, make sure it\'s a string',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"character", "width":10, "expression":"abc"}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"character", "width":10, "expression":"abc"}
        ]
        ';
        TestCheck::assertInArray('G.5', 'Structure::create(); if expression is populated and a string, make sure case-sensitive expression is saved',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"character", "width":10, "expression":"aBC"}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"character", "width":10, "expression":"aBC"}
        ]
        ';
        TestCheck::assertInArray('G.6', 'Structure::create(); if expression is populated and a string, make sure case-sensitive expression is saved',  $actual, $expected, $results);



        // TEST: Structure::create(); column entry; make sure a suitable internal store_name is created from the input name

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"character", "width":10, "scale":0, "unknown":"value"}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "store_name":"field1"}
        ]
        ';
        TestCheck::assertInArray('H.1', 'Structure::create(); make sure a suitable internal store_name is created from the input name',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field 1", "type":"character", "width":10, "scale":0, "unknown":"value"}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field 1", "store_name":"field_1"}
        ]
        ';
        TestCheck::assertInArray('H.2', 'Structure::create(); make sure a suitable internal store_name is created from the input name',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field #", "type":"character", "width":10, "scale":0, "unknown":"value"}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field #", "store_name":"field_no"}
        ]
        ';
        TestCheck::assertInArray('H.3', 'Structure::create(); make sure a suitable internal store_name is created from the input name',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field %", "type":"character", "width":10, "scale":0, "unknown":"value"}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field %", "store_name":"field_pct"}
        ]
        ';
        TestCheck::assertInArray('H.3', 'Structure::create(); make sure a suitable internal store_name is created from the input name',  $actual, $expected, $results);



        // TEST: Structure::create(); column entry; make sure unknown parameters aren't saved

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"character", "width":10, "scale":0, "unknown":"value"}
        ]
        ';
        $columns = \Flexio\Object\Structure::create($column_info)->get();
        $actual = isset($columns['unknown']) === false;
        $expected = true;
        TestCheck::assertBoolean('I.1', 'Structure::create(); make sure unknown parameters aren\'t saved',  $actual, $expected, $results);



        // TEST: Structure::create(); column entry; multiple entries

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field1", "type":"character", "width":10, "scale":0},
            {"name":"field2", "type":"character", "width":10, "scale":0}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field1", "type":"character", "width":10, "scale":null},
            {"name":"field2", "type":"character", "width":10, "scale":null}
        ]
        ';
        TestCheck::assertInArray('J.1', 'Structure::create(); make sure multiple columns can be added',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field", "type":"character", "width":5, "scale":0},
            {"name":"field", "type":"character", "width":10, "scale":0}
        ]
        ';
        $actual = \Flexio\Object\Structure::create($column_info)->get();
        $expected = '
        [
            {"name":"field", "type":"character", "width":5, "scale":null},
            {"name":"field_1", "type":"character", "width":10, "scale":null}
        ]
        ';
        TestCheck::assertInArray('J.2', 'Structure::create(); make sure duplicate fields are properly incremented',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info = '
        [
            {"name":"field", "type":"character", "width":5, "scale":0},
            {"name":"field", "type":"character", "width":10, "scale":0},
            {"name":"field_1", "type":"character", "width":15, "scale":0}
        ]
        ';
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
        $column_info = '
        [
            {"name":"field", "type":"character", "width":5, "scale":0},
            {"name":"field_1", "type":"character", "width":10, "scale":0},
            {"name":"field", "type":"character", "width":15, "scale":0}
        ]
        ';
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
        $column_info = '
        [
            {"name":"column 1", "type":"character", "width":5, "scale":0},
            {"name":"column 1", "type":"character", "width":10, "scale":0},
            {"name":"column ", "type":"character", "width":15, "scale":0}
        ]
        ';
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
