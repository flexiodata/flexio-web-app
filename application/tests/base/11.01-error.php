<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
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
        // TEST: error codes

        // BEGIN TEST
        $actual = \Flexio\Base\Error::UNDEFINED;
        $expected = '';
        TestCheck::assertString('A.1', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::GENERAL;
        $expected = 'general';
        TestCheck::assertString('A.2', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::UNIMPLEMENTED;
        $expected = 'unimplemented';
        TestCheck::assertString('A.3', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::NO_DATABASE;
        $expected = 'no-database';
        TestCheck::assertString('A.4', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::NO_MODEL;
        $expected = 'no-model';
        TestCheck::assertString('A.5', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::NO_SERVICE;
        $expected = 'no-service';
        TestCheck::assertString('A.6', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::MISSING_PARAMETER;
        $expected = 'missing-parameter';
        TestCheck::assertString('A.7', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::INVALID_PARAMETER;
        $expected = 'invalid-parameter';
        TestCheck::assertString('A.8', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::INVALID_SYNTAX;
        $expected = 'invalid-syntax';
        TestCheck::assertString('A.9', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::NO_OBJECT;
        $expected = 'no-object';
        TestCheck::assertString('A.10', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::CONNECTION_FAILED;
        $expected = 'connection-failed';
        TestCheck::assertString('A.11', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::CREATE_FAILED;
        $expected = 'create-failed';
        TestCheck::assertString('A.12', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::DELETE_FAILED;
        $expected = 'delete-failed';
        TestCheck::assertString('A.13', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::WRITE_FAILED;
        $expected = 'write-failed';
        TestCheck::assertString('A.14', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::READ_FAILED;
        $expected = 'read-failed';
        TestCheck::assertString('A.15', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::UNAUTHORIZED;
        $expected = 'unauthorized';
        TestCheck::assertString('A.16', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::INSUFFICIENT_RIGHTS;
        $expected = 'insufficient-rights';
        TestCheck::assertString('A.17', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::SIZE_LIMIT_EXCEEDED;
        $expected = 'size-limit-exceeded';
        TestCheck::assertString('A.18', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::RATE_LIMIT_EXCEEDED;
        $expected = 'rate-limit-exceeded';
        TestCheck::assertString('A.19', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::INVALID_VERSION;
        $expected = 'invalid-version';
        TestCheck::assertString('A.20', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::INVALID_REQUEST;
        $expected = 'invalid-request';
        TestCheck::assertString('A.21', '\Flexio\Base\Error; constant check', $actual, $expected, $results);
    }
}
