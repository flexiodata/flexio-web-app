<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-04-20
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


        // TEST: test invalid api endpoint

        // BEGIN TEST
        $endpoints = [
            '/api/1',
            '/api/2',
            '/api/v1',
            '/api/v3'
        ];
        $idx = 0;
        foreach ($endpoints as $e)
        {
            $idx++;
            $apibase = \Flexio\Tests\Util::getTestHost() . $e;
            $params = array(
                'method' => 'GET',
                'url' => "$apibase/about",
                // 'token' => '', // no token included
            );

            $result = \Flexio\Tests\Util::callApi($params);
            $response = json_decode($result['response'],true);
            $actual = $result;
            $actual['response'] = $response;
            $expected = '{
                "code": 400,
                "content_type": "application/json",
                "response": {
                    "error": {
                        "code": "invalid-version",
                        "message": "Invalid version"
                    }
                }
            }';
            \Flexio\Tests\Check::assertInArray("A.$idx", "GET $e/about; test invalid api endpoint'",  $actual, $expected, $results);
        }


        // TEST: test the allowed endpoints to access the api

        // BEGIN TEST
        $endpoints = [
            '/v1',
            '/api/v2'
        ];
        $idx = 0;
        foreach ($endpoints as $e)
        {
            $idx++;
            $apibase = \Flexio\Tests\Util::getTestHost() . $e;
            $params = array(
                'method' => 'GET',
                'url' => "$apibase/about",
                // 'token' => '', // no token included
            );
            $result = \Flexio\Tests\Util::callApi($params);
            $actual = $result['response'];
            $expected = '
            {
                "name": "flexio"
            }
            ';
            \Flexio\Tests\Check::assertInArray("B.$idx", "GET $e/about; return basic about info using allowed endpoint",  $actual, $expected, $results);
        }
    }
}
