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
        // TEST: delete tests with non-eid input

        // BEGIN TEST
        System::getModel()->clearErrors();
        $actual = System::getModel()->delete(null);
        $expected = false;
        TestCheck::assertBoolean('A.1', 'Model::delete(); return false with invalid input',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $actual = System::getModel()->delete('');
        $expected = false;
        TestCheck::assertBoolean('A.2', 'Model::delete(); return false with invalid input',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $result = System::getModel()->delete('');
        $actual = System::getModel()->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.3', 'Model::delete(); don\'t flag an error with invalid input',  $actual, $expected, $results);



        // TEST: delete tests with valid eid input, but object doesn't exist

        // BEGIN TEST
        System::getModel()->clearErrors();
        $eid = Eid::generate();
        $actual = System::getModel()->delete($eid);
        $expected = false;
        TestCheck::assertBoolean('B.1', 'Model::delete(); return false after trying to delete an object that doesn\'t exist',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $eid = Eid::generate();
        $result = System::getModel()->delete($eid);
        $actual = System::getModel()->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('B.2', 'Model::delete(); don\'t flag an error when trying to delete an object that doesn\'t exist',  $actual, $expected, $results);



        // TEST: delete tests with valid eid input, and object exists

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid = System::getModel()->create(Model::TYPE_PROJECT, $info);
        $actual = System::getModel()->delete($eid);
        $expected = true;
        TestCheck::assertBoolean('C.1', 'Model::delete(); return true when deleting an object that exists',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid = System::getModel()->create(Model::TYPE_PROJECT, $info);
        $result = System::getModel()->delete($eid);
        $actual = System::getModel()->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('C.2', 'Model::delete(); there shouldn\'t be any errors when deleting an object that exists',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid = System::getModel()->create(Model::TYPE_PROJECT, $info);
        $status_before_deletion = System::getModel()->getStatus($eid);
        $delete_result = System::getModel()->delete($eid);
        $status_after_deletion = System::getModel()->getStatus($eid);
        $actual = $delete_result === true && $status_before_deletion !== Model::STATUS_DELETED && $status_after_deletion === Model::STATUS_DELETED;
        $expected = true;
        TestCheck::assertBoolean('C.3', 'Model::delete(); when deleting, make sure object is deleted',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid = System::getModel()->create(Model::TYPE_PROJECT, $info);
        $status_before_deletion = System::getModel()->getStatus($eid);
        $first_deletion = System::getModel()->delete($eid);
        $second_deletion = System::getModel()->delete($eid);
        $status_after_deletion = System::getModel()->getStatus($eid);
        $has_errors = System::getModel()->hasErrors();
        $actual = $status_before_deletion !== Model::STATUS_DELETED && $status_after_deletion === Model::STATUS_DELETED && $first_deletion === true && $second_deletion === false && $has_errors === false;
        $expected = true;
        TestCheck::assertBoolean('C.4', 'Model::delete(); multiple deletion should succeed',  $actual, $expected, $results);
    }
}
