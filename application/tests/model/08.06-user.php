<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
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
        // FUNCTION: \Flexio\Model\User::set()


        // SETUP
        $model = \Flexio\Tests\Util::getModel()->user;


        // TEST: non-eid input

        // BEGIN TEST
        $actual = '';
        try
        {
            $handle = \Flexio\Base\Util::generateHandle();
            $info = array(
                'username' => $handle
            );
            $model->set(null, $info);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Model\User::set(); throw an error with null input',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'username' => $handle
        );
        $actual = $model->set('', $info);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Model\User::set(); return false with invalid input',  $actual, $expected, $results);


        // TEST: valid eid input, but object doesn't exist

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'username' => $handle
        );
        $eid = \Flexio\Base\Eid::generate();
        $actual = $model->set($eid, $info);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Model\User::set(); return false when trying to set parameters on an object that doesn\'t exist',  $actual, $expected, $results);

        $actual = '';
        try
        {
            $handle1 = \Flexio\Base\Util::generateHandle();
            $handle2 = \Flexio\Tests\Util::createEmailAddress();
            $info = array(
                'username' => $handle1,
                'email' => $handle2
            );
            $eid = $model->create($info);
            $delete_result = $model->delete($eid);
            $set_result = $model->set($eid, $info);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;

        \Flexio\Tests\Check::assertString('B.2', '\Flexio\Model\User::set(); don\'t allow user to be deleted via status flag',  $actual, $expected, $results);

/*
        // note: keep following test in case user deletion via status flag of STATUS_DELETED
        // is reactivated; right now, users can't be deleted by setting the status flag
        // and the only way to delete them is via purging

        // // BEGIN TEST
        // $handle1 = \Flexio\Base\Util::generateHandle();
        // $handle2 = \Flexio\Tests\Util::createEmailAddress();
        // $info = array(
        //     'username' => $handle1,
        //     'email' => $handle2
        // );
        // $eid = $model->create($info);
        // $delete_result = $model->delete($eid);
        // $set_result = $model->set($eid, $info);
        // $actual = \Flexio\Base\Eid::isValid($eid) && $delete_result === true && $set_result === true;
        // $expected = true;
        // \Flexio\Tests\Check::assertBoolean('B.2', '\Flexio\Model\User::set(); return true when setting parameters on an object that\'s been deleted; allowed in the model',  $actual, $expected, $results);
*/


        // TEST: object that exists

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $info = array(
        );
        $actual = $model->set($eid, $info);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.1', '\Flexio\Model\User::set(); return true when setting parameters that affect an eid but don\'t change anything',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $info = array(
            'username' => $handle1
        );
        $actual = $model->set($eid, $info);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.2', '\Flexio\Model\User::set(); return true when setting parameters that affect an eid but don\'t change anything',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $info = array(
            'xxx' => 'abc'
        );
        $actual = $model->set($eid, $info);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.3', '\Flexio\Model\User::set(); return true when trying to set parameters that don\'t exist',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $info = array(
            'full_name' => 'John Williams'
        );
        $actual = $model->set($eid, $info);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.4', '\Flexio\Model\User::set(); return true when parameters are set successfully',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = array();
        try
        {
            $handle1 = \Flexio\Base\Util::generateHandle();
            $handle2 = \Flexio\Tests\Util::createEmailAddress();
            $info = array(
                'username' => $handle1,
                'email' => $handle2
            );
            $eid = $model->create($info);
            $info = array(
                'username' => null
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
        \Flexio\Tests\Check::assertInArray('C.5', '\Flexio\Model\User::set(); throw an exception when a parameter is set to a bad value',  $actual, $expected, $results);


        // TEST: make sure that non-specified properties aren't changed

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $info = array(
            'company_name' => 'Express Kitchen'
        );
        $result = $model->set($eid, $info);
        $actual = $model->get($eid);
        $expected = array(
            'username' => $handle1,
            'email' => $handle2,
            'company_name' => 'Express Kitchen'
        );
        \Flexio\Tests\Check::assertInArray('D.1', '\Flexio\Model\User::set(); for object update, make sure non-specified properties aren\'t changed',  $actual, $expected, $results);


        // TEST: make sure settable properties are set

        // BEGIN TEST
        $random_eid1 = \Flexio\Base\Eid::generate();
        $random_eid2 = \Flexio\Base\Eid::generate();
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $info = array(
            'eid_status' => \Model::STATUS_AVAILABLE,
            'owned_by' => $random_eid1,
            'created_by' => $random_eid2
        );
        $result = $model->set($eid, $info);
        $actual = $model->get($eid);
        $expected = array(
            'eid_status' => \Model::STATUS_AVAILABLE,
            'owned_by' => $eid,
            'created_by' => $eid
        );
        \Flexio\Tests\Check::assertInArray('E.1', '\Flexio\Model\User::set(); make sure properties are updated; don\'t allow owned_by/created_by to be changed for users',  $actual, $expected, $results);
    }
}
