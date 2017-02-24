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


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: identifiers should start with a character

        // BEGIN TEST
        $actual = \Flexio\System\Identifier::isValid('abc');
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Flexio\System\Identifier::isValid(); identifiers should start with a character', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Identifier::isValid('1bc');
        $expected = false;
        TestCheck::assertBoolean('A.2', '\Flexio\System\Identifier::isValid(); identifiers should start with a character', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Identifier::isValid('.bc');
        $expected = false;
        TestCheck::assertBoolean('A.3', '\Flexio\System\Identifier::isValid(); identifiers should start with a character', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Identifier::isValid('/bc');
        $expected = false;
        TestCheck::assertBoolean('A.4', '\Flexio\System\Identifier::isValid(); identifiers should start with a character', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Identifier::isValid('\bc');
        $expected = false;
        TestCheck::assertBoolean('A.5', '\Flexio\System\Identifier::isValid(); identifiers should start with a character', $actual, $expected, $results);



        // TEST: chars after first char should be alphanmeric

        // BEGIN TEST
        $actual = \Flexio\System\Identifier::isValid('abcdef123');
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Flexio\System\Identifier::isValid(); non-alphanumeric characters', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Identifier::isValid('abc.def');
        $expected = false;
        TestCheck::assertBoolean('B.2', '\Flexio\System\Identifier::isValid(); non-alphanumeric characters', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Identifier::isValid('abc/def');
        $expected = false;
        TestCheck::assertBoolean('B.3', '\Flexio\System\Identifier::isValid(); non-alphanumeric characters', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Identifier::isValid('abc\def');
        $expected = false;
        TestCheck::assertBoolean('B.4', '\Flexio\System\Identifier::isValid(); non-alphanumeric characters', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Identifier::isValid('abc-def');
        $expected = true;
        TestCheck::assertBoolean('B.5', '\Flexio\System\Identifier::isValid(); non-alphanumeric characters; hyphens and underscores are allowed', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Identifier::isValid('abc_def');
        $expected = true;
        TestCheck::assertBoolean('B.6', '\Flexio\System\Identifier::isValid(); non-alphanumeric characters; hyphens and underscores are allowed', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Identifier::isValid('abc/');
        $expected = false;
        TestCheck::assertBoolean('B.7', '\Flexio\System\Identifier::isValid(); non-alphanumeric characters', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Identifier::isValid('abc.');
        $expected = false;
        TestCheck::assertBoolean('B.8', '\Flexio\System\Identifier::isValid(); non-alphanumeric characters', $actual, $expected, $results);
    }
}
