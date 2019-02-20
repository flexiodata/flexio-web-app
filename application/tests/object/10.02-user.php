<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
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
        // TEST: constant tests

        // BEGIN TEST
        $actual = \Flexio\Object\User::MEMBER_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('A.1', 'User member type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\User::MEMBER_OWNER;
        $expected = 'owner';
        \Flexio\Tests\Check::assertString('A.2', 'User member type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\User::MEMBER_GROUP;
        $expected = 'member';
        \Flexio\Tests\Check::assertString('A.3', 'User member type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\User::MEMBER_PUBLIC;
        $expected = 'public';
        \Flexio\Tests\Check::assertString('A.4', 'User member type',  $actual, $expected, $results);
    }
}
