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
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"character","width":10,"scale":0}
                	],
                	"content": [
                		["a"],
                		[null],
                		["A"],
                		["aa Bb CC"]
                	]
                }
            },
            {
                "type": "flexio.transform",
                "params": {
                    "columns": [
                        "f"
                    ],
                    "operations": [
                        { "operation": "case", "case": "unknown" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
		TestCheck::assertString('A.1', 'Transform Job; if capitalization mode is set to bad parameter, job should fail',  $actual, $expected, $results);

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"character","width":10,"scale":0}
                	],
                	"content": [
                		["a"],
                		[null],
                		["A"],
                		["aa Bb CC"]
                	]
                }
            },
            {
                "type": "flexio.transform",
                "params": {
                    "columns": [
                        "f"
                    ],
                    "operations": [
                        { "operation": "case", "case": "upper" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		["A"],
        		[null],
        		["A"],
        		["AA BB CC"]
        	]
        }
        ';
		TestCheck::assertInArray('A.2', 'Transform Job; standardize text with capitalization',  $actual, $expected, $results);

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"character","width":10,"scale":0}
                	],
                	"content": [
                		["a"],
                		[null],
                		["A"],
                		["aa Bb CC"]
                	]
                }
            },
            {
                "type": "flexio.transform",
                "params": {
                    "columns": [
                        "f"
                    ],
                    "operations": [
                        { "operation": "case", "case": "lower" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		["a"],
        		[null],
        		["a"],
        		["aa bb cc"]
        	]
        }
        ';
		TestCheck::assertInArray('A.3', 'Transform Job; standardize text with capitalization',  $actual, $expected, $results);

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"character","width":10,"scale":0}
                	],
                	"content": [
                		["a"],
                		[null],
                		["A"],
                		["aa Bb CC"]
                	]
                }
            },
            {
                "type": "flexio.transform",
                "params": {
                    "columns": [
                        "f"
                    ],
                    "operations": [
                        { "operation": "case", "case": "proper" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		["A"],
        		[null],
        		["A"],
        		["Aa Bb Cc"]
        	]
        }
        ';
		TestCheck::assertInArray('A.4', 'Transform Job; standardize text with capitalization',  $actual, $expected, $results);

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"character","width":10,"scale":0}
                	],
                	"content": [
                		["a"],
                		[null],
                		["A"],
                		["aa Bb CC"]
                	]
                }
            },
            {
                "type": "flexio.transform",
                "params": {
                    "columns": [
                        "f"
                    ],
                    "operations": [
                        { "operation": "case", "case": "first-letter" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		["A"],
        		[null],
        		["A"],
        		["Aa bb cc"]
        	]
        }
        ';
		TestCheck::assertInArray('A.5', 'Transform Job; standardize text with capitalization',  $actual, $expected, $results);



        // TEST: Transform Job; standardize text with pad (left, right)

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"character","width":10,"scale":0}
                	],
                	"content": [
                		["a"],
                		[null],
                		["Abc"],
                		["aa Bb CC"]
                	]
                }
            },
            {
                "type": "flexio.transform",
                "params": {
                    "columns": [
                        "f"
                    ],
                    "operations": [
                        { "operation": "pad", "location": "unknown", "length": 7, "value": "0" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
		TestCheck::assertString('B.1', 'Transform Job; standardize text with pad; don\'t do anything if the location isn\'t recognized',  $actual, $expected, $results);

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"character","width":10,"scale":0}
                	],
                	"content": [
                		["a"],
                		[null],
                		["Abc"],
                		["aa Bb CC"]
                	]
                }
            },
            {
                "type": "flexio.transform",
                "params": {
                    "columns": [
                        "f"
                    ],
                    "operations": [
                        { "operation": "pad", "location": "left", "length": 7, "value": "0" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		["000000a"],
        		[null],
        		["0000Abc"],
        		["aa Bb C"]
        	]
        }
        ';
		TestCheck::assertInArray('B.2', 'Transform Job; standardize text with pad',  $actual, $expected, $results);

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"character","width":10,"scale":0}
                	],
                	"content": [
                		["a"],
                		[null],
                		["Abc"],
                		["aa Bb CC"]
                	]
                }
            },
            {
                "type": "flexio.transform",
                "params": {
                    "columns": [
                        "f"
                    ],
                    "operations": [
                        { "operation": "pad", "location": "left", "length": 1, "value": "0" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		["a"],
        		[null],
        		["A"],
        		["a"]
        	]
        }
        ';
		TestCheck::assertInArray('B.3', 'Transform Job; standardize text with pad',  $actual, $expected, $results);

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"character","width":10,"scale":0}
                	],
                	"content": [
                		["a"],
                		[null],
                		["Abc"],
                		["aa Bb CC"]
                	]
                }
            },
            {
                "type": "flexio.transform",
                "params": {
                    "columns": [
                        "f"
                    ],
                    "operations": [
                        { "operation": "pad", "location": "left", "length": 12, "value": "0" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		["0000000000"],
        		[null],
        		["000000000A"],
        		["0000aa Bb "]
        	]
        }
        ';
		TestCheck::assertInArray('B.4', 'Transform Job; standardize text with pad',  $actual, $expected, $results);

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"character","width":10,"scale":0}
                	],
                	"content": [
                		["a"],
                		[null],
                		["Abc"],
                		["aa Bb CC"]
                	]
                }
            },
            {
                "type": "flexio.transform",
                "params": {
                    "columns": [
                        "f"
                    ],
                    "operations": [
                        { "operation": "pad", "location": "left", "length": 7, "value": "*" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		["******a"],
        		[null],
        		["****Abc"],
        		["aa Bb C"]
        	]
        }
        ';
		TestCheck::assertInArray('B.5', 'Transform Job; standardize text with pad',  $actual, $expected, $results);

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"numeric","width":10,"scale":0}
                	],
                	"content": [
                		["1"],
                        [null],
                		["0"],
                		["-1"],
                		["987"]
                	]
                }
            },
            {
                "type": "flexio.transform",
                "params": {
                    "columns": [
                        "f"
                    ],
                    "operations": [
                        { "operation": "type", "type": "character" },
                        { "operation": "pad", "location": "left", "length": 5, "value": "0" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		["00001"],
                [null],
        		["00000"],
        		["000-1"],
        		["00987"]
        	]
        }
        ';
		TestCheck::assertInArray('B.6', 'Transform Job; standardize text with pad',  $actual, $expected, $results);

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"numeric","width":10,"scale":2}
                	],
                	"content": [
                		["1.23"],
                        [null],
                		["0"],
                		["-1.0"],
                		["987.6"]
                	]
                }
            },
            {
                "type": "flexio.transform",
                "params": {
                    "type": "character",
                    "columns": [
                        "f"
                    ],
                    "operations": [
                        { "operation": "type", "type": "character" },
                        { "operation": "pad", "location": "left", "length": 8, "value": "0" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		["00001.23"],
                [null],
        		["00000.00"],
        		["000-1.00"],
        		["00987.60"]
        	]
        }
        ';
		TestCheck::assertInArray('B.7', 'Transform Job; standardize text with pad',  $actual, $expected, $results);

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"character","width":10,"scale":0}
                	],
                	"content": [
                		["a"],
                		[null],
                		["Abc"],
                		["aa Bb CC"]
                	]
                }
            },
            {
                "type": "flexio.transform",
                "params": {
                    "columns": [
                        "f"
                    ],
                    "operations": [
                        { "operation": "pad", "location": "right", "length": 7, "value": "0" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		["a000000"],
        		[null],
        		["Abc0000"],
        		["aa Bb C"]
        	]
        }
        ';
		TestCheck::assertInArray('B.8', 'Transform Job; standardize text with pad',  $actual, $expected, $results);

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"character","width":10,"scale":0}
                	],
                	"content": [
                		["a"],
                		[null],
                		["Abc"],
                		["aa Bb CC"]
                	]
                }
            },
            {
                "type": "flexio.transform",
                "params": {
                    "columns": [
                        "f"
                    ],
                    "operations": [
                        { "operation": "pad", "location": "right", "length": 1, "value": "0" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		["a"],
        		[null],
        		["A"],
        		["a"]
        	]
        }
        ';
		TestCheck::assertInArray('B.9', 'Transform Job; standardize text with pad',  $actual, $expected, $results);

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"character","width":10,"scale":0}
                	],
                	"content": [
                		["a"],
                		[null],
                		["Abc"],
                		["aa Bb CC"]
                	]
                }
            },
            {
                "type": "flexio.transform",
                "params": {
                    "columns": [
                        "f"
                    ],
                    "operations": [
                        { "operation": "pad", "location": "right", "length": 12, "value": "0" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		["a000000000"],
        		[null],
        		["Abc0000000"],
        		["aa Bb CC00"]
        	]
        }
        ';
		TestCheck::assertInArray('B.10', 'Transform Job; standardize text with pad',  $actual, $expected, $results);

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"character","width":10,"scale":0}
                	],
                	"content": [
                		["a"],
                		[null],
                		["Abc"],
                		["aa Bb CC"]
                	]
                }
            },
            {
                "type": "flexio.transform",
                "params": {
                    "columns": [
                        "f"
                    ],
                    "operations": [
                        { "operation": "pad", "location": "right", "length": 7, "value": "*" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		["a******"],
        		[null],
        		["Abc****"],
        		["aa Bb C"]
        	]
        }
        ';
		TestCheck::assertInArray('B.11', 'Transform Job; standardize text with pad',  $actual, $expected, $results);

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"numeric","width":10,"scale":0}
                	],
                	"content": [
                		["1"],
                        [null],
                		["0"],
                		["-1"],
                		["987"]
                	]
                }
            },
            {
                "type": "flexio.transform",
                "params": {
                    "columns": [
                        "f"
                    ],
                    "operations": [
                        { "operation": "type", "type": "character" },
                        { "operation": "pad", "location": "right", "length": 5, "value": "0" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		["10000"],
                [null],
        		["00000"],
        		["-1000"],
        		["98700"]
        	]
        }
        ';
		TestCheck::assertInArray('B.12', 'Transform Job; standardize text with pad',  $actual, $expected, $results);

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"numeric","width":10,"scale":2}
                	],
                	"content": [
                		["1.23"],
                        [null],
                		["0"],
                		["-1.0"],
                		["987.6"]
                	]
                }
            },
            {
                "type": "flexio.transform",
                "params": {
                    "columns": [
                        "f"
                    ],
                    "operations": [
                        { "operation": "type", "type": "character" },
                        { "operation": "pad", "location": "right", "length": 8, "value": "0" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		["1.230000"],
                [null],
        		["0.000000"],
        		["-1.00000"],
        		["987.6000"]
        	]
        }
        ';
		TestCheck::assertInArray('B.13', 'Transform Job; standardize text with pad',  $actual, $expected, $results);



        // TEST: Transform Job; standardize text with trim spaces (leading, trailing, leading and trailing)

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"character","width":15,"scale":0}
                	],
                	"content": [
                		[" a"],
                		[null],
                		["A "],
                		["  aa Bb CC  "]
                	]
                }
            },
            {
                "type": "flexio.transform",
                "params": {
                    "columns": [
                        "f"
                    ],
                    "operations": [
                        { "operation": "trim", "location": "unknown" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
		TestCheck::assertString('C.1', 'Transform Job; standardize text with trim spaces; don\'t do anything if the type isn\'t recognized',  $actual, $expected, $results);

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"character","width":15,"scale":0}
                	],
                	"content": [
                		[" a"],
                		[null],
                		["A "],
                		["  aa Bb CC  "]
                	]
                }
            },
            {
                "type": "flexio.transform",
                "params": {
                    "columns": [
                        "f"
                    ],
                    "operations": [
                        { "operation": "trim", "location": "leading" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		["a"],
        		[null],
        		["A "],
        		["aa Bb CC  "]
        	]
        }
        ';
		TestCheck::assertInArray('C.2', 'Transform Job; standardize text with trim spaces',  $actual, $expected, $results);

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"character","width":15,"scale":0}
                	],
                	"content": [
                		[" a"],
                		[null],
                		["A "],
                		["  aa Bb CC  "]
                	]
                }
            },
            {
                "type": "flexio.transform",
                "params": {
                    "columns": [
                        "f"
                    ],
                    "operations": [
                        { "operation": "trim", "location": "trailing" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		[" a"],
        		[null],
        		["A"],
        		["  aa Bb CC"]
        	]
        }
        ';
		TestCheck::assertInArray('C.3', 'Transform Job; standardize text with trim spaces',  $actual, $expected, $results);

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"character","width":15,"scale":0}
                	],
                	"content": [
                		[" a"],
                		[null],
                		["A "],
                		["  aa Bb CC  "]
                	]
                }
            },
            {
                "type": "flexio.transform",
                "params": {
                    "columns": [
                        "f"
                    ],
                    "operations": [
                        { "operation": "trim", "location": "leading-trailing" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		["a"],
        		[null],
        		["A"],
        		["aa Bb CC"]
        	]
        }
        ';
		TestCheck::assertInArray('C.4', 'Transform Job; standardize text with trim spaces',  $actual, $expected, $results);

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"numeric","width":5,"scale":0}
                	],
                	"content": [
                		["1"],
                		[null],
                		["2"],
                		["123"]
                	]
                }
            },
            {
                "type": "flexio.transform",
                "params": {
                    "columns": [
                        "f"
                    ],
                    "operations": [
                        { "operation": "type", "type": "character" },
                        { "operation": "trim", "location": "leading-trailing" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		["1"],
        		[null],
        		["2"],
        		["123"]
        	]
        }
        ';
		TestCheck::assertInArray('C.5', 'Transform Job; standardize text with trim spaces',  $actual, $expected, $results);
    }
}
