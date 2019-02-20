<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
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
        // TEST: SDK template tests

        // BEGIN TEST
        $script = \Flexio\Tests\Util::getTestSDKSetup() . <<<EOD
Flexio.pipe()
    .python(
    'https://raw.githubusercontent.com/flexiodata/functions/master/python/hello-world.py', // code path
    'sha256:891568494dfb8fce562955b1509aee5a1ce0ce05ae210da6556517dd3986de36' // optional integrity check
    )
    .run(function(err, response) {
    console.log(response.text)
    })
EOD;
        $actual = \Flexio\Tests\Util::execSDKJS($script);
        $expected = "Hello, World!\n";
        \Flexio\Tests\Check::assertString('A.1', 'SDK; check basic functionality',  $actual, $expected, $results);
    }
}

