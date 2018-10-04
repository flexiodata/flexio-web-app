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


        // TEST: execute task sha256 integrity check

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.end('Hello,World!');}";
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "execute",
                "lang" => "nodejs",
                "code" => base64_encode($script),
                "integrity" => "sha256:5fdf4d934018537db16a8e5cd24d84edb7e6efaee155ea5219d48c55e7b06b27"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = $result;
        $expected = '{
            "code": 200,
            "content_type": "text/plain;charset=UTF-8",
            "response": "Hello,World!"
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'Process Execute; (nodejs) execute task with lowercase sha256 integrity check',  $actual, $expected, $results);

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.end('Hello,World!');}";
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "execute",
                "lang" => "nodejs",
                "code" => base64_encode($script),
                "integrity" => "sha256:5FDF4D934018537DB16A8E5CD24D84EDB7E6EFAEE155EA5219D48C55E7B06B27"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = $result;
        $expected = '{
            "code": 200,
            "content_type": "text/plain;charset=UTF-8",
            "response": "Hello,World!"
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'Process Execute; (nodejs) execute task with uppercase sha256 integrity check',  $actual, $expected, $results);

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.end('Hello,World!');}";
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "execute",
                "lang" => "nodejs",
                "code" => base64_encode($script),
                "integrity" => "sha256:9beee4ffd5906d126ac456b262c5f6fac718062bab269886149f73773a31d9b7"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $response = json_decode($result['response'],true);
        $actual = $result;
        $actual['response'] = $response;
        $expected = '{
            "code": 422,
            "content_type": "application/json",
            "response": {
                "error": {
                    "code": "integrity-failed",
                    "message": "Integrity check failed"
                }
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.3', 'Process Execute; (nodejs) execute task with sha256 integrity failure',  $actual, $expected, $results);

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.end('Hello,World!');}";
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "execute",
                "lang" => "nodejs",
                "code" => base64_encode($script),
                "integrity" => "sha512:9beee4ffd5906d126ac456b262c5f6fac718062bab269886149f73773a31d9b7"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $response = json_decode($result['response'],true);
        $actual = $result;
        $actual['response'] = $response;
        $expected = '{
            "code": 422,
            "content_type": "application/json",
            "response": {
                "error": {
                    "code": "integrity-failed",
                    "message": "Integrity check failed"
                }
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.4', 'Process Execute; (nodejs) execute task with sha256 format (sha512 indicated) integrity failure',  $actual, $expected, $results);

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.end('Hello,World!');}";
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "execute",
                "lang" => "nodejs",
                "code" => base64_encode($script),
                "integrity" => "md5-a223b94fdf072f085adf67a4310abb59"
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
        \Flexio\Tests\Check::assertInArray('A.5', 'Process Execute; (nodejs) execute task with md5 integrity failure; md5 not supported',  $actual, $expected, $results);


        // TEST: execute task sha384 integrity check

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.end('Hello,World!');}";
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "execute",
                "lang" => "nodejs",
                "code" => base64_encode($script),
                "integrity" => "sha384:703eeae35c1281c445575d9326f0ab03b7e84886fd54f782ea17c7dccbf9ada9a792222356d752d7792f993e6326531a"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = $result;
        $expected = '{
            "code": 200,
            "content_type": "text/plain;charset=UTF-8",
            "response": "Hello,World!"
        }';
        \Flexio\Tests\Check::assertInArray('B.1', 'Process Execute; (nodejs) execute task with sha384 integrity check',  $actual, $expected, $results);

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.end('Hello,You!');}";
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "execute",
                "lang" => "nodejs",
                "code" => base64_encode($script),
                "integrity" => "sha384:703eeae35c1281c445575d9326f0ab03b7e84886fd54f782ea17c7dccbf9ada9a792222356d752d7792f993e6326531b"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $response = json_decode($result['response'],true);
        $actual = $result;
        $actual['response'] = $response;
        $expected = '{
            "code": 422,
            "content_type": "application/json",
            "response": {
                "error": {
                    "code": "integrity-failed",
                    "message": "Integrity check failed"
                }
            }
        }';
        \Flexio\Tests\Check::assertInArray('B.2', 'Process Execute; (nodejs) execute task with sha384 integrity failure',  $actual, $expected, $results);


        // TEST: execute task sha512 integrity check

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.end('Hello,World!');}";
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "execute",
                "lang" => "nodejs",
                "code" => base64_encode($script),
                "integrity" => "sha512:E9C5660CBD8BACBAFFE6B237A65B8C8A7FAE9EA0DB07EDAEBEF7CFCE8499F6431C2AF750714201FE559F68259A744B8EB9B5EDB17773F2B3D72392C03B51AF5A"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = $result;
        $expected = '{
            "code": 200,
            "content_type": "text/plain;charset=UTF-8",
            "response": "Hello,World!"
        }';
        \Flexio\Tests\Check::assertInArray('C.1', 'Process Execute; (nodejs) execute task with sha512 integrity check',  $actual, $expected, $results);

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.end('Hello,You!');}";
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "execute",
                "lang" => "nodejs",
                "code" => base64_encode($script),
                "integrity" => "sha512:E9C5660CBD8BACBAFFE6B237A65B8C8A7FAE9EA0DB07EDAEBEF7CFCE8499F6431C2AF750714201FE559F68259A744B8EB9B5EDB17773F2B3D72392C03B51AF5B"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $response = json_decode($result['response'],true);
        $actual = $result;
        $actual['response'] = $response;
        $expected = '{
            "code": 422,
            "content_type": "application/json",
            "response": {
                "error": {
                    "code": "integrity-failed",
                    "message": "Integrity check failed"
                }
            }
        }';
        \Flexio\Tests\Check::assertInArray('C.2', 'Process Execute; (nodejs) execute task with sha512 integrity failure',  $actual, $expected, $results);
    }
}

