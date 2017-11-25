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


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP



        // TEST: Transform Job: standardize text with capitalization (none, lowercase, uppercase, proper, first letter)

        // BEGIN TEST
        $task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_TXT.'",
                    "content": "'. base64_encode(trim('some content')) . '"
                }
            },
            {
                "type": "flexio.transform",
                "params": {
                    "operations": [
                        { "operation": "case", "case": "upper" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = \Flexio\Base\Util::getStreamContents($process->getStdout());
        $expected = 'SOME CONTENT';
        TestCheck::assertString('A.1', 'Transform Job; basic transformation on stream content',  $actual, $expected, $results);
    }
}
