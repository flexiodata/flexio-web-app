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


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: error codes

        // BEGIN TEST
        $actual = \Flexio\Base\Error::NONE;
        $expected = 'none';
        TestCheck::assertString('A.1', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::UNDEFINED;
        $expected = 'undefined';
        TestCheck::assertString('A.2', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::GENERAL;
        $expected = 'general';
        TestCheck::assertString('A.3', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::UNIMPLEMENTED;
        $expected = 'unimplemented';
        TestCheck::assertString('A.4', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::NO_DATABASE;
        $expected = 'no_database';
        TestCheck::assertString('A.5', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::NO_MODEL;
        $expected = 'no_model';
        TestCheck::assertString('A.6', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::NO_SERVICE;
        $expected = 'no_service';
        TestCheck::assertString('A.7', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::MISSING_PARAMETER;
        $expected = 'missing_parameter';
        TestCheck::assertString('A.8', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::INVALID_PARAMETER;
        $expected = 'invalid_parameter';
        TestCheck::assertString('A.9', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::INVALID_SYNTAX;
        $expected = 'invalid_syntax';
        TestCheck::assertString('A.10', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::NO_OBJECT;
        $expected = 'no_object';
        TestCheck::assertString('A.11', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::CREATE_FAILED;
        $expected = 'create_failed';
        TestCheck::assertString('A.12', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::DELETE_FAILED;
        $expected = 'delete_failed';
        TestCheck::assertString('A.13', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::WRITE_FAILED;
        $expected = 'write_failed';
        TestCheck::assertString('A.14', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::READ_FAILED;
        $expected = 'read_failed';
        TestCheck::assertString('A.15', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::UNAUTHORIZED;
        $expected = 'unauthorized';
        TestCheck::assertString('A.16', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::INSUFFICIENT_RIGHTS;
        $expected = 'insufficient_rights';
        TestCheck::assertString('A.17', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::SIZE_LIMIT_EXCEEDED;
        $expected = 'size_limit_exceeded';
        TestCheck::assertString('A.18', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::INVALID_METHOD;
        $expected = 'invalid_method';
        TestCheck::assertString('A.19', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::INVALID_VERSION;
        $expected = 'invalid_version';
        TestCheck::assertString('A.20', '\Flexio\Base\Error; constant check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Error::INVALID_REQUEST;
        $expected = 'invalid_request';
        TestCheck::assertString('A.21', '\Flexio\Base\Error; constant check', $actual, $expected, $results);
    }
}
