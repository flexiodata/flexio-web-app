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


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {

/*

// TODO: debug following tests and re-add (they are causing the tests to fail in a way
// that does't display the results of the other tests

        // TEST: Structure Conversion; Character to Boolean Type; Invalid Conversions

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        [null],
                        ["a"],
                        ["-"],
                        ["+"],
                        ["/"],
                        ["."],
                        [","],
                        ["0a"],
                        ["1b"],
                        ["Ta"],
                        ["Fb"],
                        ["truea"],
                        ["falseb"],
                        ["null"]
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
                [null],
                [null]
            ]
        }
        ';
        TestCheck::assertInArray('K.1', 'Conversion from Character to Boolean',  $actual, $expected, $results);



        // TEST: Structure Conversion; Character to Boolean Type; valid conversions

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        [""],
                        ["X"],
                        ["1"],
                        ["0"],
                        ["true"],
                        ["false"],
                        ["TRUE"],
                        ["FALSE"],
                        ["T"],
                        ["F"],
                        ["t"],
                        ["f"]
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
                [false],
                [true],
                [true],
                [false],
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
        TestCheck::assertInArray('K.2', 'Conversion from Character to Boolean',  $actual, $expected, $results);
*/


        // TEST: Structure Conversion; Character to Integer Type Width and Scale Bound Checks

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
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
        TestCheck::assertString('A.1', 'Conversion from Character to Integer; fail if width or scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
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
        TestCheck::assertString('A.2', 'Conversion from Character to Integer; fail if width or scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
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
        TestCheck::assertString('A.3', 'Conversion from Character to Integer; fail if width or scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
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
        TestCheck::assertString('A.4', 'Conversion from Character to Integer; fail if width or scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
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
        TestCheck::assertString('A.5', 'Conversion from Character to Integer; fail if width or scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
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
        TestCheck::assertString('A.6', 'Conversion from Character to Integer; fail if width or scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
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
        TestCheck::assertString('A.7', 'Conversion from Character to Integer; fail if width or scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
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
        TestCheck::assertString('A.8', 'Conversion from Character to Integer; fail if width or scale is specified',  $actual, $expected, $results);



        // TEST: Structure Conversion; Character to Numeric Type Decimal Handling

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
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
                { "name": "field1", "type": "integer", "width": 8}
            ],
            "rows": [
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
        TestCheck::assertInArray('B.1', 'Conversion from Character to Integer; round to nearest integer (similarly to how setting numeric scale to zero rounds to nearest integer)',  $actual, $expected, $results);



        // TEST: Structure Conversion; Character to Integer; Different Types of Numeric Representation

        // BEGIN TEST
        $task = '
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
                { "name": "field1", "type": "integer", "width": 8}
            ],
            "rows": [
                [123],
                [123],
                [-123]
            ]
        }
        ';
        TestCheck::assertInArray('C.1', 'Conversion from Character to Integer; make sure numbers with digit separators convert properly',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["123,456"],
                        ["-123,456"]
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
                [123456],
                [-123456]
            ]
        }
        ';
        TestCheck::assertInArray('C.2', 'Conversion from Character to Integer; make sure numbers with digit separators convert properly',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
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
                [123000],
                [12300],
                [1230],
                [123]
            ]
        }
        ';
        TestCheck::assertInArray('C.3', 'Conversion from Character to Integer; make sure leader zeros are removed',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        [null],
                        ["a"],
                        ["a b"],
                        [""],
                        [" "],
                        ["-"],
                        ["+"],
                        ["."],
                        ["-"],
                        ["$"],
                        ["%"]
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
        }';
        TestCheck::assertInArray('C.4', 'Conversion from Character to Numeric; handle spaces appropriately',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
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
                [123],
                [123],
                [123],
                [-123]
            ]
        }
        ';
        TestCheck::assertInArray('C.5', 'Conversion from Character to Integer; handle spaces appropriately',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["$1"],
                        ["-$1"],
                        ["$10"],
                        ["$99"],
                        ["1%"],
                        ["#10"],
                        ["10#"]
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
                [1],
                [-1],
                [10],
                [99],
                [1],
                [10],
                [10]
            ]
        }
        ';
        TestCheck::assertInArray('C.6', 'Conversion from Character to Integer; handle a few symbol types',  $actual, $expected, $results);



        // TEST: Structure Conversion; Character to Date Type Width and Scale Bound Checks

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["01/01/2001"]
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
        TestCheck::assertString('D.1', 'Conversion from Character to Date; fail if width is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["01/01/2001"]
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
        TestCheck::assertString('D.2', 'Conversion from Character to Date; fail if scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["01/01/2001"]
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
        TestCheck::assertString('D.3', 'Conversion from Character to Date; fail if scale is specified',  $actual, $expected, $results);



        // TEST: Structure Conversion; Character to Date Type

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        [null],
                        [""],
                        ["0"],
                        ["1"],
                        ["a"],
                        ["-"],
                        ["/"],
                        ["."],
                        [","],
                        ["true"],
                        ["false"]
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
                [null],
                [null],
                [null],
                [null],
                [null],
                [null]
            ]
        }
        ';
        TestCheck::assertInArray('E.1', 'Conversion from Character to Date',  $actual, $expected, $results);



        // TEST: Structure Conversion; Character to Date Type

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["12/31/99"],
                        ["01/01/00"],
                        ["02/15/2015"],
                        ["03/15/05"],
                        ["03/16/05 00:00:00"],
                        ["10/15/05 23:23:59"]
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
                ["1999-12-31"],
                ["2000-01-01"],
                ["2015-02-15"],
                ["2005-03-15"],
                ["2005-03-16"],
                ["2005-10-15"]
            ]
        }
        ';
        TestCheck::assertInArray('F.1', 'Conversion from Character to Date',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["12/31/99"],
                        ["1/1/00"],
                        ["2/15/2015"],
                        ["3/15/05"],
                        ["3/16/05 00:00:00"],
                        ["10/15/05 23:23:59"]
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
                ["1999-12-31"],
                ["2000-01-01"],
                ["2015-02-15"],
                ["2005-03-15"],
                ["2005-03-16"],
                ["2005-10-15"]
            ]
        }
        ';
        TestCheck::assertInArray('F.2', 'Conversion from Character to Date',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["12-31-99"],
                        ["01-01-00"],
                        ["02-15-2015"],
                        ["03-15-05"],
                        ["03-16-05 00:00:00"],
                        ["10-15-05 23:23:59"]
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
                ["1999-12-31"],
                ["2000-01-01"],
                ["2015-02-15"],
                ["2005-03-15"],
                ["2005-03-16"],
                ["2005-10-15"]
            ]
        }
        ';
        TestCheck::assertInArray('F.3', 'Conversion from Character to Date',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["12-31-99"],
                        ["1-1-00"],
                        ["2-15-2015"],
                        ["3-15-05"],
                        ["3-16/05 00:00:00"],
                        ["10-15-05 23:23:59"]
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
                ["1999-12-31"],
                ["2000-01-01"],
                ["2015-02-15"],
                ["2005-03-15"],
                ["2005-03-16"],
                ["2005-10-15"]
            ]
        }
        ';
        TestCheck::assertInArray('F.4', 'Conversion from Character to Date',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["12.31.99"],
                        ["01.01.00"],
                        ["02.15.2015"],
                        ["03.15.05"],
                        ["03.16.05 00:00:00"],
                        ["10.15.05 23:23:59"]
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
                ["1999-12-31"],
                ["2000-01-01"],
                ["2015-02-15"],
                ["2005-03-15"],
                ["2005-03-16"],
                ["2005-10-15"]
            ]
        }
        ';
        TestCheck::assertInArray('F.5', 'Conversion from Character to Date',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["12 31 99"],
                        ["01 01 00"],
                        ["02 15 2015"],
                        ["03 15 05"],
                        ["03 16 05 00:00:00"],
                        ["10 15 05 23:23:59"]
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
                ["1999-12-31"],
                ["2000-01-01"],
                ["2015-02-15"],
                ["2005-03-15"],
                ["2005-03-16"],
                ["2005-10-15"]
            ]
        }
        ';
        TestCheck::assertInArray('F.6', 'Conversion from Character to Date',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["1999-12-31"],
                        ["2000-01-01"],
                        ["2015-02-15"],
                        ["2005-03-15"],
                        ["2005-03-16 00:00:00"],
                        ["2005-10-15T23:23:59"]
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
                ["1999-12-31"],
                ["2000-01-01"],
                ["2015-02-15"],
                ["2005-03-15"],
                ["2005-03-16"],
                ["2005-10-15"]
            ]
        }
        ';
        TestCheck::assertInArray('F.7', 'Conversion from Character to Date',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["19991231"],
                        ["20000101"],
                        ["20150215"],
                        ["20050315"],
                        ["20050316 00:00:00"],
                        ["20051015T23:23:59"]
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
                ["1999-12-31"],
                ["2000-01-01"],
                ["2015-02-15"],
                ["2005-03-15"],
                ["2005-03-16"],
                ["2005-10-15"]
            ]
        }
        ';
        TestCheck::assertInArray('F.8', 'Conversion from Character to Date',  $actual, $expected, $results);



        // TEST: Structure Conversion; Character to DateTime Type Width and Scale Bound Checks

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["01/01/2001"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "datetime", "width": 4 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('G.1', 'Conversion from Character to DateTime; fail if width or scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["01/01/2001"]
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
        TestCheck::assertString('G.2', 'Conversion from Character to DateTime; fail if width or scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["01/01/2001"]
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
        TestCheck::assertString('G.3', 'Conversion from Character to DateTime; fail if width or scale is specified',  $actual, $expected, $results);



        // TEST: Structure Conversion; Character to DateTime Type

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        [null],
                        [""],
                        ["1"],
                        ["a"],
                        ["-"],
                        ["/"],
                        ["."],
                        [","],
                        ["true"],
                        ["false"]
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
                [null],
                [null],
                [null],
                [null],
                [null]
            ]
        }
        ';
        TestCheck::assertInArray('H.1', 'Conversion from Character to DateTime',  $actual, $expected, $results);



        // TEST: Structure Conversion; Character to DateTime Type

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["12/31/99"],
                        ["01/01/00"],
                        ["02/15/2015"],
                        ["03/15/05"],
                        ["03/16/05 00:00:00"],
                        ["10/15/05 23:23:59"]
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
                ["1999-12-31 00:00:00"],
                ["2000-01-01 00:00:00"],
                ["2015-02-15 00:00:00"],
                ["2005-03-15 00:00:00"],
                ["2005-03-16 00:00:00"],
                ["2005-10-15 23:23:59"]
            ]
        }
        ';
        TestCheck::assertInArray('I.1', 'Conversion from Character to DateTime',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["12/31/99"],
                        ["1/1/00"],
                        ["2/15/2015"],
                        ["3/15/05"],
                        ["3/16/05 00:00:00"],
                        ["10/15/05 23:23:59"]
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
                ["1999-12-31 00:00:00"],
                ["2000-01-01 00:00:00"],
                ["2015-02-15 00:00:00"],
                ["2005-03-15 00:00:00"],
                ["2005-03-16 00:00:00"],
                ["2005-10-15 23:23:59"]
            ]
        }
        ';
        TestCheck::assertInArray('I.2', 'Conversion from Character to DateTime',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["12-31-99"],
                        ["01-01-00"],
                        ["02-15-2015"],
                        ["03-15-05"],
                        ["03-16-05 00:00:00"],
                        ["10-15-05 23:23:59"]
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
                ["1999-12-31 00:00:00"],
                ["2000-01-01 00:00:00"],
                ["2015-02-15 00:00:00"],
                ["2005-03-15 00:00:00"],
                ["2005-03-16 00:00:00"],
                ["2005-10-15 23:23:59"]
            ]
        }
        ';
        TestCheck::assertInArray('I.3', 'Conversion from Character to DateTime',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["12-31-99"],
                        ["1-1-00"],
                        ["2-15-2015"],
                        ["3-15-05"],
                        ["3-16/05 00:00:00"],
                        ["10-15-05 23:23:59"]
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
                ["1999-12-31 00:00:00"],
                ["2000-01-01 00:00:00"],
                ["2015-02-15 00:00:00"],
                ["2005-03-15 00:00:00"],
                ["2005-03-16 00:00:00"],
                ["2005-10-15 23:23:59"]
            ]
        }
        ';
        TestCheck::assertInArray('I.4', 'Conversion from Character to DateTime',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["12.31.99"],
                        ["01.01.00"],
                        ["02.15.2015"],
                        ["03.15.05"],
                        ["03.16.05 00:00:00"],
                        ["10.15.05 23:23:59"]
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
                ["1999-12-31 00:00:00"],
                ["2000-01-01 00:00:00"],
                ["2015-02-15 00:00:00"],
                ["2005-03-15 00:00:00"],
                ["2005-03-16 00:00:00"],
                ["2005-10-15 23:23:59"]
            ]
        }
        ';
        TestCheck::assertInArray('I.5', 'Conversion from Character to DateTime',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["12 31 99"],
                        ["01 01 00"],
                        ["02 15 2015"],
                        ["03 15 05"],
                        ["03 16 05 00:00:00"],
                        ["10 15 05 23:23:59"]
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
                ["1999-12-31 00:00:00"],
                ["2000-01-01 00:00:00"],
                ["2015-02-15 00:00:00"],
                ["2005-03-15 00:00:00"],
                ["2005-03-16 00:00:00"],
                ["2005-10-15 23:23:59"]
            ]
        }
        ';
        TestCheck::assertInArray('I.6', 'Conversion from Character to DateTime',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["1999-12-31"],
                        ["2000-01-01"],
                        ["2015-02-15"],
                        ["2005-03-15"],
                        ["2005-03-16 00:00:00"],
                        ["2005-10-15T23:23:59"]
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
                ["1999-12-31 00:00:00"],
                ["2000-01-01 00:00:00"],
                ["2015-02-15 00:00:00"],
                ["2005-03-15 00:00:00"],
                ["2005-03-16 00:00:00"],
                ["2005-10-15 23:23:59"]
            ]
        }
        ';
        TestCheck::assertInArray('I.7', 'Conversion from Character to DateTime',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["19991231"],
                        ["20000101"],
                        ["20150215"],
                        ["20050315"],
                        ["20050316 00:00:00"],
                        ["20051015T23:23:59"]
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
                ["1999-12-31 00:00:00"],
                ["2000-01-01 00:00:00"],
                ["2015-02-15 00:00:00"],
                ["2005-03-15 00:00:00"],
                ["2005-03-16 00:00:00"],
                ["2005-10-15 23:23:59"]
            ]
        }
        ';
        TestCheck::assertInArray('I.8', 'Conversion from Character to DateTime',  $actual, $expected, $results);



        // TEST: Structure Conversion; Character to Boolean Type Width and Scale Bound Checks

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["true"],
                        ["false"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "boolean", "width": 1 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('J.1', 'Conversion from Character to Boolean; fail if width or scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["true"],
                        ["false"]
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
        TestCheck::assertString('J.2', 'Conversion from Character to Boolean; fail if width or scale is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 20, "scale": 0 }
                    ],
                    "rows": [
                        ["true"],
                        ["false"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "type" : "boolean", "width": 1, "scale": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('J.3', 'Conversion from Character to Boolean; fail if width or scale is specified',  $actual, $expected, $results);
    }
}
