<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-11
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
        // SETUP
        $model = \Flexio\Tests\Util::getModel();



        // TEST: misc comment functions

        // BEGIN TEST
        $actual = true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', 'Misc comment functions; TODO: fill out',  $actual, $expected, $results);
    }
}
