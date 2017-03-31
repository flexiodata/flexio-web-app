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
                        { "name": "field2", "type": "character", "width": 3, "scale": 0 },
                        { "name": "field3", "type": "character", "width": 3, "scale": 0 }
                    ],
                    "content": [
                        ["a","b","c"],
                        ["b","B","c"],
                        ["c","b","c"]
                    ]
                }
            },
            {
                "type": "flexio.select",
                "params": {
                    "columns" : ["field3","field2"]
                }
            }
        ]
        ',true);



        // TEST: Select Job

        // BEGIN TEST
        $params = [
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = [["c","b"],["c","B"],["c","b"]];
        TestCheck::assertString('A.1', 'Select Job; check basic functionality',  $actual, $expected, $results);
    }
}
