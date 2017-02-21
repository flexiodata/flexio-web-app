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
        // TEST: Structure Conversion; Numeric to Character Width and Scale Bound Checks

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 10, "scale": 2 }
                    ],
                    "rows": [
                        ["123.00"],
                        ["+123.00"],
                        ["1123.00"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "character", "width": 4 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_COMPLETED;
        TestCheck::assertString('A.1', 'Conversion from Numeric to Character; allow width change; fail if scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 10, "scale": 2 }
                    ],
                    "rows": [
                        ["123.00"],
                        ["+123.00"],
                        ["1123.00"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "character", "scale": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('A.2', 'Conversion from Numeric to Character; allow width change; fail if scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 10, "scale": 2 }
                    ],
                    "rows": [
                        ["123.00"],
                        ["+123.00"],
                        ["1123.00"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "character", "width": 4, "scale": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('A.3', 'Conversion from Numeric to Character; allow width change; fail if scale is specified',  $actual, $expected, $results);



        // TEST: Structure Conversion; Numeric to Character Type Width and Scale Bound Checks

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 10, "scale": 4 }
                    ],
                    "rows": [
                        [null],
                        ["123.0001"],
                        ["+123.9999"],
                        ["-123.9999"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "character"} } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "character", "width": 10 }
            ],
            "rows": [
                [null],
                ["123.0001"],
                ["123.9999"],
                ["-123.9999"]
            ]
        }
        ';
        TestCheck::assertInArray('B.1', 'Conversion from Numeric to Character; when converting numeric to character, preserve decimal places and negative sign',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 10, "scale": 4 }
                    ],
                    "rows": [
                        [null],
                        ["123.0001"],
                        ["+123.9999"],
                        ["-123.9999"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "character", "width": 4} } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "character", "width": 4 }
            ],
            "rows": [
                [null],
                ["123."],
                ["123."],
                ["-123"]
            ]
        }
        ';
        TestCheck::assertInArray('B.2', 'Conversion from Numeric to Character; when converting numeric to character, truncate values outside width',  $actual, $expected, $results);



        // TEST: Structure Conversion; Numeric to Integer Type Width and Scale Bound Checks

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 10, "scale": 0 }
                    ],
                    "rows": [
                        ["1"],
                        ["-1"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "integer", "width": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('C.1', 'Conversion from Numeric to Integer; fail if width or scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 10, "scale": 0 }
                    ],
                    "rows": [
                        ["1"],
                        ["-1"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "integer", "width": 4 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('C.2', 'Conversion from Numeric to Integer; fail if width or scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 10, "scale": 0 }
                    ],
                    "rows": [
                        ["1"],
                        ["-1"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "integer", "width": 8 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('C.3', 'Conversion from Numeric to Integer; fail if width or scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 10, "scale": 0 }
                    ],
                    "rows": [
                        ["1"],
                        ["-1"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "integer", "width": 10 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('C.4', 'Conversion from Numeric to Integer; fail if width or scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 10, "scale": 0 }
                    ],
                    "rows": [
                        ["1"],
                        ["-1"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "integer", "scale": -1 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('C.5', 'Conversion from Numeric to Integer; fail if width or scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 10, "scale": 0 }
                    ],
                    "rows": [
                        ["1"],
                        ["-1"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "integer", "scale": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('C.6', 'Conversion from Numeric to Integer; fail if width or scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 10, "scale": 0 }
                    ],
                    "rows": [
                        ["1"],
                        ["-1"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "integer", "scale": 1 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('C.7', 'Conversion from Numeric to Integer; fail if width or scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 10, "scale": 0 }
                    ],
                    "rows": [
                        ["1"],
                        ["-1"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "integer", "width": 4, "scale": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('C.8', 'Conversion from Numeric to Integer; fail if width or scale is specified',  $actual, $expected, $results);



        // TEST: Structure Conversion; Numeric to Integer Decimal Handling

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 10, "scale": 2 }
                    ],
                    "rows": [
                        ["0"],
                        ["-1"],
                        ["1"],
                        ["0.4"],
                        ["0.5"],
                        ["1."],
                        ["1.4"],
                        ["1.5"],
                        ["1.6"],
                        ["-0.4"],
                        ["-0.6"],
                        ["-1."],
                        ["-1.4"],
                        ["-1.5"],
                        ["-1.6"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "integer" } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "integer", "width": 8 }
            ],
            "rows": [
                [0],
                [-1],
                [1],
                [0],
                [1],
                [1],
                [1],
                [2],
                [2],
                [0],
                [-1],
                [-1],
                [-1],
                [-2],
                [-2]
            ]
        }
        ';
        TestCheck::assertInArray('D.1', 'Conversion from Numeric to Integer; round to nearest integer (similarly to how setting numeric scale to zero rounds to nearest integer)',  $actual, $expected, $results);



        // TEST: Structure Conversion; Numeric to Date Type Width and Scale Bound Checks

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 10, "scale": 0 }
                    ],
                    "rows": [
                        ["20010101"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "date", "width": 4 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('E.1', 'Conversion from Numeric to Date; fail if width or scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 10, "scale": 0 }
                    ],
                    "rows": [
                        ["20010101"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "date", "scale": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('E.2', 'Conversion from Numeric to Date; fail if width or scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 10, "scale": 0 }
                    ],
                    "rows": [
                        ["20010101"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "date", "width": 4, "scale": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('E.3', 'Conversion from Numeric to Date; fail if width or scale is specified',  $actual, $expected, $results);



        // TEST: Structure Conversion; Numeric to Date Type

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 10, "scale": 0 }
                    ],
                    "rows": [
                        [null],
                        ["0"],
                        ["1"],
                        ["-1"],
                        ["-19991231"],
                        ["19991231"],
                        ["20000101"],
                        ["20010101"],
                        ["20000000"],
                        ["20011200"],
                        ["20010001"],
                        ["20011301"],
                        ["20010132"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "date" } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "date", "width": 8 }
            ],
            "rows": [
                [null],
                [null],
                [null],
                [null],
                [null],
                ["1999-12-31"],
                ["2000-01-01"],
                ["2001-01-01"],
                ["2000-01-01"],
                ["2001-12-01"],
                ["2001-01-01"],
                ["2002-01-01"],
                ["2001-02-01"]
            ]
        }
        ';
        TestCheck::assertInArray('F.1', 'Conversion from Numeric to Date; convert valid YYYYMMDD numbers to date; if value isn\'t a valid date, convert to null',  $actual, $expected, $results);



        // TEST: Structure Conversion; Numeric to DateTime Type Width and Scale Bound Checks

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 10, "scale": 0 }
                    ],
                    "rows": [
                        ["20010101"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "datetime", "width": 8 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('G.1', 'Conversion from Numeric to DateTime; fail if width or scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 10, "scale": 0 }
                    ],
                    "rows": [
                        ["20010101"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "datetime", "scale": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('G.2', 'Conversion from Numeric to DateTime; fail if width or scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 10, "scale": 0 }
                    ],
                    "rows": [
                        ["20010101"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "datetime", "width": 4, "scale": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('G.3', 'Conversion from Numeric to DateTime; fail if width or scale is specified',  $actual, $expected, $results);



        // TEST: Structure Conversion; Numeric to DateTime Type

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 10, "scale": 0 }
                    ],
                    "rows": [
                        [null],
                        ["0"],
                        ["1"],
                        ["-1"],
                        ["-19991231"],
                        ["19991231"],
                        ["20000101"],
                        ["20010101"],
                        ["20000000"],
                        ["20011200"],
                        ["20010001"],
                        ["20011301"],
                        ["20010132"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "datetime" } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "datetime", "width": 8 }
            ],
            "rows": [
                [null],
                [null],
                [null],
                [null],
                [null],
                ["1999-12-31 00:00:00"],
                ["2000-01-01 00:00:00"],
                ["2001-01-01 00:00:00"],
                ["2000-01-01 00:00:00"],
                ["2001-12-01 00:00:00"],
                ["2001-01-01 00:00:00"],
                ["2002-01-01 00:00:00"],
                ["2001-02-01 00:00:00"]
            ]
        }
        ';
        TestCheck::assertInArray('H.1', 'Conversion from Numeric to DateTime; convert valid YYYYMMDD numbers to date; if value isn\'t a valid date, convert to null',  $actual, $expected, $results);



        // TEST: Structure Conversion; Numeric to Boolean Type Width and Scale Bound Checks

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 10, "scale": 0 }
                    ],
                    "rows": [
                        ["1234"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "boolean", "width": 8 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('I.1', 'Conversion from Numeric to Boolean; fail if width or scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 10, "scale": 0 }
                    ],
                    "rows": [
                        ["20010101"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "boolean", "scale": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('I.2', 'Conversion from Numeric to Boolean; fail if width or scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 10, "scale": 0 }
                    ],
                    "rows": [
                        ["20010101"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "boolean", "width": 4, "scale": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('I.3', 'Conversion from Numeric to Boolean; fail if width or scale is specified',  $actual, $expected, $results);



        // TEST: Structure Conversion; Numeric to Boolean Type

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 10, "scale": 0 }
                    ],
                    "rows": [
                        [null],
                        ["0"],
                        ["1"],
                        ["-1"],
                        ["1.0"],
                        ["1.1"],
                        ["2"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "boolean" } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "boolean", "width": 1 }
            ],
            "rows": [
                [null],
                [false],
                [true],
                [true],
                [true],
                [true],
                [true]
            ]
        }
        ';
        TestCheck::assertInArray('J.1', 'Conversion from Numeric to Boolean; convert 0 to false, 1 to true, and everything else to null',  $actual, $expected, $results);



        // TEST: Structure Conversion; Date to Character Type Width and Scale Bound Checks

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "date" }
                    ],
                    "rows": [
                        ["2000-01-01"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "character", "width": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('K.1', 'Conversion from Date to Character; allow width change; fail if width is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "date" }
                    ],
                    "rows": [
                        ["2000-01-01"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "character", "width": 4 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_COMPLETED;
        TestCheck::assertString('K.2', 'Conversion from Date to Character; allow width change; fail if scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "date" }
                    ],
                    "rows": [
                        ["2000-01-01"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "character", "scale": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('K.3', 'Conversion from Date to Character; allow width change; fail if scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "date" }
                    ],
                    "rows": [
                        ["2000-01-01"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "character", "width": 4, "scale": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('K.4', 'Conversion from Date to Character; allow width change; fail if scale is specified',  $actual, $expected, $results);



        // TEST: Structure Conversion; Date to Character Type

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "date" }
                    ],
                    "rows": [
                        [null],
                        ["1999-12-31"],
                        ["2000-01-01"],
                        ["2001-02-03"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "character" } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "character", "width": 10 }
            ],
            "rows": [
                [null],
                ["1999-12-31"],
                ["2000-01-01"],
                ["2001-02-03"]
            ]
        }
        ';
        TestCheck::assertInArray('L.1', 'Conversion from Date to Character',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "date" }
                    ],
                    "rows": [
                        [null],
                        ["1999-12-31"],
                        ["2000-01-01"],
                        ["2001-02-03"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "character", "width": 7 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "character", "width": 7 }
            ],
            "rows": [
                [null],
                ["1999-12"],
                ["2000-01"],
                ["2001-02"]
            ]
        }
        ';
        TestCheck::assertInArray('L.2', 'Conversion from Date to Character; truncated to width of 7',  $actual, $expected, $results);



        // TEST: Structure Conversion; Date to DateTime Type Width and Scale Bound Checks

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "date" }
                    ],
                    "rows": [
                        ["2001-01-01"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "datetime", "width": 8 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('M.1', 'Conversion from Date to DateTime; fail if width or scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "date" }
                    ],
                    "rows": [
                        ["2001-01-01"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "datetime", "scale": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('M.2', 'Conversion from Date to DateTime; fail if width or scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "date" }
                    ],
                    "rows": [
                        ["2001-01-01"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "boolean", "width": 8, "scale": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('M.3', 'Conversion from Date to DateTime; fail if width or scale is specified',  $actual, $expected, $results);



        // TEST: Structure Conversion; Date to DateTime Type

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "date" }
                    ],
                    "rows": [
                        [null],
                        ["1999-12-31"],
                        ["2000-01-01"],
                        ["2001-02-03"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "datetime" } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "datetime", "width": 8 }
            ],
            "rows": [
                [null],
                ["1999-12-31 00:00:00"],
                ["2000-01-01 00:00:00"],
                ["2001-02-03 00:00:00"]
            ]
        }
        ';
        TestCheck::assertInArray('N.1', 'Conversion from Date to DateTime; convert valid dates; handle null',  $actual, $expected, $results);
    }
}
