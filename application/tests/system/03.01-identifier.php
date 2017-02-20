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
        // TEST: non-string inputs

        // BEGIN TEST
        $actual = Identifier::isValid(null);
        $expected = false;
        TestCheck::assertBoolean('A.1', 'Identifier::isValid() null input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = Identifier::isValid(false);
        $expected = false;
        TestCheck::assertBoolean('A.2', 'Identifier::isValid() false input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = Identifier::isValid(true);
        $expected = false;
        TestCheck::assertBoolean('A.3', 'Identifier::isValid() true input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = Identifier::isValid(111111111111);
        $expected = false;
        TestCheck::assertBoolean('A.4', 'Identifier::isValid() numeric input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = Identifier::isValid(array());
        $expected = false;
        TestCheck::assertBoolean('A.5', 'Identifier::isValid() array input', $actual, $expected, $results);
    }
}
