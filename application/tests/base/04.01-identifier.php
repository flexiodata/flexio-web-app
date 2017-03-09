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
        $actual = \Flexio\Base\Identifier::isValid(null);
        $expected = false;
        TestCheck::assertBoolean('A.1', '\Flexio\Base\Identifier::isValid() null input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid(false);
        $expected = false;
        TestCheck::assertBoolean('A.2', '\Flexio\Base\Identifier::isValid() false input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid(true);
        $expected = false;
        TestCheck::assertBoolean('A.3', '\Flexio\Base\Identifier::isValid() true input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid(111111111111);
        $expected = false;
        TestCheck::assertBoolean('A.4', '\Flexio\Base\Identifier::isValid() numeric input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid(array());
        $expected = false;
        TestCheck::assertBoolean('A.5', '\Flexio\Base\Identifier::isValid() array input', $actual, $expected, $results);
    }
}
