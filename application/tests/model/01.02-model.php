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


class Test
{
    public function run(&$results)
    {
        // TEST: Model error functions; check for error existence

        // BEGIN TEST
        System::getModel()->clearErrors();
        $actual = System::getModel()->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.1', 'Model::hasErrors(); no errors present',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        System::getModel()->setError(Model::ERROR_UNDEFINED,'general failure');
        $actual = System::getModel()->hasErrors();
        $expected = true;
        TestCheck::assertBoolean('A.2', 'Model::hasErrors(); single error present',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        System::getModel()->setError(Model::ERROR_UNDEFINED,'general failure');
        System::getModel()->clearErrors();
        $actual = System::getModel()->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.3', 'Model::hasErrors(); no errors present',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        System::getModel()->setError(Model::ERROR_UNDEFINED,'general failure');
        System::getModel()->setError(Model::ERROR_MISSING_PARAMETER,'missing parameter');
        System::getModel()->setError(Model::ERROR_INVALID_PARAMETER,'invalid parameter');
        $actual = System::getModel()->hasErrors();
        $expected = true;
        TestCheck::assertBoolean('A.4', 'Model::hasErrors(); multiple errors present',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        System::getModel()->setError(Model::ERROR_UNDEFINED,'general failure');
        System::getModel()->setError(Model::ERROR_MISSING_PARAMETER,'missing parameter');
        System::getModel()->setError(Model::ERROR_INVALID_PARAMETER,'invalid parameter');
        System::getModel()->clearErrors();
        $actual = System::getModel()->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.5', 'Model::hasErrors(); no errors present',  $actual, $expected, $results);



        // TEST: Model error functions; get list of errors

        // BEGIN TEST
        System::getModel()->clearErrors();
        $actual = System::getModel()->getErrors();
        $expected = '[]';
        TestCheck::assertArray('B.1', 'Model::getErrors(); no errors present',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        System::getModel()->setError(Model::ERROR_UNDEFINED,'general failure');
        $actual = System::getModel()->getErrors();
        $expected = '[{"code":"undefined","message":"general failure"}]';
        TestCheck::assertArray('B.2', 'Model::getErrors(); single error present',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        System::getModel()->setError(Model::ERROR_UNDEFINED,'general failure');
        System::getModel()->clearErrors();
        $actual = System::getModel()->getErrors();
        $expected = '[]';
        TestCheck::assertArray('B.3', 'Model::getErrors(); no errors present',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        System::getModel()->setError(Model::ERROR_UNDEFINED,'general failure');
        System::getModel()->setError(Model::ERROR_MISSING_PARAMETER,'missing parameter');
        $actual = System::getModel()->getErrors();
        $expected = '[{"code":"undefined","message":"general failure"},{"code":"missing_parameter","message":"missing parameter"}]';
        TestCheck::assertArray('B.4', 'Model::getErrors(); multiple errors present',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        System::getModel()->setError(Model::ERROR_UNDEFINED,'general failure');
        System::getModel()->setError(Model::ERROR_MISSING_PARAMETER,'missing parameter');
        System::getModel()->setError(Model::ERROR_INVALID_PARAMETER,'invalid parameter');
        $actual = System::getModel()->getErrors();
        $expected = '[{"code":"undefined","message":"general failure"},{"code":"missing_parameter","message":"missing parameter"},{"code":"invalid_parameter","message":"invalid parameter"}]';
        TestCheck::assertArray('B.5', 'Model::getErrors(); multiple errors present',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        System::getModel()->setError(Model::ERROR_UNDEFINED,'general failure');
        System::getModel()->setError(Model::ERROR_MISSING_PARAMETER,'missing parameter');
        System::getModel()->setError(Model::ERROR_INVALID_PARAMETER,'invalid parameter');
        System::getModel()->clearErrors();
        $actual = System::getModel()->getErrors();
        $expected = '[]';
        TestCheck::assertArray('B.6', 'Model::getErrors(); no errors present',  $actual, $expected, $results);



        // TEST: Model error functions; set error code

        // BEGIN TEST
        System::getModel()->clearErrors();
        System::getModel()->setError(Model::ERROR_UNDEFINED,'');
        $actual = System::getModel()->getErrors();
        $expected = '[{"code":"undefined","message":""}]';
        TestCheck::assertArray('C.1', 'Model::setError(); set error code',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        System::getModel()->setError(Model::ERROR_NO_DATABASE,'');
        $actual = System::getModel()->getErrors();
        $expected = '[{"code":"no_database","message":""}]';
        TestCheck::assertArray('C.2', 'Model::setError(); set error code',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        System::getModel()->setError(Model::ERROR_CREATE_FAILED,'');
        $actual = System::getModel()->getErrors();
        $expected = '[{"code":"create_failed","message":""}]';
        TestCheck::assertArray('C.3', 'Model::setError(); set error code',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        System::getModel()->setError(Model::ERROR_DELETE_FAILED,'');
        $actual = System::getModel()->getErrors();
        $expected = '[{"code":"delete_failed","message":""}]';
        TestCheck::assertArray('C.4', 'Model::setError(); set error code',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        System::getModel()->setError(Model::ERROR_WRITE_FAILED,'');
        $actual = System::getModel()->getErrors();
        $expected = '[{"code":"write_failed","message":""}]';
        TestCheck::assertArray('C.5', 'Model::setError(); set error code',  $actual, $expected, $results);
    }
}
