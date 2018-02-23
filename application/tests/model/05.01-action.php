<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-07-21
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
        // TEST: misc action functions

        // BEGIN TEST
        $actual = true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', 'Misc action functions; TODO: fill out',  $actual, $expected, $results);
    }
}
