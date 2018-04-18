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


        // TEST: execute task basic functionality

        // BEGIN TEST
        $script = <<<EOD
def flexio_handler(context):
    context.output.content_type = "text/plain"
    context.output.write("Hello, World!")
EOD;
        $result = \Flexio\Tests\Util::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/pipes",
            'token' => $token,
            'content_type' => 'application/json',
            'params' => '{
                "task": {
                    "op": "execute",
                    "params": {
                        "lang": "python",
                        "code": "'.base64_encode($script).'"
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
            "response": "Hello, World!"
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'POST /:userid/pipes/:objeid/run; (python) execute task basic functionality',  $actual, $expected, $results);

        // BEGIN TEST
        $script = <<<EOD
exports.flexio_handler = function(context) {
    context.output.content_type = "text/plain"
    context.output.write('Hello, World!')
}
EOD;
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
                        "code": "'.base64_encode($script).'"
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
            "response": "Hello, World!"
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'POST /:userid/pipes/:objeid/run; (javascript) execute task basic functionality',  $actual, $expected, $results);
    }
}

