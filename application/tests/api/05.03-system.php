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
        // ENDPOINT: POST /login
        // ENDPOINT: POST /logout


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/api/v2';
        $username1 = \Flexio\Base\Identifier::generate();
        $email1 = \Flexio\Tests\Util::createEmailAddress();
        $password1 = \Flexio\Base\Password::generate();
        $userid1 = \Flexio\Tests\Util::createUser($username1, $email1, $password1);
        $token1 = \Flexio\Tests\Util::createToken($userid1);
        $username2 = \Flexio\Base\Identifier::generate();
        $email2 = \Flexio\Tests\Util::createEmailAddress();
        $password2 = \Flexio\Base\Password::generate();
        $userid2 = \Flexio\Tests\Util::createUser($username2, $email2, $password2);
        $token2 = \Flexio\Tests\Util::createToken($userid2);


        // TEST: check login

        // BEGIN TEST
        \Flexio\System\System::clearLoginIdentity();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/login",
            // 'token' => '', // no token included
            'content_type' => 'application/json',
            'params' => '{
                "username": "'.\Flexio\Base\Identifier::generate().'",
                "password": "'.$password1.'"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error" : {
                "code":"invalid-parameter",
                "message":"Invalid username or password."
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'POST /login; fail if the user attempting to log in doesn\'t exist',  $actual, $expected, $results);

        // BEGIN TEST
        \Flexio\System\System::clearLoginIdentity();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/login",
            // 'token' => '', // no token included
            'content_type' => 'application/json',
            'params' => '{
                "username": "'.$username1.'",
                "password": "'.$password2.'"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error" : {
                "code":"invalid-parameter",
                "message":"Invalid username or password."
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'POST /login; fail if the user attempting to log in has the wrong credentials',  $actual, $expected, $results);

        // BEGIN TEST
        \Flexio\System\System::clearLoginIdentity();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/login",
            // 'token' => '', // no token included
            'content_type' => 'application/json',
            'params' => '{
                "username": "'.$username1.'",
                "password": "'.$password1.'"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid":"'.$userid1.'",
            "eid_type":"USR",
            "eid_status":"A",
            "username":"'.$username1.'",
            "first_name":"",
            "last_name":"",
            "email":"'.$email1.'"
        }';
        \Flexio\Tests\Check::assertInArray('A.3', 'POST /login; allow logins by username',  $actual, $expected, $results);

        // BEGIN TEST
        \Flexio\System\System::clearLoginIdentity();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/login",
            // 'token' => '', // no token included
            'content_type' => 'application/json',
            'params' => '{
                "username": "'.$email1.'",
                "password": "'.$password1.'"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid":"'.$userid1.'",
            "eid_type":"USR",
            "eid_status":"A",
            "username":"'.$username1.'",
            "first_name":"",
            "last_name":"",
            "email":"'.$email1.'"
        }';
        \Flexio\Tests\Check::assertInArray('A.4', 'POST /login; allow logins by email',  $actual, $expected, $results);


        // TEST: check logout

        // BEGIN TEST
        \Flexio\System\System::clearLoginIdentity();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/login",
            // 'token' => '', // no token included
            'content_type' => 'application/json',
            'params' => '{
                "username": "'.$username1.'",
                "password": "'.$password1.'"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid":"'.$userid1.'",
            "eid_type":"USR",
            "eid_status":"A",
            "username":"'.$username1.'",
            "first_name":"",
            "last_name":"",
            "email":"'.$email1.'"
        }';
        \Flexio\Tests\Check::assertInArray('B.1', 'POST /logout; make sure a user is logged in correctly before attempting a logout',  $actual, $expected, $results);
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/logout"
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid":"",
            "eid_type":"USR"
        }';
        \Flexio\Tests\Check::assertInArray('B.2', 'POST /logout; make sure sessions is destroyed on logout',  $actual, $expected, $results);
    }
}
