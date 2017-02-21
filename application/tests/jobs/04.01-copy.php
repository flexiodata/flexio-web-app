<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-08-27
 *
 * @package flexio
 * @subpackage Tests
 */


class Test
{
    public function run(&$results)
    {
        // TODO: still need to check:
        // 1. display name changes
        // 2. description additions
        // 3. tag additions
        // 4. expressions
        // 5. selecting subset of columns with different orders

        // TODO: add tests for the following:
        // 1. converstion from character to double type
        // 2. converstion from character to float type
        // 3. converstion from numeric to double type
        // 4. converstion from numeric to float type
        // 5. converstion from double, float, and integer to other types
        // 6. converstion from date to numeric type
        // 7. converstion from date to float and double type
        // 8. converstion from date to integer type

/*
        // SETUP
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 3, "scale": 0 }
                    ],
                    "rows": [
                        ["x"],
                        ["xx"],
                        ["xxx"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "width": 0 } } ]
                }
            }
        ]';

        // BEGIN TEST
        $data = [
            ["x"],
            ["xx"],
            ["xxx"]
        ];
        $params = [
            "data" => $data,
            "input.name"   => "field1",
            "input.type"   => "character",
            "input.width"  => 3,
            "input.scale"  => 0,
            "output.name"  => "field1",
            "output.type"  => "character",
            "output.width" => 0,
            "output.scale" => 0
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('A.1', 'Width change on character data; if the width is invalid, the job should fail',  $actual, $expected, $results);
*/


        // TEST: Structure Conversion; Character Type Width Changes

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 3, "scale": 0 }
                    ],
                    "rows": [
                        ["x"],
                        ["xx"],
                        ["xxx"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "width": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('A.1', 'Width change on character data; if the width is invalid, the job should fail',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 3, "scale": 0 }
                    ],
                    "rows": [
                        ["x"],
                        ["xx"],
                        ["xxx"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "width": 100000 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('A.2', 'Width change on character data; if the width is invalid, the job should fail',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 3, "scale": 0 }
                    ],
                    "rows": [
                        ["x"],
                        ["xx"],
                        ["xxx"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "width": 1 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "character" }
            ],
            "rows": [
                ["x"],
                ["x"],
                ["x"]
            ]
        }
        ';
        TestCheck::assertInArray('A.3', 'Width change on character data; make sure width is set and data converted properly',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 3, "scale": 0 }
                    ],
                    "rows": [
                        ["x"],
                        ["xx"],
                        ["xxx"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "width": 10 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "character" }
            ],
            "rows": [
                ["x"],
                ["xx"],
                ["xxx"]
            ]
        }
        ';
        TestCheck::assertInArray('A.4', 'Width change on character data; make sure width is set and data converted properly',  $actual, $expected, $results);



        // TEST: Structure Conversion; Character Type Scale Changes

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 3, "scale": 0 }
                    ],
                    "rows": [
                        ["x"],
                        ["xx"],
                        ["xxx"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "scale": 2 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.1', 'Scale change on character data; scale changes aren\'t allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "character", "width": 3, "scale": 0 }
                    ],
                    "rows": [
                        ["x"],
                        ["xx"],
                        ["xxx"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "width": 10, "scale": 2 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.2', 'Scale change on character data; scale changes aren\'t allowed',  $actual, $expected, $results);



        // TEST: Structure Conversion; Numeric Type Width Changes

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 3, "scale": 0 }
                    ],
                    "rows": [
                        ["1"],
                        ["140"],
                        ["150"],
                        ["160"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "width": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('C.1', 'Width change on numeric data; if the width is invalid, the job should fail',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 3, "scale": 0 }
                    ],
                    "rows": [
                        ["1"],
                        ["140"],
                        ["150"],
                        ["160"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "width": 100000 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('C.2', 'Width change on numeric data; if the width is invalid, the job should fail',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 3, "scale": 0 }
                    ],
                    "rows": [
                        ["1"],
                        ["140"],
                        ["150"],
                        ["160"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "width": 1 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "numeric", "width": 2, "scale": 0 }
            ],
            "rows": [
                ["1"],
                [null],
                [null],
                [null]
            ]
        }
        ';
        TestCheck::assertInArray('C.3', 'Width change on numeric data; make sure width is set and data converted properly',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 3, "scale": 0 }
                    ],
                    "rows": [
                        ["1"],
                        ["140"],
                        ["150"],
                        ["160"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "width": 10 } } ]
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
                ["1"],
                ["140"],
                ["150"],
                ["160"]
            ]
        }
        ';
        TestCheck::assertInArray('C.4', 'Width change on numeric data; make sure width is set and data converted properly',  $actual, $expected, $results);



        // TEST: Structure Conversion; Numeric Type Scale Changes

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 3, "scale": 0 }
                    ],
                    "rows": [
                        ["1"],
                        ["140"],
                        ["150"],
                        ["160"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "scale": -1 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('D.1', 'Width change on numeric data; if the scale is invalid, the job should fail',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 5, "scale": 0 }
                    ],
                    "rows": [
                        ["1"],
                        ["140"],
                        ["150"],
                        ["160"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "scale": 100 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('D.2', 'Width change on numeric data; if the scale is invalid, the job should fail',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 5, "scale": 0 }
                    ],
                    "rows": [
                        ["1"],
                        ["140"],
                        ["150"],
                        ["160"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "scale": 2 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "numeric", "width": 5, "scale": 2 }
            ],
            "rows": [
                ["1.00"],
                ["140.00"],
                ["150.00"],
                ["160.00"]
            ]
        }
        ';
        TestCheck::assertInArray('D.3', 'Width change on numeric data; make sure width is set and data converted properly',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 5, "scale": 2 }
                    ],
                    "rows": [
                        ["1.00"],
                        ["1.04"],
                        ["1.05"],
                        ["1.06"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "scale": 1 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "numeric", "width": 5, "scale": 1 }
            ],
            "rows": [
                ["1.0"],
                ["1.0"],
                ["1.1"],
                ["1.1"]
            ]
        }
        ';
        TestCheck::assertInArray('D.4', 'Width change on numeric data; make sure width is set and data converted properly',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "numeric", "width": 5, "scale": 0 }
                    ],
                    "rows": [
                        ["1"],
                        ["140"],
                        ["150"],
                        ["160"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "width": 8, "scale": 2 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getProcessSingleOutputResult($process);
        $expected = '
        {
            "columns": [
                { "name": "field1", "type": "numeric", "width": 8, "scale": 2 }
            ],
            "rows": [
                ["1.00"],
                ["140.00"],
                ["150.00"],
                ["160.00"]
            ]
        }
        ';
        TestCheck::assertInArray('D.5', 'Width change on numeric data; make sure width is set and data converted properly',  $actual, $expected, $results);



        // TEST: Structure Conversion; Float Type Width and Scale Changes

        // TODO: float width and scale changes should fail; fill out



        // TEST: Structure Conversion; Float Type Width and Scale Changes

        // TODO: double width and scale changes should fail; fill out



        // TEST: Structure Conversion; Date Type Width and Scale Changes

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
                        ["2015-01-01"],
                        ["2016-02-03"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "width": 4 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('G.1', 'Width change on date data; if the width is invalid, the job should fail',  $actual, $expected, $results);

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
                        ["2015-01-01"],
                        ["2016-02-03"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "scale": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('G.2', 'Width change on date data; if the width is invalid, the job should fail',  $actual, $expected, $results);

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
                        ["2015-01-01"],
                        ["2016-02-03"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "width": 10, "scale": 2 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('G.3', 'Width change on date data; if the width is invalid, the job should fail',  $actual, $expected, $results);



        // TEST: Structure Conversion; DateTime Type Width and Scale Changes

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "datetime" }
                    ],
                    "rows": [
                        ["2015-01-01 01:01:01"],
                        ["2016-02-03 01:02:03"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "width": 8 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('H.1', 'Width change on datetime data; if the width is invalid, the job should fail',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "datetime" }
                    ],
                    "rows": [
                        ["2015-01-01 01:01:01"],
                        ["2016-02-03 01:02:03"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "scale": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('H.2', 'Width change on datetime data; if the width is invalid, the job should fail',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "datetime" }
                    ],
                    "rows": [
                        ["2015-01-01 01:01:01"],
                        ["2016-02-03 01:02:03"]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "width": 10, "scale": 2 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('H.3', 'Width change on datetime data; if the width is invalid, the job should fail',  $actual, $expected, $results);



        // TEST: Structure Conversion; Date Type Width and Scale Changes

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "boolean" }
                    ],
                    "rows": [
                        [false],
                        [true]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "width": 1 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('I.1', 'Width change on date data; if the width is invalid, the job should fail',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "boolean" }
                    ],
                    "rows": [
                        [false],
                        [true]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "scale": 0 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('I.2', 'Width change on date data; if the width is invalid, the job should fail',  $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        { "name": "field1", "type": "boolean" }
                    ],
                    "rows": [
                        [false],
                        [true]
                    ]
                }
            },
            {
                "type": "flexio.copy",
                "params": {
                    "actions": [ { "action": "alter", "name": "field1", "params": { "width": 1, "scale": 2 } } ]
                }
            }
        ]
        ';
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('I.3', 'Width change on date data; if the width is invalid, the job should fail',  $actual, $expected, $results);
    }
}
