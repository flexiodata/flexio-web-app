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
        // ENDPOINT: POST /:userid/pipes/:objeid/run


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/api/v2';
        $password = \Flexio\Base\Password::generate();
        $userid = \Flexio\Tests\Util::createUser(null, null, $password);
        $token = \Flexio\Tests\Util::createToken($userid);


        // TEST: execute task sha256 integrity check

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.output.content_type='text/plain';context.output.write('Hello,World!');}";
        $result = \Flexio\Tests\Util::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/pipes",
            'token' => $token,
            'content_type' => 'application/json',
            'params' => '{
                "task": {
                    "op": "execute",
                    "params": {
                        "lang": "javascript",
                        "code": "'.base64_encode($script).'",
                        "integrity": "sha256:91898ffd8d03ac47e045cbcd60d1c2133df7b6b9c845d87094ec69196cf39119"
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
        $expected = '{
            "code": 200,
            "content_type": "text\/plain;charset=UTF-8",
            "response": "Hello,World!"
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'POST /:userid/pipes/:objeid/run; execute task with lowercase sha256 integrity check',  $actual, $expected, $results);

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.output.content_type='text/plain';context.output.write('Hello,World!');}";
        $result = \Flexio\Tests\Util::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/pipes",
            'token' => $token,
            'content_type' => 'application/json',
            'params' => '{
                "task": {
                    "op": "execute",
                    "params": {
                        "lang": "javascript",
                        "code": "'.base64_encode($script).'",
                        "integrity": "sha256:91898FFD8D03AC47E045CBCD60D1C2133DF7B6B9C845D87094EC69196CF39119"
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
        $expected = '{
            "code": 200,
            "content_type": "text\/plain;charset=UTF-8",
            "response": "Hello,World!"
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'POST /:userid/pipes/:objeid/run; execute task with uppercase sha256 integrity check',  $actual, $expected, $results);

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.output.content_type='text/plain';context.output.write('Hello,World!');}";
        $result = \Flexio\Tests\Util::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/pipes",
            'token' => $token,
            'content_type' => 'application/json',
            'params' => '{
                "task": {
                    "op": "execute",
                    "params": {
                        "lang": "javascript",
                        "code": "'.base64_encode($script).'",
                        "integrity": "sha256:9beee4ffd5906d126ac456b262c5f6fac718062bab269886149f73773a31d9b7"
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
        \Flexio\Tests\Check::assertInArray('A.3', 'POST /:userid/pipes/:objeid/run; execute task with sha256 integrity failure',  $actual, $expected, $results);

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.output.content_type='text/plain';context.output.write('Hello,World!');}";
        $result = \Flexio\Tests\Util::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/pipes",
            'token' => $token,
            'content_type' => 'application/json',
            'params' => '{
                "task": {
                    "op": "execute",
                    "params": {
                        "lang": "javascript",
                        "code": "'.base64_encode($script).'",
                        "integrity": "sha512:9beee4ffd5906d126ac456b262c5f6fac718062bab269886149f73773a31d9b7"
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
        \Flexio\Tests\Check::assertInArray('A.4', 'POST /:userid/pipes/:objeid/run; execute task with sha256 format (sha512 indicated) integrity failure',  $actual, $expected, $results);

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.output.content_type='text/plain';context.output.write('Hello,World!');}";
        $result = \Flexio\Tests\Util::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/pipes",
            'token' => $token,
            'content_type' => 'application/json',
            'params' => '{
                "task": {
                    "op": "execute",
                    "params": {
                        "lang": "javascript",
                        "code": "'.base64_encode($script).'",
                        "integrity": "md5-a223b94fdf072f085adf67a4310abb59"
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
        $response = json_decode($result['response'],true);
        $actual = $result;
        $actual['response'] = $response;
        $expected = '{
            "code": 404,
            "content_type": "application/json",
            "response": {
                "error": {
                    "code": "invalid-parameter",
                    "message": "Invalid parameter"
                }
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.5', 'POST /:userid/pipes/:objeid/run; execute task with md5 integrity failure; md5 not supported',  $actual, $expected, $results);



        // TEST: execute task sha384 integrity check

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.output.content_type='text/plain';context.output.write('Hello,World!');}";
        $result = \Flexio\Tests\Util::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/pipes",
            'token' => $token,
            'content_type' => 'application/json',
            'params' => '{
                "task": {
                    "op": "execute",
                    "params": {
                        "lang": "javascript",
                        "code": "'.base64_encode($script).'",
                        "integrity": "sha384:c72e2df773b8c4117b19e3ced4c7e30582a5a75f93837632287b5fb65e0c9c393435c4d816f6d24054148f099b43be23"
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
        $expected = '{
            "code": 200,
            "content_type": "text\/plain;charset=UTF-8",
            "response": "Hello,World!"
        }';
        \Flexio\Tests\Check::assertInArray('B.1', 'POST /:userid/pipes/:objeid/run; execute task with sha384 integrity check',  $actual, $expected, $results);

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.output.content_type='text/plain';context.output.write('Hello,You!');}";
        $result = \Flexio\Tests\Util::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/pipes",
            'token' => $token,
            'content_type' => 'application/json',
            'params' => '{
                "task": {
                    "op": "execute",
                    "params": {
                        "lang": "javascript",
                        "code": "'.base64_encode($script).'",
                        "integrity": "sha384:c72e2df773b8c4117b19e3ced4c7e30582a5a75f93837632287b5fb65e0c9c393435c4d816f6d24054148f099b43be23"
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
        \Flexio\Tests\Check::assertInArray('B.2', 'POST /:userid/pipes/:objeid/run; execute task with sha384 integrity failure',  $actual, $expected, $results);


        // TEST: execute task sha512 integrity check

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.output.content_type='text/plain';context.output.write('Hello,World!');}";
        $result = \Flexio\Tests\Util::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/pipes",
            'token' => $token,
            'content_type' => 'application/json',
            'params' => '{
                "task": {
                    "op": "execute",
                    "params": {
                        "lang": "javascript",
                        "code": "'.base64_encode($script).'",
                        "integrity": "sha512:c43a28e60208e274d88bfd4a25d219854e921a78c68395c2d1b7ee5a65da2d057f82cd266b5ae5fde7740b3e9e175e0802633b0844b34366bb3b0eef9d165a1d"
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
        $expected = '{
            "code": 200,
            "content_type": "text\/plain;charset=UTF-8",
            "response": "Hello,World!"
        }';
        \Flexio\Tests\Check::assertInArray('B.1', 'POST /:userid/pipes/:objeid/run; execute task with sha512 integrity check',  $actual, $expected, $results);

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.output.content_type='text/plain';context.output.write('Hello,You!');}";
        $result = \Flexio\Tests\Util::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/pipes",
            'token' => $token,
            'content_type' => 'application/json',
            'params' => '{
                "task": {
                    "op": "execute",
                    "params": {
                        "lang": "javascript",
                        "code": "'.base64_encode($script).'",
                        "integrity": "sha512:c43a28e60208e274d88bfd4a25d219854e921a78c68395c2d1b7ee5a65da2d057f82cd266b5ae5fde7740b3e9e175e0802633b0844b34366bb3b0eef9d165a1d"
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
        \Flexio\Tests\Check::assertInArray('B.2', 'POST /:userid/pipes/:objeid/run; execute task with sha512 integrity failure',  $actual, $expected, $results);
    }
}

