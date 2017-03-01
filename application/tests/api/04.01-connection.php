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


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TODO: add tests

        // TEST: object creation

        // BEGIN TEST
        $params = json_decode('
        {
            "name": "Connection",
            "description": "Test connection"
        }
        ',true);
        $request = \Flexio\Api\Request::create()->setRequestingUser(\Flexio\Object\User::USER_SYSTEM);
        $actual = \Flexio\Api\Connection::create($params, $request);
        $expected = '
        {
            "eid_type": "'.\Model::TYPE_CONNECTION.'",
            "name": "Connection",
            "description": "Test connection"
        }
        ';
        TestCheck::assertInArray('A.1', '\Flexio\Api\Connection::create(); return the object',  $actual, $expected, $results);
    }
}
