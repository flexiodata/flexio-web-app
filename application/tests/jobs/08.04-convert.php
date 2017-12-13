<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-06-23
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



        // TEST: Convert; default format behavior

        // BEGIN TEST
        $data = <<<EOD
"field1", "field2"
"a1", "b1"
"a2", "b2"
EOD;
        $task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "content_type": "'.\Flexio\Base\ContentType::CSV.'",
                    "content": "'. base64_encode(trim($data)) .'"
                }
            },
            {
                "type": "flexio.convert",
                "params": {
                    "input": {
                        "delimiter": "{comma}",
                        "header_row": "true",
                        "text_qualifier": "{double_quote}"
                    }
                }
            }
        ]
        ',true);
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());

        // note: this line uses excel's rules
        //$expected = '[["a1"," \\"b1\\""],["a2"," \\"b2\\""]]';

        // note: this line uses php's fgetcsv rules
        $expected = '[["a1","b1"],["a2","b2"]]';
        TestCheck::assertArray('A.1', 'Convert Job; basic content upload test',  $actual, $expected, $results);
    }
}
