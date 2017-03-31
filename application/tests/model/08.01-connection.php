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
        $model = TestUtil::getModel();



        // TEST: model constant tests

        // BEGIN TEST
        $actual = \Model::CONNECTION_STATUS_INVALID;
        $expected = 'I';
        TestCheck::assertString('A.1', 'Connection status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_STATUS_AVAILABLE;
        $expected = 'A';
        TestCheck::assertString('A.2', 'Connection status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_STATUS_UNAVAILABLE;
        $expected = 'U';
        TestCheck::assertString('A.3', 'Connection status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_STATUS_ERROR;
        $expected = 'E';
        TestCheck::assertString('A.4', 'Connection status constant',  $actual, $expected, $results);



        // TEST: misc connection functions

        // BEGIN TEST
        $actual = true;
        $expected = true;
        TestCheck::assertBoolean('B.1', 'Misc connection functions; TODO: fill out',  $actual, $expected, $results);
    }
}
