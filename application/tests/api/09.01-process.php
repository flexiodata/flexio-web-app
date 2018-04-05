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
        $apibase = \Flexio\Tests\Util::getTestHost() . '/api/v2';
        $userid = \Flexio\Tests\Util::createUser();
        $token = \Flexio\Tests\Util::createToken($userid);


        // TEST: create a new process

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid/processes",
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
            'url' => "$apibase/$userid/processes",
            'token' => $token,
            'content_type' => 'application/json',
            'params' => '{
                "process_mode": "'.\Flexio\Jobs\Process::MODE_RUN.'",
                "task": {
                    "op": "request",
                    "params": {
                        "url": "https://api.domain.com"
                    }
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
            "parent" : {
                "eid" : "",
                "eid_type" : "PIP"
            },
            "process_mode": "R",
            "task": {
                "op": "request",
                "params": {
                    "url": "https://api.domain.com"
                }
            },
            "started_by": "",
            "started": null,
            "finished": null,
            "duration": null,
            "process_info": {
            },
            "process_status": "S",
            "owned_by": {
                "eid": "'.$userid.'",
                "eid_type": "USR"
            }
        }
        ';
        \Flexio\Tests\Check::assertInArray('A.2', 'POST /:userid/processes; create a new process',  $actual, $expected, $results);
    }
}
