<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-06-25
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


        // TEST: Rights constant tests

        // BEGIN TEST
        $actual = \Flexio\Object\Rights::ACTION_UNDEFINED;
        $expected = '';
        TestCheck::assertString('A.1', 'Rights access type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Rights::ACTION_READ;
        $expected = 'action_read';
        TestCheck::assertString('A.2', 'Rights access type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Rights::ACTION_WRITE;
        $expected = 'action_write';
        TestCheck::assertString('A.3', 'Rights access type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Rights::ACTION_DELETE;
        $expected = 'action_delete';
        TestCheck::assertString('A.4', 'Rights access type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Rights::ACTION_EXECUTE;
        $expected = 'action_execute';
        TestCheck::assertString('A.5', 'Rights access type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Rights::MEMBER_UNDEFINED;
        $expected = '';
        TestCheck::assertString('A.6', 'Rights member type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Rights::MEMBER_OWNER;
        $expected = 'O';
        TestCheck::assertString('A.7', 'Rights member type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Rights::MEMBER_GROUP;
        $expected = 'G';
        TestCheck::assertString('A.8', 'Rights member type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Rights::MEMBER_PUBLIC;
        $expected = 'P';
        TestCheck::assertString('A.9', 'Rights member type',  $actual, $expected, $results);
    }
}
