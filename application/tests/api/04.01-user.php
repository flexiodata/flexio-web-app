<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-04-03
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
        $apibase = \Flexio\Tests\Util::getTestHost() . '/api/v2';
        $token = \Flexio\Tests\Util::getDefaultTestUserToken();


        // TEST: create a new user

        // BEGIN TEST
        $username = \Flexio\Base\Util::generateHandle();
        $email = \Flexio\Tests\Util::createEmailAddress();
        $password = \Flexio\Base\Util::generatePassword();

        $params = array(
            'method' => 'POST',
            'url' => $apibase . '/signup',
            'content_type' => 'application/json',
            'params' => '{
                "user_name": "'.$username.'",
                "email": "'.$email.'",
                "password": "'.$password.'",
                "send_email": "true",
                "create_examples": "true"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid_type": "'.\Model::TYPE_USER.'",
            "user_name": "'.$username.'",
            "email": "'.$email.'"
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'GET /api/v2/signup; create a new user',  $actual, $expected, $results);


        // TEST: make sure we can't create a duplicate user on either username or email

        // BEGIN TEST
        $username_original = \Flexio\Base\Util::generateHandle();
        $email_original = \Flexio\Tests\Util::createEmailAddress();
        $password = \Flexio\Base\Util::generatePassword();

        for ($i = 1; $i <= 3; ++$i)
        {
            $username = $username_original;
            $email = $email_original;

            // on first loop, create the user; on second loop, check
            // for duplicate username (i.e., change the email); on third
            // loop, check for duplicate email (i.e., change the username)
            if ($i === 2)
            {
                $email = \Flexio\Tests\Util::createEmailAddress();
            }
            if ($i === 3)
                $username = \Flexio\Base\Util::generateHandle();

            $params = array(
                'method' => 'POST',
                'url' => $apibase . '/signup',
                'content_type' => 'application/json',
                'params' => '{
                    "user_name": "'.$username.'",
                    "email": "'.$email.'",
                    "password": "'.$password.'",
                    "send_email": "true",
                    "create_examples": "true"
                }'
            );
            $result = \Flexio\Tests\Util::callApi($params);
            $actual = $result['response'];

            if ($i === 1)
            {
                $expected = '
                {
                    "eid_type": "'.\Model::TYPE_USER.'",
                    "user_name": "'.$username.'",
                    "email": "'.$email.'"
                }';
                \Flexio\Tests\Check::assertInArray('B.1', 'GET /api/v2/signup; create a new user',  $actual, $expected, $results);
            }

            if ($i === 2 || $i === 3)
            {
                $expected = '
                {
                    "error" : {
                        "code": "create-failed"
                    }
                }';
                \Flexio\Tests\Check::assertInArray("B.$i", 'GET /api/v2/signup; create a new user',  $actual, $expected, $results);
            }
        }

/*
// TODO: old tests; convert over to new

        // TEST: change password

        // BEGIN TEST
        $username = \Flexio\Base\Util::generateHandle();
        $email = \Flexio\Tests\Util::createEmailAddress();
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
        $request = \Flexio\Api1\Request::create();
        $request->setPostParams($params);
        $user_info = \Flexio\Api1\User::create($request);
        $user_eid = $user_info['eid'];
        $initial_password1_match = \Flexio\Tests\Util::getModel()->user->checkUserPasswordByEid($user_eid, $password1); // should match
        $initial_password2_match = \Flexio\Tests\Util::getModel()->user->checkUserPasswordByEid($user_eid, $password2); // should not match
        $params = json_decode('
        {
            "eid": "'.$user_eid.'",
            "old_password": "'.$password1.'",
            "new_password": "'.$password2.'"
        }
        ',true);
        $request = \Flexio\Api1\Request::create();
        $request->setRequestingUser($user_eid);
        $request->setPostParams($params);
        \Flexio\Api1\User::changepassword($request);
        $updated_password1_match = \Flexio\Tests\Util::getModel()->user->checkUserPasswordByEid($user_eid, $password1); // should not match
        $updated_password2_match = \Flexio\Tests\Util::getModel()->user->checkUserPasswordByEid($user_eid, $password2); // should match
        $actual = ($initial_password1_match == true && $initial_password2_match == false && $updated_password1_match == false && $updated_password2_match == true);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Api1\User::changepassword(); make sure that the password is changed',  $actual, $expected, $results);



        // TEST: request password change
*/
    }
}
