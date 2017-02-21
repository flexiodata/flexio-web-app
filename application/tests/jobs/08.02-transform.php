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


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
		$task = '
        [
            {
                "type": "flexio.create",
                "params": {
                	"columns": [
                		{"name":"f1","type":"character","width":"10","scale":"0"}
                	],
                	"rows": [
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
                    "columns": [
                        "{input.fieldname}"
                    ],
                    "type": "${output.type}",
                    "width": "${output.width}",
                    "scale": "${output.scale}"
                }
            }
        ]
        ';



        // TEST: Transform Job; conversion to character type from character type (identity)
/*
		// BEGIN TEST

        $params = [
            "input.fieldname" => "f1",
            "output.type" => "character",
            "output.width" => null,
            "output.scale" => null
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [{"name":"f1","type":"character","width":10,"scale":0}],
        	"rows": [["a"],[null],["A"],["a B C"]]
        }
        ';
		TestCheck::assertArray('A.1', 'Transform Job; conversion from character to character (identity)',  $actual, $expected, $results);
*/



        // BEGIN TEST
		$task = '
        [
            {
                "type": "flexio.create",
                "params": {
                	"columns": [
                		{"name":"f","type":"character","width":6,"scale":0}
                	],
                	"rows": [
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
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"rows": [
        		["a"],
        		[null],
        		["A"],
        		["a B C"]
        	]
        }
        ';
		TestCheck::assertInArray('A.1', 'Transform Job; conversion from character to character (identity)',  $actual, $expected, $results);



        // TEST: Transform Job; conversion to character type from numeric type

		// BEGIN TEST
		$task = '
        [
            {
                "type": "flexio.create",
                "params": {
                	"columns": [
                		{"name":"f","type":"numeric","width":2,"scale":0}
                	],
                	"rows": [
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
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"rows": [
        		["0"],
        		[null],
        		["1"],
        		["-1"],
        		["21"],
                ["-23"]
        	]
        }
        ';
		TestCheck::assertInArray('B.1', 'Transform Job; conversion from numeric to character',  $actual, $expected, $results);

		// BEGIN TEST
		$task = '
        [
            {
                "type": "flexio.create",
                "params": {
                	"columns": [
                		{"name":"f","type":"numeric","width":14,"scale":0}
                	],
                	"rows": [
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
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"rows": [
        		["0"],
        		[null],
        		["99999999999999"],
        		["-99999999999999"]
        	]
        }
        ';
		TestCheck::assertInArray('B.2', 'Transform Job; conversion from numeric to character',  $actual, $expected, $results);

		// BEGIN TEST
		$task = '
        [
            {
                "type": "flexio.create",
                "params": {
                	"columns": [
                		{"name":"f","type":"numeric","width":4,"scale":2}
                	],
                	"rows": [
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
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"rows": [
        		["0.00"],
        		[null],
        		["1.23"],
        		["-1.00"],
        		["21.99"]
        	]
        }
        ';
		TestCheck::assertInArray('B.3', 'Transform Job; conversion from numeric to character',  $actual, $expected, $results);



        // TEST: Transform Job; conversion to character type from double type

		// BEGIN TEST
		$task = '
        [
            {
                "type": "flexio.create",
                "params": {
                	"columns": [
                		{"name":"f","type":"double","width":2,"scale":0}
                	],
                	"rows": [
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
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"rows": [
        		["0"],
        		[null],
        		["1"],
        		["-1"],
        		["21"],
                ["-23"]
        	]
        }
        ';
		TestCheck::assertInArray('C.1', 'Transform Job; conversion from double to character',  $actual, $expected, $results);

		// BEGIN TEST
		$task = '
        [
            {
                "type": "flexio.create",
                "params": {
                	"columns": [
                		{"name":"f","type":"double","width":4,"scale":2}
                	],
                	"rows": [
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
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"rows": [
        		["0"],
        		[null],
        		["1.23"],
        		["-1"],
        		["21.99"]
        	]
        }
        ';
		TestCheck::assertInArray('C.2', 'Transform Job; conversion from double to character',  $actual, $expected, $results);



        // TEST: Transform Job; conversion to character type from integer type

		// BEGIN TEST
		$task = '
        [
            {
                "type": "flexio.create",
                "params": {
                	"columns": [
                		{"name":"f","type":"integer","width":2,"scale":0}
                	],
                	"rows": [
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
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"rows": [
        		["0"],
        		[null],
        		["1"],
        		["-1"],
        		["21"],
                ["-23"]
        	]
        }
        ';
		TestCheck::assertInArray('D.1', 'Transform Job; conversion from integer to character',  $actual, $expected, $results);



        // TEST: Transform Job; conversion to character type from date type

		// BEGIN TEST
		$task = '
        [
            {
                "type": "flexio.create",
                "params": {
                	"columns": [
                		{"name":"f","type":"date","width":8,"scale":0}
                	],
                	"rows": [
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
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"rows": [
        		["2001-02-03"],
        		[null],
        		["1970-01-01"],
        		["1999-12-31"]
        	]
        }
        ';
		TestCheck::assertInArray('E.1', 'Transform Job; conversion from date to character',  $actual, $expected, $results);



        // TEST: Transform Job; conversion to character type from datetime type

		// BEGIN TEST
		$task = '
        [
            {
                "type": "flexio.create",
                "params": {
                	"columns": [
                		{"name":"f","type":"datetime","width":8,"scale":0}
                	],
                	"rows": [
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
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"rows": [
        		["2001-02-03 00:00:00"],
        		[null],
        		["1970-01-01 01:02:03"],
        		["1999-12-31 23:59:59"]
        	]
        }
        ';
		TestCheck::assertInArray('F.1', 'Transform Job; conversion from datetime to character',  $actual, $expected, $results);



        // TEST: Transform Job; conversion to character type from boolean type

		// BEGIN TEST
		$task = '
        [
            {
                "type": "flexio.create",
                "params": {
                	"columns": [
                		{"name":"f","type":"boolean","width":1,"scale":0}
                	],
                	"rows": [
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
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"character"}
        	],
        	"rows": [
        		["true"],
        		[null],
        		["false"]
        	]
        }
        ';
		TestCheck::assertInArray('G.1', 'Transform Job; conversion from boolean to character',  $actual, $expected, $results);


        // TEST: Transform Job; conversion to boolean type from character type

		// BEGIN TEST
		$task = '
        [
            {
                "type": "flexio.create",
                "params": {
                	"columns": [
                		{"name":"f","type":"character","width":5,"scale":0}
                	],
                	"rows": [
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
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"boolean"}
        	],
        	"rows": [
                [null],
                [null],
                [false],
                [true],
                [null],
                [null],
                [true],
                [false],
                [true],
                [false],
                [true],
                [false],
                [true],
                [false]
        	]
        }
        ';
		TestCheck::assertInArray('H.1', 'Transform Job; conversion from character to boolean',  $actual, $expected, $results);



        // TEST: Transform Job; conversion to boolean type from numeric type

		// BEGIN TEST
		$task = '
        [
            {
                "type": "flexio.create",
                "params": {
                	"columns": [
                		{"name":"f","type":"numeric","width":8,"scale":0}
                	],
                	"rows": [
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
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"boolean"}
        	],
        	"rows": [
        		[null],
        		[false],
        		[true],
        		[true],
        		[true]
        	]
        }
        ';
		TestCheck::assertInArray('I.1', 'Transform Job; conversion from numeric to boolean',  $actual, $expected, $results);

		// BEGIN TEST
		$task = '
        [
            {
                "type": "flexio.create",
                "params": {
                	"columns": [
                		{"name":"f","type":"numeric","width":8,"scale":2}
                	],
                	"rows": [
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
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"boolean"}
        	],
        	"rows": [
        		[null],
        		[false],
        		[true],
        		[true],
        		[true]
        	]
        }
        ';
		TestCheck::assertInArray('I.2', 'Transform Job; conversion from numeric to boolean',  $actual, $expected, $results);



        // TEST: Transform Job; conversion to boolean type from double type

		// BEGIN TEST
		$task = '
        [
            {
                "type": "flexio.create",
                "params": {
                	"columns": [
                		{"name":"f","type":"double","width":4,"scale":2}
                	],
                	"rows": [
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
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"boolean"}
        	],
        	"rows": [
        		[null],
        		[false],
        		[true],
        		[true],
        		[true]
        	]
        }
        ';
		TestCheck::assertInArray('J.1', 'Transform Job; conversion from double to boolean',  $actual, $expected, $results);



        // TEST: Transform Job; conversion to boolean type from integer type

		// BEGIN TEST
		$task = '
        [
            {
                "type": "flexio.create",
                "params": {
                	"columns": [
                		{"name":"f","type":"integer","width":4,"scale":0}
                	],
                	"rows": [
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
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"boolean"}
        	],
        	"rows": [
        		[null],
        		[false],
        		[true],
        		[true],
        		[true]
        	]
        }
        ';
		TestCheck::assertInArray('K.1', 'Transform Job; conversion from integer to boolean',  $actual, $expected, $results);



        // TEST: Transform Job; conversion to boolean type from date type

		// BEGIN TEST
		$task = '
        [
            {
                "type": "flexio.create",
                "params": {
                	"columns": [
                		{"name":"f","type":"date","width":4,"scale":0}
                	],
                	"rows": [
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
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"boolean"}
        	],
        	"rows": [
        		[false],
        		[true],
        		[true]
        	]
        }
        ';
		TestCheck::assertInArray('L.1', 'Transform Job; conversion from date to boolean.  Non-null values are true, null-values false',  $actual, $expected, $results);



        // TEST: Transform Job; conversion to boolean type from datetime type

		// BEGIN TEST
		$task = '
        [
            {
                "type": "flexio.create",
                "params": {
                	"columns": [
                		{"name":"f","type":"datetime","width":8,"scale":0}
                	],
                	"rows": [
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
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"boolean"}
        	],
        	"rows": [
        		[false],
        		[true],
        		[true]
        	]
        }
        ';
		TestCheck::assertInArray('M.1', 'Transform Job; conversion from datetime to boolean.  Non-null values are true, null-values false',  $actual, $expected, $results);



        // TEST: Transform Job; conversion to boolean type from boolean type (identity)

		// BEGIN TEST
		$task = '
        [
            {
                "type": "flexio.create",
                "params": {
                	"columns": [
                		{"name":"f","type":"boolean","width":1,"scale":0}
                	],
                	"rows": [
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
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
		$expected = '
        {
        	"columns": [
        		{"name":"f","type":"boolean"}
        	],
        	"rows": [
        		[null],
        		[true],
        		[false]
        	]
        }
        ';
		TestCheck::assertInArray('N.1', 'Transform Job; conversion from boolean to boolean (identity)',  $actual, $expected, $results);
    }
}
