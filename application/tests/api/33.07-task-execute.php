<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-09-20
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


        // TEST: execute execution without handler

        // BEGIN TEST
        $script = <<<EOD
print('Hello, World!')
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
            "response": "Hello, World!\n"
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'Process Execute; (python) execute task print to stdout',  $actual, $expected, $results);

        // BEGIN TEST
        $script = <<<EOD
import sys
sys.stdout.write('Hello, World!')
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
            "response": "Hello, World!"
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'Process Execute; (python) execute task writing to stdout',  $actual, $expected, $results);

        // BEGIN TEST
        $script = <<<EOD
Console.log('Hello, World!')
EOD;
        $task = \Flexio\Tests\Task::create([
            ["op" => "execute", "lang" => "nodejs", "code" => base64_encode($script)]
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
        $response = json_decode($result['response'],true);
        $actual = $result;
        $actual['response'] = $response;
        $expected = '{
            "code": 400,
            "content_type": "application/json",
            "response": {
                "error": {
                    "code": "invalid-syntax"
                }
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.3', 'Process Execute; (nodejs) in nodejs, a handler is needed to shut down the script',  $actual, $expected, $results);
    }
}

