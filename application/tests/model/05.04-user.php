<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-07
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



        // TEST: \Model::create(); when creating a user, make sure the required parameters are set

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Flexio\System\Util::generateHandle();
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $has_errors = $model->hasErrors();
        $actual = $eid === false && $has_errors === true;
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Model::create(); return false when user_name is not specified; flag an error',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Flexio\System\Util::generateHandle();
        $info = array(
            'password' => $handle
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $has_errors = $model->hasErrors();
        $actual = $eid === false && $has_errors === true;
        $expected = true;
        TestCheck::assertBoolean('A.2', '\Model::create(); return false when user_name is not specified; flag an error',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Flexio\System\Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => null,
            'email' => $handle2,
            'password' => $handle1
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $has_errors = $model->hasErrors();
        $actual = $eid === false && $has_errors === true;
        $expected = true;
        TestCheck::assertBoolean('A.3', '\Model::create(); return false when user_name is invalid; flag an error',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Flexio\System\Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => null,
            'password' => $handle1
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $has_errors = $model->hasErrors();
        $actual = $eid === false && $has_errors === true;
        $expected = true;
        TestCheck::assertBoolean('A.4', '\Model::create(); return false when user_name is invalid; flag an error',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Flexio\System\Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => '',
            'email' => $handle2,
            'password' => $handle1
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $has_errors = $model->hasErrors();
        $actual = $eid === false && $has_errors === true;
        $expected = true;
        TestCheck::assertBoolean('A.5', '\Model::create(); return false when user_name is invalid; flag an error',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Flexio\System\Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => '',
            'password' => $handle1
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $has_errors = $model->hasErrors();
        $actual = $eid === false && $has_errors === true;
        $expected = true;
        TestCheck::assertBoolean('A.6', '\Model::create(); return false when user_name is invalid; flag an error',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Flexio\System\Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = \Flexio\System\Eid::isValid($eid);
        $expected = true;
        TestCheck::assertBoolean('A.7', '\Model::create(); make sure that a valid eid is returned on success',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Flexio\System\Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $model->get($eid);
        $expected = array(
            'eid' => $eid,
            'user_name' => $handle1,
            'email' => $handle2
        );
        TestCheck::assertInArray('A.8', '\Model::create(); make sure that eid and user_name are set when a user is created',  $actual, $expected, $results);
    }
}
