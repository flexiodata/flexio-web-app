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
        // TEST: \Flexio\Base\Util::filterAlphaNumeric() non-string input

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterAlphaNumeric(null);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Flexio\Base\Util::filterAlphaNumeric() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterAlphaNumeric(false);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.2', '\Flexio\Base\Util::filterAlphaNumeric() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterAlphaNumeric(1);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.3', '\Flexio\Base\Util::filterAlphaNumeric() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterAlphaNumeric('a',null);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.4', '\Flexio\Base\Util::filterAlphaNumeric() non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterAlphaNumeric('a',true);
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.5', '\Flexio\Base\Util::filterAlphaNumeric() non-string input',  $actual, $expected, $results);



        // TEST: \Flexio\Base\Util::filterAlphaNumeric() string input

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterAlphaNumeric('');
        $actual = ($str == '' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Flexio\Base\Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterAlphaNumeric('a');
        $actual = ($str == 'a' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.2', '\Flexio\Base\Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterAlphaNumeric('aa');
        $actual = ($str == 'aa' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.3', '\Flexio\Base\Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterAlphaNumeric('0');
        $actual = ($str == '0' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.4', '\Flexio\Base\Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterAlphaNumeric('12');
        $actual = ($str == '12' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.5', '\Flexio\Base\Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterAlphaNumeric('a1');
        $actual = ($str == 'a1' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.6', '\Flexio\Base\Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterAlphaNumeric('1a');
        $actual = ($str == '1a' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.7', '\Flexio\Base\Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterAlphaNumeric('-0.123');
        $actual = ($str == '123' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.8', '\Flexio\Base\Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterAlphaNumeric('"45%"');
        $actual = ($str == '45' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.9', '\Flexio\Base\Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterAlphaNumeric('X != Y');
        $actual = ($str == 'XY' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.10', '\Flexio\Base\Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterAlphaNumeric('X != Y','');
        $actual = ($str == 'XY' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.11', '\Flexio\Base\Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterAlphaNumeric('X != Y','!=');
        $actual = ($str == 'X!=Y' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.12', '\Flexio\Base\Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterAlphaNumeric('X != Y','!= ');
        $actual = ($str == 'X != Y' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.13', '\Flexio\Base\Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterAlphaNumeric(' X != Y ','= ');
        $actual = ($str == ' X = Y ' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.14', '\Flexio\Base\Util::filterAlphaNumeric() string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::filterAlphaNumeric(' X != Y ',' =');
        $actual = ($str == ' X = Y ' ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.15', '\Flexio\Base\Util::filterAlphaNumeric() string input',  $actual, $expected, $results);
    }
}
