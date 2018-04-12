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
        // ENDPOINT: DEL /:userid/pipes/:objeid


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/api/v2';
        $password1 = \Flexio\Base\Util::generatePassword();
        $userid1 = \Flexio\Tests\Util::createUser(null, null, $password1);
        $token1 = \Flexio\Tests\Util::createToken($userid1);
        $password2 = \Flexio\Base\Util::generatePassword();
        $userid2 = \Flexio\Tests\Util::createUser(null, null, $password2);
        $token2 = \Flexio\Tests\Util::createToken($userid2);

        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "Test Pipe",
                "alias": "alias1"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = json_decode($result['response'],true);
        $objeid1 = $response['eid'] ?? '';

        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid2/pipes",
            'token' => $token2,
            'content_type' => 'application/json',
            'params' => '{
                "name": "Test Pipe",
                "alias": "alias2"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = json_decode($result['response'],true);
        $objeid2 = $response['eid'] ?? '';

        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "Test Pipe",
                "alias": "alias3"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = json_decode($result['response'],true);
        $objeid3 = $response['eid'] ?? '';


        // TEST: delete pipe

        // BEGIN TEST
        $params = array(
            'method' => 'DELETE',
            'url' => "$apibase/$userid1/pipes/$objeid1",
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
        \Flexio\Tests\Check::assertInArray('A.1', 'DELETE /:userid/pipes/:objeid; fail if requesting user doesn\'t have credentials',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'DELETE',
            'url' => "$apibase/$userid1/pipes/$objeid2",
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
        \Flexio\Tests\Check::assertInArray('A.2', 'DELETE /:userid/pipes/:objeid; fail if object isn\'t owned by specified owner',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'DELETE',
            'url' => "$apibase/$userid1/pipes/$objeid1",
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
        \Flexio\Tests\Check::assertInArray('A.3', 'DELETE /:userid/pipes/:objeid; fail if requesting user doesn\'t have rights',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'DELETE',
            'url' => "$apibase/$userid1/pipes/$objeid1",
            'token' => $token1
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PIP",
            "eid_status": "D"
        }';
        \Flexio\Tests\Check::assertInArray('A.4', 'DELETE /:userid/pipes/:objeid; delete pipe',  $actual, $expected, $results);
        $params = array(
            'method' => 'GET',
            'url' => "$apibase/$userid1/pipes/$objeid1",
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
        \Flexio\Tests\Check::assertInArray('A.5', 'DELETE /:userid/pipes/:objeid; make sure a pipe is deleted',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'DELETE',
            'url' => "$apibase/$userid1/pipes/alias3",
            'token' => $token1
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid3.'",
            "eid_type": "PIP",
            "eid_status": "D"
        }';
        \Flexio\Tests\Check::assertInArray('A.6', 'DELETE /:userid/pipes/:objeid; allow deletion by alias',  $actual, $expected, $results);
        $params = array(
            'method' => 'DELETE',
            'url' => "$apibase/$userid1/pipes/alias3",
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
        \Flexio\Tests\Check::assertInArray('A.7', 'DELETE /:userid/pipes/:objeid; make sure a pipe is deleted',  $actual, $expected, $results);

        // BEGIN
        $unique_alias = \Flexio\Base\Util::generateHandle();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "Test Pipe",
                "alias": "'.$unique_alias.'"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = json_decode($result['response'],true);
        $objeid1 = $response['eid'] ?? '';
        $params = array(
            'method' => 'DELETE',
            'url' => "$apibase/$userid1/pipes/$unique_alias",
            'token' => $token1
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid": "'.$objeid1.'",
            "eid_type": "PIP",
            "eid_status": "D"
        }';
        \Flexio\Tests\Check::assertInArray('A.8', 'DELETE /:userid/pipes/:objeid; delete pipe',  $actual, $expected, $results);
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "Test Pipe",
                "alias": "'.$unique_alias.'"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid_type": "PIP",
            "eid_status": "A",
            "alias": "'.$unique_alias.'"
        }';
        \Flexio\Tests\Check::assertInArray('A.9', 'DELETE /:userid/pipes/:objeid; clear out alias when deleting a pipe',  $actual, $expected, $results);
    }
}
