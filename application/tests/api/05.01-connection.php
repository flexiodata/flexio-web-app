<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-06-27
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
        // ENDPOINT: POS /:userid/connections


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/api/v2';
        $userid = \Flexio\Tests\Util::getDefaultTestUser();
        $token = \Flexio\Tests\Util::getDefaultTestUserToken();


        // TEST: create a new connection

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid/connections",
            'content_type' => 'application/json',
            // 'token' => '', // don't include a token
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
        \Flexio\Tests\Check::assertInArray('A.1', 'POS /:userid/connections; fail if requesting user doesn\'t have credentials',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid/connections",
            'token' => $token,
            'content_type' => 'application/json',
            'params' => '{
                "name": "Test Connection",
                "ename": "",
                "description": "Test Connection Description",
                "connection_type": "'.\Flexio\Services\Factory::TYPE_HTTP.'",
                "connection_status": "'.\Model::CONNECTION_STATUS_AVAILABLE.'",
                "connection_info": {
                    "host": "https://api.domain.com",
                    "port": 443,
                    "username": "default",
                    "password": "default"
                },
                "expires": null
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid_type": "CTN",
            "eid_status": "A",
            "ename": "",
            "name": "Test Connection",
            "description": "Test Connection Description",
            "connection_type": "http",
            "connection_status": "A",
            "connection_info": {
                "host": "https://api.domain.com",
                "port": 443,
                "username": "default",
                "password": "*****"
            },
            "expires": null,
            "owned_by": {
                "eid": "'.$userid.'",
                "eid_type": "USR"
            }
        }
        ';
        \Flexio\Tests\Check::assertInArray('A.2', 'POS /:userid/connections; basic connection creation',  $actual, $expected, $results);
    }
}
