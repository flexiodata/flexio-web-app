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
        // TEST: non-string inputs

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid(null);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Base\Eid::isValid() null input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid(false);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\Eid::isValid() false input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid(true);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Base\Eid::isValid() true input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid(111111111111);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.4', '\Flexio\Base\Eid::isValid() numeric input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid(array());
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.5', '\Flexio\Base\Eid::isValid() array input', $actual, $expected, $results);
    }
}
