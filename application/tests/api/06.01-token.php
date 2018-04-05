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
        // ENDPOINT: POST /:userid/auth/keys


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/api/v2';
        $userid = \Flexio\Tests\Util::createUser();
        $token = \Flexio\Tests\Util::createToken($userid);


        // TEST: create a new token

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid/auth/keys",
            // 'token' => '', // don't include a token
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error" : {
                "code": "insufficient-rights"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'POST /:userid/auth/keys; fail if requesting user doesn\'t have credentials',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid/auth/keys",
            'token' => $token
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid_type": "TKN",
            "eid_status": "A",
            "user_eid": "'.$userid.'",
            "owned_by": "'.$userid.'"
        }
        ';
        \Flexio\Tests\Check::assertInArray('A.2', 'POST /:userid/auth/keys; create a new token',  $actual, $expected, $results);
    }
}
