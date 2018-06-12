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


        // TEST: request task POST method

        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "post",
                "url" => "https://postman-echo.com/post"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = json_decode($result['response'],true);
        $expected = '{
            "url":"https://postman-echo.com/post",
            "args":[],
            "form":[]
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'Process Request; POST method type',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "post",
                "url" => "https://postman-echo.com/post?p1=A&P2=b"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = json_decode($result['response'],true);
        $expected = '{
            "url":"https://postman-echo.com/post?p1=A&P2=b",
            "args":{
                "p1":"A",
                "P2":"b"
            },
            "form":[]
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'Process Request; POST method type',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "post",
                "url" => "https://postman-echo.com/post",
                "data" => [
                    "f1" => "A",
                    "F2" => "b"
                ]
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = json_decode($result['response'],true);
        $expected = '{
            "url":"https://postman-echo.com/post",
            "args":[],
            "form":{
                "f1":"A",
                "F2":"b"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.3', 'Process Request; POST method type',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "post",
                "url" => "https://postman-echo.com/post?p1=A&P2=b",
                "data" => [
                    "f1" => "A",
                    "F2" => "b"
                ]
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = json_decode($result['response'],true);
        $expected = '{
            "url":"https://postman-echo.com/post?p1=A&P2=b",
            "args":{
                "p1":"A",
                "P2":"b"
            },
            "form":{
                "f1":"A",
                "F2":"b"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.4', 'Process Request; POST method type',  $actual, $expected, $results);
    }
}

