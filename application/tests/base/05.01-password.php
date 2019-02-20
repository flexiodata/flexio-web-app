<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-04-13
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
        // TEST: type and length

        // BEGIN TEST
        $str = \Flexio\Base\Password::generate();
        $actual = is_string($str);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Base\Password::generate(); test for string return type',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Password::generate();
        $actual = strlen($str) === 10;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\Password::generate(); test for length',  $actual, $expected, $results);



        // TEST: make sure password contains mixture of characters and numbers

        // BEGIN TEST
        $str = \Flexio\Base\Password::generate();
        $actual = (preg_match('/[0-9]+/', $str) > 0);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Base\Password::generate(); test for embedded numbers',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Password::generate();
        $actual = (preg_match('/[A-Z]+/', $str) > 0);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.2', '\Flexio\Base\Password::generate(); test for embedded uppercase',  $actual, $expected, $results);



        // TEST: check for some variation within multiple creations

        // BEGIN TEST
        $random_str = array();
        for ($i = 0; $i < 1000; $i++)
        {
            $str = \Flexio\Base\Password::generate();
            $random_str[$str] = 1;
        }
        $random_str_count = count($random_str);
        $actual = $random_str_count === 1000;  // we should have enough variation to have 1000 unique values
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.1', '\Flexio\Base\Password::generate(); test for randomness',  $actual, $expected, $results);
    }
}
