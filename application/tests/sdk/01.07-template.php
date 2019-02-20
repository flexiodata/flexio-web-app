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
// write text to stdout
var python_func1 = `
def flexio_handler(context):
    context.output.write('Hello World!')
`
// read stdin and convert it to uppercase
var python_func2 = `
def flexio_handler(context):
    input = context.input.read().upper()
    context.output.write(input)
`
Flexio.pipe()
    .python(python_func1)
    .python(python_func2)
    .run(function(err, response) {
    console.log(response.text)
    })
EOD;
        $actual = \Flexio\Tests\Util::execSDKJS($script);
        $expected = "HELLO WORLD!\n";
        \Flexio\Tests\Check::assertString('A.1', 'SDK; check basic functionality',  $actual, $expected, $results);
    }
}

