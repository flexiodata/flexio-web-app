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
    private static function buildTask(string $data) : array
    {
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "content_type" => \Flexio\Base\ContentType::CSV,
                "content" => base64_encode(trim($data))
            ],
            [
                "op" => "convert",
                "input" => [
                    "delimiter" => "{comma}",
                    "header_row" => "true",
                    "text_qualifier" => "{double_quote}"
                ]
            ]
        ]);
        return $task;
    }

    public function run(&$results)
    {
        // TEST: Convert; default format behavior

        // BEGIN TEST
        $data = <<<EOD
"field1", "field2"
"a1", "b1"
"a2", "b2"
EOD;
$process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());

        // note: this line uses excel's rules
        //$expected = '[["a1"," \\"b1\\""],["a2"," \\"b2\\""]]';

        // note: this line uses php's fgetcsv rules
        $expected = '[["a1","b1"],["a2","b2"]]';
        \Flexio\Tests\Check::assertArray('A.1', 'Convert Job; basic content upload test',  $actual, $expected, $results);
    }
}
