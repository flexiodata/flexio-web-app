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


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TEST: set tests with non-eid input

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $actual = $model->set(null, $info);
        $expected = false;
        TestCheck::assertBoolean('A.1', '\Model::set(); return false with invalid input',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $actual = $model->set('', $info);
        $expected = false;
        TestCheck::assertBoolean('A.2', '\Model::set(); return false with invalid input',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $result = $model->set('', $info);
        $actual = $model->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.3', '\Model::set(); don\'t flag an error with invalid input',  $actual, $expected, $results);



        // TEST: set tests with valid eid input, but object doesn't exist

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid = \Flexio\Base\Eid::generate();
        $actual = $model->set($eid, $info);
        $expected = false;
        TestCheck::assertBoolean('B.1', '\Model::set(); return false after trying to set parameters on an object that doesn\'t exist',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid = \Flexio\Base\Eid::generate();
        $result = $model->set($eid, $info);
        $actual = $model->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('B.2', '\Model::set(); don\'t flag an error when trying to set parameters on an object that doesn\'t exist',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid = $model->create(\Model::TYPE_CONNECTION, $info);
        $delete_result = $model->delete($eid);
        $set_result = $model->set($eid, $info);
        $has_errors = $model->hasErrors();
        $actual = \Flexio\Base\Eid::isValid($eid) && $delete_result === true && $set_result === false && $has_errors === false;
        $expected = true;
        TestCheck::assertBoolean('B.3', '\Model::set(); return false and don\'t flag an error when trying to set parameters on an object that\'s been deleted',  $actual, $expected, $results);



        // TEST: set tests on an object that exists

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Flexio\Base\Util::generateHandle();
        $eid = $model->create(\Model::TYPE_CONNECTION, $info);
        $info = array(
        );
        $actual = $model->set($eid, $info);
        $expected = true;
        TestCheck::assertBoolean('C.1', '\Model::set(); return true when setting parameters that affect an eid but don\'t change anything',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid = $model->create(\Model::TYPE_CONNECTION, $info);
        $info = array(
            'name' => $handle
        );
        $actual = $model->set($eid, $info);
        $expected = true;
        TestCheck::assertBoolean('C.2', '\Model::set(); return true when setting parameters that affect an eid but don\'t change anything',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid = $model->create(\Model::TYPE_CONNECTION, $info);
        $info = array(
            'xxx' => $handle
        );
        $actual = $model->set($eid, $info);
        $expected = true;
        TestCheck::assertBoolean('C.3', '\Model::set(); return true when trying to set parameters that don\'t exist',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid = $model->create(\Model::TYPE_CONNECTION, $info);
        $info = array(
            'description' => 'This is a test'
        );
        $actual = $model->set($eid, $info);
        $expected = true;
        TestCheck::assertBoolean('C.4', '\Model::set(); return true when parameters are set successfully',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid = $model->create(\Model::TYPE_CONNECTION, $info);
        $info = array(
            'description' => null
        );
        $result = $model->set($eid, $info);
        $has_errors = $model->hasErrors();
        $actual = $result === false && $has_errors === true;
        $expected = true;
        TestCheck::assertBoolean('C.5', '\Model::set(); return false and flag an error when a parameter is set to a bad value',  $actual, $expected, $results);



        // TEST: \Model::set(); make sure that non-specified properties aren't changed

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
            'name' => 'Test connection'
        );
        $eid = $model->create(\Model::TYPE_CONNECTION, $info);
        $info = array(
            'description' => 'This is a test'
        );
        $result = $model->set($eid, $info);
        $actual = $model->get($eid);
        $expected = array(
            'name' => 'Test connection',
            'description' => 'This is a test'
        );
        TestCheck::assertInArray('D.1', '\Model::set(); for object update, make sure non-specified properties aren\'t changed',  $actual, $expected, $results);
    }
}
