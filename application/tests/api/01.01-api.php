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
        // TEST: api constant tests

        // BEGIN TEST
        $actual = \Flexio\Base\Error::UNDEFINED;
        $expected = '';
        TestCheck::assertString('A.1', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::GENERAL;
        $expected = 'general';
        TestCheck::assertString('A.2', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::UNIMPLEMENTED;
        $expected = 'unimplemented';
        TestCheck::assertString('A.3', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::NO_DATABASE;
        $expected = 'no-database';
        TestCheck::assertString('A.4', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::NO_MODEL;
        $expected = 'no-model';
        TestCheck::assertString('A.5', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::NO_SERVICE;
        $expected = 'no-service';
        TestCheck::assertString('A.6', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::INVALID_SYNTAX;
        $expected = 'invalid-syntax';
        TestCheck::assertString('A.7', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::MISSING_PARAMETER;
        $expected = 'missing-parameter';
        TestCheck::assertString('A.8', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::INVALID_PARAMETER;
        $expected = 'invalid-parameter';
        TestCheck::assertString('A.9', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::NO_OBJECT;
        $expected = 'no-object';
        TestCheck::assertString('A.10', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::CREATE_FAILED;
        $expected = 'create-failed';
        TestCheck::assertString('A.11', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::DELETE_FAILED;
        $expected = 'delete-failed';
        TestCheck::assertString('A.12', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::WRITE_FAILED;
        $expected = 'write-failed';
        TestCheck::assertString('A.13', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::READ_FAILED;
        $expected = 'read-failed';
        TestCheck::assertString('A.14', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::INSUFFICIENT_RIGHTS;
        $expected = 'insufficient-rights';
        TestCheck::assertString('A.15', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::SIZE_LIMIT_EXCEEDED;
        $expected = 'size-limit-exceeded';
        TestCheck::assertString('A.16', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::RATE_LIMIT_EXCEEDED;
        $expected = 'rate-limit-exceeded';
        TestCheck::assertString('A.17', 'Api error constant',  $actual, $expected, $results);
    }
}
