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


        // TEST: execute task default variable keys

        // BEGIN TEST
        $script = <<<EOD
from collections import OrderedDict
def flexio_handler(context):
    vars = OrderedDict(sorted(context.vars.items()))
    for v in vars:
        context.output.write(str(v) + ";")
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
            'token' => $token
        ));
        $actual = $result;
        $expected = '{
            "code": 200,
            "content_type": "text\/plain;charset=UTF-8",
            "response": "process.time.started;process.time.unix;process.user.email;process.user.firstname;process.user.lastname;"
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'POST /:userid/pipes/:objeid/run; (python) execute task default variable keys',  $actual, $expected, $results);

        // BEGIN TEST
        $script = <<<EOD
exports.flexio_handler = function(context) {
    vars = context.vars;
    keys = Object.keys(vars).sort();
    for (k in keys) {
        var p = keys[k];
        context.output.write(p + ";");
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
            'token' => $token
        ));
        $actual = $result;
        $expected = '{
            "code": 200,
            "content_type": "text\/plain;charset=UTF-8",
            "response": "process.time.started;process.time.unix;process.user.email;process.user.firstname;process.user.lastname;"
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'POST /:userid/pipes/:objeid/run; (javascript) execute task default variable keys',  $actual, $expected, $results);
    }
}

