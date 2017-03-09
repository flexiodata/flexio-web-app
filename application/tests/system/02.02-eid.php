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
        // TEST: eids should be 12 characters; test for invalid lengths

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('');
        $expected = false;
        TestCheck::assertBoolean('A.1', '\Flexio\Base\Eid::isValid() invalid length', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('xxxxxxxxxxx');
        $expected = false;
        TestCheck::assertBoolean('A.2', '\Flexio\Base\Eid::isValid() invalid length', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('xxxxxxxxxxxxx');
        $expected = false;
        TestCheck::assertBoolean('A.3', '\Flexio\Base\Eid::isValid() invalid length', $actual, $expected, $results);



        // TEST: eids should be 12 characters; test for valid lengths

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('xxxxxxxxxxxx');
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Flexio\Base\Eid::isValid() valid length', $actual, $expected, $results);
    }
}
