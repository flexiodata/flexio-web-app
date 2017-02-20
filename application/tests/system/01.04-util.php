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


class Test
{
    public function run(&$results)
    {
        // TEST: type and length

        // BEGIN TEST
        $str = Util::generatePassword();
        $actual = is_string($str);
        $expected = true;
        TestCheck::assertBoolean('A.1', 'Util::generatePassword() test for string return type',  $actual, $expected, $results);

        // BEGIN TEST
        $str = Util::generatePassword();
        $actual = strlen($str) === 10;
        $expected = true;
        TestCheck::assertBoolean('A.2', 'Util::generatePassword() test for length',  $actual, $expected, $results);



        // TEST: make sure password contains mixture of characters and numbers

        // BEGIN TEST
        $str = Util::generatePassword();
        $actual = (preg_match('/[0-9]+/', $str) > 0);
        $expected = true;
        TestCheck::assertBoolean('B.1', 'Util::generatePassword() test for embedded numbers',  $actual, $expected, $results);

        // BEGIN TEST
        $str = Util::generatePassword();
        $actual = (preg_match('/[A-Z]+/', $str) > 0);
        $expected = true;
        TestCheck::assertBoolean('B.2', 'Util::generatePassword() test for embedded uppercase',  $actual, $expected, $results);



        // TEST: check for some variation within multiple creations

        // BEGIN TEST
        $random_str = array();
        for ($i = 0; $i < 1000; $i++)
        {
            $str = Util::generatePassword();
            $random_str[$str] = 1;
        }
        $random_str_count = count($random_str);
        $actual = $random_str_count === 1000;  // we should have enough variation to have 1000 unique values
        $expected = true;
        TestCheck::assertBoolean('C.1', 'Util::generatePassword() test for randomness',  $actual, $expected, $results);
    }
}
