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
        // TEST: Transform Job: standardize text with capitalization (none, lowercase, uppercase, proper, first letter)

		// BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f", "type" => "character", "width" => 10, "scale" => 0]
                ],
                "content" => [
                    ["a"],
                    [null],
                    ["A"],
                    ["aa Bb CC"]
                ]
            ],
            [
                "op" => "transform",
                "columns" => ["f"],
                "operations" => [
                    ["operation" => "case", "case" => "unknown"]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = $process->hasError();
        $expected = true;
		\Flexio\Tests\Check::assertBoolean('A.1', 'Transform Job; if capitalization mode is set to bad parameter, job should fail',  $actual, $expected, $results);

		// BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f", "type" => "character", "width" => 10, "scale" => 0]
                ],
                "content" => [
                    ["a"],
                    [null],
                    ["A"],
                    ["aa Bb CC"]
                ]
            ],
            [
                "op" => "transform",
                "columns" => ["f"],
                "operations" => [
                    ["operation" => "case", "case" => "upper"]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "A"},
        		{"f": null},
        		{"f": "A"},
        		{"f": "AA BB CC"}
        	]
        }
        ';
		\Flexio\Tests\Check::assertInArray('A.2', 'Transform Job; standardize text with capitalization',  $actual, $expected, $results);

		// BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f", "type" => "character", "width" => 10, "scale" => 0]
                ],
                "content" => [
                    ["a"],
                    [null],
                    ["A"],
                    ["aa Bb CC"]
                ]
            ],
            [
                "op" => "transform",
                "columns" => ["f"],
                "operations" => [
                    ["operation" => "case", "case" => "lower"]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "a"},
        		{"f": null},
        		{"f": "a"},
        		{"f": "aa bb cc"}
        	]
        }
        ';
		\Flexio\Tests\Check::assertInArray('A.3', 'Transform Job; standardize text with capitalization',  $actual, $expected, $results);

		// BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f", "type" => "character", "width" => 10, "scale" => 0]
                ],
                "content" => [
                    ["a"],
                    [null],
                    ["A"],
                    ["aa Bb CC"]
                ]
            ],
            [
                "op" => "transform",
                "columns" => ["f"],
                "operations" => [
                    ["operation" => "case", "case" => "proper"]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "A"},
        		{"f": null},
        		{"f": "A"},
        		{"f": "Aa Bb Cc"}
        	]
        }
        ';
		\Flexio\Tests\Check::assertInArray('A.4', 'Transform Job; standardize text with capitalization',  $actual, $expected, $results);

		// BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f", "type" => "character", "width" => 10, "scale" => 0]
                ],
                "content" => [
                    ["a"],
                    [null],
                    ["A"],
                    ["aa Bb CC"]
                ]
            ],
            [
                "op" => "transform",
                "columns" => ["f"],
                "operations" => [
                    ["operation" => "case", "case" => "first-letter"]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "A"},
        		{"f": null},
        		{"f": "A"},
        		{"f": "Aa bb cc"}
        	]
        }
        ';
		\Flexio\Tests\Check::assertInArray('A.5', 'Transform Job; standardize text with capitalization',  $actual, $expected, $results);



        // TEST: Transform Job; standardize text with pad (left, right)

		// BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f", "type" => "character", "width" => 10, "scale" => 0]
                ],
                "content" => [
                    ["a"],
                    [null],
                    ["A"],
                    ["aa Bb CC"]
                ]
            ],
            [
                "op" => "transform",
                "columns" => ["f"],
                "operations" => [
                    ["operation" => "pad", "location" => "unknown", "length" => 7, "value" => 0]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = $process->hasError();
        $expected = true;
		\Flexio\Tests\Check::assertString('B.1', 'Transform Job; standardize text with pad; don\'t do anything if the location isn\'t recognized',  $actual, $expected, $results);

		// BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f", "type" => "character", "width" => 10, "scale" => 0]
                ],
                "content" => [
                    ["a"],
                    [null],
                    ["Abc"],
                    ["aa Bb CC"]
                ]
            ],
            [
                "op" => "transform",
                "columns" => ["f"],
                "operations" => [
                    ["operation" => "pad", "location" => "left", "length" => 7, "value" => "0"]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "000000a"},
        		{"f": null},
        		{"f": "0000Abc"},
        		{"f": "aa Bb C"}
        	]
        }
        ';
		\Flexio\Tests\Check::assertInArray('B.2', 'Transform Job; standardize text with pad',  $actual, $expected, $results);

		// BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f", "type" => "character", "width" => 10, "scale" => 0]
                ],
                "content" => [
                    ["a"],
                    [null],
                    ["Abc"],
                    ["aa Bb CC"]
                ]
            ],
            [
                "op" => "transform",
                "columns" => ["f"],
                "operations" => [
                    ["operation" => "pad", "location" => "left", "length" => 1, "value" => "0"]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "a"},
        		{"f": null},
        		{"f": "A"},
        		{"f": "a"}
        	]
        }
        ';
		\Flexio\Tests\Check::assertInArray('B.3', 'Transform Job; standardize text with pad',  $actual, $expected, $results);

		// BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f", "type" => "character", "width" => 10, "scale" => 0]
                ],
                "content" => [
                    ["a"],
                    [null],
                    ["Abc"],
                    ["aa Bb CC"]
                ]
            ],
            [
                "op" => "transform",
                "columns" => ["f"],
                "operations" => [
                    ["operation" => "pad", "location" => "left", "length" => 12, "value" => "0"]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "0000000000"},
        		{"f": null},
        		{"f": "000000000A"},
        		{"f": "0000aa Bb "}
        	]
        }
        ';
		\Flexio\Tests\Check::assertInArray('B.4', 'Transform Job; standardize text with pad',  $actual, $expected, $results);

		// BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f", "type" => "character", "width" => 10, "scale" => 0]
                ],
                "content" => [
                    ["a"],
                    [null],
                    ["Abc"],
                    ["aa Bb CC"]
                ]
            ],
            [
                "op" => "transform",
                "columns" => ["f"],
                "operations" => [
                    ["operation" => "pad", "location" => "left", "length" => 7, "value" => "*"]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "******a"},
        		{"f": null},
        		{"f": "****Abc"},
        		{"f": "aa Bb C"}
        	]
        }
        ';
		\Flexio\Tests\Check::assertInArray('B.5', 'Transform Job; standardize text with pad',  $actual, $expected, $results);

		// BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f", "type" => "numeric", "width" => 10, "scale" => 0]
                ],
                "content" => [
                    ["1"],
                    [null],
                    ["0"],
                    ["-1"],
                    ["987"]
                ]
            ],
            [
                "op" => "transform",
                "columns" => ["f"],
                "operations" => [
                    ["operation" => "type", "type" => "character"],
                    ["operation" => "pad", "location" => "left", "length" => 5, "value" => "0"]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "00001"},
                {"f": null},
        		{"f": "00000"},
        		{"f": "000-1"},
        		{"f": "00987"}
        	]
        }
        ';
		\Flexio\Tests\Check::assertInArray('B.6', 'Transform Job; standardize text with pad',  $actual, $expected, $results);

		// BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f", "type" => "numeric", "width" => 10, "scale" => 2]
                ],
                "content" => [
                    ["1.23"],
                    [null],
                    ["0.00"],
                    ["-1.00"],
                    ["987.60"]
                ]
            ],
            [
                "op" => "transform",
                "columns" => ["f"],
                "operations" => [
                    ["operation" => "type", "type" => "character"],
                    ["operation" => "pad", "location" => "left", "length" => 8, "value" => "0"]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "00001.23"},
                {"f": null},
        		{"f": "00000.00"},
        		{"f": "000-1.00"},
        		{"f": "00987.60"}
        	]
        }
        ';
		\Flexio\Tests\Check::assertInArray('B.7', 'Transform Job; standardize text with pad',  $actual, $expected, $results);

		// BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f", "type" => "character", "width" => 10, "scale" => 0]
                ],
                "content" => [
                    ["a"],
                    [null],
                    ["Abc"],
                    ["aa Bb CC"]
                ]
            ],
            [
                "op" => "transform",
                "columns" => ["f"],
                "operations" => [
                    ["operation" => "pad", "location" => "right", "length" => 7, "value" => "0"]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "a000000"},
        		{"f": null},
        		{"f": "Abc0000"},
        		{"f": "aa Bb C"}
        	]
        }
        ';
		\Flexio\Tests\Check::assertInArray('B.8', 'Transform Job; standardize text with pad',  $actual, $expected, $results);

		// BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f", "type" => "character", "width" => 10, "scale" => 0]
                ],
                "content" => [
                    ["a"],
                    [null],
                    ["Abc"],
                    ["aa Bb CC"]
                ]
            ],
            [
                "op" => "transform",
                "columns" => ["f"],
                "operations" => [
                    ["operation" => "pad", "location" => "right", "length" => 1, "value" => "0"]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "a"},
        		{"f": null},
        		{"f": "A"},
        		{"f": "a"}
        	]
        }
        ';
		\Flexio\Tests\Check::assertInArray('B.9', 'Transform Job; standardize text with pad',  $actual, $expected, $results);

		// BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f", "type" => "character", "width" => 10, "scale" => 0]
                ],
                "content" => [
                    ["a"],
                    [null],
                    ["Abc"],
                    ["aa Bb CC"]
                ]
            ],
            [
                "op" => "transform",
                "columns" => ["f"],
                "operations" => [
                    ["operation" => "pad", "location" => "right", "length" => 12, "value" => "0"]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "a000000000"},
        		{"f": null},
        		{"f": "Abc0000000"},
        		{"f": "aa Bb CC00"}
        	]
        }
        ';
		\Flexio\Tests\Check::assertInArray('B.10', 'Transform Job; standardize text with pad',  $actual, $expected, $results);

		// BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f", "type" => "character", "width" => 10, "scale" => 0]
                ],
                "content" => [
                    ["a"],
                    [null],
                    ["Abc"],
                    ["aa Bb CC"]
                ]
            ],
            [
                "op" => "transform",
                "columns" => ["f"],
                "operations" => [
                    ["operation" => "pad", "location" => "right", "length" => 7, "value" => "*"]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "a******"},
        		{"f": null},
        		{"f": "Abc****"},
        		{"f": "aa Bb C"}
        	]
        }
        ';
		\Flexio\Tests\Check::assertInArray('B.11', 'Transform Job; standardize text with pad',  $actual, $expected, $results);

		// BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f", "type" => "numeric", "width" => 10, "scale" => 0]
                ],
                "content" => [
                    ["1"],
                    [null],
                    ["0"],
                    ["-1"],
                    ["987"]
                ]
            ],
            [
                "op" => "transform",
                "columns" => ["f"],
                "operations" => [
                    ["operation" => "type", "type" => "character"],
                    ["operation" => "pad", "location" => "right", "length" => 5, "value" => "0"]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "10000"},
                {"f": null},
        		{"f": "00000"},
        		{"f": "-1000"},
        		{"f": "98700"}
        	]
        }
        ';
		\Flexio\Tests\Check::assertInArray('B.12', 'Transform Job; standardize text with pad',  $actual, $expected, $results);

		// BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f", "type" => "numeric", "width" => 10, "scale" => 2]
                ],
                "content" => [
                    ["1.23"],
                    [null],
                    ["0"],
                    ["-1.0"],
                    ["987.6"]
                ]
            ],
            [
                "op" => "transform",
                "columns" => ["f"],
                "operations" => [
                    ["operation" => "type", "type" => "character"],
                    ["operation" => "pad", "location" => "right", "length" => 8, "value" => "0"]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "1.230000"},
                {"f": null},
        		{"f": "0.000000"},
        		{"f": "-1.00000"},
        		{"f": "987.6000"}
        	]
        }
        ';
		\Flexio\Tests\Check::assertInArray('B.13', 'Transform Job; standardize text with pad',  $actual, $expected, $results);



        // TEST: Transform Job; standardize text with trim spaces (leading, trailing, leading and trailing)

		// BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f", "type" => "character", "width" => 15, "scale" => 2]
                ],
                "content" => [
                    [" a"],
                    [null],
                    ["A "],
                    ["  aa Bb CC  "]
                ]
            ],
            [
                "op" => "transform",
                "columns" => ["f"],
                "operations" => [
                    ["operation" => "trim", "location" => "unknown"]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = $process->hasError();
        $expected = true;
		\Flexio\Tests\Check::assertString('C.1', 'Transform Job; standardize text with trim spaces; don\'t do anything if the type isn\'t recognized',  $actual, $expected, $results);

		// BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f", "type" => "character", "width" => 15, "scale" => 2]
                ],
                "content" => [
                    [" a"],
                    [null],
                    ["A "],
                    ["  aa Bb CC  "]
                ]
            ],
            [
                "op" => "transform",
                "columns" => ["f"],
                "operations" => [
                    ["operation" => "trim", "location" => "leading"]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "a"},
        		{"f": null},
        		{"f": "A "},
        		{"f": "aa Bb CC  "}
        	]
        }
        ';
		\Flexio\Tests\Check::assertInArray('C.2', 'Transform Job; standardize text with trim spaces',  $actual, $expected, $results);

		// BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f", "type" => "character", "width" => 15, "scale" => 2]
                ],
                "content" => [
                    [" a"],
                    [null],
                    ["A "],
                    ["  aa Bb CC  "]
                ]
            ],
            [
                "op" => "transform",
                "columns" => ["f"],
                "operations" => [
                    ["operation" => "trim", "location" => "trailing"]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": " a"},
        		{"f": null},
        		{"f": "A"},
        		{"f": "  aa Bb CC"}
        	]
        }
        ';
		\Flexio\Tests\Check::assertInArray('C.3', 'Transform Job; standardize text with trim spaces',  $actual, $expected, $results);

		// BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f", "type" => "character", "width" => 15, "scale" => 2]
                ],
                "content" => [
                    [" a"],
                    [null],
                    ["A "],
                    ["  aa Bb CC  "]
                ]
            ],
            [
                "op" => "transform",
                "columns" => ["f"],
                "operations" => [
                    ["operation" => "trim", "location" => "leading-trailing"]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "a"},
        		{"f": null},
        		{"f": "A"},
        		{"f": "aa Bb CC"}
        	]
        }
        ';
		\Flexio\Tests\Check::assertInArray('C.4', 'Transform Job; standardize text with trim spaces',  $actual, $expected, $results);

		// BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "content_type" => \Flexio\Base\ContentType::FLEXIO_TABLE,
                "columns" => [
                    ["name" => "f", "type" => "numeric", "width" => 5, "scale" => 2]
                ],
                "content" => [
                    ["1"],
                    [null],
                    ["2"],
                    ["123"]
                ]
            ],
            [
                "op" => "transform",
                "columns" => ["f"],
                "operations" => [
                    ["operation" => "type", "type" => "character"],
                    ["operation" => "trim", "location" => "leading-trailing"]
                ]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = \Flexio\Tests\Content::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "1.00"},
        		{"f": null},
        		{"f": "2.00"},
        		{"f": "123.00"}
        	]
        }
        ';
		\Flexio\Tests\Check::assertInArray('C.5', 'Transform Job; standardize text with trim spaces',  $actual, $expected, $results);
    }
}
