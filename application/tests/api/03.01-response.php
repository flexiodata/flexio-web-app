<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-04-03
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


        // TEST: tests for checking HTTP error code mappings

        // BEGIN TEST
        $flexio_error_code = \Flexio\Base\Error::GENERAL;
        $actual = \Flexio\Api2\Response::getHttpErrorCode($flexio_error_code);
        $expected = 400;
        \Flexio\Tests\Check::assertNumber('A.1', '\Flexio\Api2\Response::getHttpErrorCode(); check http error code mapping',  $actual, $expected, $results);

        // BEGIN TEST
        $flexio_error_code = \Flexio\Base\Error::UNAUTHORIZED;
        $actual = \Flexio\Api2\Response::getHttpErrorCode($flexio_error_code);
        $expected = 401;
        \Flexio\Tests\Check::assertNumber('A.2', '\Flexio\Api2\Response::getHttpErrorCode(); check http error code mapping',  $actual, $expected, $results);

        // BEGIN TEST
        $flexio_error_code = \Flexio\Base\Error::INSUFFICIENT_RIGHTS;
        $actual = \Flexio\Api2\Response::getHttpErrorCode($flexio_error_code);
        $expected = 403;
        \Flexio\Tests\Check::assertNumber('A.3', '\Flexio\Api2\Response::getHttpErrorCode(); check http error code mapping',  $actual, $expected, $results);

        // BEGIN TEST
        $flexio_error_code = \Flexio\Base\Error::UNIMPLEMENTED;
        $actual = \Flexio\Api2\Response::getHttpErrorCode($flexio_error_code);
        $expected = 404;
        \Flexio\Tests\Check::assertNumber('A.4', '\Flexio\Api2\Response::getHttpErrorCode(); check http error code mapping',  $actual, $expected, $results);

        // BEGIN TEST
        $flexio_error_code = \Flexio\Base\Error::INVALID_VERSION;
        $actual = \Flexio\Api2\Response::getHttpErrorCode($flexio_error_code);
        $expected = 404;
        \Flexio\Tests\Check::assertNumber('A.5', '\Flexio\Api2\Response::getHttpErrorCode(); check http error code mapping',  $actual, $expected, $results);

        // BEGIN TEST
        $flexio_error_code = \Flexio\Base\Error::INVALID_PARAMETER;
        $actual = \Flexio\Api2\Response::getHttpErrorCode($flexio_error_code);
        $expected = 404;
        \Flexio\Tests\Check::assertNumber('A.6', '\Flexio\Api2\Response::getHttpErrorCode(); check http error code mapping',  $actual, $expected, $results);

        // BEGIN TEST
        $flexio_error_code = \Flexio\Base\Error::NO_OBJECT;
        $actual = \Flexio\Api2\Response::getHttpErrorCode($flexio_error_code);
        $expected = 404;
        \Flexio\Tests\Check::assertNumber('A.7', '\Flexio\Api2\Response::getHttpErrorCode(); check http error code mapping',  $actual, $expected, $results);

        // BEGIN TEST
        $flexio_error_code = \Flexio\Base\Error::CREATE_FAILED;
        $actual = \Flexio\Api2\Response::getHttpErrorCode($flexio_error_code);
        $expected = 422;
        \Flexio\Tests\Check::assertNumber('A.8', '\Flexio\Api2\Response::getHttpErrorCode(); check http error code mapping',  $actual, $expected, $results);

        // BEGIN TEST
        $flexio_error_code = \Flexio\Base\Error::WRITE_FAILED;
        $actual = \Flexio\Api2\Response::getHttpErrorCode($flexio_error_code);
        $expected = 422;
        \Flexio\Tests\Check::assertNumber('A.9', '\Flexio\Api2\Response::getHttpErrorCode(); check http error code mapping',  $actual, $expected, $results);

        // BEGIN TEST
        $flexio_error_code = \Flexio\Base\Error::RATE_LIMIT_EXCEEDED;
        $actual = \Flexio\Api2\Response::getHttpErrorCode($flexio_error_code);
        $expected = 422;
        \Flexio\Tests\Check::assertNumber('A.10', '\Flexio\Api2\Response::getHttpErrorCode(); check http error code mapping',  $actual, $expected, $results);

        // BEGIN TEST
        $flexio_error_code = \Flexio\Base\Error::UNDEFINED;
        $actual = \Flexio\Api2\Response::getHttpErrorCode($flexio_error_code);
        $expected = 500;
        \Flexio\Tests\Check::assertNumber('A.11', '\Flexio\Api2\Response::getHttpErrorCode(); check http error code mapping',  $actual, $expected, $results);

        // BEGIN TEST
        $flexio_error_code = \Flexio\Base\Error::NO_SERVICE;
        $actual = \Flexio\Api2\Response::getHttpErrorCode($flexio_error_code);
        $expected = 500;
        \Flexio\Tests\Check::assertNumber('A.12', '\Flexio\Api2\Response::getHttpErrorCode(); check http error code mapping',  $actual, $expected, $results);
    }
}
