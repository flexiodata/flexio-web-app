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


        // TEST: execute task context query parameters

        // BEGIN TEST
        $script = <<<EOD
from collections import OrderedDict
def flexio_handler(context):
    params = OrderedDict(sorted(context.query.items()))
    for p in params:
        context.output.write(p + ":" + params[p] + ";")
    context.output.content_type = "text/plain"
EOD;
        $result = \Flexio\Tests\Util::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/processes",
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
            'url' => "$apibase/$userid/processes/$objeid/run?param1=d&param3=c&param2=b&param1=a",
            'token' => $token
        ));
        $actual = $result;
        $expected = '{
            "code": 200,
            "content_type": "text\/plain;charset=UTF-8",
            "response": "param1:a;param2:b;param3:c;"
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'Process Execute; (python) execute task context query parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $script = <<<EOD
exports.flexio_handler = function(context) {
    params = context.query;
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
            'url' => "$apibase/$userid/processes",
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
            'url' => "$apibase/$userid/processes/$objeid/run?param1=d&param3=c&param2=b&param1=a",
            'token' => $token
        ));
        $actual = $result;
        $expected = '{
            "code": 200,
            "content_type": "text\/plain;charset=UTF-8",
            "response": "param1:a;param2:b;param3:c;"
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'Process Execute; (javascript) execute task context query parameters',  $actual, $expected, $results);
    }
}

