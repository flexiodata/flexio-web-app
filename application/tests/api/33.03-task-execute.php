<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
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


        // TEST: execute task with remote code

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "execute",
                "lang" => "python",
                "path" => "https://raw.githubusercontent.com/flexiodata/functions/master/python/hello-world.py"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = $result;
        $expected = '{
            "code": 200,
            "content_type": "text/plain;charset=UTF-8",
            "response": "Hello, World!"
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'Process Execute; (python) execute task with remote code',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "execute",
                "lang" => "python",
                "path" => "https://raw.githubusercontent.com/flexiodata/functions/master/python/hello-world.py",
                "integrity" => "sha256:891568494dfb8fce562955b1509aee5a1ce0ce05ae210da6556517dd3986de36"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = $result;
        $expected = '{
            "code": 200,
            "content_type": "text/plain;charset=UTF-8",
            "response": "Hello, World!"
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'Process Execute; (python) execute task with remote code and sha256 integrity check',  $actual, $expected, $results);


        // TEST: execute task with remote code, but local override

        // BEGIN TEST
        $script = <<<EOD
def flexio_handler(context):
    context.output.content_type = "text/plain"
    context.output.write("This is local.")
EOD;
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "execute",
                "lang" => "python",
                "code" => base64_encode($script),
                "path" => "https://raw.githubusercontent.com/flexiodata/functions/master/python/hello-world.py"
            ]
        ]);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        $actual = $result;
        $expected = '{
            "code": 200,
            "content_type": "text/plain;charset=UTF-8",
            "response": "This is local."
        }';
        \Flexio\Tests\Check::assertInArray('B.1', 'Process Execute; (python) execute task with remote code, but local override',  $actual, $expected, $results);
    }
}

