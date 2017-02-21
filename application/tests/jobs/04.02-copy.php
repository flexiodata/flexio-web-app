<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-08-24
 *
 * @package flexio
 * @subpackage Tests
 */


class Test
{
    public function run(&$results)
    {
        // TEST: Structure Conversion; Character to Numeric Type Width and Scale Bound Checks

        // BEGIN TEST
        $task ='
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 10, "scale": 0 }
                    ],
                    "rows": [
                        ["123"],
                        ["+123"],
                        ["-123"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "numeric"} } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "numeric", "width": 10, "scale": 0 }
            ],
            "rows": [
                ["123"],
                ["123"],
                ["-123"]
            ]
        }
        ';
        TestCheck::assertInArray('A.1', 'Conversion from Character to Numeric; when converting character to numeric, keep width and set the default scale to zero when only changing the type',  $actual, $expected, $results);

        // BEGIN TEST
        $task ='
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 10, "scale": 0 }
                    ],
                    "rows": [
                        ["123"],
                        ["+123"],
                        ["-123"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "numeric", "width": 5 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "numeric", "width": 5, "scale": 0 }
            ],
            "rows": [
                ["123"],
                ["123"],
                ["-123"]
            ]
        }
        ';
        TestCheck::assertInArray('A.2', 'Conversion from Character to Numeric; when converting character to numeric, set default scale to zero if it\'s not specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task ='
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 10, "scale": 0 }
                    ],
                    "rows": [
                        ["123"],
                        ["+123"],
                        ["-123"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "numeric", "scale": 2 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "numeric", "width": 10, "scale": 2 }
            ],
            "rows": [
                ["123.00"],
                ["123.00"],
                ["-123.00"]
            ]
        }
        ';
        TestCheck::assertInArray('A.3', 'Conversion from Character to Numeric; when converting character to numeric, keep width when only setting the scale',  $actual, $expected, $results);



        // TEST: Structure Conversion; Character to Numeric Type Value Conversion

        // BEGIN TEST
        $task ='
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["123"],
                        ["+123"],
                        ["-123"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "numeric", "width": 0, "scale": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.1', 'Conversion from Character to Numeric; fail if width is set to a value out-of-bounds',  $actual, $expected, $results);

        // BEGIN TEST
        $task ='
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["123"],
                        ["+123"],
                        ["-123"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "numeric", "width": 10000, "scale": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.2', 'Conversion from Character to Numeric; fail if width is set to a value out-of-bounds',  $actual, $expected, $results);

        // BEGIN TEST
        $task ='
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["123"],
                        ["+123"],
                        ["-123"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "numeric", "width": 0, "scale": -1 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.3', 'Conversion from Character to Numeric; fail if scale is set to a value out-of-bounds',  $actual, $expected, $results);

        // BEGIN TEST
        $task ='
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["123"],
                        ["+123"],
                        ["-123"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "numeric", "width": 10, "scale": 1000 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.4', 'Conversion from Character to Numeric; fail if scale is set to a value out-of-bounds',  $actual, $expected, $results);

        // BEGIN TEST
        $task ='
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["123"],
                        ["+123"],
                        ["-123"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "numeric", "width": 12, "scale": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "numeric", "width": 12, "scale": 0 }
            ],
            "rows": [
                ["123"],
                ["123"],
                ["-123"]
            ]
        }
        ';
        TestCheck::assertInArray('B.5', 'Conversion from Character to Numeric; make sure positive and negative integers convert properly',  $actual, $expected, $results);

        // BEGIN TEST
        $task ='
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["a"],
                        ["1a2"],
                        ["9"],
                        ["99"],
                        ["999"],
                        ["9999"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "numeric", "width": 3, "scale": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "numeric", "width": 3, "scale": 0 }
            ],
            "rows": [
                [null],
                [null],
                ["9"],
                ["99"],
                ["999"],
                [null]
            ]
        }
        ';
        TestCheck::assertInArray('B.6', 'Conversion from Character to Numeric; convert out-of-bounds and bad values to null',  $actual, $expected, $results);

        // BEGIN TEST
        $task ='
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["0"],
                        ["1"],
                        ["1."],
                        [".1"],
                        ["+.1"],
                        ["-.1"],
                        ["0.1"],
                        ["+0.1"],
                        ["-0.1"],
                        ["0.4"],
                        ["-0.4"],
                        ["0.5"],
                        ["-0.5"],
                        ["0.9"],
                        ["-0.9"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "numeric", "width": 12, "scale": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "numeric", "width": 12, "scale": 0 }
            ],
            "rows": [
                ["0"],
                ["1"],
                ["1"],
                ["0"],
                ["0"],
                ["0"],
                ["0"],
                ["0"],
                ["0"],
                ["0"],
                ["0"],
                ["1"],
                ["-1"],
                ["1"],
                ["-1"]
            ]
        }
        ';
        TestCheck::assertInArray('B.7', 'Conversion from Character to Numeric; make sure positive and negative decimal values convert properly',  $actual, $expected, $results);

        // BEGIN TEST
        $task ='
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["123"],
                        ["123."],
                        ["123.45"],
                        ["-123.45"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "numeric", "width": 12, "scale": 2 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "numeric", "width": 12, "scale": 2 }
            ],
            "rows": [
                ["123.00"],
                ["123.00"],
                ["123.45"],
                ["-123.45"]
            ]
        }
        ';
        TestCheck::assertInArray('B.8', 'Conversion from Character to Numeric; make sure mix of positive and negative numbers convert properly',  $actual, $expected, $results);

        // BEGIN TEST
        $task ='
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["123,456.78"],
                        ["+123,456.78"],
                        ["-123,456.78"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "numeric", "width": 12, "scale": 2 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "numeric", "width": 12, "scale": 2 }
            ],
            "rows": [
                ["123456.78"],
                ["123456.78"],
                ["-123456.78"]
            ]
        }
        ';
        TestCheck::assertInArray('B.9', 'Conversion from Character to Numeric; make sure numbers with digit separators convert properly',  $actual, $expected, $results);

        // BEGIN TEST
        $task ='
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["123000"],
                        ["012300"],
                        ["001230"],
                        ["000123"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "numeric", "width": 12, "scale": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "numeric", "width": 12, "scale": 0 }
            ],
            "rows": [
                ["123000"],
                ["12300"],
                ["1230"],
                ["123"]
            ]
        }
        ';
        TestCheck::assertInArray('B.10', 'Conversion from Character to Numeric; make sure leader zeros are removed',  $actual, $expected, $results);

        // BEGIN TEST
        $task ='
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["null"],
                        ["a"],
                        ["a b"],
                        [""],
                        [" "],
                        ["-"],
                        ["+"],
                        ["."],
                        [","],
                        ["-"],
                        ["$"],
                        ["%"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "numeric", "width": 12, "scale": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "numeric", "width": 12, "scale": 0 }
            ],
            "rows": [
                [null],
                [null],
                [null],
                [null],
                [null],
                [null],
                [null],
                [null],
                [null],
                [null],
                [null],
                [null]
            ]
        }
        ';
        TestCheck::assertInArray('B.11', 'Conversion from Character to Numeric; handle spaces appropriately',  $actual, $expected, $results);

        // BEGIN TEST
        $task ='
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["123   "],
                        ["   123"],
                        ["1 2 3"],
                        [" - 123"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "numeric", "width": 12, "scale": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "numeric", "width": 12, "scale": 0 }
            ],
            "rows": [
                ["123"],
                ["123"],
                ["123"],
                ["-123"]
            ]
        }
        ';
        TestCheck::assertInArray('B.12', 'Conversion from Character to Numeric; handle spaces appropriately',  $actual, $expected, $results);

        // BEGIN TEST
        $task ='
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["$0.01"],
                        ["-$0.01"],
                        ["$99.99"],
                        ["1%"],
                        ["0.01%"],
                        [".01%"],
                        ["#10"],
                        ["10#"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "numeric", "width": 12, "scale": 2 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "numeric", "width": 12, "scale": 2 }
            ],
            "rows": [
                ["0.01"],
                ["-0.01"],
                ["99.99"],
                ["1.00"],
                ["0.01"],
                ["0.01"],
                ["10.00"],
                ["10.00"]
            ]
        }
        ';
        TestCheck::assertInArray('B.13', 'Conversion from Character to Numeric; handle a few symbol types',  $actual, $expected, $results);
    }
}
