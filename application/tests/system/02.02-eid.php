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


class Test
{
    public function run(&$results)
    {
        // TEST: eids should be 12 characters; test for invalid lengths

        // BEGIN TEST
        $actual = \Eid::isValid('');
        $expected = false;
        TestCheck::assertBoolean('A.1', '\Eid::isValid() invalid length', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Eid::isValid('xxxxxxxxxxx');
        $expected = false;
        TestCheck::assertBoolean('A.2', '\Eid::isValid() invalid length', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Eid::isValid('xxxxxxxxxxxxx');
        $expected = false;
        TestCheck::assertBoolean('A.3', '\Eid::isValid() invalid length', $actual, $expected, $results);



        // TEST: eids should be 12 characters; test for valid lengths

        // BEGIN TEST
        $actual = \Eid::isValid('xxxxxxxxxxxx');
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Eid::isValid() valid length', $actual, $expected, $results);
    }
}
