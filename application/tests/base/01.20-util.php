<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-10-22
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
        // TEST: \Flexio\Base\Util::createPageRangeArray(); invalid page input

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.1', '\Flexio\Base\Util::createPageRangeArray(); invalid page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("invalid", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.2', '\Flexio\Base\Util::createPageRangeArray(); invalid page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray(",", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.3', '\Flexio\Base\Util::createPageRangeArray(); invalid page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("-", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.4', '\Flexio\Base\Util::createPageRangeArray(); invalid page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray(",,-,,", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.5', '\Flexio\Base\Util::createPageRangeArray(); invalid page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("1,", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.6', '\Flexio\Base\Util::createPageRangeArray(); invalid page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray(",1", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.7', '\Flexio\Base\Util::createPageRangeArray(); invalid page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("1-", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.8', '\Flexio\Base\Util::createPageRangeArray(); invalid page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("1,invalid", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.9', '\Flexio\Base\Util::createPageRangeArray(); invalid page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("1 2", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.10', '\Flexio\Base\Util::createPageRangeArray(); invalid page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("1 last", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.11', '\Flexio\Base\Util::createPageRangeArray(); invalid page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("1 2,3 4", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.12', '\Flexio\Base\Util::createPageRangeArray(); invalid page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("1,,2", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.13', '\Flexio\Base\Util::createPageRangeArray(); invalid page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("1--2", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.14', '\Flexio\Base\Util::createPageRangeArray(); invalid page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("1,null,3", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.14', '\Flexio\Base\Util::createPageRangeArray(); invalid page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("1,false,3", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.14', '\Flexio\Base\Util::createPageRangeArray(); invalid page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("1,2.1,3", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.15', '\Flexio\Base\Util::createPageRangeArray(); basic delimited page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("1.1-2", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.16', '\Flexio\Base\Util::createPageRangeArray(); basic delimited page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("2-3.1", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.17', '\Flexio\Base\Util::createPageRangeArray(); basic delimited page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("1-LAST", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.18', '\Flexio\Base\Util::createPageRangeArray(); basic delimited page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("true", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.19', '\Flexio\Base\Util::createPageRangeArray(); basic delimited page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("1-true", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.20', '\Flexio\Base\Util::createPageRangeArray(); basic delimited page input',  $actual, $expected, $results);


        // TEST: \Flexio\Base\Util::createPageRangeArray(); basic delimited page input

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("none", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('B.1', '\Flexio\Base\Util::createPageRangeArray(); basic delimited page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("-1", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('B.2', '\Flexio\Base\Util::createPageRangeArray(); basic delimited page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("0", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('B.3', '\Flexio\Base\Util::createPageRangeArray(); basic delimited page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("1", 10);
        $expected = [1];
        \Flexio\Tests\Check::assertArray('B.4', '\Flexio\Base\Util::createPageRangeArray(); basic delimited page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("10", 10);
        $expected = [10];
        \Flexio\Tests\Check::assertArray('B.5', '\Flexio\Base\Util::createPageRangeArray(); basic delimited page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("11", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('B.6', '\Flexio\Base\Util::createPageRangeArray(); basic delimited page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray(" 1,2 , 9 ,10  ", 10);
        $expected = [1,2,9,10];
        \Flexio\Tests\Check::assertArray('B.7', '\Flexio\Base\Util::createPageRangeArray(); basic delimited page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("0,1,2,9,10,11", 10);
        $expected = [1,2,9,10];
        \Flexio\Tests\Check::assertArray('B.8', '\Flexio\Base\Util::createPageRangeArray(); basic delimited page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("last", 10);
        $expected = [10];
        \Flexio\Tests\Check::assertArray('B.9', '\Flexio\Base\Util::createPageRangeArray(); basic delimited page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("1,last", 10);
        $expected = [1,10];
        \Flexio\Tests\Check::assertArray('B.10', '\Flexio\Base\Util::createPageRangeArray(); basic delimited page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("last,1", 10);
        $expected = [1,10];
        \Flexio\Tests\Check::assertArray('B.11', '\Flexio\Base\Util::createPageRangeArray(); basic delimited page input',  $actual, $expected, $results);


        // TEST: \Flexio\Base\Util::createPageRangeArray(); basic range page input

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("0-1", 10);
        $expected = [1];
        \Flexio\Tests\Check::assertArray('C.1', '\Flexio\Base\Util::createPageRangeArray(); basic range page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("10-11", 10);
        $expected = [10];
        \Flexio\Tests\Check::assertArray('C.2', '\Flexio\Base\Util::createPageRangeArray(); basic range page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("1-2,9-10", 10);
        $expected = [1,2,9,10];
        \Flexio\Tests\Check::assertArray('C.3', '\Flexio\Base\Util::createPageRangeArray(); basic range page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("1-last", 10);
        $expected = [1,2,3,4,5,6,7,8,9,10];
        \Flexio\Tests\Check::assertArray('C.4', '\Flexio\Base\Util::createPageRangeArray(); basic range page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("2-last", 10);
        $expected = [2,3,4,5,6,7,8,9,10];
        \Flexio\Tests\Check::assertArray('C.5', '\Flexio\Base\Util::createPageRangeArray(); basic range page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("9-last", 10);
        $expected = [9,10];
        \Flexio\Tests\Check::assertArray('C.6', '\Flexio\Base\Util::createPageRangeArray(); basic range page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("2-1", 10);
        $expected = [];
        \Flexio\Tests\Check::assertArray('C.7', '\Flexio\Base\Util::createPageRangeArray(); basic range page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("1-2,4-3", 10);
        $expected = [1,2];
        \Flexio\Tests\Check::assertArray('C.8', '\Flexio\Base\Util::createPageRangeArray(); basic range page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("4-3,1-2", 10);
        $expected = [1,2];
        \Flexio\Tests\Check::assertArray('C.9', '\Flexio\Base\Util::createPageRangeArray(); basic range page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("1-2,last-9", 10);
        $expected = [1,2];
        \Flexio\Tests\Check::assertArray('C.10', '\Flexio\Base\Util::createPageRangeArray(); basic range page input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::createPageRangeArray("last-9,1-2", 10);
        $expected = [1,2];
        \Flexio\Tests\Check::assertArray('C.11', '\Flexio\Base\Util::createPageRangeArray(); basic range page input',  $actual, $expected, $results);


        // TEST: \Flexio\Base\Util::createPageRangeArray(); sort and remove duplicates

       // BEGIN TEST
       $actual = \Flexio\Base\Util::createPageRangeArray("1,1", 10);
       $expected = [1];
       \Flexio\Tests\Check::assertArray('D.1', '\Flexio\Base\Util::createPageRangeArray(); sort and remove duplicates',  $actual, $expected, $results);

       // BEGIN TEST
       $actual = \Flexio\Base\Util::createPageRangeArray("last,last", 10);
       $expected = [10];
       \Flexio\Tests\Check::assertArray('D.2', '\Flexio\Base\Util::createPageRangeArray();sort and remove duplicates',  $actual, $expected, $results);

       // BEGIN TEST
       $actual = \Flexio\Base\Util::createPageRangeArray("10,last", 10);
       $expected = [10];
       \Flexio\Tests\Check::assertArray('D.3', '\Flexio\Base\Util::createPageRangeArray();sort and remove duplicates',  $actual, $expected, $results);

       // BEGIN TEST
       $actual = \Flexio\Base\Util::createPageRangeArray("2,1", 10);
       $expected = [1,2];
       \Flexio\Tests\Check::assertArray('D.4', '\Flexio\Base\Util::createPageRangeArray();sort and remove duplicates',  $actual, $expected, $results);

       // BEGIN TEST
       $actual = \Flexio\Base\Util::createPageRangeArray("1,3,2-4", 10);
       $expected = [1,2,3,4];
       \Flexio\Tests\Check::assertArray('D.5', '\Flexio\Base\Util::createPageRangeArray();sort and remove duplicates',  $actual, $expected, $results);

       // BEGIN TEST
       $actual = \Flexio\Base\Util::createPageRangeArray("6-8,1-3,2-4,3-7", 10);
       $expected = [1,2,3,4,5,6,7,8];
       \Flexio\Tests\Check::assertArray('D.6', '\Flexio\Base\Util::createPageRangeArray();sort and remove duplicates',  $actual, $expected, $results);

       // BEGIN TEST
       $actual = \Flexio\Base\Util::createPageRangeArray("9-11,last", 10);
       $expected = [9,10];
       \Flexio\Tests\Check::assertArray('D.7', '\Flexio\Base\Util::createPageRangeArray();sort and remove duplicates',  $actual, $expected, $results);

       // BEGIN TEST
       $actual = \Flexio\Base\Util::createPageRangeArray("last,9-11", 10);
       $expected = [9,10];
       \Flexio\Tests\Check::assertArray('D.8', '\Flexio\Base\Util::createPageRangeArray();sort and remove duplicates',  $actual, $expected, $results);

       // BEGIN TEST
       $actual = \Flexio\Base\Util::createPageRangeArray("11-9,last", 10);
       $expected = [10];
       \Flexio\Tests\Check::assertArray('D.9', '\Flexio\Base\Util::createPageRangeArray();sort and remove duplicates',  $actual, $expected, $results);

       // BEGIN TEST
       $actual = \Flexio\Base\Util::createPageRangeArray("last,11-9", 10);
       $expected = [10];
       \Flexio\Tests\Check::assertArray('D.10', '\Flexio\Base\Util::createPageRangeArray();sort and remove duplicates',  $actual, $expected, $results);
    }
}
