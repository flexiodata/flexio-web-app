<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-04-11
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
                    "name": "file1",
                    "content_type": "'.\Flexio\Base\ContentType::MIME_TYPE_TXT.'",
                    "content": "'.base64_encode("a\nbc").'"
                }
            },
            {
                "type": "flexio.create",
                "params": {
                    "name": "file2",
                    "content_type": "'.\Flexio\Base\ContentType::MIME_TYPE_TXT.'",
                    "content": "'.base64_encode("\nde").'"
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
        $actual = $process->getStdout()->getReader()->readRow();
        $expected = "a\nbc\nde";
        TestCheck::assertString('A.1', 'Merge Job; check basic functionality',  $actual, $expected, $results);
    }
}
