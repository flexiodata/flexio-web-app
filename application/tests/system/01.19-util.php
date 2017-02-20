<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-06
 *
 * @package flexio
 * @subpackage Tests
 */


class Test
{
    public function run(&$results)
    {
        // TODO: uncomment tests and fix tests and/or logic

        // TEST: different input types

        // BEGIN TEST
        $str = Util::formatJson(null);
        $actual = ($str == "null\n" ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.1', 'Util::formatJson() different input types',  $actual, $expected, $results);

        // BEGIN TEST
        $str = Util::formatJson(false);
        $actual = ($str == "false\n" ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.2', 'Util::formatJson() different input types',  $actual, $expected, $results);

        // BEGIN TEST
        $str = Util::formatJson(true);
        $actual = ($str == "true\n" ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.3', 'Util::formatJson() different input types',  $actual, $expected, $results);

        // BEGIN TEST
        $str = Util::formatJson(0);
        $actual = ($str == "0\n" ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.4', 'Util::formatJson() different input types',  $actual, $expected, $results);

        // BEGIN TEST
        $str = Util::formatJson(-1.2);
        $actual = ($str == "-1.2\n" ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.5', 'Util::formatJson() different input types',  $actual, $expected, $results);

        // BEGIN TEST
        $str = Util::formatJson('');
        $actual = ($str == "\n" ? true : false);
        $expected = true;
        TestCheck::assertBoolean('A.6', 'Util::formatJson() different input types',  $actual, $expected, $results);

        // BEGIN TEST
        //$str = Util::formatJson(array());
        //$actual = ($str == "[\n]\n" ? true : false);
        //$expected = true;
        //TestCheck::assertBoolean('A.7', 'Util::formatJson() different input types',  $actual, $expected, $results);



        // TEST: basic json string input

        // BEGIN TEST
        //$str = Util::formatJson('null');
        //$actual = ($str == "null\n" ? true : false);
        //$expected = true;
        //TestCheck::assertBoolean('B.1', 'Util::formatJson() basic json string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = Util::formatJson('false');
        $actual = ($str == "false\n" ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.2', 'Util::formatJson() basic json string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = Util::formatJson('true');
        $actual = ($str == "true\n" ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.3', 'Util::formatJson() basic json string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = Util::formatJson('0');
        $actual = ($str == "0\n" ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.4', 'Util::formatJson() basic json string input',  $actual, $expected, $results);

        // BEGIN TEST
        //$str = Util::formatJson('"test"');
        //$actual = ($str == '"test"\n' ? true : false);
        //$expected = true;
        //TestCheck::assertBoolean('B.5', 'Util::formatJson() basic json string input',  $actual, $expected, $results);

        // BEGIN TEST
        //$str = Util::formatJson('[]');
        //$actual = ($str == "[\n]\n" ? true : false);
        //$expected = true;
        //TestCheck::assertBoolean('B.6', 'Util::formatJson() basic json string input',  $actual, $expected, $results);

        // BEGIN TEST
        //$str = Util::formatJson('{}');
        //$actual = ($str == "{\n}\n" ? true : false);
        //$expected = true;
        //TestCheck::assertBoolean('B.7', 'Util::formatJson() basic json string input',  $actual, $expected, $results);

        // BEGIN TEST
        $str = Util::formatJson('{"a":"b"}');
        $actual = ($str == "{\n    \"a\": \"b\"\n}\n" ? true : false);
        $expected = true;
        TestCheck::assertBoolean('B.8', 'Util::formatJson() basic json string input',  $actual, $expected, $results);
    }
}
