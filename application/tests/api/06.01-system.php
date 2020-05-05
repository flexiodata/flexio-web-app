<?php
/**
 *
 * Copyright (c) 2015, Flex Research LLC. All rights reserved.
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
        $apibase = \Flexio\Tests\Util::getTestHost() . '/v1';


        // TEST: get system information

        // BEGIN TEST
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
        \Flexio\Tests\Check::assertInArray('A.1', 'GET /about; return basic info',  $actual, $expected, $results);
    }
}
