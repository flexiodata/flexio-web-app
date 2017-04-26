<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-02-09
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
        // note: the following date/time functions are currently supported:
        //     current_date();
        //     day(date val)
        //     hour(date val)
        //     minute(date val)
        //     month(date val)
        //     now()
        //     second(date val)
        //     year(date val)


        // TEST: date function: current_date()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('current_date(1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.1', 'Expression; current_date() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('current_date()');
        $expected = substr(\Flexio\System\System::getTimestamp(), 0, 11);
        TestCheck::assertDateApprox('A.2', 'Expression; current_date() date function',  $actual, $expected, $results);



        // TEST: date function: day()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('day()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.1', 'Expression; day() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('day(to_date("2001-02-03","YYYY-MM-DD"),"")');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertNumber('B.2', 'Expression; day() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('day(to_date("2001-02-03","YYYY-MM-DD"))');
        $expected = (float)3;
        TestCheck::assertNumber('B.3', 'Expression; day() date function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('day(to_date("2001-02-03 04:05:06","YYYY-MM-DD HH:MI:SS"))');
        $expected = (float)3;
        TestCheck::assertNumber('B.4', 'Expression; day() date function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('day("2001-02-03")');
        $expected = (float)3;
        TestCheck::assertNumber('B.5', 'Expression; day() date function; TODO: allow implicit type conversion?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('day("2001-02-03 04:05:06")');
        $expected = (float)3;
        TestCheck::assertNumber('B.6', 'Expression; day() date function; TODO: allow implicit type conversion?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('day(null)');
        $expected = null;
        TestCheck::assertNull('B.7', 'Expression; day() date function; implicit type conversion',  $actual, $expected, $results);



        // TEST: date function: hour()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('hour()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.1', 'Expression; hour() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('hour(to_date("2001-02-03","YYYY-MM-DD"),"")');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertNumber('C.2', 'Expression; hour() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('hour(to_date("2001-02-03","YYYY-MM-DD"))');
        $expected = (float)0;
        TestCheck::assertNumber('C.3', 'Expression; hour() date function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('hour(to_timestamp("2001-02-03 04:05:06","YYYY-MM-DD HH:MI:SS"))');
        $expected = (float)4;
        TestCheck::assertNumber('C.4', 'Expression; hour() date function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('hour("2001-02-03")');
        $expected = (float)0;
        TestCheck::assertNumber('C.5', 'Expression; hour() date function; TODO: allow implicit type conversion?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('hour("2001-02-03 04:05:06")');
        $expected = (float)4;
        TestCheck::assertNumber('C.6', 'Expression; hour() date function; TODO: allow implicit type conversion?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('hour(null)');
        $expected = null;
        TestCheck::assertNull('C.7', 'Expression; hour() date function; implicit type conversion',  $actual, $expected, $results);



        // TEST: date function: minute()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('minute()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.1', 'Expression; minute() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('minute(to_date("2001-02-03","YYYY-MM-DD"),"")');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertNumber('D.2', 'Expression; minute() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('minute(to_date("2001-02-03","YYYY-MM-DD"))');
        $expected = (float)0;
        TestCheck::assertNumber('D.3', 'Expression; minute() date function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('minute(to_timestamp("2001-02-03 04:05:06","YYYY-MM-DD HH:MI:SS"))');
        $expected = (float)5;
        TestCheck::assertNumber('D.4', 'Expression; minute() date function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('minute("2001-02-03")');
        $expected = (float)0;
        TestCheck::assertNumber('D.5', 'Expression; minute() date function; TODO: allow implicit type conversion?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('minute("2001-02-03 04:05:06")');
        $expected = (float)5;
        TestCheck::assertNumber('D.6', 'Expression; minute() date function; TODO: allow implicit type conversion?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('minute(null)');
        $expected = null;
        TestCheck::assertNull('D.7', 'Expression; minute() date function; implicit type conversion',  $actual, $expected, $results);



        // TEST: date function: month()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('month()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.1', 'Expression; month() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('month(to_date("2001-02-03","YYYY-MM-DD"),"")');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertNumber('E.2', 'Expression; month() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('month(to_date("2001-02-03","YYYY-MM-DD"))');
        $expected = (float)2;
        TestCheck::assertNumber('E.3', 'Expression; month() date function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('month(to_date("2001-02-03 04:05:06","YYYY-MM-DD HH:MI:SS"))');
        $expected = (float)2;
        TestCheck::assertNumber('E.4', 'Expression; month() date function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('month("2001-02-03")');
        $expected = (float)2;
        TestCheck::assertNumber('E.5', 'Expression; month() date function; TODO: allow implicit type conversion?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('month("2001-02-03 04:05:06")');
        $expected = (float)2;
        TestCheck::assertNumber('E.6', 'Expression; month() date function; TODO: allow implicit type conversion?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('month(null)');
        $expected = null;
        TestCheck::assertNull('E.7', 'Expression; month() date function; implicit type conversion',  $actual, $expected, $results);



        // TEST: date function: now()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('now(1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('F.1', 'Expression; now() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('now()');
        $expected = \Flexio\System\System::getTimestamp();
        TestCheck::assertDateApprox('F.2', 'Expression; now() date function',  $actual, $expected, $results, TestCheck::FLAG_ERROR_SUPPRESS);



        // TEST: date function: second()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('second()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('G.1', 'Expression; second() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('second(to_date("2001-02-03","YYYY-MM-DD"),"")');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertNumber('G.2', 'Expression; second() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('second(to_date("2001-02-03","YYYY-MM-DD"))');
        $expected = (float)0;
        TestCheck::assertNumber('G.3', 'Expression; second() date function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('second(to_timestamp("2001-02-03 04:05:06","YYYY-MM-DD HH:MI:SS"))');
        $expected = (float)6;
        TestCheck::assertNumber('G.4', 'Expression; second() date function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('second("2001-02-03")');
        $expected = (float)0;
        TestCheck::assertNumber('G.5', 'Expression; second() date function; TODO: allow implicit type conversion?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('second("2001-02-03 04:05:06")');
        $expected = (float)6;
        TestCheck::assertNumber('G.6', 'Expression; second() date function; TODO: allow implicit type conversion?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('second(null)');
        $expected = null;
        TestCheck::assertNull('G.7', 'Expression; second() date function; implicit type conversion',  $actual, $expected, $results);



        // TEST: date function: year()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('year()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('H.1', 'Expression; year() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('year(to_date("2001-02-03","YYYY-MM-DD"),"")');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertNumber('H.2', 'Expression; year() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('year(to_date("2001-02-03","YYYY-MM-DD"))');
        $expected = (float)2001;
        TestCheck::assertNumber('H.3', 'Expression; year() date function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('year(to_date("2001-02-03 04:05:06","YYYY-MM-DD HH:MI:SS"))');
        $expected = (float)2001;
        TestCheck::assertNumber('H.4', 'Expression; year() date function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('year("2001-02-03")');
        $expected = (float)2001;
        TestCheck::assertNumber('H.5', 'Expression; year() date function; TODO: allow implicit type conversion?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('year("2001-02-03 04:05:06")');
        $expected = (float)2001;
        TestCheck::assertNumber('H.6', 'Expression; year() date function; TODO: allow implicit type conversion?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('year(null)');
        $expected = null;
        TestCheck::assertNull('H.7', 'Expression; year() date function; implicit type conversion',  $actual, $expected, $results);
    }
}
