<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-01-24
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
        $server_ipaddr = $_SERVER['SERVER_ADDR'];

        $script = <<<EOD
const Flexio = require('flexio-sdk-js')
Flexio.setup('xtdkxpcjwzkpgkhkcmhx', { baseUrl: 'https://$server_ipaddr/api/v1', insecure: true })
Flexio.pipe()
.echo("Hello, World.")
.run(function(err, response) {
    console.log(response.text)
})
EOD;
        $actual = TestUtil::execSDKJS($script);
        $expected = 'Hello, World.';
        TestCheck::assertString('A.1', 'SDK; check basic functionality',  $actual, $expected, $results);
    }
}
