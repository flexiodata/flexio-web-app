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
        // TEST: object creation

        // BEGIN TEST
        $username = \Flexio\Base\Util::generateHandle();
        $email = TestUtil::generateEmail();
        $password = \Flexio\Base\Util::generateHandle();
        $params = json_decode('
        {
            "user_name": "'.$username.'",
            "email": "'.$email.'",
            "password": "'.$password.'",
            "send_email": false,
            "create_sample_project": false
        }
        ',true);
        $request = \Flexio\Api\Request::create()->setRequestingUser(\Flexio\Object\User::USER_SYSTEM);
        $actual= \Flexio\Api\User::create($params, $request);
        $expected = '
        {
            "eid_type": "'.\Model::TYPE_USER.'",
            "user_name": "'.$username.'",
            "email": "'.$email.'"
        }
        ';
        TestCheck::assertInArray('A.1', '\Flexio\Api\User::create(); return the object',  $actual, $expected, $results);
    }
}
