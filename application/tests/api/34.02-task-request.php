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
        $response = json_decode($result['response'],true);
        $actual = $result;
        $actual['response'] = $response;
        $expected = '{
            "code": 200,
            "content_type": "application\/json; charset=utf-8",
            "response": {
                "args":[],
                "url":"https://postman-echo.com/get"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'Process Request; method case insensitivity',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "GET",
                "url" => "https://postman-echo.com/get"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $response = json_decode($result['response'],true);
        $actual = $result;
        $actual['response'] = $response;
        $expected = '{
            "code": 200,
            "content_type": "application\/json; charset=utf-8",
            "response": {
                "args":[],
                "url":"https://postman-echo.com/get"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'Process Request; method case insensitivity',  $actual, $expected, $results);


        // TEST: request task method types

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "head",
                "url" => "https://postman-echo.com/head"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $response = json_decode($result['response'],true);
        $actual = $result;
        $actual['response'] = $response;
        $expected = '{
            "code": 200,
            "content_type": "application\/json; charset=utf-8",
            "response": {
                "args":[],
                "url":"https://postman-echo.com/head"
            }
        }';
        \Flexio\Tests\Check::assertInArray('B.1', 'Process Request; \'head\' method type',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "get",
                "url" => "https://postman-echo.com/get"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $response = json_decode($result['response'],true);
        $actual = $result;
        $actual['response'] = $response;
        $expected = '{
            "code": 200,
            "content_type": "application\/json; charset=utf-8",
            "response": {
                "args":[],
                "url":"https://postman-echo.com/get"
            }
        }';
        \Flexio\Tests\Check::assertInArray('B.2', 'Process Request; \'get\' method type',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "put",
                "url" => "https://postman-echo.com/put"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $response = json_decode($result['response'],true);
        $actual = $result;
        $actual['response'] = $response;
        $expected = '{
            "code": 200,
            "content_type": "application\/json; charset=utf-8",
            "response": {
                "args":[],
                "url":"https://postman-echo.com/put"
            }
        }';
        \Flexio\Tests\Check::assertInArray('B.3', 'Process Request; \'put\' method type',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "post",
                "url" => "https://postman-echo.com/post"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $response = json_decode($result['response'],true);
        $actual = $result;
        $actual['response'] = $response;
        $expected = '{
            "code": 200,
            "content_type": "application\/json; charset=utf-8",
            "response": {
                "args":[],
                "url":"https://postman-echo.com/post"
            }
        }';
        \Flexio\Tests\Check::assertInArray('B.4', 'Process Request; \'post\' method type',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "patch",
                "url" => "https://postman-echo.com/patch"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $response = json_decode($result['response'],true);
        $actual = $result;
        $actual['response'] = $response;
        $expected = '{
            "code": 200,
            "content_type": "application\/json; charset=utf-8",
            "response": {
                "args":[],
                "url":"https://postman-echo.com/patch"
            }
        }';
        \Flexio\Tests\Check::assertInArray('B.5', 'Process Request; \'patch\' method type',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "delete",
                "url" => "https://postman-echo.com/delete"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $response = json_decode($result['response'],true);
        $actual = $result;
        $actual['response'] = $response;
        $expected = '{
            "code": 200,
            "content_type": "application\/json; charset=utf-8",
            "response": {
                "args":[],
                "url":"https://postman-echo.com/delete"
            }
        }';
        \Flexio\Tests\Check::assertInArray('B.6', 'Process Request; \'delete\' method type',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "options",
                "url" => "https://postman-echo.com/options"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $response = json_decode($result['response'],true);
        $actual = $result;
        $actual['response'] = $response;
        $expected = '{
            "code": 200,
            "content_type": "application\/json; charset=utf-8",
            "response": {
                "args":[],
                "url":"https://postman-echo.com/options"
            }
        }';
        \Flexio\Tests\Check::assertInArray('B.7', 'Process Request; \'options\' method type',  $actual, $expected, $results);
    }
}

