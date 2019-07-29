<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
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
        // ENDPOINT: POST /signup


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/v1';


        // TEST: create a new user

        // BEGIN TEST
        $username = \Flexio\Base\Identifier::generate();
        $email = \Flexio\Tests\Util::createEmailAddress();
        $password = \Flexio\Base\Password::generate();

        $params = array(
            'method' => 'POST',
            'url' => "$apibase/signup",
            // 'token' => '', // no token included
            'content_type' => 'application/json',
            'params' => '{
                "username": "'.$username.'",
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
            "username": "'.$username.'",
            "email": "'.$email.'"
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'POST /signup; create a new user',  $actual, $expected, $results);


        // TEST: make sure we can't create a duplicate user on either username or email

        // BEGIN TEST
        $username1 = \Flexio\Base\Identifier::generate();
        $email1 = \Flexio\Tests\Util::createEmailAddress();
        $password1 = \Flexio\Base\Password::generate();
        $username2 = $username1;
        $email2 = \Flexio\Tests\Util::createEmailAddress();
        $password2 = \Flexio\Base\Password::generate();

        $params = array(
            'method' => 'POST',
            'url' => "$apibase/signup",
            // 'token' => '', // no token included
            'content_type' => 'application/json',
            'params' => '{
                "username": "'.$username1.'",
                "email": "'.$email1.'",
                "password": "'.$password1.'",
                "send_email": "true",
                "create_examples": "true"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params); // create a user
        $user_eid = \Flexio\Object\User::getEidFromUsername($username1);
        \Flexio\Object\User::load($user_eid)->setStatus(\Model::STATUS_AVAILABLE); // quick hack for verifying the user
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/signup",
            // 'token' => '', // no token included
            'content_type' => 'application/json',
            'params' => '{
                "username": "'.$username2.'",
                "email": "'.$email2.'",
                "password": "'.$password2.'",
                "send_email": "true",
                "create_examples": "true"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params); // create another user with the same username
        $actual = $result['response'];
        $expected = '
        {
            "error" : {
                "code": "create-failed"
            }
        }';
        \Flexio\Tests\Check::assertInArray('B.1', 'POST /signup; fail if username is already taken',  $actual, $expected, $results);

        // BEGIN TEST
        $username1 = \Flexio\Base\Identifier::generate();
        $email1 = \Flexio\Tests\Util::createEmailAddress();
        $password1 = \Flexio\Base\Password::generate();
        $username2 = \Flexio\Base\Identifier::generate();
        $email2 = $email1;
        $password2 = \Flexio\Base\Password::generate();

        $params = array(
            'method' => 'POST',
            'url' => "$apibase/signup",
            // 'token' => '', // no token included
            'content_type' => 'application/json',
            'params' => '{
                "username": "'.$username1.'",
                "email": "'.$email1.'",
                "password": "'.$password1.'",
                "send_email": "true",
                "create_examples": "true"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params); // create a user
        $user_eid = \Flexio\Object\User::getEidFromUsername($username1);
        \Flexio\Object\User::load($user_eid)->setStatus(\Model::STATUS_AVAILABLE); // quick hack for verifying the user
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/signup",
            // 'token' => '', // no token included
            'content_type' => 'application/json',
            'params' => '{
                "username": "'.$username2.'",
                "email": "'.$email2.'",
                "password": "'.$password2.'",
                "send_email": "true",
                "create_examples": "true"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params); // create another user with the same username
        $actual = $result['response'];
        $expected = '
        {
            "error" : {
                "code": "create-failed"
            }
        }';
        \Flexio\Tests\Check::assertInArray('B.2', 'POST /signup; fail if email for a verified user is already taken',  $actual, $expected, $results);
    }
}
