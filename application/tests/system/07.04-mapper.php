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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
            }
        ]
        ';
        TestCheck::assertArray('A.1', '\Flexio\System\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1.key2" : "value2"
            }
        ]
        ';
        TestCheck::assertArray('A.2', '\Flexio\System\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1.key2" : "value2",
                "key1.key3" : "value3"
            }
        ]
        ';
        TestCheck::assertArray('A.3', '\Flexio\System\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : "value1",
                "key2.key3" : "value3",
                "key2.key4" : "value4"
            }
        ]
        ';
        TestCheck::assertArray('A.4', '\Flexio\System\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1.key2" : "value2",
                "key1.key3" : "value3",
                "key4" : "value4"
            }
        ]
        ';
        TestCheck::assertArray('A.5', '\Flexio\System\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1.key2" : "value2",
                "key1.key3" : "value3",
                "key4.key5" : "value5",
                "key4.key6" : "value6"
            }
        ]
        ';
        TestCheck::assertArray('A.6', '\Flexio\System\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1.key2.key3" : "value3"
            }
        ]
        ';
        TestCheck::assertArray('A.7', '\Flexio\System\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1.key2.key3" : "value3",
                "key1.key4.key5" : "value5"
            }
        ]
        ';
        TestCheck::assertArray('A.8', '\Flexio\System\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1.key2.key3" : "value3",
                "key1.key2.key4" : "value4",
                "key1.key5.key6" : "value6",
                "key1.key5.key7" : "value7"
            }
        ]
        ';
        TestCheck::assertArray('A.9', '\Flexio\System\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1.key2.key3" : "value3",
                "key1.key2.key4" : "value4",
                "key1.key5.key6" : "value6",
                "key1.key5.key7" : "value7",
                "key8.key9.key10" : "value10",
                "key8.key9.key11" : "value11",
                "key8.key12.key13" : "value13",
                "key8.key12.key14" : "value14"
            }
        ]
        ';
        TestCheck::assertArray('A.10', '\Flexio\System\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1.key2.key3" : null,
                "key1.key4.key5" : true,
                "key1.key4.key6" : false,
                "key7.key8.key9" : -1,
                "key7.key8.key10" : -0.1,
                "key7.key8.key11" : 0,
                "key7.key8.key12" : 0.1,
                "key7.key8.key13" : 1,
                "key7.key14.key15" : "value15",
                "key7.key14.key16" : "value16"
            }
        ]
        ';
        TestCheck::assertArray('A.11', '\Flexio\System\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : "value1",
                "key2.key3.key4" : "value4",
                "key2.key5" : "value5"
            }
        ]
        ';
        TestCheck::assertArray('A.12', '\Flexio\System\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1.key2.key3" : "value3",
                "key1.key2.key4" : "value4",
                "key5" : "value5"
            }
        ]
        ';
        TestCheck::assertArray('A.13', '\Flexio\System\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1.key2.key" : "a",
                "key1.key2.key3" : "value3",
                "key" : "b"
            }
        ]
        ';
        TestCheck::assertArray('A.14', '\Flexio\System\Mapper::flatten(); a top-level object with nested objects flattens the keys to a single object; duplicate keys are removed',  $actual, $expected, $results);



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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1": null
            }
        ]
        ';
        TestCheck::assertArray('B.1', '\Flexio\System\Mapper::flatten(); an object with an array of non-objects distributes the key for each element in the array',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : "value1",
                "key2" : null
            }
        ]
        ';
        TestCheck::assertArray('B.2', '\Flexio\System\Mapper::flatten(); an object with an array of non-objects distributes the key for each element in the array',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : null,
                "key2" : "value2"
            }
        ]
        ';
        TestCheck::assertArray('B.3', '\Flexio\System\Mapper::flatten(); an object with an array of non-objects distributes the key for each element in the array',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : 1
            }
        ]
        ';
        TestCheck::assertArray('B.4', '\Flexio\System\Mapper::flatten(); an object with an array of non-objects distributes the key for each element in the array',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
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
        TestCheck::assertArray('B.5', '\Flexio\System\Mapper::flatten(); an object with an array of non-objects distributes the key for each element in the array',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
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
        TestCheck::assertArray('B.6', '\Flexio\System\Mapper::flatten(); an object with an array of non-objects distributes the key for each element in the array',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
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
        TestCheck::assertArray('B.7', '\Flexio\System\Mapper::flatten(); an object with multiple subarray creates a cross product of the array; equivalent to a header table being joined to two child tables on differnet keys',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1.key2" : 2
            },
            {
                "key1.key3" : 3
            }
        ]
        ';
        TestCheck::assertArray('B.8', '\Flexio\System\Mapper::flatten(); an object with an array of objects uses the object keys in the array',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
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
        TestCheck::assertArray('B.9', '\Flexio\System\Mapper::flatten(); an object with an array of arrays distributes the parent key',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
            {"key1.key1" : "value1"},
            {"key1.key2" : "value2"},
            {"key1.key1" : "value1"},
            {"key1.key3" : "value3"}
        ]
        ';
        TestCheck::assertArray('B.10', '\Flexio\System\Mapper::flatten(); an array of arrays unions the nested arrays',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
            {"key1" : "value1", "key2" : "value2", "key3.key4" : "value4", "key6" : "value6", "key7" : "value7"},
            {"key1" : "value1", "key2" : "value2", "key3.key5" : "value5", "key6" : "value6", "key7" : "value7"}
        ]
        ';
        TestCheck::assertArray('B.11', '\Flexio\System\Mapper::flatten(); an array of arrays unions the nested arrays',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
            {"key1.key2" : "value2", "key4" : "value4", "key5" : "value5"},
            {"key1.key3" : "value3", "key4" : "value4", "key5" : "value5"},
            {"key4" : "value4", "key5" : "value5", "key1.key2" : "value2"},
            {"key4" : "value4", "key5" : "value5", "key1.key3" : "value3"}
        ]
        ';
        TestCheck::assertArray('B.12', '\Flexio\System\Mapper::flatten(); an array of arrays unions the nested arrays',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : 1,
                "key2.key3.key4" : 3,
                "key2.key5.key6" : 5
            },
            {
                "key1" : 1,
                "key2.key3.key4" : 3,
                "key2.key5.key6" : 6
            },
            {
                "key1" : 1,
                "key2.key3.key4" : 4,
                "key2.key5.key6" : 5
            },
            {
                "key1" : 1,
                "key2.key3.key4" : 4,
                "key2.key5.key6" : 6
            },
            {
                "key1" : 2,
                "key2.key3.key4" : 3,
                "key2.key5.key6" : 5
            },
            {
                "key1" : 2,
                "key2.key3.key4" : 3,
                "key2.key5.key6" : 6
            },
            {
                "key1" : 2,
                "key2.key3.key4" : 4,
                "key2.key5.key6" : 5
            },
            {
                "key1" : 2,
                "key2.key3.key4" : 4,
                "key2.key5.key6" : 6
            },
            {
                "key7" : 7
            },
            {
                "key7" : 8
            }
        ]
        ';
        TestCheck::assertArray('B.12', '\Flexio\System\Mapper::flatten(); an array of arrays unions the nested arrays',  $actual, $expected, $results);



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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : 1
            }
        ]
        ';
        TestCheck::assertArray('C.1', '\Flexio\System\Mapper::flatten(); an array with an object flattens the object',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : 1,
                "key2" : 2
            }
        ]
        ';
        TestCheck::assertArray('C.2', '\Flexio\System\Mapper::flatten(); an array with an object flattens the object',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
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
        TestCheck::assertArray('C.3', '\Flexio\System\Mapper::flatten(); an array with an object flattens the object',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
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
        TestCheck::assertArray('C.4', '\Flexio\System\Mapper::flatten(); an array with an object flattens the object',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "key1" : 1,
                "key2.key3" : 3
            }
        ]
        ';
        TestCheck::assertArray('C.5', '\Flexio\System\Mapper::flatten(); an array with an object flattens the object',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
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
        TestCheck::assertArray('C.6', '\Flexio\System\Mapper::flatten(); an array with mixed types flattens each type',  $actual, $expected, $results);



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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
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
        TestCheck::assertArray('D.1', '\Flexio\System\Mapper::flatten(); an array of arrays unions the nested arrays',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
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
        TestCheck::assertArray('D.2', '\Flexio\System\Mapper::flatten(); an array of arrays unions the nested arrays',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
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
        TestCheck::assertArray('D.3', '\Flexio\System\Mapper::flatten(); an array of arrays unions the nested arrays',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
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
        TestCheck::assertArray('D.4', '\Flexio\System\Mapper::flatten(); an array of arrays unions the nested arrays',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
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
        TestCheck::assertArray('D.5', '\Flexio\System\Mapper::flatten(); an array of arrays unions the nested arrays',  $actual, $expected, $results);

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
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
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
        TestCheck::assertArray('D.6', '\Flexio\System\Mapper::flatten(); an array of arrays unions the nested arrays',  $actual, $expected, $results);
    }
}
