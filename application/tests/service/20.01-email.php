<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-06-17
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
        // TEST: non-string inputs

        // BEGIN TEST
        $actual = '';
        try
        {
            \Flexio\Services\NoticeEmail::isValid(null);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Services\NoticeEmail::isValid() null input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Flexio\Services\NoticeEmail::isValid(false);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.2', '\Flexio\Services\NoticeEmail::isValid() false input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Flexio\Services\NoticeEmail::isValid(true);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.3', '\Flexio\Services\NoticeEmail::isValid() true input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Flexio\Services\NoticeEmail::isValid(1111);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.4', '\Flexio\Services\NoticeEmail::isValid() numeric input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Flexio\Services\NoticeEmail::isValid(array());
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.5', '\Flexio\Services\NoticeEmail::isValid() array input', $actual, $expected, $results);
    }
}
