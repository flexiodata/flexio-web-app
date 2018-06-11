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


        // TEST: request task method types

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "method" => "delete",
                "url" => "https://postman-echo.com/delete?p1=a&p2=b",
                "data" => [
                    "f1" => "a",
                    "f2" => "b"
                ]
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = json_decode($result['response'],true);
        $expected = '{
            "url":"https://postman-echo.com/delete?p1=a&p2=b",
            "args":{
                "p1":"a",
                "p2":"b"
            },
            "form":{
                "f1":"a",
                "f2":"b"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'Process Request; \'delete\' method type',  $actual, $expected, $results);
    }
}

