<?php
/**
 *
 * Copyright (c) 2018, Flex Research LLC. All rights reserved.
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
.request('https://git.io/vFBSw') // short url for names-and-ip-addresses.csv
.convert('csv', 'table')         // convert to a table to apply a filter
.filter('id = "1"')              // filter for a particular row
.run(function(err, response) {
    console.log(response.text)
})
EOD;
        $actual = \Flexio\Tests\Util::execSDKJS($script);
        $expected = "[{\"id\":\"1\",\"first_name\":\"Kimbra\",\"last_name\":\"Pettigrew\",\"email\":\"kpettigrew0@dedecms.com\",\"gender\":\"Female\",\"ip_address\":\"253.123.30.10\"}]\n";
        \Flexio\Tests\Check::assertString('A.1', 'SDK; check template functionality',  $actual, $expected, $results);
    }
}

