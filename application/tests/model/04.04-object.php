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


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TEST: \Model::set(); invalid type

        // BEGIN TEST
        $model->clearErrors();
        $params = array(
        );
        $set_result = $model->set('', $params);
        $actual = $set_result;
        $expected = false;
        TestCheck::assertBoolean('A.1', '\Model::set(); invalid eid should return false',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $params = array(
        );
        $set_result = $model->set('', $params);
        $actual = $model->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.2', '\Model::set(); invalid eid should not flag an error',  $actual, $expected, $results);



        // TEST: \Model::set(); invalid type

        // BEGIN TEST
        $model->clearErrors();
        $eid = \Flexio\Base\Eid::generate();
        $params = array(
        );
        $set_result = $model->set($eid, $params);
        $actual = $set_result;
        $expected = false;
        TestCheck::assertBoolean('B.1', '\Model::set(); invalid eid should return false',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $eid = \Flexio\Base\Eid::generate();
        $params = array(
        );
        $set_result = $model->set($eid, $params);
        $actual = $model->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('B.2', '\Model::set(); invalid eid should not flag an error',  $actual, $expected, $results);



        // TEST: \Model::set(); valid type

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_OBJECT, $info);
        $params = array(
        );
        $set_result = $model->set($eid, $params);
        $actual = \Flexio\Base\Eid::isValid($eid) === true && $set_result === true;
        $expected = true;
        TestCheck::assertBoolean('C.1', '\Model::set(); for object update, return true when set operation is performed on a valid eid, even if no values change',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_OBJECT, $info);
        $params = array(
        );
        $set_result = $model->set($eid, $params);
        $has_errors = $model->hasErrors();
        $actual = \Flexio\Base\Eid::isValid($eid) === true && $has_errors === false;
        $expected = true;
        TestCheck::assertBoolean('C.2', '\Model::set(); for object update, don\'t flag an error when an object is updated',  $actual, $expected, $results);



        // TEST: \Model::set(); make sure that some values can't be set (i.e., that input parameters are filtered)

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_OBJECT, $info);
        $eid_new = \Flexio\Base\Eid::generate();
        $params = array(
            'eid' => $eid_new,
            'eid_type' => \Model::TYPE_COMMENT
        );
        $set_result = $model->set($eid, $params);
        $actual = \Flexio\Base\Eid::isValid($eid) === true && $set_result === true;
        $expected = true;
        TestCheck::assertBoolean('D.1', '\Model::set(); for object update, simply filter parameters that can\'t be set; don\'t return false',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_OBJECT, $info);
        $eid_new = \Flexio\Base\Eid::generate();
        $params = array(
            'eid' => $eid_new,
            'eid_type' => \Model::TYPE_COMMENT
        );
        $set_result = $model->set($eid, $params);
        $has_errors = $model->hasErrors();
        $actual = \Flexio\Base\Eid::isValid($eid) === true && $has_errors === false;
        $expected = true;
        TestCheck::assertBoolean('D.2', '\Model::set(); for object update, simply filter parameters that can\'t be set; don\'t flag an error',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_OBJECT, $info);
        $eid_new = \Flexio\Base\Eid::generate();
        $params = array(
            'eid' => $eid_new,
            'eid_type' => \Model::TYPE_COMMENT
        );
        $set_result = $model->set($eid, $params);
        $get_result = $model->get($eid);
        $actual = $get_result;
        $expected = array(
            'eid' => $eid,
            'eid_type' => \Model::TYPE_OBJECT
        );
        TestCheck::assertInArray('D.3', '\Model::set(); for object update, make sure filtered parameters aren\'t set',  $actual, $expected, $results);



        // TEST: \Model::set(); make sure that valid parameters are set

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
            'eid_status' => \Model::STATUS_AVAILABLE
        );
        $eid = $model->create(\Model::TYPE_OBJECT, $info);
        $get_result_before_set = $model->get($eid);
        $params = array(
            'eid_status' => \Model::STATUS_PENDING
        );
        $set_result = $model->set($eid, $params);
        $get_result_after_set = $model->get($eid);
        $actual = $get_result_before_set['eid_status'] === \Model::STATUS_AVAILABLE && $get_result_after_set['eid_status'] === \Model::STATUS_PENDING;
        $expected = true;
        TestCheck::assertBoolean('E.1', '\Model::set(); for object update, make sure an object is actually updated',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
            'eid_status' => \Model::STATUS_AVAILABLE
        );
        $eid = $model->create(\Model::TYPE_OBJECT, $info);
        $get_result_before_set = $model->get($eid);
        $params = array(
            'eid_status' => 'ZZZ'
        );
        $set_result = $model->set($eid, $params);
        $actual = $set_result;
        $expected = false;
        TestCheck::assertBoolean('E.2', '\Model::set(); for object update, setting a bad parameter value for an allowed parameter should return false',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
            'eid_status' => \Model::STATUS_PENDING
        );
        $eid = $model->create(\Model::TYPE_OBJECT, $info);
        $get_result_before_set = $model->get($eid);
        $params = array(
            'eid_status' => 'ZZZ'
        );
        $set_result = $model->set($eid, $params);
        $has_errors = $model->hasErrors();
        $actual = $has_errors;
        $expected = true;
        TestCheck::assertBoolean('E.3', '\Model::set(); for object update, setting a bad parameter value for an allowed parameter should flag an error',  $actual, $expected, $results);



        // TEST: \Model::set(); make sure that non-specified properties aren't changed

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
            'eid_status' => \Model::STATUS_AVAILABLE
        );
        $eid = $model->create(\Model::TYPE_OBJECT, $info);
        $get_result_before_set = $model->get($eid);
        $params = array(
            'eid_status' => \Model::STATUS_PENDING
        );
        $set_result = $model->set($eid, $params);
        $get_result_after_set = $model->get($eid);
        $actual = $get_result_after_set;
        $expected = array(
            'eid' => $eid,
            'eid_type' => \Model::TYPE_OBJECT,
            'eid_status' => \Model::STATUS_PENDING
        );
        TestCheck::assertInArray('F.1', '\Model::set(); for object update, make sure non-specified properties aren\'t changed',  $actual, $expected, $results);
    }
}
