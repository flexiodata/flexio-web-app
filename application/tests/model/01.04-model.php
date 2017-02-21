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



        // TEST: \Model::setTimezone(); invalid input

        // BEGIN TEST
        $model->clearErrors();
        $result = $model->setTimezone(null);
        $has_errors = $model->hasErrors();
        $actual = $result === false && $has_errors === true;
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Model::setTimezone(); return false and set an error when input is null',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $result = $model->setTimezone(true);
        $has_errors = $model->hasErrors();
        $actual = $result === false && $has_errors === true;
        $expected = true;
        TestCheck::assertBoolean('A.2', '\Model::setTimezone(); return false and set an error when input is a boolean value',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $result = $model->setTimezone('');
        $has_errors = $model->hasErrors();
        $actual = $result === false && $has_errors === true;
        $expected = true;
        TestCheck::assertBoolean('A.3', '\Model::setTimezone(); return false and set an error when input is empty string',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $result = $model->setTimezone('\\');
        $has_errors = $model->hasErrors();
        $actual = $result === false && $has_errors === true;
        $expected = true;
        TestCheck::assertBoolean('A.4', '\Model::setTimezone(); return false and set an error when input is an invalid timezone',  $actual, $expected, $results);



        // TEST: \Model::setTimezone(); valid input

        // BEGIN TEST
        $model->clearErrors();
        $result = $model->setTimezone('UTC');
        $has_errors = $model->hasErrors();
        $actual = $result === true && $has_errors === false;
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Model::setTimezone(); return true when input is valid',  $actual, $expected, $results);
    }
}
