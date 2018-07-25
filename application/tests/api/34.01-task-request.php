<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-04-18
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
        // ENDPOINT: POST /:userid/processes/:objeid/run


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/v1';
        $password = \Flexio\Base\Password::generate();
        $userid = \Flexio\Tests\Util::createUser(null, null, $password);
        $token = \Flexio\Tests\Util::createToken($userid);


        // TEST: request parameters

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request"
            ]
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
                    "message": "Missing parameter: \'url\'"
                }
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'Process Request; missing url parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "invalid",
                "url" => "https://postman-echo.com/get"
            ]
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
                    "message": "Invalid syntax"
                }
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'Process Request; invalid method',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "get",
                "url" => "http"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $response = json_decode($result['response'],true);
        $actual = $result;
        $actual['response'] = $response;
        $expected = '{
            "code": 200,
            "content_type": "application/x-empty",
            "response": null
        }';
        \Flexio\Tests\Check::assertInArray('A.3', 'Process Request; invalid url',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "url" => "https://postman-echo.com/get"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $response = json_decode($result['response'],true);
        $actual = $result;
        $actual['response'] = $response;
        $expected = '{
            "code": 200,
            "content_type": "application/json; charset=utf-8",
            "response": {
                "args":[],
                "url":"https://postman-echo.com/get"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.4', 'Process Request; default method',  $actual, $expected, $results);


        // TEST: request task method case insensitivity

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "get",
                "url" => "https://postman-echo.com/get"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = json_decode($result['response'],true);
        $expected = '{
            "args":[],
            "url":"https://postman-echo.com/get"
        }';
        \Flexio\Tests\Check::assertInArray('B.1', 'Process Request; method case insensitivity',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "GET",
                "url" => "https://postman-echo.com/get"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = json_decode($result['response'],true);
        $expected = '{
            "args":[],
            "url":"https://postman-echo.com/get"
        }';
        \Flexio\Tests\Check::assertInArray('B.2', 'Process Request; method case insensitivity',  $actual, $expected, $results);


        // TEST: request task basic functionality

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "get",
                "url" => "https://raw.githubusercontent.com/flexiodata/functions/master/python/hello-world.py"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = substr($result['response'],0,27);
        $expected = 'def flexio_handler(context)';
        \Flexio\Tests\Check::assertString('C.1', 'Process Request; basic functionality',  $actual, $expected, $results);


        // TEST: request task basic auth

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "get",
                "url" => "https://postman-echo.com/get",
                "username" => "a",
                "password" => ""
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = json_decode($result['response'],true);
        $expected = '{
            "url":"https://postman-echo.com/get",
            "headers":{
                "authorization":"Basic YTo="
            },
            "args":[]
        }';
        \Flexio\Tests\Check::assertInArray('D.1', 'Process Request; basic auth',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "get",
                "url" => "https://postman-echo.com/get",
                "username" => "",
                "password" => "b"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = json_decode($result['response'],true);
        $expected = '{
            "url":"https://postman-echo.com/get",
            "headers":{
                "authorization":"Basic OmI="
            },
            "args":[]
        }';
        \Flexio\Tests\Check::assertInArray('D.2', 'Process Request; basic auth',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "get",
                "url" => "https://postman-echo.com/get",
                "username" => "a",
                "password" => "b"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = json_decode($result['response'],true);
        $expected = '{
            "url":"https://postman-echo.com/get",
            "headers":{
                "authorization":"Basic YTpi"
            },
            "args":[]
        }';
        \Flexio\Tests\Check::assertInArray('D.3', 'Process Request; basic auth',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "get",
                "url" => "https://postman-echo.com/get",
                "username" => "A",
                "password" => "B"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = json_decode($result['response'],true);
        $expected = '{
            "url":"https://postman-echo.com/get",
            "headers":{
                "authorization":"Basic QTpC"
            },
            "args":[]
        }';
        \Flexio\Tests\Check::assertInArray('D.4', 'Process Request; basic auth',  $actual, $expected, $results);
    }
}

