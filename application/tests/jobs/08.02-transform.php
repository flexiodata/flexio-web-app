<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-02-17
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $task = \Flexio\Object\Task::create('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_TXT.'",
                    "content": "${data}"
                }
            },
            {
                "type": "flexio.transform",
                "params": {
                    "operations": [
                        { "operation": "case", "case": "${case}" }
                    ]
                }
            }
        ]
        ')->get();



        // TEST: Transform Job: standardize text with capitalization (none, lowercase, uppercase, proper, first letter)

        // BEGIN TEST
        $data = <<<EOD
some content
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "case" => "upper"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $result = TestUtil::getProcessResult($process);
        $actual = $result[0] ?? false;
        $expected = 'SOME CONTENT';
        TestCheck::assertString('A.1', 'Transform Job; basic transformation on stream content',  $actual, $expected, $results);
    }
}
