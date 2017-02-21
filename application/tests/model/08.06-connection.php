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



        // TEST: delete tests with non-eid input

        // BEGIN TEST
        $model->clearErrors();
        $actual = $model->delete(null);
        $expected = false;
        TestCheck::assertBoolean('A.1', '\Model::delete(); return false with invalid input',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $actual = $model->delete('');
        $expected = false;
        TestCheck::assertBoolean('A.2', '\Model::delete(); return false with invalid input',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $result = $model->delete('');
        $actual = $model->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.3', '\Model::delete(); don\'t flag an error with invalid input',  $actual, $expected, $results);



        // TEST: delete tests with valid eid input, but object doesn't exist

        // BEGIN TEST
        $model->clearErrors();
        $eid = \Eid::generate();
        $actual = $model->delete($eid);
        $expected = false;
        TestCheck::assertBoolean('B.1', '\Model::delete(); return false after trying to delete an object that doesn\'t exist',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $eid = \Eid::generate();
        $result = $model->delete($eid);
        $actual = $model->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('B.2', '\Model::delete(); don\'t flag an error when trying to delete an object that doesn\'t exist',  $actual, $expected, $results);



        // TEST: delete tests with valid eid input, and object exists

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid = $model->create(\Model::TYPE_CONNECTION, $info);
        $actual = $model->delete($eid);
        $expected = true;
        TestCheck::assertBoolean('C.1', '\Model::delete(); return true when deleting an object that exists',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid = $model->create(\Model::TYPE_CONNECTION, $info);
        $result = $model->delete($eid);
        $actual = $model->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('C.2', '\Model::delete(); there shouldn\'t be any errors when deleting an object that exists',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid = $model->create(\Model::TYPE_CONNECTION, $info);
        $status_before_deletion = $model->getStatus($eid);
        $delete_result = $model->delete($eid);
        $status_after_deletion = $model->getStatus($eid);
        $actual = $delete_result === true && $status_before_deletion !== \Model::STATUS_DELETED && $status_after_deletion === \Model::STATUS_DELETED;
        $expected = true;
        TestCheck::assertBoolean('C.3', '\Model::delete(); when deleting, make sure object is deleted',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid = $model->create(\Model::TYPE_CONNECTION, $info);
        $status_before_deletion = $model->getStatus($eid);
        $first_deletion = $model->delete($eid);
        $second_deletion = $model->delete($eid);
        $status_after_deletion = $model->getStatus($eid);
        $has_errors = $model->hasErrors();
        $actual = $status_before_deletion !== \Model::STATUS_DELETED && $status_after_deletion === \Model::STATUS_DELETED && $first_deletion === true && $second_deletion === false && $has_errors === false;
        $expected = true;
        TestCheck::assertBoolean('C.4', '\Model::delete(); multiple deletion should succeed',  $actual, $expected, $results);
    }
}
