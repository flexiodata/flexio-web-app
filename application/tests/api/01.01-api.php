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


class Test
{
    public function run(&$results)
    {
        // TEST: api constant tests

        // BEGIN TEST
        $actual = \Flexio\Api\Api::ERROR_NONE;
        $expected = 'none';
        TestCheck::assertString('A.1', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Api\Api::ERROR_UNDEFINED;
        $expected = 'undefined';
        TestCheck::assertString('A.2', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Api\Api::ERROR_GENERAL;
        $expected = 'general';
        TestCheck::assertString('A.3', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Api\Api::ERROR_UNIMPLEMENTED;
        $expected = 'unimplemented';
        TestCheck::assertString('A.4', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Api\Api::ERROR_NO_DATABASE;
        $expected = 'no_database';
        TestCheck::assertString('A.5', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Api\Api::ERROR_NO_MODEL;
        $expected = 'no_model';
        TestCheck::assertString('A.6', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Api\Api::ERROR_NO_SERVICE;
        $expected = 'no_service';
        TestCheck::assertString('A.7', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Api\Api::ERROR_INVALID_SYNTAX;
        $expected = 'invalid_syntax';
        TestCheck::assertString('A.8', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Api\Api::ERROR_MISSING_PARAMETER;
        $expected = 'missing_parameter';
        TestCheck::assertString('A.9', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Api\Api::ERROR_INVALID_PARAMETER;
        $expected = 'invalid_parameter';
        TestCheck::assertString('A.10', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Api\Api::ERROR_NO_OBJECT;
        $expected = 'no_object';
        TestCheck::assertString('A.11', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Api\Api::ERROR_CREATE_FAILED;
        $expected = 'create_failed';
        TestCheck::assertString('A.12', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Api\Api::ERROR_DELETE_FAILED;
        $expected = 'delete_failed';
        TestCheck::assertString('A.13', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Api\Api::ERROR_WRITE_FAILED;
        $expected = 'write_failed';
        TestCheck::assertString('A.14', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Api\Api::ERROR_READ_FAILED;
        $expected = 'read_failed';
        TestCheck::assertString('A.15', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Api\Api::ERROR_INSUFFICIENT_RIGHTS;
        $expected = 'insufficient_rights';
        TestCheck::assertString('A.16', 'Api error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Api\Api::ERROR_SIZE_LIMIT_EXCEEDED;
        $expected = 'size_limit_exceeded';
        TestCheck::assertString('A.17', 'Api error constant',  $actual, $expected, $results);
    }
}
