<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-05-09
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
        // ENDPOINT: POST /:userid/processes


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/v1';
        $password1 = \Flexio\Base\Password::generate();
        $userid1 = \Flexio\Tests\Util::createUser(null, null, $password1);
        $token1 = \Flexio\Tests\Util::createToken($userid1);
        $password2 = \Flexio\Base\Password::generate();
        $userid2 = \Flexio\Tests\Util::createUser(null, null, $password2);
        $token2 = \Flexio\Tests\Util::createToken($userid2);


        // TEST: create a new process

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/processes",
            // 'token' => '', // no token included
            'content_type' => 'application/json',
            'params' => '{
                "name": "Test Connection"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error" : {
                "code": "insufficient-rights"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'POST /:userid/processes; fail if requesting user doesn\'t have credentials',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/processes",
            'token' => $token2, // token for another user
            'content_type' => 'application/json',
            'params' => '{
                "name": "Test Connection"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error" : {
                "code": "insufficient-rights"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'POST /:userid/processes; fail if requesting user doesn\'t have rights',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/processes",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "process_mode": "'.\Flexio\Jobs\Process::MODE_RUN.'",
                "task": {
                    "op": "echo"
                },
                "process_status": "'.\Flexio\Jobs\Process::STATUS_PENDING.'"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid_type": "PRC",
            "eid_status": "A",
            "parent": {
                "eid": "",
                "eid_type": "PIP"
            },
            "process_mode": "R",
            "task": {
                "op": "echo"
            },
            "started_by": "",
            "started": null,
            "finished": null,
            "duration": null,
            "process_info": {
            },
            "process_status": "S",
            "owned_by": {
                "eid": "'.$userid1.'",
                "eid_type": "USR"
            }
        }
        ';
        \Flexio\Tests\Check::assertInArray('A.3', 'POST /:userid/processes; create a new process',  $actual, $expected, $results);
    }
}
