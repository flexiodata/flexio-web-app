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
        // TEST: identifiers should be a lowercase string

        // BEGIN TEST
        $actual = \Identifier::isValid('xxxxx');
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Identifier::isValid(); identifiers should be a lowercase string', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('Xxxxx');
        $expected = false;
        TestCheck::assertBoolean('A.2', '\Identifier::isValid(); identifiers should be a lowercase string', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('xxxxX');
        $expected = false;
        TestCheck::assertBoolean('A.3', '\Identifier::isValid(); identifiers should be a lowercase string', $actual, $expected, $results);



        // TEST: identifiers should be between 3 and 39 chars in length

        // BEGIN TEST
        $actual = \Identifier::isValid('xx');
        $expected = false;
        TestCheck::assertBoolean('B.1', '\Identifier::isValid(); identifiers should be between 3 and 39 chars', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('xxx');
        $expected = true;
        TestCheck::assertBoolean('B.2', '\Identifier::isValid(); identifiers should be between 3 and 39 chars', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'); // 39 chars
        $expected = true;
        TestCheck::assertBoolean('B.3', '\Identifier::isValid(); identifiers should be between 3 and 39 chars', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'); // 40 chars
        $expected = false;
        TestCheck::assertBoolean('B.4', '\Identifier::isValid(); identifiers should be between 3 and 39 chars', $actual, $expected, $results);
    }
}
