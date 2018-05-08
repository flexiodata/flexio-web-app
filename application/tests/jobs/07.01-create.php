<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-08-25
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
        // TODO: basic tests for valid input:
        // 2. missing params (e.g. name, type)
        // 3. name variations
        // 4. type variations
        // 5. width variations
        // 6. scale variations
        // 7. invalid type param


        // SETUP



        // TEST: Table Creation; no columns

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "name" => "table",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                ],
                "content" => "bad content"
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = $process->hasError();
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', 'Table Creation; fail when a job definition is invalid',  $actual, $expected, $results);



        // TEST: Create; text column creation

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "name" => "table",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f1", "type" => "text"]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = $process->getStdout()->getStructure()->get();
        $expected = '[{ "name": "f1", "type": "text" }]';
        \Flexio\Tests\Check::assertInArray('B.1', 'Create Job; text column creation, no scale',  $actual, $expected, $results);



        // TEST: Create; character column creation

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "name" => "table",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f1", "type" => "character", "width" => null]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = $process->getStdout()->getStructure()->get();
        $expected = '[{ "name": "f1", "type": "character", "width": null }]';
        \Flexio\Tests\Check::assertInArray('C.1', 'Create Job; character column creation, no scale',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "name" => "table",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f1", "type" => "character", "width" => null, "scale" => null]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = $process->getStdout()->getStructure()->get();
        $expected = '[{ "name": "f1", "type": "character", "width": null, "scale": null }]';
        \Flexio\Tests\Check::assertInArray('C.2', 'Create Job; character column creation, no scale',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "name" => "table",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f1", "type" => "character", "width" => 10, "scale" => 2]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = $process->getStdout()->getStructure()->get();
        $expected = '[{ "name": "f1", "type": "character", "width": 10 }]';
        \Flexio\Tests\Check::assertInArray('C.3', 'Create Job; character column creation',  $actual, $expected, $results);



        // TEST: Create; numeric column creation

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "name" => "table",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f1", "type" => "numeric", "width" => 14, "scale" => 4]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = $process->getStdout()->getStructure()->get();
        $expected = '[{ "name": "f1", "type": "numeric", "width": 14, "scale": 4 }]';
        \Flexio\Tests\Check::assertInArray('D.1', 'Create Job; numeric column creation',  $actual, $expected, $results);



        // TEST: Create; double column creation

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "name" => "table",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f1", "type" => "double", "width" => 14, "scale" => 6]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = $process->getStdout()->getStructure()->get();
        $expected = '[{ "name": "f1", "type": "double" }]';
        \Flexio\Tests\Check::assertInArray('E.1', 'Create Job; double column creation',  $actual, $expected, $results);



        // TEST: Create; integer field creation

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "name" => "table",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f1", "type" => "integer", "width" => 5, "scale" => 2]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = $process->getStdout()->getStructure()->get();
        $expected = '[{ "name": "f1", "type": "integer" }]';
        \Flexio\Tests\Check::assertInArray('F.1', 'Create Job; integer column creation',  $actual, $expected, $results);



        // TEST: Create; date field creation

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "name" => "table",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f1", "type" => "date", "width" => 5, "scale" => 2]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = $process->getStdout()->getStructure()->get();
        $expected = '[{ "name": "f1", "type": "date" }]';
        \Flexio\Tests\Check::assertInArray('G.1', 'Create Job; date column creation',  $actual, $expected, $results);



        // TEST: Create; datetime field creation

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "name" => "table",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f1", "type" => "datetime", "width" => 5, "scale" => 2]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = $process->getStdout()->getStructure()->get();
        $expected = '[{ "name": "f1", "type": "datetime" }]';
        \Flexio\Tests\Check::assertInArray('H.1', 'Create Job; datetime column creation', $actual, $expected, $results);



        // TEST: Create; boolean field creation

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "name" => "table",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f1", "type" => "boolean", "width" => 5, "scale" => 2]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = $process->getStdout()->getStructure()->get();
        $expected = '[{ "name": "f1", "type": "boolean" }]';
        \Flexio\Tests\Check::assertInArray('H.1', 'Create Job; datetime column creation',  $actual, $expected, $results);



        // TEST: Create; basic test with multiple columns and no values

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "name" => "table",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f1", "type" => "character", "width" => 10, "scale" => 0],
                    ["name" => "f2", "type" => "numeric",   "width" => 10, "scale" => 2],
                    ["name" => "f3", "type" => "date",      "width" => 4,  "scale" => 0]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = $process->getStdout()->getStructure()->get();
        $expected = '
        [
            { "name": "f1", "type": "character", "width": 10,   "scale": null },
            { "name": "f2", "type": "numeric",   "width": 10,   "scale": 2    },
            { "name": "f3", "type": "date",      "width": 8,    "scale": null }
        ]
        ';
        \Flexio\Tests\Check::assertInArray('I.2', 'Create Job; make sure structure is properly created',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "name" => "table",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f1", "type" => "character", "width" => 10, "scale" => 0],
                    ["name" => "f2", "type" => "numeric",   "width" => 10, "scale" => 2],
                    ["name" => "f3", "type" => "date",      "width" => 4,  "scale" => 0]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = $process->getStdout()->getStructure()->get();
        $expected = '
        [
            { "name": "f1", "type": "character", "width": 10 },
            { "name": "f2", "type": "numeric",   "width": 10, "scale": 2 },
            { "name": "f3", "type": "date" }
        ]
        ';
        \Flexio\Tests\Check::assertInArray('I.2', 'Create Job; make sure structure is properly created',  $actual, $expected, $results);
    }
}
