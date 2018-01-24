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
        $script = TestUtil::getTestSDKSetup() . <<<EOD
var python_code = `
def flexio_handler(context):
    context.output.write('Hello ' + context.query['name'] + '!')
`
Flexio.pipe()
    .python(python_code)
    .run({ query: { name: 'World' } }, function(err, response) {
    console.log(response.text)
    })
EOD;
        $actual = TestUtil::execSDKJS($script);
        $expected = "Hello World!\n";
        TestCheck::assertString('A.1', 'SDK; check template functionality',  $actual, $expected, $results);
    }
}

