<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-11
 *
 * @package flexio
 * @subpackage Tests
 */


class Test
{
    public function run(&$results)
    {
        // TEST: set tests with non-eid input

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'comment' => $handle
        );
        $actual = System::getModel()->set(null, $info);
        $expected = false;
        TestCheck::assertBoolean('A.1', 'Model::set(); return false with invalid input',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'comment' => $handle
        );
        $actual = System::getModel()->set('', $info);
        $expected = false;
        TestCheck::assertBoolean('A.2', 'Model::set(); return false with invalid input',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'comment' => $handle
        );
        $result = System::getModel()->set('', $info);
        $actual = System::getModel()->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.3', 'Model::set(); don\'t flag an error with invalid input',  $actual, $expected, $results);



        // TEST: set tests with valid eid input, but object doesn't exist

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'comment' => $handle
        );
        $eid = Eid::generate();
        $actual = System::getModel()->set($eid, $info);
        $expected = false;
        TestCheck::assertBoolean('B.1', 'Model::set(); return false after trying to set parameters on an object that doesn\'t exist',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'comment' => $handle
        );
        $eid = Eid::generate();
        $result = System::getModel()->set($eid, $info);
        $actual = System::getModel()->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('B.2', 'Model::set(); don\'t flag an error when trying to set parameters on an object that doesn\'t exist',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'comment' => $handle
        );
        $eid = System::getModel()->create(Model::TYPE_COMMENT, $info);
        $delete_result = System::getModel()->delete($eid);
        $set_result = System::getModel()->set($eid, $info);
        $has_errors = System::getModel()->hasErrors();
        $actual = Eid::isValid($eid) && $delete_result === true && $set_result === false && $has_errors === false;
        $expected = true;
        TestCheck::assertBoolean('B.3', 'Model::set(); return false and don\'t flag an error when trying to set parameters on an object that\'s been deleted',  $actual, $expected, $results);



        // TEST: set tests on an object that exists

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $eid = System::getModel()->create(Model::TYPE_COMMENT, $info);
        $info = array(
        );
        $actual = System::getModel()->set($eid, $info);
        $expected = true;
        TestCheck::assertBoolean('C.1', 'Model::set(); return true when setting parameters that affect an eid but don\'t change anything',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'comment' => $handle
        );
        $eid = System::getModel()->create(Model::TYPE_COMMENT, $info);
        $info = array(
            'comment' => $handle
        );
        $actual = System::getModel()->set($eid, $info);
        $expected = true;
        TestCheck::assertBoolean('C.2', 'Model::set(); return true when setting parameters that affect an eid but don\'t change anything',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'comment' => $handle
        );
        $eid = System::getModel()->create(Model::TYPE_COMMENT, $info);
        $info = array(
            'xxx' => $handle
        );
        $actual = System::getModel()->set($eid, $info);
        $expected = true;
        TestCheck::assertBoolean('C.3', 'Model::set(); return true when trying to set parameters that don\'t exist',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'comment' => $handle
        );
        $eid = System::getModel()->create(Model::TYPE_COMMENT, $info);
        $info = array(
            'comment' => 'This is a test'
        );
        $actual = System::getModel()->set($eid, $info);
        $expected = true;
        TestCheck::assertBoolean('C.4', 'Model::set(); return true when parameters are set successfully',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'comment' => $handle
        );
        $eid = System::getModel()->create(Model::TYPE_COMMENT, $info);
        $info = array(
            'comment' => null
        );
        $result = System::getModel()->set($eid, $info);
        $has_errors = System::getModel()->hasErrors();
        $actual = $result === false && $has_errors === true;
        $expected = true;
        TestCheck::assertBoolean('C.5', 'Model::set(); return false and flag an error when a parameter is set to a bad value',  $actual, $expected, $results);



        // TEST: Model::set(); make sure that non-specified properties aren't changed

        // BEGIN TEST
        System::getModel()->clearErrors();
        $info = array(
            'eid_status' => Model::STATUS_PENDING
        );
        $eid = System::getModel()->create(Model::TYPE_COMMENT, $info);
        $info = array(
            'comment' => 'Test comment'
        );
        $result = System::getModel()->set($eid, $info);
        $actual = System::getModel()->get($eid);
        $expected = array(
            'eid_status' => Model::STATUS_PENDING,
            'comment' => 'Test comment'
        );
        TestCheck::assertInArray('D.1', 'Model::set(); for object update, make sure non-specified properties aren\'t changed',  $actual, $expected, $results);
    }
}
