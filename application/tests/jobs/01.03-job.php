<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-11-27
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
        // TEST: \Flexio\Jobs\Base::addEids(); test adding new eids

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": ""
        }
        ',true);
        $actual = \Flexio\Jobs\Base::addEids($properties);
        $expected = json_decode('
        {
            "eid": "",
            "op": ""
        }
        ',true);
        \Flexio\Tests\Check::assertArrayKeys('A.1', '\Flexio\Jobs\Base::addEids(); make sure eids are added for each operation', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        [
            {
                "op": ""
            }
        ]
        ',true);
        $actual = \Flexio\Jobs\Base::addEids($properties);
        $expected = json_decode('
        [
            {
                "eid": "",
                "op": ""
            }
        ]
        ',true);
        \Flexio\Tests\Check::assertArrayKeys('A.2', '\Flexio\Jobs\Base::addEids(); make sure eids are added for each operation', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        [
            {
                "op": ""
            },
            {
                "op": ""
            }
        ]
        ',true);
        $actual = \Flexio\Jobs\Base::addEids($properties);
        $expected = json_decode('
        [
            {
                "eid": "",
                "op": ""
            },
            {
                "eid": "",
                "op": ""
            }
        ]
        ',true);
        \Flexio\Tests\Check::assertArrayKeys('A.3', '\Flexio\Jobs\Base::addEids(); make sure eids are added for each operation', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "sequence",
            "params": {
                "items": [
                    {
                        "op": ""
                    },
                    {
                        "op": ""
                    }
                ]
            }
        }
        ',true);
        $actual = \Flexio\Jobs\Base::addEids($properties);
        $expected = json_decode('
        {
            "eid": "",
            "op": "sequence",
            "params": {
                "items": [
                    {
                        "eid": "",
                        "op": ""
                    },
                    {
                        "eid": "",
                        "op": ""
                    }
                ]
            }
        }
        ',true);
        \Flexio\Tests\Check::assertArrayKeys('A.4', '\Flexio\Jobs\Base::addEids(); make sure eids are added for each operation', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        [
            {
                "op": "sequence",
                "params": {
                    "items": [
                        {
                            "op": ""
                        },
                        {
                            "op": ""
                        }
                    ]
                }
            },
            {
                "op": "sequence",
                "params": {
                    "items": [
                        {
                            "op": "sequence",
                            "params": {
                                "items": [
                                    {
                                        "op": ""
                                    },
                                    {
                                        "op": ""
                                    }
                                ]
                            }
                        },
                        {
                            "op": ""
                        }
                    ]
                }
            }
        ]
        ',true);
        $actual = \Flexio\Jobs\Base::addEids($properties);
        $expected = json_decode('
        [
            {
                "eid": "",
                "op": "sequence",
                "params": {
                    "items": [
                        {
                            "eid": "",
                            "op": ""
                        },
                        {
                            "eid": "",
                            "op": ""
                        }
                    ]
                }
            },
            {
                "eid": "",
                "op": "sequence",
                "params": {
                    "items": [
                        {
                            "eid": "",
                            "op": "sequence",
                            "params": {
                                "items": [
                                    {
                                        "eid": "",
                                        "op": ""
                                    },
                                    {
                                        "eid": "",
                                        "op": ""
                                    }
                                ]
                            }
                        },
                        {
                            "eid": "",
                            "op": ""
                        }
                    ]
                }
            }
        ]
        ',true);
        \Flexio\Tests\Check::assertArrayKeys('A.5', '\Flexio\Jobs\Base::addEids(); make sure eids are added for each operation', $actual, $expected, $results);



        // TEST: \Flexio\Jobs\Base::addEids(); don't replace existing eids

        // BEGIN TEST
        $properties = json_decode('
        {
            "eid": "abc",
            "op": ""
        }
        ',true);
        $actual = \Flexio\Jobs\Base::addEids($properties);
        $expected = json_decode('
        {
            "eid": "abc",
            "op": ""
        }
        ',true);
        \Flexio\Tests\Check::assertArrayKeys('A.1', '\Flexio\Jobs\Base::addEids(); don\'t replace existing eids', $actual, $expected, $results);
    }
}
