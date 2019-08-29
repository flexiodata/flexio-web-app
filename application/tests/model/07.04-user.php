<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-07
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
        // FUNCTION: \Flexio\Model\User::delete()


        // SETUP
        $model = \Flexio\Tests\Util::getModel()->user;

        // BEGIN TEST

        $actual = '';
        try
        {
            $info = array(
            );
            $eid = $model->create($info);
            $model->delete($eid);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Model\User::delete(); throw an error when attempting to delete a user with normal delete() method',  $actual, $expected, $results);


/*
        // note: keep following tests in case user deletion via status flag of STATUS_DELETED
        // is reactivated; right now, users can't be deleted by setting the status flag
        // and the only way to delete them is via purging

        // TEST: non-eid input

        // BEGIN TEST
        $actual = '';
        try
        {
            $model->delete(null);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Model\User::delete(); throw an error with null input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = $model->delete('');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Model\User::delete(); return false with invalid input',  $actual, $expected, $results);


        // TEST: valid eid input, but object doesn't exist

        // BEGIN TEST
        $eid = \Flexio\Base\Eid::generate();
        $actual = $model->delete($eid);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Model\User::delete(); return false after trying to delete an object that doesn\'t exist',  $actual, $expected, $results);


        // TEST: valid eid input, and object exists

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $actual = $model->delete($eid);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.1', '\Flexio\Model\User::delete(); return true when deleting an object that exists',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $status_before_deletion = $model->get($eid)['eid_status'];
        $delete_result = $model->delete($eid);
        $status_after_deletion = $model->get($eid)['eid_status'];
        $actual = $delete_result === true && $status_before_deletion !== \Model::STATUS_DELETED  && $status_after_deletion === \Model::STATUS_DELETED;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.2', '\Flexio\Model\User::delete(); when deleting, make sure object is deleted',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $status_before_deletion = $model->get($eid)['eid_status'];
        $first_deletion = $model->delete($eid);
        $second_deletion = $model->delete($eid);
        $status_after_deletion = $model->get($eid)['eid_status'];
        $actual = $status_before_deletion !== \Model::STATUS_DELETED && $status_after_deletion === \Model::STATUS_DELETED && $first_deletion === true && $second_deletion === true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.3', '\Flexio\Model\User::delete(); multiple deletion should succeed',  $actual, $expected, $results);
*/
    }
}
