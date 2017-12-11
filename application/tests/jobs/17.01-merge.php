<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-06-07
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
// TODO:
// merges tests are deprecated until the merge job is brought back to work on params
return;

        // TODO: placeholder job to test basic functionality; fill out tests

        // SETUP
        $task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "name": "table1",
                    "content_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                    "columns": [
                        { "name": "field1", "type": "character", "width": 3, "scale": 0 },
                        { "name": "field2", "type": "character", "width": 3, "scale": 0 }
                    ],
                    "content": [
                        ["a","b"],
                        ["b","B"],
                        ["c","b"]
                    ]
                }
            },
            {
                "type": "flexio.create",
                "params": {
                    "name": "table2",
                    "content_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                    "columns": [
                        { "name": "field1", "type": "character", "width": 3, "scale": 0 },
                        { "name": "field3", "type": "character", "width": 3, "scale": 0 }
                    ],
                    "content": [
                        ["d","e"]
                    ]
                }
            },
            {
                "type": "flexio.merge",
                "params": {
                }
            }
        ]
        ',true);



        // TEST: Merge Job

        // BEGIN TEST
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
        $expected = [["a","b",null],["b","B",null],["c","b",null],["d",null,"e"]];
        TestCheck::assertArray('A.1', 'Merge Job; check basic functionality',  $actual, $expected, $results);
    }
}
