<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-14
 *
 * @package flexio
 * @subpackage Tests
 */


class Test
{
    public function run(&$results)
    {
        // TEST: Model::delete(); invalid type

        // BEGIN TEST
        System::getModel()->clearErrors();
        $delete_result = System::getModel()->delete('');
        $actual = $delete_result;
        $expected = false;
        TestCheck::assertBoolean('A.1', 'Model::delete(); invalid eid should return false',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $delete_result = System::getModel()->delete('');
        $actual = System::getModel()->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.2', 'Model::delete(); invalid eid should not return an error',  $actual, $expected, $results);



        // TEST: Model::delete(); invalid type

        // BEGIN TEST
        System::getModel()->clearErrors();
        $eid = Eid::generate();
        $delete_result = System::getModel()->delete($eid);
        $actual = $delete_result;
        $expected = false;
        TestCheck::assertBoolean('B.1', 'Model::delete(); invalid eid should return false',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $eid = Eid::generate();
        $delete_result = System::getModel()->delete($eid);
        $actual = System::getModel()->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('B.2', 'Model::delete(); invalid eid should not return an error',  $actual, $expected, $results);



        // TEST: Model::delete(); valid type

        // BEGIN TEST
        System::getModel()->clearErrors();
        $info = array(
        );
        $eid = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $delete_result = System::getModel()->delete($eid);
        $actual = Eid::isValid($eid) === true && $delete_result === true;
        $expected = true;
        TestCheck::assertBoolean('C.1', 'Model::delete(); for object deletion, return true when object is deleted',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $info = array(
        );
        $eid = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $delete_result = System::getModel()->delete($eid);
        $has_errors = System::getModel()->hasErrors();
        $actual = Eid::isValid($eid) === true && $has_errors === false;
        $expected = true;
        TestCheck::assertBoolean('C.2', 'Model::delete(); for object deletion, don\'t flag an error when an object is correctly deleted',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $info = array(
        );
        $eid = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $status_after_add = System::getModel()->getStatus($eid);
        $delete_result = System::getModel()->delete($eid);
        $status_after_delete = System::getModel()->getStatus($eid);
        $actual = Eid::isValid($eid) === true && $status_after_add !== Model::STATUS_DELETED && $status_after_delete === Model::STATUS_DELETED;
        $expected = true;
        TestCheck::assertBoolean('C.3', 'Model::delete(); for object deletion, make sure an object is actually deleted',  $actual, $expected, $results);
    }
}
