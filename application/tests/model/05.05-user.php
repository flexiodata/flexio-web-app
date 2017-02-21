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


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TEST: when creating a user, reject invalid parameters

        // BEGIN TEST
        $model->clearErrors();
        $input_eid = 'xxxxxxxxxxxx';
        $handle1 = \Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'eid' => $input_eid,
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $eid !== $input_eid;
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Model::create(); don\'t allow the eid to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $eid_type = \Model::TYPE_UNDEFINED;
        $handle1 = \Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'eid_type' => $eid_type,
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $info = $model->get($eid);
        $actual = isset($info['eid_type']) && $info['eid_type'] === \Model::TYPE_USER;
        $expected = true;
        TestCheck::assertBoolean('A.2', '\Model::create(); don\'t allow the eid_type to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'xyz' => ''
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $info = $model->get($eid);
        $actual = isset($info['xyz']);
        $expected = false;
        TestCheck::assertBoolean('A.3', '\Model::create(); don\'t allow random parameters to be set',  $actual, $expected, $results);



        // TEST: make sure the password isn't returned in the output

        // BEGIN TEST
        $model->clearErrors();
        $eid_type = \Model::TYPE_UNDEFINED;
        $handle1 = \Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'password' => $handle1
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $info = $model->get($eid);
        $actual = isset($info['password']);
        $expected = false;
        TestCheck::assertBoolean('B.1', '\Model::create(); password shouldn\'t be returned in the output',  $actual, $expected, $results);



        // TEST: when creating a user, make sure it has the essential fields

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $info = $model->get($eid);
        $actual = isset($info['eid']) && isset($info['eid_type']) && isset($info['created']) && isset($info['updated']);
        $expected = true;
        TestCheck::assertBoolean('C.1', '\Model::create(); when creating user, make sure the identifier and date fields are returned',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $model->get($eid);
        $expected = array(
            'eid' => $eid,
            'eid_type' => \Model::TYPE_USER,
            'user_name' => $handle1,
            'verify_code' => '',
            'first_name' => '',
            'last_name' => '',
            'full_name' => '',
            'email' => $handle2,
            'phone' => '',
            'locale_language' => 'en_US',
            'locale_decimal' => '.',
            'locale_thousands' => ',',
            'locale_dateformat' => 'm/d/Y',
            'timezone' => 'UTC',
            'eid_status' => \Model::STATUS_AVAILABLE
        );
        TestCheck::assertInArray('C.2', '\Model::create(); when creating user, make sure essential fields are created',  $actual, $expected, $results);



        // TEST: make sure fields that are specified are properly set

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'eid_status' => \Model::STATUS_PENDING // currently, items are created in active state
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $model->get($eid);
        $expected = array(
            'eid_status' => \Model::STATUS_PENDING
        );
        TestCheck::assertInArray('D.1', '\Model::create(); when creating user, allow eid_status to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $model->get($eid);
        $expected = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        TestCheck::assertInArray('D.2', '\Model::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'first_name' => 'John'
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $model->get($eid);
        $expected = array(
            'first_name' => 'John'
        );
        TestCheck::assertInArray('D.3', '\Model::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'last_name' => 'Williams'
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $model->get($eid);
        $expected = array(
            'last_name' => 'Williams'
        );
        TestCheck::assertInArray('D.4', '\Model::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'full_name' => 'John Williams'
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $model->get($eid);
        $expected = array(
            'first_name' => '', // first/last name parsing happens in api, not model
            'last_name' => '',
            'full_name' => 'John Williams'
        );
        TestCheck::assertInArray('D.5', '\Model::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'first_name' => 'John',
            'last_name' => 'Williams',
            'full_name' => 'Another Name'
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $model->get($eid);
        $expected = array(
            'first_name' => 'John',
            'last_name' => 'Williams',
            'full_name' => 'Another Name'
        );
        TestCheck::assertInArray('D.6', '\Model::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $model->get($eid);
        $expected = array(
            'email' => $handle2
        );
        TestCheck::assertInArray('D.7', '\Model::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'phone' => '123-456-7890'
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $model->get($eid);
        $expected = array(
            'phone' => '123-456-7890'
        );
        TestCheck::assertInArray('D.8', '\Model::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'locale_language' => 'e'
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $model->get($eid);
        $expected = array(
            'locale_language' => 'e'
        );
        TestCheck::assertInArray('D.9', '\Model::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'locale_decimal' => 'd'
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $model->get($eid);
        $expected = array(
            'locale_decimal' => 'd'
        );
        TestCheck::assertInArray('D.10', '\Model::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'locale_thousands' => 't'
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $model->get($eid);
        $expected = array(
            'locale_thousands' => 't'
        );
        TestCheck::assertInArray('D.11', '\Model::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'locale_dateformat' => 'f'
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $model->get($eid);
        $expected = array(
            'locale_dateformat' => 'f',
        );
        TestCheck::assertInArray('D.12', '\Model::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'timezone' => 'CDT'
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $model->get($eid);
        $expected = array(
            'timezone' => 'CDT'
        );
        TestCheck::assertInArray('D.13', '\Model::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);
    }
}
