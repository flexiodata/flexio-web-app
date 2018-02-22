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
        // TODO: add tests

        // TEST: object creation

        // BEGIN TEST
        $params = json_decode('
        {
            "name": "Pipe",
            "description": "Test pipe"
        }
        ',true);
        $request = \Flexio\Api\Request::create();
        $request->setPostParams($params);
        $request->setRequestingUser(\Flexio\Tests\Util::getDefaultTestUser());
        $actual = \Flexio\Api\Pipe::create($request);
        $expected = '
        {
            "eid_type": "'.\Model::TYPE_PIPE.'",
            "name": "Pipe",
            "description": "Test pipe"
        }
        ';
        \Flexio\Tests\Check::assertInArray('A.1', '\Flexio\Api\Pipe::create(); return the object',  $actual, $expected, $results);
    }
}
