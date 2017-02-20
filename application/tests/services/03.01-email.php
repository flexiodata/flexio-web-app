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
        $actual = Email::isValid(null);
        $expected = false;
        TestCheck::assertBoolean('A.1', 'Email::isValid() null input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = Email::isValid(false);
        $expected = false;
        TestCheck::assertBoolean('A.2', 'Email::isValid() false input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = Email::isValid(true);
        $expected = false;
        TestCheck::assertBoolean('A.3', 'Email::isValid() true input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = Email::isValid(111111111111);
        $expected = false;
        TestCheck::assertBoolean('A.4', 'Email::isValid() numeric input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = Email::isValid(array());
        $expected = false;
        TestCheck::assertBoolean('A.5', 'Email::isValid() array input', $actual, $expected, $results);
    }
}
