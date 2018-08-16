<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-07-05
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
        // FUNCTION: \Flexio\Model\Right::set()


        // SETUP
        $model = \Flexio\Tests\Util::getModel()->right;


        // TEST: non-eid input

        // BEGIN TEST
        $actual = '';
        try
        {
            $handle = \Flexio\Base\Eid::generate();
            $info = array(
                'object_eid' => $handle
            );
            $model->set(null, $info);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Model\Right::set(); throw an error with null input',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Eid::generate();
        $info = array(
            'object_eid' => $handle
        );
        $actual = $model->set('', $info);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Model\Right::set(); return false with invalid input',  $actual, $expected, $results);


        // TEST: valid eid input, but object doesn't exist

        // BEGIN TEST
        $handle = \Flexio\Base\Eid::generate();
        $info = array(
            'object_eid' => $handle
        );
        $eid = \Flexio\Base\Eid::generate();
        $actual = $model->set($eid, $info);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Model\Right::set(); return false after trying to set parameters on an object that doesn\'t exist',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Eid::generate();
        $info = array(
            'object_eid' => $handle
        );
        $eid = $model->create($info);
        $delete_result = $model->delete($eid);
        $set_result = $model->set($eid, $info);
        $actual = \Flexio\Base\Eid::isValid($eid) && $delete_result === true && $set_result === true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.2', '\Flexio\Model\Right::set(); return true when setting parameters on an object that\'s been deleted; allowed in the model',  $actual, $expected, $results);


        // TEST: tests on an object that exists

        // BEGIN TEST
        $handle = \Flexio\Base\Eid::generate();
        $eid = $model->create($info);
        $info = array(
        );
        $actual = $model->set($eid, $info);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.1', '\Flexio\Model\Right::set(); return true when setting parameters that affect an eid but don\'t change anything',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Eid::generate();
        $info = array(
            'object_eid' => $handle
        );
        $eid = $model->create($info);
        $info = array(
            'object_eid' => $handle
        );
        $actual = $model->set($eid, $info);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.2', '\Flexio\Model\Right::set(); return true when setting parameters that affect an eid but don\'t change anything',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Eid::generate();
        $info = array(
            'object_eid' => $handle
        );
        $eid = $model->create($info);
        $info = array(
            'xxx' => $handle
        );
        $actual = $model->set($eid, $info);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.3', '\Flexio\Model\Right::set(); return true when trying to set parameters that don\'t exist',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Eid::generate();
        $handle2 = \Flexio\Base\Eid::generate();
        $info = array(
            'object_eid' => $handle1
        );
        $eid = $model->create($info);
        $info = array(
            'object_eid' => $handle2
        );
        $actual = $model->set($eid, $info);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.4', '\Flexio\Model\Right::set(); return true when parameters are set successfully',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = array();
        try
        {
            $handle = \Flexio\Base\Eid::generate();
            $info = array(
                'object_eid' => $handle
            );
            $eid = $model->create($info);
            $info = array(
                'object_eid' => null
            );
            $result = $model->set($eid, $info);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::INVALID_SYNTAX
        );
        \Flexio\Tests\Check::assertInArray('C.5', '\Flexio\Model\Right::set(); return false and flag an error when a parameter is set to a bad value',  $actual, $expected, $results);


        // TEST: make sure that non-specified properties aren't changed

        // BEGIN TEST
        $handle = \Flexio\Base\Eid::generate();
        $info = array(
            'eid_status' => \Model::STATUS_PENDING
        );
        $eid = $model->create($info);
        $info = array(
            'object_eid' => $handle
        );
        $result = $model->set($eid, $info);
        $actual = $model->get($eid);
        $expected = array(
            'eid_status' => \Model::STATUS_PENDING,
            'object_eid' => $handle
        );
        \Flexio\Tests\Check::assertInArray('D.1', '\Flexio\Model\Right::set(); for object update, make sure non-specified properties aren\'t changed',  $actual, $expected, $results);


        // TEST: make sure settable properties are set

        // BEGIN TEST
        $handle1 = \Flexio\Base\Eid::generate();
        $handle2 = \Flexio\Base\Eid::generate();
        $random_eid1 = \Flexio\Base\Eid::generate();
        $random_eid2 = \Flexio\Base\Eid::generate();
        $info = array(
        );
        $eid = $model->create($info);
        $info = array(
            'eid_status' => \Model::STATUS_PENDING,
            'object_eid' => $handle1,
            'owned_by' => $random_eid1,
            'created_by' => $random_eid2
        );
        $result = $model->set($eid, $info);
        $info = array(
            'object_eid' => $handle2,
        );
        $result = $model->set($eid, $info);
        $actual = $model->get($eid);
        $expected = array(
            'eid_status' => \Model::STATUS_PENDING,
            'object_eid' => $handle2,
            'owned_by' => $random_eid1,
            'created_by' => $random_eid2
        );
        \Flexio\Tests\Check::assertInArray('E.1', '\Flexio\Model\Right::set(); make sure properties are updated',  $actual, $expected, $results);
    }
}
