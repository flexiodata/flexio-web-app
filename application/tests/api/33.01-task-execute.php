<?php
/**
 *
 * Copyright (c) 2018, Flex Research LLC. All rights reserved.
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
        // ENDPOINT: POST /:teamid/processes/:objeid/run


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/v1';
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
        $task = \Flexio\Tests\Task::create([
            ["op" => "execute", "lang" => "python", "code" => base64_encode($script)]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = $result;
        $expected = '{
            "code": 200,
            "content_type": "text/plain;charset=UTF-8",
            "response": "Hello, World!"
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'Process Execute; (python) execute task basic functionality',  $actual, $expected, $results);

        // BEGIN TEST
        $script = <<<EOD
exports.flexio_handler = function(context) {
    context.output.content_type = "text/plain";
    context.output.write('Hello, World!');
    context.end();
}
EOD;
        $task = \Flexio\Tests\Task::create([
            ["op" => "execute", "lang" => "nodejs", "code" => base64_encode($script)]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = $result;
        $expected = '{
            "code": 200,
            "content_type": "text/plain;charset=UTF-8",
            "response": "Hello, World!"
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'Process Execute; (nodejs) execute task basic functionality',  $actual, $expected, $results);
    }
}

