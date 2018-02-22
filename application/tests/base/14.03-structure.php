<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-11-07
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
        // TODO: add tests for merging of structures where there are slight differences
        // in fieldnames cause by capitalization; equality rules used for matching should
        // follow the same rules as used for looking up a field (e.g. if fieldnames are not
        // case sensitive so that Field1 and field1 are equivalent, then Field1 should be
        // merged with field1)


        // TEST: Structure::union(); no input

        // BEGIN TEST
        $actual = \Flexio\Base\Structure::union()->get();
        $expected = array();
        \Flexio\Tests\Check::assertArray('A.1', 'Structure::union(); if no input is specified, create an empty structure',  $actual, $expected, $results);



        // TEST: Structure::union(); non-array input

        // BEGIN TEST
        $actual = \Flexio\Base\Structure::union(null)->get();
        $expected = array();
        \Flexio\Tests\Check::assertArray('B.1', 'Structure::union(); if no input is specified, create an empty structure',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Flexio\Base\Structure::union(false);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('B.2', 'Structure::union(); if a non-array input is specified, throw an exception',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Flexio\Base\Structure::union(true);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('B.3', 'Structure::union(); if a non-array input is specified, throw an exception',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Flexio\Base\Structure::union(1);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('B.4', 'Structure::union(); if a non-array input is specified, throw an exception',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Flexio\Base\Structure::union('a');
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('B.5', 'Structure::union(); if a non-array input is specified, throw an exception',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Flexio\Base\Structure::union(new \stdClass());
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('B.6', 'Structure::union(); if a non-array input is specified, throw an exception',  $actual, $expected, $results);



        // TEST: Structure::union(); single structure input

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field1", "type":"text"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('C.1', 'Structure::union(); single column info input should produce a valid structure object',  $actual, $expected, $results);



        // TEST: Structure::union(); multiple structure input

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field1", "type":"text"}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field2", "type":"text"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('D.1', 'Structure::union(); input structure columns with the same name should be combined into a single column',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field1", "type":"text"}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field1", "type":"text"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('D.2', 'Structure::union(); input structure columns with the same name should be combined into a single column',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field1", "type":"text"}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field2", "type":"text"}
        ]
        ',true);
        $column_info3 = json_decode('
        [
            {"name":"field3", "type":"text"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('D.3', 'Structure::union(); input structure columns with the same name should be combined into a single column',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field1", "type":"text"}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field1", "type":"text"}
        ]
        ',true);
        $column_info3 = json_decode('
        [
            {"name":"field3", "type":"text"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('D.4', 'Structure::union(); input structure columns with the same name should be combined into a single column',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field1", "type":"text"}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field2", "type":"text"}
        ]
        ',true);
        $column_info3 = json_decode('
        [
            {"name":"field2", "type":"text"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('D.4', 'Structure::union(); input structure columns with the same name should be combined into a single column',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field1", "type":"text"}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field2", "type":"text"}
        ]
        ',true);
        $column_info3 = json_decode('
        [
            {"name":"field1", "type":"text"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('D.4', 'Structure::union(); input structure columns with the same name should be combined into a single column',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field2", "type":"text"},
            {"name":"field1", "type":"text"}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field2", "type":"text"}
        ]
        ',true);
        $column_info3 = json_decode('
        [
            {"name":"field1", "type":"text"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field2", "type":"text"},
            {"name":"field1", "type":"text"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('D.5', 'Structure::union(); input structure columns with the same name should be combined into a single column',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"FIELD2", "type":"text"}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"Field1", "type":"text"},
            {"name":"Field2", "type":"text"}
        ]
        ',true);
        $column_info3 = json_decode('
        [
            {"name":"FIELD1", "type":"text"},
            {"name":"field2", "type":"text"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('D.6', 'Structure::union(); merge fields that only differ by case',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"    field2", "type":"text"}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field1    ", "type":"text"},
            {"name":"field2    ", "type":"text"}
        ]
        ',true);
        $column_info3 = json_decode('
        [
            {"name":"    field1", "type":"text"},
            {"name":"field2    ", "type":"text"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('D.7', 'Structure::union(); merge fields that only differ by leading/trailing spaces',  $actual, $expected, $results);



        // TEST: Structure::union(); insert columns in output starting with most common structures

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field3", "type":"text"}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"},
        ]
        ',true);
        $column_info3 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('E.1', 'Structure::union(); insert columns in output starting with most common structures',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field2", "type":"text"}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"},
        ]
        ',true);
        $column_info3 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('E.2', 'Structure::union(); insert columns in output starting with most common structures',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field1", "type":"text"}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"},
        ]
        ',true);
        $column_info3 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('E.3', 'Structure::union(); insert columns in output starting with most common structures',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field3", "type":"text"},
            {"name":"field2", "type":"text"}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"},
        ]
        ',true);
        $column_info3 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('E.4', 'Structure::union(); insert columns in output starting with most common structures',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field3", "type":"text"},
            {"name":"field1", "type":"text"}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"},
        ]
        ',true);
        $column_info3 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('E.5', 'Structure::union(); insert columns in output starting with most common structures',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field2", "type":"text"},
            {"name":"field1", "type":"text"}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"},
        ]
        ',true);
        $column_info3 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('E.6', 'Structure::union(); insert columns in output starting with most common structures',  $actual, $expected, $results);


        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"},
            {"name":"field4", "type":"text"},
            {"name":"field5", "type":"text"}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field3", "type":"text"},
            {"name":"field4", "type":"text"}
        ]
        ',true);
        $column_info3 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field3", "type":"text"},
            {"name":"field4", "type":"text"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"},
            {"name":"field4", "type":"text"},
            {"name":"field5", "type":"text"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('E.7', 'Structure::union(); insert columns in output starting with most common structures',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field0", "type":"text"},
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"},
            {"name":"field4", "type":"text"},
            {"name":"field5", "type":"text"}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field3", "type":"text"},
            {"name":"field4", "type":"text"}
        ]
        ',true);
        $column_info3 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field3", "type":"text"},
            {"name":"field4", "type":"text"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field0", "type":"text"},
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"},
            {"name":"field4", "type":"text"},
            {"name":"field5", "type":"text"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('E.8', 'Structure::union(); insert columns in output starting with most common structures',  $actual, $expected, $results);



        // TEST: Structure::union(); compatible type conversion

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"character"},
            {"name":"field3", "type":"widecharacter"},
            {"name":"field4", "type":"numeric"},
            {"name":"field5", "type":"double"},
            {"name":"field6", "type":"integer"},
            {"name":"field7", "type":"date"},
            {"name":"field8", "type":"datetime"},
            {"name":"field9", "type":"boolean"}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"},
            {"name":"field4", "type":"text"},
            {"name":"field5", "type":"text"},
            {"name":"field6", "type":"text"},
            {"name":"field7", "type":"text"},
            {"name":"field8", "type":"text"},
            {"name":"field9", "type":"text"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"},
            {"name":"field4", "type":"text"},
            {"name":"field5", "type":"text"},
            {"name":"field6", "type":"text"},
            {"name":"field7", "type":"text"},
            {"name":"field8", "type":"text"},
            {"name":"field9", "type":"text"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('F.1', 'Structure::union(); use compatible type when fields have the same name and different types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"character"},
            {"name":"field3", "type":"widecharacter"},
            {"name":"field4", "type":"numeric"},
            {"name":"field5", "type":"double"},
            {"name":"field6", "type":"integer"},
            {"name":"field7", "type":"date"},
            {"name":"field8", "type":"datetime"},
            {"name":"field9", "type":"boolean"}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field1", "type":"character"},
            {"name":"field2", "type":"character"},
            {"name":"field3", "type":"character"},
            {"name":"field4", "type":"character"},
            {"name":"field5", "type":"character"},
            {"name":"field6", "type":"character"},
            {"name":"field7", "type":"character"},
            {"name":"field8", "type":"character"},
            {"name":"field9", "type":"character"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"character"},
            {"name":"field3", "type":"widecharacter"},
            {"name":"field4", "type":"character"},
            {"name":"field5", "type":"character"},
            {"name":"field6", "type":"character"},
            {"name":"field7", "type":"character"},
            {"name":"field8", "type":"character"},
            {"name":"field9", "type":"character"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('F.2', 'Structure::union(); use compatible type when fields have the same name and different types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"character"},
            {"name":"field3", "type":"widecharacter"},
            {"name":"field4", "type":"numeric"},
            {"name":"field5", "type":"double"},
            {"name":"field6", "type":"integer"},
            {"name":"field7", "type":"date"},
            {"name":"field8", "type":"datetime"},
            {"name":"field9", "type":"boolean"}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field1", "type":"widecharacter"},
            {"name":"field2", "type":"widecharacter"},
            {"name":"field3", "type":"widecharacter"},
            {"name":"field4", "type":"widecharacter"},
            {"name":"field5", "type":"widecharacter"},
            {"name":"field6", "type":"widecharacter"},
            {"name":"field7", "type":"widecharacter"},
            {"name":"field8", "type":"widecharacter"},
            {"name":"field9", "type":"widecharacter"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"widecharacter"},
            {"name":"field3", "type":"widecharacter"},
            {"name":"field4", "type":"widecharacter"},
            {"name":"field5", "type":"widecharacter"},
            {"name":"field6", "type":"widecharacter"},
            {"name":"field7", "type":"widecharacter"},
            {"name":"field8", "type":"widecharacter"},
            {"name":"field9", "type":"widecharacter"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('F.3', 'Structure::union(); use compatible type when fields have the same name and different types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"character"},
            {"name":"field3", "type":"widecharacter"},
            {"name":"field4", "type":"numeric"},
            {"name":"field5", "type":"double"},
            {"name":"field6", "type":"integer"},
            {"name":"field7", "type":"date"},
            {"name":"field8", "type":"datetime"},
            {"name":"field9", "type":"boolean"}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field1", "type":"numeric"},
            {"name":"field2", "type":"numeric"},
            {"name":"field3", "type":"numeric"},
            {"name":"field4", "type":"numeric"},
            {"name":"field5", "type":"numeric"},
            {"name":"field6", "type":"numeric"},
            {"name":"field7", "type":"numeric"},
            {"name":"field8", "type":"numeric"},
            {"name":"field9", "type":"numeric"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"character"},
            {"name":"field3", "type":"widecharacter"},
            {"name":"field4", "type":"numeric"},
            {"name":"field5", "type":"numeric"},
            {"name":"field6", "type":"numeric"},
            {"name":"field7", "type":"character"},
            {"name":"field8", "type":"character"},
            {"name":"field9", "type":"character"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('F.4', 'Structure::union(); use compatible type when fields have the same name and different types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"character"},
            {"name":"field3", "type":"widecharacter"},
            {"name":"field4", "type":"numeric"},
            {"name":"field5", "type":"double"},
            {"name":"field6", "type":"integer"},
            {"name":"field7", "type":"date"},
            {"name":"field8", "type":"datetime"},
            {"name":"field9", "type":"boolean"}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field1", "type":"double"},
            {"name":"field2", "type":"double"},
            {"name":"field3", "type":"double"},
            {"name":"field4", "type":"double"},
            {"name":"field5", "type":"double"},
            {"name":"field6", "type":"double"},
            {"name":"field7", "type":"double"},
            {"name":"field8", "type":"double"},
            {"name":"field9", "type":"double"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"character"},
            {"name":"field3", "type":"widecharacter"},
            {"name":"field4", "type":"numeric"},
            {"name":"field5", "type":"double"},
            {"name":"field6", "type":"double"},
            {"name":"field7", "type":"character"},
            {"name":"field8", "type":"character"},
            {"name":"field9", "type":"character"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('F.5', 'Structure::union(); use compatible type when fields have the same name and different types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"character"},
            {"name":"field3", "type":"widecharacter"},
            {"name":"field4", "type":"numeric"},
            {"name":"field5", "type":"double"},
            {"name":"field6", "type":"integer"},
            {"name":"field7", "type":"date"},
            {"name":"field8", "type":"datetime"},
            {"name":"field9", "type":"boolean"}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field1", "type":"integer"},
            {"name":"field2", "type":"integer"},
            {"name":"field3", "type":"integer"},
            {"name":"field4", "type":"integer"},
            {"name":"field5", "type":"integer"},
            {"name":"field6", "type":"integer"},
            {"name":"field7", "type":"integer"},
            {"name":"field8", "type":"integer"},
            {"name":"field9", "type":"integer"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"character"},
            {"name":"field3", "type":"widecharacter"},
            {"name":"field4", "type":"numeric"},
            {"name":"field5", "type":"double"},
            {"name":"field6", "type":"integer"},
            {"name":"field7", "type":"character"},
            {"name":"field8", "type":"character"},
            {"name":"field9", "type":"character"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('F.6', 'Structure::union(); use compatible type when fields have the same name and different types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"character"},
            {"name":"field3", "type":"widecharacter"},
            {"name":"field4", "type":"numeric"},
            {"name":"field5", "type":"double"},
            {"name":"field6", "type":"integer"},
            {"name":"field7", "type":"date"},
            {"name":"field8", "type":"datetime"},
            {"name":"field9", "type":"boolean"}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field1", "type":"date"},
            {"name":"field2", "type":"date"},
            {"name":"field3", "type":"date"},
            {"name":"field4", "type":"date"},
            {"name":"field5", "type":"date"},
            {"name":"field6", "type":"date"},
            {"name":"field7", "type":"date"},
            {"name":"field8", "type":"date"},
            {"name":"field9", "type":"date"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"character"},
            {"name":"field3", "type":"widecharacter"},
            {"name":"field4", "type":"character"},
            {"name":"field5", "type":"character"},
            {"name":"field6", "type":"character"},
            {"name":"field7", "type":"date"},
            {"name":"field8", "type":"datetime"},
            {"name":"field9", "type":"character"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('F.7', 'Structure::union(); use compatible type when fields have the same name and different types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"character"},
            {"name":"field3", "type":"widecharacter"},
            {"name":"field4", "type":"numeric"},
            {"name":"field5", "type":"double"},
            {"name":"field6", "type":"integer"},
            {"name":"field7", "type":"date"},
            {"name":"field8", "type":"datetime"},
            {"name":"field9", "type":"boolean"}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field1", "type":"datetime"},
            {"name":"field2", "type":"datetime"},
            {"name":"field3", "type":"datetime"},
            {"name":"field4", "type":"datetime"},
            {"name":"field5", "type":"datetime"},
            {"name":"field6", "type":"datetime"},
            {"name":"field7", "type":"datetime"},
            {"name":"field8", "type":"datetime"},
            {"name":"field9", "type":"datetime"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"character"},
            {"name":"field3", "type":"widecharacter"},
            {"name":"field4", "type":"character"},
            {"name":"field5", "type":"character"},
            {"name":"field6", "type":"character"},
            {"name":"field7", "type":"datetime"},
            {"name":"field8", "type":"datetime"},
            {"name":"field9", "type":"character"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('F.8', 'Structure::union(); use compatible type when fields have the same name and different types',  $actual, $expected, $results);


        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"character"},
            {"name":"field3", "type":"widecharacter"},
            {"name":"field4", "type":"numeric"},
            {"name":"field5", "type":"double"},
            {"name":"field6", "type":"integer"},
            {"name":"field7", "type":"date"},
            {"name":"field8", "type":"datetime"},
            {"name":"field9", "type":"boolean"}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field1", "type":"boolean"},
            {"name":"field2", "type":"boolean"},
            {"name":"field3", "type":"boolean"},
            {"name":"field4", "type":"boolean"},
            {"name":"field5", "type":"boolean"},
            {"name":"field6", "type":"boolean"},
            {"name":"field7", "type":"boolean"},
            {"name":"field8", "type":"boolean"},
            {"name":"field9", "type":"boolean"}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"character"},
            {"name":"field3", "type":"widecharacter"},
            {"name":"field4", "type":"character"},
            {"name":"field5", "type":"character"},
            {"name":"field6", "type":"character"},
            {"name":"field7", "type":"character"},
            {"name":"field8", "type":"character"},
            {"name":"field9", "type":"boolean"}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('F.9', 'Structure::union(); use compatible type when fields have the same name and different types',  $actual, $expected, $results);



        // TEST: Structure::union(); largest width conversion

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field1", "type":"character", "width": 20},
            {"name":"field2", "type":"character", "width": 30}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field1", "type":"character", "width": 10},
            {"name":"field2", "type":"character", "width": 40}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2))->get();
        $expected = '
        [
            {"name":"field1", "type":"character", "width": 20},
            {"name":"field2", "type":"character", "width": 40}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('G.1', 'Structure::union(); use compatible type when fields have the same name and different types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field1", "type":"widecharacter", "width": 20},
            {"name":"field2", "type":"widecharacter", "width": 30}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field1", "type":"widecharacter", "width": 10},
            {"name":"field2", "type":"widecharacter", "width": 40}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2))->get();
        $expected = '
        [
            {"name":"field1", "type":"widecharacter", "width": 20},
            {"name":"field2", "type":"widecharacter", "width": 40}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('G.2', 'Structure::union(); use compatible type when fields have the same name and different types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field1", "type":"numeric", "width": 8},
            {"name":"field2", "type":"numeric", "width": 10}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field1", "type":"numeric", "width": 4},
            {"name":"field2", "type":"numeric", "width": 12}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2))->get();
        $expected = '
        [
            {"name":"field1", "type":"numeric", "width": 8},
            {"name":"field2", "type":"numeric", "width": 12}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('G.3', 'Structure::union(); use compatible type when fields have the same name and different types',  $actual, $expected, $results);



        // TEST: Structure::union(); largest scale conversion

        // BEGIN TEST
        $column_info1 = json_decode('
        [
            {"name":"field1", "type":"numeric", "width": 10, "scale": 2},
            {"name":"field2", "type":"numeric", "width": 10, "scale": 4}
        ]
        ',true);
        $column_info2 = json_decode('
        [
            {"name":"field1", "type":"numeric", "width": 10, "scale": 6},
            {"name":"field2", "type":"numeric", "width": 10, "scale": 3}
        ]
        ',true);
        $actual = \Flexio\Base\Structure::union(array($column_info1, $column_info2))->get();
        $expected = '
        [
            {"name":"field1", "type":"numeric", "width": 10, "scale": 6},
            {"name":"field2", "type":"numeric", "width": 10, "scale": 4}
        ]
        ';
        \Flexio\Tests\Check::assertInArray('H.1', 'Structure::union(); use compatible type when fields have the same name and different types',  $actual, $expected, $results);
    }
}
