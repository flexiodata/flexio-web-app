<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-13
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: Validator error constants

        // BEGIN TEST
        $actual = \Flexio\System\Validator::ERROR_NONE;
        $expected = 'none';
        TestCheck::assertString('A.1', '\Flexio\System\Validator::ERROR_NONE; check for existence of constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Validator::ERROR_UNDEFINED;
        $expected = 'undefined';
        TestCheck::assertString('A.2', '\Flexio\System\Validator::ERROR_UNDEFINED; check for existence of constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Validator::ERROR_GENERAL;
        $expected = 'general';
        TestCheck::assertString('A.3', '\Flexio\System\Validator::ERROR_GENERAL; check for existence of constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Validator::ERROR_MISSING_PARAMETER;
        $expected = 'missing_parameter';
        TestCheck::assertString('A.4', '\Flexio\System\Validator::ERROR_MISSING_PARAMETER; check for existence of constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Validator::ERROR_INVALID_PARAMETER;
        $expected = 'invalid_parameter';
        TestCheck::assertString('A.5', '\Flexio\System\Validator::ERROR_INVALID_PARAMETER; check for existence of constant',  $actual, $expected, $results);
    }
}
