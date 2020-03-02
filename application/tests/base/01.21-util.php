<?php
/**
 *
 * Copyright (c) 2020, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2020-03-02
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
        // TEST: \Flexio\Base\Util::coerceToArray()

        // 1 = ["1"]
        // false => ["false"]
        // 1,2,3 = ["1", "2", "3"]
        // "a,b,c" = ["a", "b", "c"]
        // ["a","b","c"] = ["a", "b", "c"]
        // [["a"],["b"],["c"]] = ["a", "b", "c"]

        // BEGIN TEST
        $actual = \Flexio\Base\Util::coerceToArray(null);
        $expected = [""];
        \Flexio\Tests\Check::assertArray('A.1', '\Flexio\Base\Util::coerceToArray(); coerce input to an array of values',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::coerceToArray(false);
        $expected = ["false"];
        \Flexio\Tests\Check::assertArray('A.2', '\Flexio\Base\Util::coerceToArray(); coerce input to an array of values',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::coerceToArray(true);
        $expected = ["true"];
        \Flexio\Tests\Check::assertArray('A.3', '\Flexio\Base\Util::coerceToArray(); coerce input to an array of values',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::coerceToArray(1);
        $expected = ["1"];
        \Flexio\Tests\Check::assertArray('A.4', '\Flexio\Base\Util::coerceToArray(); coerce input to an array of values',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::coerceToArray(-1);
        $expected = ["-1"];
        \Flexio\Tests\Check::assertArray('A.5', '\Flexio\Base\Util::coerceToArray(); coerce input to an array of values',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::coerceToArray("");
        $expected = [""];
        \Flexio\Tests\Check::assertArray('A.6', '\Flexio\Base\Util::coerceToArray(); coerce input to an array of values',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::coerceToArray(" ");
        $expected = [""];
        \Flexio\Tests\Check::assertArray('A.7', '\Flexio\Base\Util::coerceToArray(); coerce input to an array of values',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::coerceToArray("a");
        $expected = ["a"];
        \Flexio\Tests\Check::assertArray('A.8', '\Flexio\Base\Util::coerceToArray(); coerce input to an array of values',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::coerceToArray("a,",",");
        $expected = ["a",""];
        \Flexio\Tests\Check::assertArray('A.9', '\Flexio\Base\Util::coerceToArray(); coerce input to an array of values',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::coerceToArray(",a",",");
        $expected = ["","a"];
        \Flexio\Tests\Check::assertArray('A.10', '\Flexio\Base\Util::coerceToArray(); coerce input to an array of values',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::coerceToArray("a,b",",");
        $expected = ["a","b"];
        \Flexio\Tests\Check::assertArray('A.11', '\Flexio\Base\Util::coerceToArray(); coerce input to an array of values',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::coerceToArray(" a , b ",",");
        $expected = ["a","b"];
        \Flexio\Tests\Check::assertArray('A.12', '\Flexio\Base\Util::coerceToArray(); coerce input to an array of values',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::coerceToArray([1,2,3]);
        $expected = ["1","2","3"];
        \Flexio\Tests\Check::assertArray('A.13', '\Flexio\Base\Util::coerceToArray(); coerce input to an array of values',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::coerceToArray(["a","b","c"]);
        $expected = ["a","b","c"];
        \Flexio\Tests\Check::assertArray('A.14', '\Flexio\Base\Util::coerceToArray(); coerce input to an array of values',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::coerceToArray([["a"],["b"],["c"]]);
        $expected = ["a","b","c"];
        \Flexio\Tests\Check::assertArray('A.15', '\Flexio\Base\Util::coerceToArray(); coerce input to an array of values',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::coerceToArray([["a","b","c"]]);
        $expected = ["a","b","c"];
        \Flexio\Tests\Check::assertArray('A.16', '\Flexio\Base\Util::coerceToArray(); coerce input to an array of values',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::coerceToArray([["a,b,c"],["d"],["e"]]);
        $expected = ["a","b","c","d","e"];
        \Flexio\Tests\Check::assertArray('A.17', '\Flexio\Base\Util::coerceToArray(); coerce input to an array of values',  $actual, $expected, $results);
    }
}
