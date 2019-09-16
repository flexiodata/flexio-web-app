<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
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
        // ENDPOINT: POST /:teamid/connections


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/v1';
        $password1 = \Flexio\Base\Password::generate();
        $userid1 = \Flexio\Tests\Util::createUser(null, null, $password1);
        $token1 = \Flexio\Tests\Util::createToken($userid1);
        $password2 = \Flexio\Base\Password::generate();
        $userid2 = \Flexio\Tests\Util::createUser(null, null, $password2);
        $token2 = \Flexio\Tests\Util::createToken($userid2);


        // TEST: create a new connection

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/connections",
            // 'token' => '', // no token included
            'content_type' => 'application/json',
            'params' => '{
                "name": "test-connection"
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
        \Flexio\Tests\Check::assertInArray('A.1', 'POST /:teamid/connections; fail if requesting user doesn\'t have credentials',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/connections",
            'token' => $token2, // token for another user
            'content_type' => 'application/json',
            'params' => '{
                "name": "test-connection"
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
        \Flexio\Tests\Check::assertInArray('A.2', 'POST /:teamid/connections; fail if requesting user doesn\'t have rights',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/connections",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "name1",
                "title": "Test Connection Title",
                "description": "Test Connection Description",
                "connection_type": "'.\Flexio\Services\Factory::TYPE_HTTP.'",
                "connection_mode": "'.\Model::CONNECTION_MODE_RESOURCE.'",
                "connection_status": "'.\Model::CONNECTION_STATUS_AVAILABLE.'",
                "connection_info": {
                    "host": "https://api.domain.com",
                    "port": 443,
                    "username": "default",
                    "password": "default"
                },
                "setup_config": {
                    "param1": "value1",
                    "param2": "value2"
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
            "name": "name1",
            "title": "Test Connection Title",
            "description": "Test Connection Description",
            "connection_type": "http",
            "connection_mode": "R",
            "connection_status": "A",
            "connection_info": {
                "host": "https://api.domain.com",
                "port": 443,
                "username": "default",
                "password": "*****"
            },
            "setup_config": {
                "param1": "value1",
                "param2": "value2"
            },
            "expires": null,
            "owned_by": {
                "eid": "'.$userid1.'",
                "eid_type": "USR"
            }
        }
        ';
        \Flexio\Tests\Check::assertInArray('A.3', 'POST /:teamid/connections; create a new connection',  $actual, $expected, $results);
    }
}
