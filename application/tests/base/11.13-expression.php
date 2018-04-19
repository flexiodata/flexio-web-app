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
        $actual = \Flexio\Tests\Util::evalExpression('current_date(1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.1', 'Expression; current_date() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('current_date()');
        $expected = substr(\Flexio\System\System::getTimestamp(), 0, 11);
        \Flexio\Tests\Check::assertDateApprox('A.2', 'Expression; current_date() date function',  $actual, $expected, $results);

        // BEGIN TEST -- this also works as a variable
        $actual = \Flexio\Tests\Util::evalExpression('current_date');
        $expected = substr(\Flexio\System\System::getTimestamp(), 0, 11);
        \Flexio\Tests\Check::assertDateApprox('A.3', 'Expression; current_date date variable',  $actual, $expected, $results);



        // TEST: date function: day()

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('day()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.1', 'Expression; day() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('day(to_date("2001-02-03","YYYY-MM-DD"),"")');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertNumber('B.2', 'Expression; day() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('day(to_date("2001-02-03","YYYY-MM-DD"))');
        $expected = (float)3;
        \Flexio\Tests\Check::assertNumber('B.3', 'Expression; day() date function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('day(to_date("2001-02-03 04:05:06","YYYY-MM-DD HH:MI:SS"))');
        $expected = (float)3;
        \Flexio\Tests\Check::assertNumber('B.4', 'Expression; day() date function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('day("2001-02-03")');
        $expected = (float)3;
        \Flexio\Tests\Check::assertNumber('B.5', 'Expression; day() date function; TODO: allow implicit type conversion?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('day("2001-02-03 04:05:06")');
        $expected = (float)3;
        \Flexio\Tests\Check::assertNumber('B.6', 'Expression; day() date function; TODO: allow implicit type conversion?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('day(null)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('B.7', 'Expression; day() date function; implicit type conversion',  $actual, $expected, $results);



        // TEST: date function: hour()

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('hour()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.1', 'Expression; hour() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('hour(to_date("2001-02-03","YYYY-MM-DD"),"")');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertNumber('C.2', 'Expression; hour() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('hour(to_date("2001-02-03","YYYY-MM-DD"))');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('C.3', 'Expression; hour() date function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('hour(to_timestamp("2001-02-03 04:05:06","YYYY-MM-DD HH:MI:SS"))');
        $expected = (float)4;
        \Flexio\Tests\Check::assertNumber('C.4', 'Expression; hour() date function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('hour("2001-02-03")');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('C.5', 'Expression; hour() date function; TODO: allow implicit type conversion?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('hour("2001-02-03 04:05:06")');
        $expected = (float)4;
        \Flexio\Tests\Check::assertNumber('C.6', 'Expression; hour() date function; TODO: allow implicit type conversion?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('hour(null)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('C.7', 'Expression; hour() date function; implicit type conversion',  $actual, $expected, $results);



        // TEST: date function: minute()

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('minute()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.1', 'Expression; minute() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('minute(to_date("2001-02-03","YYYY-MM-DD"),"")');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertNumber('D.2', 'Expression; minute() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('minute(to_date("2001-02-03","YYYY-MM-DD"))');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('D.3', 'Expression; minute() date function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('minute(to_timestamp("2001-02-03 04:05:06","YYYY-MM-DD HH:MI:SS"))');
        $expected = (float)5;
        \Flexio\Tests\Check::assertNumber('D.4', 'Expression; minute() date function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('minute("2001-02-03")');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('D.5', 'Expression; minute() date function; TODO: allow implicit type conversion?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('minute("2001-02-03 04:05:06")');
        $expected = (float)5;
        \Flexio\Tests\Check::assertNumber('D.6', 'Expression; minute() date function; TODO: allow implicit type conversion?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('minute(null)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('D.7', 'Expression; minute() date function; implicit type conversion',  $actual, $expected, $results);



        // TEST: date function: month()

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('month()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.1', 'Expression; month() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('month(to_date("2001-02-03","YYYY-MM-DD"),"")');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertNumber('E.2', 'Expression; month() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('month(to_date("2001-02-03","YYYY-MM-DD"))');
        $expected = (float)2;
        \Flexio\Tests\Check::assertNumber('E.3', 'Expression; month() date function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('month(to_date("2001-02-03 04:05:06","YYYY-MM-DD HH:MI:SS"))');
        $expected = (float)2;
        \Flexio\Tests\Check::assertNumber('E.4', 'Expression; month() date function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('month("2001-02-03")');
        $expected = (float)2;
        \Flexio\Tests\Check::assertNumber('E.5', 'Expression; month() date function; TODO: allow implicit type conversion?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('month("2001-02-03 04:05:06")');
        $expected = (float)2;
        \Flexio\Tests\Check::assertNumber('E.6', 'Expression; month() date function; TODO: allow implicit type conversion?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('month(null)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('E.7', 'Expression; month() date function; implicit type conversion',  $actual, $expected, $results);



        // TEST: date function: now()

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('now(1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.1', 'Expression; now() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('now()');
        $expected = \Flexio\System\System::getTimestamp();
        \Flexio\Tests\Check::assertDateApprox('F.2', 'Expression; now() date function',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);



        // TEST: date function: second()

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('second()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('G.1', 'Expression; second() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('second(to_date("2001-02-03","YYYY-MM-DD"),"")');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertNumber('G.2', 'Expression; second() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('second(to_date("2001-02-03","YYYY-MM-DD"))');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('G.3', 'Expression; second() date function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('second(to_timestamp("2001-02-03 04:05:06","YYYY-MM-DD HH:MI:SS"))');
        $expected = (float)6;
        \Flexio\Tests\Check::assertNumber('G.4', 'Expression; second() date function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('second("2001-02-03")');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('G.5', 'Expression; second() date function; TODO: allow implicit type conversion?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('second("2001-02-03 04:05:06")');
        $expected = (float)6;
        \Flexio\Tests\Check::assertNumber('G.6', 'Expression; second() date function; TODO: allow implicit type conversion?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('second(null)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('G.7', 'Expression; second() date function; implicit type conversion',  $actual, $expected, $results);



        // TEST: date function: year()

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('year()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('H.1', 'Expression; year() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('year(to_date("2001-02-03","YYYY-MM-DD"),"")');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertNumber('H.2', 'Expression; year() date function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('year(to_date("2001-02-03","YYYY-MM-DD"))');
        $expected = (float)2001;
        \Flexio\Tests\Check::assertNumber('H.3', 'Expression; year() date function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('year(to_date("2001-02-03 04:05:06","YYYY-MM-DD HH:MI:SS"))');
        $expected = (float)2001;
        \Flexio\Tests\Check::assertNumber('H.4', 'Expression; year() date function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('year("2001-02-03")');
        $expected = (float)2001;
        \Flexio\Tests\Check::assertNumber('H.5', 'Expression; year() date function; TODO: allow implicit type conversion?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('year("2001-02-03 04:05:06")');
        $expected = (float)2001;
        \Flexio\Tests\Check::assertNumber('H.6', 'Expression; year() date function; TODO: allow implicit type conversion?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('year(null)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('H.7', 'Expression; year() date function; implicit type conversion',  $actual, $expected, $results);
    }
}
