<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-04-05
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
        // ENDPOINT: POST /:teamid/connections/:objeid


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/v1';
        $password1 = \Flexio\Base\Password::generate();
        $userid1 = \Flexio\Tests\Util::createUser(null, null, $password1);
        $token1 = \Flexio\Tests\Util::createToken($userid1);
        $password2 = \Flexio\Base\Password::generate();
        $userid2 = \Flexio\Tests\Util::createUser(null, null, $password2);
        $token2 = \Flexio\Tests\Util::createToken($userid2);

        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/connections",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "name1",
                "title": "Test Connection"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = json_decode($result['response'],true);
        $objeid1 = $response['eid'] ?? '';

        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid2/connections",
            'token' => $token2,
            'content_type' => 'application/json',
            'params' => '{
                "name": "name2",
                "title": "Test Connection"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = json_decode($result['response'],true);
        $objeid2 = $response['eid'] ?? '';

        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/connections",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "name3",
                "title": "Test Connection"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = json_decode($result['response'],true);
        $objeid3 = $response['eid'] ?? '';


        // TEST: change connection info

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/connections/$objeid1",
            // 'token' => '', // no token included
            'content_type' => 'application/json',
            'params' => '{
                "title": "Updated Test Connection"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code": "insufficient-rights"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'POST /:teamid/connections/:objeid; fail if requesting user doesn\'t have credentials',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/connections/$objeid2",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "title": "Updated Test Connection"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code": "unavailable"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'POST /:teamid/connections/:objeid; fail if object isn\'t owned by specified owner',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/connections/$objeid1",
            'token' => $token2, // token for another user
            'content_type' => 'application/json',
            'params' => '{
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code": "insufficient-rights"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.3', 'POST /:teamid/connections/:objeid; fail if requesting user doesn\'t have rights',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/connections/$objeid1",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "name3"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code": "write-failed"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.4', 'POST /:teamid/connections/:objeid; fail if name already exists',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/connections/$objeid1",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "name2"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "CTN",
            "eid_status": "A",
            "name": "name2"
        }';
        \Flexio\Tests\Check::assertInArray('A.5', 'POST /:teamid/connections/:objeid; make sure unique name only applies within an owner',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/connections/$objeid1",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "name1"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "CTN",
            "eid_status": "A",
            "name": "name1"
        }';
        \Flexio\Tests\Check::assertInArray('A.6', 'POST /:teamid/connections/:objeid; allow name to be set to what it already is',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/connections/$objeid1",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "another-name"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "CTN",
            "eid_status": "A",
            "name": "another-name"
        }';
        \Flexio\Tests\Check::assertInArray('A.7', 'POST /:teamid/connections/:objeid; allow name to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $new_username = \Flexio\Base\Identifier::generate();
        $new_password = \Flexio\Base\Password::generate();
        $new_email = \Flexio\Tests\Util::createEmailAddress();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/connections/$objeid1",
            'token' => $token1, // valid token for user
            'content_type' => 'application/json',
            'params' => '{
                "name": "name1-updated",
                "title": "Test Connection Updated",
                "description": "Test Connection Description Updated",
                "connection_type": "'.\Flexio\Services\Factory::TYPE_HTTP.'",
                "connection_mode": "'.\Model::CONNECTION_MODE_FUNCTION.'",
                "connection_status": "'.\Model::CONNECTION_STATUS_AVAILABLE.'",
                "connection_info": {
                    "host": "https://api.domain.com",
                    "port": 443,
                    "username": "default",
                    "password": "default"
                },
                "expires": "2018-01-02 03:04:05"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "CTN",
            "eid_status": "A",
            "name": "name1-updated",
            "title": "Test Connection Updated",
            "description": "Test Connection Description Updated",
            "connection_type": "http",
            "connection_mode": "F",
            "connection_status": "A",
            "connection_info": {
                "host": "https://api.domain.com",
                "port": 443,
                "username": "default",
                "password": "*****"
            },
            "expires": null,
            "owned_by": {
                "eid": "'.$userid1.'",
                "eid_type": "USR"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.8', 'POST /:teamid/connections/:objeid; return updated connection info; don\'t allow \'expires\' to be set from api for now',  $actual, $expected, $results);
    }
}
