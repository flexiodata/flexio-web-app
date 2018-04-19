<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
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
        // ENDPOINT: POST /:userid/pipes/:objeid/run


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/api/v2';
        $password = \Flexio\Base\Password::generate();
        $userid = \Flexio\Tests\Util::createUser(null, null, $password);
        $token = \Flexio\Tests\Util::createToken($userid);


        // TEST: exit task basic task functionality

        // BEGIN TEST
        $result = \Flexio\Tests\Util::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/pipes",
            'token' => $token,
            'content_type' => 'application/json',
            'params' => '{
                "task": {
                    "op": "exit",
                    "params": {
                        "code": 404,
                        "response": {
                            "error": "item-not-found",
                            "message": "The item you requested was not found."
                        }
                    }
                }
            }'
        ));
        $response = json_decode($result['response'],true);
        $objeid = $response['eid'] ?? '';
        $result = \Flexio\Tests\Util::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/pipes/$objeid/run",
            'token' => $token
        ));
        $actual = $result;
        $expected = array(
            "code" => 404,
            "response" => json_encode(array(
                "error" => "item-not-found",
                "message" => "The item you requested was not found."
            ))
        );
        \Flexio\Tests\Check::assertInArray('A.1', 'POST /:userid/pipes/:objeid/run; exit task basic functionality',  $actual, $expected, $results);
    }
}

