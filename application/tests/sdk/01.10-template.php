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
var python_code = `
import json
def flexio_handler(context):
    totals = {'males': 0, 'females': 0}
    for row in context.input:
        if (row['gender'].lower() == 'male'):
            totals['males'] = totals['males'] + 1;
        if (row['gender'].lower() == 'female'):
            totals['females'] = totals['females'] + 1;
    context.output.write(json.dumps(totals))
`
Flexio.pipe()
    .request('https://git.io/vFBSw')  // short url for names-and-ip-addresses.csv
    .convert('csv', 'table')          // convert to a table to process by row/column
    .python(python_code)              // add up the total number of males and females
    .run(function(err, response) {
    console.log(response.text)
    })
EOD;
        $actual = json_decode(\Flexio\Tests\Util::execSDKJS($script),true);
        $expected = array("males" => 50, "females" => 50);
        \Flexio\Tests\Check::assertInArray('A.1', 'SDK; check basic functionality',  $actual, $expected, $results);
    }
}

