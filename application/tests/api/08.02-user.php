<?php
/**
 *
 * Copyright (c) 2018, Flex Research LLC. All rights reserved.
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
        // ENDPOINT: POST /:teamid/account


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/v1';
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


        // TEST: change account info

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/account",
            // 'token' => '', // no token included
            'content_type' => 'application/json',
            'params' => '{
                "full_name": "Test User"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code": "insufficient-rights"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'POST /:teamid/account; fail if requesting user doesn\'t have credentials',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/account",
            'token' => $token2, // token for another user
            'content_type' => 'application/json',
            'params' => '{
                "full_name": "Test User"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code": "insufficient-rights"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'POST /:teamid/account; fail if requesting user doesn\'t have rights',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/account",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "username": "'.$username2.'"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code": "write-failed"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.3', 'POST /:teamid/account; fail if username is already exists',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/account",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "email": "'.$email2.'"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code": "write-failed"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.4', 'POST /:teamid/account; fail if email is already exists',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/account",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "username": "'.$username1.'",
                "email": "'.$email1.'"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$userid1.'",
            "eid_type": "USR",
            "eid_status": "A",
            "username": "'.$username1.'",
            "email": "'.$email1.'"
        }';
        \Flexio\Tests\Check::assertInArray('A.5', 'POST /:teamid/account; allow username and email to be set to what they already are',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/account",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "username": "'.$new_username.'",
                "password": "'.$new_password.'",
                "first_name": "a",
                "last_name": "b",
                "email": "'.$new_email.'",
                "phone": "c",
                "location_city": "d",
                "location_state": "e",
                "location_country": "f",
                "company_name": "g",
                "company_url": "h",
                "locale_language": "i",
                "locale_decimal": "j",
                "locale_thousands": "k",
                "locale_dateformat": "l",
                "timezone": "m",
                "config": {
                    "custom_value": "abc"
                }
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$userid1.'",
            "eid_type": "USR",
            "eid_status": "A",
            "username": "'.$new_username.'",
            "first_name": "a",
            "last_name": "b",
            "email": "'.$new_email.'",
            "phone": "c",
            "location_city": "d",
            "location_state": "e",
            "location_country": "f",
            "company_name": "g",
            "company_url": "h",
            "locale_language": "i",
            "locale_decimal": "j",
            "locale_thousands": "k",
            "locale_dateformat": "l",
            "timezone": "m",
            "config": {
                "custom_value": "abc"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.6', 'POST /:teamid/account; change account response',  $actual, $expected, $results);
    }
}
