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
        // TEST: Replace Job

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "field1", "type" => "character", "width" => 3, "scale" => 0],
                    ["name" => "field2", "type" => "character", "width" => 3, "scale" => 0]
                ],
                "content" => [
                    ["a","b"],
                    ["b","B"],
                    ["c","b"]
                ]
            ],
            [
                "op" => "replace",
                "columns" => [ "field1", "field2" ],
                "find" => "b",
                "replace" => "D",
                "location" => "any",
                "match_case" => true
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["a","D"],["D","B"],["c","D"]];
        \Flexio\Tests\Check::assertString('A.1', 'Replace Job; check basic functionality',  $actual, $expected, $results);
    }
}
