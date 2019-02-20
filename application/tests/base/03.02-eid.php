<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-04
 *
 * @package flexio
 * @subpackage Tests
 */


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: eids should be 12 characters; test for invalid lengths

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Base\Eid::isValid() invalid length', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('xxxxxxxxxxx');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\Eid::isValid() invalid length', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('xxxxxxxxxxxxx');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Base\Eid::isValid() invalid length', $actual, $expected, $results);



        // TEST: eids should be 12 characters; test for valid lengths

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('xxxxxxxxxxxx');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Base\Eid::isValid() valid length', $actual, $expected, $results);
    }
}
