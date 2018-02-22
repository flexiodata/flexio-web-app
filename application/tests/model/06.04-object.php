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


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TEST: \Model::set(); invalid type

        // BEGIN TEST
        $params = array(
        );
        $set_result = $model->set('', $params);
        $actual = $set_result;
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Model::set(); invalid eid should return false',  $actual, $expected, $results);



        // TEST: \Model::set(); invalid type

        // BEGIN TEST
        $eid = \Flexio\Base\Eid::generate();
        $params = array(
        );
        $set_result = $model->set($eid, $params);
        $actual = $set_result;
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Model::set(); invalid eid should return false',  $actual, $expected, $results);



        // TEST: \Model::set(); valid type

        // BEGIN TEST
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_OBJECT, $info);
        $params = array(
        );
        $set_result = $model->set($eid, $params);
        $actual = \Flexio\Base\Eid::isValid($eid) === true && $set_result === true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.1', '\Model::set(); for object update, return true when set operation is performed on a valid eid, even if no values change',  $actual, $expected, $results);



        // TEST: \Model::set(); make sure that some values can't be set (i.e., that input parameters are filtered)

        // BEGIN TEST
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
        \Flexio\Tests\Check::assertBoolean('D.1', '\Model::set(); for object update, simply filter parameters that can\'t be set; don\'t return false',  $actual, $expected, $results);

        // BEGIN TEST
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
        \Flexio\Tests\Check::assertInArray('D.2', '\Model::set(); for object update, make sure filtered parameters aren\'t set',  $actual, $expected, $results);



        // TEST: \Model::set(); make sure that valid parameters are set

        // BEGIN TEST
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
        \Flexio\Tests\Check::assertBoolean('E.1', '\Model::set(); for object update, make sure an object is actually updated',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = array();
        try
        {
            $info = array(
                'eid_status' => \Model::STATUS_AVAILABLE
            );
            $eid = $model->create(\Model::TYPE_OBJECT, $info);
            $params = array(
                'eid_status' => 'ZZZ'
            );
            $set_result = $model->set($eid, $params);
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::INVALID_PARAMETER
        );
        \Flexio\Tests\Check::assertInArray('E.2', '\Model::create(); bad parameter should throw an exception',  $actual, $expected, $results);



        // TEST: \Model::set(); make sure that non-specified properties aren't changed

        // BEGIN TEST
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
        \Flexio\Tests\Check::assertInArray('F.1', '\Model::set(); for object update, make sure non-specified properties aren\'t changed',  $actual, $expected, $results);
    }
}
