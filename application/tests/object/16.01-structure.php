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


class Test
{
    public function run(&$results)
    {
        // BEGIN TEST
        $actual = \Flexio\Object\Structure::TYPE_INVALID;
        $expected = '';
        TestCheck::assertString('A.1', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::TYPE_TEXT;
        $expected = 'text';
        TestCheck::assertString('A.2', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::TYPE_CHARACTER;
        $expected = 'character';
        TestCheck::assertString('A.3', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::TYPE_WIDECHARACTER;
        $expected = 'widecharacter';
        TestCheck::assertString('A.4', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::TYPE_NUMERIC;
        $expected = 'numeric';
        TestCheck::assertString('A.5', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::TYPE_DOUBLE;
        $expected = 'double';
        TestCheck::assertString('A.6', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::TYPE_INTEGER;
        $expected = 'integer';
        TestCheck::assertString('A.7', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::TYPE_DATE;
        $expected = 'date';
        TestCheck::assertString('A.8', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::TYPE_DATETIME;
        $expected = 'datetime';
        TestCheck::assertString('A.9', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::TYPE_BOOLEAN;
        $expected = 'boolean';
        TestCheck::assertString('A.10', 'Structure; verify class constants',  $actual, $expected, $results);



        // TEST: Structure wildcard constants

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::WILDCARD_INVALID;
        $expected = '';
        TestCheck::assertString('B.1', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::WILDCARD_ALL;
        $expected = '*';
        TestCheck::assertString('B.2', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::WILDCARD_TEXT;
        $expected = ':text';
        TestCheck::assertString('B.3', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::WILDCARD_NUMBER;
        $expected = ':number';
        TestCheck::assertString('B.4', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::WILDCARD_DATE;
        $expected = ':date';
        TestCheck::assertString('B.5', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::WILDCARD_DATETIME;
        $expected = ':datetime';
        TestCheck::assertString('B.6', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Structure::WILDCARD_BOOLEAN;
        $expected = ':boolean';
        TestCheck::assertString('B.7', 'Structure; verify class constants',  $actual, $expected, $results);
    }
}
