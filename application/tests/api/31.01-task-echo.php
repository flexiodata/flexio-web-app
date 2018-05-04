<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-04-17
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


        // TEST: echo task basic functionality

        // BEGIN TEST
        $tasks = json_decode('[{
            "op": "echo",
            "params": {
                "msg": "Hello, World!"
            }
        }]',true);
        $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $tasks);
        $actual = $result['response'];
        $expected = 'Hello, World!';
        \Flexio\Tests\Check::assertString('A.1', 'Process Echo; basic functionality',  $actual, $expected, $results);
    }
}

