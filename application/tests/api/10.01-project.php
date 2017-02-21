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


class Test
{
    public function run(&$results)
    {
        // TODO: add tests

        // TEST: object creation

        // BEGIN TEST
        $username = Util::generateHandle();
        $email = $username . '@flex.io';
        $properties = array('user_name' => $username, 'email' => $email);
        $user = \Flexio\Object\User::create($properties);

        $params = json_decode('
        {
            "name": "Project",
            "description": "Test project"
        }
        ',true);
        $request = \Flexio\Api\Request::create()->setRequestingUser($user->getEid());
        $actual = \Flexio\Api\ProjectApi::create($params, $request);
        $expected = '
        {
            "eid_type": "'.\Model::TYPE_PROJECT.'",
            "name": "Project",
            "description": "Test project"
        }
        ';
        TestCheck::assertInArray('A.1', '\Flexio\Api\ProjectApi::create(); return the object',  $actual, $expected, $results);
    }
}
