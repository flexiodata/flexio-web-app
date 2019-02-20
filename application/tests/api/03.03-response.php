<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
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

        // TEST: tests for sending an error response code

        // BEGIN TEST
        $content = array(
            'code' => 'invalid-request'
        );
        ob_start();
        @\Flexio\Api\Response::sendError($content);
        $result = ob_get_clean();
        $code = http_response_code(); // get the response code before it's reset
        http_response_code(200); // reset the response code so that the test doesn't fail from the http header that's set in the sendError() function
        $actual = $code;
        $expected = 400;
        \Flexio\Tests\Check::assertNumber('A.1', '\Flexio\Api\Response::sendError(); error response code',  $actual, $expected, $results);

        // BEGIN TEST
        $content = array(
            'code' => 'unauthorized'
        );
        ob_start();
        @\Flexio\Api\Response::sendError($content);
        $result = ob_get_clean();
        $code = http_response_code(); // get the response code before it's reset
        http_response_code(200); // reset the response code so that the test doesn't fail from the http header that's set in the sendError() function
        $actual = $code;
        $expected = 401;
        \Flexio\Tests\Check::assertNumber('A.2', '\Flexio\Api\Response::sendError(); error response code',  $actual, $expected, $results);

        // BEGIN TEST
        $content = array(
            'code' => 'insufficient-rights'
        );
        ob_start();
        @\Flexio\Api\Response::sendError($content);
        $result = ob_get_clean();
        $code = http_response_code(); // get the response code before it's reset
        http_response_code(200); // reset the response code so that the test doesn't fail from the http header that's set in the sendError() function
        $actual = $code;
        $expected = 403;
        \Flexio\Tests\Check::assertNumber('A.3', '\Flexio\Api\Response::sendError(); error response code',  $actual, $expected, $results);

        // BEGIN TEST
        $content = array(
            'code' => 'no-model'
        );
        ob_start();
        @\Flexio\Api\Response::sendError($content);
        $result = ob_get_clean();
        $code = http_response_code(); // get the response code before it's reset
        http_response_code(200); // reset the response code so that the test doesn't fail from the http header that's set in the sendError() function
        $actual = $code;
        $expected = 500;
        \Flexio\Tests\Check::assertNumber('A.4', '\Flexio\Api\Response::sendError(); error response code',  $actual, $expected, $results);


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
        \Flexio\Tests\Check::assertArray('B.1', '\Flexio\Api\Response::sendError(); error response content',  $actual, $expected, $results);
    }
}
