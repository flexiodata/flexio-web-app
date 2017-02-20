<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-02-08
 *
 * @package flexio
 * @subpackage Tests
 */


class Test
{
    public function run(&$results)
    {
        // note: the following case-insensitive literals are currently supported:
        //     null
        //     true, false (boolean)
        //     numbers     (integer, decimal, including numbers specified in scientific notation)
        //     strings

        // TODO:
        //     - add tests for reserved words


        // TEST: null literals

        // BEGIN TEST
        $actual = TestUtil::evalExpression('null');
        $expected = null;
        TestCheck::assertNull('A.1', 'Expression; null literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('NULL');
        $expected = null;
        TestCheck::assertNull('A.2', 'Expression; null literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('Null');
        $expected = null;
        TestCheck::assertNull('A.3', 'Expression; null literal is mixed-case',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('nuLL');
        $expected = null;
        TestCheck::assertNull('A.4', 'Expression; null literal is mixed-case',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('n ull');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.5', 'Expression; fail if null literal contains spaces',  $actual, $expected, $results);



        // TEST: boolean literals

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true');
        $expected = true;
        TestCheck::assertBoolean('B.1', 'Expression; boolean literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false');
        $expected = false;
        TestCheck::assertBoolean('B.2', 'Expression; boolean literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('TRUE');
        $expected = true;
        TestCheck::assertBoolean('B.3', 'Expression; boolean literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('FALSE');
        $expected = false;
        TestCheck::assertBoolean('B.4', 'Expression; boolean literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('True');
        $expected = true;
        TestCheck::assertBoolean('B.5', 'Expression; mixed-case',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('False');
        $expected = false;
        TestCheck::assertBoolean('B.6', 'Expression; mixed-case',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('truE');
        $expected = true;
        TestCheck::assertBoolean('B.7', 'Expression; mixed-case',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('falsE');
        $expected = false;
        TestCheck::assertBoolean('B.8', 'Expression; mixed-case',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('t rue');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.9', 'Expression; fail if boolean literal contains embedded spaces',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('fals e');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.10', 'Expression; fail if boolean literal contains embedded spaces',  $actual, $expected, $results);



        // TEST: numeric literals

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0');
        $expected = 0;
        TestCheck::assertNumber('C.1', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-0');
        $expected = 0;
        TestCheck::assertNumber('C.2', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('+0');
        $expected = 0;
        TestCheck::assertNumber('C.3', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1');
        $expected = 1;
        TestCheck::assertNumber('C.4', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1');
        $expected = -1;
        TestCheck::assertNumber('C.5', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('+1');
        $expected = 1;
        TestCheck::assertNumber('C.6', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('.1');
        $expected = 0.1;
        TestCheck::assertNumber('C.7', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-.1');
        $expected = -0.1;
        TestCheck::assertNumber('C.8', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('+.1');
        $expected = 0.1;
        TestCheck::assertNumber('C.9', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.1');
        $expected = 0.1;
        TestCheck::assertNumber('C.10', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-0.1');
        $expected = -0.1;
        TestCheck::assertNumber('C.11', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('+0.1');
        $expected = 0.1;
        TestCheck::assertNumber('C.12', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('3.99');
        $expected = 3.99;
        TestCheck::assertNumber('C.13', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-3.99');
        $expected = -3.99;
        TestCheck::assertNumber('C.14', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('+3.99');
        $expected = 3.99;
        TestCheck::assertNumber('C.15', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1 2');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.16', 'Expression; fail if numeric literal contains embedded spaces',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0. 1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.17', 'Expression; fail if numeric literal contains embedded spaces',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1 2');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.18', 'Expression; fail if numeric literal contains embedded spaces',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('- 1');
        $expected = -1;
        TestCheck::assertNumber('C.19', 'Expression; numeric literals with spaces in some places are equivalent to numbers with operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('+ 1');
        $expected = 1;
        TestCheck::assertNumber('C.20', 'Expression; numeric literals with spaces in some places are equivalent to numbers with operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1,000');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.21', 'Expression; fail if numeric literal contains embedded comma; not supported at this time',  $actual, $expected, $results);



        // TEST: numeric literals (exponent form)

        // BEGIN TEST
        $actual = TestUtil::evalExpression('e');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.1', 'Expression; fail when number isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('e-');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.2', 'Expression; fail when number isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('e+');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.3', 'Expression; fail when number isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-e');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.4', 'Expression; fail when number isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('+e');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.5', 'Expression; fail when number isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2e');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.6', 'Expression; fail when number isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2e-');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.7', 'Expression; fail when number isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('e2');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.8', 'Expression; fail when number isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('e-2');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.9', 'Expression; fail when number isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ae2');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.10', 'Expression; fail when number isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2ea');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.11', 'Expression; fail when number isn\'t formatted correctly',  $actual, $expected, $results);


        // BEGIN TEST
        $actual = TestUtil::evalExpression('.e2');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.12', 'Expression; fail when number isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2e.');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.13', 'Expression; fail when number isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0e0');
        $expected = (float)0;
        TestCheck::assertNumber('D.14', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e0');
        $expected = (float)1;
        TestCheck::assertNumber('D.15', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0e1');
        $expected = (float)0;
        TestCheck::assertNumber('D.16', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e1');
        $expected = (float)10;
        TestCheck::assertNumber('D.17', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1ee1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.18', 'Expression; fail if numeric literal isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e2');
        $expected = (float)100;
        TestCheck::assertNumber('D.19', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e-2');
        $expected = 0.01;
        TestCheck::assertNumber('D.20', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e+2');
        $expected = (float)100;
        TestCheck::assertNumber('D.21', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('.1e+2');
        $expected = (float)10;
        TestCheck::assertNumber('D.22', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1.e+2');
        $expected = (float)100;
        TestCheck::assertNumber('D.23', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.1e+2');
        $expected = (float)10;
        TestCheck::assertNumber('D.24', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1.01e2');
        $expected = (float)101;
        TestCheck::assertNumber('D.25', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.01e-2');
        $expected = -0.0101;
        TestCheck::assertNumber('D.26', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('E');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.27', 'Expression; fail when number isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('E-');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.28', 'Expression; fail when number isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('E+');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.29', 'Expression; fail when number isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-E');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.30', 'Expression; fail when number isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('+E');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.31', 'Expression; fail when number isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2E');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.32', 'Expression; fail when number isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2E-');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.33', 'Expression; fail when number isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('E2');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.34', 'Expression; fail when number isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('E-2');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.35', 'Expression; fail when number isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('aE2');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.36', 'Expression; fail when number isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2Ea');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.37', 'Expression; fail when number isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('.E2');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.38', 'Expression; fail when number isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2E.');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.39', 'Expression; fail when number isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0E0');
        $expected = (float)0;
        TestCheck::assertNumber('D.40', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1E0');
        $expected = (float)1;
        TestCheck::assertNumber('D.41', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0E1');
        $expected = (float)0;
        TestCheck::assertNumber('D.42', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1E1');
        $expected = (float)10;
        TestCheck::assertNumber('D.43', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1EE1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.44', 'Expression; fail if numeric literal isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1E2');
        $expected = (float)100;
        TestCheck::assertNumber('D.45', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1E-2');
        $expected = 0.01;
        TestCheck::assertNumber('D.46', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1E+2');
        $expected = (float)100;
        TestCheck::assertNumber('D.47', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('.1E+2');
        $expected = (float)10;
        TestCheck::assertNumber('D.48', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1.E+2');
        $expected = (float)100;
        TestCheck::assertNumber('D.49', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.1E+2');
        $expected = (float)10;
        TestCheck::assertNumber('D.50', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1.01E2');
        $expected = (float)101;
        TestCheck::assertNumber('D.51', 'Expression; numeric literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.01E-2');
        $expected = -0.0101;
        TestCheck::assertNumber('D.52', 'Expression; numeric literal',  $actual, $expected, $results);



        // TEST: string literals

        // BEGIN TEST
        // note: following expression could be valid if 'A' is a column name;
        // leave out test for now, but keep code for reference
        //$actual = TestUtil::evalExpression('A');
        //$expected = TestError::ERROR_BAD_PARSE;
        //TestCheck::assertString('E.1', 'Expression; fail if string literal isn\'t enclosed in single or double quotes',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('\'\'');
        $expected = '';
        TestCheck::assertString('E.2', 'Expression; empty string literal in single quotes',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('""');
        $expected = '';
        TestCheck::assertString('E.3', 'Expression; empty string literal in double quotes',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"\'');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.4', 'Expression; fail if quotes containing string literals aren\'t matched',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('\'"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.5', 'Expression; fail if quotes containing string literals aren\'t matched',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('\'A\'');
        $expected = 'A';
        TestCheck::assertString('E.6', 'Expression; string literal in single quotes',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"A"');
        $expected = 'A';
        TestCheck::assertString('E.7', 'Expression; string literal in double quotes',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"');
        $expected = 'a';
        TestCheck::assertString('E.8', 'Expression; string literal in double quotes',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"Abc"');
        $expected = 'Abc';
        TestCheck::assertString('E.9', 'Expression; string literal with lowercase and uppercase',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"abC"');
        $expected = 'abC';
        TestCheck::assertString('E.10', 'Expression; string literal with lowercase and uppercase',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression("\"'\"");
        $expected = "'";
        TestCheck::assertString('E.11', 'Expression; string literal with single quote enclosed with double quotes',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression("'\"'");
        $expected = '"';
        TestCheck::assertString('E.12', 'Expression; string literal with double quote enclosed with single quotes',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression("'''");
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.13', 'Expression; fail for a string literal with unescaped single quote enclosed with single quotes',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression("\"\"\"");
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.14', 'Expression; fail for a string literal with unescaped double quote enclosed with double quotes',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression("''''");
        $expected = "'";
        TestCheck::assertString('E.15', 'Expression; string literal with escaped single quote enclosed with single quotes',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression("\"\"\"\"");
        $expected = '"';
        TestCheck::assertString('E.16', 'Expression; string literal with escaped double quote enclosed with double quotes',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"  AB"');
        $expected = '  AB';
        TestCheck::assertString('E.17', 'Expression; string literal with embedded white space',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"AB  "');
        $expected = 'AB  ';
        TestCheck::assertString('E.18', 'Expression; string literal with embedded white space',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"A  B"');
        $expected = 'A  B';
        TestCheck::assertString('E.19', 'Expression; string literal with embedded white space',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"null"');
        $expected = 'null';
        TestCheck::assertString('E.20', 'Expression; string literal with embedded keyword',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"null"');
        $expected = 'null';
        TestCheck::assertString('E.21', 'Expression; string literal with embedded keyword',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"false"');
        $expected = 'false';
        TestCheck::assertString('E.22', 'Expression; string literal with embedded keyword',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"-0.123"');
        $expected = '-0.123';
        TestCheck::assertString('E.23', 'Expression; string literal with embedded number',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"123,456"');
        $expected = '123,456';
        TestCheck::assertString('E.24', 'Expression; string literal with embedded number',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"999-9999"');
        $expected = '999-9999';
        TestCheck::assertString('E.25', 'Expression; string literal with embedded numbers',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"%0"');
        $expected = '%0';
        TestCheck::assertString('E.26', 'Expression; string literal with embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"$2"');
        $expected = '$2';
        TestCheck::assertString('E.27', 'Expression; string literal with embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"("');
        $expected = '(';
        TestCheck::assertString('E.28', 'Expression; string literal with embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('")"');
        $expected = ')';
        TestCheck::assertString('E.29', 'Expression; string literal with embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"["');
        $expected = '[';
        TestCheck::assertString('E.30', 'Expression; string literal with embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"]"');
        $expected = ']';
        TestCheck::assertString('E.31', 'Expression; string literal with embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('","');
        $expected = ',';
        TestCheck::assertString('E.32', 'Expression; string literal with embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('";"');
        $expected = ';';
        TestCheck::assertString('E.33', 'Expression; string literal with embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"."');
        $expected = '.';
        TestCheck::assertString('E.34', 'Expression; string literal with embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"(1+1)"');
        $expected = '(1+1)';
        TestCheck::assertString('E.35', 'Expression; string literal with embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"+"');
        $expected = '+';
        TestCheck::assertString('E.36', 'Expression; string literal with embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"-"');
        $expected = '-';
        TestCheck::assertString('E.37', 'Expression; string literal with embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"*"');
        $expected = '*';
        TestCheck::assertString('E.38', 'Expression; string literal with embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"/"');
        $expected = '/';
        TestCheck::assertString('E.39', 'Expression; string literal with embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"%"');
        $expected = '%';
        TestCheck::assertString('E.40', 'Expression; string literal with embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"="');
        $expected = '=';
        TestCheck::assertString('E.41', 'Expression; string literal with embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"!="');
        $expected = '!=';
        TestCheck::assertString('E.42', 'Expression; string literal with embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"<>"');
        $expected = '<>';
        TestCheck::assertString('E.43', 'Expression; string literal with embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('">"');
        $expected = '>';
        TestCheck::assertString('E.44', 'Expression; string literal with embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('">="');
        $expected = '>=';
        TestCheck::assertString('E.45', 'Expression; string literal with embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"<"');
        $expected = '<';
        TestCheck::assertString('E.46', 'Expression; string literal with embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"<="');
        $expected = '<=';
        TestCheck::assertString('E.47', 'Expression; string literal with embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"~"');
        $expected = '~';
        TestCheck::assertString('E.48', 'Expression; string literal with embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"~*"');
        $expected = '~*';
        TestCheck::assertString('E.49', 'Expression; string literal with embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"!~"');
        $expected = '!~';
        TestCheck::assertString('E.50', 'Expression; string literal with embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"~!*"');
        $expected = '~!*';
        TestCheck::assertString('E.51', 'Expression; string literal with embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"!"');
        $expected = '!';
        TestCheck::assertString('E.52', 'Expression; string literal with embedded logical operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"not"');
        $expected = 'not';
        TestCheck::assertString('E.53', 'Expression; string literal with embedded logical operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"and"');
        $expected = 'and';
        TestCheck::assertString('E.54', 'Expression; string literal with embedded logical operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"or"');
        $expected = 'or';
        TestCheck::assertString('E.55', 'Expression; string literal with embedded logical operator',  $actual, $expected, $results);
    }
}
