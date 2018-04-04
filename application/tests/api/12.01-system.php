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


        // TEST: /about

        // BEGIN TEST
        $params = array(
            'method' => 'GET',
            'url' => $apibase . '/about'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "name": "Flex.io"
        }
        ';
        \Flexio\Tests\Check::assertInArray('A.1', 'GET /api/v2/about; return basic info',  $actual, $expected, $results);


        // TEST: /validate

        // BEGIN TEST
        $key = \Flexio\Base\Eid::generate();
        $params = array(
            'method' => 'POST',
            'url' => $apibase . '/validate',
            'content_type' => 'application/json',
            'params' => '
                [
                    {
                        "key": "'.$key.'",
                        "value": "1",
                        "type": "password"
                    },
                    {
                        "key": "'.$key.'",
                        "value": "1234567890",
                        "type": "password"
                    },
                    {
                        "key": "'.$key.'",
                        "value": "abcd1efgh",
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
                "valid":true
            },
            {
                "key":"'.$key.'",
                "valid":true
            }
        ]
        ';
        \Flexio\Tests\Check::assertInArray('B.1', 'GET /api/v2/about; return basic info',  $actual, $expected, $results);


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
