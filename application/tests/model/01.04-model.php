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
        // SETUP
        $model = TestUtil::getModel();



        // TEST: \Model::setTimezone(); invalid input

        // BEGIN TEST
        $actual = TestError::ERROR_NO_EXCEPTION;
        try
        {
            $result = $model->setTimezone(null);
        }
        catch (\Error $e)
        {
            $actual = TestError::ERROR_EXCEPTION;
        }
        $expected = TestError::ERROR_EXCEPTION;
        TestCheck::assertString('A.1', '\Model::setTimezone(); throw an exception with a bad input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestError::ERROR_NO_EXCEPTION;
        try
        {
            $result = $model->setTimezone(true);
        }
        catch (\Error $e)
        {
            $actual = TestError::ERROR_EXCEPTION;
        }
        $expected = TestError::ERROR_EXCEPTION;
        TestCheck::assertString('A.2', '\Model::setTimezone(); throw an exception with a bad input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestError::ERROR_NO_EXCEPTION;
        try
        {
            $result = $model->setTimezone('');
        }
        catch (\Exception $e)
        {
            $actual = TestError::ERROR_EXCEPTION;
        }
        $expected = TestError::ERROR_EXCEPTION;
        TestCheck::assertString('A.1', '\Model::setTimezone(); throw an exception with a bad input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestError::ERROR_NO_EXCEPTION;
        try
        {
            $result = $model->setTimezone('\\');
        }
        catch (\Exception $e)
        {
            $actual = TestError::ERROR_EXCEPTION;
        }
        $expected = TestError::ERROR_EXCEPTION;
        TestCheck::assertString('A.1', '\Model::setTimezone(); throw an exception with a bad input',  $actual, $expected, $results);



        // TEST: \Model::setTimezone(); valid input

        // BEGIN TEST
        $actual = TestError::ERROR_NO_EXCEPTION;
        try
        {
            $result = $model->setTimezone('UTC');
        }
        catch (\Exception $e)
        {
            $actual = TestError::ERROR_EXCEPTION;
        }
        $expected = TestError::ERROR_NO_EXCEPTION;
        TestCheck::assertString('A.1', '\Model::setTimezone(); throw an exception with a bad input',  $actual, $expected, $results);
    }
}
