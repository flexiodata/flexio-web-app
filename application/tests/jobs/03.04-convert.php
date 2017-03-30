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


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_CSV.'",
                    "content": "${data}"
                }
            },
            {
                "type": "flexio.convert",
                "params": {
                    "input": {
                        "format": "${format}",
                        "delimiter": "${delimiter}",
                        "header_row": "${header}",
                        "text_qualifier": "${qualifier}"
                    }
                }
            }
        ]
        ',true);



        // TEST: Convert; default format behavior

        // BEGIN TEST
        $data = <<<EOD
"field1", "field2"
"a1", "b1"
"a2", "b2"
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => null,
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);

        // note: this line uses excel's rules
        //$expected = '[["a1"," \\"b1\\""],["a2"," \\"b2\\""]]';

        // note: this line uses php's fgetcsv rules
        $expected = '[["a1","b1"],["a2","b2"]]';
        TestCheck::assertArray('A.1', 'Convert Job; basic content upload test',  $actual, $expected, $results);
    }
}
