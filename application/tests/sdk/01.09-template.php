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
Flexio.pipe()
.request('https://git.io/vFBSw')       // short url for names-and-ip-addresses.csv
.convert('csv', 'table')               // convert to a table to process by row/column
.javascript(function(context) {        // add up the total number of males and females
    totals = {"males": 0, "females": 0}
    while ((row = context.input.readLine()) !== null) {
        if (row['gender'].toLowerCase() == 'male')
            totals.males++;
        if (row['gender'].toLowerCase() == 'female')
            totals.females++;
    }
    context.output.write(JSON.stringify(totals))
})
.run(function(err, response) {
    console.log(response.text)
})
EOD;
        $actual = json_decode(TestUtil::execSDKJS($script),true);
        $expected = array("males" => 50, "females" => 50);
        TestCheck::assertInArray('A.1', 'SDK; check basic functionality',  $actual, $expected, $results);
    }
}

