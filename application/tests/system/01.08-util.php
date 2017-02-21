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
        // note: this function finds the location of a search
        // string outside of the quoted portion of a string



        // TEST: \Util::zlstrpos() non-string input and empty strings

        // BEGIN TEST
        $pos = \Util::zlstrpos(null, null);
        $actual = ($pos === false);
        $expected = true;
        TestCheck::assertBoolean('A.1', 'Util::zlstrpos() non-string input and empty strings',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos('', null);
        $actual = ($pos === false);
        $expected = true;
        TestCheck::assertBoolean('A.2', 'Util::zlstrpos() non-string input and empty strings',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos(null, '');
        $actual = ($pos === false);
        $expected = true;
        TestCheck::assertBoolean('A.3', 'Util::zlstrpos() non-string input and empty strings',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos(false, false);
        $actual = ($pos === false);
        $expected = true;
        TestCheck::assertBoolean('A.4', 'Util::zlstrpos() non-string input and empty strings',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos('', false);
        $actual = ($pos === false);
        $expected = true;
        TestCheck::assertBoolean('A.5', 'Util::zlstrpos() non-string input and empty strings',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos(false, '');
        $actual = ($pos === false);
        $expected = true;
        TestCheck::assertBoolean('A.6', 'Util::zlstrpos() non-string input and empty strings',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos('', '');
        $actual = ($pos === false);
        $expected = true;
        TestCheck::assertBoolean('A.7', 'Util::zlstrpos() non-string input and empty strings',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos('', '', 5);
        $actual = ($pos === false);
        $expected = true;
        TestCheck::assertBoolean('A.8', 'Util::zlstrpos() non-string input and empty strings',  $actual, $expected, $results);



        // TEST: \Util::zlstrpos() offset parameter

        // BEGIN TEST
        $pos = \Util::zlstrpos('aba', 'a');
        $actual = ($pos === 0);
        $expected = true;
        TestCheck::assertBoolean('B.1', 'Util::zlstrpos() offset parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos('aba', 'a', 0);
        $actual = ($pos === 0);
        $expected = true;
        TestCheck::assertBoolean('B.2', 'Util::zlstrpos() offset parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos('aba', 'a', 1);
        $actual = ($pos === 2);
        $expected = true;
        TestCheck::assertBoolean('B.3', 'Util::zlstrpos() offset parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos('aba', 'a', 2);
        $actual = ($pos === 2);
        $expected = true;
        TestCheck::assertBoolean('B.4', 'Util::zlstrpos() offset parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos('aba', 'a', 3);
        $actual = ($pos === false);
        $expected = true;
        TestCheck::assertBoolean('B.5', 'Util::zlstrpos() offset parameter',  $actual, $expected, $results);



        // TEST: \Util::zlstrpos() multiple character search strings

        // BEGIN TEST
        $pos = \Util::zlstrpos('abab', 'ab', 0);
        $actual = ($pos === 0);
        $expected = true;
        TestCheck::assertBoolean('C.1', 'Util::zlstrpos() multiple character search strings',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos('abab', 'ab', 1);
        $actual = ($pos === 2);
        $expected = true;
        TestCheck::assertBoolean('C.2', 'Util::zlstrpos() multiple character search strings',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos('abab', 'aba', 0);
        $actual = ($pos === 0);
        $expected = true;
        TestCheck::assertBoolean('C.3', 'Util::zlstrpos() multiple character search strings',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos('abab', 'aba', 1);
        $actual = ($pos === false);
        $expected = true;
        TestCheck::assertBoolean('C.4', 'Util::zlstrpos() multiple character search strings',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos('abab', 'bab', 0);
        $actual = ($pos === 1);
        $expected = true;
        TestCheck::assertBoolean('C.5', 'Util::zlstrpos() multiple character search strings',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos('abab', 'bab', 1);
        $actual = ($pos === 1);
        $expected = true;
        TestCheck::assertBoolean('C.6', 'Util::zlstrpos() multiple character search strings',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos('abab', 'abab', 0);
        $actual = ($pos === 0);
        $expected = true;
        TestCheck::assertBoolean('C.7', 'Util::zlstrpos() multiple character search strings',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos('abab', 'abab', 1);
        $actual = ($pos === false);
        $expected = true;
        TestCheck::assertBoolean('C.8', 'Util::zlstrpos() multiple character search strings',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos('abab', 'ababa', 0);
        $actual = ($pos === false);
        $expected = true;
        TestCheck::assertBoolean('C.9', 'Util::zlstrpos() multiple character search strings',  $actual, $expected, $results);



        // TEST: \Util::zlstrpos() quoted character combinations

        // BEGIN TEST
        $pos = \Util::zlstrpos("'a'", '');
        $actual = ($pos === false);
        $expected = true;
        TestCheck::assertBoolean('D.1', 'Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos("'a'", "a");
        $actual = ($pos === 1);
        $expected = true;
        TestCheck::assertBoolean('D.2', 'Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos('"a"', '');
        $actual = ($pos === false);
        $expected = true;
        TestCheck::assertBoolean('D.3', 'Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos('"a"', 'a');
        $actual = ($pos === false);
        $expected = true;
        TestCheck::assertBoolean('D.4', 'Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos('"a"a', 'a');
        $actual = ($pos === 3);
        $expected = true;
        TestCheck::assertBoolean('D.5', 'Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos('"aaa"a', 'a');
        $actual = ($pos === 5);
        $expected = true;
        TestCheck::assertBoolean('D.6', 'Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos('a"aaa"a', 'a');
        $actual = ($pos === 0);
        $expected = true;
        TestCheck::assertBoolean('D.7', 'Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos('a"aaa"a', 'a', 0);
        $actual = ($pos === 0);
        $expected = true;
        TestCheck::assertBoolean('D.8', 'Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos('a"aaa"a', 'a', 1);
        $actual = ($pos === 6);
        $expected = true;
        TestCheck::assertBoolean('D.9', 'Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos("a'aaa'a", 'a', 1);
        $actual = ($pos === 2);
        $expected = true;
        TestCheck::assertBoolean('D.10', 'Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos("a\"aaa\"a", 'a', 1);
        $actual = ($pos === 6);
        $expected = true;
        TestCheck::assertBoolean('D.11', 'Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos('"a"a"a"a"', 'a', 0);
        $actual = ($pos === 3);
        $expected = true;
        TestCheck::assertBoolean('D.12', 'Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos('{"a":"value1,value2", "b":"value3,value4"}', ',', 0);
        $actual = ($pos === 20);
        $expected = true;
        TestCheck::assertBoolean('D.13', 'Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Util::zlstrpos('{"a":"value1,value2, "b":"value3,value4"}', ',', 0);
        $actual = ($pos === 32);
        $expected = true;
        TestCheck::assertBoolean('D.14', 'Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);


        // TEST: \Util::zlstrpos() backslashes

        // BEGIN TEST
        $pos = \Util::zlstrpos('a\\\\b', 'b', 0);
        $actual = ($pos === 3);
        $expected = true;
        TestCheck::assertBoolean('E.1', 'Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);
    }
}
