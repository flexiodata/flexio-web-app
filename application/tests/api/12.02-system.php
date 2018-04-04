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
        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/api/v2';


        // TEST: /validate

        // BEGIN TEST
        $key = \Flexio\Base\Eid::generate();
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/validate",
            'content_type' => 'application/json',
            'params' => '
                [
                    {
                        "key": "'.$key.'",
                        "value": "abcd3fg",
                        "type": "password"
                    },
                    {
                        "key": "'.$key.'",
                        "value": "abcdefgh",
                        "type": "password"
                    },
                    {
                        "key": "'.$key.'",
                        "value": "abcd3fgh",
                        "type": "password"
                    },
                    {
                        "key": "'.$key.'",
                        "value": "12345678",
                        "type": "password"
                    }
                ]
            '
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        [
            {
                "key":"'.$key.'",
                "valid":false
            },
            {
                "key":"'.$key.'",
                "valid":false
            },
            {
                "key":"'.$key.'",
                "valid":true
            },
            {
                "key":"'.$key.'",
                "valid":true
            }
        ]
        ';
        \Flexio\Tests\Check::assertInArray('B.1', 'GET /validate; return basic info',  $actual, $expected, $results);


        // TODO: add tests for following validation types:
        /*
        'identifier'
        'username'
        'email'
        'ename'
        'password'
        */
    }
}
