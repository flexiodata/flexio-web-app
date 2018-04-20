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
        $apibase = \Flexio\Tests\Util::getTestHost() . '/v1';
        $password = \Flexio\Base\Password::generate();
        $userid = \Flexio\Tests\Util::createUser(null, null, $password);
        $token = \Flexio\Tests\Util::createToken($userid);


        // TEST: execute task context post parameters routed to context form

        // BEGIN TEST
        $script = <<<EOD
from collections import OrderedDict
def flexio_handler(context):
    params = OrderedDict(sorted(context.form.items()))
    for p in params:
        context.output.write(str(p) + ":" + str(params[p]) + ";")
    context.output.content_type = "text/plain"
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
            'token' => $token,
            'params' => array(
                'param1' => 'a',
                'param3' => 'c',
                'param2' => 'b'
            )
        ));
        $actual = $result;
        $expected = '{
            "code": 200,
            "content_type": "text\/plain;charset=UTF-8",
            "response": "param1:a;param2:b;param3:c;"
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'POST /:userid/pipes/:objeid/run; (python) execute task context post parameters routed to context form',  $actual, $expected, $results);

        // BEGIN TEST
        $script = <<<EOD
exports.flexio_handler = function(context) {
    params = context.form;
    keys = Object.keys(params).sort();
    for (k in keys) {
        var p = keys[k];
        context.output.write(p + ":" + params[p] + ";");
    }
    context.output.content_type = "text/plain";
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
            'token' => $token,
            'params' => array(
                'param1' => 'a',
                'param3' => 'c',
                'param2' => 'b'
            )
        ));
        $actual = $result;
        $expected = '{
            "code": 200,
            "content_type": "text\/plain;charset=UTF-8",
            "response": "param1:a;param2:b;param3:c;"
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'POST /:userid/pipes/:objeid/run; (python) execute task context post parameters routed to context form',  $actual, $expected, $results);


        // TEST: execute task context post parameters routed to context input

        // BEGIN TEST
        $script = <<<EOD
import json
def flexio_handler(context):
    for row in context.input:
        context.output.write(row)
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
            'token' => $token,
            'content_type' => 'application/json',
            'params' => json_encode(array(
                'param1' => 'a',
                'param2' => 'b',
                'param3' => 'c'
            ))
        ));
        $actual = $result;
        $expected = '{
            "code": 200,
            "content_type": "text\/plain;charset=UTF-8",
            "response": "param1:a;param2:b;param3:c;"
        }';
        \Flexio\Tests\Check::assertInArray('B.1', 'POST /:userid/pipes/:objeid/run; (javascript) execute task context post parameters routed to context input',  $actual, $expected, $results);

        // BEGIN TEST
        $script = <<<EOD
exports.flexio_handler = function(context) {
    var json = context.input.read();
    var obj = JSON.parse(json);
    for (key in obj) {
        context.output.write(key+":"+obj[key]+";");
    }
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
            'token' => $token,
            'content_type' => 'application/json',
            'params' => json_encode(array(
                'param1' => 'a',
                'param2' => 'b',
                'param3' => 'c'
            ))
        ));
        $actual = $result;
        $expected = '{
            "code": 200,
            "content_type": "text\/plain;charset=UTF-8",
            "response": "param1:a;param2:b;param3:c;"
        }';
        \Flexio\Tests\Check::assertInArray('B.2', 'POST /:userid/pipes/:objeid/run; (javascript) execute task context post parameters routed to context input',  $actual, $expected, $results);
    }
}

