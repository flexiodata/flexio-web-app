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
        // note: operator precedence needs to be finalized; tests assume the following:

        // precedence   operator            associativity   description
        // 1            ()                  left-to-right   grouping
        // 2            + - NOT (!)         right-to-left   unary plus and minus, logical NOT
        // 3            * / %               left-to-right   multiplication, division, modulo
        // 4            + -                 left-to-right   addition and subtraction
        // 5            < <= > >=           left-to-right   comparison
        // 6            = != (<>)           left-to-right   equal and not-equal (!= and <> are equivalent)
        // 7            AND                 left-to-right   logical conjunction
        // 8            OR                  left-to-right   logical disjunction

        // TODO:
        //     - add support for "in" operator as well as ternary "?:"?
        //     - add tests for operator precedence within a given level and
        //       precedence between levels


        // TEST: operator precedence: OR vs AND

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true or true and false');
        $expected = true;
        TestCheck::assertBoolean('A.1', 'Expression; AND operator takes precedence over OR operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false and true or true');
        $expected = true;
        TestCheck::assertBoolean('A.2', 'Expression; AND operator takes precedence over OR operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true and true or false and false');
        $expected = true;
        TestCheck::assertBoolean('A.3', 'Expression; AND operator takes precedence over OR operator',  $actual, $expected, $results);



        // TEST: operator precedence: =,!=,<> vs AND

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false = true and false');
        $expected = false;
        TestCheck::assertBoolean('B.1', 'Expression; = operator takes precedence over AND operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(false = true) and false');
        $expected = false;
        TestCheck::assertBoolean('B.2', 'Expression; = operator takes precedence over AND operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false = (true and false)');
        $expected = true;
        TestCheck::assertBoolean('B.3', 'Expression; = operator takes precedence over AND operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true != true and false');
        $expected = false;
        TestCheck::assertBoolean('B.4', 'Expression; != operator takes precedence over AND operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(true != true) and false');
        $expected = false;
        TestCheck::assertBoolean('B.5', 'Expression; != operator takes precedence over AND operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true != (true and false)');
        $expected = true;
        TestCheck::assertBoolean('B.6', 'Expression; != operator takes precedence over AND operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false and true != true');
        $expected = false;
        TestCheck::assertBoolean('B.7', 'Expression; != operator takes precedence over AND operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(false and true) != true');
        $expected = true;
        TestCheck::assertBoolean('B.8', 'Expression; != operator takes precedence over AND operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false and (true != true)');
        $expected = false;
        TestCheck::assertBoolean('B.9', 'Expression; != operator takes precedence over AND operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true <> true and false');
        $expected = false;
        TestCheck::assertBoolean('B.10', 'Expression; <> operator takes precedence over AND operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(true <> true) and false');
        $expected = false;
        TestCheck::assertBoolean('B.11', 'Expression; <> operator takes precedence over AND operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true <> (true and false)');
        $expected = true;
        TestCheck::assertBoolean('B.12', 'Expression; <> operator takes precedence over AND operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false and true <> true');
        $expected = false;
        TestCheck::assertBoolean('B.13', 'Expression; <> operator takes precedence over AND operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(false and true) <> true');
        $expected = true;
        TestCheck::assertBoolean('B.14', 'Expression; <> operator takes precedence over AND operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false and (true <> true)');
        $expected = false;
        TestCheck::assertBoolean('B.15', 'Expression; <> operator takes precedence over AND operator',  $actual, $expected, $results);



        // TEST: operator precedence: <,<=,>,>= vs =,!=,<>

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true < true = false');
        $expected = true;
        TestCheck::assertBoolean('C.1', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(true < true) = false');
        $expected = true;
        TestCheck::assertBoolean('C.2', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true < (true = false)');
        $expected = false;
        TestCheck::assertBoolean('C.3', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false = true < false');
        $expected = true;
        TestCheck::assertBoolean('C.4', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(false = true) < false');
        $expected = false;
        TestCheck::assertBoolean('C.5', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false = (true < false)');
        $expected = true;
        TestCheck::assertBoolean('C.6', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false <= true = false');
        $expected = false;
        TestCheck::assertBoolean('C.7', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(false <= true) = false');
        $expected = false;
        TestCheck::assertBoolean('C.8', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false <= (true = false)');
        $expected = true;
        TestCheck::assertBoolean('C.9', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false = false <= true');
        $expected = false;
        TestCheck::assertBoolean('C.10', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(false = false) <= true');
        $expected = true;
        TestCheck::assertBoolean('C.11', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false = (false <= true)');
        $expected = false;
        TestCheck::assertBoolean('C.12', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false > true = false');
        $expected = true;
        TestCheck::assertBoolean('C.13', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(false > true) = false');
        $expected = true;
        TestCheck::assertBoolean('C.14', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false > (true = false)');
        $expected = false;
        TestCheck::assertBoolean('C.15', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false = true > true');
        $expected = true;
        TestCheck::assertBoolean('C.16', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(false = true) > true');
        $expected = false;
        TestCheck::assertBoolean('C.17', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false = (true > true)');
        $expected = true;
        TestCheck::assertBoolean('C.18', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true >= true = false');
        $expected = false;
        TestCheck::assertBoolean('C.19', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(true >= true) = false');
        $expected = false;
        TestCheck::assertBoolean('C.20', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true >= (true = false)');
        $expected = true;
        TestCheck::assertBoolean('C.21', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true = true >= false');
        $expected = true;
        TestCheck::assertBoolean('C.22', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(false = true) >= false');
        $expected = true;
        TestCheck::assertBoolean('C.23', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false = (true >= false)');
        $expected = false;
        TestCheck::assertBoolean('C.24', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true < true != true');
        $expected = true;
        TestCheck::assertBoolean('C.25', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(true < true) != true');
        $expected = true;
        TestCheck::assertBoolean('C.26', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true < (true != true)');
        $expected = false;
        TestCheck::assertBoolean('C.27', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true != true < false');
        $expected = true;
        TestCheck::assertBoolean('C.28', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(true != true) < false');
        $expected = false;
        TestCheck::assertBoolean('C.29', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true != (true < false)');
        $expected = true;
        TestCheck::assertBoolean('C.30', 'Expression; <,<=,>,>= operators take precedence over =,!=,<> operators',  $actual, $expected, $results);



        // TEST: operator precedence: +,- vs <,<=,>,>=

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0+1>0');
        $expected = true;
        TestCheck::assertBoolean('D.1', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(0+1)>0');
        $expected = true;
        TestCheck::assertBoolean('D.2', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0+(1>0)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.3', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1>0+0');
        $expected = true;
        TestCheck::assertBoolean('D.4', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(1>0)+0');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.5', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1>(0+0)');
        $expected = true;
        TestCheck::assertBoolean('D.6', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0+0>=0');
        $expected = true;
        TestCheck::assertBoolean('D.7', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(0+0)>=0');
        $expected = true;
        TestCheck::assertBoolean('D.8', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0+(0>=0)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.9', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0>=0+0');
        $expected = true;
        TestCheck::assertBoolean('D.10', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(0>=0)+0');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.11', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0>=(0+0)');
        $expected = true;
        TestCheck::assertBoolean('D.12', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0<1+0');
        $expected = true;
        TestCheck::assertBoolean('D.13', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(0<1)+0');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.14', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0<(1+0)');
        $expected = true;
        TestCheck::assertBoolean('D.15', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0+0<1');
        $expected = true;
        TestCheck::assertBoolean('D.16', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(0+0)<1');
        $expected = true;
        TestCheck::assertBoolean('D.17', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0+(0<1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.18', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0+0<=0');
        $expected = true;
        TestCheck::assertBoolean('D.19', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(0+0)<=0');
        $expected = true;
        TestCheck::assertBoolean('D.20', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0+(0<=0)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.21', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0<=0+0');
        $expected = true;
        TestCheck::assertBoolean('D.22', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(0<=0)+0');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.23', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0<=(0+0)');
        $expected = true;
        TestCheck::assertBoolean('D.24', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0-1>0');
        $expected = false;
        TestCheck::assertBoolean('D.25', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(0-1)>0');
        $expected = false;
        TestCheck::assertBoolean('D.26', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0-(1>0)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.27', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1>0-0');
        $expected = true;
        TestCheck::assertBoolean('D.28', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(1>0)-0');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.29', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1>(0-0)');
        $expected = true;
        TestCheck::assertBoolean('D.30', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0-0>=0');
        $expected = true;
        TestCheck::assertBoolean('D.31', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(0-0)>=0');
        $expected = true;
        TestCheck::assertBoolean('D.32', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0-(0>=0)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.33', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0>=0-0');
        $expected = true;
        TestCheck::assertBoolean('D.34', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(0>=0)-0');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.35', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0>=(0-0)');
        $expected = true;
        TestCheck::assertBoolean('D.36', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0<1-0');
        $expected = true;
        TestCheck::assertBoolean('D.37', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(0<1)-0');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.38', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0<(1-0)');
        $expected = true;
        TestCheck::assertBoolean('D.39', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0-0<1');
        $expected = true;
        TestCheck::assertBoolean('D.40', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(0-0)<1');
        $expected = true;
        TestCheck::assertBoolean('D.41', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0-(0<1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.42', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0-0<=0');
        $expected = true;
        TestCheck::assertBoolean('D.43', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(0-0)<=0');
        $expected = true;
        TestCheck::assertBoolean('D.44', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0-(0<=0)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.45', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0<=0-0');
        $expected = true;
        TestCheck::assertBoolean('D.46', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(0<=0)-0');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.47', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0<=(0-0)');
        $expected = true;
        TestCheck::assertBoolean('D.48', 'Expression; +,- operators take precedence over <,<=,>,>= operators',  $actual, $expected, $results);



        // TEST: operator precedence: *,/,% vs +,-

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1+2*4');
        $expected = 9;
        TestCheck::assertNumber('E.1', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(1+2)*4');
        $expected = 12;
        TestCheck::assertNumber('E.2', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1+(2*4)');
        $expected = 9;
        TestCheck::assertNumber('E.3', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('4*2+1');
        $expected = 9;
        TestCheck::assertNumber('E.4', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(4*2)+1');
        $expected = 9;
        TestCheck::assertNumber('E.5', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('4*(2+1)');
        $expected = 12;
        TestCheck::assertNumber('E.6', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1-2*4');
        $expected = -7;
        TestCheck::assertNumber('E.7', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(1-2)*4');
        $expected = -4;
        TestCheck::assertNumber('E.8', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1-(2*4)');
        $expected = -7;
        TestCheck::assertNumber('E.9', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('4*2-1');
        $expected = 7;
        TestCheck::assertNumber('E.10', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(4*2)-1');
        $expected = 7;
        TestCheck::assertNumber('E.11', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('4*(2-1)');
        $expected = 4;
        TestCheck::assertNumber('E.12', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('3+4/2');
        $expected = (float)5;
        TestCheck::assertNumber('E.13', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(3+4)/2');
        $expected = (float)3.5;
        TestCheck::assertNumber('E.14', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('3+(4/2)');
        $expected = (float)5;
        TestCheck::assertNumber('E.15', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('4/2+3');
        $expected = (float)5;
        TestCheck::assertNumber('E.16', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(4/2)+3');
        $expected = (float)5;
        TestCheck::assertNumber('E.17', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('4/(2+3)');
        $expected = (float)0.8;
        TestCheck::assertNumber('E.18', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('3-4/2');
        $expected = (float)1;
        TestCheck::assertNumber('E.19', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(3-4)/2');
        $expected = (float)-0.5;
        TestCheck::assertNumber('E.20', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('3-(4/2)');
        $expected = (float)1;
        TestCheck::assertNumber('E.21', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('4/2-3');
        $expected = (float)-1;
        TestCheck::assertNumber('E.22', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(4/2)-3');
        $expected = (float)-1;
        TestCheck::assertNumber('E.23', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('4/(2-3)');
        $expected = (float)-4;
        TestCheck::assertNumber('E.24', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('3+4%2');
        $expected = 3;
        TestCheck::assertNumber('E.25', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(3+4)%2');
        $expected = 1;
        TestCheck::assertNumber('E.26', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('3+(4%2)');
        $expected = 3;
        TestCheck::assertNumber('E.27', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('4%2+3');
        $expected = 3;
        TestCheck::assertNumber('E.28', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(4%2)+3');
        $expected = 3;
        TestCheck::assertNumber('E.29', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('4%(2+3)');
        $expected = 4;
        TestCheck::assertNumber('E.30', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('3-4%2');
        $expected = 3;
        TestCheck::assertNumber('E.31', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(3-4)%2');
        $expected = -1;
        TestCheck::assertNumber('E.32', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('3-(4%2)');
        $expected = 3;
        TestCheck::assertNumber('E.33', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('4%2-3');
        $expected = -3;
        TestCheck::assertNumber('E.34', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(4%2)-3');
        $expected = -3;
        TestCheck::assertNumber('E.35', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('4%(2-3)');
        $expected = 0;
        TestCheck::assertNumber('E.36', 'Expression; *,/,% operators take precedence over +,- operators',  $actual, $expected, $results);



        // TEST: operator precedence: NOT (!),+,- vs *,/,%

        // TODO: revise tests

        // BEGIN TEST
        $actual = TestUtil::evalExpression('not true and false');
        $expected = false;
        TestCheck::assertBoolean('F.1', 'Expression; NOT operator takes precedence over AND operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true and not false');
        $expected = true;
        TestCheck::assertBoolean('F.2', 'Expression; NOT operator takes precedence over AND operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true and not true and false');
        $expected = false;
        TestCheck::assertBoolean('F.3', 'Expression; NOT operator takes precedence over AND operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('!true and false');
        $expected = false;
        TestCheck::assertBoolean('F.4', 'Expression; ! operator takes precedence over AND operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true and !false');
        $expected = true;
        TestCheck::assertBoolean('F.5', 'Expression; ! operator takes precedence over AND operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true and !true and false');
        $expected = false;
        TestCheck::assertBoolean('F.6', 'Expression; ! operator takes precedence over AND operator',  $actual, $expected, $results);




        // TEST: operator precedence: () vs NOT (!),+,-

        // TODO: revise tests

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1-2-3-4');
        $expected = -8;
        TestCheck::assertNumber('G.1', 'Expression; evaluate expressions within the inner-most group first',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1-2-3-(4)');
        $expected = -8;
        TestCheck::assertNumber('G.2', 'Expression; evaluate expressions within the inner-most group first',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1-2-(3)-4');
        $expected = -8;
        TestCheck::assertNumber('G.3', 'Expression; evaluate expressions within the inner-most group first',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1-(2)-3-4');
        $expected = -8;
        TestCheck::assertNumber('G.4', 'Expression; evaluate expressions within the inner-most group first',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(1)-2-3-4');
        $expected = -8;
        TestCheck::assertNumber('G.5', 'Expression; evaluate expressions within the inner-most group first',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1-2-(3-4)');
        $expected = 0;
        TestCheck::assertNumber('G.6', 'Expression; evaluate expressions within the inner-most group first',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1-(2-3)-4');
        $expected = -2;
        TestCheck::assertNumber('G.7', 'Expression; evaluate expressions within the inner-most group first',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(1-2)-3-4');
        $expected = -8;
        TestCheck::assertNumber('G.8', 'Expression; evaluate expressions within the inner-most group first',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1-(2-3-4)');
        $expected = 6;
        TestCheck::assertNumber('G.9', 'Expression; evaluate expressions within the inner-most group first',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(1-2-3)-4');
        $expected = -8;
        TestCheck::assertNumber('G.10', 'Expression; evaluate expressions within the inner-most group first',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(1-2-3-4)');
        $expected = -8;
        TestCheck::assertNumber('G.11', 'Expression; evaluate expressions within the inner-most group first',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(1-2)-(3-4)');
        $expected = 0;
        TestCheck::assertNumber('G.12', 'Expression; evaluate expressions within the inner-most group first',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('((1)-(2)-(3)-(4))');
        $expected = -8;
        TestCheck::assertNumber('G.13', 'Expression; evaluate expressions within the inner-most group first',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('((1)-(2)-(3)-((4)))');
        $expected = -8;
        TestCheck::assertNumber('G.14', 'Expression; evaluate expressions within the inner-most group first',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('((1)-(2)-((3))-(4))');
        $expected = -8;
        TestCheck::assertNumber('G.15', 'Expression; evaluate expressions within the inner-most group first',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('((1)-((2))-(3)-(4))');
        $expected = -8;
        TestCheck::assertNumber('G.16', 'Expression; evaluate expressions within the inner-most group first',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(((1))-(2)-(3)-(4))');
        $expected = -8;
        TestCheck::assertNumber('G.17', 'Expression; evaluate expressions within the inner-most group first',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('((1)-(2)-((3)-(4)))');
        $expected = 0;
        TestCheck::assertNumber('G.18', 'Expression; evaluate expressions within the inner-most group first',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('((1)-((2)-(3))-(4))');
        $expected = -2;
        TestCheck::assertNumber('G.19', 'Expression; evaluate expressions within the inner-most group first',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(((1)-(2))-(3)-(4))');
        $expected = -8;
        TestCheck::assertNumber('G.20', 'Expression; evaluate expressions within the inner-most group first',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('((1)-((2)-(3)-(4)))');
        $expected = 6;
        TestCheck::assertNumber('G.21', 'Expression; evaluate expressions within the inner-most group first',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(((1)-(2))-(3)-(4))');
        $expected = -8;
        TestCheck::assertNumber('G.22', 'Expression; evaluate expressions within the inner-most group first',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(((1)-(2)-(3)-(4)))');
        $expected = -8;
        TestCheck::assertNumber('G.23', 'Expression; evaluate expressions within the inner-most group first',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(((1)-(2))-((3)-(4)))');
        $expected = 0;
        TestCheck::assertNumber('G.24', 'Expression; evaluate expressions within the inner-most group first',  $actual, $expected, $results);



        // TEST: check identity and negation operators; evaluate expressions
        // within inner-most matched group operators first

        // BEGIN TEST
        $actual = TestUtil::evalExpression('+-1');
        $expected = -1;
        TestCheck::assertNumber('H.1', 'Expression; check precedence for unary operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('+-2.1');
        $expected = -2.1;
        TestCheck::assertNumber('H.2', 'Expression; check precedence for unary operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-+1');
        $expected = -1;
        TestCheck::assertNumber('H.3', 'Expression; check precedence for unary operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-+2.1');
        $expected = -2.1;
        TestCheck::assertNumber('H.4', 'Expression; check precedence for unary operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('+-1');
        $expected = -1;
        TestCheck::assertNumber('H.5', 'Expression; check precedence for unary operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('+-2.1');
        $expected = -2.1;
        TestCheck::assertNumber('H.6', 'Expression; check precedence for unary operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('++1');
        $expected = 1;
        TestCheck::assertNumber('H.7', 'Expression; check precedence for unary operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('++2.1');
        $expected = 2.1;
        TestCheck::assertNumber('H.8', 'Expression; check precedence for unary operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('--1');
        $expected = 1;
        TestCheck::assertNumber('H.9', 'Expression; check precedence for unary operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('--2.1');
        $expected = 2.1;
        TestCheck::assertNumber('H.10', 'Expression; check precedence for unary operators',  $actual, $expected, $results);



        // TEST: operator precedence: * / %

        // TODO: note: following is actually a test to see if /* will trigger a comment in the implementation

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"2/*2"');
        $expected = '2/*2';
        TestCheck::assertString('I.1', 'Expression; *,/,% operators are evaluted left to right',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2/*2');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('I.2', 'Expression; *,/,% operators are evaluted left to right',  $actual, $expected, $results);
    }
}
