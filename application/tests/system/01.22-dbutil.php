<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-04-16
 *
 * @package flexio
 * @subpackage Tests
 */


class Test
{
    public function run(&$results)
    {
        // TEST: DbUtil constant tests

        // BEGIN TEST
        $actual = \DbUtil::TYPE_TEXT;
        $expected = 'text';
        TestCheck::assertString('A.1', 'DbUtil field type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::TYPE_CHARACTER;
        $expected = 'character';
        TestCheck::assertString('A.2', 'DbUtil field type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::TYPE_WIDECHARACTER;
        $expected = 'widecharacter';
        TestCheck::assertString('A.3', 'DbUtil field type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::TYPE_NUMERIC;
        $expected = 'numeric';
        TestCheck::assertString('A.4', 'DbUtil field type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::TYPE_DOUBLE;
        $expected = 'double';
        TestCheck::assertString('A.5', 'DbUtil field type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::TYPE_INTEGER;
        $expected = 'integer';
        TestCheck::assertString('A.6', 'DbUtil field type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::TYPE_DATE;
        $expected = 'date';
        TestCheck::assertString('A.7', 'DbUtil field type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::TYPE_DATETIME;
        $expected = 'datetime';
        TestCheck::assertString('A.8', 'DbUtil field type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::TYPE_BOOLEAN;
        $expected = 'boolean';
        TestCheck::assertString('A.9', 'DbUtil field type constant',  $actual, $expected, $results);



        // TEST: \DbUtil::getCompatibleType() tests; default type

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_CHARACTER, false);
        $expected = \DbUtil::TYPE_TEXT;
        TestCheck::assertString('B.1', '\DbUtil::getCompatibleType() default test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(false, \DbUtil::TYPE_CHARACTER);
        $expected = \DbUtil::TYPE_TEXT;
        TestCheck::assertString('B.2', '\DbUtil::getCompatibleType() default test',  $actual, $expected, $results);



        // TEST: \DbUtil::getCompatibleType() tests; text type vs other types

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_TEXT, \DbUtil::TYPE_TEXT);
        $expected = \DbUtil::TYPE_TEXT;
        TestCheck::assertString('C.1', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_TEXT, \DbUtil::TYPE_CHARACTER);
        $expected = \DbUtil::TYPE_TEXT;
        TestCheck::assertString('C.2', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_TEXT, \DbUtil::TYPE_WIDECHARACTER);
        $expected = \DbUtil::TYPE_TEXT;
        TestCheck::assertString('C.3', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_TEXT, \DbUtil::TYPE_NUMERIC);
        $expected = \DbUtil::TYPE_TEXT;
        TestCheck::assertString('C.4', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_TEXT, \DbUtil::TYPE_DOUBLE);
        $expected = \DbUtil::TYPE_TEXT;
        TestCheck::assertString('C.5', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_TEXT, \DbUtil::TYPE_INTEGER);
        $expected = \DbUtil::TYPE_TEXT;
        TestCheck::assertString('C.6', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_TEXT, \DbUtil::TYPE_DATE);
        $expected = \DbUtil::TYPE_TEXT;
        TestCheck::assertString('C.7', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_TEXT, \DbUtil::TYPE_DATETIME);
        $expected = \DbUtil::TYPE_TEXT;
        TestCheck::assertString('C.8', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_TEXT, \DbUtil::TYPE_BOOLEAN);
        $expected = \DbUtil::TYPE_TEXT;
        TestCheck::assertString('C.9', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);



        // TEST: \DbUtil::getCompatibleType() tests; character type vs other types

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_CHARACTER, \DbUtil::TYPE_TEXT);
        $expected = \DbUtil::TYPE_TEXT;
        TestCheck::assertString('D.1', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_CHARACTER, \DbUtil::TYPE_CHARACTER);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('D.2', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_CHARACTER, \DbUtil::TYPE_WIDECHARACTER);
        $expected = \DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('D.3', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_CHARACTER, \DbUtil::TYPE_NUMERIC);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('D.4', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_CHARACTER, \DbUtil::TYPE_DOUBLE);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('D.5', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_CHARACTER, \DbUtil::TYPE_INTEGER);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('D.6', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_CHARACTER, \DbUtil::TYPE_DATE);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('D.7', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_CHARACTER, \DbUtil::TYPE_DATETIME);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('D.8', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_CHARACTER, \DbUtil::TYPE_BOOLEAN);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('D.9', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);



        // TEST: \DbUtil::getCompatibleType() tests; widcharacter type vs other types

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_WIDECHARACTER, \DbUtil::TYPE_TEXT);
        $expected = \DbUtil::TYPE_TEXT;
        TestCheck::assertString('E.1', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_WIDECHARACTER, \DbUtil::TYPE_CHARACTER);
        $expected = \DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('E.2', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_WIDECHARACTER, \DbUtil::TYPE_WIDECHARACTER);
        $expected = \DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('E.3', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_WIDECHARACTER, \DbUtil::TYPE_NUMERIC);
        $expected = \DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('E.4', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_WIDECHARACTER, \DbUtil::TYPE_DOUBLE);
        $expected = \DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('E.5', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_WIDECHARACTER, \DbUtil::TYPE_INTEGER);
        $expected = \DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('E.6', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_WIDECHARACTER, \DbUtil::TYPE_DATE);
        $expected = \DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('E.7', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_WIDECHARACTER, \DbUtil::TYPE_DATETIME);
        $expected = \DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('E.8', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_WIDECHARACTER, \DbUtil::TYPE_BOOLEAN);
        $expected = \DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('E.9', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);



        // TEST: \DbUtil::getCompatibleType() tests; numeric type vs other types

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_NUMERIC, \DbUtil::TYPE_TEXT);
        $expected = \DbUtil::TYPE_TEXT;
        TestCheck::assertString('F.1', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_NUMERIC, \DbUtil::TYPE_CHARACTER);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('F.2', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_NUMERIC, \DbUtil::TYPE_WIDECHARACTER);
        $expected = \DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('F.3', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_NUMERIC, \DbUtil::TYPE_NUMERIC);
        $expected = \DbUtil::TYPE_NUMERIC;
        TestCheck::assertString('F.4', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_NUMERIC, \DbUtil::TYPE_DOUBLE);
        $expected = \DbUtil::TYPE_NUMERIC;
        TestCheck::assertString('F.5', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_NUMERIC, \DbUtil::TYPE_INTEGER);
        $expected = \DbUtil::TYPE_NUMERIC;
        TestCheck::assertString('F.6', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_NUMERIC, \DbUtil::TYPE_DATE);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('F.7', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_NUMERIC, \DbUtil::TYPE_DATETIME);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('F.8', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_NUMERIC, \DbUtil::TYPE_BOOLEAN);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('F.9', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);



        // TEST: \DbUtil::getCompatibleType() tests; double type vs other types

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DOUBLE, \DbUtil::TYPE_TEXT);
        $expected = \DbUtil::TYPE_TEXT;
        TestCheck::assertString('G.1', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DOUBLE, \DbUtil::TYPE_CHARACTER);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('G.2', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DOUBLE, \DbUtil::TYPE_WIDECHARACTER);
        $expected = \DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('G.3', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DOUBLE, \DbUtil::TYPE_NUMERIC);
        $expected = \DbUtil::TYPE_NUMERIC;
        TestCheck::assertString('G.4', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DOUBLE, \DbUtil::TYPE_DOUBLE);
        $expected = \DbUtil::TYPE_DOUBLE;
        TestCheck::assertString('G.5', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DOUBLE, \DbUtil::TYPE_INTEGER);
        $expected = \DbUtil::TYPE_DOUBLE;
        TestCheck::assertString('G.6', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DOUBLE, \DbUtil::TYPE_DATE);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('G.7', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DOUBLE, \DbUtil::TYPE_DATETIME);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('G.8', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DOUBLE, \DbUtil::TYPE_BOOLEAN);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('G.9', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);



        // TEST: \DbUtil::getCompatibleType() tests; integer type vs other types

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_INTEGER, \DbUtil::TYPE_TEXT);
        $expected = \DbUtil::TYPE_TEXT;
        TestCheck::assertString('H.1', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_INTEGER, \DbUtil::TYPE_CHARACTER);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('H.2', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_INTEGER, \DbUtil::TYPE_WIDECHARACTER);
        $expected = \DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('H.3', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_INTEGER, \DbUtil::TYPE_NUMERIC);
        $expected = \DbUtil::TYPE_NUMERIC;
        TestCheck::assertString('H.4', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_INTEGER, \DbUtil::TYPE_DOUBLE);
        $expected = \DbUtil::TYPE_DOUBLE;
        TestCheck::assertString('H.5', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_INTEGER, \DbUtil::TYPE_INTEGER);
        $expected = \DbUtil::TYPE_INTEGER;
        TestCheck::assertString('H.6', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_INTEGER, \DbUtil::TYPE_DATE);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('H.7', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_INTEGER, \DbUtil::TYPE_DATETIME);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('H.8', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_INTEGER, \DbUtil::TYPE_BOOLEAN);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('H.9', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);



        // TEST: \DbUtil::getCompatibleType() tests; date type vs other types

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DATE, \DbUtil::TYPE_TEXT);
        $expected = \DbUtil::TYPE_TEXT;
        TestCheck::assertString('I.1', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DATE, \DbUtil::TYPE_CHARACTER);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('I.2', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DATE, \DbUtil::TYPE_WIDECHARACTER);
        $expected = \DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('I.3', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DATE, \DbUtil::TYPE_NUMERIC);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('I.4', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DATE, \DbUtil::TYPE_DOUBLE);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('I.5', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DATE, \DbUtil::TYPE_INTEGER);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('I.6', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DATE, \DbUtil::TYPE_DATE);
        $expected = \DbUtil::TYPE_DATE;
        TestCheck::assertString('I.7', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DATE, \DbUtil::TYPE_DATETIME);
        $expected = \DbUtil::TYPE_DATETIME;
        TestCheck::assertString('I.8', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DATE, \DbUtil::TYPE_BOOLEAN);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('I.9', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);



        // TEST: \DbUtil::getCompatibleType() tests; datetime type vs other types

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DATETIME, \DbUtil::TYPE_TEXT);
        $expected = \DbUtil::TYPE_TEXT;
        TestCheck::assertString('J.1', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DATETIME, \DbUtil::TYPE_CHARACTER);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('J.2', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DATETIME, \DbUtil::TYPE_WIDECHARACTER);
        $expected = \DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('J.3', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DATETIME, \DbUtil::TYPE_NUMERIC);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('J.4', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DATETIME, \DbUtil::TYPE_DOUBLE);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('J.5', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DATETIME, \DbUtil::TYPE_INTEGER);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('J.6', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DATETIME, \DbUtil::TYPE_DATE);
        $expected = \DbUtil::TYPE_DATETIME;
        TestCheck::assertString('J.7', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DATETIME, \DbUtil::TYPE_DATETIME);
        $expected = \DbUtil::TYPE_DATETIME;
        TestCheck::assertString('J.8', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_DATETIME, \DbUtil::TYPE_BOOLEAN);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('J.9', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);



        // TEST: \DbUtil::getCompatibleType() tests; boolean type vs other types

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_BOOLEAN, \DbUtil::TYPE_TEXT);
        $expected = \DbUtil::TYPE_TEXT;
        TestCheck::assertString('K.1', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_BOOLEAN, \DbUtil::TYPE_CHARACTER);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('K.2', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_BOOLEAN, \DbUtil::TYPE_WIDECHARACTER);
        $expected = \DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('K.3', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_BOOLEAN, \DbUtil::TYPE_NUMERIC);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('K.4', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_BOOLEAN, \DbUtil::TYPE_DOUBLE);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('K.5', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_BOOLEAN, \DbUtil::TYPE_INTEGER);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('K.6', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_BOOLEAN, \DbUtil::TYPE_DATE);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('K.7', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_BOOLEAN, \DbUtil::TYPE_DATETIME);
        $expected = \DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('K.8', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \DbUtil::getCompatibleType(\DbUtil::TYPE_BOOLEAN, \DbUtil::TYPE_BOOLEAN);
        $expected = \DbUtil::TYPE_BOOLEAN;
        TestCheck::assertString('K.9', '\DbUtil::getCompatibleType() test',  $actual, $expected, $results);
    }
}
