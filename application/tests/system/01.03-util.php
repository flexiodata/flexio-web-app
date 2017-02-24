<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-05
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: test for type and length

        // BEGIN TEST
        $str = \Flexio\System\Util::generateHandle();
        $actual = is_string($str);
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Flexio\System\Util::generateHandle() test for string return type',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\System\Util::generateHandle();
        $actual = strlen($str) === 20;
        $expected = true;
        TestCheck::assertBoolean('A.2', '\Flexio\System\Util::generateHandle() test for length',  $actual, $expected, $results);



        // TEST: check for some variation within multiple creations

        // BEGIN TEST
        $random_str = array();
        for ($i = 0; $i < 1000; $i++)
        {
            $str = \Flexio\System\Util::generateHandle();
            $random_str[$str] = 1;
        }
        $random_str_count = count($random_str);
        $actual = $random_str_count === 1000;  // we should have enough variation to have 1000 unique values
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Flexio\System\Util::generateHandle() test for randomness',  $actual, $expected, $results);
    }
}
