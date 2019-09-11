<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-10-23
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
        // ENDPOINT: POST /:teamid/pipes


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/v1';
        $password1 = \Flexio\Base\Password::generate();
        $userid1 = \Flexio\Tests\Util::createUser(null, null, $password1);
        $token1 = \Flexio\Tests\Util::createToken($userid1);
        $password2 = \Flexio\Base\Password::generate();
        $userid2 = \Flexio\Tests\Util::createUser(null, null, $password2);
        $token2 = \Flexio\Tests\Util::createToken($userid2);


        // TEST: create a new pipe

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes",
            // 'token' => '', // no token included
            'content_type' => 'application/json',
            'params' => '{
                "name": "test-pipe"
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
        \Flexio\Tests\Check::assertInArray('A.1', 'POST /:teamid/pipes; fail if requesting user doesn\'t have credentials',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes",
            'token' => $token2, // token for another user
            'content_type' => 'application/json',
            'params' => '{
                "name": "test-pipe"
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
        \Flexio\Tests\Check::assertInArray('A.2', 'POST /:teamid/pipes; fail if requesting user doesn\'t have rights',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "name1",
                "title": "Test Pipe Title",
                "description": "Test Pipe Description",
                "syntax": "param1, param2",
                "examples": ["e1", "e2", "e3"],
                "params": ["p1", "p2", "p3"],
                "notes": "Test Pipe Notes",
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
                "deploy_api": "A"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid_type": "PIP",
            "eid_status": "A",
            "parent": {
                "eid": "",
                "eid_type": "CTN"
            },
            "name": "name1",
            "title": "Test Pipe Title",
            "description": "Test Pipe Description",
            "syntax": "param1, param2",
            "examples": ["e1", "e2", "e3"],
            "params": ["p1", "p2", "p3"],
            "notes": "Test Pipe Notes",
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
            "owned_by": {
                "eid": "'.$userid1.'",
                "eid_type": "USR"
            }
        }
        ';
        \Flexio\Tests\Check::assertInArray('A.3', 'POST /:teamid/pipes; create a new pipe',  $actual, $expected, $results);


        // TEST: create a new pipe; check variations in schedule and schedule status

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "name2",
                "description": "Test Pipe Description",
                "schedule": {
                    "frequency": "daily",
                    "timezone": "UTC",
                    "days": [],
                    "times": [
                        {
                            "hour": 8,
                            "minute": 0
                        }
                    ]
                },
                "deploy_schedule": ""
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid_type": "PIP",
            "eid_status": "A",
            "parent": {
                "eid": "",
                "eid_type": "CTN"
            },
            "name": "name2",
            "description": "Test Pipe Description",
            "schedule": {
                "frequency": "daily",
                "timezone": "UTC",
                "days": [],
                "times": [
                    {
                        "hour": 8,
                        "minute": 0
                    }
                ]
            },
            "deploy_schedule": "I",
            "owned_by": {
                "eid": "'.$userid1.'",
                "eid_type": "USR"
            }
        }
        ';
        \Flexio\Tests\Check::assertInArray('B.1', 'POST /:teamid/pipes; create a new pipe; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "name3",
                "description": "Test Pipe Description",
                "schedule": {
                    "frequency": "daily",
                    "timezone": "UTC",
                    "days": [],
                    "times": [
                        {
                            "hour": 8,
                            "minute": 0
                        }
                    ]
                },
                "deploy_schedule": "bad"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid_type": "PIP",
            "eid_status": "A",
            "parent": {
                "eid": "",
                "eid_type": "CTN"
            },
            "name": "name3",
            "description": "Test Pipe Description",
            "schedule": {
                "frequency": "daily",
                "timezone": "UTC",
                "days": [],
                "times": [
                    {
                        "hour": 8,
                        "minute": 0
                    }
                ]
            },
            "deploy_schedule": "I",
            "owned_by": {
                "eid": "'.$userid1.'",
                "eid_type": "USR"
            }
        }
        ';
        \Flexio\Tests\Check::assertInArray('B.2', 'POST /:teamid/pipes; create a new pipe; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "name4",
                "description": "Test Pipe Description",
                "schedule": {
                    "frequency": "daily",
                    "timezone": "UTC",
                    "days": [],
                    "times": [
                        {
                            "hour": 8,
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
            "eid_type": "PIP",
            "eid_status": "A",
            "parent": {
                "eid": "",
                "eid_type": "CTN"
            },
            "name": "name4",
            "description": "Test Pipe Description",
            "schedule": {
                "frequency": "daily",
                "timezone": "UTC",
                "days": [],
                "times": [
                    {
                        "hour": 8,
                        "minute": 0
                    }
                ]
            },
            "deploy_schedule": "I",
            "owned_by": {
                "eid": "'.$userid1.'",
                "eid_type": "USR"
            }
        }
        ';
        \Flexio\Tests\Check::assertInArray('B.3', 'POST /:teamid/pipes; create a new pipe; check variations in schedule and schedule status',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "name5",
                "description": "Test Pipe Description",
                "schedule": {
                    "frequency": "daily",
                    "timezone": "UTC",
                    "days": [],
                    "times": [
                        {
                            "hour": 8,
                            "minute": 0
                        }
                    ]
                },
                "deploy_schedule": "A"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid_type": "PIP",
            "eid_status": "A",
            "parent": {
                "eid": "",
                "eid_type": "CTN"
            },
            "name": "name5",
            "description": "Test Pipe Description",
            "schedule": {
                "frequency": "daily",
                "timezone": "UTC",
                "days": [],
                "times": [
                    {
                        "hour": 8,
                        "minute": 0
                    }
                ]
            },
            "deploy_schedule": "A",
            "owned_by": {
                "eid": "'.$userid1.'",
                "eid_type": "USR"
            }
        }
        ';
        \Flexio\Tests\Check::assertInArray('B.4', 'POST /:teamid/pipes; create a new pipe; check variations in schedule and schedule status',  $actual, $expected, $results);
    }
}
