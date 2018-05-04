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
        // ENDPOINT: POST /:userid/processes/:objeid/run


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/v1';
        $password = \Flexio\Base\Password::generate();
        $userid = \Flexio\Tests\Util::createUser(null, null, $password);
        $token = \Flexio\Tests\Util::createToken($userid);


        // TEST: undefined task

        // BEGIN TEST
        $tasks = json_decode('[{
        }]',true);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $tasks);
        $response = json_decode($result['response'],true);
        $actual = $result;
        $actual['response'] = $response;
        $expected = '{
            "code": 404,
            "content_type": "application/json",
            "response": {
                "error": {
                    "code": "missing-parameter",
                    "message": "Missing operation \'op\' task parameter"
                }
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'POST /:userid/processes/:objeid/run; return error for missing \'op\' task parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $tasks = json_decode('[{
            "op": true
        }]',true);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $tasks);
        $response = json_decode($result['response'],true);
        $actual = $result;
        $actual['response'] = $response;
        $expected = '{
            "code": 404,
            "content_type": "application/json",
            "response": {
                "error": {
                    "code": "invalid-parameter",
                    "message": "Invalid operation \'op\' task parameter"
                }
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'POST /:userid/processes/:objeid/run; return error for invalid \'op\' task parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $tasks = json_decode('[{
            "op": "undefined"
        }]',true);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $tasks);
        $response = json_decode($result['response'],true);
        $actual = $result;
        $actual['response'] = $response;
        $expected = '{
            "code": 404,
            "content_type": "application/json",
            "response": {
                "error": {
                    "code": "invalid-parameter",
                    "message": "Invalid operation \'op\' task parameter"
                }
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.3', 'POST /:userid/processes/:objeid/run; return error for invalid \'op\' task parameter',  $actual, $expected, $results);
    }
}

