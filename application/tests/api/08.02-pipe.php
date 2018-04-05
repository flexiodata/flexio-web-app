<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-04-05
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
        // ENDPOINT: POST /:userid/pipes/:objeid


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/api/v2';
        $password1 = \Flexio\Base\Util::generatePassword();
        $userid1 = \Flexio\Tests\Util::createUser(null, null, $password1);
        $token1 = \Flexio\Tests\Util::createToken($userid1);
        $password2 = \Flexio\Base\Util::generatePassword();
        $userid2 = \Flexio\Tests\Util::createUser(null, null, $password2);
        $token2 = \Flexio\Tests\Util::createToken($userid2);

        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "Test Pipe",
                "ename": "ename1"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = json_decode($result['response'],true);
        $objeid1 = $response['eid'] ?? '';

        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid2/pipes",
            'token' => $token2,
            'content_type' => 'application/json',
            'params' => '{
                "name": "Test Pipe",
                "ename": "ename2"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = json_decode($result['response'],true);
        $objeid2 = $response['eid'] ?? '';

        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "Test Pipe",
                "ename": "ename3"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = json_decode($result['response'],true);
        $objeid3 = $response['eid'] ?? '';


        // TEST: change pipe info

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            // 'token' => '', // no token included
            'content_type' => 'application/json',
            'params' => '{
                "name": "Updated Test Pipe"
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
        \Flexio\Tests\Check::assertInArray('A.1', 'POST /:userid/pipes/:objeid; fail if requesting user doesn\'t have credentials',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid2",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "Updated Test Pipe"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code": "no-object"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'POST /:userid/pipes/:objeid; fail if object isn\'t owned by specified owner',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token2, // token for another user
            'content_type' => 'application/json',
            'params' => '{
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
        \Flexio\Tests\Check::assertInArray('A.3', 'POST /:userid/pipes/:objeid; fail if requesting user doesn\'t have rights',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "ename": "ename3"
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
        \Flexio\Tests\Check::assertInArray('A.4', 'POST /:userid/pipes/:objeid; fail if ename already exists',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "ename": "ename2"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PIP",
            "eid_status": "A",
            "ename": "ename2"
        }';
        \Flexio\Tests\Check::assertInArray('A.5', 'POST /:userid/pipes/:objeid; make sure unique ename only applies within an owner',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "ename": "ename1"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PIP",
            "eid_status": "A",
            "ename": "ename1"
        }';
        \Flexio\Tests\Check::assertInArray('A.6', 'POST /:userid/pipes/:objeid; allow ename to be set to what it alrady is',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "ename": ""
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PIP",
            "eid_status": "A",
            "ename": ""
        }';
        \Flexio\Tests\Check::assertInArray('A.7', 'POST /:userid/pipes/:objeid; allow ename to be reset',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Util::generateHandle();
        $new_password = \Flexio\Base\Util::generatePassword();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "name": "Test Pipe Updated",
                "ename": "ename1-updated",
                "description": "Test Pipe Description Updated",
                "task": {
                    "op": "request",
                    "params": {
                        "url": "https://api.domain.com"
                    }
                },
                "schedule": {
                    "frequency": "",
                    "timezone": "UTC",
                    "days": ["mon","tue","wed","thu","fri"],
                    "times": [
                        {
                            "hour": 5,
                            "minute": 0
                        }
                    ]
                },
                "schedule_status": "I"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PIP",
            "eid_status": "A",
            "ename": "ename1-updated",
            "name": "Test Pipe Updated",
            "description": "Test Pipe Description Updated",
            "task": {
                "op": "request",
                "params": {
                    "url": "https://api.domain.com"
                }
            },
            "schedule": {
                "timezone": "UTC",
                "days": ["mon","tue","wed","thu","fri"],
                "times": [
                    {
                        "hour": 5,
                        "minute": 0
                    }
                ]
            },
            "schedule_status": "I",
            "owned_by": {
                "eid": "'.$userid1.'",
                "eid_type": "USR"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.8', 'POST /:userid/pipes/:objeid; return updated pipe info',  $actual, $expected, $results);
    }
}
