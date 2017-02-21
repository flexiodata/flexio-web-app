<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-04
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
        $actual = \Eid::isValid(null);
        $expected = false;
        TestCheck::assertBoolean('A.1', '\Eid::isValid() null input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Eid::isValid(false);
        $expected = false;
        TestCheck::assertBoolean('A.2', '\Eid::isValid() false input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Eid::isValid(true);
        $expected = false;
        TestCheck::assertBoolean('A.3', '\Eid::isValid() true input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Eid::isValid(111111111111);
        $expected = false;
        TestCheck::assertBoolean('A.4', '\Eid::isValid() numeric input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Eid::isValid(array());
        $expected = false;
        TestCheck::assertBoolean('A.5', '\Eid::isValid() array input', $actual, $expected, $results);
    }
}
