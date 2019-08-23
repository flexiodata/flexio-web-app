<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
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
        // ENDPOINT: POST /:teamid/pipes/:objeid


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
            'url' => "$apibase/$userid1/pipes",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "name1",
                "title": "Test Pipe"
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
                "name": "name2",
                "title": "Test Pipe"
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
                "name": "name3",
                "title": "Test Pipe"
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
                "title": "Updated Test Pipe"
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
        \Flexio\Tests\Check::assertInArray('A.1', 'POST /:teamid/pipes/:objeid; fail if requesting user doesn\'t have credentials',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid2",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "title": "Updated Test Pipe"
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
        \Flexio\Tests\Check::assertInArray('A.2', 'POST /:teamid/pipes/:objeid; fail if object isn\'t owned by specified owner',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertInArray('A.3', 'POST /:teamid/pipes/:objeid; fail if requesting user doesn\'t have rights',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "name3"
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
        \Flexio\Tests\Check::assertInArray('A.4', 'POST /:teamid/pipes/:objeid; fail if name already exists',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "name2"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PIP",
            "eid_status": "A",
            "name": "name2"
        }';
        \Flexio\Tests\Check::assertInArray('A.5', 'POST /:teamid/pipes/:objeid; make sure unique name only applies within an owner',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "name1"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PIP",
            "eid_status": "A",
            "name": "name1"
        }';
        \Flexio\Tests\Check::assertInArray('A.6', 'POST /:teamid/pipes/:objeid; allow name to be set to what it already is',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "another-name"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PIP",
            "eid_status": "A",
            "name": "another-name"
        }';
        \Flexio\Tests\Check::assertInArray('A.7', 'POST /:teamid/pipes/:objeid; allow name to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "name": "name1-updated",
                "title": "Test Pipe Updated",
                "description": "Test Pipe Description Updated",
                "syntax": "param1, param2",
                "task": {
                    "op": "echo"
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
                "deploy_mode": "B",
                "deploy_schedule": "I",
                "deploy_email": "A",
                "deploy_api": "A",
                "deploy_ui": "I"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PIP",
            "eid_status": "A",
            "name": "name1-updated",
            "title": "Test Pipe Updated",
            "description": "Test Pipe Description Updated",
            "syntax": "param1, param2",
            "task": {
                "op": "echo"
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
            "deploy_mode": "B",
            "deploy_schedule": "I",
            "deploy_email": "A",
            "deploy_api": "A",
            "deploy_ui": "I",
            "owned_by": {
                "eid": "'.$userid1.'",
                "eid_type": "USR"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.8', 'POST /:teamid/pipes/:objeid; return updated pipe info',  $actual, $expected, $results);


        // TEST: change pipe info; check variations in schedule and schedule status

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "deploy_schedule": ""
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PIP",
            "deploy_schedule": "I"
        }';
        \Flexio\Tests\Check::assertInArray('B.1', 'POST /:teamid/pipes/:objeid; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "deploy_schedule": "bad"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PIP",
            "eid_status": "A",
            "deploy_schedule": "I"
        }';
        \Flexio\Tests\Check::assertInArray('B.2', 'POST /:teamid/pipes/:objeid; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "deploy_schedule": "I"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PIP",
            "eid_status": "A",
            "deploy_schedule": "I"
        }';
        \Flexio\Tests\Check::assertInArray('B.3', 'POST /:teamid/pipes/:objeid; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        // note: in following test, leave setting of the schedule status to "A"
        // here so that subsequent tests disable it so it doesn't run needlessly
        // in the test environment
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "deploy_schedule": "A"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PIP",
            "eid_status": "A",
            "deploy_schedule": "A"
        }';
        \Flexio\Tests\Check::assertInArray('B.4', 'POST /:teamid/pipes/:objeid; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "deploy_schedule": "I"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PIP",
            "eid_status": "A",
            "deploy_schedule": "I"
        }';
        \Flexio\Tests\Check::assertInArray('B.5', 'POST /:teamid/pipes/:objeid; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "name": "name1-updated",
                "title": "Test Pipe Updated",
                "description": "Test Pipe Description Updated",
                "task": {
                    "op": "echo"
                },
                "schedule": {
                    "frequency": "",
                    "timezone": "",
                    "days": [],
                    "times": []
                },
                "deploy_schedule": "I"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PIP",
            "eid_status": "A",
            "name": "name1-updated",
            "title": "Test Pipe Updated",
            "description": "Test Pipe Description Updated",
            "task": {
                "op": "echo"
            },
            "schedule": {
                "frequency": "",
                "timezone": "",
                "days": [],
                "times": []
            },
            "deploy_schedule": "I"
        }';
        \Flexio\Tests\Check::assertInArray('B.6', 'POST /:teamid/pipes/:objeid; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "schedule": {
                    "frequency": "one-minute",
                    "timezone": "",
                    "days": [],
                    "times": []
                },
                "deploy_schedule": "I"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PIP",
            "eid_status": "A",
            "schedule": {
                "frequency": "one-minute",
                "timezone": "",
                "days": [],
                "times": []
            },
            "deploy_schedule": "I"
        }';
        \Flexio\Tests\Check::assertInArray('B.7', 'POST /:teamid/pipes/:objeid; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "schedule": {
                    "frequency": "five-minutes",
                    "timezone": "",
                    "days": [],
                    "times": []
                },
                "deploy_schedule": "I"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PIP",
            "eid_status": "A",
            "schedule": {
                "frequency": "five-minutes",
                "timezone": "",
                "days": [],
                "times": []
            },
            "deploy_schedule": "I"
        }';
        \Flexio\Tests\Check::assertInArray('B.8', 'POST /:teamid/pipes/:objeid; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "schedule": {
                    "frequency": "fifteen-minutes",
                    "timezone": "",
                    "days": [],
                    "times": []
                },
                "deploy_schedule": "I"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PIP",
            "eid_status": "A",
            "schedule": {
                "frequency": "fifteen-minutes",
                "timezone": "",
                "days": [],
                "times": []
            },
            "deploy_schedule": "I"
        }';
        \Flexio\Tests\Check::assertInArray('B.9', 'POST /:teamid/pipes/:objeid; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "schedule": {
                    "frequency": "thirty-minutes",
                    "timezone": "",
                    "days": [],
                    "times": []
                },
                "deploy_schedule": "I"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PIP",
            "eid_status": "A",
            "schedule": {
                "frequency": "thirty-minutes",
                "timezone": "",
                "days": [],
                "times": []
            },
            "deploy_schedule": "I"
        }';
        \Flexio\Tests\Check::assertInArray('B.10', 'POST /:teamid/pipes/:objeid; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "schedule": {
                    "frequency": "hourly",
                    "timezone": "",
                    "days": [],
                    "times": []
                },
                "deploy_schedule": "I"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PIP",
            "eid_status": "A",
            "schedule": {
                "frequency": "hourly",
                "timezone": "",
                "days": [],
                "times": []
            },
            "deploy_schedule": "I"
        }';
        \Flexio\Tests\Check::assertInArray('B.11', 'POST /:teamid/pipes/:objeid; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "schedule": {
                    "frequency": "daily",
                    "timezone": "",
                    "days": [],
                    "times": []
                },
                "deploy_schedule": "I"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PIP",
            "eid_status": "A",
            "schedule": {
                "frequency": "daily",
                "timezone": "",
                "days": [],
                "times": []
            },
            "deploy_schedule": "I"
        }';
        \Flexio\Tests\Check::assertInArray('B.12', 'POST /:teamid/pipes/:objeid; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "schedule": {
                    "frequency": "weekly",
                    "timezone": "",
                    "days": [],
                    "times": []
                },
                "deploy_schedule": "I"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PIP",
            "eid_status": "A",
            "schedule": {
                "frequency": "weekly",
                "timezone": "",
                "days": [],
                "times": []
            },
            "deploy_schedule": "I"
        }';
        \Flexio\Tests\Check::assertInArray('B.13', 'POST /:teamid/pipes/:objeid; check variations in schedule and schedule status',  $actual, $expected, $results);


        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "schedule": {
                    "frequency": "monthly",
                    "timezone": "",
                    "days": [],
                    "times": []
                },
                "deploy_schedule": "I"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PIP",
            "eid_status": "A",
            "schedule": {
                "frequency": "monthly",
                "timezone": "",
                "days": [],
                "times": []
            },
            "deploy_schedule": "I"
        }';
        \Flexio\Tests\Check::assertInArray('B.14', 'POST /:teamid/pipes/:objeid; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "schedule": {
                    "frequency": "yearly",
                    "timezone": "",
                    "days": [],
                    "times": []
                },
                "deploy_schedule": "I"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code":"invalid-syntax",
                "message":"Invalid syntax"
            }
        }';
        \Flexio\Tests\Check::assertInArray('B.15', 'POST /:teamid/pipes/:objeid; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "schedule": {
                    "frequency": "DAILY",
                    "timezone": "",
                    "days": [],
                    "times": []
                },
                "deploy_schedule": "I"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code":"invalid-syntax",
                "message":"Invalid syntax"
            }
        }';
        \Flexio\Tests\Check::assertInArray('B.16', 'POST /:teamid/pipes/:objeid; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "schedule": {
                    "frequency": "daily",
                    "timezone": "",
                    "days": ["mon","tue","wed","thu","fri","sat","sun","last",1,15],
                    "times": []
                },
                "deploy_schedule": "I"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PIP",
            "eid_status": "A",
            "schedule": {
                "frequency": "daily",
                "timezone": "",
                "days": ["mon","tue","wed","thu","fri","sat","sun","last",1,15],
                "times": []
            },
            "deploy_schedule": "I"
        }';
        \Flexio\Tests\Check::assertInArray('B.17', 'POST /:teamid/pipes/:objeid; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "schedule": {
                    "frequency": "daily",
                    "timezone": "",
                    "days": ["saturday"],
                    "times": []
                },
                "deploy_schedule": "I"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code":"invalid-syntax",
                "message":"Invalid syntax"
            }
        }';
        \Flexio\Tests\Check::assertInArray('B.18', 'POST /:teamid/pipes/:objeid; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "schedule": {
                    "frequency": "daily",
                    "timezone": "",
                    "days": ["sat"],
                    "times": [
                        {}
                    ]
                },
                "deploy_schedule": "I"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code":"invalid-syntax",
                "message":"Invalid syntax"
            }
        }';
        \Flexio\Tests\Check::assertInArray('B.19', 'POST /:teamid/pipes/:objeid; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "schedule": {
                    "frequency": "daily",
                    "timezone": "",
                    "days": [15],
                    "times": [
                        {
                            "hour": 0,
                            "minute": 0
                        },
                        {
                            "hour": 10,
                            "minute": 20
                        }
                    ]
                },
                "deploy_schedule": "I"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PIP",
            "eid_status": "A",
            "schedule": {
                "frequency": "daily",
                "timezone": "",
                "days": [15],
                "times": [
                    {
                        "hour": 0,
                        "minute": 0
                    },
                    {
                        "hour": 10,
                        "minute": 20
                    }
                ]
            },
            "deploy_schedule": "I"
        }';
        \Flexio\Tests\Check::assertInArray('B.20', 'POST /:teamid/pipes/:objeid; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "schedule": {
                    "frequency": "daily",
                    "timezone": "",
                    "days": ["sat"],
                    "times": [
                        {
                            "hour": 1.1,
                            "minute": 0
                        }
                    ]
                },
                "deploy_schedule": "I"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code":"invalid-syntax",
                "message":"Invalid syntax"
            }
        }';
        \Flexio\Tests\Check::assertInArray('B.21', 'POST /:teamid/pipes/:objeid; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "schedule": {
                    "frequency": "daily",
                    "timezone": "",
                    "days": ["sat"],
                    "times": [
                        {
                            "hour": 0,
                            "minute": 1.1
                        }
                    ]
                },
                "deploy_schedule": "I"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code":"invalid-syntax",
                "message":"Invalid syntax"
            }
        }';
        \Flexio\Tests\Check::assertInArray('B.22', 'POST /:teamid/pipes/:objeid; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "schedule": {
                    "frequency": "daily",
                    "timezone": "",
                    "days": ["sat"],
                    "times": [
                        {
                            "hour": 25,
                            "minute": 0
                        }
                    ]
                },
                "deploy_schedule": "I"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code":"invalid-syntax",
                "message":"Invalid syntax"
            }
        }';
        \Flexio\Tests\Check::assertInArray('B.23', 'POST /:teamid/pipes/:objeid; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "schedule": {
                    "frequency": "daily",
                    "timezone": "",
                    "days": ["sat"],
                    "times": [
                        {
                            "hour": 0,
                            "minute": 61
                        }
                    ]
                },
                "deploy_schedule": "I"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code":"invalid-syntax",
                "message":"Invalid syntax"
            }
        }';
        \Flexio\Tests\Check::assertInArray('B.24', 'POST /:teamid/pipes/:objeid; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "schedule": {
                    "frequency": "daily",
                    "timezone": "",
                    "days": ["sat"],
                    "times": [
                        {
                            "hour": -1,
                            "minute": 0
                        }
                    ]
                },
                "deploy_schedule": "I"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code":"invalid-syntax",
                "message":"Invalid syntax"
            }
        }';
        \Flexio\Tests\Check::assertInArray('B.25', 'POST /:teamid/pipes/:objeid; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "schedule": {
                    "frequency": "daily",
                    "timezone": "",
                    "days": ["sat"],
                    "times": [
                        {
                            "hour": 0,
                            "minute": -1
                        }
                    ]
                },
                "deploy_schedule": "I"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code":"invalid-syntax",
                "message":"Invalid syntax"
            }
        }';
        \Flexio\Tests\Check::assertInArray('B.26', 'POST /:teamid/pipes/:objeid; check variations in schedule and schedule status',  $actual, $expected, $results);
    }
}
