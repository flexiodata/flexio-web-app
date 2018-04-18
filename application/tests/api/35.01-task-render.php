<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-04-18
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
        // ENDPOINT: POST /:userid/pipes/:objeid/run


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/api/v2';
        $password = \Flexio\Base\Password::generate();
        $userid = \Flexio\Tests\Util::createUser(null, null, $password);
        $token = \Flexio\Tests\Util::createToken($userid);


        // TEST: render task basic functionality

        // BEGIN TEST
        $result = \Flexio\Tests\Util::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/pipes",
            'token' => $token,
            'content_type' => 'application/json',
            'params' => '{
                "task": {
                    "op": "render",
                    "params": {
                        "url": "https://www.flex.io",
                        "width": 50,
                        "height": 50
                    }
                }
            }'
        ));
        $response = json_decode($result['response'],true);
        $objeid = $response['eid'] ?? '';
        $result = \Flexio\Tests\Util::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/pipes/$objeid/run",
            'token' => $token
        ));
        $response = $result['response'] ?? '';
        $mime_type = '';
        $content_type = '';
        \Flexio\Base\ContentType::getMimeAndContentType($response,$mime_type,$content_type);
        $actual = array(
            "code" => $result['code'],
            "content_type" => $result['content_type'],
            "response_mime_type" => $mime_type,
            "response_content_type" => $content_type,
        );
        $expected = array(
            "code" => 200,
            "content_type" => "image/png",
            "response_mime_type" => "image/png",
            "response_content_type" => "charset=binary"
        );
        \Flexio\Tests\Check::assertArray('A.1', 'POST /:userid/pipes/:objeid/run; render task basic functionality',  $actual, $expected, $results);
    }
}

