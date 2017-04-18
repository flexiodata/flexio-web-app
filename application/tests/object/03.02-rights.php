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


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();


        // TEST: Action constant tests

        // BEGIN TEST
        $actual = \Flexio\Object\Rights::MEMBER_UNDEFINED;
        $expected = '';
        TestCheck::assertString('A.1', 'Rights member type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Rights::MEMBER_OWNER;
        $expected = 'O';
        TestCheck::assertString('A.2', 'Rights member type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Rights::MEMBER_GROUP;
        $expected = 'G';
        TestCheck::assertString('A.3', 'Rights member type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Rights::MEMBER_PUBLIC;
        $expected = 'P';
        TestCheck::assertString('A.4', 'Rights member type',  $actual, $expected, $results);
    }
}
