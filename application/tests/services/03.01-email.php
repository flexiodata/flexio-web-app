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
        // TEST: non-string inputs

        // BEGIN TEST
        $actual = \Flexio\Services\Email::isValid(null);
        $expected = false;
        TestCheck::assertBoolean('A.1', '\Flexio\Services\Email::isValid() null input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::isValid(false);
        $expected = false;
        TestCheck::assertBoolean('A.2', '\Flexio\Services\Email::isValid() false input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::isValid(true);
        $expected = false;
        TestCheck::assertBoolean('A.3', '\Flexio\Services\Email::isValid() true input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::isValid(111111111111);
        $expected = false;
        TestCheck::assertBoolean('A.4', '\Flexio\Services\Email::isValid() numeric input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::isValid(array());
        $expected = false;
        TestCheck::assertBoolean('A.5', '\Flexio\Services\Email::isValid() array input', $actual, $expected, $results);
    }
}
