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
        $actual = \Flexio\Object\Structure::union()->get();
        $expected = array();
        TestCheck::assertArray('A.1', 'Structure::union(); if no input is specified, create an empty structure',  $actual, $expected, $results);



        // TEST: Structure::union(); non-array input

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::union(null);
        $expected = false;
        TestCheck::assertBoolean('B.1', 'Structure::union(); if a non-array input is specified, return false',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::union(false);
        $expected = false;
        TestCheck::assertBoolean('B.2', 'Structure::union(); if a non-array input is specified, return false',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::union(true);
        $expected = false;
        TestCheck::assertBoolean('B.3', 'Structure::union(); if a non-array input is specified, return false',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::union(1);
        $expected = false;
        TestCheck::assertBoolean('B.4', 'Structure::union(); if a non-array input is specified, return false',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::union('a');
        $expected = false;
        TestCheck::assertBoolean('B.5', 'Structure::union(); if a non-array input is specified, return false',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::union(new \stdClass());
        $expected = false;
        TestCheck::assertBoolean('B.6', 'Structure::union(); if a non-array input is specified, return false',  $actual, $expected, $results);



        // TEST: Structure::union(); single structure input

        // BEGIN TEST
        $column_info1 = '
        [
            {"name":"field1", "type":"text"}
        ]
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"}
        ]
        ';
        TestCheck::assertInArray('C.1', 'Structure::union(); single column info input should produce a valid structure object',  $actual, $expected, $results);



        // TEST: Structure::union(); multiple structure input

        // BEGIN TEST
        $column_info1 = '
        [
            {"name":"field1", "type":"text"}
        ]
        ';
        $column_info2 = '
        [
            {"name":"field2", "type":"text"}
        ]
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"}
        ]
        ';
        TestCheck::assertInArray('D.1', 'Structure::union(); input structure columns with the same name should be combined into a single column',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = '
        [
            {"name":"field1", "type":"text"}
        ]
        ';
        $column_info2 = '
        [
            {"name":"field1", "type":"text"}
        ]
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"}
        ]
        ';
        TestCheck::assertInArray('D.2', 'Structure::union(); input structure columns with the same name should be combined into a single column',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = '
        [
            {"name":"field1", "type":"text"}
        ]
        ';
        $column_info2 = '
        [
            {"name":"field2", "type":"text"}
        ]
        ';
        $column_info3 = '
        [
            {"name":"field3", "type":"text"}
        ]
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ';
        TestCheck::assertInArray('D.3', 'Structure::union(); input structure columns with the same name should be combined into a single column',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = '
        [
            {"name":"field1", "type":"text"}
        ]
        ';
        $column_info2 = '
        [
            {"name":"field1", "type":"text"}
        ]
        ';
        $column_info3 = '
        [
            {"name":"field3", "type":"text"}
        ]
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ';
        TestCheck::assertInArray('D.4', 'Structure::union(); input structure columns with the same name should be combined into a single column',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = '
        [
            {"name":"field1", "type":"text"}
        ]
        ';
        $column_info2 = '
        [
            {"name":"field2", "type":"text"}
        ]
        ';
        $column_info3 = '
        [
            {"name":"field2", "type":"text"}
        ]
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"}
        ]
        ';
        TestCheck::assertInArray('D.4', 'Structure::union(); input structure columns with the same name should be combined into a single column',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = '
        [
            {"name":"field1", "type":"text"}
        ]
        ';
        $column_info2 = '
        [
            {"name":"field2", "type":"text"}
        ]
        ';
        $column_info3 = '
        [
            {"name":"field1", "type":"text"}
        ]
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"}
        ]
        ';
        TestCheck::assertInArray('D.4', 'Structure::union(); input structure columns with the same name should be combined into a single column',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = '
        [
            {"name":"field2", "type":"text"},
            {"name":"field1", "type":"text"}
        ]
        ';
        $column_info2 = '
        [
            {"name":"field2", "type":"text"}
        ]
        ';
        $column_info3 = '
        [
            {"name":"field1", "type":"text"}
        ]
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field2", "type":"text"},
            {"name":"field1", "type":"text"}
        ]
        ';
        TestCheck::assertInArray('D.5', 'Structure::union(); input structure columns with the same name should be combined into a single column',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = '
        [
            {"name":"field1", "type":"text"},
            {"name":"FIELD2", "type":"text"}
        ]
        ';
        $column_info2 = '
        [
            {"name":"Field1", "type":"text"},
            {"name":"Field2", "type":"text"}
        ]
        ';
        $column_info3 = '
        [
            {"name":"FIELD1", "type":"text"},
            {"name":"field2", "type":"text"}
        ]
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"}
        ]
        ';
        TestCheck::assertInArray('D.6', 'Structure::union(); merge fields that only differ by case',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = '
        [
            {"name":"field1", "type":"text"},
            {"name":"    field2", "type":"text"}
        ]
        ';
        $column_info2 = '
        [
            {"name":"field1    ", "type":"text"},
            {"name":"field2    ", "type":"text"}
        ]
        ';
        $column_info3 = '
        [
            {"name":"    field1", "type":"text"},
            {"name":"field2    ", "type":"text"}
        ]
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"}
        ]
        ';
        TestCheck::assertInArray('D.7', 'Structure::union(); merge fields that only differ by leading/trailing spaces',  $actual, $expected, $results);



        // TEST: Structure::union(); insert columns in output starting with most common structures

        // BEGIN TEST
        $column_info1 = '
        [
            {"name":"field3", "type":"text"}
        ]
        ';
        $column_info2 = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"},
        ]
        ';
        $column_info3 = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ';
        TestCheck::assertInArray('E.1', 'Structure::union(); insert columns in output starting with most common structures',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = '
        [
            {"name":"field2", "type":"text"}
        ]
        ';
        $column_info2 = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"},
        ]
        ';
        $column_info3 = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ';
        TestCheck::assertInArray('E.2', 'Structure::union(); insert columns in output starting with most common structures',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = '
        [
            {"name":"field1", "type":"text"}
        ]
        ';
        $column_info2 = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"},
        ]
        ';
        $column_info3 = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ';
        TestCheck::assertInArray('E.3', 'Structure::union(); insert columns in output starting with most common structures',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = '
        [
            {"name":"field3", "type":"text"},
            {"name":"field2", "type":"text"}
        ]
        ';
        $column_info2 = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"},
        ]
        ';
        $column_info3 = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ';
        TestCheck::assertInArray('E.4', 'Structure::union(); insert columns in output starting with most common structures',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = '
        [
            {"name":"field3", "type":"text"},
            {"name":"field1", "type":"text"}
        ]
        ';
        $column_info2 = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"},
        ]
        ';
        $column_info3 = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ';
        TestCheck::assertInArray('E.5', 'Structure::union(); insert columns in output starting with most common structures',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = '
        [
            {"name":"field2", "type":"text"},
            {"name":"field1", "type":"text"}
        ]
        ';
        $column_info2 = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"},
        ]
        ';
        $column_info3 = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"}
        ]
        ';
        TestCheck::assertInArray('E.6', 'Structure::union(); insert columns in output starting with most common structures',  $actual, $expected, $results);


        // BEGIN TEST
        $column_info1 = '
        [
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"},
            {"name":"field4", "type":"text"},
            {"name":"field5", "type":"text"}
        ]
        ';
        $column_info2 = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field3", "type":"text"},
            {"name":"field4", "type":"text"}
        ]
        ';
        $column_info3 = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field3", "type":"text"},
            {"name":"field4", "type":"text"}
        ]
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
        $expected = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"},
            {"name":"field4", "type":"text"},
            {"name":"field5", "type":"text"}
        ]
        ';
        TestCheck::assertInArray('E.7', 'Structure::union(); insert columns in output starting with most common structures',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = '
        [
            {"name":"field0", "type":"text"},
            {"name":"field1", "type":"text"},
            {"name":"field2", "type":"text"},
            {"name":"field3", "type":"text"},
            {"name":"field4", "type":"text"},
            {"name":"field5", "type":"text"}
        ]
        ';
        $column_info2 = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field3", "type":"text"},
            {"name":"field4", "type":"text"}
        ]
        ';
        $column_info3 = '
        [
            {"name":"field1", "type":"text"},
            {"name":"field3", "type":"text"},
            {"name":"field4", "type":"text"}
        ]
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2, $column_info3))->get();
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
        TestCheck::assertInArray('E.8', 'Structure::union(); insert columns in output starting with most common structures',  $actual, $expected, $results);



        // TEST: Structure::union(); compatible type conversion

        // BEGIN TEST
        $column_info1 = '
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
        ';
        $column_info2 = '
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
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2))->get();
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
        TestCheck::assertInArray('F.1', 'Structure::union(); use compatible type when fields have the same name and different types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = '
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
        ';
        $column_info2 = '
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
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2))->get();
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
        TestCheck::assertInArray('F.2', 'Structure::union(); use compatible type when fields have the same name and different types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = '
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
        ';
        $column_info2 = '
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
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2))->get();
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
        TestCheck::assertInArray('F.3', 'Structure::union(); use compatible type when fields have the same name and different types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = '
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
        ';
        $column_info2 = '
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
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2))->get();
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
        TestCheck::assertInArray('F.4', 'Structure::union(); use compatible type when fields have the same name and different types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = '
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
        ';
        $column_info2 = '
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
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2))->get();
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
        TestCheck::assertInArray('F.5', 'Structure::union(); use compatible type when fields have the same name and different types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = '
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
        ';
        $column_info2 = '
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
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2))->get();
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
        TestCheck::assertInArray('F.6', 'Structure::union(); use compatible type when fields have the same name and different types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = '
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
        ';
        $column_info2 = '
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
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2))->get();
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
        TestCheck::assertInArray('F.7', 'Structure::union(); use compatible type when fields have the same name and different types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = '
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
        ';
        $column_info2 = '
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
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2))->get();
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
        TestCheck::assertInArray('F.8', 'Structure::union(); use compatible type when fields have the same name and different types',  $actual, $expected, $results);


        // BEGIN TEST
        $column_info1 = '
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
        ';
        $column_info2 = '
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
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2))->get();
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
        TestCheck::assertInArray('F.9', 'Structure::union(); use compatible type when fields have the same name and different types',  $actual, $expected, $results);



        // TEST: Structure::union(); largest width conversion

        // BEGIN TEST
        $column_info1 = '
        [
            {"name":"field1", "type":"character", "width": 20},
            {"name":"field2", "type":"character", "width": 30}
        ]
        ';
        $column_info2 = '
        [
            {"name":"field1", "type":"character", "width": 10},
            {"name":"field2", "type":"character", "width": 40}
        ]
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2))->get();
        $expected = '
        [
            {"name":"field1", "type":"character", "width": 20},
            {"name":"field2", "type":"character", "width": 40}
        ]
        ';
        TestCheck::assertInArray('G.1', 'Structure::union(); use compatible type when fields have the same name and different types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = '
        [
            {"name":"field1", "type":"widecharacter", "width": 20},
            {"name":"field2", "type":"widecharacter", "width": 30}
        ]
        ';
        $column_info2 = '
        [
            {"name":"field1", "type":"widecharacter", "width": 10},
            {"name":"field2", "type":"widecharacter", "width": 40}
        ]
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2))->get();
        $expected = '
        [
            {"name":"field1", "type":"widecharacter", "width": 20},
            {"name":"field2", "type":"widecharacter", "width": 40}
        ]
        ';
        TestCheck::assertInArray('G.2', 'Structure::union(); use compatible type when fields have the same name and different types',  $actual, $expected, $results);

        // BEGIN TEST
        $column_info1 = '
        [
            {"name":"field1", "type":"numeric", "width": 8},
            {"name":"field2", "type":"numeric", "width": 10}
        ]
        ';
        $column_info2 = '
        [
            {"name":"field1", "type":"numeric", "width": 4},
            {"name":"field2", "type":"numeric", "width": 12}
        ]
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2))->get();
        $expected = '
        [
            {"name":"field1", "type":"numeric", "width": 8},
            {"name":"field2", "type":"numeric", "width": 12}
        ]
        ';
        TestCheck::assertInArray('G.3', 'Structure::union(); use compatible type when fields have the same name and different types',  $actual, $expected, $results);



        // TEST: Structure::union(); largest scale conversion

        // BEGIN TEST
        $column_info1 = '
        [
            {"name":"field1", "type":"numeric", "width": 10, "scale": 2},
            {"name":"field2", "type":"numeric", "width": 10, "scale": 4}
        ]
        ';
        $column_info2 = '
        [
            {"name":"field1", "type":"numeric", "width": 10, "scale": 6},
            {"name":"field2", "type":"numeric", "width": 10, "scale": 3}
        ]
        ';
        $actual = \Flexio\Object\Structure::union(array($column_info1, $column_info2))->get();
        $expected = '
        [
            {"name":"field1", "type":"numeric", "width": 10, "scale": 6},
            {"name":"field2", "type":"numeric", "width": 10, "scale": 4}
        ]
        ';
        TestCheck::assertInArray('H.1', 'Structure::union(); use compatible type when fields have the same name and different types',  $actual, $expected, $results);
    }
}
