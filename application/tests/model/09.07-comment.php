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


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TEST: set tests with non-eid input

        // BEGIN TEST
        $actual = '';
        try
        {
            $handle = \Flexio\Base\Util::generateHandle();
            $info = array(
                'comment' => $handle
            );
            $model->set(null, $info);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('A.1', '\Model::set(); throw an error with null input',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'comment' => $handle
        );
        $actual = $model->set('', $info);
        $expected = false;
        TestCheck::assertBoolean('A.2', '\Model::set(); return false with invalid input',  $actual, $expected, $results);



        // TEST: set tests with valid eid input, but object doesn't exist

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'comment' => $handle
        );
        $eid = \Flexio\Base\Eid::generate();
        $actual = $model->set($eid, $info);
        $expected = false;
        TestCheck::assertBoolean('B.1', '\Model::set(); return false after trying to set parameters on an object that doesn\'t exist',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'comment' => $handle
        );
        $eid = $model->create(\Model::TYPE_COMMENT, $info);
        $delete_result = $model->delete($eid);
        $set_result = $model->set($eid, $info);
        $actual = \Flexio\Base\Eid::isValid($eid) && $delete_result === true && $set_result === false;
        $expected = true;
        TestCheck::assertBoolean('B.2', '\Model::set(); return false and don\'t throw an exception when trying to set parameters on an object that\'s been deleted',  $actual, $expected, $results);



        // TEST: set tests on an object that exists

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $eid = $model->create(\Model::TYPE_COMMENT, $info);
        $info = array(
        );
        $actual = $model->set($eid, $info);
        $expected = true;
        TestCheck::assertBoolean('C.1', '\Model::set(); return true when setting parameters that affect an eid but don\'t change anything',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'comment' => $handle
        );
        $eid = $model->create(\Model::TYPE_COMMENT, $info);
        $info = array(
            'comment' => $handle
        );
        $actual = $model->set($eid, $info);
        $expected = true;
        TestCheck::assertBoolean('C.2', '\Model::set(); return true when setting parameters that affect an eid but don\'t change anything',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'comment' => $handle
        );
        $eid = $model->create(\Model::TYPE_COMMENT, $info);
        $info = array(
            'xxx' => $handle
        );
        $actual = $model->set($eid, $info);
        $expected = true;
        TestCheck::assertBoolean('C.3', '\Model::set(); return true when trying to set parameters that don\'t exist',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'comment' => $handle
        );
        $eid = $model->create(\Model::TYPE_COMMENT, $info);
        $info = array(
            'comment' => 'This is a test'
        );
        $actual = $model->set($eid, $info);
        $expected = true;
        TestCheck::assertBoolean('C.4', '\Model::set(); return true when parameters are set successfully',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = array();
        try
        {
            $handle = \Flexio\Base\Util::generateHandle();
            $info = array(
                'comment' => $handle
            );
            $eid = $model->create(\Model::TYPE_COMMENT, $info);
            $info = array(
                'comment' => null
            );
            $result = $model->set($eid, $info);
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::INVALID_PARAMETER
        );
        TestCheck::assertInArray('A.2', '\Model::set(); return false and flag an error when a parameter is set to a bad value',  $actual, $expected, $results);



        // TEST: \Model::set(); make sure that non-specified properties aren't changed

        // BEGIN TEST
        $info = array(
            'eid_status' => \Model::STATUS_PENDING
        );
        $eid = $model->create(\Model::TYPE_COMMENT, $info);
        $info = array(
            'comment' => 'Test comment'
        );
        $result = $model->set($eid, $info);
        $actual = $model->get($eid);
        $expected = array(
            'eid_status' => \Model::STATUS_PENDING,
            'comment' => 'Test comment'
        );
        TestCheck::assertInArray('D.1', '\Model::set(); for object update, make sure non-specified properties aren\'t changed',  $actual, $expected, $results);
    }
}
