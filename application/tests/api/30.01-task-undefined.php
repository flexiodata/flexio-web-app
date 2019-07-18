<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-04-17
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
        // ENDPOINT: POST /:teamid/processes/:objeid/run


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/v1';
        $password = \Flexio\Base\Password::generate();
        $userid = \Flexio\Tests\Util::createUser(null, null, $password);
        $token = \Flexio\Tests\Util::createToken($userid);


        // TEST: undefined task

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([[
            "a" => "b"
        ]]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $response = json_decode($result['response'],true);
        $actual = $result;
        $actual['response'] = $response;
        $expected = '{
            "code": 400,
            "content_type": "application/json",
            "response": {
                "error": {
                    "code": "invalid-syntax",
                    "message": "Missing operation \'op\' task parameter"
                }
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'POST /:teamid/processes/:objeid/run; return error for missing \'op\' task parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            ["op" => true]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $response = json_decode($result['response'],true);
        $actual = $result;
        $actual['response'] = $response;
        $expected = '{
            "code": 400,
            "content_type": "application/json",
            "response": {
                "error": {
                    "code": "invalid-syntax",
                    "message": "Invalid operation \'op\' task parameter"
                }
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'POST /:teamid/processes/:objeid/run; return error for invalid \'op\' task parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            ["op" => "undefined"]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $response = json_decode($result['response'],true);
        $actual = $result;
        $actual['response'] = $response;
        $expected = '{
            "code": 400,
            "content_type": "application/json",
            "response": {
                "error": {
                    "code": "invalid-syntax",
                    "message": "Invalid operation \'op\' task parameter"
                }
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.3', 'POST /:teamid/processes/:objeid/run; return error for invalid \'op\' task parameter',  $actual, $expected, $results);
    }
}

