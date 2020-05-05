<?php
/**
 *
 * Copyright (c) 2020, Flex Research LLC. All rights reserved.
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
        // TEST: \Flexio\Base\Util::coerceToParams(); string input

        // BEGIN TEST
        $actual = \Flexio\Base\Util::coerceToQueryParams("col1=a");
        $expected = [
            "col1"=>["a"]
        ];
        \Flexio\Tests\Check::assertArray('A.1', '\Flexio\Base\Util::coerceToQueryParams(); coerce input to an array of keys and associated match params',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::coerceToQueryParams("col1=a&col2=b");
        $expected = [
            "col1"=>["a"],
            "col2"=>["b"]
        ];
        \Flexio\Tests\Check::assertArray('A.2', '\Flexio\Base\Util::coerceToQueryParams(); coerce input to an array of keys and associated match params',  $actual, $expected, $results);


        // TEST: \Flexio\Base\Util::coerceToParams(); array input

        // BEGIN TEST
        $available_columns = ["col1"];
        $query_arr = [
            ["col1", "a"]
        ];
        $actual = \Flexio\Base\Util::coerceToQueryParams($query_arr, $available_columns);
        $expected = [
            "col1"=>["a"]
        ];
        \Flexio\Tests\Check::assertArray('B.1', '\Flexio\Base\Util::coerceToQueryParams(); coerce 2d-array with single row',  $actual, $expected, $results);

        // BEGIN TEST
        $available_columns = ["col1"];
        $query_arr = [
            ["col1"],
            ["a"]
        ];
        $actual = \Flexio\Base\Util::coerceToQueryParams($query_arr, $available_columns);
        $expected = [
            "col1"=>["a"]
        ];
        \Flexio\Tests\Check::assertArray('B.2', '\Flexio\Base\Util::coerceToQueryParams(); coerce 2d-array with single col',  $actual, $expected, $results);

        // BEGIN TEST
        $available_columns = ["col1"];
        $query_arr = [
            ["Col1", "a"]
        ];
        $actual = \Flexio\Base\Util::coerceToQueryParams($query_arr, $available_columns);
        $expected = [
            "Col1"=>["a"]
        ];
        \Flexio\Tests\Check::assertArray('B.3', '\Flexio\Base\Util::coerceToQueryParams(); coerce 2d-array with single row; preserve case to let downstream use determine behavior',  $actual, $expected, $results);

        // BEGIN TEST
        $available_columns = ["col1"];
        $query_arr = [
            ["Col1"],
            ["a"]
        ];
        $actual = \Flexio\Base\Util::coerceToQueryParams($query_arr, $available_columns);
        $expected = [
            "Col1"=>["a"]
        ];
        \Flexio\Tests\Check::assertArray('B.4', '\Flexio\Base\Util::coerceToQueryParams(); coerce 2d-array with single col; preserve case to let downstream use determine behavior',  $actual, $expected, $results);


        // TEST: \Flexio\Base\Util::coerceToParams(); array input

        // BEGIN TEST
        $available_columns = ["col1"];
        $query_arr = [
            ["col1", "a", "b"]
        ];
        $actual = \Flexio\Base\Util::coerceToQueryParams($query_arr, $available_columns);
        $expected = [
            "col1"=>["a","b"]
        ];
        \Flexio\Tests\Check::assertArray('C.1', '\Flexio\Base\Util::coerceToQueryParams(); coerce 2d-array with single row with multiple values',  $actual, $expected, $results);

        // BEGIN TEST
        $available_columns = ["col1"];
        $query_arr = [
            ["col1"],
            ["a"],
            ["b"]
        ];
        $actual = \Flexio\Base\Util::coerceToQueryParams($query_arr, $available_columns);
        $expected = [
            "col1"=>["a","b"]
        ];
        \Flexio\Tests\Check::assertArray('C.2', '\Flexio\Base\Util::coerceToQueryParams(); coerce 2d-array with single col and multiple values',  $actual, $expected, $results);
    }
}
