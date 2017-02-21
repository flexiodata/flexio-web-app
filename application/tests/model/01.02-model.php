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
        // SETUP
        $model = TestUtil::getModel();



        // TEST: Model error functions; check for error existence

        // BEGIN TEST
        $model->clearErrors();
        $actual = $model->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.1', '\Model::hasErrors(); no errors present',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $model->setError(\Model::ERROR_UNDEFINED,'general failure');
        $actual = $model->hasErrors();
        $expected = true;
        TestCheck::assertBoolean('A.2', '\Model::hasErrors(); single error present',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $model->setError(\Model::ERROR_UNDEFINED,'general failure');
        $model->clearErrors();
        $actual = $model->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.3', '\Model::hasErrors(); no errors present',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $model->setError(\Model::ERROR_UNDEFINED,'general failure');
        $model->setError(\Model::ERROR_MISSING_PARAMETER,'missing parameter');
        $model->setError(\Model::ERROR_INVALID_PARAMETER,'invalid parameter');
        $actual = $model->hasErrors();
        $expected = true;
        TestCheck::assertBoolean('A.4', '\Model::hasErrors(); multiple errors present',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $model->setError(\Model::ERROR_UNDEFINED,'general failure');
        $model->setError(\Model::ERROR_MISSING_PARAMETER,'missing parameter');
        $model->setError(\Model::ERROR_INVALID_PARAMETER,'invalid parameter');
        $model->clearErrors();
        $actual = $model->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.5', '\Model::hasErrors(); no errors present',  $actual, $expected, $results);



        // TEST: Model error functions; get list of errors

        // BEGIN TEST
        $model->clearErrors();
        $actual = $model->getErrors();
        $expected = '[]';
        TestCheck::assertArray('B.1', '\Model::getErrors(); no errors present',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $model->setError(\Model::ERROR_UNDEFINED,'general failure');
        $actual = $model->getErrors();
        $expected = '[{"code":"undefined","message":"general failure"}]';
        TestCheck::assertArray('B.2', '\Model::getErrors(); single error present',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $model->setError(\Model::ERROR_UNDEFINED,'general failure');
        $model->clearErrors();
        $actual = $model->getErrors();
        $expected = '[]';
        TestCheck::assertArray('B.3', '\Model::getErrors(); no errors present',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $model->setError(\Model::ERROR_UNDEFINED,'general failure');
        $model->setError(\Model::ERROR_MISSING_PARAMETER,'missing parameter');
        $actual = $model->getErrors();
        $expected = '[{"code":"undefined","message":"general failure"},{"code":"missing_parameter","message":"missing parameter"}]';
        TestCheck::assertArray('B.4', '\Model::getErrors(); multiple errors present',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $model->setError(\Model::ERROR_UNDEFINED,'general failure');
        $model->setError(\Model::ERROR_MISSING_PARAMETER,'missing parameter');
        $model->setError(\Model::ERROR_INVALID_PARAMETER,'invalid parameter');
        $actual = $model->getErrors();
        $expected = '[{"code":"undefined","message":"general failure"},{"code":"missing_parameter","message":"missing parameter"},{"code":"invalid_parameter","message":"invalid parameter"}]';
        TestCheck::assertArray('B.5', '\Model::getErrors(); multiple errors present',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $model->setError(\Model::ERROR_UNDEFINED,'general failure');
        $model->setError(\Model::ERROR_MISSING_PARAMETER,'missing parameter');
        $model->setError(\Model::ERROR_INVALID_PARAMETER,'invalid parameter');
        $model->clearErrors();
        $actual = $model->getErrors();
        $expected = '[]';
        TestCheck::assertArray('B.6', '\Model::getErrors(); no errors present',  $actual, $expected, $results);



        // TEST: Model error functions; set error code

        // BEGIN TEST
        $model->clearErrors();
        $model->setError(\Model::ERROR_UNDEFINED,'');
        $actual = $model->getErrors();
        $expected = '[{"code":"undefined","message":""}]';
        TestCheck::assertArray('C.1', '\Model::setError(); set error code',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $model->setError(\Model::ERROR_NO_DATABASE,'');
        $actual = $model->getErrors();
        $expected = '[{"code":"no_database","message":""}]';
        TestCheck::assertArray('C.2', '\Model::setError(); set error code',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $model->setError(\Model::ERROR_CREATE_FAILED,'');
        $actual = $model->getErrors();
        $expected = '[{"code":"create_failed","message":""}]';
        TestCheck::assertArray('C.3', '\Model::setError(); set error code',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $model->setError(\Model::ERROR_DELETE_FAILED,'');
        $actual = $model->getErrors();
        $expected = '[{"code":"delete_failed","message":""}]';
        TestCheck::assertArray('C.4', '\Model::setError(); set error code',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $model->setError(\Model::ERROR_WRITE_FAILED,'');
        $actual = $model->getErrors();
        $expected = '[{"code":"write_failed","message":""}]';
        TestCheck::assertArray('C.5', '\Model::setError(); set error code',  $actual, $expected, $results);
    }
}
