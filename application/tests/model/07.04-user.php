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


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = \Flexio\Tests\Util::getModel()->user;


        // TEST: \Flexio\Model\User::create(); when creating a user, reject invalid parameters

        // BEGIN TEST
        $input_eid = 'xxxxxxxxxxxx';
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'eid' => $input_eid,
            'username' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $actual = $eid !== $input_eid;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Model\User::create(); don\'t allow the eid to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $eid_type = \Model::TYPE_UNDEFINED;
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'eid_type' => $eid_type,
            'username' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $info = $model->get($eid);
        $actual = isset($info['eid_type']) && $info['eid_type'] === \Model::TYPE_USER;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Model\User::create(); don\'t allow the eid_type to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2,
            'xyz' => ''
        );
        $eid = $model->create($info);
        $info = $model->get($eid);
        $actual = isset($info['xyz']);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Model\User::create(); don\'t allow random parameters to be set',  $actual, $expected, $results);



        // TEST: \Flexio\Model\User::create(); make sure the password isn't returned in the output

        // BEGIN TEST
        $eid_type = \Model::TYPE_UNDEFINED;
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $handle3 = \Flexio\Base\Password::generate();
        $info = array(
            'username' => $handle1,
            'email' => $handle2,
            'password' => $handle3
        );
        $eid = $model->create($info);
        $info = $model->get($eid);
        $actual = isset($info['password']);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Model\User::create(); password shouldn\'t be returned in the output',  $actual, $expected, $results);



        // TEST: \Flexio\Model\User::create(); when creating a user, make sure it has the essential fields

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $info = $model->get($eid);
        $actual = isset($info['eid']) && isset($info['eid_type']) && isset($info['created']) && isset($info['updated']);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.1', '\Flexio\Model\User::create(); when creating user, make sure the identifier and date fields are returned',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'eid' => $eid,
            'eid_type' => \Model::TYPE_USER,
            'eid_status' => \Model::STATUS_AVAILABLE,
            'username' => $handle1,
            'first_name' => '',
            'last_name' => '',
            'full_name' => '',
            'email' => $handle2,
            'phone' => '',
            'location_city' => '',
            'location_state' => '',
            'location_country' => '',
            'company_name' => '',
            'company_url' => '',
            'locale_language' => 'en_US',
            'locale_decimal' => '.',
            'locale_thousands' => ',',
            'locale_dateformat' => 'm/d/Y',
            'timezone' => 'UTC',
            'verify_code' => '',
            'config' => '{}',
            'owned_by' => '',
            'created_by' => ''
        );
        \Flexio\Tests\Check::assertInArray('C.2', '\Flexio\Model\User::create(); when creating user, make sure essential fields are created',  $actual, $expected, $results);



        // TEST: \Flexio\Model\User::create(); make sure fields that are specified are properly set

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2,
            'eid_status' => \Model::STATUS_PENDING // currently, items are created in active state
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'eid_status' => \Model::STATUS_PENDING
        );
        \Flexio\Tests\Check::assertInArray('D.1', '\Flexio\Model\User::create(); when creating user, allow eid_status to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'username' => $handle1,
            'email' => $handle2
        );
        \Flexio\Tests\Check::assertInArray('D.2', '\Flexio\Model\User::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2,
            'first_name' => 'John'
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'first_name' => 'John'
        );
        \Flexio\Tests\Check::assertInArray('D.3', '\Flexio\Model\User::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2,
            'last_name' => 'Williams'
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'last_name' => 'Williams'
        );
        \Flexio\Tests\Check::assertInArray('D.4', '\Flexio\Model\User::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2,
            'full_name' => 'John Williams'
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'first_name' => '', // first/last name parsing happens in api, not model
            'last_name' => '',
            'full_name' => 'John Williams'
        );
        \Flexio\Tests\Check::assertInArray('D.5', '\Flexio\Model\User::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2,
            'first_name' => 'John',
            'last_name' => 'Williams',
            'full_name' => 'Another Name'
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'first_name' => 'John',
            'last_name' => 'Williams',
            'full_name' => 'Another Name'
        );
        \Flexio\Tests\Check::assertInArray('D.6', '\Flexio\Model\User::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'email' => $handle2
        );
        \Flexio\Tests\Check::assertInArray('D.7', '\Flexio\Model\User::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2,
            'phone' => '123-456-7890'
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'phone' => '123-456-7890'
        );
        \Flexio\Tests\Check::assertInArray('D.8', '\Flexio\Model\User::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2,
            'locale_language' => 'e'
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'locale_language' => 'e'
        );
        \Flexio\Tests\Check::assertInArray('D.9', '\Flexio\Model\User::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2,
            'locale_decimal' => 'd'
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'locale_decimal' => 'd'
        );
        \Flexio\Tests\Check::assertInArray('D.10', '\Flexio\Model\User::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2,
            'locale_thousands' => 't'
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'locale_thousands' => 't'
        );
        \Flexio\Tests\Check::assertInArray('D.11', '\Flexio\Model\User::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2,
            'locale_dateformat' => 'f'
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'locale_dateformat' => 'f',
        );
        \Flexio\Tests\Check::assertInArray('D.12', '\Flexio\Model\User::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2,
            'timezone' => 'CDT'
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'timezone' => 'CDT'
        );
        \Flexio\Tests\Check::assertInArray('D.13', '\Flexio\Model\User::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'owned_by' => '',
            'created_by' => ''
        );
        \Flexio\Tests\Check::assertInArray('D.14', '\Flexio\Model\User::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $random_eid1 = \Flexio\Base\Eid::generate();
        $random_eid2 = \Flexio\Base\Eid::generate();
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2,
            'owned_by' => $random_eid1,
            'created_by' => $random_eid2
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'owned_by' => $random_eid1,
            'created_by' => $random_eid2
        );
        \Flexio\Tests\Check::assertInArray('D.14', '\Flexio\Model\User::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);
    }
}
