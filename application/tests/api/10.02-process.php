<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-05-09
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
        // ENDPOINT: POST /:userid/processes/:objeid


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/v1';
        $password1 = \Flexio\Base\Password::generate();
        $userid1 = \Flexio\Tests\Util::createUser(null, null, $password1);
        $token1 = \Flexio\Tests\Util::createToken($userid1);
        $password2 = \Flexio\Base\Password::generate();
        $userid2 = \Flexio\Tests\Util::createUser(null, null, $password2);
        $token2 = \Flexio\Tests\Util::createToken($userid2);

        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/processes",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = json_decode($result['response'],true);
        $objeid1 = $response['eid'] ?? '';

        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid2/processes",
            'token' => $token2,
            'content_type' => 'application/json',
            'params' => '{
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = json_decode($result['response'],true);
        $objeid2 = $response['eid'] ?? '';

        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/processes",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = json_decode($result['response'],true);
        $objeid3 = $response['eid'] ?? '';


        // TEST: change process info

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/processes/$objeid1",
            // 'token' => '', // no token included
            'content_type' => 'application/json',
            'params' => '{
                "task": {
                    "op": "echo"
                }
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
        \Flexio\Tests\Check::assertInArray('A.1', 'POST /:userid/processes/:objeid; fail if requesting user doesn\'t have credentials',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/processes/$objeid2",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "task": {
                    "op": "echo"
                }
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code": "unavailable"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'POST /:userid/processes/:objeid; fail if object isn\'t owned by specified owner',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/processes/$objeid1",
            'token' => $token2, // token for another user
            'content_type' => 'application/json',
            'params' => '{
                "task": {
                    "op": "echo"
                }
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
        \Flexio\Tests\Check::assertInArray('A.3', 'POST /:userid/processes/:objeid; fail if requesting user doesn\'t have rights',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/processes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "task": {
                    "op": "echo"
                }
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PRC",
            "eid_status": "A",
            "task": {
                "op": "echo"
            },
            "owned_by": {
                "eid": "'.$userid1.'",
                "eid_type": "USR"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.4', 'POST /:userid/processes/:objeid; return updated process info',  $actual, $expected, $results);
    }
}
