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
        // ENDPOINT: GET /about


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/api/v2';


        // TEST: get system information

        // BEGIN TEST
        $params = array(
            'method' => 'GET',
            'url' => "$apibase/about",
            // 'token' => '', // don't include a token
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "name": "Flex.io"
        }
        ';
        \Flexio\Tests\Check::assertInArray('A.1', 'GET /about; return basic info',  $actual, $expected, $results);
    }
}
