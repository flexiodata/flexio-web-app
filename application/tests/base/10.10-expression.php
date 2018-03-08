<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-02-26
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
        // note: the following conversion functions are currently supported:
        //     to_char(mixed val[, text format])
        //     to_date(mixed val[, text format])
        //     to_datetime(mixed val[, text format])
        //     to_number(mixed val[, text format])
        //     to_timestamp(mixed val[, text format])

        // note: to_char details:
        //     to_char supports the following numeric format codes:
        //       ;:-,etc     returns the number formatted with punctuation or quoted text in the
        //                     specified position
        //       , (comma)   returns the number formatted with a comma in the specified position
        //       . (period)  returns the number formatted with a period in the specified position; only one allowed
        //                     in the format string
        //       $           returns the number formatted with a dollar sign ($) in the specified position
        //       0           returns the number formatted with leading zeros to the left-most occurrence
        //                     of the 0 in the format string
        //       9           returns the number formatted with the specified number of digits with a leading
        //                     space if the number is positive or a leading minus sign if the number is negative
        //       D           returns the number formatted with a decimal character (.) in the specified position
        //       EEEE        returns the number formatted in scientific notation
        //       FM          returns the number formatted to the specified format, but without leading spaces
        //       G           returns the number formatted with a group separator (,) in the specified position
        //       L           returns the number formatted with the currency symbol in the specified position
        //       MI          returns the number formatted with a minus sign in the specified position if the number is negative
        //       PL          returns the number formatted with a plus sign in the specified position if the number is positive
        //       PR          returns the number formatted with leading and trailing spaces if the number if positive
        //                     or enclosed in brackets if the number is negative; format element can only appear at the end
        //                     of the format string
        //       RN          returns a number formatted as an uppercase roman numeral; number must be between 1 and 3999
        //       S           returns a number formatted with a leading or trailing plus sign if the number is positive or a
        //                     leading or trailing minus sign if the number is negative; the leading or trailing position in
        //                     the output number is determined by whether the format element appears at the beginning or the
        //                     end of the format string
        //     to_char supports the following date codes:
        //       HH, HH12    returns the hour of the date (01-12)
        //       HH24        returns the hour of the date (00-23)
        //       MI          returns the minute of the date (00-59)
        //       SS          returns the second of the date (00-59)
        //       MS          returns the millisecond of the date (000-999)
        //       US          returns the microsecond of the date (000000-999999)
        //       SSSS        returns the seconds past midnight of the date (0-86399)
        //       YYYY        returns the year of the date (4 digit or more)
        //       YY          returns the year of the date (2 digit)
        //       IYYY        returns the ISO 8601 week-numbered year of the date (4 or more digits)
        //       MM          returns the day of month of the date (01-12)
        //       MONTH       returns the uppercase month name of the date, padded with spaces to 9 characters
        //       Month       returns the capitalized month name of the date, padded with spaces to 9 characters
        //       month       returns the lowercase month name, padded with spaces to 9 characters
        //       MON         returns the uppercase, abbreviated, three-character month name of the date
        //       Mon         returns the capitalized, abbreviated, three-character month name of the date
        //       mon         returns the lowercase, abbreviated, three-character month name of the date
        //       DAY         returns the uppercase day name of the date, padded with spaces to 9 characters
        //       Day         returns the capitalized day name of the date, padded with spaces to 9 characters
        //       day         returns the lowercase day name of the date, padded with spaces to 9 characters
        //       DY          returns the uppercase, abbreviate day name of the date
        //       Dy          returns the capitalized, abbreviated day name of the date
        //       dy          returns the lowercase, abbreviated day name of the date
        //       DDD         returns the day of year of the date (001-366)
        //       IDDD        returns the ISO 8601 week-numbered day of the date (001-371)
        //       DD          returns the day of month of the date (01-31)
        //       D           returns the day number of the week of the date, where Sunday is the first day of the week
        //       W           returns the week of month of the date, where the first week starts on the first day of the month
        //       WW          returns the week of the year of the date, where the first week starts on the first day of the year
        //       IW          returns the week number of ISO 8601 week-numbered week of the date, where the first week is the first Thursday of the year
        //       J           returns the Julian Day of the date (days since November 24, 4714 BC 00:00:00 UTC)
        //       TZ          returns the uppercase timezone name of the date
        //       tz          returns the lowercase timezone name of the date
        //       OF          returns the timezone offset of the date


        // TODO: do we need to add date format support for the following in to_char()?
        //       AM, am, PM or pm          meridiem indicator
        //       A.M., a.m., P.M. or p.m.  meridiem indicator with periods
        //       BC, bc, AD or ad          era indicator
        //       B.C., b.c., A.A. or a.d.  era indicator with periods



        // TEST: text function: to_char(); function input

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.1', 'Expression; to_char() conversion function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"","")');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.2', 'Expression; to_char() conversion function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(null)');
        $expected = null;
        \Flexio\Tests\Check::assertString('A.3', 'Expression; to_char() conversion function; support conversion of a number to a string without a format',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(true)');
        $expected = "true";
        \Flexio\Tests\Check::assertString('A.4', 'Expression; to_char() conversion function; support conversion of a number to a string without a format',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(false)');
        $expected = "false";
        \Flexio\Tests\Check::assertString('A.5', 'Expression; to_char() conversion function; support conversion of a number to a string without a format',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1)');
        $expected = "1";
        \Flexio\Tests\Check::assertString('A.6', 'Expression; to_char() conversion function; support conversion of a number to a string without a format',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.23)');
        $expected = "-1.23";
        \Flexio\Tests\Check::assertString('A.7', 'Expression; to_char() conversion function; support conversion of a number to a string without a format',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(null,"FM00000")');
        $expected = null;
        \Flexio\Tests\Check::assertNull('A.8', 'Expression; to_char() conversion function; cast non-numeric value input to numeric type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(false,"FM00000")');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.9', 'Expression; to_char() conversion function; ambiguous coercion not possible',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(true,"FM00000")');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.10', 'Expression; to_char() conversion function; ambiguous coercion not possible',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char("0","FM00000")');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.11', 'Expression; to_char() conversion function; ambiguous coercion not possible',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char("1","FM00000")');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.12', 'Expression; to_char() conversion function; ambiguous coercion not possible',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char("10","FM00000")');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.13', 'Expression; to_char() conversion function; ambiguous coercion not possible',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char("123","FM00000")');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.14', 'Expression; to_char() conversion function; ambiguous coercion not possible',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char("abc","FM00000")');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.15', 'Expression; to_char() conversion function; ambiguous coercion not possible',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"")');
        $expected = "";
        \Flexio\Tests\Check::assertString('A.16', 'Expression; to_char() conversion function; no format parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"")');
        $expected = "";
        \Flexio\Tests\Check::assertString('A.17', 'Expression; to_char() conversion function; no format parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(123,"")');
        $expected = "";
        \Flexio\Tests\Check::assertString('A.18', 'Expression; to_char() conversion function; no format parameter',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with '9'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"9")');
        $expected = " 0";
        \Flexio\Tests\Check::assertString('B.1', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"999")');
        $expected = "   0";
        \Flexio\Tests\Check::assertString('B.2', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"9")');
        $expected = " 0";
        \Flexio\Tests\Check::assertString('B.3', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"999")');
        $expected = "   0";
        \Flexio\Tests\Check::assertString('B.4', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"9")');
        $expected = " 0";
        \Flexio\Tests\Check::assertString('B.5', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"999")');
        $expected = "   0";
        \Flexio\Tests\Check::assertString('B.6', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.9,"9")');
        $expected = " 1";
        \Flexio\Tests\Check::assertString('B.7', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.9,"999")');
        $expected = "   1";
        \Flexio\Tests\Check::assertString('B.8', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.9,"9")');
        $expected = "-1";
        \Flexio\Tests\Check::assertString('B.9', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.9,"999")');
        $expected = "  -1";
        \Flexio\Tests\Check::assertString('B.10', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"9")');
        $expected = " 1";
        \Flexio\Tests\Check::assertString('B.11', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"999")');
        $expected = "   1";
        \Flexio\Tests\Check::assertString('B.12', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1,"9")');
        $expected = "-1";
        \Flexio\Tests\Check::assertString('B.13', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1,"999")');
        $expected = "  -1";
        \Flexio\Tests\Check::assertString('B.14', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.4,"9")');
        $expected = " 1";
        \Flexio\Tests\Check::assertString('B.15', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.4,"999")');
        $expected = "   1";
        \Flexio\Tests\Check::assertString('B.16', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.4,"9")');
        $expected = "-1";
        \Flexio\Tests\Check::assertString('B.17', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.4,"999")');
        $expected = "  -1";
        \Flexio\Tests\Check::assertString('B.18', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.5,"9")');
        $expected = " 2";
        \Flexio\Tests\Check::assertString('B.19', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.5,"999")');
        $expected = "   2";
        \Flexio\Tests\Check::assertString('B.20', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.5,"9")');
        $expected = "-2";
        \Flexio\Tests\Check::assertString('B.21', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.5,"999")');
        $expected = "  -2";
        \Flexio\Tests\Check::assertString('B.22', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(10,"9")');
        $expected = " #";
        \Flexio\Tests\Check::assertString('B.23', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(10,"999")');
        $expected = "  10";
        \Flexio\Tests\Check::assertString('B.24', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-10,"9")');
        $expected = "-#";
        \Flexio\Tests\Check::assertString('B.25', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-10,"999")');
        $expected = " -10";
        \Flexio\Tests\Check::assertString('B.26', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234567,"999999")');
        $expected = " ######";
        \Flexio\Tests\Check::assertString('B.27', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234567,"9999999")');
        $expected = " 1234567";
        \Flexio\Tests\Check::assertString('B.28', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234567,"999999")');
        $expected = "-######";
        \Flexio\Tests\Check::assertString('B.29', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234567,"9999999")');
        $expected = "-1234567";
        \Flexio\Tests\Check::assertString('B.30', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with '9' and '.'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"9.99")');
        $expected = "  .00";
        \Flexio\Tests\Check::assertString('C.1', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"999.99")');
        $expected = "    .00";
        \Flexio\Tests\Check::assertString('C.2', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"9.99")');
        $expected = "  .10";
        \Flexio\Tests\Check::assertString('C.3', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"999.99")');
        $expected = "    .10";
        \Flexio\Tests\Check::assertString('C.4', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"9.99")');
        $expected = " -.10";
        \Flexio\Tests\Check::assertString('C.5', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"999.99")');
        $expected = "   -.10";
        \Flexio\Tests\Check::assertString('C.6', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.9,"9.99")');
        $expected = "  .90";
        \Flexio\Tests\Check::assertString('C.7', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.9,"999.99")');
        $expected = "    .90";
        \Flexio\Tests\Check::assertString('C.8', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.9,"9.99")');
        $expected = " -.90";
        \Flexio\Tests\Check::assertString('C.9', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.9,"999.99")');
        $expected = "   -.90";
        \Flexio\Tests\Check::assertString('C.10', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"9.99")');
        $expected = " 1.00";
        \Flexio\Tests\Check::assertString('C.11', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"999.99")');
        $expected = "   1.00";
        \Flexio\Tests\Check::assertString('C.12', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1,"9.99")');
        $expected = "-1.00";
        \Flexio\Tests\Check::assertString('C.13', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1,"999.99")');
        $expected = "  -1.00";
        \Flexio\Tests\Check::assertString('C.14', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.4,"9.99")');
        $expected = " 1.40";
        \Flexio\Tests\Check::assertString('C.15', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.4,"999.99")');
        $expected = "   1.40";
        \Flexio\Tests\Check::assertString('C.16', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.4,"9.99")');
        $expected = "-1.40";
        \Flexio\Tests\Check::assertString('C.17', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.4,"999.99")');
        $expected = "  -1.40";
        \Flexio\Tests\Check::assertString('C.18', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.5,"9.99")');
        $expected = " 1.50";
        \Flexio\Tests\Check::assertString('C.19', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.5,"999.99")');
        $expected = "   1.50";
        \Flexio\Tests\Check::assertString('C.20', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.5,"9.99")');
        $expected = "-1.50";
        \Flexio\Tests\Check::assertString('C.21', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.5,"999.99")');
        $expected = "  -1.50";
        \Flexio\Tests\Check::assertString('C.22', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(10,"9.99")');
        $expected = " #.##";
        \Flexio\Tests\Check::assertString('C.23', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(10,"999.99")');
        $expected = "  10.00";
        \Flexio\Tests\Check::assertString('C.24', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-10,"9.99")');
        $expected = "-#.##";
        \Flexio\Tests\Check::assertString('C.25', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-10,"999.99")');
        $expected = " -10.00";
        \Flexio\Tests\Check::assertString('C.26', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234567,"999999.99")');
        $expected = " ######.##";
        \Flexio\Tests\Check::assertString('C.27', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234567,"9999999.99")');
        $expected = " 1234567.00";
        \Flexio\Tests\Check::assertString('C.28', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234567,"999999.99")');
        $expected = "-######.##";
        \Flexio\Tests\Check::assertString('C.29', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234567,"9999999.99")');
        $expected = "-1234567.00";
        \Flexio\Tests\Check::assertString('C.30', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.3456,"99.")');
        $expected = " 12";
        \Flexio\Tests\Check::assertString('C.31', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.3456,"99.9")');
        $expected = " 12.3";
        \Flexio\Tests\Check::assertString('C.32', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.3456,"99.99")');
        $expected = " 12.35";
        \Flexio\Tests\Check::assertString('C.33', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.3456,"99.999")');
        $expected = " 12.346";
        \Flexio\Tests\Check::assertString('C.34', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.3456,"99.9999")');
        $expected = " 12.3456";
        \Flexio\Tests\Check::assertString('C.35', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.3456,"99.99999")');
        $expected = " 12.34560";
        \Flexio\Tests\Check::assertString('C.36', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-12.3456,"99.")');
        $expected = "-12";
        \Flexio\Tests\Check::assertString('C.37', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-12.3456,"99.9")');
        $expected = "-12.3";
        \Flexio\Tests\Check::assertString('C.38', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-12.3456,"99.99")');
        $expected = "-12.35";
        \Flexio\Tests\Check::assertString('C.39', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-12.3456,"99.999")');
        $expected = "-12.346";
        \Flexio\Tests\Check::assertString('C.40', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-12.3456,"99.9999")');
        $expected = "-12.3456";
        \Flexio\Tests\Check::assertString('C.41', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-12.3456,"99.99999")');
        $expected = "-12.34560";
        \Flexio\Tests\Check::assertString('C.42', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,".99")');
        $expected = " .##";
        \Flexio\Tests\Check::assertString('C.43', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"99.")');
        $expected = "  0";
        \Flexio\Tests\Check::assertString('C.44', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,".99")');
        $expected = "-.##";
        \Flexio\Tests\Check::assertString('C.45', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"99.")');
        $expected = "  0";
        \Flexio\Tests\Check::assertString('C.46', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.3456,"99..99999")');
        $expected = null;
        \Flexio\Tests\Check::assertNull('C.47', 'Expression; to_char() conversion function; return null with multiple decimal places',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.3456,"99.99.999")');
        $expected = null;
        \Flexio\Tests\Check::assertNull('C.48', 'Expression; to_char() conversion function; return null with multiple decimal places',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);



        // TEST: text function: to_char(); format types with '0'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0")');
        $expected = " 0";
        \Flexio\Tests\Check::assertString('D.1', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"000")');
        $expected = " 000";
        \Flexio\Tests\Check::assertString('D.2', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"0")');
        $expected = " 0";
        \Flexio\Tests\Check::assertString('D.3', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"000")');
        $expected = " 000";
        \Flexio\Tests\Check::assertString('D.4', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"0")');
        $expected = " 0";
        \Flexio\Tests\Check::assertString('D.5', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"000")');
        $expected = " 000";
        \Flexio\Tests\Check::assertString('D.6', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.9,"0")');
        $expected = " 1";
        \Flexio\Tests\Check::assertString('D.7', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.9,"000")');
        $expected = " 001";
        \Flexio\Tests\Check::assertString('D.8', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.9,"0")');
        $expected = "-1";
        \Flexio\Tests\Check::assertString('D.9', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.9,"000")');
        $expected = "-001";
        \Flexio\Tests\Check::assertString('D.10', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"0")');
        $expected = " 1";
        \Flexio\Tests\Check::assertString('D.11', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"000")');
        $expected = " 001";
        \Flexio\Tests\Check::assertString('D.12', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1,"0")');
        $expected = "-1";
        \Flexio\Tests\Check::assertString('D.13', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1,"000")');
        $expected = "-001";
        \Flexio\Tests\Check::assertString('D.14', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.4,"0")');
        $expected = " 1";
        \Flexio\Tests\Check::assertString('D.15', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.4,"000")');
        $expected = " 001";
        \Flexio\Tests\Check::assertString('D.16', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.4,"0")');
        $expected = "-1";
        \Flexio\Tests\Check::assertString('D.17', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.4,"000")');
        $expected = "-001";
        \Flexio\Tests\Check::assertString('D.18', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.5,"0")');
        $expected = " 2";
        \Flexio\Tests\Check::assertString('D.19', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.5,"000")');
        $expected = " 002";
        \Flexio\Tests\Check::assertString('D.20', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.5,"0")');
        $expected = "-2";
        \Flexio\Tests\Check::assertString('D.21', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.5,"000")');
        $expected = "-002";
        \Flexio\Tests\Check::assertString('D.22', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(10,"0")');
        $expected = " #";
        \Flexio\Tests\Check::assertString('D.23', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(10,"000")');
        $expected = " 010";
        \Flexio\Tests\Check::assertString('D.24', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-10,"0")');
        $expected = "-#";
        \Flexio\Tests\Check::assertString('D.25', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-10,"000")');
        $expected = "-010";
        \Flexio\Tests\Check::assertString('D.26', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234567,"000000")');
        $expected = " ######";
        \Flexio\Tests\Check::assertString('D.27', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234567,"0000000")');
        $expected = " 1234567";
        \Flexio\Tests\Check::assertString('D.28', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234567,"000000")');
        $expected = "-######";
        \Flexio\Tests\Check::assertString('D.29', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234567,"0000000")');
        $expected = "-1234567";
        \Flexio\Tests\Check::assertString('D.30', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with '0' and '.'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0.00")');
        $expected = " 0.00";
        \Flexio\Tests\Check::assertString('E.1', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"000.00")');
        $expected = " 000.00";
        \Flexio\Tests\Check::assertString('E.2', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"0.00")');
        $expected = " 0.10";
        \Flexio\Tests\Check::assertString('E.3', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"000.00")');
        $expected = " 000.10";
        \Flexio\Tests\Check::assertString('E.4', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"0.00")');
        $expected = "-0.10";
        \Flexio\Tests\Check::assertString('E.5', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"000.00")');
        $expected = "-000.10";
        \Flexio\Tests\Check::assertString('E.6', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.9,"0.00")');
        $expected = " 0.90";
        \Flexio\Tests\Check::assertString('E.7', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.9,"000.00")');
        $expected = " 000.90";
        \Flexio\Tests\Check::assertString('E.8', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.9,"0.00")');
        $expected = "-0.90";
        \Flexio\Tests\Check::assertString('E.9', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.9,"000.00")');
        $expected = "-000.90";
        \Flexio\Tests\Check::assertString('E.10', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"0.00")');
        $expected = " 1.00";
        \Flexio\Tests\Check::assertString('E.11', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"000.00")');
        $expected = " 001.00";
        \Flexio\Tests\Check::assertString('E.12', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1,"0.00")');
        $expected = "-1.00";
        \Flexio\Tests\Check::assertString('E.13', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1,"000.00")');
        $expected = "-001.00";
        \Flexio\Tests\Check::assertString('E.14', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.4,"0.00")');
        $expected = " 1.40";
        \Flexio\Tests\Check::assertString('E.15', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.4,"000.00")');
        $expected = " 001.40";
        \Flexio\Tests\Check::assertString('E.16', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.4,"0.00")');
        $expected = "-1.40";
        \Flexio\Tests\Check::assertString('E.17', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.4,"000.00")');
        $expected = "-001.40";
        \Flexio\Tests\Check::assertString('E.18', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.5,"0.00")');
        $expected = " 1.50";
        \Flexio\Tests\Check::assertString('E.19', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.5,"000.00")');
        $expected = " 001.50";
        \Flexio\Tests\Check::assertString('E.20', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.5,"0.00")');
        $expected = "-1.50";
        \Flexio\Tests\Check::assertString('E.21', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.5,"000.00")');
        $expected = "-001.50";
        \Flexio\Tests\Check::assertString('E.22', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(10,"0.00")');
        $expected = " #.##";
        \Flexio\Tests\Check::assertString('E.23', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(10,"000.00")');
        $expected = " 010.00";
        \Flexio\Tests\Check::assertString('E.24', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-10,"0.00")');
        $expected = "-#.##";
        \Flexio\Tests\Check::assertString('E.25', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-10,"000.00")');
        $expected = "-010.00";
        \Flexio\Tests\Check::assertString('E.26', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234567,"000000.00")');
        $expected = " ######.##";
        \Flexio\Tests\Check::assertString('E.27', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234567,"0000000.00")');
        $expected = " 1234567.00";
        \Flexio\Tests\Check::assertString('E.28', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234567,"000000.00")');
        $expected = "-######.##";
        \Flexio\Tests\Check::assertString('E.29', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234567,"0000000.00")');
        $expected = "-1234567.00";
        \Flexio\Tests\Check::assertString('E.30', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.3456,"00.")');
        $expected = " 12";
        \Flexio\Tests\Check::assertString('E.31', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.3456,"00.0")');
        $expected = " 12.3";
        \Flexio\Tests\Check::assertString('E.32', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.3456,"00.00")');
        $expected = " 12.35";
        \Flexio\Tests\Check::assertString('E.33', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.3456,"00.000")');
        $expected = " 12.346";
        \Flexio\Tests\Check::assertString('E.34', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.3456,"00.0000")');
        $expected = " 12.3456";
        \Flexio\Tests\Check::assertString('E.35', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.3456,"00.00000")');
        $expected = " 12.34560";
        \Flexio\Tests\Check::assertString('E.36', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-12.3456,"00.")');
        $expected = "-12";
        \Flexio\Tests\Check::assertString('E.37', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-12.3456,"00.0")');
        $expected = "-12.3";
        \Flexio\Tests\Check::assertString('E.38', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-12.3456,"00.00")');
        $expected = "-12.35";
        \Flexio\Tests\Check::assertString('E.39', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-12.3456,"00.000")');
        $expected = "-12.346";
        \Flexio\Tests\Check::assertString('E.40', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-12.3456,"00.0000")');
        $expected = "-12.3456";
        \Flexio\Tests\Check::assertString('E.41', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-12.3456,"00.00000")');
        $expected = "-12.34560";
        \Flexio\Tests\Check::assertString('E.42', 'Expression; to_char() conversion function; \'0\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,".00")');
        $expected = " .##";
        \Flexio\Tests\Check::assertString('E.43', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"00.")');
        $expected = " 00";
        \Flexio\Tests\Check::assertString('E.44', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,".00")');
        $expected = "-.##";
        \Flexio\Tests\Check::assertString('E.45', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"00.")');
        $expected = " 00";
        \Flexio\Tests\Check::assertString('E.46', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.3456,"00..00000")');
        $expected = null;
        \Flexio\Tests\Check::assertNull('E.47', 'Expression; to_char() conversion function; return null with multiple decimal places',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.3456,"00.00.000")');
        $expected = null;
        \Flexio\Tests\Check::assertNull('E.48', 'Expression; to_char() conversion function; return null with multiple decimal places',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);



        // TEST: text function: to_char(); format types with mixed '0', '9' and '.'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"999.9")');
        $expected = "   -.1";
        \Flexio\Tests\Check::assertString('F.1', 'Expression; to_char() conversion function; mixed \'0\' and \'9\' format elements',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"999.0")');
        $expected = "   -.1";
        \Flexio\Tests\Check::assertString('F.2', 'Expression; to_char() conversion function; mixed \'0\' and \'9\' format elements',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"990.9")');
        $expected = "  -0.1";
        \Flexio\Tests\Check::assertString('F.3', 'Expression; to_char() conversion function; mixed \'0\' and \'9\' format elements',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"909.9")');
        $expected = " -00.1";
        \Flexio\Tests\Check::assertString('F.4', 'Expression; to_char() conversion function; mixed \'0\' and \'9\' format elements',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"099.9")');
        $expected = "-000.1";
        \Flexio\Tests\Check::assertString('F.5', 'Expression; to_char() conversion function; mixed \'0\' and \'9\' format elements',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"000.0")');
        $expected = "-000.1";
        \Flexio\Tests\Check::assertString('F.6', 'Expression; to_char() conversion function; mixed \'0\' and \'9\' format elements',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"000.9")');
        $expected = "-000.1";
        \Flexio\Tests\Check::assertString('F.7', 'Expression; to_char() conversion function; mixed \'0\' and \'9\' format elements',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"009.0")');
        $expected = "-000.1";
        \Flexio\Tests\Check::assertString('F.8', 'Expression; to_char() conversion function; mixed \'0\' and \'9\' format elements',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"090.0")');
        $expected = "-000.1";
        \Flexio\Tests\Check::assertString('F.9', 'Expression; to_char() conversion function; mixed \'0\' and \'9\' format elements',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"900.0")');
        $expected = " -00.1";
        \Flexio\Tests\Check::assertString('F.10', 'Expression; to_char() conversion function; mixed \'0\' and \'9\' format elements',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"900.9")');
        $expected = " -00.1";
        \Flexio\Tests\Check::assertString('F.11', 'Expression; to_char() conversion function; mixed \'0\' and \'9\' format elements',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"909.0")');
        $expected = " -00.1";
        \Flexio\Tests\Check::assertString('F.12', 'Expression; to_char() conversion function; mixed \'0\' and \'9\' format elements',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"990.9")');
        $expected = "  -0.1";
        \Flexio\Tests\Check::assertString('F.13', 'Expression; to_char() conversion function; mixed \'0\' and \'9\' format elements',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with ','

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"9.9,")');
        $expected = "  .0,";
        \Flexio\Tests\Check::assertString('G.1', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"9.,9")');
        $expected = "  .,0";
        \Flexio\Tests\Check::assertString('G.2', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"9,.9")');
        $expected = "   .0";
        \Flexio\Tests\Check::assertString('G.3', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,",9.9")');
        $expected = "   .0";
        \Flexio\Tests\Check::assertString('G.4', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"9.9,")');
        $expected = " 1.0,";
        \Flexio\Tests\Check::assertString('G.5', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"9.,9")');
        $expected = " 1.,0";
        \Flexio\Tests\Check::assertString('G.6', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"9,.9")');
        $expected = " 1,.0";
        \Flexio\Tests\Check::assertString('G.7', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,",9.9")');
        $expected = "  1.0";
        \Flexio\Tests\Check::assertString('G.8', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"999.999,")');
        $expected = "  12.340,";
        \Flexio\Tests\Check::assertString('G.9', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"999.99,9")');
        $expected = "  12.34,0";
        \Flexio\Tests\Check::assertString('G.10', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"999.9,99")');
        $expected = "  12.3,40";
        \Flexio\Tests\Check::assertString('G.11', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"999.,999")');
        $expected = "  12.,340";
        \Flexio\Tests\Check::assertString('G.12', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"999,.999")');
        $expected = "  12,.340";
        \Flexio\Tests\Check::assertString('G.13', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"99,9.999")');
        $expected = "  1,2.340";
        \Flexio\Tests\Check::assertString('G.14', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"9,99.999")');
        $expected = "   12.340";
        \Flexio\Tests\Check::assertString('G.15', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,",999.999")');
        $expected = "   12.340";
        \Flexio\Tests\Check::assertString('G.16', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"99,9.99,9")');
        $expected = "  1,2.34,0";
        \Flexio\Tests\Check::assertString('G.17', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"99,9.9,99")');
        $expected = "  1,2.3,40";
        \Flexio\Tests\Check::assertString('G.18', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0.0,")');
        $expected = " 0.0,";
        \Flexio\Tests\Check::assertString('G.19', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0.,0")');
        $expected = " 0.,0";
        \Flexio\Tests\Check::assertString('G.20', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0,.0")');
        $expected = " 0,.0";
        \Flexio\Tests\Check::assertString('G.21', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,",0.0")');
        $expected = "  0.0";
        \Flexio\Tests\Check::assertString('G.22', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"0.0,")');
        $expected = " 1.0,";
        \Flexio\Tests\Check::assertString('G.23', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"0.,0")');
        $expected = " 1.,0";
        \Flexio\Tests\Check::assertString('G.24', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"0,.0")');
        $expected = " 1,.0";
        \Flexio\Tests\Check::assertString('G.25', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,",0.0")');
        $expected = "  1.0";
        \Flexio\Tests\Check::assertString('G.26', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"000.000,")');
        $expected = " 012.340,";
        \Flexio\Tests\Check::assertString('G.27', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"000.00,0")');
        $expected = " 012.34,0";
        \Flexio\Tests\Check::assertString('G.28', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"000.0,00")');
        $expected = " 012.3,40";
        \Flexio\Tests\Check::assertString('G.29', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"000.,000")');
        $expected = " 012.,340";
        \Flexio\Tests\Check::assertString('G.30', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"000,.000")');
        $expected = " 012,.340";
        \Flexio\Tests\Check::assertString('G.31', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"00,0.000")');
        $expected = " 01,2.340";
        \Flexio\Tests\Check::assertString('G.32', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"0,00.000")');
        $expected = " 0,12.340";
        \Flexio\Tests\Check::assertString('G.33', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,",000.000")');
        $expected = "  012.340";
        \Flexio\Tests\Check::assertString('G.34', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"00,0.00,0")');
        $expected = " 01,2.34,0";
        \Flexio\Tests\Check::assertString('G.35', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"00,0.0,00")');
        $expected = " 01,2.3,40";
        \Flexio\Tests\Check::assertString('G.36', 'Expression; to_char() conversion function; \',\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with '$'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"9.9$")');
        $expected = "  .0$";
        \Flexio\Tests\Check::assertString('H.1', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"9.$9")');
        $expected = "  .$0";
        \Flexio\Tests\Check::assertString('H.2', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"9$.9")');
        $expected = "  $.0";
        \Flexio\Tests\Check::assertString('H.3', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"$9.9")');
        $expected = "  $.0";
        \Flexio\Tests\Check::assertString('H.4', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"9.9$")');
        $expected = " 1.0$";
        \Flexio\Tests\Check::assertString('H.5', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"9.$9")');
        $expected = " 1.$0";
        \Flexio\Tests\Check::assertString('H.6', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"9$.9")');
        $expected = " 1$.0";
        \Flexio\Tests\Check::assertString('H.7', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"$9.9")');
        $expected = "$ 1.0";
        \Flexio\Tests\Check::assertString('H.8', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"999.999$")');
        $expected = "  12.340$";
        \Flexio\Tests\Check::assertString('H.9', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"999.99$9")');
        $expected = "  12.34$0";
        \Flexio\Tests\Check::assertString('H.10', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"999.9$99")');
        $expected = "  12.3$40";
        \Flexio\Tests\Check::assertString('H.11', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"999.$999")');
        $expected = "  12.$340";
        \Flexio\Tests\Check::assertString('H.12', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"999$.999")');
        $expected = "  12$.340";
        \Flexio\Tests\Check::assertString('H.13', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"99$9.999")');
        $expected = "  1$2.340";
        \Flexio\Tests\Check::assertString('H.14', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"9$99.999")');
        $expected = " $ 12.340";
        \Flexio\Tests\Check::assertString('H.15', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"$999.999")');
        $expected = " $ 12.340";
        \Flexio\Tests\Check::assertString('H.16', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"99$9.99$9")');
        $expected = "  1$2.34$0";
        \Flexio\Tests\Check::assertString('H.17', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"99$9.9$99")');
        $expected = "  1$2.3$40";
        \Flexio\Tests\Check::assertString('H.18', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0.0$")');
        $expected = " 0.0$";
        \Flexio\Tests\Check::assertString('H.19', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0.$0")');
        $expected = " 0.$0";
        \Flexio\Tests\Check::assertString('H.20', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0$.0")');
        $expected = " 0$.0";
        \Flexio\Tests\Check::assertString('H.21', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"$0.0")');
        $expected = "$ 0.0";
        \Flexio\Tests\Check::assertString('H.22', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"0.0$")');
        $expected = " 1.0$";
        \Flexio\Tests\Check::assertString('H.23', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"0.$0")');
        $expected = " 1.$0";
        \Flexio\Tests\Check::assertString('H.24', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"0$.0")');
        $expected = " 1$.0";
        \Flexio\Tests\Check::assertString('H.25', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"$0.0")');
        $expected = "$ 1.0";
        \Flexio\Tests\Check::assertString('H.26', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"000.000$")');
        $expected = " 012.340$";
        \Flexio\Tests\Check::assertString('H.27', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"000.00$0")');
        $expected = " 012.34$0";
        \Flexio\Tests\Check::assertString('H.28', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"000.0$00")');
        $expected = " 012.3$40";
        \Flexio\Tests\Check::assertString('H.29', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"000.$000")');
        $expected = " 012.$340";
        \Flexio\Tests\Check::assertString('H.30', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"000$.000")');
        $expected = " 012$.340";
        \Flexio\Tests\Check::assertString('H.31', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"00$0.000")');
        $expected = " 01$2.340";
        \Flexio\Tests\Check::assertString('H.32', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"0$00.000")');
        $expected = " 0$12.340";
        \Flexio\Tests\Check::assertString('H.33', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"$000.000")');
        $expected = "$ 012.340";
        \Flexio\Tests\Check::assertString('H.34', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"00$0.00$0")');
        $expected = " 01$2.34$0";
        \Flexio\Tests\Check::assertString('H.35', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"00$0.0$00")');
        $expected = " 01$2.3$40";
        \Flexio\Tests\Check::assertString('H.36', 'Expression; to_char() conversion function; \'$\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'D'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"9D99")');
        $expected = "  .00";
        \Flexio\Tests\Check::assertString('I.1', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"999D99")');
        $expected = "    .00";
        \Flexio\Tests\Check::assertString('I.2', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"9D99")');
        $expected = "  .10";
        \Flexio\Tests\Check::assertString('I.3', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"999D99")');
        $expected = "    .10";
        \Flexio\Tests\Check::assertString('I.4', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"9D99")');
        $expected = " -.10";
        \Flexio\Tests\Check::assertString('I.5', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"999D99")');
        $expected = "   -.10";
        \Flexio\Tests\Check::assertString('I.6', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.9,"9D99")');
        $expected = "  .90";
        \Flexio\Tests\Check::assertString('I.7', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.9,"999D99")');
        $expected = "    .90";
        \Flexio\Tests\Check::assertString('I.8', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.9,"9D99")');
        $expected = " -.90";
        \Flexio\Tests\Check::assertString('I.9', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.9,"999D99")');
        $expected = "   -.90";
        \Flexio\Tests\Check::assertString('I.10', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"9D99")');
        $expected = " 1.00";
        \Flexio\Tests\Check::assertString('I.11', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"999D99")');
        $expected = "   1.00";
        \Flexio\Tests\Check::assertString('I.12', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1,"9D99")');
        $expected = "-1.00";
        \Flexio\Tests\Check::assertString('I.13', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1,"999D99")');
        $expected = "  -1.00";
        \Flexio\Tests\Check::assertString('I.14', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.4,"9D99")');
        $expected = " 1.40";
        \Flexio\Tests\Check::assertString('I.15', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.4,"999D99")');
        $expected = "   1.40";
        \Flexio\Tests\Check::assertString('I.16', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.4,"9D99")');
        $expected = "-1.40";
        \Flexio\Tests\Check::assertString('I.17', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.4,"999D99")');
        $expected = "  -1.40";
        \Flexio\Tests\Check::assertString('I.18', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.5,"9D99")');
        $expected = " 1.50";
        \Flexio\Tests\Check::assertString('I.19', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.5,"999D99")');
        $expected = "   1.50";
        \Flexio\Tests\Check::assertString('I.20', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.5,"9D99")');
        $expected = "-1.50";
        \Flexio\Tests\Check::assertString('I.21', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.5,"999D99")');
        $expected = "  -1.50";
        \Flexio\Tests\Check::assertString('I.22', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(10,"9D99")');
        $expected = " #.##";
        \Flexio\Tests\Check::assertString('I.23', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(10,"999D99")');
        $expected = "  10.00";
        \Flexio\Tests\Check::assertString('I.24', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-10,"9D99")');
        $expected = "-#.##";
        \Flexio\Tests\Check::assertString('I.25', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-10,"999D99")');
        $expected = " -10.00";
        \Flexio\Tests\Check::assertString('I.26', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234567,"999999D99")');
        $expected = " ######.##";
        \Flexio\Tests\Check::assertString('I.27', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234567,"9999999D99")');
        $expected = " 1234567.00";
        \Flexio\Tests\Check::assertString('I.28', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234567,"999999D99")');
        $expected = "-######.##";
        \Flexio\Tests\Check::assertString('I.29', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234567,"9999999D99")');
        $expected = "-1234567.00";
        \Flexio\Tests\Check::assertString('I.30', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.3456,"99D")');
        $expected = " 12";
        \Flexio\Tests\Check::assertString('I.31', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.3456,"99D9")');
        $expected = " 12.3";
        \Flexio\Tests\Check::assertString('I.32', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.3456,"99D99")');
        $expected = " 12.35";
        \Flexio\Tests\Check::assertString('I.33', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.3456,"99D999")');
        $expected = " 12.346";
        \Flexio\Tests\Check::assertString('I.34', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.3456,"99D9999")');
        $expected = " 12.3456";
        \Flexio\Tests\Check::assertString('I.35', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.3456,"99D99999")');
        $expected = " 12.34560";
        \Flexio\Tests\Check::assertString('I.36', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-12.3456,"99D")');
        $expected = "-12";
        \Flexio\Tests\Check::assertString('I.37', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-12.3456,"99D9")');
        $expected = "-12.3";
        \Flexio\Tests\Check::assertString('I.38', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-12.3456,"99D99")');
        $expected = "-12.35";
        \Flexio\Tests\Check::assertString('I.39', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-12.3456,"99D999")');
        $expected = "-12.346";
        \Flexio\Tests\Check::assertString('I.40', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-12.3456,"99D9999")');
        $expected = "-12.3456";
        \Flexio\Tests\Check::assertString('I.41', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-12.3456,"99D99999")');
        $expected = "-12.34560";
        \Flexio\Tests\Check::assertString('I.42', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"D99")');
        $expected = " .##";
        \Flexio\Tests\Check::assertString('I.43', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"99D")');
        $expected = "  0";
        \Flexio\Tests\Check::assertString('I.44', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"D99")');
        $expected = "-.##";
        \Flexio\Tests\Check::assertString('I.45', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"9D")');
        $expected = " 0";
        \Flexio\Tests\Check::assertString('I.46', 'Expression; to_char() conversion function; \'9\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.3456,"99DD99999")');
        $expected = null;
        \Flexio\Tests\Check::assertNull('I.47', 'Expression; to_char() conversion function; return null with multiple decimal places',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.3456,"99D99D999")');
        $expected = null;
        \Flexio\Tests\Check::assertNull('I.48', 'Expression; to_char() conversion function; return null with multiple decimal places',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.3456,"99.99D999")');
        $expected = null;
        \Flexio\Tests\Check::assertNull('I.49', 'Expression; to_char() conversion function; return null with multiple decimal places',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.3456,"D99.99999")');
        $expected = null;
        \Flexio\Tests\Check::assertNull('I.50', 'Expression; to_char() conversion function; return null with multiple decimal places',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);



        // TEST: text function: to_char(); format types with 'G'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"9.9G")');
        $expected = "  .0,";
        \Flexio\Tests\Check::assertString('J.1', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"9.G9")');
        $expected = "  .,0";
        \Flexio\Tests\Check::assertString('J.2', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"9G.9")');
        $expected = "   .0";
        \Flexio\Tests\Check::assertString('J.3', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"G9.9")');
        $expected = "   .0";
        \Flexio\Tests\Check::assertString('J.4', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"9.9G")');
        $expected = " 1.0,";
        \Flexio\Tests\Check::assertString('J.5', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"9.G9")');
        $expected = " 1.,0";
        \Flexio\Tests\Check::assertString('J.6', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"9G.9")');
        $expected = " 1,.0";
        \Flexio\Tests\Check::assertString('J.7', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"G9.9")');
        $expected = "  1.0";
        \Flexio\Tests\Check::assertString('J.8', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"999.999G")');
        $expected = "  12.340,";
        \Flexio\Tests\Check::assertString('J.9', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"999.99G9")');
        $expected = "  12.34,0";
        \Flexio\Tests\Check::assertString('J.10', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"999.9G99")');
        $expected = "  12.3,40";
        \Flexio\Tests\Check::assertString('J.11', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"999.G999")');
        $expected = "  12.,340";
        \Flexio\Tests\Check::assertString('J.12', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"999G.999")');
        $expected = "  12,.340";
        \Flexio\Tests\Check::assertString('J.13', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"99G9.999")');
        $expected = "  1,2.340";
        \Flexio\Tests\Check::assertString('J.14', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"9G99.999")');
        $expected = "   12.340";
        \Flexio\Tests\Check::assertString('J.15', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"G999.999")');
        $expected = "   12.340";
        \Flexio\Tests\Check::assertString('J.16', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"99G9.99G9")');
        $expected = "  1,2.34,0";
        \Flexio\Tests\Check::assertString('J.17', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"99G9.9G99")');
        $expected = "  1,2.3,40";
        \Flexio\Tests\Check::assertString('J.18', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0.0G")');
        $expected = " 0.0,";
        \Flexio\Tests\Check::assertString('J.19', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0.G0")');
        $expected = " 0.,0";
        \Flexio\Tests\Check::assertString('J.20', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0G.0")');
        $expected = " 0,.0";
        \Flexio\Tests\Check::assertString('J.21', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"G0.0")');
        $expected = "  0.0";
        \Flexio\Tests\Check::assertString('J.22', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"0.0G")');
        $expected = " 1.0,";
        \Flexio\Tests\Check::assertString('J.23', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"0.G0")');
        $expected = " 1.,0";
        \Flexio\Tests\Check::assertString('J.24', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"0G.0")');
        $expected = " 1,.0";
        \Flexio\Tests\Check::assertString('J.25', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"G0.0")');
        $expected = "  1.0";
        \Flexio\Tests\Check::assertString('J.26', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"000.000G")');
        $expected = " 012.340,";
        \Flexio\Tests\Check::assertString('J.27', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"000.00G0")');
        $expected = " 012.34,0";
        \Flexio\Tests\Check::assertString('J.28', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"000.0G00")');
        $expected = " 012.3,40";
        \Flexio\Tests\Check::assertString('J.29', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"000.G000")');
        $expected = " 012.,340";
        \Flexio\Tests\Check::assertString('J.30', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"000G.000")');
        $expected = " 012,.340";
        \Flexio\Tests\Check::assertString('J.31', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"00G0.000")');
        $expected = " 01,2.340";
        \Flexio\Tests\Check::assertString('J.32', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"0G00.000")');
        $expected = " 0,12.340";
        \Flexio\Tests\Check::assertString('J.33', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"G000.000")');
        $expected = "  012.340";
        \Flexio\Tests\Check::assertString('J.34', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"00G0.00G0")');
        $expected = " 01,2.34,0";
        \Flexio\Tests\Check::assertString('J.35', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"00G0.0G00")');
        $expected = " 01,2.3,40";
        \Flexio\Tests\Check::assertString('J.36', 'Expression; to_char() conversion function; \'G\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'L'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"9.9L")');
        $expected = "  .0$";
        \Flexio\Tests\Check::assertString('K.1', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"9.L9")');
        $expected = "  .$0";
        \Flexio\Tests\Check::assertString('K.2', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"9L.9")');
        $expected = "  $.0";
        \Flexio\Tests\Check::assertString('K.3', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"L9.9")');
        $expected = "  $.0";
        \Flexio\Tests\Check::assertString('K.4', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"9.9L")');
        $expected = " 1.0$";
        \Flexio\Tests\Check::assertString('K.5', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"9.L9")');
        $expected = " 1.$0";
        \Flexio\Tests\Check::assertString('K.6', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"9L.9")');
        $expected = " 1$.0";
        \Flexio\Tests\Check::assertString('K.7', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"L9.9")');
        $expected = "$ 1.0";
        \Flexio\Tests\Check::assertString('K.8', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"999.999L")');
        $expected = "  12.340$";
        \Flexio\Tests\Check::assertString('K.9', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"999.99L9")');
        $expected = "  12.34$0";
        \Flexio\Tests\Check::assertString('K.10', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"999.9L99")');
        $expected = "  12.3$40";
        \Flexio\Tests\Check::assertString('K.11', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"999.L999")');
        $expected = "  12.$340";
        \Flexio\Tests\Check::assertString('K.12', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"999L.999")');
        $expected = "  12$.340";
        \Flexio\Tests\Check::assertString('K.13', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"99L9.999")');
        $expected = "  1$2.340";
        \Flexio\Tests\Check::assertString('K.14', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"9L99.999")');
        $expected = " $ 12.340";
        \Flexio\Tests\Check::assertString('K.15', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"L999.999")');
        $expected = " $ 12.340";
        \Flexio\Tests\Check::assertString('K.16', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"99L9.99L9")');
        $expected = "  1$2.34$0";
        \Flexio\Tests\Check::assertString('K.17', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"99L9.9L99")');
        $expected = "  1$2.3$40";
        \Flexio\Tests\Check::assertString('K.18', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0.0L")');
        $expected = " 0.0$";
        \Flexio\Tests\Check::assertString('K.19', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0.L0")');
        $expected = " 0.$0";
        \Flexio\Tests\Check::assertString('K.20', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0L.0")');
        $expected = " 0$.0";
        \Flexio\Tests\Check::assertString('K.21', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"L0.0")');
        $expected = "$ 0.0";
        \Flexio\Tests\Check::assertString('K.22', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"0.0L")');
        $expected = " 1.0$";
        \Flexio\Tests\Check::assertString('K.23', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"0.L0")');
        $expected = " 1.$0";
        \Flexio\Tests\Check::assertString('K.24', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"0L.0")');
        $expected = " 1$.0";
        \Flexio\Tests\Check::assertString('K.25', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"L0.0")');
        $expected = "$ 1.0";
        \Flexio\Tests\Check::assertString('K.26', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"000.000L")');
        $expected = " 012.340$";
        \Flexio\Tests\Check::assertString('K.27', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"000.00L0")');
        $expected = " 012.34$0";
        \Flexio\Tests\Check::assertString('K.28', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"000.0L00")');
        $expected = " 012.3$40";
        \Flexio\Tests\Check::assertString('K.29', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"000.L000")');
        $expected = " 012.$340";
        \Flexio\Tests\Check::assertString('K.30', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"000L.000")');
        $expected = " 012$.340";
        \Flexio\Tests\Check::assertString('K.31', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"00L0.000")');
        $expected = " 01$2.340";
        \Flexio\Tests\Check::assertString('K.32', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"0L00.000")');
        $expected = " 0$12.340";
        \Flexio\Tests\Check::assertString('K.33', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"L000.000")');
        $expected = "$ 012.340";
        \Flexio\Tests\Check::assertString('K.34', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"00L0.00L0")');
        $expected = " 01$2.34$0";
        \Flexio\Tests\Check::assertString('K.35', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.34,"00L0.0L00")');
        $expected = " 01$2.3$40";
        \Flexio\Tests\Check::assertString('K.36', 'Expression; to_char() conversion function; \'L\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'MI'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0,000.0MI")');
        $expected = "0,000.0 ";
        \Flexio\Tests\Check::assertString('L.1', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0,000.MI0")');
        $expected = "0,000. 0";
        \Flexio\Tests\Check::assertString('L.2', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0,000MI.0")');
        $expected = "0,000 .0";
        \Flexio\Tests\Check::assertString('L.3', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0,00MI0.0")');
        $expected = "0,00 0.0";
        \Flexio\Tests\Check::assertString('L.4', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0,0MI00.0")');
        $expected = "0,0 00.0";
        \Flexio\Tests\Check::assertString('L.5', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0,MI000.0")');
        $expected = "0, 000.0";
        \Flexio\Tests\Check::assertString('L.6', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0MI,000.0")');
        $expected = "0 ,000.0";
        \Flexio\Tests\Check::assertString('L.7', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"MI0,000.0")');
        $expected = " 0,000.0";
        \Flexio\Tests\Check::assertString('L.8', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"0,000.0MI")');
        $expected = "0,000.1 ";
        \Flexio\Tests\Check::assertString('L.9', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"0,000.MI0")');
        $expected = "0,000. 1";
        \Flexio\Tests\Check::assertString('L.10', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"0,000MI.0")');
        $expected = "0,000 .1";
        \Flexio\Tests\Check::assertString('L.11', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"0,00MI0.0")');
        $expected = "0,00 0.1";
        \Flexio\Tests\Check::assertString('L.12', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"0,0MI00.0")');
        $expected = "0,0 00.1";
        \Flexio\Tests\Check::assertString('L.13', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"0,MI000.0")');
        $expected = "0, 000.1";
        \Flexio\Tests\Check::assertString('L.14', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"0MI,000.0")');
        $expected = "0 ,000.1";
        \Flexio\Tests\Check::assertString('L.15', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"MI0,000.0")');
        $expected = " 0,000.1";
        \Flexio\Tests\Check::assertString('L.16', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"0,000.0MI")');
        $expected = "0,000.1-";
        \Flexio\Tests\Check::assertString('L.17', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"0,000.MI0")');
        $expected = "0,000.-1";
        \Flexio\Tests\Check::assertString('L.18', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"0,000MI.0")');
        $expected = "0,000-.1";
        \Flexio\Tests\Check::assertString('L.19', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"0,00MI0.0")');
        $expected = "0,00-0.1";
        \Flexio\Tests\Check::assertString('L.20', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"0,0MI00.0")');
        $expected = "0,0-00.1";
        \Flexio\Tests\Check::assertString('L.21', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"0,MI000.0")');
        $expected = "0,-000.1";
        \Flexio\Tests\Check::assertString('L.22', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"0MI,000.0")');
        $expected = "0-,000.1";
        \Flexio\Tests\Check::assertString('L.23', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"MI0,000.0")');
        $expected = "-0,000.1";
        \Flexio\Tests\Check::assertString('L.24', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234.5,"0,000.0MI")');
        $expected = "1,234.5 ";
        \Flexio\Tests\Check::assertString('L.25', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234.5,"0,000.MI0")');
        $expected = "1,234. 5";
        \Flexio\Tests\Check::assertString('L.26', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234.5,"0,000MI.0")');
        $expected = "1,234 .5";
        \Flexio\Tests\Check::assertString('L.27', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234.5,"0,00MI0.0")');
        $expected = "1,23 4.5";
        \Flexio\Tests\Check::assertString('L.28', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234.5,"0,0MI00.0")');
        $expected = "1,2 34.5";
        \Flexio\Tests\Check::assertString('L.29', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234.5,"0,MI000.0")');
        $expected = "1, 234.5";
        \Flexio\Tests\Check::assertString('L.30', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234.5,"0MI,000.0")');
        $expected = "1 ,234.5";
        \Flexio\Tests\Check::assertString('L.31', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234.5,"MI0,000.0")');
        $expected = " 1,234.5";
        \Flexio\Tests\Check::assertString('L.32', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234.5,"0,000.0MI")');
        $expected = "1,234.5-";
        \Flexio\Tests\Check::assertString('L.33', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234.5,"0,000.MI0")');
        $expected = "1,234.-5";
        \Flexio\Tests\Check::assertString('L.34', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234.5,"0,000MI.0")');
        $expected = "1,234-.5";
        \Flexio\Tests\Check::assertString('L.35', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234.5,"0,00MI0.0")');
        $expected = "1,23-4.5";
        \Flexio\Tests\Check::assertString('L.36', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234.5,"0,0MI00.0")');
        $expected = "1,2-34.5";
        \Flexio\Tests\Check::assertString('L.37', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234.5,"0,MI000.0")');
        $expected = "1,-234.5";
        \Flexio\Tests\Check::assertString('L.38', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234.5,"0MI,000.0")');
        $expected = "1-,234.5";
        \Flexio\Tests\Check::assertString('L.39', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234.5,"MI0,000.0")');
        $expected = "-1,234.5";
        \Flexio\Tests\Check::assertString('L.40', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'PL'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0,000.0PL")');
        $expected = " 0,000.0+";
        \Flexio\Tests\Check::assertString('M.1', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0,000.PL0")');
        $expected = " 0,000.+0";
        \Flexio\Tests\Check::assertString('M.2', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0,000PL.0")');
        $expected = " 0,000+.0";
        \Flexio\Tests\Check::assertString('M.3', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0,00PL0.0")');
        $expected = " 0,00+0.0";
        \Flexio\Tests\Check::assertString('M.4', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0,0PL00.0")');
        $expected = " 0,0+00.0";
        \Flexio\Tests\Check::assertString('M.5', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0,PL000.0")');
        $expected = " 0,+000.0";
        \Flexio\Tests\Check::assertString('M.6', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0PL,000.0")');
        $expected = " 0+,000.0";
        \Flexio\Tests\Check::assertString('M.7', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"PL0,000.0")');
        $expected = "+ 0,000.0";
        \Flexio\Tests\Check::assertString('M.8', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"0,000.0PL")');
        $expected = " 0,000.1+";
        \Flexio\Tests\Check::assertString('M.9', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"0,000.PL0")');
        $expected = " 0,000.+1";
        \Flexio\Tests\Check::assertString('M.10', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"0,000PL.0")');
        $expected = " 0,000+.1";
        \Flexio\Tests\Check::assertString('M.11', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"0,00PL0.0")');
        $expected = " 0,00+0.1";
        \Flexio\Tests\Check::assertString('M.12', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"0,0PL00.0")');
        $expected = " 0,0+00.1";
        \Flexio\Tests\Check::assertString('M.13', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"0,PL000.0")');
        $expected = " 0,+000.1";
        \Flexio\Tests\Check::assertString('M.14', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"0PL,000.0")');
        $expected = " 0+,000.1";
        \Flexio\Tests\Check::assertString('M.15', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"PL0,000.0")');
        $expected = "+ 0,000.1";
        \Flexio\Tests\Check::assertString('M.16', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"0,000.0PL")');
        $expected = "-0,000.1 ";
        \Flexio\Tests\Check::assertString('M.17', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"0,000.PL0")');
        $expected = "-0,000. 1";
        \Flexio\Tests\Check::assertString('M.18', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"0,000PL.0")');
        $expected = "-0,000 .1";
        \Flexio\Tests\Check::assertString('M.19', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"0,00PL0.0")');
        $expected = "-0,00 0.1";
        \Flexio\Tests\Check::assertString('M.20', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"0,0PL00.0")');
        $expected = "-0,0 00.1";
        \Flexio\Tests\Check::assertString('M.21', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"0,PL000.0")');
        $expected = "-0, 000.1";
        \Flexio\Tests\Check::assertString('M.22', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"0PL,000.0")');
        $expected = "-0 ,000.1";
        \Flexio\Tests\Check::assertString('M.23', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"PL0,000.0")');
        $expected = " -0,000.1";
        \Flexio\Tests\Check::assertString('M.24', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234.5,"0,000.0PL")');
        $expected = " 1,234.5+";
        \Flexio\Tests\Check::assertString('M.25', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234.5,"0,000.PL0")');
        $expected = " 1,234.+5";
        \Flexio\Tests\Check::assertString('M.26', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234.5,"0,000PL.0")');
        $expected = " 1,234+.5";
        \Flexio\Tests\Check::assertString('M.27', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234.5,"0,00PL0.0")');
        $expected = " 1,23+4.5";
        \Flexio\Tests\Check::assertString('M.28', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234.5,"0,0PL00.0")');
        $expected = " 1,2+34.5";
        \Flexio\Tests\Check::assertString('M.29', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234.5,"0,PL000.0")');
        $expected = " 1,+234.5";
        \Flexio\Tests\Check::assertString('M.30', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234.5,"0PL,000.0")');
        $expected = " 1+,234.5";
        \Flexio\Tests\Check::assertString('M.31', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234.5,"PL0,000.0")');
        $expected = "+ 1,234.5";
        \Flexio\Tests\Check::assertString('M.32', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234.5,"0,000.0PL")');
        $expected = "-1,234.5 ";
        \Flexio\Tests\Check::assertString('M.33', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234.5,"0,000.PL0")');
        $expected = "-1,234. 5";
        \Flexio\Tests\Check::assertString('M.34', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234.5,"0,000PL.0")');
        $expected = "-1,234 .5";
        \Flexio\Tests\Check::assertString('M.35', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234.5,"0,00PL0.0")');
        $expected = "-1,23 4.5";
        \Flexio\Tests\Check::assertString('M.36', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234.5,"0,0PL00.0")');
        $expected = "-1,2 34.5";
        \Flexio\Tests\Check::assertString('M.37', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234.5,"0,PL000.0")');
        $expected = "-1, 234.5";
        \Flexio\Tests\Check::assertString('M.38', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234.5,"0PL,000.0")');
        $expected = "-1 ,234.5";
        \Flexio\Tests\Check::assertString('M.39', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234.5,"PL0,000.0")');
        $expected = " -1,234.5";
        \Flexio\Tests\Check::assertString('M.40', 'Expression; to_char() conversion function; \'PL\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'S'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0,000.0S")');
        $expected = "0,000.0+";
        \Flexio\Tests\Check::assertString('N.1', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0,000.S0")');
        $expected = "0,000.0+";
        \Flexio\Tests\Check::assertString('N.2', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0,000S.0")');
        $expected = "0,000.0+";
        \Flexio\Tests\Check::assertString('N.3', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0,00S0.0")');
        $expected = "+0,000.0";
        \Flexio\Tests\Check::assertString('N.4', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0,0S00.0")');
        $expected = "+0,000.0";
        \Flexio\Tests\Check::assertString('N.5', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0,S000.0")');
        $expected = "+0,000.0";
        \Flexio\Tests\Check::assertString('N.6', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0S,000.0")');
        $expected = "+0,000.0";
        \Flexio\Tests\Check::assertString('N.7', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"S0,000.0")');
        $expected = "+0,000.0";
        \Flexio\Tests\Check::assertString('N.8', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"0,000.0S")');
        $expected = "0,000.1+";
        \Flexio\Tests\Check::assertString('N.9', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"0,000.S0")');
        $expected = "0,000.1+";
        \Flexio\Tests\Check::assertString('N.10', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"0,000S.0")');
        $expected = "0,000.1+";
        \Flexio\Tests\Check::assertString('N.11', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"0,00S0.0")');
        $expected = "+0,000.1";
        \Flexio\Tests\Check::assertString('N.12', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"0,0S00.0")');
        $expected = "+0,000.1";
        \Flexio\Tests\Check::assertString('N.13', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"0,S000.0")');
        $expected = "+0,000.1";
        \Flexio\Tests\Check::assertString('N.14', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"0S,000.0")');
        $expected = "+0,000.1";
        \Flexio\Tests\Check::assertString('N.15', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1,"S0,000.0")');
        $expected = "+0,000.1";
        \Flexio\Tests\Check::assertString('N.16', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"0,000.0S")');
        $expected = "0,000.1-";
        \Flexio\Tests\Check::assertString('N.17', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"0,000.S0")');
        $expected = "0,000.1-";
        \Flexio\Tests\Check::assertString('N.18', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"0,000S.0")');
        $expected = "0,000.1-";
        \Flexio\Tests\Check::assertString('N.19', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"0,00S0.0")');
        $expected = "-0,000.1";
        \Flexio\Tests\Check::assertString('N.20', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"0,0S00.0")');
        $expected = "-0,000.1";
        \Flexio\Tests\Check::assertString('N.21', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"0,S000.0")');
        $expected = "-0,000.1";
        \Flexio\Tests\Check::assertString('N.22', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"0S,000.0")');
        $expected = "-0,000.1";
        \Flexio\Tests\Check::assertString('N.23', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1,"S0,000.0")');
        $expected = "-0,000.1";
        \Flexio\Tests\Check::assertString('N.24', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234.5,"0,000.0S")');
        $expected = "1,234.5+";
        \Flexio\Tests\Check::assertString('N.25', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234.5,"0,000.S0")');
        $expected = "1,234.5+";
        \Flexio\Tests\Check::assertString('N.26', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234.5,"0,000S.0")');
        $expected = "1,234.5+";
        \Flexio\Tests\Check::assertString('N.27', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234.5,"0,00S0.0")');
        $expected = "+1,234.5";
        \Flexio\Tests\Check::assertString('N.28', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234.5,"0,0S00.0")');
        $expected = "+1,234.5";
        \Flexio\Tests\Check::assertString('N.29', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234.5,"0,S000.0")');
        $expected = "+1,234.5";
        \Flexio\Tests\Check::assertString('N.30', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234.5,"0S,000.0")');
        $expected = "+1,234.5";
        \Flexio\Tests\Check::assertString('N.31', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1234.5,"S0,000.0")');
        $expected = "+1,234.5";
        \Flexio\Tests\Check::assertString('N.32', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234.5,"0,000.0S")');
        $expected = "1,234.5-";
        \Flexio\Tests\Check::assertString('N.33', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234.5,"0,000.S0")');
        $expected = "1,234.5-";
        \Flexio\Tests\Check::assertString('N.34', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234.5,"0,000S.0")');
        $expected = "1,234.5-";
        \Flexio\Tests\Check::assertString('N.35', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234.5,"0,00S0.0")');
        $expected = "-1,234.5";
        \Flexio\Tests\Check::assertString('N.36', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234.5,"0,0S00.0")');
        $expected = "-1,234.5";
        \Flexio\Tests\Check::assertString('N.37', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234.5,"0,S000.0")');
        $expected = "-1,234.5";
        \Flexio\Tests\Check::assertString('N.38', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234.5,"0S,000.0")');
        $expected = "-1,234.5";
        \Flexio\Tests\Check::assertString('N.39', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1234.5,"S0,000.0")');
        $expected = "-1,234.5";
        \Flexio\Tests\Check::assertString('N.40', 'Expression; to_char() conversion function; \'S\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'PR'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0.0PR")');
        $expected = " 0.0 ";
        \Flexio\Tests\Check::assertString('O.1', 'Expression; to_char() conversion function; \'PR\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"0PR0.0")');
        $expected = null;
        \Flexio\Tests\Check::assertNull('O.2', 'Expression; to_char() conversion function; \'PR\' format element needs to occur at the end of the format string',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"PR0.0")');
        $expected = null;
        \Flexio\Tests\Check::assertNull('O.3', 'Expression; to_char() conversion function; \'PR\' format element needs to occur at the end of the format string',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.2,"0.0PR")');
        $expected = " 1.2 ";
        \Flexio\Tests\Check::assertString('O.4', 'Expression; to_char() conversion function; \'PR\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.2,"0PR0.0")');
        $expected = null;
        \Flexio\Tests\Check::assertNull('O.5', 'Expression; to_char() conversion function; \'PR\' format element needs to occur at the end of the format string',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.2,"PR0.0")');
        $expected = null;
        \Flexio\Tests\Check::assertNull('O.6', 'Expression; to_char() conversion function; \'PR\' format element needs to occur at the end of the format string',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.2,"0.0PR")');
        $expected = "<1.2>";
        \Flexio\Tests\Check::assertString('O.7', 'Expression; to_char() conversion function; \'PR\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.2,"0PR0.0")');
        $expected = null;
        \Flexio\Tests\Check::assertNull('O.8', 'Expression; to_char() conversion function; \'PR\' format element needs to occur at the end of the format string',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.2,"PR0.0")');
        $expected = null;
        \Flexio\Tests\Check::assertNull('O.9', 'Expression; to_char() conversion function; \'PR\' format element needs to occur at the end of the format string',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);



        // TEST: text function: to_char(); format types with 'EEEE'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"EEEE")');
        $expected = " 0e+00";
        \Flexio\Tests\Check::assertString('P.1', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0,"EEEE")');
        $expected = " 0e+00";
        \Flexio\Tests\Check::assertString('P.2', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.14,"EEEE")');
        $expected = " 1e-01";
        \Flexio\Tests\Check::assertString('P.3', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.14,"EEEE")');
        $expected = "-1e-01";
        \Flexio\Tests\Check::assertString('P.4', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.15,"EEEE")');
        $expected = " 2e-01";
        \Flexio\Tests\Check::assertString('P.5', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.15,"EEEE")');
        $expected = "-2e-01";
        \Flexio\Tests\Check::assertString('P.6', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.4,"EEEE")');
        $expected = " 1e+00";
        \Flexio\Tests\Check::assertString('P.7', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.4,"EEEE")');
        $expected = "-1e+00";
        \Flexio\Tests\Check::assertString('P.8', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.5,"EEEE")');
        $expected = " 2e+00";
        \Flexio\Tests\Check::assertString('P.9', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.5,"EEEE")');
        $expected = "-2e+00";
        \Flexio\Tests\Check::assertString('P.10', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(14,"EEEE")');
        $expected = " 1e+01";
        \Flexio\Tests\Check::assertString('P.11', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-14,"EEEE")');
        $expected = "-1e+01";
        \Flexio\Tests\Check::assertString('P.12', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(15,"EEEE")');
        $expected = " 2e+01";
        \Flexio\Tests\Check::assertString('P.13', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-15,"EEEE")');
        $expected = "-2e+01";
        \Flexio\Tests\Check::assertString('P.14', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.14,".EEEE")');
        $expected = " 1e-01";
        \Flexio\Tests\Check::assertString('P.15', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.14,".EEEE")');
        $expected = "-1e-01";
        \Flexio\Tests\Check::assertString('P.16', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.15,".EEEE")');
        $expected = " 2e-01";
        \Flexio\Tests\Check::assertString('P.17', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.15,".EEEE")');
        $expected = "-2e-01";
        \Flexio\Tests\Check::assertString('P.18', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.14,".9EEEE")');
        $expected = " 1.4e-01";
        \Flexio\Tests\Check::assertString('P.19', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.14,".9EEEE")');
        $expected = "-1.4e-01";
        \Flexio\Tests\Check::assertString('P.20', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.15,".9EEEE")');
        $expected = " 1.5e-01";
        \Flexio\Tests\Check::assertString('P.21', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.15,".9EEEE")');
        $expected = "-1.5e-01";
        \Flexio\Tests\Check::assertString('P.22', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.14,"9.EEEE")');
        $expected = " 1e-01";
        \Flexio\Tests\Check::assertString('P.23', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.14,"9.EEEE")');
        $expected = "-1e-01";
        \Flexio\Tests\Check::assertString('P.24', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.15,"9.EEEE")');
        $expected = " 2e-01";
        \Flexio\Tests\Check::assertString('P.25', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.15,"9.EEEE")');
        $expected = "-2e-01";
        \Flexio\Tests\Check::assertString('P.26', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.14,"9.9EEEE")');
        $expected = " 1.4e-01";
        \Flexio\Tests\Check::assertString('P.27', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.14,"9.9EEEE")');
        $expected = "-1.4e-01";
        \Flexio\Tests\Check::assertString('P.28', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.15,"9.9EEEE")');
        $expected = " 1.5e-01";
        \Flexio\Tests\Check::assertString('P.29', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.15,"9.9EEEE")');
        $expected = "-1.5e-01";
        \Flexio\Tests\Check::assertString('P.30', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(123456,"9.99EEEE")');
        $expected = " 1.23e+05";
        \Flexio\Tests\Check::assertString('P.31', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-123456,"9.99EEEE")');
        $expected = "-1.23e+05";
        \Flexio\Tests\Check::assertString('P.32', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(123456,"9.99,99EEEE")');
        $expected = " 1.2346e+05";
        \Flexio\Tests\Check::assertString('P.33', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-123456,"9.99,99EEEE")');
        $expected = "-1.2346e+05";
        \Flexio\Tests\Check::assertString('P.34', 'Expression; to_char() conversion function; \'EEEE\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.15,"EEEE9.9")');
        $expected = null;
        \Flexio\Tests\Check::assertNull('P.35', 'Expression; to_char() conversion function; \'EEEE\' format element must be at the end of the format string',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.15,"EEEE9.9")');
        $expected = null;
        \Flexio\Tests\Check::assertNull('P.36', 'Expression; to_char() conversion function; \'EEEE\' format element must be at the end of the format string',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.15,"MI9.9EEEE")');
        $expected = null;
        \Flexio\Tests\Check::assertNull('P.37', 'Expression; to_char() conversion function; \'EEEE\' format element can only be used with decimal point patterns',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.15,"MI9.9EEEE")');
        $expected = null;
        \Flexio\Tests\Check::assertNull('P.38', 'Expression; to_char() conversion function; \'EEEE\' format element can only be used with decimal point patterns',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);



        // TEST: text function: to_char(); format types with 'RN'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1,"RN")');
        $expected = "###############";
        \Flexio\Tests\Check::assertString('Q.1', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"RN")');
        $expected = "###############";
        \Flexio\Tests\Check::assertString('Q.2', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"RN")');
        $expected = "              I";
        \Flexio\Tests\Check::assertString('Q.3', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.1,"RN")');
        $expected = "              I";
        \Flexio\Tests\Check::assertString('Q.4', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(3,"RN")');
        $expected = "            III";
        \Flexio\Tests\Check::assertString('Q.5', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(5,"RN")');
        $expected = "              V";
        \Flexio\Tests\Check::assertString('Q.6', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(6,"RN")');
        $expected = "             VI";
        \Flexio\Tests\Check::assertString('Q.7', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(9,"RN")');
        $expected = "             IX";
        \Flexio\Tests\Check::assertString('Q.8', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(48,"RN")');
        $expected = "         XLVIII";
        \Flexio\Tests\Check::assertString('Q.9', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(49,"RN")');
        $expected = "           XLIX";
        \Flexio\Tests\Check::assertString('Q.10', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(99,"RN")');
        $expected = "           XCIX";
        \Flexio\Tests\Check::assertString('Q.11', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(100,"RN")');
        $expected = "              C";
        \Flexio\Tests\Check::assertString('Q.12', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(101,"RN")');
        $expected = "             CI";
        \Flexio\Tests\Check::assertString('Q.13', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(450,"RN")');
        $expected = "            CDL";
        \Flexio\Tests\Check::assertString('Q.14', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(500,"RN")');
        $expected = "              D";
        \Flexio\Tests\Check::assertString('Q.15', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(750,"RN")');
        $expected = "           DCCL";
        \Flexio\Tests\Check::assertString('Q.16', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(900,"RN")');
        $expected = "             CM";
        \Flexio\Tests\Check::assertString('Q.17', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(999,"RN")');
        $expected = "         CMXCIX";
        \Flexio\Tests\Check::assertString('Q.18', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1000,"RN")');
        $expected = "              M";
        \Flexio\Tests\Check::assertString('Q.19', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(3888,"RN")');
        $expected = "MMMDCCCLXXXVIII";
        \Flexio\Tests\Check::assertString('Q.20', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(3889,"RN")');
        $expected = "  MMMDCCCLXXXIX";
        \Flexio\Tests\Check::assertString('Q.21', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(3999,"RN")');
        $expected = "      MMMCMXCIX";
        \Flexio\Tests\Check::assertString('Q.22', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(40000,"RN")');
        $expected = "###############";
        \Flexio\Tests\Check::assertString('Q.23', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"99.99RN")');
        $expected = "              I";
        \Flexio\Tests\Check::assertString('Q.24', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"RN99.99")');
        $expected = "              I";
        \Flexio\Tests\Check::assertString('Q.25', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"$RN")');
        $expected = "$              I";
        \Flexio\Tests\Check::assertString('Q.26', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"LRN")');
        $expected = "$              I";
        \Flexio\Tests\Check::assertString('Q.27', 'Expression; to_char() conversion function; \'RN\' format element; number must be an integer between 1 and 3999',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);



        // TEST: text function: to_char(); format types with 'FM'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"FM9.99")');
        $expected = ".00";
        \Flexio\Tests\Check::assertString('R.1', 'Expression; to_char() conversion function; \'FM\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(0,"FM999.99")');
        $expected = ".00";
        \Flexio\Tests\Check::assertString('R.2', 'Expression; to_char() conversion function; \'FM\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.01,"FM9,999.99")');
        $expected = "1.01";
        \Flexio\Tests\Check::assertString('R.3', 'Expression; to_char() conversion function; \'FM\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.01,"FM9,999.99")');
        $expected = "-1.01";
        \Flexio\Tests\Check::assertString('R.4', 'Expression; to_char() conversion function; \'FM\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.01,"FM9,999.99PL")');
        $expected = "1.01+";
        \Flexio\Tests\Check::assertString('R.5', 'Expression; to_char() conversion function; \'FM\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.01,"FM9,999.99PL")');
        $expected = "-1.01";
        \Flexio\Tests\Check::assertString('R.6', 'Expression; to_char() conversion function; \'FM\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.01,"FM9,999.99MI")');
        $expected = "1.01";
        \Flexio\Tests\Check::assertString('R.7', 'Expression; to_char() conversion function; \'FM\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.01,"FM9,999.99MI")');
        $expected = "1.01-";
        \Flexio\Tests\Check::assertString('R.8', 'Expression; to_char() conversion function; \'FM\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.01,"FMMI9,999.99")');
        $expected = "1.01";
        \Flexio\Tests\Check::assertString('R.9', 'Expression; to_char() conversion function; \'FM\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(-1.01,"FMMI9,999.99")');
        $expected = "-1.01";
        \Flexio\Tests\Check::assertString('R.10', 'Expression; to_char() conversion function; \'FM\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"FM0.00")');
        $expected = "1.00";
        \Flexio\Tests\Check::assertString('R.11', 'Expression; to_char() conversion function; \'FM\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"FM000")');
        $expected = "001";
        \Flexio\Tests\Check::assertString('R.12', 'Expression; to_char() conversion function; \'FM\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1.01,"FM0.00")');
        $expected = "1.01";
        \Flexio\Tests\Check::assertString('R.13', 'Expression; to_char() conversion function; \'FM\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"FMRN")');
        $expected = "I";
        \Flexio\Tests\Check::assertString('R.14', 'Expression; to_char() conversion function; \'FM\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"FMRN")');
        $expected = "I";
        \Flexio\Tests\Check::assertString('R.15', 'Expression; to_char() conversion function; \'FM\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(1,"FMEEEE")');
        $expected = "0e+00";
        \Flexio\Tests\Check::assertString('R.16', 'Expression; to_char() conversion function; \'FM\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);



        // TEST: text function: to_char(); combinations of numeric format parameters; tests based on postgres
        // to_char() examples here: https://wwX.postgresql.org/docs/9.5/static/functions-formatting.html

        $actual = \Flexio\Tests\Util::evalExpression('to_char(-0.1, "FM9.99")');
        $expected = "-.1";
        \Flexio\Tests\Check::assertString('S.1', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.1, "0.9")');
        $expected = " 0.1";
        \Flexio\Tests\Check::assertString('S.2', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(12, "9990999.9")');
        $expected = "    0012.0";
        \Flexio\Tests\Check::assertString('S.3', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(12, "FM9990999.9")');
        $expected = "0012.";
        \Flexio\Tests\Check::assertString('S.4', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(485, "999")');
        $expected = " 485";
        \Flexio\Tests\Check::assertString('S.5', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(-485, "999")');
        $expected = "-485";
        \Flexio\Tests\Check::assertString('S.6', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(485, "9 9 9")');
        $expected = " 4 8 5";
        \Flexio\Tests\Check::assertString('S.7', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(1485, "9,999")');
        $expected = " 1,485";
        \Flexio\Tests\Check::assertString('S.8', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(1485, "9G999")');
        $expected = " 1,485";
        \Flexio\Tests\Check::assertString('S.9', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(148.5, "999.999")');
        $expected = " 148.500";
        \Flexio\Tests\Check::assertString('S.10', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(148.5, "FM999.999")');
        $expected = "148.5";
        \Flexio\Tests\Check::assertString('S.11', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(148.5, "FM999.990")');
        $expected = "148.500";
        \Flexio\Tests\Check::assertString('S.12', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(148.5, "999D999")');
        $expected = " 148.500";
        \Flexio\Tests\Check::assertString('S.13', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(3148.5, "9G999D999")');
        $expected = " 3,148.500";
        \Flexio\Tests\Check::assertString('S.14', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(-485, "999S")');
        $expected = "485-";
        \Flexio\Tests\Check::assertString('S.15', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(-485, "999MI")');
        $expected = "485-";
        \Flexio\Tests\Check::assertString('S.16', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(485, "999MI")');
        $expected = "485 ";
        \Flexio\Tests\Check::assertString('S.17', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(485, "FM999MI")');
        $expected = "485";
        \Flexio\Tests\Check::assertString('S.18', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(485, "PL999")');
        $expected = "+ 485";
        \Flexio\Tests\Check::assertString('S.19', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(485, "SG999")');
        $expected = "+485";
        \Flexio\Tests\Check::assertString('S.20', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(-485, "SG999")');
        $expected = "-485";
        \Flexio\Tests\Check::assertString('S.21', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(-485, "9SG99")');
        $expected = "4-85";
        \Flexio\Tests\Check::assertString('S.22', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(-485, "999PR")');
        $expected = "<485>";
        \Flexio\Tests\Check::assertString('S.23', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(485, "L999")');
        $expected = "$ 485";
        \Flexio\Tests\Check::assertString('S.24', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(485, "RN")');
        $expected = "        CDLXXXV";
        \Flexio\Tests\Check::assertString('S.25', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(485, "FMRN")');
        $expected = "CDLXXXV";
        \Flexio\Tests\Check::assertString('S.26', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(5.2, "FMRN")');
        $expected = "V";
        \Flexio\Tests\Check::assertString('S.27', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(482, "999th")');
        $expected = " 482nd";
        \Flexio\Tests\Check::assertString('S.28', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(485, \'"Good number:"999\')');
        $expected = "Good number: 485";
        \Flexio\Tests\Check::assertString('S.29', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(485.8, \'"Pre:"999" Post:" .999\')');
        $expected = "Pre: 485 Post: .800";
        \Flexio\Tests\Check::assertString('S.30', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(12, "99V999")');
        $expected = " 12000";
        \Flexio\Tests\Check::assertString('S.31', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.4, "99V999")');
        $expected = " 12400";
        \Flexio\Tests\Check::assertString('S.32', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(12.45, "99V9")');
        $expected = " 125";
        \Flexio\Tests\Check::assertString('S.33', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(0.0004859, "9.99EEEE")');
        $expected = " 4.86e-04";
        \Flexio\Tests\Check::assertString('S.34', 'Expression; to_char() conversion function; combinations of numeric format parameters',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);



        // TEST: text function: to_char(); format types with 'YYYY'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp(""), "YYYY")');
        $expected = "";
        \Flexio\Tests\Check::assertString('T.1', 'Expression; to_char() conversion function; \'YYYY\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1460-01-01 00:00:01"), "YYYY")');
        $expected = "1460";
        \Flexio\Tests\Check::assertString('T.2', 'Expression; to_char() conversion function; \'YYYY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1509-02-02 00:01:02"), "YYYY")');
        $expected = "1509";
        \Flexio\Tests\Check::assertString('T.3', 'Expression; to_char() conversion function; \'YYYY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1558-03-03 01:02:03"), "YYYY")');
        $expected = "1558";
        \Flexio\Tests\Check::assertString('T.4', 'Expression; to_char() conversion function; \'YYYY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1607-04-05 02:03:05"), "YYYY")');
        $expected = "1607";
        \Flexio\Tests\Check::assertString('T.5', 'Expression; to_char() conversion function; \'YYYY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1656-05-10 03:05:10"), "YYYY")');
        $expected = "1656";
        \Flexio\Tests\Check::assertString('T.6', 'Expression; to_char() conversion function; \'YYYY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1705-06-14 05:10:11"), "YYYY")');
        $expected = "1705";
        \Flexio\Tests\Check::assertString('T.7', 'Expression; to_char() conversion function; \'YYYY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1754-07-15 10:11:12"), "YYYY")');
        $expected = "1754";
        \Flexio\Tests\Check::assertString('T.8', 'Expression; to_char() conversion function; \'YYYY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1803-08-16 11:12:13"), "YYYY")');
        $expected = "1803";
        \Flexio\Tests\Check::assertString('T.9', 'Expression; to_char() conversion function; \'YYYY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1852-09-20 12:13:15"), "YYYY")');
        $expected = "1852";
        \Flexio\Tests\Check::assertString('T.10', 'Expression; to_char() conversion function; \'YYYY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1901-10-25 13:15:23"), "YYYY")');
        $expected = "1901";
        \Flexio\Tests\Check::assertString('T.11', 'Expression; to_char() conversion function; \'YYYY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1950-11-30 15:23:59"), "YYYY")');
        $expected = "1950";
        \Flexio\Tests\Check::assertString('T.12', 'Expression; to_char() conversion function; \'YYYY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1999-12-31 23:59:59"), "YYYY")');
        $expected = "1999";
        \Flexio\Tests\Check::assertString('T.13', 'Expression; to_char() conversion function; \'YYYY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2048-01-01 00:00:00"), "YYYY")');
        $expected = "2048";
        \Flexio\Tests\Check::assertString('T.14', 'Expression; to_char() conversion function; \'YYYY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2097-02-02 12:00:00"), "YYYY")');
        $expected = "2097";
        \Flexio\Tests\Check::assertString('T.15', 'Expression; to_char() conversion function; \'YYYY\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'YY'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp(""), "YY")');
        $expected = null;
        \Flexio\Tests\Check::assertString('U.1', 'Expression; to_char() conversion function; \'YY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1460-01-01 00:00:01"), "YY")');
        $expected = "60";
        \Flexio\Tests\Check::assertString('U.2', 'Expression; to_char() conversion function; \'YY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1509-02-02 00:01:02"), "YY")');
        $expected = "09";
        \Flexio\Tests\Check::assertString('U.3', 'Expression; to_char() conversion function; \'YY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1558-03-03 01:02:03"), "YY")');
        $expected = "58";
        \Flexio\Tests\Check::assertString('U.4', 'Expression; to_char() conversion function; \'YY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1607-04-05 02:03:05"), "YY")');
        $expected = "07";
        \Flexio\Tests\Check::assertString('U.5', 'Expression; to_char() conversion function; \'YY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1656-05-10 03:05:10"), "YY")');
        $expected = "56";
        \Flexio\Tests\Check::assertString('U.6', 'Expression; to_char() conversion function; \'YY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1705-06-14 05:10:11"), "YY")');
        $expected = "05";
        \Flexio\Tests\Check::assertString('U.7', 'Expression; to_char() conversion function; \'YY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1754-07-15 10:11:12"), "YY")');
        $expected = "54";
        \Flexio\Tests\Check::assertString('U.8', 'Expression; to_char() conversion function; \'YY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1803-08-16 11:12:13"), "YY")');
        $expected = "03";
        \Flexio\Tests\Check::assertString('U.9', 'Expression; to_char() conversion function; \'YY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1852-09-20 12:13:15"), "YY")');
        $expected = "52";
        \Flexio\Tests\Check::assertString('U.10', 'Expression; to_char() conversion function; \'YY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1901-10-25 13:15:23"), "YY")');
        $expected = "01";
        \Flexio\Tests\Check::assertString('U.11', 'Expression; to_char() conversion function; \'YY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1950-11-30 15:23:59"), "YY")');
        $expected = "50";
        \Flexio\Tests\Check::assertString('U.12', 'Expression; to_char() conversion function; \'YY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1999-12-31 23:59:59"), "YY")');
        $expected = "99";
        \Flexio\Tests\Check::assertString('U.13', 'Expression; to_char() conversion function; \'YY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2048-01-01 00:00:00"), "YY")');
        $expected = "48";
        \Flexio\Tests\Check::assertString('U.14', 'Expression; to_char() conversion function; \'YY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2097-02-02 12:00:00"), "YY")');
        $expected = "97";
        \Flexio\Tests\Check::assertString('U.15', 'Expression; to_char() conversion function; \'YY\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'MM'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp(""), "MM")');
        $expected = null;
        \Flexio\Tests\Check::assertString('V.1', 'Expression; to_char() conversion function; \'MM\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1460-01-01 00:00:01"), "MM")');
        $expected = "01";
        \Flexio\Tests\Check::assertString('V.2', 'Expression; to_char() conversion function; \'MM\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1509-02-02 00:01:02"), "MM")');
        $expected = "02";
        \Flexio\Tests\Check::assertString('V.3', 'Expression; to_char() conversion function; \'MM\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1558-03-03 01:02:03"), "MM")');
        $expected = "03";
        \Flexio\Tests\Check::assertString('V.4', 'Expression; to_char() conversion function; \'MM\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1607-04-05 02:03:05"), "MM")');
        $expected = "04";
        \Flexio\Tests\Check::assertString('V.5', 'Expression; to_char() conversion function; \'MM\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1656-05-10 03:05:10"), "MM")');
        $expected = "05";
        \Flexio\Tests\Check::assertString('V.6', 'Expression; to_char() conversion function; \'MM\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1705-06-14 05:10:11"), "MM")');
        $expected = "06";
        \Flexio\Tests\Check::assertString('V.7', 'Expression; to_char() conversion function; \'MM\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1754-07-15 10:11:12"), "MM")');
        $expected = "07";
        \Flexio\Tests\Check::assertString('V.8', 'Expression; to_char() conversion function; \'MM\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1803-08-16 11:12:13"), "MM")');
        $expected = "08";
        \Flexio\Tests\Check::assertString('V.9', 'Expression; to_char() conversion function; \'MM\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1852-09-20 12:13:15"), "MM")');
        $expected = "09";
        \Flexio\Tests\Check::assertString('V.10', 'Expression; to_char() conversion function; \'MM\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1901-10-25 13:15:23"), "MM")');
        $expected = "10";
        \Flexio\Tests\Check::assertString('V.11', 'Expression; to_char() conversion function; \'MM\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1950-11-30 15:23:59"), "MM")');
        $expected = "11";
        \Flexio\Tests\Check::assertString('V.12', 'Expression; to_char() conversion function; \'MM\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1999-12-31 23:59:59"), "MM")');
        $expected = "12";
        \Flexio\Tests\Check::assertString('V.13', 'Expression; to_char() conversion function; \'MM\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2048-01-01 00:00:00"), "MM")');
        $expected = "01";
        \Flexio\Tests\Check::assertString('V.14', 'Expression; to_char() conversion function; \'MM\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2097-02-02 12:00:00"), "MM")');
        $expected = "02";
        \Flexio\Tests\Check::assertString('V.15', 'Expression; to_char() conversion function; \'MM\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'DD'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp(""), "DD")');
        $expected = null;
        \Flexio\Tests\Check::assertString('W.1', 'Expression; to_char() conversion function; \'DD\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1460-01-01 00:00:01"), "DD")');
        $expected = "01";
        \Flexio\Tests\Check::assertString('W.2', 'Expression; to_char() conversion function; \'DD\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1509-02-02 00:01:02"), "DD")');
        $expected = "02";
        \Flexio\Tests\Check::assertString('W.3', 'Expression; to_char() conversion function; \'DD\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1558-03-03 01:02:03"), "DD")');
        $expected = "03";
        \Flexio\Tests\Check::assertString('W.4', 'Expression; to_char() conversion function; \'DD\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1607-04-05 02:03:05"), "DD")');
        $expected = "05";
        \Flexio\Tests\Check::assertString('W.5', 'Expression; to_char() conversion function; \'DD\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1656-05-10 03:05:10"), "DD")');
        $expected = "10";
        \Flexio\Tests\Check::assertString('W.6', 'Expression; to_char() conversion function; \'DD\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1705-06-14 05:10:11"), "DD")');
        $expected = "14";
        \Flexio\Tests\Check::assertString('W.7', 'Expression; to_char() conversion function; \'DD\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1754-07-15 10:11:12"), "DD")');
        $expected = "15";
        \Flexio\Tests\Check::assertString('W.8', 'Expression; to_char() conversion function; \'DD\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1803-08-16 11:12:13"), "DD")');
        $expected = "16";
        \Flexio\Tests\Check::assertString('W.9', 'Expression; to_char() conversion function; \'DD\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1852-09-20 12:13:15"), "DD")');
        $expected = "20";
        \Flexio\Tests\Check::assertString('W.10', 'Expression; to_char() conversion function; \'DD\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1901-10-25 13:15:23"), "DD")');
        $expected = "25";
        \Flexio\Tests\Check::assertString('W.11', 'Expression; to_char() conversion function; \'DD\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1950-11-30 15:23:59"), "DD")');
        $expected = "30";
        \Flexio\Tests\Check::assertString('W.12', 'Expression; to_char() conversion function; \'DD\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1999-12-31 23:59:59"), "DD")');
        $expected = "31";
        \Flexio\Tests\Check::assertString('W.13', 'Expression; to_char() conversion function; \'DD\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2048-01-01 00:00:00"), "DD")');
        $expected = "01";
        \Flexio\Tests\Check::assertString('W.14', 'Expression; to_char() conversion function; \'DD\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2097-02-02 12:00:00"), "DD")');
        $expected = "02";
        \Flexio\Tests\Check::assertString('W.15', 'Expression; to_char() conversion function; \'DD\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'HH24'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp(""), "HH24")');
        $expected = null;
        \Flexio\Tests\Check::assertString('X.1', 'Expression; to_char() conversion function; \'HH24\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1460-01-01 00:00:01"), "HH24")');
        $expected = "00";
        \Flexio\Tests\Check::assertString('X.2', 'Expression; to_char() conversion function; \'HH24\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1509-02-02 00:01:02"), "HH24")');
        $expected = "00";
        \Flexio\Tests\Check::assertString('X.3', 'Expression; to_char() conversion function; \'HH24\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1558-03-03 01:02:03"), "HH24")');
        $expected = "01";
        \Flexio\Tests\Check::assertString('X.4', 'Expression; to_char() conversion function; \'HH24\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1607-04-05 02:03:05"), "HH24")');
        $expected = "02";
        \Flexio\Tests\Check::assertString('X.5', 'Expression; to_char() conversion function; \'HH24\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1656-05-10 03:05:10"), "HH24")');
        $expected = "03";
        \Flexio\Tests\Check::assertString('X.6', 'Expression; to_char() conversion function; \'HH24\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1705-06-14 05:10:11"), "HH24")');
        $expected = "05";
        \Flexio\Tests\Check::assertString('X.7', 'Expression; to_char() conversion function; \'HH24\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1754-07-15 10:11:12"), "HH24")');
        $expected = "10";
        \Flexio\Tests\Check::assertString('X.8', 'Expression; to_char() conversion function; \'HH24\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1803-08-16 11:12:13"), "HH24")');
        $expected = "11";
        \Flexio\Tests\Check::assertString('X.9', 'Expression; to_char() conversion function; \'HH24\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1852-09-20 12:13:15"), "HH24")');
        $expected = "12";
        \Flexio\Tests\Check::assertString('X.10', 'Expression; to_char() conversion function; \'HH24\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1901-10-25 13:15:23"), "HH24")');
        $expected = "13";
        \Flexio\Tests\Check::assertString('X.11', 'Expression; to_char() conversion function; \'HH24\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1950-11-30 15:23:59"), "HH24")');
        $expected = "15";
        \Flexio\Tests\Check::assertString('X.12', 'Expression; to_char() conversion function; \'HH24\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1999-12-31 23:59:59"), "HH24")');
        $expected = "23";
        \Flexio\Tests\Check::assertString('X.13', 'Expression; to_char() conversion function; \'HH24\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2048-01-01 00:00:00"), "HH24")');
        $expected = "00";
        \Flexio\Tests\Check::assertString('X.14', 'Expression; to_char() conversion function; \'HH24\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2097-02-02 12:00:00"), "HH24")');
        $expected = "12";
        \Flexio\Tests\Check::assertString('X.15', 'Expression; to_char() conversion function; \'HH24\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'HH12'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp(""), "HH12")');
        $expected = null;
        \Flexio\Tests\Check::assertString('Y.1', 'Expression; to_char() conversion function; \'HH12\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1460-01-01 00:00:01"), "HH12")');
        $expected = "00";
        \Flexio\Tests\Check::assertString('Y.2', 'Expression; to_char() conversion function; \'HH12\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1509-02-02 00:01:02"), "HH12")');
        $expected = "00";
        \Flexio\Tests\Check::assertString('Y.3', 'Expression; to_char() conversion function; \'HH12\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1558-03-03 01:02:03"), "HH12")');
        $expected = "01";
        \Flexio\Tests\Check::assertString('Y.4', 'Expression; to_char() conversion function; \'HH12\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1607-04-05 02:03:05"), "HH12")');
        $expected = "02";
        \Flexio\Tests\Check::assertString('Y.5', 'Expression; to_char() conversion function; \'HH12\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1656-05-10 03:05:10"), "HH12")');
        $expected = "03";
        \Flexio\Tests\Check::assertString('Y.6', 'Expression; to_char() conversion function; \'HH12\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1705-06-14 05:10:11"), "HH12")');
        $expected = "05";
        \Flexio\Tests\Check::assertString('Y.7', 'Expression; to_char() conversion function; \'HH12\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1754-07-15 10:11:12"), "HH12")');
        $expected = "10";
        \Flexio\Tests\Check::assertString('Y.8', 'Expression; to_char() conversion function; \'HH12\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1803-08-16 11:12:13"), "HH12")');
        $expected = "11";
        \Flexio\Tests\Check::assertString('Y.9', 'Expression; to_char() conversion function; \'HH12\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1852-09-20 12:13:15"), "HH12")');
        $expected = "12";
        \Flexio\Tests\Check::assertString('Y.10', 'Expression; to_char() conversion function; \'HH12\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1901-10-25 13:15:23"), "HH12")');
        $expected = "01";
        \Flexio\Tests\Check::assertString('Y.11', 'Expression; to_char() conversion function; \'HH12\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1950-11-30 15:23:59"), "HH12")');
        $expected = "03";
        \Flexio\Tests\Check::assertString('Y.12', 'Expression; to_char() conversion function; \'HH12\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1999-12-31 23:59:59"), "HH12")');
        $expected = "11";
        \Flexio\Tests\Check::assertString('Y.13', 'Expression; to_char() conversion function; \'HH12\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2048-01-01 00:00:00"), "HH12")');
        $expected = "00";
        \Flexio\Tests\Check::assertString('Y.14', 'Expression; to_char() conversion function; \'HH12\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2097-02-02 12:00:00"), "HH12")');
        $expected = "12";
        \Flexio\Tests\Check::assertString('Y.15', 'Expression; to_char() conversion function; \'HH12\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'HH'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp(""), "HH")');
        $expected = null;
        \Flexio\Tests\Check::assertString('Z.1', 'Expression; to_char() conversion function; \'HH\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1460-01-01 00:00:01"), "HH")');
        $expected = "00";
        \Flexio\Tests\Check::assertString('Z.2', 'Expression; to_char() conversion function; \'HH\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1509-02-02 00:01:02"), "HH")');
        $expected = "00";
        \Flexio\Tests\Check::assertString('Z.3', 'Expression; to_char() conversion function; \'HH\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1558-03-03 01:02:03"), "HH")');
        $expected = "01";
        \Flexio\Tests\Check::assertString('Z.4', 'Expression; to_char() conversion function; \'HH\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1607-04-05 02:03:05"), "HH")');
        $expected = "02";
        \Flexio\Tests\Check::assertString('Z.5', 'Expression; to_char() conversion function; \'HH\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1656-05-10 03:05:10"), "HH")');
        $expected = "03";
        \Flexio\Tests\Check::assertString('Z.6', 'Expression; to_char() conversion function; \'HH\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1705-06-14 05:10:11"), "HH")');
        $expected = "05";
        \Flexio\Tests\Check::assertString('Z.7', 'Expression; to_char() conversion function; \'HH\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1754-07-15 10:11:12"), "HH")');
        $expected = "10";
        \Flexio\Tests\Check::assertString('Z.8', 'Expression; to_char() conversion function; \'HH\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1803-08-16 11:12:13"), "HH")');
        $expected = "11";
        \Flexio\Tests\Check::assertString('Z.9', 'Expression; to_char() conversion function; \'HH\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1852-09-20 12:13:15"), "HH")');
        $expected = "12";
        \Flexio\Tests\Check::assertString('Z.10', 'Expression; to_char() conversion function; \'HH\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1901-10-25 13:15:23"), "HH")');
        $expected = "01";
        \Flexio\Tests\Check::assertString('Z.11', 'Expression; to_char() conversion function; \'HH\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1950-11-30 15:23:59"), "HH")');
        $expected = "03";
        \Flexio\Tests\Check::assertString('Z.12', 'Expression; to_char() conversion function; \'HH\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1999-12-31 23:59:59"), "HH")');
        $expected = "11";
        \Flexio\Tests\Check::assertString('Z.13', 'Expression; to_char() conversion function; \'HH\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2048-01-01 00:00:00"), "HH")');
        $expected = "00";
        \Flexio\Tests\Check::assertString('Z.14', 'Expression; to_char() conversion function; \'HH\' format element',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2097-02-02 12:00:00"), "HH")');
        $expected = "12";
        \Flexio\Tests\Check::assertString('Z.15', 'Expression; to_char() conversion function; \'HH\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'MI'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp(""), "MI")');
        $expected = null;
        \Flexio\Tests\Check::assertString('AA.1', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1460-01-01 00:00:01"), "MI")');
        $expected = "00";
        \Flexio\Tests\Check::assertString('AA.2', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1509-02-02 00:01:02"), "MI")');
        $expected = "01";
        \Flexio\Tests\Check::assertString('AA.3', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1558-03-03 01:02:03"), "MI")');
        $expected = "02";
        \Flexio\Tests\Check::assertString('AA.4', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1607-04-05 02:03:05"), "MI")');
        $expected = "03";
        \Flexio\Tests\Check::assertString('AA.5', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1656-05-10 03:05:10"), "MI")');
        $expected = "05";
        \Flexio\Tests\Check::assertString('AA.6', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1705-06-14 05:10:11"), "MI")');
        $expected = "10";
        \Flexio\Tests\Check::assertString('AA.7', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1754-07-15 10:11:12"), "MI")');
        $expected = "11";
        \Flexio\Tests\Check::assertString('AA.8', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1803-08-16 11:12:13"), "MI")');
        $expected = "12";
        \Flexio\Tests\Check::assertString('AA.9', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1852-09-20 12:13:15"), "MI")');
        $expected = "13";
        \Flexio\Tests\Check::assertString('AA.10', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1901-10-25 13:15:23"), "MI")');
        $expected = "15";
        \Flexio\Tests\Check::assertString('AA.11', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1950-11-30 15:23:59"), "MI")');
        $expected = "23";
        \Flexio\Tests\Check::assertString('AA.12', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1999-12-31 23:59:59"), "MI")');
        $expected = "59";
        \Flexio\Tests\Check::assertString('AA.13', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2048-01-01 00:00:00"), "MI")');
        $expected = "00";
        \Flexio\Tests\Check::assertString('AA.14', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2097-02-02 12:00:00"), "MI")');
        $expected = "00";
        \Flexio\Tests\Check::assertString('AA.15', 'Expression; to_char() conversion function; \'MI\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'SS'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp(""), "SS")');
        $expected = null;
        \Flexio\Tests\Check::assertString('AB.1', 'Expression; to_char() conversion function; \'SS\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1460-01-01 00:00:01"), "SS")');
        $expected = "01";
        \Flexio\Tests\Check::assertString('AB.2', 'Expression; to_char() conversion function; \'SS\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1509-02-02 00:01:02"), "SS")');
        $expected = "02";
        \Flexio\Tests\Check::assertString('AB.3', 'Expression; to_char() conversion function; \'SS\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1558-03-03 01:02:03"), "SS")');
        $expected = "03";
        \Flexio\Tests\Check::assertString('AB.4', 'Expression; to_char() conversion function; \'SS\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1607-04-05 02:03:05"), "SS")');
        $expected = "05";
        \Flexio\Tests\Check::assertString('AB.5', 'Expression; to_char() conversion function; \'SS\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1656-05-10 03:05:10"), "SS")');
        $expected = "10";
        \Flexio\Tests\Check::assertString('AB.6', 'Expression; to_char() conversion function; \'SS\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1705-06-14 05:10:11"), "SS")');
        $expected = "11";
        \Flexio\Tests\Check::assertString('AB.7', 'Expression; to_char() conversion function; \'SS\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1754-07-15 10:11:12"), "SS")');
        $expected = "12";
        \Flexio\Tests\Check::assertString('AB.8', 'Expression; to_char() conversion function; \'SS\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1803-08-16 11:12:13"), "SS")');
        $expected = "13";
        \Flexio\Tests\Check::assertString('AB.9', 'Expression; to_char() conversion function; \'SS\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1852-09-20 12:13:15"), "SS")');
        $expected = "15";
        \Flexio\Tests\Check::assertString('AB.10', 'Expression; to_char() conversion function; \'SS\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1901-10-25 13:15:23"), "SS")');
        $expected = "23";
        \Flexio\Tests\Check::assertString('AB.11', 'Expression; to_char() conversion function; \'SS\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1950-11-30 15:23:59"), "SS")');
        $expected = "59";
        \Flexio\Tests\Check::assertString('AB.12', 'Expression; to_char() conversion function; \'SS\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1999-12-31 23:59:59"), "SS")');
        $expected = "59";
        \Flexio\Tests\Check::assertString('AB.13', 'Expression; to_char() conversion function; \'SS\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2048-01-01 00:00:00"), "SS")');
        $expected = "00";
        \Flexio\Tests\Check::assertString('AB.14', 'Expression; to_char() conversion function; \'SS\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2097-02-02 12:00:00"), "SS")');
        $expected = "00";
        \Flexio\Tests\Check::assertString('AB.15', 'Expression; to_char() conversion function; \'SS\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'MONTH'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp(""), "MONTH")');
        $expected = null;
        \Flexio\Tests\Check::assertString('AC.1', 'Expression; to_char() conversion function; \'MONTH\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1460-01-01 00:00:01"), "MONTH")');
        $expected = "JANUARY  ";
        \Flexio\Tests\Check::assertString('AC.2', 'Expression; to_char() conversion function; \'MONTH\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1509-02-02 00:01:02"), "MONTH")');
        $expected = "FEBRUARY ";
        \Flexio\Tests\Check::assertString('AC.3', 'Expression; to_char() conversion function; \'MONTH\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1558-03-03 01:02:03"), "MONTH")');
        $expected = "MARCH    ";
        \Flexio\Tests\Check::assertString('AC.4', 'Expression; to_char() conversion function; \'MONTH\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1607-04-05 02:03:05"), "MONTH")');
        $expected = "APRIL    ";
        \Flexio\Tests\Check::assertString('AC.5', 'Expression; to_char() conversion function; \'MONTH\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1656-05-10 03:05:10"), "MONTH")');
        $expected = "MAY      ";
        \Flexio\Tests\Check::assertString('AC.6', 'Expression; to_char() conversion function; \'MONTH\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1705-06-14 05:10:11"), "MONTH")');
        $expected = "JUNE     ";
        \Flexio\Tests\Check::assertString('AC.7', 'Expression; to_char() conversion function; \'MONTH\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1754-07-15 10:11:12"), "MONTH")');
        $expected = "JULY     ";
        \Flexio\Tests\Check::assertString('AC.8', 'Expression; to_char() conversion function; \'MONTH\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1803-08-16 11:12:13"), "MONTH")');
        $expected = "AUGUST   ";
        \Flexio\Tests\Check::assertString('AC.9', 'Expression; to_char() conversion function; \'MONTH\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1852-09-20 12:13:15"), "MONTH")');
        $expected = "SEPTEMBER";
        \Flexio\Tests\Check::assertString('AC.10', 'Expression; to_char() conversion function; \'MONTH\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1901-10-25 13:15:23"), "MONTH")');
        $expected = "OCTOBER  ";
        \Flexio\Tests\Check::assertString('AC.11', 'Expression; to_char() conversion function; \'MONTH\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1950-11-30 15:23:59"), "MONTH")');
        $expected = "NOVEMBER ";
        \Flexio\Tests\Check::assertString('AC.12', 'Expression; to_char() conversion function; \'MONTH\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1999-12-31 23:59:59"), "MONTH")');
        $expected = "DECEMBER ";
        \Flexio\Tests\Check::assertString('AC.13', 'Expression; to_char() conversion function; \'MONTH\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2048-01-01 00:00:00"), "MONTH")');
        $expected = "JANUARY  ";
        \Flexio\Tests\Check::assertString('AC.14', 'Expression; to_char() conversion function; \'MONTH\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2097-02-02 12:00:00"), "MONTH")');
        $expected = "FEBRUARY ";
        \Flexio\Tests\Check::assertString('AC.15', 'Expression; to_char() conversion function; \'MONTH\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'Month'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp(""), "Month")');
        $expected = null;
        \Flexio\Tests\Check::assertString('AD.1', 'Expression; to_char() conversion function; \'Month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1460-01-01 00:00:01"), "Month")');
        $expected = "January  ";
        \Flexio\Tests\Check::assertString('AD.2', 'Expression; to_char() conversion function; \'Month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1509-02-02 00:01:02"), "Month")');
        $expected = "February ";
        \Flexio\Tests\Check::assertString('AD.3', 'Expression; to_char() conversion function; \'Month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1558-03-03 01:02:03"), "Month")');
        $expected = "March    ";
        \Flexio\Tests\Check::assertString('AD.4', 'Expression; to_char() conversion function; \'Month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1607-04-05 02:03:05"), "Month")');
        $expected = "April    ";
        \Flexio\Tests\Check::assertString('AD.5', 'Expression; to_char() conversion function; \'Month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1656-05-10 03:05:10"), "Month")');
        $expected = "May      ";
        \Flexio\Tests\Check::assertString('AD.6', 'Expression; to_char() conversion function; \'Month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1705-06-14 05:10:11"), "Month")');
        $expected = "June     ";
        \Flexio\Tests\Check::assertString('AD.7', 'Expression; to_char() conversion function; \'Month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1754-07-15 10:11:12"), "Month")');
        $expected = "July     ";
        \Flexio\Tests\Check::assertString('AD.8', 'Expression; to_char() conversion function; \'Month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1803-08-16 11:12:13"), "Month")');
        $expected = "August   ";
        \Flexio\Tests\Check::assertString('AD.9', 'Expression; to_char() conversion function; \'Month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1852-09-20 12:13:15"), "Month")');
        $expected = "September";
        \Flexio\Tests\Check::assertString('AD.10', 'Expression; to_char() conversion function; \'Month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1901-10-25 13:15:23"), "Month")');
        $expected = "October  ";
        \Flexio\Tests\Check::assertString('AD.11', 'Expression; to_char() conversion function; \'Month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1950-11-30 15:23:59"), "Month")');
        $expected = "November ";
        \Flexio\Tests\Check::assertString('AD.12', 'Expression; to_char() conversion function; \'Month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1999-12-31 23:59:59"), "Month")');
        $expected = "December ";
        \Flexio\Tests\Check::assertString('AD.13', 'Expression; to_char() conversion function; \'Month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2048-01-01 00:00:00"), "Month")');
        $expected = "January  ";
        \Flexio\Tests\Check::assertString('AD.14', 'Expression; to_char() conversion function; \'Month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2097-02-02 12:00:00"), "Month")');
        $expected = "February ";
        \Flexio\Tests\Check::assertString('AD.15', 'Expression; to_char() conversion function; \'Month\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'month'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp(""), "month")');
        $expected = null;
        \Flexio\Tests\Check::assertString('AE.1', 'Expression; to_char() conversion function; \'month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1460-01-01 00:00:01"), "month")');
        $expected = "january  ";
        \Flexio\Tests\Check::assertString('AE.2', 'Expression; to_char() conversion function; \'month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1509-02-02 00:01:02"), "month")');
        $expected = "february ";
        \Flexio\Tests\Check::assertString('AE.3', 'Expression; to_char() conversion function; \'month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1558-03-03 01:02:03"), "month")');
        $expected = "march    ";
        \Flexio\Tests\Check::assertString('AE.4', 'Expression; to_char() conversion function; \'month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1607-04-05 02:03:05"), "month")');
        $expected = "april    ";
        \Flexio\Tests\Check::assertString('AE.5', 'Expression; to_char() conversion function; \'month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1656-05-10 03:05:10"), "month")');
        $expected = "may      ";
        \Flexio\Tests\Check::assertString('AE.6', 'Expression; to_char() conversion function; \'month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1705-06-14 05:10:11"), "month")');
        $expected = "june     ";
        \Flexio\Tests\Check::assertString('AE.7', 'Expression; to_char() conversion function; \'month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1754-07-15 10:11:12"), "month")');
        $expected = "july     ";
        \Flexio\Tests\Check::assertString('AE.8', 'Expression; to_char() conversion function; \'month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1803-08-16 11:12:13"), "month")');
        $expected = "august   ";
        \Flexio\Tests\Check::assertString('AE.9', 'Expression; to_char() conversion function; \'month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1852-09-20 12:13:15"), "month")');
        $expected = "september";
        \Flexio\Tests\Check::assertString('AE.10', 'Expression; to_char() conversion function; \'month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1901-10-25 13:15:23"), "month")');
        $expected = "october  ";
        \Flexio\Tests\Check::assertString('AE.11', 'Expression; to_char() conversion function; \'month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1950-11-30 15:23:59"), "month")');
        $expected = "november ";
        \Flexio\Tests\Check::assertString('AE.12', 'Expression; to_char() conversion function; \'month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1999-12-31 23:59:59"), "month")');
        $expected = "december ";
        \Flexio\Tests\Check::assertString('AE.13', 'Expression; to_char() conversion function; \'month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2048-01-01 00:00:00"), "month")');
        $expected = "january  ";
        \Flexio\Tests\Check::assertString('AE.14', 'Expression; to_char() conversion function; \'month\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2097-02-02 12:00:00"), "month")');
        $expected = "february ";
        \Flexio\Tests\Check::assertString('AE.15', 'Expression; to_char() conversion function; \'month\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'MON'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp(""), "MON")');
        $expected = null;
        \Flexio\Tests\Check::assertString('AF.1', 'Expression; to_char() conversion function; \'MON\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1460-01-01 00:00:01"), "MON")');
        $expected = "JAN";
        \Flexio\Tests\Check::assertString('AF.2', 'Expression; to_char() conversion function; \'MON\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1509-02-02 00:01:02"), "MON")');
        $expected = "FEB";
        \Flexio\Tests\Check::assertString('AF.3', 'Expression; to_char() conversion function; \'MON\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1558-03-03 01:02:03"), "MON")');
        $expected = "MAR";
        \Flexio\Tests\Check::assertString('AF.4', 'Expression; to_char() conversion function; \'MON\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1607-04-05 02:03:05"), "MON")');
        $expected = "APR";
        \Flexio\Tests\Check::assertString('AF.5', 'Expression; to_char() conversion function; \'MON\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1656-05-10 03:05:10"), "MON")');
        $expected = "MAY";
        \Flexio\Tests\Check::assertString('AF.6', 'Expression; to_char() conversion function; \'MON\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1705-06-14 05:10:11"), "MON")');
        $expected = "JUN";
        \Flexio\Tests\Check::assertString('AF.7', 'Expression; to_char() conversion function; \'MON\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1754-07-15 10:11:12"), "MON")');
        $expected = "JUL";
        \Flexio\Tests\Check::assertString('AF.8', 'Expression; to_char() conversion function; \'MON\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1803-08-16 11:12:13"), "MON")');
        $expected = "AUG";
        \Flexio\Tests\Check::assertString('AF.9', 'Expression; to_char() conversion function; \'MON\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1852-09-20 12:13:15"), "MON")');
        $expected = "SEP";
        \Flexio\Tests\Check::assertString('AF.10', 'Expression; to_char() conversion function; \'MON\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1901-10-25 13:15:23"), "MON")');
        $expected = "OCT";
        \Flexio\Tests\Check::assertString('AF.11', 'Expression; to_char() conversion function; \'MON\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1950-11-30 15:23:59"), "MON")');
        $expected = "NOV";
        \Flexio\Tests\Check::assertString('AF.12', 'Expression; to_char() conversion function; \'MON\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1999-12-31 23:59:59"), "MON")');
        $expected = "DEC";
        \Flexio\Tests\Check::assertString('AF.13', 'Expression; to_char() conversion function; \'MON\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2048-01-01 00:00:00"), "MON")');
        $expected = "JAN";
        \Flexio\Tests\Check::assertString('AF.14', 'Expression; to_char() conversion function; \'MON\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2097-02-02 12:00:00"), "MON")');
        $expected = "FEB";
        \Flexio\Tests\Check::assertString('AF.15', 'Expression; to_char() conversion function; \'MON\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'Mon'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp(""), "Mon")');
        $expected = null;
        \Flexio\Tests\Check::assertString('AG.1', 'Expression; to_char() conversion function; \'Mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1460-01-01 00:00:01"), "Mon")');
        $expected = "Jan";
        \Flexio\Tests\Check::assertString('AG.2', 'Expression; to_char() conversion function; \'Mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1509-02-02 00:01:02"), "Mon")');
        $expected = "Feb";
        \Flexio\Tests\Check::assertString('AG.3', 'Expression; to_char() conversion function; \'Mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1558-03-03 01:02:03"), "Mon")');
        $expected = "Mar";
        \Flexio\Tests\Check::assertString('AG.4', 'Expression; to_char() conversion function; \'Mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1607-04-05 02:03:05"), "Mon")');
        $expected = "Apr";
        \Flexio\Tests\Check::assertString('AG.5', 'Expression; to_char() conversion function; \'Mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1656-05-10 03:05:10"), "Mon")');
        $expected = "May";
        \Flexio\Tests\Check::assertString('AG.6', 'Expression; to_char() conversion function; \'Mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1705-06-14 05:10:11"), "Mon")');
        $expected = "Jun";
        \Flexio\Tests\Check::assertString('AG.7', 'Expression; to_char() conversion function; \'Mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1754-07-15 10:11:12"), "Mon")');
        $expected = "Jul";
        \Flexio\Tests\Check::assertString('AG.8', 'Expression; to_char() conversion function; \'Mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1803-08-16 11:12:13"), "Mon")');
        $expected = "Aug";
        \Flexio\Tests\Check::assertString('AG.9', 'Expression; to_char() conversion function; \'Mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1852-09-20 12:13:15"), "Mon")');
        $expected = "Sep";
        \Flexio\Tests\Check::assertString('AG.10', 'Expression; to_char() conversion function; \'Mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1901-10-25 13:15:23"), "Mon")');
        $expected = "Oct";
        \Flexio\Tests\Check::assertString('AG.11', 'Expression; to_char() conversion function; \'Mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1950-11-30 15:23:59"), "Mon")');
        $expected = "Nov";
        \Flexio\Tests\Check::assertString('AG.12', 'Expression; to_char() conversion function; \'Mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1999-12-31 23:59:59"), "Mon")');
        $expected = "Dec";
        \Flexio\Tests\Check::assertString('AG.13', 'Expression; to_char() conversion function; \'Mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2048-01-01 00:00:00"), "Mon")');
        $expected = "Jan";
        \Flexio\Tests\Check::assertString('AG.14', 'Expression; to_char() conversion function; \'Mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2097-02-02 12:00:00"), "Mon")');
        $expected = "Feb";
        \Flexio\Tests\Check::assertString('AG.15', 'Expression; to_char() conversion function; \'Mon\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'mon'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp(""), "mon")');
        $expected = null;
        \Flexio\Tests\Check::assertString('AH.1', 'Expression; to_char() conversion function; \'mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1460-01-01 00:00:01"), "mon")');
        $expected = "jan";
        \Flexio\Tests\Check::assertString('AH.2', 'Expression; to_char() conversion function; \'mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1509-02-02 00:01:02"), "mon")');
        $expected = "feb";
        \Flexio\Tests\Check::assertString('AH.3', 'Expression; to_char() conversion function; \'mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1558-03-03 01:02:03"), "mon")');
        $expected = "mar";
        \Flexio\Tests\Check::assertString('AH.4', 'Expression; to_char() conversion function; \'mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1607-04-05 02:03:05"), "mon")');
        $expected = "apr";
        \Flexio\Tests\Check::assertString('AH.5', 'Expression; to_char() conversion function; \'mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1656-05-10 03:05:10"), "mon")');
        $expected = "may";
        \Flexio\Tests\Check::assertString('AH.6', 'Expression; to_char() conversion function; \'mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1705-06-14 05:10:11"), "mon")');
        $expected = "jun";
        \Flexio\Tests\Check::assertString('AH.7', 'Expression; to_char() conversion function; \'mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1754-07-15 10:11:12"), "mon")');
        $expected = "jul";
        \Flexio\Tests\Check::assertString('AH.8', 'Expression; to_char() conversion function; \'mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1803-08-16 11:12:13"), "mon")');
        $expected = "aug";
        \Flexio\Tests\Check::assertString('AH.9', 'Expression; to_char() conversion function; \'mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1852-09-20 12:13:15"), "mon")');
        $expected = "sep";
        \Flexio\Tests\Check::assertString('AH.10', 'Expression; to_char() conversion function; \'mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1901-10-25 13:15:23"), "mon")');
        $expected = "oct";
        \Flexio\Tests\Check::assertString('AH.11', 'Expression; to_char() conversion function; \'mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1950-11-30 15:23:59"), "mon")');
        $expected = "nov";
        \Flexio\Tests\Check::assertString('AH.12', 'Expression; to_char() conversion function; \'mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1999-12-31 23:59:59"), "mon")');
        $expected = "dec";
        \Flexio\Tests\Check::assertString('AH.13', 'Expression; to_char() conversion function; \'mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2048-01-01 00:00:00"), "mon")');
        $expected = "jan";
        \Flexio\Tests\Check::assertString('AH.14', 'Expression; to_char() conversion function; \'mon\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2097-02-02 12:00:00"), "mon")');
        $expected = "feb";
        \Flexio\Tests\Check::assertString('AH.15', 'Expression; to_char() conversion function; \'mon\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'DAY'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp(""), "DAY")');
        $expected = null;
        \Flexio\Tests\Check::assertString('AI.1', 'Expression; to_char() conversion function; \'DAY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1460-01-01 00:00:01"), "DAY")');
        $expected = "SUNDAY   ";
        \Flexio\Tests\Check::assertString('AI.2', 'Expression; to_char() conversion function; \'DAY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1509-02-02 00:01:02"), "DAY")');
        $expected = "TUESDAY  ";
        \Flexio\Tests\Check::assertString('AI.3', 'Expression; to_char() conversion function; \'DAY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1558-03-03 01:02:03"), "DAY")');
        $expected = "MONDAY   ";
        \Flexio\Tests\Check::assertString('AI.4', 'Expression; to_char() conversion function; \'DAY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1607-04-05 02:03:05"), "DAY")');
        $expected = "THURSDAY ";
        \Flexio\Tests\Check::assertString('AI.5', 'Expression; to_char() conversion function; \'DAY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1656-05-10 03:05:10"), "DAY")');
        $expected = "WEDNESDAY";
        \Flexio\Tests\Check::assertString('AI.6', 'Expression; to_char() conversion function; \'DAY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1705-06-14 05:10:11"), "DAY")');
        $expected = "SUNDAY   ";
        \Flexio\Tests\Check::assertString('AI.7', 'Expression; to_char() conversion function; \'DAY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1754-07-15 10:11:12"), "DAY")');
        $expected = "MONDAY   ";
        \Flexio\Tests\Check::assertString('AI.8', 'Expression; to_char() conversion function; \'DAY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1803-08-16 11:12:13"), "DAY")');
        $expected = "TUESDAY  ";
        \Flexio\Tests\Check::assertString('AI.9', 'Expression; to_char() conversion function; \'DAY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1852-09-20 12:13:15"), "DAY")');
        $expected = "MONDAY   ";
        \Flexio\Tests\Check::assertString('AI.10', 'Expression; to_char() conversion function; \'DAY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1901-10-25 13:15:23"), "DAY")');
        $expected = "FRIDAY   ";
        \Flexio\Tests\Check::assertString('AI.11', 'Expression; to_char() conversion function; \'DAY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1950-11-30 15:23:59"), "DAY")');
        $expected = "THURSDAY ";
        \Flexio\Tests\Check::assertString('AI.12', 'Expression; to_char() conversion function; \'DAY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1999-12-31 23:59:59"), "DAY")');
        $expected = "FRIDAY   ";
        \Flexio\Tests\Check::assertString('AI.13', 'Expression; to_char() conversion function; \'DAY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2048-01-01 00:00:00"), "DAY")');
        $expected = "WEDNESDAY";
        \Flexio\Tests\Check::assertString('AI.14', 'Expression; to_char() conversion function; \'DAY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2097-02-02 12:00:00"), "DAY")');
        $expected = "SATURDAY ";
        \Flexio\Tests\Check::assertString('AI.15', 'Expression; to_char() conversion function; \'DAY\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'Day'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp(""), "Day")');
        $expected = null;
        \Flexio\Tests\Check::assertString('AJ.1', 'Expression; to_char() conversion function; \'Day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1460-01-01 00:00:01"), "Day")');
        $expected = "Sunday   ";
        \Flexio\Tests\Check::assertString('AJ.2', 'Expression; to_char() conversion function; \'Day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1509-02-02 00:01:02"), "Day")');
        $expected = "Tuesday  ";
        \Flexio\Tests\Check::assertString('AJ.3', 'Expression; to_char() conversion function; \'Day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1558-03-03 01:02:03"), "Day")');
        $expected = "Monday   ";
        \Flexio\Tests\Check::assertString('AJ.4', 'Expression; to_char() conversion function; \'Day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1607-04-05 02:03:05"), "Day")');
        $expected = "Thursday ";
        \Flexio\Tests\Check::assertString('AJ.5', 'Expression; to_char() conversion function; \'Day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1656-05-10 03:05:10"), "Day")');
        $expected = "Wednesday";
        \Flexio\Tests\Check::assertString('AJ.6', 'Expression; to_char() conversion function; \'Day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1705-06-14 05:10:11"), "Day")');
        $expected = "Sunday   ";
        \Flexio\Tests\Check::assertString('AJ.7', 'Expression; to_char() conversion function; \'Day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1754-07-15 10:11:12"), "Day")');
        $expected = "Monday   ";
        \Flexio\Tests\Check::assertString('AJ.8', 'Expression; to_char() conversion function; \'Day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1803-08-16 11:12:13"), "Day")');
        $expected = "Tuesday  ";
        \Flexio\Tests\Check::assertString('AJ.9', 'Expression; to_char() conversion function; \'Day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1852-09-20 12:13:15"), "Day")');
        $expected = "Monday   ";
        \Flexio\Tests\Check::assertString('AJ.10', 'Expression; to_char() conversion function; \'Day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1901-10-25 13:15:23"), "Day")');
        $expected = "Friday   ";
        \Flexio\Tests\Check::assertString('AJ.11', 'Expression; to_char() conversion function; \'Day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1950-11-30 15:23:59"), "Day")');
        $expected = "Thursday ";
        \Flexio\Tests\Check::assertString('AJ.12', 'Expression; to_char() conversion function; \'Day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1999-12-31 23:59:59"), "Day")');
        $expected = "Friday   ";
        \Flexio\Tests\Check::assertString('AJ.13', 'Expression; to_char() conversion function; \'Day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2048-01-01 00:00:00"), "Day")');
        $expected = "Wednesday";
        \Flexio\Tests\Check::assertString('AJ.14', 'Expression; to_char() conversion function; \'Day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2097-02-02 12:00:00"), "Day")');
        $expected = "Saturday ";
        \Flexio\Tests\Check::assertString('AJ.15', 'Expression; to_char() conversion function; \'Day\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'day'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp(""), "day")');
        $expected = null;
        \Flexio\Tests\Check::assertString('AK.1', 'Expression; to_char() conversion function; \'day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1460-01-01 00:00:01"), "day")');
        $expected = "sunday   ";
        \Flexio\Tests\Check::assertString('AK.2', 'Expression; to_char() conversion function; \'day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1509-02-02 00:01:02"), "day")');
        $expected = "tuesday  ";
        \Flexio\Tests\Check::assertString('AK.3', 'Expression; to_char() conversion function; \'day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1558-03-03 01:02:03"), "day")');
        $expected = "monday   ";
        \Flexio\Tests\Check::assertString('AK.4', 'Expression; to_char() conversion function; \'day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1607-04-05 02:03:05"), "day")');
        $expected = "thursday ";
        \Flexio\Tests\Check::assertString('AK.5', 'Expression; to_char() conversion function; \'day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1656-05-10 03:05:10"), "day")');
        $expected = "wednesday";
        \Flexio\Tests\Check::assertString('AK.6', 'Expression; to_char() conversion function; \'day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1705-06-14 05:10:11"), "day")');
        $expected = "sunday   ";
        \Flexio\Tests\Check::assertString('AK.7', 'Expression; to_char() conversion function; \'day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1754-07-15 10:11:12"), "day")');
        $expected = "monday   ";
        \Flexio\Tests\Check::assertString('AK.8', 'Expression; to_char() conversion function; \'day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1803-08-16 11:12:13"), "day")');
        $expected = "tuesday  ";
        \Flexio\Tests\Check::assertString('AK.9', 'Expression; to_char() conversion function; \'day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1852-09-20 12:13:15"), "day")');
        $expected = "monday   ";
        \Flexio\Tests\Check::assertString('AK.10', 'Expression; to_char() conversion function; \'day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1901-10-25 13:15:23"), "day")');
        $expected = "friday   ";
        \Flexio\Tests\Check::assertString('AK.11', 'Expression; to_char() conversion function; \'day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1950-11-30 15:23:59"), "day")');
        $expected = "thursday ";
        \Flexio\Tests\Check::assertString('AK.12', 'Expression; to_char() conversion function; \'day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1999-12-31 23:59:59"), "day")');
        $expected = "friday   ";
        \Flexio\Tests\Check::assertString('AK.13', 'Expression; to_char() conversion function; \'day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2048-01-01 00:00:00"), "day")');
        $expected = "wednesday";
        \Flexio\Tests\Check::assertString('AK.14', 'Expression; to_char() conversion function; \'day\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2097-02-02 12:00:00"), "day")');
        $expected = "saturday ";
        \Flexio\Tests\Check::assertString('AK.15', 'Expression; to_char() conversion function; \'day\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'DY'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp(""), "DY")');
        $expected = null;
        \Flexio\Tests\Check::assertString('AL.1', 'Expression; to_char() conversion function; \'DY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1460-01-01 00:00:01"), "DY")');
        $expected = "SUN";
        \Flexio\Tests\Check::assertString('AL.2', 'Expression; to_char() conversion function; \'DY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1509-02-02 00:01:02"), "DY")');
        $expected = "TUE";
        \Flexio\Tests\Check::assertString('AL.3', 'Expression; to_char() conversion function; \'DY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1558-03-03 01:02:03"), "DY")');
        $expected = "MON";
        \Flexio\Tests\Check::assertString('AL.4', 'Expression; to_char() conversion function; \'DY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1607-04-05 02:03:05"), "DY")');
        $expected = "THU";
        \Flexio\Tests\Check::assertString('AL.5', 'Expression; to_char() conversion function; \'DY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1656-05-10 03:05:10"), "DY")');
        $expected = "WED";
        \Flexio\Tests\Check::assertString('AL.6', 'Expression; to_char() conversion function; \'DY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1705-06-14 05:10:11"), "DY")');
        $expected = "SUN";
        \Flexio\Tests\Check::assertString('AL.7', 'Expression; to_char() conversion function; \'DY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1754-07-15 10:11:12"), "DY")');
        $expected = "MON";
        \Flexio\Tests\Check::assertString('AL.8', 'Expression; to_char() conversion function; \'DY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1803-08-16 11:12:13"), "DY")');
        $expected = "TUE";
        \Flexio\Tests\Check::assertString('AL.9', 'Expression; to_char() conversion function; \'DY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1852-09-20 12:13:15"), "DY")');
        $expected = "MON";
        \Flexio\Tests\Check::assertString('AL.10', 'Expression; to_char() conversion function; \'DY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1901-10-25 13:15:23"), "DY")');
        $expected = "FRI";
        \Flexio\Tests\Check::assertString('AL.11', 'Expression; to_char() conversion function; \'DY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1950-11-30 15:23:59"), "DY")');
        $expected = "THU";
        \Flexio\Tests\Check::assertString('AL.12', 'Expression; to_char() conversion function; \'DY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1999-12-31 23:59:59"), "DY")');
        $expected = "FRI";
        \Flexio\Tests\Check::assertString('AL.13', 'Expression; to_char() conversion function; \'DY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2048-01-01 00:00:00"), "DY")');
        $expected = "WED";
        \Flexio\Tests\Check::assertString('AL.14', 'Expression; to_char() conversion function; \'DY\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2097-02-02 12:00:00"), "DY")');
        $expected = "SAT";
        \Flexio\Tests\Check::assertString('AL.15', 'Expression; to_char() conversion function; \'DY\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'Dy'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp(""), "Dy")');
        $expected = null;
        \Flexio\Tests\Check::assertString('AM.1', 'Expression; to_char() conversion function; \'Dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1460-01-01 00:00:01"), "Dy")');
        $expected = "Sun";
        \Flexio\Tests\Check::assertString('AM.2', 'Expression; to_char() conversion function; \'Dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1509-02-02 00:01:02"), "Dy")');
        $expected = "Tue";
        \Flexio\Tests\Check::assertString('AM.3', 'Expression; to_char() conversion function; \'Dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1558-03-03 01:02:03"), "Dy")');
        $expected = "Mon";
        \Flexio\Tests\Check::assertString('AM.4', 'Expression; to_char() conversion function; \'Dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1607-04-05 02:03:05"), "Dy")');
        $expected = "Thu";
        \Flexio\Tests\Check::assertString('AM.5', 'Expression; to_char() conversion function; \'Dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1656-05-10 03:05:10"), "Dy")');
        $expected = "Wed";
        \Flexio\Tests\Check::assertString('AM.6', 'Expression; to_char() conversion function; \'Dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1705-06-14 05:10:11"), "Dy")');
        $expected = "Sun";
        \Flexio\Tests\Check::assertString('AM.7', 'Expression; to_char() conversion function; \'Dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1754-07-15 10:11:12"), "Dy")');
        $expected = "Mon";
        \Flexio\Tests\Check::assertString('AM.8', 'Expression; to_char() conversion function; \'Dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1803-08-16 11:12:13"), "Dy")');
        $expected = "Tue";
        \Flexio\Tests\Check::assertString('AM.9', 'Expression; to_char() conversion function; \'Dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1852-09-20 12:13:15"), "Dy")');
        $expected = "Mon";
        \Flexio\Tests\Check::assertString('AM.10', 'Expression; to_char() conversion function; \'Dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1901-10-25 13:15:23"), "Dy")');
        $expected = "Fri";
        \Flexio\Tests\Check::assertString('AM.11', 'Expression; to_char() conversion function; \'Dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1950-11-30 15:23:59"), "Dy")');
        $expected = "Thu";
        \Flexio\Tests\Check::assertString('AM.12', 'Expression; to_char() conversion function; \'Dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1999-12-31 23:59:59"), "Dy")');
        $expected = "Fri";
        \Flexio\Tests\Check::assertString('AM.13', 'Expression; to_char() conversion function; \'Dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2048-01-01 00:00:00"), "Dy")');
        $expected = "Wed";
        \Flexio\Tests\Check::assertString('AM.14', 'Expression; to_char() conversion function; \'Dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2097-02-02 12:00:00"), "Dy")');
        $expected = "Sat";
        \Flexio\Tests\Check::assertString('AM.15', 'Expression; to_char() conversion function; \'Dy\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'dy'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp(""), "dy")');
        $expected = null;
        \Flexio\Tests\Check::assertString('AN.1', 'Expression; to_char() conversion function; \'dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1460-01-01 00:00:01"), "dy")');
        $expected = "sun";
        \Flexio\Tests\Check::assertString('AN.2', 'Expression; to_char() conversion function; \'dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1509-02-02 00:01:02"), "dy")');
        $expected = "tue";
        \Flexio\Tests\Check::assertString('AN.3', 'Expression; to_char() conversion function; \'dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1558-03-03 01:02:03"), "dy")');
        $expected = "mon";
        \Flexio\Tests\Check::assertString('AN.4', 'Expression; to_char() conversion function; \'dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1607-04-05 02:03:05"), "dy")');
        $expected = "thu";
        \Flexio\Tests\Check::assertString('AN.5', 'Expression; to_char() conversion function; \'dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1656-05-10 03:05:10"), "dy")');
        $expected = "wed";
        \Flexio\Tests\Check::assertString('AN.6', 'Expression; to_char() conversion function; \'dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1705-06-14 05:10:11"), "dy")');
        $expected = "sun";
        \Flexio\Tests\Check::assertString('AN.7', 'Expression; to_char() conversion function; \'dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1754-07-15 10:11:12"), "dy")');
        $expected = "mon";
        \Flexio\Tests\Check::assertString('AN.8', 'Expression; to_char() conversion function; \'dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1803-08-16 11:12:13"), "dy")');
        $expected = "tue";
        \Flexio\Tests\Check::assertString('AN.9', 'Expression; to_char() conversion function; \'dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1852-09-20 12:13:15"), "dy")');
        $expected = "mon";
        \Flexio\Tests\Check::assertString('AN.10', 'Expression; to_char() conversion function; \'dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1901-10-25 13:15:23"), "dy")');
        $expected = "fri";
        \Flexio\Tests\Check::assertString('AN.11', 'Expression; to_char() conversion function; \'dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1950-11-30 15:23:59"), "dy")');
        $expected = "thu";
        \Flexio\Tests\Check::assertString('AN.12', 'Expression; to_char() conversion function; \'dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1999-12-31 23:59:59"), "dy")');
        $expected = "fri";
        \Flexio\Tests\Check::assertString('AN.13', 'Expression; to_char() conversion function; \'dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2048-01-01 00:00:00"), "dy")');
        $expected = "wed";
        \Flexio\Tests\Check::assertString('AN.14', 'Expression; to_char() conversion function; \'dy\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2097-02-02 12:00:00"), "dy")');
        $expected = "sat";
        \Flexio\Tests\Check::assertString('AN.15', 'Expression; to_char() conversion function; \'dy\' format element',  $actual, $expected, $results);



        // TEST: text function: to_char(); format types with 'D'

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp(""), "D")');
        $expected = null;
        \Flexio\Tests\Check::assertString('AO.1', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1460-01-01 00:00:01"), "D")');
        $expected = "1";
        \Flexio\Tests\Check::assertString('AO.2', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1509-02-02 00:01:02"), "D")');
        $expected = "3";
        \Flexio\Tests\Check::assertString('AO.3', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1558-03-03 01:02:03"), "D")');
        $expected = "2";
        \Flexio\Tests\Check::assertString('AO.4', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1607-04-05 02:03:05"), "D")');
        $expected = "5";
        \Flexio\Tests\Check::assertString('AO.5', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1656-05-10 03:05:10"), "D")');
        $expected = "4";
        \Flexio\Tests\Check::assertString('AO.6', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1705-06-14 05:10:11"), "D")');
        $expected = "1";
        \Flexio\Tests\Check::assertString('AO.7', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1754-07-15 10:11:12"), "D")');
        $expected = "2";
        \Flexio\Tests\Check::assertString('AO.8', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1803-08-16 11:12:13"), "D")');
        $expected = "3";
        \Flexio\Tests\Check::assertString('AO.9', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1852-09-20 12:13:15"), "D")');
        $expected = "2";
        \Flexio\Tests\Check::assertString('AO.10', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1901-10-25 13:15:23"), "D")');
        $expected = "6";
        \Flexio\Tests\Check::assertString('AO.11', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1950-11-30 15:23:59"), "D")');
        $expected = "5";
        \Flexio\Tests\Check::assertString('AO.12', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("1999-12-31 23:59:59"), "D")');
        $expected = "6";
        \Flexio\Tests\Check::assertString('AO.13', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2048-01-01 00:00:00"), "D")');
        $expected = "4";
        \Flexio\Tests\Check::assertString('AO.14', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2097-02-02 12:00:00"), "D")');
        $expected = "7";
        \Flexio\Tests\Check::assertString('AO.15', 'Expression; to_char() conversion function; \'D\' format element',  $actual, $expected, $results);



        // TEST: text function: to_date()

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_date()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('AX.1', 'Expression; to_date() conversion function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_date("2001-01-01","","")');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('AX.2', 'Expression; to_date() conversion function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_date("2001-02-03"))');
        $expected = "2001-02-03";
        \Flexio\Tests\Check::assertString('AX.3', 'Expression; to_date() conversion function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_date("1999-12-11","YYYY/MM/DD"))');
        $expected = "1999-12-11";
        \Flexio\Tests\Check::assertString('AX.4', 'Expression; to_date() conversion function; with format; tolerance of differing separator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_date("1999-12-11","YYYY.MM.DD"))');
        $expected = "1999-12-11";
        \Flexio\Tests\Check::assertString('AX.5', 'Expression; to_date() conversion function; with format; tolerance of differing separator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_date("19991211","YYYYMMDD"))');
        $expected = "1999-12-11";
        \Flexio\Tests\Check::assertString('AX.6', 'Expression; to_date() conversion function; with format; no separators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_date("12/11/1999","MM/DD/YYYY"))');
        $expected = "1999-12-11";
        \Flexio\Tests\Check::assertString('AX.7', 'Expression; to_date() conversion function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_date("12/11/1999","DD/MM/YYYY"))');
        $expected = "1999-11-12";
        \Flexio\Tests\Check::assertString('AX.8', 'Expression; to_date() conversion function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_date("12/11/1999 23:11:38","DD/MM/YYYY HH24:MM:SS"))');
        $expected = "1999-11-12";
        \Flexio\Tests\Check::assertString('AX.9', 'Expression; to_date() conversion function; ignore time portion',  $actual, $expected, $results);

        // TEST: text function: to_datetime()

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_datetime()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('AAA.1', 'Expression; to_datetime() conversion function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_datetime("2001-01-01","","")');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('AAA.2', 'Expression; to_datetime() conversion function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_datetime("2001-02-03"))');
        $expected = "2001-02-03 00:00:00+00";
        \Flexio\Tests\Check::assertString('AAA.3', 'Expression; to_datetime() conversion function; support conversion of a string to a timestamp without a format',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_datetime("2001-02-03 04:05:06"))');
        $expected = "2001-02-03 04:05:06+00";
        \Flexio\Tests\Check::assertString('AAA.4', 'Expression; to_datetime() conversion function; support conversion of a string to a timestamp without a format',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_datetime("2001-02-03 04:05:06","YYYY-MM-DD HH:MI:SS"))');
        $expected = "2001-02-03 04:05:06+00";
        \Flexio\Tests\Check::assertString('AAA.5', 'Expression; to_datetime() conversion function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_datetime("2001-02-03 23:05:06","YYYY-MM-DD HH24:MI:SS"))');
        $expected = "2001-02-03 23:05:06+00";
        \Flexio\Tests\Check::assertString('AAA.6', 'Expression; to_datetime() conversion function',  $actual, $expected, $results);


        // TEST: text function: to_number()

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_number()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('AAB.1', 'Expression; to_number() conversion function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_number("1","","")');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('AAB.2', 'Expression; to_number() conversion function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_number("1")');
        $expected = (float)1;
        \Flexio\Tests\Check::assertNumber('AAB.3', 'Expression; support conversion of a string to a number without a format',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_number("-1.23")');
        $expected = -1.23;
        \Flexio\Tests\Check::assertNumber('AAB.4', 'Expression; support conversion of a string to a number without a format',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_number("-12,345.67","S99,999.99")');
        $expected = -12345.67;
        \Flexio\Tests\Check::assertNumber('AAB.5', 'Expression; to_number() conversion function',  $actual, $expected, $results);



        // TEST: text function: to_timestamp()

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_timestamp()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('AAC.1', 'Expression; to_timestamp() conversion function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_timestamp("2001-01-01","","")');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('AAC.2', 'Expression; to_timestamp() conversion function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2001-02-03"))');
        $expected = "2001-02-03 00:00:00+00";
        \Flexio\Tests\Check::assertString('AAC.3', 'Expression; to_timestamp() conversion function; support conversion of a string to a timestamp without a format',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2001-02-03 04:05:06"))');
        $expected = "2001-02-03 04:05:06+00";
        \Flexio\Tests\Check::assertString('AAC.4', 'Expression; to_timestamp() conversion function; support conversion of a string to a timestamp without a format',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("2001-02-03 04:05:06","YYYY-MM-DD HH:MI:SS"))');
        $expected = "2001-02-03 04:05:06+00";
        \Flexio\Tests\Check::assertString('AAC.5', 'Expression; to_timestamp() conversion function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("03-2001-02 06:05:04","DD-YYYY-MM SS:MI:HH24"))');
        $expected = "2001-02-03 04:05:06+00";
        \Flexio\Tests\Check::assertString('AAC.6', 'Expression; to_timestamp() conversion function; non-standard order with format',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("20010203040506","YYYYMMDDHH24MISS"))');
        $expected = "2001-02-03 04:05:06+00";
        \Flexio\Tests\Check::assertString('AAC.7', 'Expression; to_timestamp() conversion function; no separators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('to_char(to_timestamp("20010203230506","YYYYMMDDHHMISS"))');
        $expected = "2001-02-03 23:05:06+00";
        \Flexio\Tests\Check::assertString('AAC.8', 'Expression; to_timestamp() conversion function; HH24 hour specified with HH specifier',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);


        // TEST: cast to text: cast(various, text)

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('cast(null,text)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('AB.1', 'Expression; cast() conversion function; cast null to text',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('cast("abc",text)');
        $expected = 'abc';
        \Flexio\Tests\Check::assertString('AB.2', 'Expression; cast() conversion function; cast string to text',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('cast(123,text)');
        $expected = "123";
        \Flexio\Tests\Check::assertString('AB.3', 'Expression; cast() conversion function; cast int to text',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('cast(-123.45,text)');
        $expected = "-123.45";
        \Flexio\Tests\Check::assertString('AB.4', 'Expression; cast() conversion function; cast float to text',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('cast(true,text)');
        $expected = "true";
        \Flexio\Tests\Check::assertString('AB.5', 'Expression; cast() conversion function; cast bool (true) to text',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('cast(false,text)');
        $expected = "false";
        \Flexio\Tests\Check::assertString('AB.6', 'Expression; cast() conversion function; cast bool (false) to text',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('cast(to_date("2001-02-03"),text)');
        $expected = "2001-02-03";
        \Flexio\Tests\Check::assertString('AB.7', 'Expression; cast() conversion function; cast date to text',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('cast(to_timestamp("2001-02-03 04:05:06"),text)');
        $expected = "2001-02-03 04:05:06+00";
        \Flexio\Tests\Check::assertString('AB.8', 'Expression; cast() conversion function; cast timestamp to text',  $actual, $expected, $results);


        // TEST: cast to numeric: cast(various, numeric)

        $actual = \Flexio\Tests\Util::evalExpression('cast(null,numeric)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('AC.1', 'Expression; cast() conversion function; cast null to numeric',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('cast("-123.49",numeric)');
        $expected = -123.49;
        \Flexio\Tests\Check::assertNumber('AC.2', 'Expression; cast() conversion function; cast string to numeric',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('cast("-123.49",numeric(10,1))');
        $expected = -123.5;
        \Flexio\Tests\Check::assertNumber('AC.3', 'Expression; cast() conversion function; cast string to numeric',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('cast(true,numeric)');
        $expected = (float)1;
        \Flexio\Tests\Check::assertNumber('AC.4', 'Expression; cast() conversion function; cast bool (true) to numeric',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('cast(false,numeric)');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('AC.5', 'Expression; cast() conversion function; cast bool (false) to numeric',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('cast(to_date("2001-02-03"),numeric)');
        $expected = (float)20010203;
        \Flexio\Tests\Check::assertNumber('AC.6', 'Expression; cast() conversion function; cast date to numeric',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('cast(to_timestamp("2001-02-03 04:05:06"),numeric)');
        $expected = (float)20010203040506;
        \Flexio\Tests\Check::assertNumber('AC.7', 'Expression; cast() conversion function; cast timestamp to numeric',  $actual, $expected, $results);


        // TEST: cast to double: cast(various, double)

        $actual = \Flexio\Tests\Util::evalExpression('cast(null,double)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('AD.1', 'Expression; cast() conversion function; cast null to double',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('cast("-123.49",double)');
        $expected = (float)-123.49;
        \Flexio\Tests\Check::assertNumber('AD.2', 'Expression; cast() conversion function; cast string to double',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('cast(true,double)');
        $expected = (float)1;
        \Flexio\Tests\Check::assertNumber('AD.4', 'Expression; cast() conversion function; cast bool (true) to double',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('cast(false,double)');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('AD.5', 'Expression; cast() conversion function; cast bool (false) to double',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('cast(to_date("2001-02-03"),double)');
        $expected = (float)20010203;
        \Flexio\Tests\Check::assertNumber('AD.6', 'Expression; cast() conversion function; cast date to double',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('cast(to_timestamp("2001-02-03 04:05:06"),double)');
        $expected = (float)20010203040506;
        \Flexio\Tests\Check::assertNumber('AD.7', 'Expression; cast() conversion function; cast timestamp to double',  $actual, $expected, $results);



        // TEST: cast to integer: cast(various, integer)

        $actual = \Flexio\Tests\Util::evalExpression('cast(null,integer)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('AE.1', 'Expression; cast() conversion function; cast null to integer',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('cast("-123.49",integer)');
        $expected = (float)-123;
        \Flexio\Tests\Check::assertNumber('AE.2', 'Expression; cast() conversion function; cast string to integer',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('cast(true,integer)');
        $expected = 1;
        \Flexio\Tests\Check::assertNumber('AE.4', 'Expression; cast() conversion function; cast bool (true) to integer',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('cast(false,integer)');
        $expected = 0;
        \Flexio\Tests\Check::assertNumber('AE.5', 'Expression; cast() conversion function; cast bool (false) to integer',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('cast(to_date("2001-02-03"),integer)');
        $expected = (float)20010203;
        \Flexio\Tests\Check::assertNumber('AE.6', 'Expression; cast() conversion function; cast date to integer',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('cast(to_timestamp("2001-02-03 04:05:06"),integer)');
        $expected = (float)20010203040506;
        \Flexio\Tests\Check::assertNumber('AE.7', 'Expression; cast() conversion function; cast timestamp to integer',  $actual, $expected, $results);



        // TEST: cast to date: cast(various, date)

        $actual = \Flexio\Tests\Util::evalExpression('cast(null,datetime)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('AF.1', 'Expression; cast() conversion function; cast null to date',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(cast("2015-02-03 23:11:05",datetime))');
        $expected = "2015-02-03 23:11:05+00";
        \Flexio\Tests\Check::assertString('AF.2', 'Expression; cast() conversion function; cast string to date',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('cast(true,datetime)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('AF.3', 'Expression; cast() conversion function; cast null to date',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('cast(false,datetime)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('AF.4', 'Expression; cast() conversion function; cast null to date',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('to_char(cast(20010203231105,datetime))');
        $expected = "2001-02-03 23:11:05+00";
        \Flexio\Tests\Check::assertString('AF.5', 'Expression; cast() conversion function; cast date to integer',  $actual, $expected, $results);

    }
}
