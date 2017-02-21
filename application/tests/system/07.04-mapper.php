<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-07-22
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: top-level object with nested objects

        // BEGIN TEST
        $data = '
        {
            "key1" :
            {
            }
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
            }
        ]
        ';
        TestCheck::assertArray('A.1', '\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" :
            {
                "key2" : "value2"
            }
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1_key2" : "value2"
            }
        ]
        ';
        TestCheck::assertArray('A.2', '\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" :
            {
                "key2" : "value2",
                "key3" : "value3"
            }
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1_key2" : "value2",
                "key1_key3" : "value3"
            }
        ]
        ';
        TestCheck::assertArray('A.3', '\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" : "value1",
            "key2" :
            {
                "key3" : "value3",
                "key4" : "value4"
            }
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : "value1",
                "key2_key3" : "value3",
                "key2_key4" : "value4"
            }
        ]
        ';
        TestCheck::assertArray('A.4', '\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" :
            {
                "key2" : "value2",
                "key3" : "value3"
            },
            "key4" : "value4"
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1_key2" : "value2",
                "key1_key3" : "value3",
                "key4" : "value4"
            }
        ]
        ';
        TestCheck::assertArray('A.5', '\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" :
            {
                "key2" : "value2",
                "key3" : "value3"
            },
            "key4" :
            {
                "key5" : "value5",
                "key6" : "value6"
            }
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1_key2" : "value2",
                "key1_key3" : "value3",
                "key4_key5" : "value5",
                "key4_key6" : "value6"
            }
        ]
        ';
        TestCheck::assertArray('A.6', '\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" :
            {
                "key2" :
                {
                    "key3" : "value3"
                }
            }
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1_key2_key3" : "value3"
            }
        ]
        ';
        TestCheck::assertArray('A.7', '\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" :
            {
                "key2" :
                {
                    "key3" : "value3"
                },
                "key4" :
                {
                    "key5" : "value5"
                }
            }
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1_key2_key3" : "value3",
                "key1_key4_key5" : "value5"
            }
        ]
        ';
        TestCheck::assertArray('A.8', '\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" :
            {
                "key2" :
                {
                    "key3" : "value3",
                    "key4" : "value4"
                },
                "key5" :
                {
                    "key6" : "value6",
                    "key7" : "value7"
                }
            }
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1_key2_key3" : "value3",
                "key1_key2_key4" : "value4",
                "key1_key5_key6" : "value6",
                "key1_key5_key7" : "value7"
            }
        ]
        ';
        TestCheck::assertArray('A.9', '\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" :
            {
                "key2" :
                {
                    "key3" : "value3",
                    "key4" : "value4"
                },
                "key5" :
                {
                    "key6" : "value6",
                    "key7" : "value7"
                }
            },
            "key8" :
            {
                "key9" :
                {
                    "key10" : "value10",
                    "key11" : "value11"
                },
                "key12" :
                {
                    "key13" : "value13",
                    "key14" : "value14"
                }
            }
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1_key2_key3" : "value3",
                "key1_key2_key4" : "value4",
                "key1_key5_key6" : "value6",
                "key1_key5_key7" : "value7",
                "key8_key9_key10" : "value10",
                "key8_key9_key11" : "value11",
                "key8_key12_key13" : "value13",
                "key8_key12_key14" : "value14"
            }
        ]
        ';
        TestCheck::assertArray('A.10', '\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" :
            {
                "key2" :
                {
                    "key3" : null
                },
                "key4" :
                {
                    "key5" : true,
                    "key6" : false
                }
            },
            "key7" :
            {
                "key8" :
                {
                    "key9" : -1,
                    "key10" : -0.1,
                    "key11" : 0,
                    "key12" : 0.1,
                    "key13" : 1
                },
                "key14" :
                {
                    "key15" : "value15",
                    "key16" : "value16"
                }
            }
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1_key2_key3" : null,
                "key1_key4_key5" : true,
                "key1_key4_key6" : false,
                "key7_key8_key9" : -1,
                "key7_key8_key10" : -0.1,
                "key7_key8_key11" : 0,
                "key7_key8_key12" : 0.1,
                "key7_key8_key13" : 1,
                "key7_key14_key15" : "value15",
                "key7_key14_key16" : "value16"
            }
        ]
        ';
        TestCheck::assertArray('A.11', '\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" : "value1",
            "key2" :
            {
                "key3" :
                {
                    "key4" : "value4"
                },
                "key5" : "value5"
            }
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : "value1",
                "key2_key3_key4" : "value4",
                "key2_key5" : "value5"
            }
        ]
        ';
        TestCheck::assertArray('A.12', '\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" :
            {
                "key2" :
                {
                    "key3" : "value3",
                    "key4" : "value4"
                }
            },
            "key5" : "value5"
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1_key2_key3" : "value3",
                "key1_key2_key4" : "value4",
                "key5" : "value5"
            }
        ]
        ';
        TestCheck::assertArray('A.13', '\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" :
            {
                "key2" :
                {
                    "key" : "a",
                    "key3" : "value3"
                }
            },
            "key" : "b"
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1_key2_key" : "a",
                "key1_key2_key3" : "value3",
                "key" : "b"
            }
        ]
        ';
        TestCheck::assertArray('A.14', '\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object; duplicate keys are removed',  $actual, $expected, $results);



        // TEST: top-level object with nested arrays

        // BEGIN TEST
        $data = '
        {
            "key1" :
            [
            ]
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
            }
        ]
        ';
        TestCheck::assertArray('B.1', '\Mapper::flatten(); an object with an array of non-objects distributes the key for each element in the array',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" : "value1",
            "key2" :
            [
            ]
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : "value1"
            }
        ]
        ';
        TestCheck::assertArray('B.2', '\Mapper::flatten(); an object with an array of non-objects distributes the key for each element in the array',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" :
            [
            ],
            "key2" : "value2"
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key2" : "value2"
            }
        ]
        ';
        TestCheck::assertArray('B.3', '\Mapper::flatten(); an object with an array of non-objects distributes the key for each element in the array',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" :
            [
                1
            ]
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : 1
            }
        ]
        ';
        TestCheck::assertArray('B.4', '\Mapper::flatten(); an object with an array of non-objects distributes the key for each element in the array',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" :
            [
                1,
                2
            ]
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : 1
            },
            {
                "key1" : 2
            }
        ]
        ';
        TestCheck::assertArray('B.5', '\Mapper::flatten(); an object with an array of non-objects distributes the key for each element in the array',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" :
            [
                1,
                2
            ],
            "key2" : "value2"
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : 1,
                "key2" : "value2"
            },
            {
                "key1" : 2,
                "key2" : "value2"
            }
        ]
        ';
        TestCheck::assertArray('B.6', '\Mapper::flatten(); an object with an array of non-objects distributes the key for each element in the array',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" :
            [
                1,
                2
            ],
            "key2" :
            [
                3,
                4
            ]
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : 1,
                "key2" : 3
            },
            {
                "key1" : 1,
                "key2" : 4
            },
            {
                "key1" : 2,
                "key2" : 3
            },
            {
                "key1" : 2,
                "key2" : 4
            }
        ]
        ';
        TestCheck::assertArray('B.7', '\Mapper::flatten(); an object with multiple subarray creates a cross product of the array; equivalent to a header table being joined to two child tables on differnet keys',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" :
            [
                {
                    "key2" : 2
                },
                {
                    "key3" : 3
                }
            ]
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1_key2" : 2
            },
            {
                "key1_key3" : 3
            }
        ]
        ';
        TestCheck::assertArray('B.8', '\Mapper::flatten(); an object with an array of objects uses the object keys in the array',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        {
            "key1" :
            [
                [
                    1,
                    2
                ]
            ]
        }
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : 1
            },
            {
                "key1" : 2
            }
        ]
        ';
        TestCheck::assertArray('B.9', '\Mapper::flatten(); an object with an array of arrays distributes the parent key',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            {
                "key1" :
                [
                    {"key1" : "value1"},
                    {"key2" : "value2"}
                ]
            },
            {
                "key1" :
                [
                    {"key1" : "value1"},
                    {"key3" : "value3"}
                ]
            }
        ]
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {"key1_key1" : "value1"},
            {"key1_key2" : "value2"},
            {"key1_key1" : "value1"},
            {"key1_key3" : "value3"}
        ]
        ';
        TestCheck::assertArray('B.10', '\Mapper::flatten(); an array of arrays unions the nested arrays',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            {
                "key1" : "value1",
                "key2" : "value2",
                "key3" : [
                    {"key4" : "value4"},
                    {"key5" : "value5"}
                ],
                "key6" : "value6",
                "key7" : "value7"
            }
        ]
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {"key1" : "value1", "key2" : "value2", "key3_key4" : "value4", "key6" : "value6", "key7" : "value7"},
            {"key1" : "value1", "key2" : "value2", "key3_key5" : "value5", "key6" : "value6", "key7" : "value7"}
        ]
        ';
        TestCheck::assertArray('B.11', '\Mapper::flatten(); an array of arrays unions the nested arrays',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            {
                "key1" : [
                    {"key2" : "value2"},
                    {"key3" : "value3"}
                ],
                "key4" : "value4",
                "key5" : "value5"
            },
            {
                "key4" : "value4",
                "key5" : "value5",
                "key1" : [
                    {"key2" : "value2"},
                    {"key3" : "value3"}
                ]
            }
        ]
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {"key1_key2" : "value2", "key4" : "value4", "key5" : "value5"},
            {"key1_key3" : "value3", "key4" : "value4", "key5" : "value5"},
            {"key4" : "value4", "key5" : "value5", "key1_key2" : "value2"},
            {"key4" : "value4", "key5" : "value5", "key1_key3" : "value3"}
        ]
        ';
        TestCheck::assertArray('B.12', '\Mapper::flatten(); an array of arrays unions the nested arrays',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            {
                "key1" : [
                    1,
                    2
                ],
                "key2" :
                {
                    "key3" :
                    {
                        "key4" : [
                            3,
                            4
                        ]
                    },
                    "key5" :
                    {
                        "key6" : [
                            5,
                            6
                        ]
                    }
                }
            },
            {
                "key7" : [
                    7,
                    8
                ]
            }
        ]
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : 1,
                "key2_key3_key4" : 3,
                "key2_key5_key6" : 5
            },
            {
                "key1" : 1,
                "key2_key3_key4" : 3,
                "key2_key5_key6" : 6
            },
            {
                "key1" : 1,
                "key2_key3_key4" : 4,
                "key2_key5_key6" : 5
            },
            {
                "key1" : 1,
                "key2_key3_key4" : 4,
                "key2_key5_key6" : 6
            },
            {
                "key1" : 2,
                "key2_key3_key4" : 3,
                "key2_key5_key6" : 5
            },
            {
                "key1" : 2,
                "key2_key3_key4" : 3,
                "key2_key5_key6" : 6
            },
            {
                "key1" : 2,
                "key2_key3_key4" : 4,
                "key2_key5_key6" : 5
            },
            {
                "key1" : 2,
                "key2_key3_key4" : 4,
                "key2_key5_key6" : 6
            },
            {
                "key7" : 7
            },
            {
                "key7" : 8
            }
        ]
        ';
        TestCheck::assertArray('B.12', '\Mapper::flatten(); an array of arrays unions the nested arrays',  $actual, $expected, $results);



        // TEST: top-level arrays with objects

        // BEGIN TEST
        $data = '
        [
            {
                "key1" : 1
            }
        ]
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : 1
            }
        ]
        ';
        TestCheck::assertArray('C.1', '\Mapper::flatten(); an array with an object flattens the object',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            {
                "key1" : 1,
                "key2" : 2
            }
        ]
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : 1,
                "key2" : 2
            }
        ]
        ';
        TestCheck::assertArray('C.2', '\Mapper::flatten(); an array with an object flattens the object',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            {
                "key1" : 1
            },
            {
                "key2" : 2
            }
        ]
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : 1
            },
            {
                "key2" : 2
            }
        ]
        ';
        TestCheck::assertArray('C.3', '\Mapper::flatten(); an array with an object flattens the object',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            {
                "key1" : 1,
                "key2" : 2.1
            },
            {
                "key2" : 2.2,
                "key3" : 3
            }
        ]
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : 1,
                "key2" : 2.1
            },
            {
                "key2" : 2.2,
                "key3" : 3
            }
        ]
        ';
        TestCheck::assertArray('C.4', '\Mapper::flatten(); an array with an object flattens the object',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            {
                "key1" : 1,
                "key2" :
                {
                    "key3" : 3
                }
            }
        ]
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : 1,
                "key2_key3" : 3
            }
        ]
        ';
        TestCheck::assertArray('C.5', '\Mapper::flatten(); an array with an object flattens the object',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            true,
            {
                "key1" : 1,
                "key2" : 2
            },
            false
        ]
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "field" : true
            },
            {
                "key1" : 1,
                "key2" : 2
            },
            {
                "field" : false
            }
        ]
        ';
        TestCheck::assertArray('C.6', '\Mapper::flatten(); an array with mixed types flattens each type',  $actual, $expected, $results);



        // TEST: top-level arrays with nested arrays

        // BEGIN TEST
        $data = '
        [
            [
                1,
                2
            ]
        ]
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "field" : 1
            },
            {
                "field" : 2
            }
        ]
        ';
        TestCheck::assertArray('D.1', '\Mapper::flatten(); an array of arrays unions the nested arrays',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            [
                1,
                2
            ],
            [
            ]
        ]
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "field" : 1
            },
            {
                "field" : 2
            }
        ]
        ';
        TestCheck::assertArray('D.2', '\Mapper::flatten(); an array of arrays unions the nested arrays',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            [
            ],
            [
                3,
                4
            ]
        ]
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "field" : 3
            },
            {
                "field" : 4
            }
        ]
        ';
        TestCheck::assertArray('D.3', '\Mapper::flatten(); an array of arrays unions the nested arrays',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            [
                1
            ],
            [
                2
            ]
        ]
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "field" : 1
            },
            {
                "field" : 2
            }
        ]
        ';
        TestCheck::assertArray('D.4', '\Mapper::flatten(); an array of arrays unions the nested arrays',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            [
                1,
                2
            ],
            [
                3,
                4
            ]
        ]
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "field" : 1
            },
            {
                "field" : 2
            },
            {
                "field" : 3
            },
            {
                "field" : 4
            }
        ]
        ';
        TestCheck::assertArray('D.5', '\Mapper::flatten(); an array of arrays unions the nested arrays',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            [
                { "key1" : 1},
                { "key2" : 2}
            ],
            [
                { "key3" : 3},
                { "key4" : 4}
            ]
        ]
        ';
        $schema = '
        {
        }
        ';
        $actual = \Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : 1
            },
            {
                "key2" : 2
            },
            {
                "key3" : 3
            },
            {
                "key4" : 4
            }
        ]
        ';
        TestCheck::assertArray('D.6', '\Mapper::flatten(); an array of arrays unions the nested arrays',  $actual, $expected, $results);
    }
}
