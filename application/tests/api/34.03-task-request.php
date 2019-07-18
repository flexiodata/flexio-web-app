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


        // TEST: request task GET method

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
            "url":"https://postman-echo.com/get",
            "args":[]
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'Process Request; GET method type',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "get",
                "url" => "https://postman-echo.com/get?p1=A&P2=b"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = json_decode($result['response'],true);
        $expected = '{
            "url":"https://postman-echo.com/get?p1=A&P2=b",
            "args":{
                "p1":"A",
                "P2":"b"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'Process Request; GET method type with query params',  $actual, $expected, $results);
    }
}

