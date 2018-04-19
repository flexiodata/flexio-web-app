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
        // TEST: Validator error functions; check for error existence

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $actual = $validator->hasErrors();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Base\Validator::hasErrors(); should return false with no errors present',  $actual, $expected, $results);

        // BEGIN TEST
        $validator =\Flexio\Base\Validator::create();
        $validator->setError(\Flexio\Base\Validator::ERROR_GENERAL,'general failure');
        $actual = $validator->hasErrors();
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\Validator::hasErrors(); should return true with single error present',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $validator->setError(\Flexio\Base\Validator::ERROR_GENERAL,'general failure');
        $validator->clear();
        $actual = $validator->hasErrors();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Base\Validator::hasErrors(); should return false after single error is cleared',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $validator->setError(\Flexio\Base\Validator::ERROR_GENERAL,'general failure');
        $validator->setError(\Flexio\Base\Validator::ERROR_MISSING_PARAMETER,'missing parameter');
        $validator->setError(\Flexio\Base\Validator::ERROR_INVALID_PARAMETER,'invalid parameter');
        $actual = $validator->hasErrors();
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.4', '\Flexio\Base\Validator::hasErrors(); should return true when multiple errors are present',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $validator->setError(\Flexio\Base\Validator::ERROR_GENERAL,'general failure');
        $validator->setError(\Flexio\Base\Validator::ERROR_MISSING_PARAMETER,'missing parameter');
        $validator->setError(\Flexio\Base\Validator::ERROR_INVALID_PARAMETER,'invalid parameter');
        $validator->clear();
        $actual = $validator->hasErrors();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.5', '\Flexio\Base\Validator::hasErrors(); should return false after multiple errors are cleared',  $actual, $expected, $results);



        // TEST: Validator error functions; get list of errors

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $actual = $validator->getErrors();
        $expected = array(
        );
        \Flexio\Tests\Check::assertArray('B.1', '\Flexio\Base\Validator::getErrors(); return an empty array when no errors are present',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $validator->setError(\Flexio\Base\Validator::ERROR_GENERAL,'general failure error');
        $actual = $validator->getErrors();
        $expected = array(
            array(
                'code' => \Flexio\Base\Validator::ERROR_GENERAL,
                'message' => 'general failure error'
            )
        );
        \Flexio\Tests\Check::assertArray('B.2', '\Flexio\Base\Validator::getErrors(); return an array with the code and message when a single error is present',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $validator->setError(\Flexio\Base\Validator::ERROR_GENERAL,'general failure error');
        $validator->clear();
        $actual = $validator->getErrors();
        $expected = array(
        );
        \Flexio\Tests\Check::assertArray('B.3', '\Flexio\Base\Validator::getErrors(); return empty array after clearing away an error',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $validator->setError(\Flexio\Base\Validator::ERROR_GENERAL,'general failure error');
        $validator->setError(\Flexio\Base\Validator::ERROR_MISSING_PARAMETER,'missing parameter error');
        $actual = $validator->getErrors();
        $expected = array(
            array(
                'code' => \Flexio\Base\Validator::ERROR_GENERAL,
                'message' => 'general failure error'
            ),
            array(
                'code' => \Flexio\Base\Validator::ERROR_MISSING_PARAMETER,
                'message' => 'missing parameter error'
            )
        );
        \Flexio\Tests\Check::assertArray('B.4', '\Flexio\Base\Validator::getErrors(); return array of errors when multiple errors are present',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $validator->setError(\Flexio\Base\Validator::ERROR_GENERAL,'general failure error');
        $validator->setError(\Flexio\Base\Validator::ERROR_MISSING_PARAMETER,'missing parameter error');
        $validator->setError(\Flexio\Base\Validator::ERROR_INVALID_PARAMETER,'invalid parameter error');
        $actual = $validator->getErrors();
        $expected = array(
            array(
                'code' => \Flexio\Base\Validator::ERROR_GENERAL,
                'message' => 'general failure error'
            ),
            array(
                'code' => \Flexio\Base\Validator::ERROR_MISSING_PARAMETER,
                'message' => 'missing parameter error'
            ),
            array(
                'code' => \Flexio\Base\Validator::ERROR_INVALID_PARAMETER,
                'message' => 'invalid parameter error'
            )
        );
        \Flexio\Tests\Check::assertArray('B.5', '\Flexio\Base\Validator::getErrors(); return array of errors when multiple errors are present',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $validator->setError(\Flexio\Base\Validator::ERROR_GENERAL,'general failure');
        $validator->setError(\Flexio\Base\Validator::ERROR_MISSING_PARAMETER,'missing parameter');
        $validator->setError(\Flexio\Base\Validator::ERROR_INVALID_PARAMETER,'invalid parameter');
        $validator->clear();
        $actual = $validator->getErrors();
        $expected = array(
        );
        \Flexio\Tests\Check::assertArray('B.6', '\Flexio\Base\Validator::getErrors(); return empty array after clearing out multiple errors',  $actual, $expected, $results);
    }
}
