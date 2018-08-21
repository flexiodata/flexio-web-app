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
        // SETUP
        $create = [
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
        ];


        // TEST: CalcField Job

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            $create,
            [
                "op" => "calc",
                "name" => "field3",
                "type" => "character",
                "expression" => "upper(field1)"
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["a","b","A"],["b","B","B"],["c","b","C"]];
        \Flexio\Tests\Check::assertString('A.1', 'CalcField Job; check basic functionality',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            $create,
            [
                "op" => "calc",
                "name" => "field3",
                "type" => "character",
                "expression" => "concat(field1,'.',field2)"
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["a","b","a.b"],["b","B","b.B"],["c","b","c.b"]];
        \Flexio\Tests\Check::assertString('A.2', 'CalcField Job; check basic functionality',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            $create,
            [
                "op" => "calc",
                "name" => "field3",
                "type" => "character",
                "expression" => "substr(concat(field1,'.',field2),2,2)"
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["a","b",".b"],["b","B",".B"],["c","b",".b"]];
        \Flexio\Tests\Check::assertString('A.3', 'CalcField Job; check basic functionality',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            $create,
            [
                "op" => "calc",
                "name" => "field3",
                "type" => "character",
                "expression" => "lpad(field2,3,'0')"
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["a","b","00b"],["b","B","00B"],["c","b","00b"]];
        \Flexio\Tests\Check::assertString('A.4', 'CalcField Job; check basic functionality',  $actual, $expected, $results);
    }
}
