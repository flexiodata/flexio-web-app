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


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TODO: add additional validation tests for:
        // \Flexio\System\Util::isValidUrl()
        // \Flexio\System\Util::isDateTime()


        // TEST: validation tests for isPositiveInteger()

        // BEGIN TEST
        $actual = \Flexio\System\Util::isPositiveInteger(null);
        $expected = false;
        TestCheck::assertBoolean('A.1', '\Flexio\System\Util::isPositiveInteger() test for non-numeric input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isPositiveInteger('a');
        $expected = false;
        TestCheck::assertBoolean('A.2', '\Flexio\System\Util::isPositiveInteger() test for non-numeric input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isPositiveInteger('1');
        $expected = true;
        TestCheck::assertBoolean('A.3', '\Flexio\System\Util::isPositiveInteger() test for non-numeric input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isPositiveInteger(-1.0);
        $expected = false;
        TestCheck::assertBoolean('A.4', '\Flexio\System\Util::isPositiveInteger() test for non-integer input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isPositiveInteger(-0.1);
        $expected = false;
        TestCheck::assertBoolean('A.5', '\Flexio\System\Util::isPositiveInteger() test for non-integer input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isPositiveInteger(0.0);
        $expected = false;
        TestCheck::assertBoolean('A.6', '\Flexio\System\Util::isPositiveInteger() test for non-integer input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isPositiveInteger(0.1);
        $expected = false;
        TestCheck::assertBoolean('A.7', '\Flexio\System\Util::isPositiveInteger() test for non-integer input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isPositiveInteger(1.0);
        $expected = true;
        TestCheck::assertBoolean('A.8', '\Flexio\System\Util::isPositiveInteger() test for non-integer input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isPositiveInteger(-1);
        $expected = false;
        TestCheck::assertBoolean('A.9', '\Flexio\System\Util::isPositiveInteger() test for integer input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isPositiveInteger(0);
        $expected = false;
        TestCheck::assertBoolean('A.10', '\Flexio\System\Util::isPositiveInteger() test for integer input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isPositiveInteger(1);
        $expected = true;
        TestCheck::assertBoolean('A.11', '\Flexio\System\Util::isPositiveInteger() test for integer input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isPositiveInteger(9999999);
        $expected = true;
        TestCheck::assertBoolean('A.12', '\Flexio\System\Util::isPositiveInteger() test for integer input',  $actual, $expected, $results);



        // TEST: validation tests for isValidDateTime()

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidDateTime(null);
        $expected = false;
        TestCheck::assertBoolean('B.1', '\Flexio\System\Util::isValidDateTime() test for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidDateTime(true);
        $expected = false;
        TestCheck::assertBoolean('B.2', '\Flexio\System\Util::isValidDateTime() test for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidDateTime('');
        $expected = false;
        TestCheck::assertBoolean('B.3', '\Flexio\System\Util::isValidDateTime() test for string input; return false if not a valid ISO8601 datetime format',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidDateTime("2015");
        $expected = false;
        TestCheck::assertBoolean('B.4', '\Flexio\System\Util::isValidDateTime() test for string input; return false if not a valid ISO8601 datetime format',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidDateTime("2015-01");
        $expected = false;
        TestCheck::assertBoolean('B.5', '\Flexio\System\Util::isValidDateTime() test for string input; return false if not a valid ISO8601 datetime format',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidDateTime("2015-01-01");
        $expected = false;
        TestCheck::assertBoolean('B.6', '\Flexio\System\Util::isValidDateTime() test for string input; return false if not a valid ISO8601 datetime format',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidDateTime("2015-01-01 01");
        $expected = false;
        TestCheck::assertBoolean('B.7', '\Flexio\System\Util::isValidDateTime() test for string input; return false if not a valid ISO8601 datetime format',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidDateTime("2015-01-01 01:01");
        $expected = false;
        TestCheck::assertBoolean('B.8', '\Flexio\System\Util::isValidDateTime() test for string input; return false if not a valid ISO8601 datetime format',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidDateTime("2015-01-01 01:01:01");
        $expected = false;
        TestCheck::assertBoolean('B.9', '\Flexio\System\Util::isValidDateTime() test for string input; return false if not a valid ISO8601 datetime format',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidDateTime("2015/01/01T01:01:01+0000");
        $expected = false;
        TestCheck::assertBoolean('B.10', '\Flexio\System\Util::isValidDateTime() test for string input; return false if not a valid ISO8601 datetime format',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidDateTime("2015-01-01T01:01:01+0000");
        $expected = true;
        TestCheck::assertBoolean('B.11', '\Flexio\System\Util::isValidDateTime() test for string input; return false if not a valid ISO8601 datetime format',  $actual, $expected, $results);



        // TEST: validation tests for isValidEmail()

        // note: some test examples from here and here:
        // http://blogs.msdn.com/b/testing123/archive/2009/02/05/email-address-test-cases.aspx

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail(null);
        $expected = false;
        TestCheck::assertBoolean('C.1', '\Flexio\System\Util::isValidEmail() test for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail(true);
        $expected = false;
        TestCheck::assertBoolean('C.2', '\Flexio\System\Util::isValidEmail() test for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail(' email@domain.com');
        $expected = false;
        TestCheck::assertBoolean('C.3', '\Flexio\System\Util::isValidEmail() test for string input; return for invalid email; leading space not allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('email@domain.com ');
        $expected = false;
        TestCheck::assertBoolean('C.4', '\Flexio\System\Util::isValidEmail() test for string input; return true for valid email; trailing space not allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('email@domain.com');
        $expected = true;
        TestCheck::assertBoolean('C.5', '\Flexio\System\Util::isValidEmail() test for string input; return true for valid email',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('firstname.lastname@domain.com');
        $expected = true;
        TestCheck::assertBoolean('C.6', '\Flexio\System\Util::isValidEmail() test for string input; return true for valid email; period in the address field is allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('"firstnamelastname"@domain.com');
        $expected = true;
        TestCheck::assertBoolean('C.7', '\Flexio\System\Util::isValidEmail() test for string input; return true for valid email; quotes in the address field are allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('firstname-lastname@domain.com');
        $expected = true;
        TestCheck::assertBoolean('C.8', '\Flexio\System\Util::isValidEmail() test for string input; return true for valid email; dash in address field is allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('firstname+lastname@domain.com');
        $expected = true;
        TestCheck::assertBoolean('C.9', '\Flexio\System\Util::isValidEmail() test for string input; return true for valid email; plus sign in address field is allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('firstname_lastname@domain.com');
        $expected = true;
        TestCheck::assertBoolean('C.10', '\Flexio\System\Util::isValidEmail() test for string input; return true for valid email; period in the address field is allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('_@domain.com');
        $expected = true;
        TestCheck::assertBoolean('C.11', '\Flexio\System\Util::isValidEmail() test for string input; return true for valid email; underscore in the address field is allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('name1@domain.com');
        $expected = true;
        TestCheck::assertBoolean('C.12', '\Flexio\System\Util::isValidEmail() test for string input; return true for valid email; digits in address are allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('1234567890@domain.com');
        $expected = true;
        TestCheck::assertBoolean('C.13', '\Flexio\System\Util::isValidEmail() test for string input; return true for valid email; digits in address are allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('email@subdomain.domain.com');
        $expected = true;
        TestCheck::assertBoolean('C.14', '\Flexio\System\Util::isValidEmail() test for string input; return true for valid email; subdomain allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('email@[123.123.123.123]');
        $expected = true;
        TestCheck::assertBoolean('C.15', '\Flexio\System\Util::isValidEmail() test for string input; return true for valid email; square bracket around IP address',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('"email"@domain.com');
        $expected = true;
        TestCheck::assertBoolean('C.16', '\Flexio\System\Util::isValidEmail() test for string input; return true for valid email; quotes around email are allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('email@domain-one.com');
        $expected = true;
        TestCheck::assertBoolean('C.17', '\Flexio\System\Util::isValidEmail() test for string input; return true for valid email; dash in domain name is allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('email@domain.name');
        $expected = true;
        TestCheck::assertBoolean('C.18', '\Flexio\System\Util::isValidEmail() test for string input; return true for valid email; check for top-level domain name',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('email@domain.co.jp');
        $expected = true;
        TestCheck::assertBoolean('C.19', '\Flexio\System\Util::isValidEmail() test for string input; return true for valid email; period in top-level domain name is allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('email');
        $expected = false;
        TestCheck::assertBoolean('C.20', '\Flexio\System\Util::isValidEmail() test for string input; return false for invalid email; missing @ sign',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('email.domain.com');
        $expected = false;
        TestCheck::assertBoolean('C.21', '\Flexio\System\Util::isValidEmail() test for string input; return false for invalid email; missing @',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('email@');
        $expected = false;
        TestCheck::assertBoolean('C.22', '\Flexio\System\Util::isValidEmail() test for string input; return false for invalid email; missing domain',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('email@domain');
        $expected = false;
        TestCheck::assertBoolean('C.23', '\Flexio\System\Util::isValidEmail() test for string input; return false for invalid email; invalid domain',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('email@#.com');
        $expected = false;
        TestCheck::assertBoolean('C.24', '\Flexio\System\Util::isValidEmail() test for string input; return false for invalid email; invalid domain',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('email@-domain.com');
        $expected = false;
        TestCheck::assertBoolean('C.25', '\Flexio\System\Util::isValidEmail() test for string input; return false for invalid email; invalid domain',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('@domain.com');
        $expected = false;
        TestCheck::assertBoolean('C.26', '\Flexio\System\Util::isValidEmail() test for string input; return false for invalid email; missing address',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('Joe Smith <email@domain.com>');
        $expected = false;
        TestCheck::assertBoolean('C.27', '\Flexio\System\Util::isValidEmail() test for string input; return false for invalid email; encoded html within email is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('email@domain@domain.com');
        $expected = false;
        TestCheck::assertBoolean('C.28', '\Flexio\System\Util::isValidEmail() test for string input; return false for invalid email; two @ signs',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('.email@domain.com');
        $expected = false;
        TestCheck::assertBoolean('C.29', '\Flexio\System\Util::isValidEmail() test for string input; return false for invalid email; leading period in address is not allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('email.@domain.com');
        $expected = false;
        TestCheck::assertBoolean('C.30', '\Flexio\System\Util::isValidEmail() test for string input; return false for invalid email; trailing period in address is not allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('email..email@domain.com');
        $expected = false;
        TestCheck::assertBoolean('C.31', '\Flexio\System\Util::isValidEmail() test for string input; return false for invalid email; multiple periods in address not allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('あ@domain.com');
        $expected = false;
        TestCheck::assertBoolean('C.32', '\Flexio\System\Util::isValidEmail() test for string input; return false for invalid email; unicode characters in address',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('あa@domain.com');
        $expected = false;
        TestCheck::assertBoolean('C.33', '\Flexio\System\Util::isValidEmail() test for string input; return false for invalid email; unicode characters in address',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('aお@domain.com');
        $expected = false;
        TestCheck::assertBoolean('C.34', '\Flexio\System\Util::isValidEmail() test for string input; return false for invalid email; unicode characters in address',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('email@domain.com (Joe Smith)');
        $expected = false;
        TestCheck::assertBoolean('C.35', '\Flexio\System\Util::isValidEmail() test for string input; return false for invalid email; text followed email is not allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('email@111.222.333.44444');
        $expected = false;
        TestCheck::assertBoolean('C.36', '\Flexio\System\Util::isValidEmail() test for string input; return false for invalid email; invalid IP address',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('email@domain..com');
        $expected = false;
        TestCheck::assertBoolean('C.37', '\Flexio\System\Util::isValidEmail() test for string input; return false for invalid email; multiple periods in domain not allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('email@xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxx.com');
        $expected = true; // 254 chars
        TestCheck::assertBoolean('C.38', '\Flexio\System\Util::isValidEmail() test for string input; return false for email longer than 254 chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidEmail('email@xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxx.com');
        $expected = false; // 254 chars
        TestCheck::assertBoolean('C.39', '\Flexio\System\Util::isValidEmail() test for string input; return false for email longer than 254 chars',  $actual, $expected, $results);




        // TEST: validation tests for isValidHostName()

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName(null);
        $expected = false;
        TestCheck::assertBoolean('D.1', '\Flexio\System\Util::isValidHostName() test for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName(true);
        $expected = false;
        TestCheck::assertBoolean('D.2', '\Flexio\System\Util::isValidHostName() test for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName('');
        $expected = false;
        TestCheck::assertBoolean('D.3', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName('x');
        $expected = true;
        TestCheck::assertBoolean('D.4', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName(' x');
        $expected = false;
        TestCheck::assertBoolean('D.5', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName('x ');
        $expected = false;
        TestCheck::assertBoolean('D.6', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName('0');
        $expected = true;
        TestCheck::assertBoolean('D.7', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName('-');
        $expected = false;
        TestCheck::assertBoolean('D.8', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        $actual = \Flexio\System\Util::isValidHostName('x0');
        $expected = true;
        TestCheck::assertBoolean('D.9', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        $actual = \Flexio\System\Util::isValidHostName('0X');
        $expected = true;
        TestCheck::assertBoolean('D.10', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        $actual = \Flexio\System\Util::isValidHostName('x-');
        $expected = false;
        TestCheck::assertBoolean('D.11', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        $actual = \Flexio\System\Util::isValidHostName('-x');
        $expected = false;
        TestCheck::assertBoolean('D.12', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        $actual = \Flexio\System\Util::isValidHostName('0-x');
        $expected = true;
        TestCheck::assertBoolean('D.13', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        $actual = \Flexio\System\Util::isValidHostName('x-0');
        $expected = true;
        TestCheck::assertBoolean('D.14', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        $actual = \Flexio\System\Util::isValidHostName('x+0');
        $expected = false;
        TestCheck::assertBoolean('D.15', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        $actual = \Flexio\System\Util::isValidHostName('x_0');
        $expected = false;
        TestCheck::assertBoolean('D.16', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        $actual = \Flexio\System\Util::isValidHostName('(x)');
        $expected = false;
        TestCheck::assertBoolean('D.17', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        $actual = \Flexio\System\Util::isValidHostName('x@0');
        $expected = false;
        TestCheck::assertBoolean('D.18', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        $actual = \Flexio\System\Util::isValidHostName('x#0');
        $expected = false;
        TestCheck::assertBoolean('D.19', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName('localhost');
        $expected = true;
        TestCheck::assertBoolean('D.20', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName('127.0.0.1');
        $expected = true;
        TestCheck::assertBoolean('D.21', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName('10.1.1.1');
        $expected = true;
        TestCheck::assertBoolean('D.22', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
        $expected = true;
        TestCheck::assertBoolean('D.23', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname; length of label should be between 1 and 63 characters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
        $expected = false;
        TestCheck::assertBoolean('D.24', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname; length of label should be between 1 and 63 characters ',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
        $expected = false;
        TestCheck::assertBoolean('D.25', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname; length of label should be between 1 and 63 characters ',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName('.');
        $expected = false;
        TestCheck::assertBoolean('D.26', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName('x.');
        $expected = true;
        TestCheck::assertBoolean('D.27', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName('.x');
        $expected = false;
        TestCheck::assertBoolean('D.28', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName('x.x');
        $expected = true;
        TestCheck::assertBoolean('D.29', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName('x .x');
        $expected = false;
        TestCheck::assertBoolean('D.30', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName('x. x');
        $expected = false;
        TestCheck::assertBoolean('D.31', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName('x.-');
        $expected = false;
        TestCheck::assertBoolean('D.32', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName('x..x');
        $expected = false;
        TestCheck::assertBoolean('D.33', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName('x.-x');
        $expected = false;
        TestCheck::assertBoolean('D.34', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName('x.x-');
        $expected = false;
        TestCheck::assertBoolean('D.35', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName('a.Bc.deF.gHij.klmno.pqrstu');
        $expected = true;
        TestCheck::assertBoolean('D.36', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName('xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxx');
        $expected = true;
        TestCheck::assertBoolean('D.37', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname; overall length, not including final period should be between 1 and 253 characters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName('xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxx.');
        $expected = true;
        TestCheck::assertBoolean('D.38', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname; overall length, not including final period should be between 1 and 253 characters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidHostName('xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxx');
        $expected = false;
        TestCheck::assertBoolean('D.39', '\Flexio\System\Util::isValidHostName() test for string input; return false for invalid hostname; overall length, not including final period should be between 1 and 253 characters',  $actual, $expected, $results);



        // TEST: validation tests for isValidIPV4()

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4(null);
        $expected = false;
        TestCheck::assertBoolean('E.1', '\Flexio\System\Util::isValidIPV4() test for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4(true);
        $expected = false;
        TestCheck::assertBoolean('E.2', '\Flexio\System\Util::isValidIPV4() test for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('0');
        $expected = false;
        TestCheck::assertBoolean('E.3', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('255');
        $expected = false;
        TestCheck::assertBoolean('E.4', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('0.0.0.0');
        $expected = true;
        TestCheck::assertBoolean('E.5', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('0:0:0:0');
        $expected = false;
        TestCheck::assertBoolean('E.6', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('0:0:0:0:0:0:0:0:0');
        $expected = false;
        TestCheck::assertBoolean('E.7', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('0.0.0.0 ');
        $expected = false;
        TestCheck::assertBoolean('E.8', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4(' 0.0.0.0');
        $expected = false;
        TestCheck::assertBoolean('E.9', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('0. 0.0.0');
        $expected = false;
        TestCheck::assertBoolean('E.10', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('0.0.0 .0');
        $expected = false;
        TestCheck::assertBoolean('E.11', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('a.0.0.0');
        $expected = false;
        TestCheck::assertBoolean('E.12', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('0.a.0.0');
        $expected = false;
        TestCheck::assertBoolean('E.13', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('0.0.a.0');
        $expected = false;
        TestCheck::assertBoolean('E.14', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('0.0.0.a');
        $expected = false;
        TestCheck::assertBoolean('E.15', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('0..0.0');
        $expected = false;
        TestCheck::assertBoolean('E.16', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('0.0..0.0');
        $expected = false;
        TestCheck::assertBoolean('E.17', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('0.0.0..');
        $expected = false;
        TestCheck::assertBoolean('E.18', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('255.255.255.255');
        $expected = true;
        TestCheck::assertBoolean('E.19', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('256.255.255.255');
        $expected = false;
        TestCheck::assertBoolean('E.20', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('255.256.255.255');
        $expected = false;
        TestCheck::assertBoolean('E.21', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('255.255.256.255');
        $expected = false;
        TestCheck::assertBoolean('E.22', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('255.255.255.256');
        $expected = false;
        TestCheck::assertBoolean('E.23', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('127.0.0.0');
        $expected = true;
        TestCheck::assertBoolean('E.24', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('-1.0.0.0');
        $expected = false;
        TestCheck::assertBoolean('E.25', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('0.-1.0.0');
        $expected = false;
        TestCheck::assertBoolean('E.26', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('0.0.-1.0');
        $expected = false;
        TestCheck::assertBoolean('E.27', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('0.0.0.-1');
        $expected = false;
        TestCheck::assertBoolean('E.28', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('10.0.1.5');
        $expected = true;
        TestCheck::assertBoolean('E.29', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV4('198.153.192.1');
        $expected = true;
        TestCheck::assertBoolean('E.30', '\Flexio\System\Util::isValidIPV4() test for string input; return false for invalid IP4 addresses',  $actual, $expected, $results);



        // TEST: validation tests for isValidIPV6()

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6(null);
        $expected = false;
        TestCheck::assertBoolean('F.1', '\Flexio\System\Util::isValidIPV6() test for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6(true);
        $expected = false;
        TestCheck::assertBoolean('F.2', '\Flexio\System\Util::isValidIPV6() test for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('0000:0000:0000:0000:0000:0000:0000:0000');
        $expected = true;
        TestCheck::assertBoolean('F.3', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('0:0:0:0:0:0:0:0');
        $expected = true;
        TestCheck::assertBoolean('F.4', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('0.0.0.0');
        $expected = false;
        TestCheck::assertBoolean('F.5', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('0.0.0.0.0.0.0.0.0');
        $expected = false;
        TestCheck::assertBoolean('F.6', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('0:0:0:0:0:0:0');
        $expected = false;
        TestCheck::assertBoolean('F.7', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('::');
        $expected = true;
        TestCheck::assertBoolean('F.8', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('::::');
        $expected = false;
        TestCheck::assertBoolean('F.9', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('0000:0000:0000:0000:0000:0000:0000:0001');
        $expected = true;
        TestCheck::assertBoolean('F.10', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('::1');
        $expected = true;
        TestCheck::assertBoolean('F.11', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('FFFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF');
        $expected = true;
        TestCheck::assertBoolean('F.12', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('A:A:A:A:A:A:A:A');
        $expected = true;
        TestCheck::assertBoolean('F.13', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('A:A:A:A:A:A:A:A');
        $expected = true;
        TestCheck::assertBoolean('F.14', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('a:a:a:a:a:a:a:a');
        $expected = true;
        TestCheck::assertBoolean('F.15', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('Z:A:A:A:A:A:A:A');
        $expected = false;
        TestCheck::assertBoolean('F.16', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('A:Z:A:A:A:A:A:A');
        $expected = false;
        TestCheck::assertBoolean('F.17', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('A:A:Z:A:A:A:A:A');
        $expected = false;
        TestCheck::assertBoolean('F.18', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('A:A:A:Z:A:A:A:A');
        $expected = false;
        TestCheck::assertBoolean('F.19', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('A:A:A:A:Z:A:A:A');
        $expected = false;
        TestCheck::assertBoolean('F.20', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('A:A:A:A:A:Z:A:A');
        $expected = false;
        TestCheck::assertBoolean('F.21', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('A:A:A:A:A:A:Z:A');
        $expected = false;
        TestCheck::assertBoolean('F.22', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('A:A:A:A:A:A:A:Z');
        $expected = false;
        TestCheck::assertBoolean('F.23', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6(' A:A:A:A:A:A:A:A');
        $expected = false;
        TestCheck::assertBoolean('F.24', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('A:A:A:A:A:A:A:A ');
        $expected = false;
        TestCheck::assertBoolean('F.25', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('A: A:A:A:A:A:A:A');
        $expected = false;
        TestCheck::assertBoolean('F.26', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('A:A:A:A:A:A:A :A');
        $expected = false;
        TestCheck::assertBoolean('F.27', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('FE80:0000:0000:0000:0000:0000:0000:0000');
        $expected = true;
        TestCheck::assertBoolean('F.28', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('FE80:0:0:0:0:0:0:0');
        $expected = true;
        TestCheck::assertBoolean('F.29', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('FE80::');
        $expected = true;
        TestCheck::assertBoolean('F.30', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('FE80:0000:0000:0000:0000:0000:0000:000A');
        $expected = true;
        TestCheck::assertBoolean('F.31', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('FE80:0:0:0:0:0:0:A');
        $expected = true;
        TestCheck::assertBoolean('F.32', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('FE80::A');
        $expected = true;
        TestCheck::assertBoolean('F.33', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('FE80:0000:0000:0000:000A:0000:0000:000B');
        $expected = true;
        TestCheck::assertBoolean('F.34', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('FE80:0:0:0:A:0:0:B');
        $expected = true;
        TestCheck::assertBoolean('F.35', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('FE80:0:0:0:A:0:0:B');
        $expected = true;
        TestCheck::assertBoolean('F.36', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('FE80::A:0:0:B');
        $expected = true;
        TestCheck::assertBoolean('F.37', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('FE80:0:0:0:A::B');
        $expected = true;
        TestCheck::assertBoolean('F.38', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('FE80::A::B');
        $expected = false;
        TestCheck::assertBoolean('F.39', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('FE80:CD00:3000:0100:0020:4200:211E:729C');
        $expected = true;
        TestCheck::assertBoolean('F.40', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('FE80:CD00:3000:0100:00020:4200:211E:729C');
        $expected = false;
        TestCheck::assertBoolean('F.41', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('::1');
        $expected = true;
        TestCheck::assertBoolean('F.42', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('[::1]');
        $expected = false;
        TestCheck::assertBoolean('F.43', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('[::1]:80');
        $expected = false;
        TestCheck::assertBoolean('F.44', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidIPV6('http://[::1]:80');
        $expected = false;
        TestCheck::assertBoolean('F.45', '\Flexio\System\Util::isValidIPV6() test for string input; return false for invalid IP6 addresses',  $actual, $expected, $results);



        // TEST: validation tests for isValidUrl()

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl(null);
        $expected = false;
        TestCheck::assertBoolean('G.1', '\Flexio\System\Util::isValidUrl() test for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl(true);
        $expected = false;
        TestCheck::assertBoolean('G.2', '\Flexio\System\Util::isValidUrl() test for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl('www.test.com');
        $expected = false;
        TestCheck::assertBoolean('G.3', '\Flexio\System\Util::isValidUrl() test for string input; return false for invalid URLs',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl('http://');
        $expected = false;
        TestCheck::assertBoolean('G.4', '\Flexio\System\Util::isValidUrl() test for string input; return false for invalid URLs',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl('http://www.test');
        $expected = true;
        TestCheck::assertBoolean('G.5', '\Flexio\System\Util::isValidUrl() test for string input; return false for invalid URLs',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl('http://www.test.com');
        $expected = true;
        TestCheck::assertBoolean('G.6', '\Flexio\System\Util::isValidUrl() test for string input; return false for invalid URLs',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl('http://www.test.io');
        $expected = true;
        TestCheck::assertBoolean('G.7', '\Flexio\System\Util::isValidUrl() test for string input; return false for invalid URLs',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl('http://test.io');
        $expected = true;
        TestCheck::assertBoolean('G.8', '\Flexio\System\Util::isValidUrl() test for string input; return false for invalid URLs',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl('http://a.b.test.io');
        $expected = true;
        TestCheck::assertBoolean('G.9', '\Flexio\System\Util::isValidUrl() test for string input; return false for invalid URLs',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl('http://www.test.com/');
        $expected = true;
        TestCheck::assertBoolean('G.10', '\Flexio\System\Util::isValidUrl() test for string input; return false for invalid URLs',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl('https://www.test.com/');
        $expected = true;
        TestCheck::assertBoolean('G.11', '\Flexio\System\Util::isValidUrl() test for string input; return false for invalid URLs',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl('http://www.test.com/endpoint');
        $expected = true;
        TestCheck::assertBoolean('G.12', '\Flexio\System\Util::isValidUrl() test for string input; return false for invalid URLs',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl('http://www.test.com/endpoint.html');
        $expected = true;
        TestCheck::assertBoolean('G.13', '\Flexio\System\Util::isValidUrl() test for string input; return false for invalid URLs',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl('http:/www.test.com');
        $expected = false;
        TestCheck::assertBoolean('G.14', '\Flexio\System\Util::isValidUrl() test for string input; return false for invalid URLs',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl('http:\\www.test.com');
        $expected = false;
        TestCheck::assertBoolean('G.15', '\Flexio\System\Util::isValidUrl() test for string input; return false for invalid URLs',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl('http://www.test.com\endpoint.html');
        $expected = false;
        TestCheck::assertBoolean('G.16', '\Flexio\System\Util::isValidUrl() test for string input; return false for invalid URLs',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl('http://www.test.com/embedded space.html');
        $expected = false;
        TestCheck::assertBoolean('G.17', '\Flexio\System\Util::isValidUrl() test for string input; return false for invalid URLs',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl('http://www.test.com/embedded%20space.html');
        $expected = true;
        TestCheck::assertBoolean('G.18', '\Flexio\System\Util::isValidUrl() test for string input; return false for invalid URLs',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl('ftp://ftp.is.co.za/rfc/rfc1808.txt'); // example from rfc3986
        $expected = true;
        TestCheck::assertBoolean('G.19', '\Flexio\System\Util::isValidUrl() test for string input; return false for invalid URLs',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl('http://www.ietf.org/rfc/rfc2396.txt'); // example from rfc3986
        $expected = true;
        TestCheck::assertBoolean('G.20', '\Flexio\System\Util::isValidUrl() test for string input; return false for invalid URLs',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl('ldap://[2001:db8::7]/c=GB?objectClass?one'); // example from rfc3986
        $expected = true;
        TestCheck::assertBoolean('G.21', '\Flexio\System\Util::isValidUrl() test for string input; return false for invalid URLs',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl('mailto:John.Doe@example.com'); // example from rfc3986
        $expected = true;
        TestCheck::assertBoolean('G.22', '\Flexio\System\Util::isValidUrl() test for string input; return false for invalid URLs',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl('news:comp.infosystems.www.servers.unix'); // example from rfc3986
        $expected = true;
        TestCheck::assertBoolean('G.23', '\Flexio\System\Util::isValidUrl() test for string input; return false for invalid URLs',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl('tel:+1-816-555-1212'); // example from rfc3986
        $expected = false;
        TestCheck::assertBoolean('G.24', '\Flexio\System\Util::isValidUrl() test for string input; return false for invalid URLs',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl('tel:+1-816-555-1212'); // example from rfc3986
        $expected = false;
        TestCheck::assertBoolean('G.25', '\Flexio\System\Util::isValidUrl() test for string input; return false for invalid URLs',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl('telnet://192.0.2.16:80/'); // example from rfc3986
        $expected = true;
        TestCheck::assertBoolean('G.26', '\Flexio\System\Util::isValidUrl() test for string input; return false for invalid URLs',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidUrl('urn:oasis:names:specification:docbook:dtd:xml:4.1.2'); // example from rfc3986
        $expected = false;
        TestCheck::assertBoolean('G.27', '\Flexio\System\Util::isValidUrl() test for string input; return false for invalid URLs',  $actual, $expected, $results);



        // TEST: validation tests for isValidPassword()

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidPassword(null);
        $expected = false;
        TestCheck::assertBoolean('H.1', '\Flexio\System\Util::isValidPassword() test for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidPassword(true);
        $expected = false;
        TestCheck::assertBoolean('H.2', '\Flexio\System\Util::isValidPassword() test for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidPassword(1111111111);
        $expected = false;
        TestCheck::assertBoolean('H.3', '\Flexio\System\Util::isValidPassword() test for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidPassword('');
        $expected = false;
        TestCheck::assertBoolean('H.4', '\Flexio\System\Util::isValidPassword() test for string input less than the minimum length',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidPassword('a');
        $expected = false;
        TestCheck::assertBoolean('H.5', '\Flexio\System\Util::isValidPassword() test for string input less than the minimum length',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidPassword('aaaaaaa');
        $expected = false;
        TestCheck::assertBoolean('H.6', '\Flexio\System\Util::isValidPassword() test for string input less than the minimum length',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidPassword('aaaaaaa');
        $expected = false;
        TestCheck::assertBoolean('H.7', '\Flexio\System\Util::isValidPassword() test for string input less than the minimum length',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidPassword('aaaaaaaa');
        $expected = true;
        TestCheck::assertBoolean('H.8', '\Flexio\System\Util::isValidPassword() test for string input equal to the minimum length',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::isValidPassword('aaaaaaaaa');
        $expected = true;
        TestCheck::assertBoolean('H.9', '\Flexio\System\Util::isValidPassword() test for string input greater than the minimum length',  $actual, $expected, $results);



        // TEST: validation tests for getFileExtension()

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFileExtension('');
        $expected = '';
        TestCheck::assertString('I.1', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFileExtension('name');
        $expected = '';
        TestCheck::assertString('I.2', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFileExtension('.');
        $expected = '';
        TestCheck::assertString('I.3', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFileExtension('name.');
        $expected = '';
        TestCheck::assertString('I.4', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFileExtension('.ext');
        $expected = 'ext';
        TestCheck::assertString('I.5', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFileExtension('name.ext');
        $expected = 'ext';
        TestCheck::assertString('I.6', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFileExtension('/name.EXT');
        $expected = 'EXT';
        TestCheck::assertString('I.7', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFileExtension('name.ext1.ext2');
        $expected = 'ext2';
        TestCheck::assertString('I.8', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFileExtension('/path/name.ext');
        $expected = 'ext';
        TestCheck::assertString('I.9', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFileExtension('/path/.ext');
        $expected = 'ext';
        TestCheck::assertString('I.10', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFileExtension('https://www.flex.io/path/test.csv');
        $expected = 'csv';
        TestCheck::assertString('I.11', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFileExtension('https://www.flex.io/path/TEST.TXT');
        $expected = 'TXT';
        TestCheck::assertString('I.12', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFileExtension('\path\name.ext');
        $expected = 'ext';
        TestCheck::assertString('I.13', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFileExtension('c:\\path\name.ext');
        $expected = 'ext';
        TestCheck::assertString('I.14', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);



        // TEST: validation tests for getFilename()

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFilename('');
        $expected = '';
        TestCheck::assertString('J.1', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFilename('name');
        $expected = 'name';
        TestCheck::assertString('J.2', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFilename('.');
        $expected = '';
        TestCheck::assertString('J.3', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFilename('name.');
        $expected = 'name';
        TestCheck::assertString('J.4', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFilename('.ext');
        $expected = '';
        TestCheck::assertString('J.5', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFilename('name.ext');
        $expected = 'name';
        TestCheck::assertString('J.6', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFilename('/name.EXT');
        $expected = 'name';
        TestCheck::assertString('J.7', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFilename('name.ext1.ext2');
        $expected = 'name.ext1';
        TestCheck::assertString('J.8', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFilename('/path/name.ext');
        $expected = 'name';
        TestCheck::assertString('J.9', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFilename('/path/.ext');
        $expected = '';
        TestCheck::assertString('J.10', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFilename('https://www.flex.io/path/test.csv');
        $expected = 'test';
        TestCheck::assertString('J.11', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFilename('https://www.flex.io/path/TEST.TXT');
        $expected = 'TEST';
        TestCheck::assertString('J.12', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFilename('\path\name.ext');
        $expected = 'name';
        TestCheck::assertString('J.13', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Util::getFilename('c:\\path\name.ext');
        $expected = 'name';
        TestCheck::assertString('J.14', '\Flexio\System\Util::getFileExtension() test to extract extension',  $actual, $expected, $results);



        // TEST: validation tests for getGitRevision()

        // BEGIN TEST
        $git_revision = \Flexio\System\Util::getGitRevision();
        $actual = is_string($git_revision) === true && preg_match('/^[0-9a-f]{40}$/', $git_revision) === 1;
        $expected = true;
        TestCheck::assertBoolean('K.1', '\Flexio\System\Util::getGitRevision() test to verify a valid hash is returned',  $actual, $expected, $results);
    }
}
