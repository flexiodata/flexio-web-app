<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-04-13
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
        // TEST: validation tests for \Flexio\Base\Password::isValid()

        // BEGIN TEST
        $actual = \Flexio\Base\Password::isValid(null);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Base\Password::isValid(); test for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Password::isValid(true);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\Password::isValid(); test for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Password::isValid(1111111111);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Base\Password::isValid(); test for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Password::isValid('');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.4', '\Flexio\Base\Password::isValid(); valid passwords require a minimum length of 8 characters and at least 1 number',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Password::isValid('a');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.5', '\Flexio\Base\Password::isValid(); valid passwords require a minimum length of 8 characters and at least 1 number',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Password::isValid('aaaaaaa');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.6', '\Flexio\Base\Password::isValid(); valid passwords require a minimum length of 8 characters and at least 1 number',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Password::isValid('aaaaaaa');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.7', '\Flexio\Base\Password::isValid(); valid passwords require a minimum length of 8 characters and at least 1 number',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Password::isValid('aaaaaaaa');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.8', '\Flexio\Base\Password::isValid(); valid passwords require a minimum length of 8 characters and at least 1 number',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Password::isValid('aaaaaaaaa');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.9', '\Flexio\Base\Password::isValid(); valid passwords require a minimum length of 8 characters and at least 1 number',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Password::isValid('aaaaaa1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.10', '\Flexio\Base\Password::isValid(); valid passwords require a minimum length of 8 characters and at least 1 number',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Password::isValid('aaaaaaa1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.11', '\Flexio\Base\Password::isValid(); valid passwords require a minimum length of 8 characters and at least 1 number',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Password::isValid('aaaaaaaa1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.12', '\Flexio\Base\Password::isValid(); valid passwords require a minimum length of 8 characters and at least 1 number',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Password::isValid('1aaaaaa');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.13', '\Flexio\Base\Password::isValid(); valid passwords require a minimum length of 8 characters and at least 1 number',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Password::isValid('1aaaaaaa');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.14', '\Flexio\Base\Password::isValid(); valid passwords require a minimum length of 8 characters and at least 1 number',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Password::isValid('1aaaaaaaa');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.15', '\Flexio\Base\Password::isValid(); valid passwords require a minimum length of 8 characters and at least 1 number',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Password::isValid('aaa1aaa');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.16', '\Flexio\Base\Password::isValid(); valid passwords require a minimum length of 8 characters and at least 1 number',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Password::isValid('aaa1aaaa');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.17', '\Flexio\Base\Password::isValid(); valid passwords require a minimum length of 8 characters and at least 1 number',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Password::isValid('aaa1aaaaa');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.18', '\Flexio\Base\Password::isValid(); valid passwords require a minimum length of 8 characters and at least 1 number',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Password::isValid('aaa.aaa');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.19', '\Flexio\Base\Password::isValid(); valid passwords require a minimum length of 8 characters and at least 1 number',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Password::isValid('aaa.aaaa');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.20', '\Flexio\Base\Password::isValid(); valid passwords require a minimum length of 8 characters and at least 1 number',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Password::isValid('aaa.aaaaa');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.21', '\Flexio\Base\Password::isValid(); valid passwords require a minimum length of 8 characters and at least 1 number',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Password::isValid('aa1.aaa');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.22', '\Flexio\Base\Password::isValid(); valid passwords require a minimum length of 8 characters and at least 1 number',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Password::isValid('aaa.a1aa');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.23', '\Flexio\Base\Password::isValid(); valid passwords require a minimum length of 8 characters and at least 1 number',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Password::isValid('aaa.aaaa1a');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.24', '\Flexio\Base\Password::isValid(); valid passwords require a minimum length of 8 characters and at least 1 number',  $actual, $expected, $results);
    }
}
