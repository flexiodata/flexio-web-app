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
        $request = Request::create()->setRequestingUser(\Flexio\Object\User::USER_SYSTEM);
        $actual= PipeApi::create($params, $request);
        $expected = '
        {
            "eid_type": "'.Model::TYPE_PIPE.'",
            "name": "Pipe",
            "description": "Test pipe"
        }
        ';
        TestCheck::assertInArray('A.1', 'PipeApi::create(); return the object',  $actual, $expected, $results);
    }
}
