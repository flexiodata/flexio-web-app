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


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // note: this function takes two arrays and finds the difference between
        // them and reports the results similarly to how a text diff program would,
        // with + and - for the insertions/deletions; for example, if we have the
        // following:
        //     \Flexio\Base\Util::diff(array('b','c','d'),array('a','b','c','e'))
        // we get:
        //     [{"+":"a"},{"=":"b"},{"=":"c"},{"-":"d"},{"+":"e"}]



        // TEST: \Flexio\Base\Util::diff() non-array input

        // BEGIN TEST
        $arr = \Flexio\Base\Util::diff(null, null);
        $actual = !isset($arr) ? true : false;
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Flexio\Base\Util::diff() non-array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::diff(true, array());
        $actual = !isset($arr) ? true : false;
        $expected = true;
        TestCheck::assertBoolean('A.2', '\Flexio\Base\Util::diff() non-array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::diff(array(), true);
        $actual = !isset($arr) ? true : false;
        $expected = true;
        TestCheck::assertBoolean('A.3', '\Flexio\Base\Util::diff() non-array input',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::diff('a', 'b');
        $actual = !isset($arr) ? true : false;
        $expected = true;
        TestCheck::assertBoolean('A.4', '\Flexio\Base\Util::diff() non-array input',  $actual, $expected, $results);



        // TEST: \Flexio\Base\Util::diff() basic comparisons

        // BEGIN TEST
        $arr = \Flexio\Base\Util::diff(array(),array());
        $actual = (json_encode($arr) == '[]') ? true : false;
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Flexio\Base\Util::diff() basic comparisons',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::diff(array('a'),array('a'));
        $actual = (json_encode($arr) == '[{"=":"a"}]') ? true : false;
        $expected = true;
        TestCheck::assertBoolean('B.2', '\Flexio\Base\Util::diff() basic comparisons',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::diff(array('a'),array());
        $actual = (json_encode($arr) == '[{"-":"a"}]') ? true : false;
        $expected = true;
        TestCheck::assertBoolean('B.3', '\Flexio\Base\Util::diff() basic comparisons',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::diff(array(),array('a'));
        $actual = (json_encode($arr) == '[{"+":"a"}]') ? true : false;
        $expected = true;
        TestCheck::assertBoolean('B.4', '\Flexio\Base\Util::diff() basic comparisons',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::diff(array('a','b','c'),array('a','b','c','d'));
        $actual = (json_encode($arr) == '[{"=":"a"},{"=":"b"},{"=":"c"},{"+":"d"}]') ? true : false;
        $expected = true;
        TestCheck::assertBoolean('B.5', '\Flexio\Base\Util::diff() basic comparisons',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::diff(array('a','b','c','d'),array('a','b','c'));
        $actual = (json_encode($arr) == '[{"=":"a"},{"=":"b"},{"=":"c"},{"-":"d"}]') ? true : false;
        $expected = true;
        TestCheck::assertBoolean('B.6', '\Flexio\Base\Util::diff() basic comparisons',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::diff(array('a','b','c','d'),array('b','c','d'));
        $actual = (json_encode($arr) == '[{"-":"a"},{"=":"b"},{"=":"c"},{"=":"d"}]') ? true : false;
        $expected = true;
        TestCheck::assertBoolean('B.7', '\Flexio\Base\Util::diff() basic comparisons',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::diff(array('b','c','d'),array('a','b','c','d'));
        $actual = (json_encode($arr) == '[{"+":"a"},{"=":"b"},{"=":"c"},{"=":"d"}]') ? true : false;
        $expected = true;
        TestCheck::assertBoolean('B.8', '\Flexio\Base\Util::diff() basic comparisons',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::diff(array('a','d'),array('a','b','c','d'));
        $actual = (json_encode($arr) == '[{"=":"a"},{"+":"b"},{"+":"c"},{"=":"d"}]') ? true : false;
        $expected = true;
        TestCheck::assertBoolean('B.9', '\Flexio\Base\Util::diff() basic comparisons',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::diff(array('a','b','c','d'),array('a','d'));
        $actual = (json_encode($arr) == '[{"=":"a"},{"-":"b"},{"-":"c"},{"=":"d"}]') ? true : false;
        $expected = true;
        TestCheck::assertBoolean('B.10', '\Flexio\Base\Util::diff() basic comparisons',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::diff(array('b','c','d'),array('a','b','c','e'));
        $actual = (json_encode($arr) == '[{"+":"a"},{"=":"b"},{"=":"c"},{"-":"d"},{"+":"e"}]') ? true : false;
        $expected = true;
        TestCheck::assertBoolean('B.11', '\Flexio\Base\Util::diff() basic comparisons',  $actual, $expected, $results);

        // BEGIN TEST
        $arr = \Flexio\Base\Util::diff(array('a','b','c','e'),array('b','c','d'));
        $actual = (json_encode($arr) == '[{"-":"a"},{"=":"b"},{"=":"c"},{"-":"e"},{"+":"d"}]') ? true : false;
        $expected = true;
        TestCheck::assertBoolean('B.12', '\Flexio\Base\Util::diff() basic comparisons',  $actual, $expected, $results);
    }
}
