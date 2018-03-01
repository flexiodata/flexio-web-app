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
        // TEST: SDK template tests

        // BEGIN TEST
        $script = \Flexio\Tests\Util::getTestSDKSetup() . <<<'EOD'
Flexio.pipe()
.echo({a:"aaa",b:"bbb"})
.echo("${input.a}")
.run().then(response => console.log(response.text))
EOD;
        $actual = \Flexio\Tests\Util::execSDKJS($script);
        $expected = "aaa\n";
        \Flexio\Tests\Check::assertString('A.1', 'SDK; simple variable lookup in json object',  $actual, $expected, $results);
    }
}

