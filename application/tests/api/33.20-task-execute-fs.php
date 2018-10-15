<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2018-10-11
 *
 * @package flexio
 * @subpackage Tests
 */


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function runScript($script)
    {
        $apibase = \Flexio\Tests\Util::getTestHost() . '/v1';
        $password = \Flexio\Base\Password::generate();
        $userid = \Flexio\Tests\Util::createUser(null, null, $password);
        $token = \Flexio\Tests\Util::createToken($userid);


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
        return $result['response'] ?? 'MISSING_RESPONSE';
    }

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
import string
import random

def flex_handler(flex):
    rand = ''.join(random.choice(string.ascii_uppercase + string.digits) for _ in range(10))
    filename = rand
    value = rand.encode('utf-8')

    flex.fs.write(filename, value)
    check = flex.fs.read(filename)
    flex.output.write('passed' if check == value else 'failed')
EOD;

        $actual = $this->runScript($script);
        $expected = 'passed';
        \Flexio\Tests\Check::assertString('A.1', 'flex.fs.write() and flex.fs.read(), simple small value',  $actual, $expected, $results);



        // BEGIN TEST
        $script = <<<EOD
import string
import random

def flex_handler(flex):
    rand = ''.join(random.choice(string.ascii_uppercase + string.digits) for _ in range(10))
    filename = rand
    value = (rand*300000).encode('utf-8')

    flex.fs.write(filename, value)
    check = flex.fs.read(filename)
    flex.output.write('passed' if check == value else 'failed')
EOD;

        $actual = $this->runScript($script);
        $expected = 'passed';
        \Flexio\Tests\Check::assertString('A.2', 'flex.fs.write() and flex.fs.read(), large value (3 MB)',  $actual, $expected, $results);


        // BEGIN TEST
        $script = <<<EOD
import string
import random

def flex_handler(flex):
    rand = ''.join(random.choice(string.ascii_uppercase + string.digits) for _ in range(10))
    filename = rand
    value = rand.encode('utf-8')

    flex.fs.write(filename, value)

    f = flex.fs.open(filename)
    check = b''
    while True:
        c = f.read(1)
        check += c
        if len(c) == 0:
            break

    flex.output.write('passed' if check == value else 'failed')
EOD;

        $actual = $this->runScript($script);
        $expected = 'passed';
        \Flexio\Tests\Check::assertString('A.3', 'flex.fs.write() followed by flex.fs.open() with read loop',  $actual, $expected, $results);


        // BEGIN TEST
        $script = <<<EOD
import string
import random

def flex_handler(flex):
    rand = ''.join(random.choice(string.ascii_uppercase + string.digits) for _ in range(10))
    filename = rand
    value = (rand*300000).encode('utf-8')

    flex.fs.write(filename, value)

    f = flex.fs.open(filename)
    check = b''
    while True:
        c = f.read(10000)
        check += c
        if len(c) == 0:
            break

    flex.output.write('passed' if check == value else 'failed')
EOD;

        $actual = $this->runScript($script);
        $expected = 'passed';
        \Flexio\Tests\Check::assertString('A.4', 'flex.fs.write(), large value (3MB), followed by flex.fs.open() with read loop',  $actual, $expected, $results);



    }
}
