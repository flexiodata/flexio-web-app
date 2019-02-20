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
        $script = \Flexio\Tests\Util::getTestSDKSetup() . <<<'EOD'
Flexio.pipe()
.echo({a:"aaa",b:"bbb"})
.echo("${input.a}")
.run().then(response => console.log(response.text))
EOD;
        $actual = \Flexio\Tests\Util::execSDKJS($script);
        $expected = "aaa\n";
        \Flexio\Tests\Check::assertString('A.1', 'SDK; simple variable lookup in json object',  $actual, $expected, $results);

        // BEGIN TEST
        $script = \Flexio\Tests\Util::getTestSDKSetup() . <<<'EOD'
Flexio.pipe()
.echo(["aaa","bbb"])
.echo("${input[1]}")
.run().then(response => console.log(response.text))
EOD;
        $actual = \Flexio\Tests\Util::execSDKJS($script);
        $expected = "bbb\n";
        \Flexio\Tests\Check::assertString('A.2', 'SDK; simple indexed lookup in json array',  $actual, $expected, $results);

        // BEGIN TEST
        $script = \Flexio\Tests\Util::getTestSDKSetup() . <<<'EOD'
Flexio.pipe()
.echo([ { "mulch": ["xxx","yyy"] }, { "munch": ["AAA","BBB"] }, { "moo": ["aaa","bbb"] } ])
.echo("${input[2].moo[1]}")
.run().then(response => console.log(response.text))
EOD;
        $actual = \Flexio\Tests\Util::execSDKJS($script);
        $expected = "bbb\n";
        \Flexio\Tests\Check::assertString('A.2', 'SDK; advanced indexed/key lookup in json array of objects',  $actual, $expected, $results);
    }
}

