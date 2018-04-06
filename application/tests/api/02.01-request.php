<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-04-03
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
        // SETUP


        // TEST: basic get/set tests

        // BEGIN TEST
        $request_ip_address = '1.1.1.1';
        $request = \Flexio\Api\Request::create();
        $request->setIpAddress($request_ip_address);
        $actual = $request->getIpAddress();
        $expected = $request_ip_address;
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Api\Request::setIpAddress(); basic set',  $actual, $expected, $results);

        // BEGIN TEST
        $request_token = 'abcdefghijklmnopqrstuvwxyz';
        $request = \Flexio\Api\Request::create();
        $request->setToken($request_token);
        $actual = $request->getToken();
        $expected = $request_token;
        \Flexio\Tests\Check::assertString('A.2', '\Flexio\Api\Request::setToken(); basic set',  $actual, $expected, $results);

        // BEGIN TEST
        $request_method = 'PUT';
        $request = \Flexio\Api\Request::create();
        $request->setMethod($request_method);
        $actual = $request->getMethod();
        $expected = $request_method;
        \Flexio\Tests\Check::assertString('A.3', '\Flexio\Api\Request::setMethod(); basic set',  $actual, $expected, $results);

        // BEGIN TEST
        $request_url = 'https://api.domain.com';
        $request = \Flexio\Api\Request::create();
        $request->setUrl($request_url);
        $actual = $request->getUrl();
        $expected = $request_url;
        \Flexio\Tests\Check::assertString('A.4', '\Flexio\Api\Request::setUrl(); basic set',  $actual, $expected, $results);

        // BEGIN TEST
        $request_created_by = 'xxxxxxxxxxxx';
        $request = \Flexio\Api\Request::create();
        $request->setRequestingUser($request_created_by);
        $actual = $request->getRequestingUser();
        $expected = $request_created_by;
        \Flexio\Tests\Check::assertString('A.5', '\Flexio\Api\Request::setRequestingUser(); basic set',  $actual, $expected, $results);

        // BEGIN TEST
        $request_created = '2018-01-02T03:04:05';
        $request = \Flexio\Api\Request::create();
        $request->setRequestCreated($request_created);
        $actual = $request->getRequestCreated();
        $expected = $request_created;
        \Flexio\Tests\Check::assertString('A.6', '\Flexio\Api\Request::setRequestCreated(); basic set',  $actual, $expected, $results);

        // BEGIN TEST
        $request_object_owner_eid = 'yyyyyyyyyyyy';
        $request = \Flexio\Api\Request::create();
        $request->setOwnerFromUrl($request_object_owner_eid);
        $actual = $request->getOwnerFromUrl();
        $expected = $request_object_owner_eid;
        \Flexio\Tests\Check::assertString('A.7', '\Flexio\Api\Request::setOwnerFromUrl(); basic set',  $actual, $expected, $results);

        // BEGIN TEST
        $request_object_eid = 'zzzzzzzzzzzzz';
        $request = \Flexio\Api\Request::create();
        $request->setObjectFromUrl($request_object_eid);
        $actual = $request->getObjectFromUrl();
        $expected = $request_object_eid;
        \Flexio\Tests\Check::assertString('A.8', '\Flexio\Api\Request::setObjectFromUrl(); basic set',  $actual, $expected, $results);

        // BEGIN TEST
        $request_object_eid_type = \Model::TYPE_PIPE;
        $request = \Flexio\Api\Request::create();
        $request->setObjectEidType($request_object_eid_type);
        $actual = $request->getObjectEidType();
        $expected = $request_object_eid_type;
        \Flexio\Tests\Check::assertString('A.9', '\Flexio\Api\Request::setObjectEidType(); basic set',  $actual, $expected, $results);

        // BEGIN TEST
        $response_code = '300';
        $request = \Flexio\Api\Request::create();
        $request->setResponseCode($response_code);
        $actual = $request->getResponseCode();
        $expected = $response_code;
        \Flexio\Tests\Check::assertString('A.10', '\Flexio\Api\Request::setResponseCode(); basic set',  $actual, $expected, $results);

        // BEGIN TEST
        $response_created = '2018-02-03T04:05:06';
        $request = \Flexio\Api\Request::create();
        $request->setResponseCreated($response_created);
        $actual = $request->getResponseCreated();
        $expected = $response_created;
        \Flexio\Tests\Check::assertString('A.11', '\Flexio\Api\Request::setResponseCreated(); basic set',  $actual, $expected, $results);

        // BEGIN TEST
        $response_params = array(
            'code' => '2',
            'message' => 'unknown'
        );
        $request = \Flexio\Api\Request::create();
        $request->setResponseParams($response_params);
        $actual = $request->getResponseParams();
        $expected = $response_params;
        \Flexio\Tests\Check::assertArray('A.12', '\Flexio\Api\Request::setResponseParams(); basic set',  $actual, $expected, $results);

        // BEGIN TEST
        $url_params = array(
            'apiparam1' => 'xxxxxxxxxxxx',
            'apiparam2' => 'pipes'
        );
        $request = \Flexio\Api\Request::create();
        $request->setUrlParams($url_params);
        $actual = $request->getUrlParams();
        $expected = $url_params;
        \Flexio\Tests\Check::assertArray('A.13', '\Flexio\Api\Request::setUrlParams(); basic set',  $actual, $expected, $results);

        // BEGIN TEST
        $query_params = array(
            'start' => 0,
            'limit' => 10
        );
        $request = \Flexio\Api\Request::create();
        $request->setQueryParams($query_params);
        $actual = $request->getQueryParams();
        $expected = $query_params;
        \Flexio\Tests\Check::assertArray('A.14', '\Flexio\Api\Request::setQueryParams(); basic set',  $actual, $expected, $results);

        // BEGIN TEST
        $post_params = array(
            'key' => 'value'
        );
        $request = \Flexio\Api\Request::create();
        $request->setPostParams($post_params);
        $actual = $request->getPostParams();
        $expected = $post_params;
        \Flexio\Tests\Check::assertArray('A.15', '\Flexio\Api\Request::setPostParams(); basic set',  $actual, $expected, $results);
    }
}
