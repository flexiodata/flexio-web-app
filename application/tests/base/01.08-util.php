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
        // note: this function finds the location of a search
        // string outside of the quoted portion of a string



        // TEST: \Flexio\Base\Util::zlstrpos() empty strings

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('', '');
        $actual = ($pos === false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Base\Util::zlstrpos() non-string input and empty strings',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('', '', 5);
        $actual = ($pos === false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\Util::zlstrpos() non-string input and empty strings',  $actual, $expected, $results);



        // TEST: \Flexio\Base\Util::zlstrpos() offset parameter

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('aba', 'a');
        $actual = ($pos === 0);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Base\Util::zlstrpos() offset parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('aba', 'a', 0);
        $actual = ($pos === 0);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.2', '\Flexio\Base\Util::zlstrpos() offset parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('aba', 'a', 1);
        $actual = ($pos === 2);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.3', '\Flexio\Base\Util::zlstrpos() offset parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('aba', 'a', 2);
        $actual = ($pos === 2);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.4', '\Flexio\Base\Util::zlstrpos() offset parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('aba', 'a', 3);
        $actual = ($pos === false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.5', '\Flexio\Base\Util::zlstrpos() offset parameter',  $actual, $expected, $results);



        // TEST: \Flexio\Base\Util::zlstrpos() multiple character search strings

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('abab', 'ab', 0);
        $actual = ($pos === 0);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.1', '\Flexio\Base\Util::zlstrpos() multiple character search strings',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('abab', 'ab', 1);
        $actual = ($pos === 2);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.2', '\Flexio\Base\Util::zlstrpos() multiple character search strings',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('abab', 'aba', 0);
        $actual = ($pos === 0);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.3', '\Flexio\Base\Util::zlstrpos() multiple character search strings',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('abab', 'aba', 1);
        $actual = ($pos === false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.4', '\Flexio\Base\Util::zlstrpos() multiple character search strings',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('abab', 'bab', 0);
        $actual = ($pos === 1);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.5', '\Flexio\Base\Util::zlstrpos() multiple character search strings',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('abab', 'bab', 1);
        $actual = ($pos === 1);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.6', '\Flexio\Base\Util::zlstrpos() multiple character search strings',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('abab', 'abab', 0);
        $actual = ($pos === 0);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.7', '\Flexio\Base\Util::zlstrpos() multiple character search strings',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('abab', 'abab', 1);
        $actual = ($pos === false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.8', '\Flexio\Base\Util::zlstrpos() multiple character search strings',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('abab', 'ababa', 0);
        $actual = ($pos === false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.9', '\Flexio\Base\Util::zlstrpos() multiple character search strings',  $actual, $expected, $results);



        // TEST: \Flexio\Base\Util::zlstrpos() quoted character combinations

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos("'a'", '');
        $actual = ($pos === false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.1', '\Flexio\Base\Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos("'a'", "a");
        $actual = ($pos === 1);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.2', '\Flexio\Base\Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('"a"', '');
        $actual = ($pos === false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.3', '\Flexio\Base\Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('"a"', 'a');
        $actual = ($pos === false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.4', '\Flexio\Base\Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('"a"a', 'a');
        $actual = ($pos === 3);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.5', '\Flexio\Base\Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('"aaa"a', 'a');
        $actual = ($pos === 5);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.6', '\Flexio\Base\Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('a"aaa"a', 'a');
        $actual = ($pos === 0);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.7', '\Flexio\Base\Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('a"aaa"a', 'a', 0);
        $actual = ($pos === 0);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.8', '\Flexio\Base\Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('a"aaa"a', 'a', 1);
        $actual = ($pos === 6);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.9', '\Flexio\Base\Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos("a'aaa'a", 'a', 1);
        $actual = ($pos === 2);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.10', '\Flexio\Base\Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos("a\"aaa\"a", 'a', 1);
        $actual = ($pos === 6);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.11', '\Flexio\Base\Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('"a"a"a"a"', 'a', 0);
        $actual = ($pos === 3);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.12', '\Flexio\Base\Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('{"a":"value1,value2", "b":"value3,value4"}', ',', 0);
        $actual = ($pos === 20);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.13', '\Flexio\Base\Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('{"a":"value1,value2, "b":"value3,value4"}', ',', 0);
        $actual = ($pos === 32);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.14', '\Flexio\Base\Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);


        // TEST: \Flexio\Base\Util::zlstrpos() backslashes

        // BEGIN TEST
        $pos = \Flexio\Base\Util::zlstrpos('a\\\\b', 'b', 0);
        $actual = ($pos === 3);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.1', '\Flexio\Base\Util::zlstrpos() quoted character combinations',  $actual, $expected, $results);
    }
}
