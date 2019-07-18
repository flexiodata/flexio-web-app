<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
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
        // ENDPOINT: POST /:teamid/processes/:objeid/run


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/v1';
        $password = \Flexio\Base\Password::generate();
        $userid = \Flexio\Tests\Util::createUser(null, null, $password);
        $token = \Flexio\Tests\Util::createToken($userid);


        // TEST: request task PATCH method

        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "patch",
                "url" => "https://postman-echo.com/patch"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = json_decode($result['response'],true);
        $expected = '{
            "url":"https://postman-echo.com/patch",
            "args":[],
            "form":[]
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'Process Request; PATCH method type',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "patch",
                "url" => "https://postman-echo.com/patch?p1=A&P2=b"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = json_decode($result['response'],true);
        $expected = '{
            "url":"https://postman-echo.com/patch?p1=A&P2=b",
            "args":{
                "p1":"A",
                "P2":"b"
            },
            "form":[]
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'Process Request; PATCH method type',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "patch",
                "url" => "https://postman-echo.com/patch",
                "data" => [
                    "f1" => "A",
                    "F2" => "b"
                ]
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = json_decode($result['response'],true);
        $expected = '{
            "url":"https://postman-echo.com/patch",
            "args":[],
            "form":{
                "f1":"A",
                "F2":"b"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.3', 'Process Request; PATCH method type',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "patch",
                "url" => "https://postman-echo.com/patch?p1=A&P2=b",
                "data" => [
                    "f1" => "A",
                    "F2" => "b"
                ]
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = json_decode($result['response'],true);
        $expected = '{
            "url":"https://postman-echo.com/patch?p1=A&P2=b",
            "args":{
                "p1":"A",
                "P2":"b"
            },
            "form":{
                "f1":"A",
                "F2":"b"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.4', 'Process Request; PATCH method type',  $actual, $expected, $results);
    }
}

