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
        // TODO: add tests

        // TEST: object creation

        // BEGIN TEST
        $username = \Flexio\Base\Util::generateHandle();
        $email = $username . '@flex.io';
        $properties = array('user_name' => $username, 'email' => $email);
        $user = \Flexio\Object\User::create($properties);

        $params = json_decode('
        {
            "name": "Project",
            "description": "Test project"
        }
        ',true);
        $actual = \Flexio\Api\Project::create($params, $user->getEid());
        $expected = '
        {
            "eid_type": "'.\Model::TYPE_PROJECT.'",
            "name": "Project",
            "description": "Test project"
        }
        ';
        TestCheck::assertInArray('A.1', '\Flexio\Api\Project::create(); return the object',  $actual, $expected, $results);
    }
}
