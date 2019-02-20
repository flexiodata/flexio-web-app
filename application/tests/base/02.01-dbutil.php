<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-04-16
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
        // TEST: DbUtil constant tests

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::TYPE_TEXT;
        $expected = 'text';
        \Flexio\Tests\Check::assertString('A.1', '\DbUtil field type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        $expected = 'character';
        \Flexio\Tests\Check::assertString('A.2', '\DbUtil field type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::TYPE_WIDECHARACTER;
        $expected = 'widecharacter';
        \Flexio\Tests\Check::assertString('A.3', '\DbUtil field type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::TYPE_NUMERIC;
        $expected = 'numeric';
        \Flexio\Tests\Check::assertString('A.4', '\DbUtil field type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::TYPE_DOUBLE;
        $expected = 'double';
        \Flexio\Tests\Check::assertString('A.5', '\DbUtil field type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::TYPE_INTEGER;
        $expected = 'integer';
        \Flexio\Tests\Check::assertString('A.6', '\DbUtil field type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::TYPE_DATE;
        $expected = 'date';
        \Flexio\Tests\Check::assertString('A.7', '\DbUtil field type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::TYPE_DATETIME;
        $expected = 'datetime';
        \Flexio\Tests\Check::assertString('A.8', '\DbUtil field type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::TYPE_BOOLEAN;
        $expected = 'boolean';
        \Flexio\Tests\Check::assertString('A.9', '\DbUtil field type constant',  $actual, $expected, $results);



        // TEST: \Flexio\Base\DbUtil::getCompatibleType() tests; default type

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_CHARACTER, false);
        $expected = \Flexio\Base\DbUtil::TYPE_TEXT;
        \Flexio\Tests\Check::assertString('B.1', '\Flexio\Base\DbUtil::getCompatibleType() default test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(false, \Flexio\Base\DbUtil::TYPE_CHARACTER);
        $expected = \Flexio\Base\DbUtil::TYPE_TEXT;
        \Flexio\Tests\Check::assertString('B.2', '\Flexio\Base\DbUtil::getCompatibleType() default test',  $actual, $expected, $results);



        // TEST: \Flexio\Base\DbUtil::getCompatibleType() tests; text type vs other types

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_TEXT, \Flexio\Base\DbUtil::TYPE_TEXT);
        $expected = \Flexio\Base\DbUtil::TYPE_TEXT;
        \Flexio\Tests\Check::assertString('C.1', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_TEXT, \Flexio\Base\DbUtil::TYPE_CHARACTER);
        $expected = \Flexio\Base\DbUtil::TYPE_TEXT;
        \Flexio\Tests\Check::assertString('C.2', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_TEXT, \Flexio\Base\DbUtil::TYPE_WIDECHARACTER);
        $expected = \Flexio\Base\DbUtil::TYPE_TEXT;
        \Flexio\Tests\Check::assertString('C.3', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_TEXT, \Flexio\Base\DbUtil::TYPE_NUMERIC);
        $expected = \Flexio\Base\DbUtil::TYPE_TEXT;
        \Flexio\Tests\Check::assertString('C.4', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_TEXT, \Flexio\Base\DbUtil::TYPE_DOUBLE);
        $expected = \Flexio\Base\DbUtil::TYPE_TEXT;
        \Flexio\Tests\Check::assertString('C.5', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_TEXT, \Flexio\Base\DbUtil::TYPE_INTEGER);
        $expected = \Flexio\Base\DbUtil::TYPE_TEXT;
        \Flexio\Tests\Check::assertString('C.6', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_TEXT, \Flexio\Base\DbUtil::TYPE_DATE);
        $expected = \Flexio\Base\DbUtil::TYPE_TEXT;
        \Flexio\Tests\Check::assertString('C.7', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_TEXT, \Flexio\Base\DbUtil::TYPE_DATETIME);
        $expected = \Flexio\Base\DbUtil::TYPE_TEXT;
        \Flexio\Tests\Check::assertString('C.8', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_TEXT, \Flexio\Base\DbUtil::TYPE_BOOLEAN);
        $expected = \Flexio\Base\DbUtil::TYPE_TEXT;
        \Flexio\Tests\Check::assertString('C.9', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);



        // TEST: \Flexio\Base\DbUtil::getCompatibleType() tests; character type vs other types

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_CHARACTER, \Flexio\Base\DbUtil::TYPE_TEXT);
        $expected = \Flexio\Base\DbUtil::TYPE_TEXT;
        \Flexio\Tests\Check::assertString('D.1', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_CHARACTER, \Flexio\Base\DbUtil::TYPE_CHARACTER);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('D.2', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_CHARACTER, \Flexio\Base\DbUtil::TYPE_WIDECHARACTER);
        $expected = \Flexio\Base\DbUtil::TYPE_WIDECHARACTER;
        \Flexio\Tests\Check::assertString('D.3', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_CHARACTER, \Flexio\Base\DbUtil::TYPE_NUMERIC);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('D.4', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_CHARACTER, \Flexio\Base\DbUtil::TYPE_DOUBLE);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('D.5', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_CHARACTER, \Flexio\Base\DbUtil::TYPE_INTEGER);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('D.6', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_CHARACTER, \Flexio\Base\DbUtil::TYPE_DATE);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('D.7', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_CHARACTER, \Flexio\Base\DbUtil::TYPE_DATETIME);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('D.8', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_CHARACTER, \Flexio\Base\DbUtil::TYPE_BOOLEAN);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('D.9', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);



        // TEST: \Flexio\Base\DbUtil::getCompatibleType() tests; widcharacter type vs other types

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_WIDECHARACTER, \Flexio\Base\DbUtil::TYPE_TEXT);
        $expected = \Flexio\Base\DbUtil::TYPE_TEXT;
        \Flexio\Tests\Check::assertString('E.1', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_WIDECHARACTER, \Flexio\Base\DbUtil::TYPE_CHARACTER);
        $expected = \Flexio\Base\DbUtil::TYPE_WIDECHARACTER;
        \Flexio\Tests\Check::assertString('E.2', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_WIDECHARACTER, \Flexio\Base\DbUtil::TYPE_WIDECHARACTER);
        $expected = \Flexio\Base\DbUtil::TYPE_WIDECHARACTER;
        \Flexio\Tests\Check::assertString('E.3', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_WIDECHARACTER, \Flexio\Base\DbUtil::TYPE_NUMERIC);
        $expected = \Flexio\Base\DbUtil::TYPE_WIDECHARACTER;
        \Flexio\Tests\Check::assertString('E.4', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_WIDECHARACTER, \Flexio\Base\DbUtil::TYPE_DOUBLE);
        $expected = \Flexio\Base\DbUtil::TYPE_WIDECHARACTER;
        \Flexio\Tests\Check::assertString('E.5', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_WIDECHARACTER, \Flexio\Base\DbUtil::TYPE_INTEGER);
        $expected = \Flexio\Base\DbUtil::TYPE_WIDECHARACTER;
        \Flexio\Tests\Check::assertString('E.6', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_WIDECHARACTER, \Flexio\Base\DbUtil::TYPE_DATE);
        $expected = \Flexio\Base\DbUtil::TYPE_WIDECHARACTER;
        \Flexio\Tests\Check::assertString('E.7', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_WIDECHARACTER, \Flexio\Base\DbUtil::TYPE_DATETIME);
        $expected = \Flexio\Base\DbUtil::TYPE_WIDECHARACTER;
        \Flexio\Tests\Check::assertString('E.8', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_WIDECHARACTER, \Flexio\Base\DbUtil::TYPE_BOOLEAN);
        $expected = \Flexio\Base\DbUtil::TYPE_WIDECHARACTER;
        \Flexio\Tests\Check::assertString('E.9', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);



        // TEST: \Flexio\Base\DbUtil::getCompatibleType() tests; numeric type vs other types

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_NUMERIC, \Flexio\Base\DbUtil::TYPE_TEXT);
        $expected = \Flexio\Base\DbUtil::TYPE_TEXT;
        \Flexio\Tests\Check::assertString('F.1', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_NUMERIC, \Flexio\Base\DbUtil::TYPE_CHARACTER);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('F.2', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_NUMERIC, \Flexio\Base\DbUtil::TYPE_WIDECHARACTER);
        $expected = \Flexio\Base\DbUtil::TYPE_WIDECHARACTER;
        \Flexio\Tests\Check::assertString('F.3', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_NUMERIC, \Flexio\Base\DbUtil::TYPE_NUMERIC);
        $expected = \Flexio\Base\DbUtil::TYPE_NUMERIC;
        \Flexio\Tests\Check::assertString('F.4', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_NUMERIC, \Flexio\Base\DbUtil::TYPE_DOUBLE);
        $expected = \Flexio\Base\DbUtil::TYPE_NUMERIC;
        \Flexio\Tests\Check::assertString('F.5', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_NUMERIC, \Flexio\Base\DbUtil::TYPE_INTEGER);
        $expected = \Flexio\Base\DbUtil::TYPE_NUMERIC;
        \Flexio\Tests\Check::assertString('F.6', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_NUMERIC, \Flexio\Base\DbUtil::TYPE_DATE);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('F.7', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_NUMERIC, \Flexio\Base\DbUtil::TYPE_DATETIME);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('F.8', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_NUMERIC, \Flexio\Base\DbUtil::TYPE_BOOLEAN);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('F.9', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);



        // TEST: \Flexio\Base\DbUtil::getCompatibleType() tests; double type vs other types

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DOUBLE, \Flexio\Base\DbUtil::TYPE_TEXT);
        $expected = \Flexio\Base\DbUtil::TYPE_TEXT;
        \Flexio\Tests\Check::assertString('G.1', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DOUBLE, \Flexio\Base\DbUtil::TYPE_CHARACTER);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('G.2', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DOUBLE, \Flexio\Base\DbUtil::TYPE_WIDECHARACTER);
        $expected = \Flexio\Base\DbUtil::TYPE_WIDECHARACTER;
        \Flexio\Tests\Check::assertString('G.3', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DOUBLE, \Flexio\Base\DbUtil::TYPE_NUMERIC);
        $expected = \Flexio\Base\DbUtil::TYPE_NUMERIC;
        \Flexio\Tests\Check::assertString('G.4', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DOUBLE, \Flexio\Base\DbUtil::TYPE_DOUBLE);
        $expected = \Flexio\Base\DbUtil::TYPE_DOUBLE;
        \Flexio\Tests\Check::assertString('G.5', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DOUBLE, \Flexio\Base\DbUtil::TYPE_INTEGER);
        $expected = \Flexio\Base\DbUtil::TYPE_DOUBLE;
        \Flexio\Tests\Check::assertString('G.6', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DOUBLE, \Flexio\Base\DbUtil::TYPE_DATE);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('G.7', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DOUBLE, \Flexio\Base\DbUtil::TYPE_DATETIME);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('G.8', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DOUBLE, \Flexio\Base\DbUtil::TYPE_BOOLEAN);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('G.9', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);



        // TEST: \Flexio\Base\DbUtil::getCompatibleType() tests; integer type vs other types

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_INTEGER, \Flexio\Base\DbUtil::TYPE_TEXT);
        $expected = \Flexio\Base\DbUtil::TYPE_TEXT;
        \Flexio\Tests\Check::assertString('H.1', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_INTEGER, \Flexio\Base\DbUtil::TYPE_CHARACTER);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('H.2', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_INTEGER, \Flexio\Base\DbUtil::TYPE_WIDECHARACTER);
        $expected = \Flexio\Base\DbUtil::TYPE_WIDECHARACTER;
        \Flexio\Tests\Check::assertString('H.3', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_INTEGER, \Flexio\Base\DbUtil::TYPE_NUMERIC);
        $expected = \Flexio\Base\DbUtil::TYPE_NUMERIC;
        \Flexio\Tests\Check::assertString('H.4', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_INTEGER, \Flexio\Base\DbUtil::TYPE_DOUBLE);
        $expected = \Flexio\Base\DbUtil::TYPE_DOUBLE;
        \Flexio\Tests\Check::assertString('H.5', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_INTEGER, \Flexio\Base\DbUtil::TYPE_INTEGER);
        $expected = \Flexio\Base\DbUtil::TYPE_INTEGER;
        \Flexio\Tests\Check::assertString('H.6', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_INTEGER, \Flexio\Base\DbUtil::TYPE_DATE);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('H.7', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_INTEGER, \Flexio\Base\DbUtil::TYPE_DATETIME);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('H.8', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_INTEGER, \Flexio\Base\DbUtil::TYPE_BOOLEAN);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('H.9', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);



        // TEST: \Flexio\Base\DbUtil::getCompatibleType() tests; date type vs other types

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DATE, \Flexio\Base\DbUtil::TYPE_TEXT);
        $expected = \Flexio\Base\DbUtil::TYPE_TEXT;
        \Flexio\Tests\Check::assertString('I.1', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DATE, \Flexio\Base\DbUtil::TYPE_CHARACTER);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('I.2', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DATE, \Flexio\Base\DbUtil::TYPE_WIDECHARACTER);
        $expected = \Flexio\Base\DbUtil::TYPE_WIDECHARACTER;
        \Flexio\Tests\Check::assertString('I.3', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DATE, \Flexio\Base\DbUtil::TYPE_NUMERIC);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('I.4', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DATE, \Flexio\Base\DbUtil::TYPE_DOUBLE);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('I.5', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DATE, \Flexio\Base\DbUtil::TYPE_INTEGER);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('I.6', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DATE, \Flexio\Base\DbUtil::TYPE_DATE);
        $expected = \Flexio\Base\DbUtil::TYPE_DATE;
        \Flexio\Tests\Check::assertString('I.7', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DATE, \Flexio\Base\DbUtil::TYPE_DATETIME);
        $expected = \Flexio\Base\DbUtil::TYPE_DATETIME;
        \Flexio\Tests\Check::assertString('I.8', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DATE, \Flexio\Base\DbUtil::TYPE_BOOLEAN);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('I.9', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);



        // TEST: \Flexio\Base\DbUtil::getCompatibleType() tests; datetime type vs other types

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DATETIME, \Flexio\Base\DbUtil::TYPE_TEXT);
        $expected = \Flexio\Base\DbUtil::TYPE_TEXT;
        \Flexio\Tests\Check::assertString('J.1', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DATETIME, \Flexio\Base\DbUtil::TYPE_CHARACTER);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('J.2', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DATETIME, \Flexio\Base\DbUtil::TYPE_WIDECHARACTER);
        $expected = \Flexio\Base\DbUtil::TYPE_WIDECHARACTER;
        \Flexio\Tests\Check::assertString('J.3', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DATETIME, \Flexio\Base\DbUtil::TYPE_NUMERIC);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('J.4', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DATETIME, \Flexio\Base\DbUtil::TYPE_DOUBLE);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('J.5', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DATETIME, \Flexio\Base\DbUtil::TYPE_INTEGER);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('J.6', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DATETIME, \Flexio\Base\DbUtil::TYPE_DATE);
        $expected = \Flexio\Base\DbUtil::TYPE_DATETIME;
        \Flexio\Tests\Check::assertString('J.7', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DATETIME, \Flexio\Base\DbUtil::TYPE_DATETIME);
        $expected = \Flexio\Base\DbUtil::TYPE_DATETIME;
        \Flexio\Tests\Check::assertString('J.8', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_DATETIME, \Flexio\Base\DbUtil::TYPE_BOOLEAN);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('J.9', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);



        // TEST: \Flexio\Base\DbUtil::getCompatibleType() tests; boolean type vs other types

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_BOOLEAN, \Flexio\Base\DbUtil::TYPE_TEXT);
        $expected = \Flexio\Base\DbUtil::TYPE_TEXT;
        \Flexio\Tests\Check::assertString('K.1', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_BOOLEAN, \Flexio\Base\DbUtil::TYPE_CHARACTER);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('K.2', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_BOOLEAN, \Flexio\Base\DbUtil::TYPE_WIDECHARACTER);
        $expected = \Flexio\Base\DbUtil::TYPE_WIDECHARACTER;
        \Flexio\Tests\Check::assertString('K.3', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_BOOLEAN, \Flexio\Base\DbUtil::TYPE_NUMERIC);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('K.4', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_BOOLEAN, \Flexio\Base\DbUtil::TYPE_DOUBLE);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('K.5', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_BOOLEAN, \Flexio\Base\DbUtil::TYPE_INTEGER);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('K.6', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_BOOLEAN, \Flexio\Base\DbUtil::TYPE_DATE);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('K.7', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_BOOLEAN, \Flexio\Base\DbUtil::TYPE_DATETIME);
        $expected = \Flexio\Base\DbUtil::TYPE_CHARACTER;
        \Flexio\Tests\Check::assertString('K.8', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\DbUtil::getCompatibleType(\Flexio\Base\DbUtil::TYPE_BOOLEAN, \Flexio\Base\DbUtil::TYPE_BOOLEAN);
        $expected = \Flexio\Base\DbUtil::TYPE_BOOLEAN;
        \Flexio\Tests\Check::assertString('K.9', '\Flexio\Base\DbUtil::getCompatibleType() test',  $actual, $expected, $results);
    }
}
