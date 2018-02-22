<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-06-27
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



        // TEST: object creation

        // BEGIN TEST
        $username = \Flexio\Base\Util::generateHandle();
        $email = TestUtil::createEmailAddress();
        $password = \Flexio\Base\Util::generatePassword();

        $params = json_decode('
        {
            "user_name": "'.$username.'",
            "email": "'.$email.'",
            "password": "'.$password.'",
            "send_email": false,
            "create_examples": false
        }
        ',true);
        $request = \Flexio\Api\Request::create();
        $request->setPostParams($params);
        $actual= \Flexio\Api\User::create($request);
        $expected = '
        {
            "eid_type": "'.\Model::TYPE_USER.'",
            "user_name": "'.$username.'",
            "email": "'.$email.'"
        }
        ';
        \Flexio\Tests\Check::assertInArray('A.1', '\Flexio\Api\User::create(); return the object',  $actual, $expected, $results);



        // TEST: change password

        // BEGIN TEST
        $username = \Flexio\Base\Util::generateHandle();
        $email = TestUtil::createEmailAddress();
        $password1 = \Flexio\Base\Util::generatePassword();
        $password2 = \Flexio\Base\Util::generatePassword();

        $params = json_decode('
        {
            "user_name": "'.$username.'",
            "email": "'.$email.'",
            "password": "'.$password1.'",
            "send_email": false,
            "create_examples": false
        }
        ',true);
        $request = \Flexio\Api\Request::create();
        $request->setPostParams($params);
        $user_info = \Flexio\Api\User::create($request);
        $user_eid = $user_info['eid'];
        $initial_password1_match = $model->user->checkUserPasswordByEid($user_eid, $password1); // should match
        $initial_password2_match = $model->user->checkUserPasswordByEid($user_eid, $password2); // should not match
        $params = json_decode('
        {
            "eid": "'.$user_eid.'",
            "old_password": "'.$password1.'",
            "new_password": "'.$password2.'"
        }
        ',true);
        $request = \Flexio\Api\Request::create();
        $request->setRequestingUser($user_eid);
        $request->setPostParams($params);
        \Flexio\Api\User::changepassword($request);
        $updated_password1_match = $model->user->checkUserPasswordByEid($user_eid, $password1); // should not match
        $updated_password2_match = $model->user->checkUserPasswordByEid($user_eid, $password2); // should match
        $actual = ($initial_password1_match == true && $initial_password2_match == false && $updated_password1_match == false && $updated_password2_match == true);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Api\User::changepassword(); make sure that the password is changed',  $actual, $expected, $results);



        // TEST: request password change
    }
}
