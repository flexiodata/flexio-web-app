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
        // TEST: when creating a user, reject invalid parameters

        // BEGIN TEST
        $input_eid = 'xxxxxxxxxxxx';
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'eid' => $input_eid,
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_USER, $info);
        $actual = $eid !== $input_eid;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Model::create(); don\'t allow the eid to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $eid_type = \Model::TYPE_UNDEFINED;
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'eid_type' => $eid_type,
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_USER, $info);
        $info = \Flexio\Tests\Util::getModel()->get($eid);
        $actual = isset($info['eid_type']) && $info['eid_type'] === \Model::TYPE_USER;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Model::create(); don\'t allow the eid_type to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'xyz' => ''
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_USER, $info);
        $info = \Flexio\Tests\Util::getModel()->get($eid);
        $actual = isset($info['xyz']);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Model::create(); don\'t allow random parameters to be set',  $actual, $expected, $results);



        // TEST: make sure the password isn't returned in the output

        // BEGIN TEST
        $eid_type = \Model::TYPE_UNDEFINED;
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'password' => $handle1
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_USER, $info);
        $info = \Flexio\Tests\Util::getModel()->get($eid);
        $actual = isset($info['password']);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Model::create(); password shouldn\'t be returned in the output',  $actual, $expected, $results);



        // TEST: when creating a user, make sure it has the essential fields

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_USER, $info);
        $info = \Flexio\Tests\Util::getModel()->get($eid);
        $actual = isset($info['eid']) && isset($info['eid_type']) && isset($info['created']) && isset($info['updated']);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.1', '\Model::create(); when creating user, make sure the identifier and date fields are returned',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_USER, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
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
        \Flexio\Tests\Check::assertInArray('C.2', '\Model::create(); when creating user, make sure essential fields are created',  $actual, $expected, $results);



        // TEST: make sure fields that are specified are properly set

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'eid_status' => \Model::STATUS_PENDING // currently, items are created in active state
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_USER, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
        $expected = array(
            'eid_status' => \Model::STATUS_PENDING
        );
        \Flexio\Tests\Check::assertInArray('D.1', '\Model::create(); when creating user, allow eid_status to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_USER, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
        $expected = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        \Flexio\Tests\Check::assertInArray('D.2', '\Model::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'first_name' => 'John'
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_USER, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
        $expected = array(
            'first_name' => 'John'
        );
        \Flexio\Tests\Check::assertInArray('D.3', '\Model::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'last_name' => 'Williams'
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_USER, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
        $expected = array(
            'last_name' => 'Williams'
        );
        \Flexio\Tests\Check::assertInArray('D.4', '\Model::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'full_name' => 'John Williams'
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_USER, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
        $expected = array(
            'first_name' => '', // first/last name parsing happens in api, not model
            'last_name' => '',
            'full_name' => 'John Williams'
        );
        \Flexio\Tests\Check::assertInArray('D.5', '\Model::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'first_name' => 'John',
            'last_name' => 'Williams',
            'full_name' => 'Another Name'
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_USER, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
        $expected = array(
            'first_name' => 'John',
            'last_name' => 'Williams',
            'full_name' => 'Another Name'
        );
        \Flexio\Tests\Check::assertInArray('D.6', '\Model::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_USER, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
        $expected = array(
            'email' => $handle2
        );
        \Flexio\Tests\Check::assertInArray('D.7', '\Model::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'phone' => '123-456-7890'
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_USER, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
        $expected = array(
            'phone' => '123-456-7890'
        );
        \Flexio\Tests\Check::assertInArray('D.8', '\Model::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'locale_language' => 'e'
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_USER, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
        $expected = array(
            'locale_language' => 'e'
        );
        \Flexio\Tests\Check::assertInArray('D.9', '\Model::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'locale_decimal' => 'd'
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_USER, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
        $expected = array(
            'locale_decimal' => 'd'
        );
        \Flexio\Tests\Check::assertInArray('D.10', '\Model::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'locale_thousands' => 't'
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_USER, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
        $expected = array(
            'locale_thousands' => 't'
        );
        \Flexio\Tests\Check::assertInArray('D.11', '\Model::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'locale_dateformat' => 'f'
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_USER, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
        $expected = array(
            'locale_dateformat' => 'f',
        );
        \Flexio\Tests\Check::assertInArray('D.12', '\Model::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2,
            'timezone' => 'CDT'
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_USER, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
        $expected = array(
            'timezone' => 'CDT'
        );
        \Flexio\Tests\Check::assertInArray('D.13', '\Model::create(); when creating user, make sure parameter is set',  $actual, $expected, $results);
    }
}
