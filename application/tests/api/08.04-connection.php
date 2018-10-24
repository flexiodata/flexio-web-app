<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-05-01
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
        // ENDPOINT: POST /:userid/connections/:objeid/connect
        // ENDPOINT: POST /:userid/connections/:objeid/disconnect


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/v1';
        $password1 = \Flexio\Base\Password::generate();
        $userid1 = \Flexio\Tests\Util::createUser(null, null, $password1);
        $token1 = \Flexio\Tests\Util::createToken($userid1);
        $password2 = \Flexio\Base\Password::generate();
        $userid2 = \Flexio\Tests\Util::createUser(null, null, $password2);
        $token2 = \Flexio\Tests\Util::createToken($userid2);
        $storage_items = [
            \Flexio\Tests\Base::STORAGE_FLEX,
            \Flexio\Tests\Base::STORAGE_AMAZONS3,
            \Flexio\Tests\Base::STORAGE_BOX,
            \Flexio\Tests\Base::STORAGE_DROPBOX,
            \Flexio\Tests\Base::STORAGE_GITHUB,
            \Flexio\Tests\Base::STORAGE_GOOGLEDRIVE,
            \Flexio\Tests\Base::STORAGE_GOOGLECLOUDSTORAGE,
            \Flexio\Tests\Base::STORAGE_SFTP
        ];
        $test_connection_eids = array();

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

        // create a copy of each of the non-local storage types; these
        // contain the credentials we need to test connecting to the services
        $storage_owner = \Flexio\Tests\Util::getTestStorageOwner();
        foreach ($storage_items as $storage_location)
        {
            // don't test local storage; no connection exists
            if ($storage_location === \Flexio\Tests\Base::STORAGE_FLEX)
                continue;

            // get the connection info for the current testsuite connections
            $testsuite_connection_eid = \Flexio\Object\Connection::getEidFromName($storage_owner, $storage_location);

            // TODO: add some type of test setup failure notice
            if ($testsuite_connection_eid === false)
                continue;

            $testsuite_connection_info = \Flexio\Object\Connection::load($testsuite_connection_eid)->get();

            // create a new connection with the same connection info as the testsuite connections, but
            // for a new test user; make sure the connection status indicates it hasn't yet been connected
            $testsuite_connection_info['connection_status'] = \Model::CONNECTION_STATUS_UNAVAILABLE;
            $testsuite_connection_info['owned_by'] = $userid1;
            $testsuite_connection_info['created_by'] = $userid1;
            $new_connection = \Flexio\Object\Connection::create($testsuite_connection_info);

            // grant default rights to the owner
            $new_connection->grant($userid1, \Model::ACCESS_CODE_TYPE_EID,
                array(
                    \Flexio\Object\Right::TYPE_READ_RIGHTS,
                    \Flexio\Object\Right::TYPE_WRITE_RIGHTS,
                    \Flexio\Object\Right::TYPE_READ,
                    \Flexio\Object\Right::TYPE_WRITE,
                    \Flexio\Object\Right::TYPE_DELETE
                )
            );

            $new_connection_eid = $new_connection->getEid();
            $test_connection_eids[$new_connection_eid] = $storage_location;
        }


        // TEST: connect to service with invalid credentials

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/connections/$objeid1/connect",
            // 'token' => '', // no token included
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code": "insufficient-rights"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'POST /:userid/connections/:objeid/connect; fail if requesting user doesn\'t have credentials',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/connections/$objeid2/connect",
            'token' => $token1
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code": "unavailable"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'POST /:userid/connections/:objeid/connect; fail if object isn\'t owned by specified owner',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/connections/$objeid1/connect",
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
        \Flexio\Tests\Check::assertInArray('A.3', 'POST /:userid/connections/:objeid/connect; fail if requesting user doesn\'t have rights',  $actual, $expected, $results);



        // TEST: test connecting to service

        // BEGIN TEST
        $idx = 0;
        foreach ($test_connection_eids as $objeid1 => $type)
        {
            $idx++;
            $params = array(
                'method' => 'POST',
                'url' => "$apibase/$userid1/connections/$objeid1/connect",
                'token' => $token1
            );
            $result = \Flexio\Tests\Util::callApi($params);
            $response = json_decode($result['response'],true);
            $actual = $response['connection_status'] ?? '';
            $expected = \Model::CONNECTION_STATUS_AVAILABLE;
            \Flexio\Tests\Check::assertString("B.$idx", "POST /:userid/connections/:objeid/connect; copy of $type: check connecting to service",  $actual, $expected, $results);
        }


        // TEST: test disconnecting to service

        // BEGIN TEST
        $idx = 0;
        foreach ($test_connection_eids as $objeid1 => $type)
        {
            $idx++;
            $params = array(
                'method' => 'POST',
                'url' => "$apibase/$userid1/connections/$objeid1/disconnect",
                'token' => $token1
            );
            $result = \Flexio\Tests\Util::callApi($params);
            $response = json_decode($result['response'],true);
            $actual = $response['connection_status'] ?? '';
            $expected = \Model::CONNECTION_STATUS_UNAVAILABLE;
            \Flexio\Tests\Check::assertString("C.$idx", "POST /:userid/connections/:objeid/connect; copy of $type: check disconnecting to service",  $actual, $expected, $results);
        }


        // TEST: test connecting to service with bad credentials

        // BEGIN TEST
        $idx = 0;
        foreach ($test_connection_eids as $objeid1 => $type)
        {
            // TODO: we need a better way of resetting credentials
            $connection_model = \Flexio\Tests\Util::getModel()->connection;
            $connection_model->set($objeid1, array('connection_info' => '{}'));

            $idx++;
            $params = array(
                'method' => 'POST',
                'url' => "$apibase/$userid1/connections/$objeid1/connect",
                'token' => $token1
            );
            $result = \Flexio\Tests\Util::callApi($params);
            $actual = json_decode($result['response'],true);
            $expected = '
            {
                "error" : {
                    "code": "connection-failed"
                }
            }';
            \Flexio\Tests\Check::assertInArray("D.$idx", "POST /:userid/connections/:objeid/connect; copy of $type: test connecting within invalid info",  $actual, $expected, $results);
        }
    }
}
