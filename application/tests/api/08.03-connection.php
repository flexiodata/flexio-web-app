<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-04-06
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
        // ENDPOINT: DEL /:userid/connections/:objeid


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
                "name": "Test Connection",
                "alias": "alias1"
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
                "name": "Test Connection",
                "alias": "alias2"
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
                "name": "Test Connection",
                "alias": "alias3"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = json_decode($result['response'],true);
        $objeid3 = $response['eid'] ?? '';


        // TEST: delete connection

        // BEGIN TEST
        $params = array(
            'method' => 'DELETE',
            'url' => "$apibase/$userid1/connections/$objeid1",
            // 'token' => '' // no token included
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code": "insufficient-rights"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'DELETE /:userid/connections/:objeid; fail if requesting user doesn\'t have credentials',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'DELETE',
            'url' => "$apibase/$userid1/connections/$objeid2",
            'token' => $token1
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code": "no-object"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'DELETE /:userid/connections/:objeid; fail if object isn\'t owned by specified owner',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'DELETE',
            'url' => "$apibase/$userid1/connections/$objeid1",
            'token' => $token2 // token for another user
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code": "insufficient-rights"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.3', 'DELETE /:userid/connections/:objeid; fail if requesting user doesn\'t have rights',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'DELETE',
            'url' => "$apibase/$userid1/connections/$objeid1",
            'token' => $token1
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "CTN",
            "eid_status": "D"
        }';
        \Flexio\Tests\Check::assertInArray('A.4', 'DELETE /:userid/connections/:objeid; delete connection',  $actual, $expected, $results);
        $params = array(
            'method' => 'GET',
            'url' => "$apibase/$userid1/connections/$objeid1",
            'token' => $token1
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code":"no-object",
                "message":"Object not available"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.5', 'DELETE /:userid/connections/:objeid; make sure a connection is deleted',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'DELETE',
            'url' => "$apibase/$userid1/connections/alias3",
            'token' => $token1
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid3.'",
            "eid_type": "CTN",
            "eid_status": "D"
        }';
        \Flexio\Tests\Check::assertInArray('A.6', 'DELETE /:userid/connections/:objeid; allow deletion by alias',  $actual, $expected, $results);
        $params = array(
            'method' => 'DELETE',
            'url' => "$apibase/$userid1/connections/alias3",
            'token' => $token1
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code":"no-object",
                "message":"Object not available"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.7', 'DELETE /:userid/connections/:objeid; make sure a connection is deleted',  $actual, $expected, $results);

        // BEGIN
        $unique_alias = \Flexio\Base\Identifier::generate();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/connections",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "Test Connection",
                "alias": "'.$unique_alias.'"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = json_decode($result['response'],true);
        $objeid1 = $response['eid'] ?? '';
        $params = array(
            'method' => 'DELETE',
            'url' => "$apibase/$userid1/connections/$unique_alias",
            'token' => $token1
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "CTN",
            "eid_status": "D"
        }';
        \Flexio\Tests\Check::assertInArray('A.8', 'DELETE /:userid/connections/:objeid; delete connection',  $actual, $expected, $results);
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/connections",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "Test Connection",
                "alias": "'.$unique_alias.'"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid_type": "CTN",
            "eid_status": "A",
            "alias": "'.$unique_alias.'"
        }';
        \Flexio\Tests\Check::assertInArray('A.9', 'DELETE /:userid/connections/:objeid; clear out alias when deleting a connection',  $actual, $expected, $results);
    }
}
