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
        $model = \Flexio\Tests\Util::getModel()->user;


        // TEST: set tests with non-eid input

        // BEGIN TEST
        $actual = '';
        try
        {
            $handle = \Flexio\Base\Util::generateHandle();
            $info = array(
                'user_name' => $handle
            );
            $model->set(null, $info);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', '\Model::set(); throw an error with null input',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'user_name' => $handle
        );
        $actual = $model->set('', $info);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Model::set(); return false with invalid input',  $actual, $expected, $results);



        // TEST: set tests with valid eid input, but object doesn't exist

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'user_name' => $handle
        );
        $eid = \Flexio\Base\Eid::generate();
        $actual = $model->set($eid, $info);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Model::set(); return false when trying to set parameters on an object that doesn\'t exist',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $delete_result = $model->delete($eid);
        $set_result = $model->set($eid, $info);
        $actual = \Flexio\Base\Eid::isValid($eid) && $delete_result === true && $set_result === true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.2', '\Model::set(); return true when setting parameters on an object that\'s been deleted; allowed in the model',  $actual, $expected, $results);



        // TEST: set tests on an object that exists

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $info = array(
        );
        $actual = $model->set($eid, $info);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.1', '\Model::set(); return true when setting parameters that affect an eid but don\'t change anything',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $info = array(
            'user_name' => $handle1
        );
        $actual = $model->set($eid, $info);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.2', '\Model::set(); return true when setting parameters that affect an eid but don\'t change anything',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $info = array(
            'xxx' => 'abc'
        );
        $actual = $model->set($eid, $info);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.3', '\Model::set(); return true when trying to set parameters that don\'t exist',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $info = array(
            'full_name' => 'John Williams'
        );
        $actual = $model->set($eid, $info);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.4', '\Model::set(); return true when parameters are set successfully',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = array();
        try
        {
            $handle1 = \Flexio\Base\Util::generateHandle();
            $handle2 = \Flexio\Tests\Util::createEmailAddress();
            $info = array(
                'user_name' => $handle1,
                'email' => $handle2
            );
            $eid = $model->create($info);
            $info = array(
                'user_name' => null
            );
            $result = $model->set($eid, $info);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::INVALID_PARAMETER
        );
        \Flexio\Tests\Check::assertInArray('C.5', '\Model::set(); throw an exception when a parameter is set to a bad value',  $actual, $expected, $results);



        // TEST: \Model::set(); make sure that non-specified properties aren't changed

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $info = array(
            'company_name' => 'Express Kitchen'
        );
        $result = $model->set($eid, $info);
        $actual = $model->get($eid);
        $expected = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'company_name' => 'Express Kitchen'
        );
        \Flexio\Tests\Check::assertInArray('D.1', '\Model::set(); for object update, make sure non-specified properties aren\'t changed',  $actual, $expected, $results);



        // TEST: \Model::set(); make settable properties are set

        // BEGIN TEST
        $random_eid1 = \Flexio\Base\Eid::generate();
        $random_eid2 = \Flexio\Base\Eid::generate();
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $info = array(
            'eid_status' => \Model::STATUS_TRASH,
            'owned_by' => $random_eid1,
            'created_by' => $random_eid2
        );
        $result = $model->set($eid, $info);
        $actual = $model->get($eid);
        $expected = array(
            'eid_status' => \Model::STATUS_TRASH,
            'owned_by' => $random_eid1,
            'created_by' => $random_eid2
        );
        \Flexio\Tests\Check::assertInArray('E.1', '\Model::set(); make sure properties are updated',  $actual, $expected, $results);
    }
}
