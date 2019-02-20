<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-05
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
        // BEGIN TEST
        $str = \Flexio\Base\Util::generateRandomString(1);
        $actual = strlen($str) === 1;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Base\Util::generateRandomString() test for numeric input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::generateRandomString(8);
        $actual = strlen($str) === 8;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\Util::generateRandomString() test for numeric input',  $actual, $expected, $results);



        // TEST: check for some variation within multiple creations

        // BEGIN TEST
        $random_str = array();
        for ($i = 0; $i < 1000; $i++)
        {
            $str = \Flexio\Base\Util::generateRandomString(20);
            $random_str[$str] = 1;
        }
        $random_str_count = count($random_str);
        $actual = $random_str_count === 1000;  // with 20 chars, we should have enough variation to have 1000 unique values
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Base\Util::generateRandomString() test for randomness',  $actual, $expected, $results);
    }
}
