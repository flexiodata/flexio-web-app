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
/*
// TODO: old tests; convert over to new

        // TODO: add tests

        // TEST: object creation

        // BEGIN TEST
        $params = json_decode('
        {
            "name": "Connection",
            "description": "Test connection"
        }
        ',true);
        $request = \Flexio\Api1\Request::create();
        $request->setPostParams($params);
        $request->setRequestingUser(\Flexio\Tests\Util::getDefaultTestUser());
        $actual = \Flexio\Api1\Connection::create($request);
        $expected = '
        {
            "eid_type": "'.\Model::TYPE_CONNECTION.'",
            "name": "Connection",
            "description": "Test connection"
        }
        ';
        \Flexio\Tests\Check::assertInArray('A.1', '\Flexio\Api1\Connection::create(); return the object',  $actual, $expected, $results);
*/
    }
}
