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


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: DbUtil constant tests

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::TYPE_TEXT;
        $expected = 'text';
        TestCheck::assertString('A.1', '\DbUtil field type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::TYPE_CHARACTER;
        $expected = 'character';
        TestCheck::assertString('A.2', '\DbUtil field type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::TYPE_WIDECHARACTER;
        $expected = 'widecharacter';
        TestCheck::assertString('A.3', '\DbUtil field type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::TYPE_NUMERIC;
        $expected = 'numeric';
        TestCheck::assertString('A.4', '\DbUtil field type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::TYPE_DOUBLE;
        $expected = 'double';
        TestCheck::assertString('A.5', '\DbUtil field type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::TYPE_INTEGER;
        $expected = 'integer';
        TestCheck::assertString('A.6', '\DbUtil field type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::TYPE_DATE;
        $expected = 'date';
        TestCheck::assertString('A.7', '\DbUtil field type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::TYPE_DATETIME;
        $expected = 'datetime';
        TestCheck::assertString('A.8', '\DbUtil field type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::TYPE_BOOLEAN;
        $expected = 'boolean';
        TestCheck::assertString('A.9', '\DbUtil field type constant',  $actual, $expected, $results);



        // TEST: \Flexio\System\DbUtil::getCompatibleType() tests; default type

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_CHARACTER, false);
        $expected = \Flexio\System\DbUtil::TYPE_TEXT;
        TestCheck::assertString('B.1', '\Flexio\System\DbUtil::getCompatibleType() default test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(false, \Flexio\System\DbUtil::TYPE_CHARACTER);
        $expected = \Flexio\System\DbUtil::TYPE_TEXT;
        TestCheck::assertString('B.2', '\Flexio\System\DbUtil::getCompatibleType() default test',  $actual, $expected, $results);



        // TEST: \Flexio\System\DbUtil::getCompatibleType() tests; text type vs other types

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_TEXT, \Flexio\System\DbUtil::TYPE_TEXT);
        $expected = \Flexio\System\DbUtil::TYPE_TEXT;
        TestCheck::assertString('C.1', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_TEXT, \Flexio\System\DbUtil::TYPE_CHARACTER);
        $expected = \Flexio\System\DbUtil::TYPE_TEXT;
        TestCheck::assertString('C.2', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_TEXT, \Flexio\System\DbUtil::TYPE_WIDECHARACTER);
        $expected = \Flexio\System\DbUtil::TYPE_TEXT;
        TestCheck::assertString('C.3', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_TEXT, \Flexio\System\DbUtil::TYPE_NUMERIC);
        $expected = \Flexio\System\DbUtil::TYPE_TEXT;
        TestCheck::assertString('C.4', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_TEXT, \Flexio\System\DbUtil::TYPE_DOUBLE);
        $expected = \Flexio\System\DbUtil::TYPE_TEXT;
        TestCheck::assertString('C.5', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_TEXT, \Flexio\System\DbUtil::TYPE_INTEGER);
        $expected = \Flexio\System\DbUtil::TYPE_TEXT;
        TestCheck::assertString('C.6', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_TEXT, \Flexio\System\DbUtil::TYPE_DATE);
        $expected = \Flexio\System\DbUtil::TYPE_TEXT;
        TestCheck::assertString('C.7', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_TEXT, \Flexio\System\DbUtil::TYPE_DATETIME);
        $expected = \Flexio\System\DbUtil::TYPE_TEXT;
        TestCheck::assertString('C.8', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_TEXT, \Flexio\System\DbUtil::TYPE_BOOLEAN);
        $expected = \Flexio\System\DbUtil::TYPE_TEXT;
        TestCheck::assertString('C.9', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);



        // TEST: \Flexio\System\DbUtil::getCompatibleType() tests; character type vs other types

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_CHARACTER, \Flexio\System\DbUtil::TYPE_TEXT);
        $expected = \Flexio\System\DbUtil::TYPE_TEXT;
        TestCheck::assertString('D.1', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_CHARACTER, \Flexio\System\DbUtil::TYPE_CHARACTER);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('D.2', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_CHARACTER, \Flexio\System\DbUtil::TYPE_WIDECHARACTER);
        $expected = \Flexio\System\DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('D.3', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_CHARACTER, \Flexio\System\DbUtil::TYPE_NUMERIC);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('D.4', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_CHARACTER, \Flexio\System\DbUtil::TYPE_DOUBLE);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('D.5', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_CHARACTER, \Flexio\System\DbUtil::TYPE_INTEGER);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('D.6', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_CHARACTER, \Flexio\System\DbUtil::TYPE_DATE);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('D.7', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_CHARACTER, \Flexio\System\DbUtil::TYPE_DATETIME);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('D.8', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_CHARACTER, \Flexio\System\DbUtil::TYPE_BOOLEAN);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('D.9', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);



        // TEST: \Flexio\System\DbUtil::getCompatibleType() tests; widcharacter type vs other types

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_WIDECHARACTER, \Flexio\System\DbUtil::TYPE_TEXT);
        $expected = \Flexio\System\DbUtil::TYPE_TEXT;
        TestCheck::assertString('E.1', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_WIDECHARACTER, \Flexio\System\DbUtil::TYPE_CHARACTER);
        $expected = \Flexio\System\DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('E.2', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_WIDECHARACTER, \Flexio\System\DbUtil::TYPE_WIDECHARACTER);
        $expected = \Flexio\System\DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('E.3', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_WIDECHARACTER, \Flexio\System\DbUtil::TYPE_NUMERIC);
        $expected = \Flexio\System\DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('E.4', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_WIDECHARACTER, \Flexio\System\DbUtil::TYPE_DOUBLE);
        $expected = \Flexio\System\DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('E.5', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_WIDECHARACTER, \Flexio\System\DbUtil::TYPE_INTEGER);
        $expected = \Flexio\System\DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('E.6', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_WIDECHARACTER, \Flexio\System\DbUtil::TYPE_DATE);
        $expected = \Flexio\System\DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('E.7', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_WIDECHARACTER, \Flexio\System\DbUtil::TYPE_DATETIME);
        $expected = \Flexio\System\DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('E.8', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_WIDECHARACTER, \Flexio\System\DbUtil::TYPE_BOOLEAN);
        $expected = \Flexio\System\DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('E.9', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);



        // TEST: \Flexio\System\DbUtil::getCompatibleType() tests; numeric type vs other types

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_NUMERIC, \Flexio\System\DbUtil::TYPE_TEXT);
        $expected = \Flexio\System\DbUtil::TYPE_TEXT;
        TestCheck::assertString('F.1', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_NUMERIC, \Flexio\System\DbUtil::TYPE_CHARACTER);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('F.2', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_NUMERIC, \Flexio\System\DbUtil::TYPE_WIDECHARACTER);
        $expected = \Flexio\System\DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('F.3', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_NUMERIC, \Flexio\System\DbUtil::TYPE_NUMERIC);
        $expected = \Flexio\System\DbUtil::TYPE_NUMERIC;
        TestCheck::assertString('F.4', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_NUMERIC, \Flexio\System\DbUtil::TYPE_DOUBLE);
        $expected = \Flexio\System\DbUtil::TYPE_NUMERIC;
        TestCheck::assertString('F.5', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_NUMERIC, \Flexio\System\DbUtil::TYPE_INTEGER);
        $expected = \Flexio\System\DbUtil::TYPE_NUMERIC;
        TestCheck::assertString('F.6', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_NUMERIC, \Flexio\System\DbUtil::TYPE_DATE);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('F.7', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_NUMERIC, \Flexio\System\DbUtil::TYPE_DATETIME);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('F.8', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_NUMERIC, \Flexio\System\DbUtil::TYPE_BOOLEAN);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('F.9', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);



        // TEST: \Flexio\System\DbUtil::getCompatibleType() tests; double type vs other types

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DOUBLE, \Flexio\System\DbUtil::TYPE_TEXT);
        $expected = \Flexio\System\DbUtil::TYPE_TEXT;
        TestCheck::assertString('G.1', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DOUBLE, \Flexio\System\DbUtil::TYPE_CHARACTER);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('G.2', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DOUBLE, \Flexio\System\DbUtil::TYPE_WIDECHARACTER);
        $expected = \Flexio\System\DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('G.3', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DOUBLE, \Flexio\System\DbUtil::TYPE_NUMERIC);
        $expected = \Flexio\System\DbUtil::TYPE_NUMERIC;
        TestCheck::assertString('G.4', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DOUBLE, \Flexio\System\DbUtil::TYPE_DOUBLE);
        $expected = \Flexio\System\DbUtil::TYPE_DOUBLE;
        TestCheck::assertString('G.5', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DOUBLE, \Flexio\System\DbUtil::TYPE_INTEGER);
        $expected = \Flexio\System\DbUtil::TYPE_DOUBLE;
        TestCheck::assertString('G.6', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DOUBLE, \Flexio\System\DbUtil::TYPE_DATE);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('G.7', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DOUBLE, \Flexio\System\DbUtil::TYPE_DATETIME);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('G.8', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DOUBLE, \Flexio\System\DbUtil::TYPE_BOOLEAN);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('G.9', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);



        // TEST: \Flexio\System\DbUtil::getCompatibleType() tests; integer type vs other types

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_INTEGER, \Flexio\System\DbUtil::TYPE_TEXT);
        $expected = \Flexio\System\DbUtil::TYPE_TEXT;
        TestCheck::assertString('H.1', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_INTEGER, \Flexio\System\DbUtil::TYPE_CHARACTER);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('H.2', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_INTEGER, \Flexio\System\DbUtil::TYPE_WIDECHARACTER);
        $expected = \Flexio\System\DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('H.3', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_INTEGER, \Flexio\System\DbUtil::TYPE_NUMERIC);
        $expected = \Flexio\System\DbUtil::TYPE_NUMERIC;
        TestCheck::assertString('H.4', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_INTEGER, \Flexio\System\DbUtil::TYPE_DOUBLE);
        $expected = \Flexio\System\DbUtil::TYPE_DOUBLE;
        TestCheck::assertString('H.5', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_INTEGER, \Flexio\System\DbUtil::TYPE_INTEGER);
        $expected = \Flexio\System\DbUtil::TYPE_INTEGER;
        TestCheck::assertString('H.6', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_INTEGER, \Flexio\System\DbUtil::TYPE_DATE);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('H.7', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_INTEGER, \Flexio\System\DbUtil::TYPE_DATETIME);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('H.8', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_INTEGER, \Flexio\System\DbUtil::TYPE_BOOLEAN);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('H.9', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);



        // TEST: \Flexio\System\DbUtil::getCompatibleType() tests; date type vs other types

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DATE, \Flexio\System\DbUtil::TYPE_TEXT);
        $expected = \Flexio\System\DbUtil::TYPE_TEXT;
        TestCheck::assertString('I.1', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DATE, \Flexio\System\DbUtil::TYPE_CHARACTER);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('I.2', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DATE, \Flexio\System\DbUtil::TYPE_WIDECHARACTER);
        $expected = \Flexio\System\DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('I.3', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DATE, \Flexio\System\DbUtil::TYPE_NUMERIC);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('I.4', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DATE, \Flexio\System\DbUtil::TYPE_DOUBLE);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('I.5', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DATE, \Flexio\System\DbUtil::TYPE_INTEGER);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('I.6', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DATE, \Flexio\System\DbUtil::TYPE_DATE);
        $expected = \Flexio\System\DbUtil::TYPE_DATE;
        TestCheck::assertString('I.7', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DATE, \Flexio\System\DbUtil::TYPE_DATETIME);
        $expected = \Flexio\System\DbUtil::TYPE_DATETIME;
        TestCheck::assertString('I.8', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DATE, \Flexio\System\DbUtil::TYPE_BOOLEAN);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('I.9', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);



        // TEST: \Flexio\System\DbUtil::getCompatibleType() tests; datetime type vs other types

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DATETIME, \Flexio\System\DbUtil::TYPE_TEXT);
        $expected = \Flexio\System\DbUtil::TYPE_TEXT;
        TestCheck::assertString('J.1', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DATETIME, \Flexio\System\DbUtil::TYPE_CHARACTER);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('J.2', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DATETIME, \Flexio\System\DbUtil::TYPE_WIDECHARACTER);
        $expected = \Flexio\System\DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('J.3', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DATETIME, \Flexio\System\DbUtil::TYPE_NUMERIC);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('J.4', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DATETIME, \Flexio\System\DbUtil::TYPE_DOUBLE);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('J.5', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DATETIME, \Flexio\System\DbUtil::TYPE_INTEGER);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('J.6', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DATETIME, \Flexio\System\DbUtil::TYPE_DATE);
        $expected = \Flexio\System\DbUtil::TYPE_DATETIME;
        TestCheck::assertString('J.7', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DATETIME, \Flexio\System\DbUtil::TYPE_DATETIME);
        $expected = \Flexio\System\DbUtil::TYPE_DATETIME;
        TestCheck::assertString('J.8', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_DATETIME, \Flexio\System\DbUtil::TYPE_BOOLEAN);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('J.9', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);



        // TEST: \Flexio\System\DbUtil::getCompatibleType() tests; boolean type vs other types

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_BOOLEAN, \Flexio\System\DbUtil::TYPE_TEXT);
        $expected = \Flexio\System\DbUtil::TYPE_TEXT;
        TestCheck::assertString('K.1', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_BOOLEAN, \Flexio\System\DbUtil::TYPE_CHARACTER);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('K.2', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_BOOLEAN, \Flexio\System\DbUtil::TYPE_WIDECHARACTER);
        $expected = \Flexio\System\DbUtil::TYPE_WIDECHARACTER;
        TestCheck::assertString('K.3', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_BOOLEAN, \Flexio\System\DbUtil::TYPE_NUMERIC);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('K.4', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_BOOLEAN, \Flexio\System\DbUtil::TYPE_DOUBLE);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('K.5', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_BOOLEAN, \Flexio\System\DbUtil::TYPE_INTEGER);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('K.6', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_BOOLEAN, \Flexio\System\DbUtil::TYPE_DATE);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('K.7', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_BOOLEAN, \Flexio\System\DbUtil::TYPE_DATETIME);
        $expected = \Flexio\System\DbUtil::TYPE_CHARACTER;
        TestCheck::assertString('K.8', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\DbUtil::getCompatibleType(\Flexio\System\DbUtil::TYPE_BOOLEAN, \Flexio\System\DbUtil::TYPE_BOOLEAN);
        $expected = \Flexio\System\DbUtil::TYPE_BOOLEAN;
        TestCheck::assertString('K.9', '\Flexio\System\DbUtil::getCompatibleType() test',  $actual, $expected, $results);
    }
}
