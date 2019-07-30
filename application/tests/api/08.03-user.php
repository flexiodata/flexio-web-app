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
        // ENDPOINT: POST /:teamid/account/credentials


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/v1';
        $password1 = \Flexio\Base\Password::generate();
        $userid1 = \Flexio\Tests\Util::createUser(null, null, $password1);
        $token1 = \Flexio\Tests\Util::createToken($userid1);
        $password2 = \Flexio\Base\Password::generate();
        $userid2 = \Flexio\Tests\Util::createUser(null, null, $password2);
        $token2 = \Flexio\Tests\Util::createToken($userid2);


        // TEST: change password

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/account/credentials",
            // 'token' => '', // no token included
            'content_type' => 'application/json',
            'params' => '{
                "old_password": "'.$password1.'",
                "new_password": "'.$password2.'"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error" : {
                "code": "insufficient-rights"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'POST /:teamid/account/credentials; fail if requesting user doesn\'t have credentials',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/account/credentials",
            'token' => $token2, // token for another user
            'content_type' => 'application/json',
            'params' => '{
                "old_password": "'.$password1.'",
                "new_password": "'.$password2.'"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error" : {
                "code": "insufficient-rights"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'POST /:teamid/account/credentials; fail if requesting user doesn\'t have rights',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/account/credentials",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "old_password": "'.strtoupper($password1).'",
                "new_password": "'.$password2.'"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error" : {
                "code": "unauthorized"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.3', 'POST /:teamid/account/credentials; fail if old password doesn\'t match',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/account/credentials",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "old_password": "'.$password1.'",
                "new_password": "abcdef"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error" : {
                "code": "invalid-syntax"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.4', 'POST /:teamid/account/credentials; fail if new password is a bad parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $password1_matches_before = \Flexio\Tests\Util::getModel()->user->checkUserPasswordByEid($userid1, $password1);
        $password2_matches_before = \Flexio\Tests\Util::getModel()->user->checkUserPasswordByEid($userid1, $password2);
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/account/credentials",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "old_password": "'.$password1.'",
                "new_password": "'.$password2.'"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);

        $password1_matches_after = \Flexio\Tests\Util::getModel()->user->checkUserPasswordByEid($userid1, $password1);
        $password2_matches_after = \Flexio\Tests\Util::getModel()->user->checkUserPasswordByEid($userid1, $password2);
        $actual = ($password1_matches_before == true && $password2_matches_before == false && $password1_matches_after == false && $password2_matches_after == true);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.5', 'POST /:teamid/account/credentials; make sure that the password is changed',  $actual, $expected, $results);

        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$userid1.'",
            "eid_type": "USR",
            "eid_status": "A"
        }';
        \Flexio\Tests\Check::assertInArray('A.6', 'POST /:teamid/account/credentials; change password response',  $actual, $expected, $results);
    }
}