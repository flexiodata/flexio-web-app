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
    .foreach(Flexio.pipe().set('result', '${result}${input.name}'))
    .echo("${result}")
.run({data:[{"name":"111"},{"name":"222"},{"name":"333"}]}, function(err, response) {
	console.log(response.text)
})
EOD;
        $actual = \Flexio\Tests\Util::execSDKJS($script);
        $expected = "111222333\n";
        \Flexio\Tests\Check::assertString('A.1', 'SDK; for loop receiving information from post stdin json',  $actual, $expected, $results);


        // TEST: SDK template tests

        // BEGIN TEST
        $script = \Flexio\Tests\Util::getTestSDKSetup() . <<<'EOD'
Flexio.pipe()
    .echo([111,333,555])
    .foreach(Flexio.pipe().dump('${item}'))
.run().then(response => console.log(response.text))
EOD;
        $actual = \Flexio\Tests\Util::execSDKJS($script);
        $expected = "111333555\n";
        \Flexio\Tests\Check::assertString('A.2', 'SDK; for loop iterating over json array with default iterator name "item"',  $actual, $expected, $results);

        // BEGIN TEST
        $script = \Flexio\Tests\Util::getTestSDKSetup() . <<<'EOD'
Flexio.pipe()
    .echo([111,333,555])
    .foreach('moo : input', Flexio.pipe().dump('${moo}'))
.run().then(response => console.log(response.text))
EOD;
        $actual = \Flexio\Tests\Util::execSDKJS($script);
        $expected = "111333555\n";
        \Flexio\Tests\Check::assertString('A.3', 'SDK; for loop iterating over json array with custom iterator name "moo"',  $actual, $expected, $results);
    }
}

