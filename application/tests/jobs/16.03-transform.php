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
        // TODO: fill out

        // TEST: Transform Job; missing or bad parameters (e.g. type specified but something besides
        //       text or not any of the allowed types)
        // TEST: Transform Job: update values with select part (left, right, middle, part)
        // TEST: Transform Job: update values with remove text (none, selected text, selected characters, symbols, breaks, letters, numbers, regular expression)
        // TEST: combinations of operations
        // TEST: Transform Job; conversion to numeric type from character type
        // TEST: Transform Job; conversion to numeric type from numeric type (identity)
        // TEST: Transform Job; conversion to numeric type from double type
        // TEST: Transform Job; conversion to numeric type from integer type
        // TEST: Transform Job; conversion to numeric type from date type
        // TEST: Transform Job; conversion to numeric type from datetime type
        // TEST: Transform Job; conversion to numeric type from boolean type
        // TEST: Transform Job; conversion to integer type from character type
        // TEST: Transform Job; conversion to integer type from numeric type
        // TEST: Transform Job; conversion to integer type from double type
        // TEST: Transform Job; conversion to integer type from integer type (identity)
        // TEST: Transform Job; conversion to integer type from date type
        // TEST: Transform Job; conversion to integer type from datetime type
        // TEST: Transform Job; conversion to integer type from boolean type
        // TEST: Transform Job; conversion to date type from character type
        // TEST: Transform Job; conversion to date type from numeric type
        // TEST: Transform Job; conversion to date type from double type
        // TEST: Transform Job; conversion to date type from integer type
        // TEST: Transform Job; conversion to date type from date type (identity)
        // TEST: Transform Job; conversion to date type from datetime type
        // TEST: Transform Job; conversion to date type from boolean type
        // TEST: Transform Job; conversion to datetime type from character type
        // TEST: Transform Job; conversion to datetime type from numeric type
        // TEST: Transform Job; conversion to datetime type from double type
        // TEST: Transform Job; conversion to datetime type from integer type
        // TEST: Transform Job; conversion to datetime type from date type
        // TEST: Transform Job; conversion to datetime type from datetime type (identity)
        // TEST: Transform Job; conversion to datetime type from boolean type


        // SETUP



        // TEST: Transform Job; conversion to character type from character type (identity)

        // BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "name": "table",
                    "content_type": "'.\Flexio\Base\ContentType::FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"character","width":6,"scale":0}
                	],
                	"content": [
                		["a"],
                		[null],
                		["A"],
                		["a B C"]
                	]
                }
            },
            {
                "type": "flexio.transform",
                "params": {
                    "type": "character",
                    "columns": [
                        "f"
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = TestUtil::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "a"},
                {"f": null},
                {"f": "A"},
                {"f": "a B C"}
        	]
        }
        ';
		TestCheck::assertInArray('A.1', 'Transform Job; conversion from character to character (identity)',  $actual, $expected, $results);



        // TEST: Transform Job; conversion to character type from numeric type

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "name": "table",
                    "content_type": "'.\Flexio\Base\ContentType::FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"numeric","width":2,"scale":0}
                	],
                	"content": [
                		["0"],
                		[null],
                		["1"],
                		["-1"],
                		["21"],
                		["-23"]
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
                        { "operation": "type", "type": "character" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = TestUtil::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "0"},
        		{"f": null},
        		{"f": "1"},
        		{"f": "-1"},
        		{"f": "21"},
                {"f": "-23"}
        	]
        }
        ';
		TestCheck::assertInArray('B.1', 'Transform Job; conversion from numeric to character',  $actual, $expected, $results);

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "name": "table",
                    "content_type": "'.\Flexio\Base\ContentType::FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"numeric","width":14,"scale":0}
                	],
                	"content": [
                		["0"],
                		[null],
                		["99999999999999"],
                		["-99999999999999"]
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
                        { "operation": "type", "type": "character" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = TestUtil::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "0"},
        		{"f": null},
        		{"f": "99999999999999"},
        		{"f": "-99999999999999"}
        	]
        }
        ';
		TestCheck::assertInArray('B.2', 'Transform Job; conversion from numeric to character',  $actual, $expected, $results);

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "name": "table",
                    "content_type": "'.\Flexio\Base\ContentType::FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"numeric","width":4,"scale":2}
                	],
                	"content": [
                		["0.00"],
                		[null],
                		["1.23"],
                		["-1.0"],
                		["21.99"]
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
                        { "operation": "type", "type": "character" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = TestUtil::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "0.00"},
        		{"f": null},
        		{"f": "1.23"},
        		{"f": "-1.00"},
        		{"f": "21.99"}
        	]
        }
        ';
		TestCheck::assertInArray('B.3', 'Transform Job; conversion from numeric to character',  $actual, $expected, $results);



        // TEST: Transform Job; conversion to character type from double type

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "name": "table",
                    "content_type": "'.\Flexio\Base\ContentType::FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"double","width":2,"scale":0}
                	],
                	"content": [
                		["0"],
                		[null],
                		["1"],
                		["-1"],
                		["21"],
                		["-23"]
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
                        { "operation": "type", "type": "character" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = TestUtil::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "0"},
        		{"f": null},
        		{"f": "1"},
        		{"f": "-1"},
        		{"f": "21"},
                {"f": "-23"}
        	]
        }
        ';
		TestCheck::assertInArray('C.1', 'Transform Job; conversion from double to character',  $actual, $expected, $results);

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "name": "table",
                    "content_type": "'.\Flexio\Base\ContentType::FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"double","width":4,"scale":2}
                	],
                	"content": [
                		["0.00"],
                		[null],
                		["1.23"],
                		["-1.0"],
                		["21.99"]
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
                        { "operation": "type", "type": "character" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = TestUtil::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "0"},
        		{"f": null},
        		{"f": "1.23"},
        		{"f": "-1"},
        		{"f": "21.99"}
        	]
        }
        ';
		TestCheck::assertInArray('C.2', 'Transform Job; conversion from double to character',  $actual, $expected, $results);



        // TEST: Transform Job; conversion to character type from integer type

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "name": "table",
                    "content_type": "'.\Flexio\Base\ContentType::FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"integer","width":2,"scale":0}
                	],
                	"content": [
                		["0"],
                		[null],
                		["1"],
                		["-1"],
                		["21"],
                		["-23"]
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
                        { "operation": "type", "type": "character" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = TestUtil::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "0"},
        		{"f": null},
        		{"f": "1"},
        		{"f": "-1"},
        		{"f": "21"},
                {"f": "-23"}
        	]
        }
        ';
		TestCheck::assertInArray('D.1', 'Transform Job; conversion from integer to character',  $actual, $expected, $results);



        // TEST: Transform Job; conversion to character type from date type

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "name": "table",
                    "content_type": "'.\Flexio\Base\ContentType::FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"date","width":8,"scale":0}
                	],
                	"content": [
                		["2001-02-03"],
                		[null],
                		["1970-01-01"],
                		["1999-12-31"]
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
                        { "operation": "type", "type": "character" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = TestUtil::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "2001-02-03"},
        		{"f": null},
        		{"f": "1970-01-01"},
        		{"f": "1999-12-31"}
        	]
        }
        ';
		TestCheck::assertInArray('E.1', 'Transform Job; conversion from date to character',  $actual, $expected, $results);



        // TEST: Transform Job; conversion to character type from datetime type

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "name": "table",
                    "content_type": "'.\Flexio\Base\ContentType::FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"datetime","width":8,"scale":0}
                	],
                	"content": [
                		["2001-02-03 00:00:00"],
                		[null],
                		["1970-01-01 01:02:03"],
                		["1999-12-31 23:59:59"]
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
                        { "operation": "type", "type": "character" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = TestUtil::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "2001-02-03 00:00:00"},
        		{"f": null},
        		{"f": "1970-01-01 01:02:03"},
        		{"f": "1999-12-31 23:59:59"}
        	]
        }
        ';
		TestCheck::assertInArray('F.1', 'Transform Job; conversion from datetime to character',  $actual, $expected, $results);



        // TEST: Transform Job; conversion to character type from boolean type

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "name": "table",
                    "content_type": "'.\Flexio\Base\ContentType::FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"boolean","width":1,"scale":0}
                	],
                	"content": [
                		[true],
                		[null],
                		[false]
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
                        { "operation": "type", "type": "character" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = TestUtil::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"content": [
        		{"f": "true"},
        		{"f": null},
        		{"f": "false"}
        	]
        }
        ';
		TestCheck::assertInArray('G.1', 'Transform Job; conversion from boolean to character',  $actual, $expected, $results);


        // TEST: Transform Job; conversion to boolean type from character type

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "name": "table",
                    "content_type": "'.\Flexio\Base\ContentType::FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"character","width":5,"scale":0}
                	],
                	"content": [
                		[null],
                        ["a"],
                		["0"],
                		["1"],
                		["-1"],
                		["2.1"],
                		["true"],
                		["false"],
                		["True"],
                		["False"],
                		["TRUE"],
                		["FALSE"],
                		["T"],
                        ["F"]
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
                        { "operation": "type", "type": "boolean" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = TestUtil::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"boolean"}
        	],
        	"content": [
                {"f": null},
                {"f": null},
                {"f": false},
                {"f": true},
                {"f": null},
                {"f": null},
                {"f": true},
                {"f": false},
                {"f": true},
                {"f": false},
                {"f": true},
                {"f": false},
                {"f": true},
                {"f": false}
        	]
        }
        ';
		TestCheck::assertInArray('H.1', 'Transform Job; conversion from character to boolean',  $actual, $expected, $results, TestCheck::FLAG_ERROR_SUPPRESS);



        // TEST: Transform Job; conversion to boolean type from numeric type

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "name": "table",
                    "content_type": "'.\Flexio\Base\ContentType::FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"numeric","width":8,"scale":0}
                	],
                	"content": [
                		[null],
                		["0"],
                		["1"],
                		["-1"],
                		["2"]
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
                        { "operation": "type", "type": "boolean" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = TestUtil::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"boolean"}
        	],
        	"content": [
        		{"f": null},
        		{"f": false},
        		{"f": true},
        		{"f": true},
        		{"f": true}
        	]
        }
        ';
		TestCheck::assertInArray('I.1', 'Transform Job; conversion from numeric to boolean',  $actual, $expected, $results, TestCheck::FLAG_ERROR_SUPPRESS);

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "name": "table",
                    "content_type": "'.\Flexio\Base\ContentType::FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"numeric","width":8,"scale":2}
                	],
                	"content": [
                		[null],
                		["0"],
                		["1.1"],
                		["-1"],
                		["2.1"]
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
                        { "operation": "type", "type": "boolean" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = TestUtil::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"boolean"}
        	],
        	"content": [
        		{"f": null},
        		{"f": false},
        		{"f": true},
        		{"f": true},
        		{"f": true}
        	]
        }
        ';
		TestCheck::assertInArray('I.2', 'Transform Job; conversion from numeric to boolean',  $actual, $expected, $results, TestCheck::FLAG_ERROR_SUPPRESS);



        // TEST: Transform Job; conversion to boolean type from double type

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "name": "table",
                    "content_type": "'.\Flexio\Base\ContentType::FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"double","width":4,"scale":2}
                	],
                	"content": [
                		[null],
                		["0"],
                		["1.1"],
                		["-1"],
                		["2.1"]
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
                        { "operation": "type", "type": "boolean" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = TestUtil::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"boolean"}
        	],
        	"content": [
        		{"f": null},
        		{"f": false},
        		{"f": true},
        		{"f": true},
        		{"f": true}
        	]
        }
        ';
		TestCheck::assertInArray('J.1', 'Transform Job; conversion from double to boolean',  $actual, $expected, $results, TestCheck::FLAG_ERROR_SUPPRESS);



        // TEST: Transform Job; conversion to boolean type from integer type

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "name": "table",
                    "content_type": "'.\Flexio\Base\ContentType::FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"integer","width":4,"scale":0}
                	],
                	"content": [
                		[null],
                		["0"],
                		["1"],
                		["-1"],
                		["2"]
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
                        { "operation": "type", "type": "boolean" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = TestUtil::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"boolean"}
        	],
        	"content": [
        		{"f": null},
        		{"f": false},
        		{"f": true},
        		{"f": true},
        		{"f": true}
        	]
        }
        ';
		TestCheck::assertInArray('K.1', 'Transform Job; conversion from integer to boolean',  $actual, $expected, $results, TestCheck::FLAG_ERROR_SUPPRESS);



        // TEST: Transform Job; conversion to boolean type from date type

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "name": "table",
                    "content_type": "'.\Flexio\Base\ContentType::FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"date","width":4,"scale":0}
                	],
                	"content": [
                		[null],
                		["2001-01-01"],
                		["1999-12-31"]
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
                        { "operation": "type", "type": "boolean" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = TestUtil::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"boolean"}
        	],
        	"content": [
        		{"f": false},
        		{"f": true},
        		{"f": true}
        	]
        }
        ';
		TestCheck::assertInArray('L.1', 'Transform Job; conversion from date to boolean.  Non-null values are true, null-values false',  $actual, $expected, $results);



        // TEST: Transform Job; conversion to boolean type from datetime type

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "name": "table",
                    "content_type": "'.\Flexio\Base\ContentType::FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"datetime","width":8,"scale":0}
                	],
                	"content": [
                		[null],
                		["2001-01-01 01:02:03"],
                		["1999-12-31 23:59:59"]
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
                        { "operation": "type", "type": "boolean" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = TestUtil::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"boolean"}
        	],
        	"content": [
        		{"f": false},
        		{"f": true},
        		{"f": true}
        	]
        }
        ';
		TestCheck::assertInArray('M.1', 'Transform Job; conversion from datetime to boolean.  Non-null values are true, null-values false',  $actual, $expected, $results);



        // TEST: Transform Job; conversion to boolean type from boolean type (identity)

		// BEGIN TEST
		$task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "name": "table",
                    "content_type": "'.\Flexio\Base\ContentType::FLEXIO_TABLE.'",
                	"columns": [
                		{"name":"f","type":"boolean","width":1,"scale":0}
                	],
                	"content": [
                		[null],
                		[true],
                		[false]
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
                        { "operation": "type", "type": "boolean" }
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = TestUtil::getTable($process->getStdout());
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"boolean"}
        	],
        	"content": [
        		{"f": null},
        		{"f": true},
        		{"f": false}
        	]
        }
        ';
		TestCheck::assertInArray('N.1', 'Transform Job; conversion from boolean to boolean (identity)',  $actual, $expected, $results);
    }
}
