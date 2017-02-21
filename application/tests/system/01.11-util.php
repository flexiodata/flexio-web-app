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
        // TEST: \Util::filterAlphaNumeric() non-string input

        // BEGIN TEST
        $str = \Util::filterAlphaNumeric(null);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.1', 'Util::filterAlphaNumeric() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterAlphaNumeric(false);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.2', 'Util::filterAlphaNumeric() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterAlphaNumeric(1);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.3', 'Util::filterAlphaNumeric() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterAlphaNumeric('a',null);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.4', 'Util::filterAlphaNumeric() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterAlphaNumeric('a',true);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.5', 'Util::filterAlphaNumeric() non-string input',  $actual, $expected, $results);



        // TEST: \Util::filterAlphaNumeric() string input

        // BEGIN TEST
        $str = \Util::filterAlphaNumeric('');
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.1', 'Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterAlphaNumeric('a');
        $actual = ($str == 'a' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.2', 'Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterAlphaNumeric('aa');
        $actual = ($str == 'aa' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.3', 'Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterAlphaNumeric('0');
        $actual = ($str == '0' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.4', 'Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterAlphaNumeric('12');
        $actual = ($str == '12' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.5', 'Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterAlphaNumeric('a1');
        $actual = ($str == 'a1' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.6', 'Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterAlphaNumeric('1a');
        $actual = ($str == '1a' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.7', 'Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterAlphaNumeric('-0.123');
        $actual = ($str == '123' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.8', 'Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterAlphaNumeric('"45%"');
        $actual = ($str == '45' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.9', 'Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterAlphaNumeric('X != Y');
        $actual = ($str == 'XY' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.10', 'Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterAlphaNumeric('X != Y','');
        $actual = ($str == 'XY' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.11', 'Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterAlphaNumeric('X != Y','!=');
        $actual = ($str == 'X!=Y' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.12', 'Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterAlphaNumeric('X != Y','!= ');
        $actual = ($str == 'X != Y' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.13', 'Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterAlphaNumeric(' X != Y ','= ');
        $actual = ($str == ' X = Y ' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.14', 'Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::filterAlphaNumeric(' X != Y ',' =');
        $actual = ($str == ' X = Y ' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.15', 'Util::filterAlphaNumeric() string input',  $actual, $expected, $results);
    }
}
