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
        $task = \Flexio\Tests\Task::create([
            ["op" => "execute", "lang" => "python", "code" => base64_encode($script)]
        ]);
        $result = \Flexio\Tests\Util::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/processes",
            'token' => $token,
            'content_type' => 'application/json',
            'params' => json_encode(["task" => $task])
        ));
        $response = json_decode($result['response'],true);
        $objeid = $response['eid'] ?? '';
        $result = \Flexio\Tests\Util::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/processes/$objeid/run",
            'token' => $token
        ));
        $actual = $result;
        $expected = '{
            "code": 200,
            "content_type": "text/plain;charset=UTF-8",
            "response": "process.time.started;process.time.unix;process.user.email;process.user.firstname;process.user.lastname;"
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'Process Execute; (python) execute task default variable keys',  $actual, $expected, $results);

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
        $task = \Flexio\Tests\Task::create([
            ["op" => "execute", "lang" => "javascript", "code" => base64_encode($script)]
        ]);
        $result = \Flexio\Tests\Util::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/processes",
            'token' => $token,
            'content_type' => 'application/json',
            'params' => json_encode(["task" => $task])
        ));
        $response = json_decode($result['response'],true);
        $objeid = $response['eid'] ?? '';
        $result = \Flexio\Tests\Util::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/processes/$objeid/run",
            'token' => $token
        ));
        $actual = $result;
        $expected = '{
            "code": 200,
            "content_type": "text/plain;charset=UTF-8",
            "response": "process.time.started;process.time.unix;process.user.email;process.user.firstname;process.user.lastname;"
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'Process Execute; (javascript) execute task default variable keys',  $actual, $expected, $results);


        // TEST: execute task basic variable assignment

        // BEGIN TEST
        $script = <<<EOD
from collections import OrderedDict
def flexio_handler(context):
    context.vars['b'] = 'c'
    context.vars['a'] = 'b'
    result = context.vars['a'] + context.vars['b'] + context.vars.b + context.vars.a
    context.output.write(result)
    context.output.content_type = "text/plain"
EOD;
        $task = \Flexio\Tests\Task::create([
            ["op" => "execute", "lang" => "python", "code" => base64_encode($script)]
        ]);
        $result = \Flexio\Tests\Util::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/processes",
            'token' => $token,
            'content_type' => 'application/json',
            'params' => json_encode(["task" => $task])
        ));
        $response = json_decode($result['response'],true);
        $objeid = $response['eid'] ?? '';
        $result = \Flexio\Tests\Util::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/processes/$objeid/run",
            'token' => $token
        ));
        $actual = $result;
        $expected = '{
            "code": 200,
            "content_type": "text/plain;charset=UTF-8",
            "response": "bccb"
        }';
        \Flexio\Tests\Check::assertInArray('B.1', 'Process Execute; (python) execute task default variable keys',  $actual, $expected, $results);

        // BEGIN TEST
        $script = <<<EOD
exports.flexio_handler = function(context) {
    context.vars['b'] = 'c'
    context.vars['a'] = 'b'
    result = context.vars['a'] + context.vars['b'] + context.vars.b + context.vars.a
    context.output.write(result)
    context.output.content_type = "text/plain";
}
EOD;
        $task = \Flexio\Tests\Task::create([
            ["op" => "execute", "lang" => "javascript", "code" => base64_encode($script)]
        ]);
        $result = \Flexio\Tests\Util::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/processes",
            'token' => $token,
            'content_type' => 'application/json',
            'params' => json_encode(["task" => $task])
        ));
        $response = json_decode($result['response'],true);
        $objeid = $response['eid'] ?? '';
        $result = \Flexio\Tests\Util::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/processes/$objeid/run",
            'token' => $token
        ));
        $actual = $result;
        $expected = '{
            "code": 200,
            "content_type": "text/plain;charset=UTF-8",
            "response": "bccb"
        }';
        \Flexio\Tests\Check::assertInArray('B.2', 'Process Execute; (javascript) execute task default variable keys',  $actual, $expected, $results);
    }
}

