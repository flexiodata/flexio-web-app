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
        // TEST: basic SDK tests

        // BEGIN TEST
        $script = TestUtil::getTestSDKSetup() . <<<EOD
Flexio.pipe()
.javascript(function(context) {
    context.output.write('Hello World!')        // write text to stdout
})
.javascript(function(context) {
    input = context.input.read().toUpperCase()  // read stdin and convert it to uppercase
    context.output.write(input)                 // write the converted output
})
.run(function(err, response) {
    console.log(response.text)
})
EOD;
        $actual = TestUtil::execSDKJS($script);
        $expected = "HELLO WORLD!\n";
        TestCheck::assertString('A.1', 'SDK; check template functionality',  $actual, $expected, $results);
    }
}

