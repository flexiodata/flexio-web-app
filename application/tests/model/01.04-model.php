<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-06
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
        // TEST: \Model::setTimezone(); invalid input

        // BEGIN TEST
        $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        try
        {
            $result = \Flexio\Tests\Util::getModel()->setTimezone(null);
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', '\Model::setTimezone(); throw an exception with a bad input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        try
        {
            $result = \Flexio\Tests\Util::getModel()->setTimezone(true);
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.2', '\Model::setTimezone(); throw an exception with a bad input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        try
        {
            $result = \Flexio\Tests\Util::getModel()->setTimezone('');
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', '\Model::setTimezone(); throw an exception with a bad input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        try
        {
            $result = \Flexio\Tests\Util::getModel()->setTimezone('\\');
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', '\Model::setTimezone(); throw an exception with a bad input',  $actual, $expected, $results);



        // TEST: \Model::setTimezone(); valid input

        // BEGIN TEST
        $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        try
        {
            $result = \Flexio\Tests\Util::getModel()->setTimezone('UTC');
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', '\Model::setTimezone(); throw an exception with a bad input',  $actual, $expected, $results);
    }
}
