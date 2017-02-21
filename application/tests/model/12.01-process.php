<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-03-22
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TODO: need to add tests for process set(), get(), delete(), etc

        // TEST: task status constant tests

        // BEGIN TEST
        $actual = \Model::PROCESS_STATUS_UNDEFINED;
        $expected = '';
        TestCheck::assertString('A.1', 'ProcessModel task status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PROCESS_STATUS_PENDING;
        $expected = 'S';
        TestCheck::assertString('A.2', 'ProcessModel task status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PROCESS_STATUS_WAITING;
        $expected = 'W';
        TestCheck::assertString('A.3', 'ProcessModel task status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PROCESS_STATUS_RUNNING;
        $expected = 'R';
        TestCheck::assertString('A.4', 'ProcessModel task status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PROCESS_STATUS_CANCELLED;
        $expected = 'X';
        TestCheck::assertString('A.5', 'ProcessModel task status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PROCESS_STATUS_PAUSED;
        $expected = 'P';
        TestCheck::assertString('A.6', 'ProcessModel task status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PROCESS_STATUS_FAILED;
        $expected = 'F';
        TestCheck::assertString('A.7', 'ProcessModel task status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PROCESS_STATUS_COMPLETED;
        $expected = 'C';
        TestCheck::assertString('A.8', 'ProcessModel task status constant',  $actual, $expected, $results);
    }
}
