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
        // ENDPOINT: POST /:userid/processes/:objeid/run


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/v1';
        $password = \Flexio\Base\Password::generate();
        $userid = \Flexio\Tests\Util::createUser(null, null, $password);
        $token = \Flexio\Tests\Util::createToken($userid);


        // TEST: exit task basic task functionality

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "exit",
                "code" => 404,
                "response" => [
                    "error" => "item-not-found",
                    "message" => "The item you requested was not found."
                ]
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = $result;
        $expected = array(
            "code" => 404,
            "response" => json_encode(array(
                "error" => "item-not-found",
                "message" => "The item you requested was not found."
            ))
        );
        \Flexio\Tests\Check::assertInArray('A.1', 'Process Exit; basic functionality',  $actual, $expected, $results);
    }
}

