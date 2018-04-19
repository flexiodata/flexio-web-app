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


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: Validator error constants

        // BEGIN TEST
        $actual = \Flexio\Base\Validator::ERROR_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('A.2', '\Flexio\Base\Validator::ERROR_UNDEFINED; check for existence of constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Validator::ERROR_GENERAL;
        $expected = 'general';
        \Flexio\Tests\Check::assertString('A.3', '\Flexio\Base\Validator::ERROR_GENERAL; check for existence of constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Validator::ERROR_MISSING_PARAMETER;
        $expected = 'missing-parameter';
        \Flexio\Tests\Check::assertString('A.4', '\Flexio\Base\Validator::ERROR_MISSING_PARAMETER; check for existence of constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Validator::ERROR_INVALID_PARAMETER;
        $expected = 'invalid-parameter';
        \Flexio\Tests\Check::assertString('A.5', '\Flexio\Base\Validator::ERROR_INVALID_PARAMETER; check for existence of constant',  $actual, $expected, $results);
    }
}
