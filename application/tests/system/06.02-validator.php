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


class Test
{
    public function run(&$results)
    {
        // TEST: Validator error functions; check for error existence

        // BEGIN TEST
        $validator = Validator::getInstance();
        $actual = $validator->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.1', 'Validator::hasErrors(); should return false with no errors present',  $actual, $expected, $results);

        // BEGIN TEST
        $validator =Validator::getInstance();
        $validator->setError(Validator::ERROR_GENERAL,'general failure');
        $actual = $validator->hasErrors();
        $expected = true;
        TestCheck::assertBoolean('A.2', 'Validator::hasErrors(); should return true with single error present',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = Validator::getInstance();
        $validator->setError(Validator::ERROR_GENERAL,'general failure');
        $validator->clearErrors();
        $actual = $validator->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.3', 'Validator::hasErrors(); should return false after single error is cleared',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = Validator::getInstance();
        $validator->setError(Validator::ERROR_GENERAL,'general failure');
        $validator->setError(Validator::ERROR_MISSING_PARAMETER,'missing parameter');
        $validator->setError(Validator::ERROR_INVALID_PARAMETER,'invalid parameter');
        $actual = $validator->hasErrors();
        $expected = true;
        TestCheck::assertBoolean('A.4', 'Validator::hasErrors(); should return true when multiple errors are present',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = Validator::getInstance();
        $validator->setError(Validator::ERROR_GENERAL,'general failure');
        $validator->setError(Validator::ERROR_MISSING_PARAMETER,'missing parameter');
        $validator->setError(Validator::ERROR_INVALID_PARAMETER,'invalid parameter');
        $validator->clearErrors();
        $actual = $validator->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.5', 'Validator::hasErrors(); should return false after multiple errors are cleared',  $actual, $expected, $results);



        // TEST: Validator error functions; get list of errors

        // BEGIN TEST
        $validator = Validator::getInstance();
        $actual = $validator->getErrors();
        $expected = array(
        );
        TestCheck::assertArray('B.1', 'Validator::getErrors(); return an empty array when no errors are present',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = Validator::getInstance();
        $validator->setError(Validator::ERROR_GENERAL,'general failure error');
        $actual = $validator->getErrors();
        $expected = array(
            array(
                'code' => Validator::ERROR_GENERAL,
                'message' => 'general failure error'
            )
        );
        TestCheck::assertArray('B.2', 'Validator::getErrors(); return an array with the code and message when a single error is present',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = Validator::getInstance();
        $validator->setError(Validator::ERROR_GENERAL,'general failure error');
        $validator->clearErrors();
        $actual = $validator->getErrors();
        $expected = array(
        );
        TestCheck::assertArray('B.3', 'Validator::getErrors(); return empty array after clearing away an error',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = Validator::getInstance();
        $validator->setError(Validator::ERROR_GENERAL,'general failure error');
        $validator->setError(Validator::ERROR_MISSING_PARAMETER,'missing parameter error');
        $actual = $validator->getErrors();
        $expected = array(
            array(
                'code' => Validator::ERROR_GENERAL,
                'message' => 'general failure error'
            ),
            array(
                'code' => Validator::ERROR_MISSING_PARAMETER,
                'message' => 'missing parameter error'
            )
        );
        TestCheck::assertArray('B.4', 'Validator::getErrors(); return array of errors when multiple errors are present',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = Validator::getInstance();
        $validator->setError(Validator::ERROR_GENERAL,'general failure error');
        $validator->setError(Validator::ERROR_MISSING_PARAMETER,'missing parameter error');
        $validator->setError(Validator::ERROR_INVALID_PARAMETER,'invalid parameter error');
        $actual = $validator->getErrors();
        $expected = array(
            array(
                'code' => Validator::ERROR_GENERAL,
                'message' => 'general failure error'
            ),
            array(
                'code' => Validator::ERROR_MISSING_PARAMETER,
                'message' => 'missing parameter error'
            ),
            array(
                'code' => Validator::ERROR_INVALID_PARAMETER,
                'message' => 'invalid parameter error'
            )
        );
        TestCheck::assertArray('B.5', 'Validator::getErrors(); return array of errors when multiple errors are present',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = Validator::getInstance();
        $validator->setError(Validator::ERROR_GENERAL,'general failure');
        $validator->setError(Validator::ERROR_MISSING_PARAMETER,'missing parameter');
        $validator->setError(Validator::ERROR_INVALID_PARAMETER,'invalid parameter');
        $validator->clearErrors();
        $actual = $validator->getErrors();
        $expected = array(
        );
        TestCheck::assertArray('B.6', 'Validator::getErrors(); return empty array after clearing out multiple errors',  $actual, $expected, $results);
    }
}
