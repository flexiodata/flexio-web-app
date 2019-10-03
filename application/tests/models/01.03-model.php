<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
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
        // FUNCTION: \Flexio\Model\Model::setTimezone()


        // TEST: invalid input

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
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Model\Model::setTimezone(); throw an exception with a bad input',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertString('A.2', '\Flexio\Model\Model::setTimezone(); throw an exception with a bad input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        try
        {
            $result = \Flexio\Tests\Util::getModel()->setTimezone('');
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Model\Model::setTimezone(); throw an exception with a bad input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        try
        {
            $result = \Flexio\Tests\Util::getModel()->setTimezone('\\');
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Model\Model::setTimezone(); throw an exception with a bad input',  $actual, $expected, $results);


        // TEST: valid input

        // BEGIN TEST
        $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        try
        {
            $result = \Flexio\Tests\Util::getModel()->setTimezone('UTC');
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Model\Model::setTimezone(); throw an exception with a bad input',  $actual, $expected, $results);
    }
}
