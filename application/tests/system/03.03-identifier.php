<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-06-17
 *
 * @package flexio
 * @subpackage Tests
 */


class Test
{
    public function run(&$results)
    {
        // TEST: identifiers should start with a character

        // BEGIN TEST
        $actual = \Identifier::isValid('abc');
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Identifier::isValid(); identifiers should start with a character', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('1bc');
        $expected = false;
        TestCheck::assertBoolean('A.2', '\Identifier::isValid(); identifiers should start with a character', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('.bc');
        $expected = false;
        TestCheck::assertBoolean('A.3', '\Identifier::isValid(); identifiers should start with a character', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('/bc');
        $expected = false;
        TestCheck::assertBoolean('A.4', '\Identifier::isValid(); identifiers should start with a character', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('\bc');
        $expected = false;
        TestCheck::assertBoolean('A.5', '\Identifier::isValid(); identifiers should start with a character', $actual, $expected, $results);



        // TEST: chars after first char should be alphanmeric

        // BEGIN TEST
        $actual = \Identifier::isValid('abcdef123');
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Identifier::isValid(); non-alphanumeric characters', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('abc.def');
        $expected = false;
        TestCheck::assertBoolean('B.2', '\Identifier::isValid(); non-alphanumeric characters', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('abc/def');
        $expected = false;
        TestCheck::assertBoolean('B.3', '\Identifier::isValid(); non-alphanumeric characters', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('abc\def');
        $expected = false;
        TestCheck::assertBoolean('B.4', '\Identifier::isValid(); non-alphanumeric characters', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('abc-def');
        $expected = true;
        TestCheck::assertBoolean('B.5', '\Identifier::isValid(); non-alphanumeric characters; hyphens and underscores are allowed', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('abc_def');
        $expected = true;
        TestCheck::assertBoolean('B.6', '\Identifier::isValid(); non-alphanumeric characters; hyphens and underscores are allowed', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('abc/');
        $expected = false;
        TestCheck::assertBoolean('B.7', '\Identifier::isValid(); non-alphanumeric characters', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('abc.');
        $expected = false;
        TestCheck::assertBoolean('B.8', '\Identifier::isValid(); non-alphanumeric characters', $actual, $expected, $results);
    }
}
