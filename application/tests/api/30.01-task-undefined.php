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



        // TEST: run process with rights checks

        // BEGIN TEST
        $result = \Flexio\Tests\Util::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/pipes",
            'token' => $token,
            'content_type' => 'application/json',
            'params' => '{
                "task": {
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
        $code = $result['code'];
        $response = json_decode($result['response'],true);
        $actual = '{
            "code": '.$code.',
            "response": {
                "error": '.$response['error'].',
                "message": '.$response['message'].'
            }
        }';
        $expected = '{
            "code": 404,
            "response": {
                "error": "",
                "message": ""
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'POST /:userid/pipes/:objeid/run; return error for missing \'op\' task parameter',  $actual, $expected, $results);
return;
        // BEGIN TEST
        $result = \Flexio\Tests\Util::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/pipes",
            'token' => $token,
            'content_type' => 'application/json',
            'params' => '{
                "task": {
                    "op": true
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
                "error" => "invalid-parameter",
                "message" => "Invalid operation \'op\' task parameter"
            ),JSON_PRETTY_PRINT)
        );
        \Flexio\Tests\Check::assertInArray('A.2', 'POST /:userid/pipes/:objeid/run; return error for invalid \'op\' task parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $result = \Flexio\Tests\Util::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/pipes",
            'token' => $token,
            'content_type' => 'application/json',
            'params' => '{
                "task": {
                    "op": "undefined"
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
                "error" => "invalid-parameter",
                "message" => "Invalid operation \'op\' task parameter"
            ),JSON_PRETTY_PRINT)
        );
        \Flexio\Tests\Check::assertInArray('A.3', 'POST /:userid/pipes/:objeid/run; return error for invalid \'op\' task parameter',  $actual, $expected, $results);
    }
}

