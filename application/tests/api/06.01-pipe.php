<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-10-23
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
        // ENDPOINT: POS /:userid/pipes


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/api/v2';
        $userid = \Flexio\Tests\Util::getDefaultTestUser();
        $token = \Flexio\Tests\Util::getDefaultTestUserToken();


        // TEST: create a new pipe

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid/pipes",
            // 'token' => '', // don't include a token
            'content_type' => 'application/json',
            'params' => '{
                "name": "Test Pipe"
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
        \Flexio\Tests\Check::assertInArray('A.1', 'POST /:userid/pipes; fail if requesting user doesn\'t have credentials',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid/pipes",
            'token' => $token,
            'content_type' => 'application/json',
            'params' => '{
                "name": "Test Pipe",
                "ename": "",
                "description": "Test Pipe Description"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid_type": "PIP",
            "eid_status": "A",
            "ename": "",
            "name": "Test Pipe",
            "description": "Test Pipe Description",
            "owned_by": {
                "eid": "'.$userid.'",
                "eid_type": "USR"
            }
        }
        ';
        \Flexio\Tests\Check::assertInArray('A.2', 'POST /:userid/pipes; create a new pipe',  $actual, $expected, $results);
    }
}
