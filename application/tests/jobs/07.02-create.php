<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-08-26
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
        // TEST: Create; basic test with single column and row creation

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "name" => "table",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" =>"c1", "type" => "character", "width" => 3]
                ],
                "content" => [
                    ["c1" => ""],
                    ["c1" => null]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
        $expected = '
        {
            "columns": [
                { "name": "c1", "type": "character", "width": 3 }
            ],
            "content": [
                {"c1" : ""},
                {"c1" : null}
            ]
        }
        ';
        \Flexio\Tests\Check::assertInArray('A.1', 'Create Job; character field creation with row creation',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "name" => "table",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" =>"c1", "type" => "character", "width" => 3]
                ],
                "content" => [
                    ["c1" => "aBC"],
                    ["c1" => "c a"],
                    ["c1" => " -1"],
                    ["c1" => "0% "],
                    ["c1" => null]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
        $expected = '
        {
            "columns": [
                { "name": "c1", "type": "character", "width": 3 }
            ],
            "content": [
                {"c1" : "aBC"},
                {"c1" : "c a"},
                {"c1" : " -1"},
                {"c1" : "0% "},
                {"c1" : null}
            ]
        }
        ';
        \Flexio\Tests\Check::assertInArray('A.2', 'Create Job; character field creation with row creation',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "name" => "table",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" =>"c2", "type" => "character", "width" => 20]
                ],
                "content" => [
                    ["c2" => "()[]{}<>"],
                    ["c2" => "| /"],
                    ["c2" => " -1"],
                    ["c2" => "0% "],
                    ["c2" => null]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
        $expected = '
        {
            "columns": [
                { "name": "c2", "type": "character", "width": 20 }
            ],
            "content": [
                {"c2" : "()[]{}<>"},
                {"c2" : "| \\/"},
                {"c2" : " -1"},
                {"c2" : "0% "},
                {"c2" : null}
            ]
        }
        ';
        \Flexio\Tests\Check::assertInArray('A.3', 'Create Job; character field creation with row creation',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "name" => "table",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" =>"n1", "type" => "numeric", "width" => 2, "scale" => 0]
                ],
                "content" => [
                    ["n1" => "-1"],
                    ["n1" => null],
                    ["n1" => "0"],
                    ["n1" => "1"],
                    ["n1" => "2"]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
        $expected = '
        {
            "columns": [
                { "name": "n1", "type": "numeric", "width": 2, "scale": 0 }
            ],
            "content": [
                {"n1" : -1},
                {"n1" : null},
                {"n1" : 0},
                {"n1" : 1},
                {"n1" : 2}
            ]
        }
        ';
        \Flexio\Tests\Check::assertInArray('A.4', 'Create Job; numeric field creation with row creation',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "name" => "table",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" =>"n2", "type" => "numeric", "width" => 10, "scale" => 2]
                ],
                "content" => [
                    ["n2" => "-1.23"],
                    ["n2" => "0.00"],
                    ["n2" => "0.99"],
                    ["n2" => "4.56"],
                    ["n2" => "2"]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
        $expected = array(
            'columns' => array(
                array("name"=>"n2", "type"=>"numeric", "width"=>10, "scale"=>2)
            ),
            'content' => array(
                array("n2" => (float)-1.23),
                array("n2" => (float)0.00),
                array("n2" => (float)0.99),
                array("n2" => (float)4.56),
                array("n2" => (float)2.00)
            )
        );
        \Flexio\Tests\Check::assertInArray('A.5', 'Create Job; numeric field creation with row creation',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "name" => "table",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" =>"d1", "type" => "date"]
                ],
                "content" => [
                    ["d1" => "1776-07-04"],
                    ["d1" => "1970-11-22"],
                    ["d1" => "1999-12-31"],
                    ["d1" => "2000-01-01"],
                    ["d1" => null]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
        $expected = '
        {
            "columns": [
                { "name": "d1", "type": "date" }
            ],
            "content": [
                {"d1" : "1776-07-04"},
                {"d1" : "1970-11-22"},
                {"d1" : "1999-12-31"},
                {"d1" : "2000-01-01"},
                {"d1" : null}
            ]
        }
        ';
        \Flexio\Tests\Check::assertInArray('A.6', 'Create Job; date field creation with row creation',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "name" => "table",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" =>"b1", "type" => "boolean"]
                ],
                "content" => [
                    ["b1" => true],
                    ["b1" => false],
                    ["b1" => null],
                    ["b1" => false],
                    ["b1" => true]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
        $expected = '
        {
            "columns": [
                { "name": "b1", "type": "boolean" }
            ],
            "content": [
                {"b1" : true},
                {"b1" : false},
                {"b1" : null},
                {"b1" : false},
                {"b1" : true}
            ]
        }
        ';
        \Flexio\Tests\Check::assertInArray('A.7', 'Create Job; boolean field creation with row creation',  $actual, $expected, $results);



        // TEST: Create; basic test with multiple column and row creation

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "name" => "table",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "c1", "type" => "character", "width" => 3],
                    ["name" => "c2", "type" => "character", "width" => 20],
                    ["name" => "n1", "type" => "numeric", "width" => 2, "scale" => 0],
                    ["name" => "n2", "type" => "numeric", "width" => 10, "scale" => 2],
                    ["name" => "d1", "type" => "date"],
                    ["name" => "b1", "type" => "boolean"]
                ],
                "content" => [
                    ["c1" => "aBC", "c2" => "()[]{}<>", "n1" => "-1", "n2" => "-1.23", "d1" => "1776-07-04", "b1" => true],
                    ["c1" => "c a", "c2" => "| /", "n1" => null, "n2" => "0.00", "d1" => "1970-11-22", "b1" => null],
                    ["c1" => " -1", "c2" => ":;\"'", "n1" => "0", "n2" => "0.99", "d1" => "1999-12-31", "b1" => false],
                    ["c1" => "0% ", "c2" => ",.?", "n1" => "1", "n2" => "4.56", "d1" => "2000-01-01", "b1" => null],
                    ["c1" => null,  "c2" => "~`!@#$%^&*-+_=", "n1" => "2.00", "n2" => null, "d1" => null, "b1" => true]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
        $expected = '
        {
            "columns": [
                { "name": "c1", "type": "character", "width": 3 },
                { "name": "c2", "type": "character", "width": 20 },
                { "name": "n1", "type": "numeric", "width": 2, "scale": 0 },
                { "name": "n2", "type": "numeric", "width": 10, "scale": 2 },
                { "name": "d1", "type": "date" },
                { "name": "b1", "type": "boolean" }
            ],
            "content": [
                { "c1": "aBC", "c2": "()[]{}<>", "n1": -1, "n2": -1.23, "d1": "1776-07-04", "b1": true },
                { "c1": "c a", "c2": "| \\/", "n1": null, "n2": 0.00, "d1": "1970-11-22", "b1": null },
                { "c1": " -1", "c2": ":;\"\'", "n1": 0, "n2": 0.99, "d1": "1999-12-31", "b1": false },
                { "c1": "0% ", "c2": ",.?", "n1": 1, "n2": 4.56, "d1": "2000-01-01", "b1": null },
                { "c1": null,  "c2": "~`!@#$%^&*-+_=", "n1": 2, "n2": null, "d1": null, "b1": true }
            ]
        }
        ';
        \Flexio\Tests\Check::assertInArray('B.1', 'Create Job; multiple field creation with row creation',  $actual, $expected, $results);
    }
}
