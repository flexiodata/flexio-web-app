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
        $script = TestUtil::getTestSDKSetup() . <<<'EOD'
Flexio.pipe()
    .foreach(Flexio.pipe().set('result', '${result}${input.name}'))
    .echo("${result}")
.run({data:[{"name":"111"},{"name":"222"},{"name":"333"}]}, function(err, response) {
	console.log(response.text)
})
EOD;
        $actual = TestUtil::execSDKJS($script);
        $expected = "111222333\n";
        \Flexio\Tests\Check::assertString('A.1', 'SDK; check template functionality',  $actual, $expected, $results);
    }
}

