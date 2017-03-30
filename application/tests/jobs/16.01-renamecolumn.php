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


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TODO: placeholder job to test basic functionality; fill out tests

        // SETUP
        $task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
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
                "type": "flexio.rename",
                "params": {
                    "columns": [
                        { "name": "field1", "new_name": "field4" }
                    ]
                }
            }
        ]
        ',true);



        // TEST: RenameColumn Job

        // BEGIN TEST
        $params = [
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnResult($process);
        $expected = '
        [
            { "name": "field4", "type": "character", "width": 3 },
            { "name": "field2", "type": "character", "width": 3 }
        ]
        ';
        TestCheck::assertInArray('A.1', 'RenameColumn Job; check basic functionality',  $actual, $expected, $results);
    }
}
