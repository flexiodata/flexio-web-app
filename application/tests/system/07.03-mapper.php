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
        // TEST: top-level array of non-object values

        // BEGIN TEST
        $data = '
        [
        ]
        ';
        $schema = '
        {
        }
        ';
        $actual = \Flexio\System\Mapper::flatten($data, $schema);
        $expected = '
        [
        ]
        ';
        TestCheck::assertArray('A.1', '\Flexio\System\Mapper::flatten(); a simple array of values maps to an array to an array of key values; the key is either the parent node or a default if no key is available',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            1
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
            }
        ]
        ';
        TestCheck::assertArray('A.2', '\Flexio\System\Mapper::flatten(); a simple array of values maps to an array to an array of key values; the key is either the parent node or a default if no key is available',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            1,
            2
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
        TestCheck::assertArray('A.3', '\Flexio\System\Mapper::flatten(); a simple array of values maps to an array to an array of key values; the key is either the parent node or a default if no key is available',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
        [
            true,
            false,
            1,
            0.1,
            0,
            -0.1,
            -1,
            null,
            "value"
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
                "field" : false
            },
            {
                "field" : 1
            },
            {
                "field" : 0.1
            },
            {
                "field" : 0
            },
            {
                "field" : -0.1
            },
            {
                "field" : -1
            },
            {
                "field" : null
            },
            {
                "field" : "value"
            }
        ]
        ';
        TestCheck::assertArray('A.4', '\Flexio\System\Mapper::flatten(); a simple array of values maps to an array to an array of key values; the key is either the parent node or a default if no key is available',  $actual, $expected, $results);
    }
}
