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
        // SETUP


        // TEST: tests for sending an error response

        // BEGIN TEST
        $content = array(
            'code' => 'invalid-request',
            'message' => 'Invalid request'
        );
        ob_start();
        @\Flexio\Api\Response::sendError($content);
        $result = ob_get_clean();
        http_response_code(200); // reset the response code so that the test doesn't fail from the http header that's set in the sendError() function
        $actual = json_decode($result,true);
        $expected = array(
            'error' => $content
        );
        \Flexio\Tests\Check::assertArray('A.1', '\Flexio\Api\Response::sendError(); basic content response',  $actual, $expected, $results);
    }
}
