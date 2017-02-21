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


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // note: the following numeric operators are currently supported:
        //     + (unary positive, addition)
        //     - (unary negative, subtraction)
        //     * (multiplication)
        //     / (division)
        //     % (modulo)


        // TEST: arithmetic operators without any numeric parameters

        // BEGIN TEST
        $actual = TestUtil::evalExpression('+');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.1', 'Expression; fail if arithemtic operator doesn\'t have numbers on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.2', 'Expression; fail if arithemtic operator doesn\'t have numbers on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('*');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.3', 'Expression; fail if arithemtic operator doesn\'t have numbers on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('/');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.4', 'Expression; fail if arithemtic operator doesn\'t have numbers on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('%');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.5', 'Expression; fail if arithemtic operator doesn\'t have numbers on the left and right side',  $actual, $expected, $results);



        // TEST: arithmetic operators with only left numeric parameters

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1+');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.1', 'Expression; fail if arithemtic operator doesn\'t have numbers on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1-');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.2', 'Expression; fail if arithemtic operator doesn\'t have numbers on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1*');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.3', 'Expression; fail if arithemtic operator doesn\'t have numbers on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1/');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.4', 'Expression; fail if arithemtic operator doesn\'t have numbers on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1%');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.5', 'Expression; fail if arithemtic operator doesn\'t have numbers on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e1+');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.6', 'Expression; fail if arithemtic operator doesn\'t have numbers on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e1-');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.7', 'Expression; fail if arithemtic operator doesn\'t have numbers on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e1*');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.8', 'Expression; fail if arithemtic operator doesn\'t have numbers on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e1/');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.9', 'Expression; fail if arithemtic operator doesn\'t have numbers on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e1%');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.10', 'Expression; fail if arithemtic operator doesn\'t have numbers on the right side',  $actual, $expected, $results);



        // TEST: unary/arithmetic operators with only left numeric parameters

        // BEGIN TEST
        $actual = TestUtil::evalExpression('+ 1');
        $expected = 1;
        TestCheck::assertNumber('C.1', 'Expression; + operator on the left side of a number is the identity operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('- 1');
        $expected = -1;
        TestCheck::assertNumber('C.2', 'Expression; + operator on the left side of a number is the negation operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('*1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.3', 'Expression; fail if arithemtic operator doesn\'t have numbers on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('/1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.4', 'Expression; fail if arithemtic operator doesn\'t have numbers on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('%1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.5', 'Expression; fail if arithemtic operator doesn\'t have numbers on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('+ 1e1');
        $expected = (float)10;
        TestCheck::assertNumber('C.6', 'Expression; + operator on the left side of a number is the identity operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('- 1e1');
        $expected = (float)-10;
        TestCheck::assertNumber('C.7', 'Expression; + operator on the left side of a number is the negation operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('*1e1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.8', 'Expression; fail if arithemtic operator doesn\'t have numbers on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('/1e1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.9', 'Expression; fail if arithemtic operator doesn\'t have numbers on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('%1e1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.10', 'Expression; fail if arithemtic operator doesn\'t have numbers on the left side',  $actual, $expected, $results);



        // TEST: unary operators with right parameter, where one of them is non-numeric

        // BEGIN TEST
        $actual = TestUtil::evalExpression('+true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.1', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.2', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('+"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.3', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.4', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);



        // TEST: arithmetic operators with both left and right parameters, but where one of them is non-numeric

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1+true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.1', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true+1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.2', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1+"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.3', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"+1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.4', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1-true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.5', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true-1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.6', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1-"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.7', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"-1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.8', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1*true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.9', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true*1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.10', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1*"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.11', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"*1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.12', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1/true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.13', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true/1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.14', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1/"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.15', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"/1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.16', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1%true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.17', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true%1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.18', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1%"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.20', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"%1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.21', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);



        // TEST: arithmetic operators with both left and right numeric parameters; calculate the result

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1+0');
        $expected = 1;
        TestCheck::assertNumber('F.1', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0+1');
        $expected = 1;
        TestCheck::assertNumber('F.2', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2+3');
        $expected = 5;
        TestCheck::assertNumber('F.3', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('3+2');
        $expected = 5;
        TestCheck::assertNumber('F.4', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2+-3');
        $expected = -1;
        TestCheck::assertNumber('F.5', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-3+2');
        $expected = -1;
        TestCheck::assertNumber('F.6', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1.2+3');
        $expected = 4.2;
        TestCheck::assertNumber('F.7', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1+2.3');
        $expected = 3.3;
        TestCheck::assertNumber('F.8', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1.2+3.4');
        $expected = 4.6;
        TestCheck::assertNumber('F.9', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e2-2');
        $expected = (float)98;
        TestCheck::assertNumber('F.10', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2-1e2');
        $expected = (float)-98;
        TestCheck::assertNumber('F.11', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e-2+2');
        $expected = 2.01;
        TestCheck::assertNumber('F.12', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2+1e-2');
        $expected = 2.01;
        TestCheck::assertNumber('F.13', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e+2-2');
        $expected = (float)98;
        TestCheck::assertNumber('F.14', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2-1e+2');
        $expected = (float)-98;
        TestCheck::assertNumber('F.15', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        $actual = TestUtil::evalExpression('1+e2');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('F.16', 'Expression; fail if one of the operands isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e2-2e1');
        $expected = (float)80;
        TestCheck::assertNumber('F.17', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e2+2e1');
        $expected = (float)120;
        TestCheck::assertNumber('F.18', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e-2+2e1');
        $expected = 20.01;
        TestCheck::assertNumber('F.19', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e+2-2e1');
        $expected = (float)80;
        TestCheck::assertNumber('F.20', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);



        // TEST: arithmetic operators with both left and right numeric parameters; calculate the result

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1-0');
        $expected = 1;
        TestCheck::assertNumber('G.1', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0-1');
        $expected = -1;
        TestCheck::assertNumber('G.2', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2-3');
        $expected = -1;
        TestCheck::assertNumber('G.3', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('3-2');
        $expected = 1;
        TestCheck::assertNumber('G.4', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2-+3');
        $expected = -1;
        TestCheck::assertNumber('G.5', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('+3-2');
        $expected = 1;
        TestCheck::assertNumber('G.6', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1.2-3');
        $expected = -1.8;
        TestCheck::assertNumber('G.7', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1-2.3');
        $expected = -1.3;
        TestCheck::assertNumber('G.8', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1.2-3.4');
        $expected = -2.2;
        TestCheck::assertNumber('G.9', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);



        // TEST: arithmetic operators with both left and right numeric parameters; calculate the result

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1*0');
        $expected = 0;
        TestCheck::assertNumber('H.1', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0*1');
        $expected = 0;
        TestCheck::assertNumber('H.2', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2*3');
        $expected = 6;
        TestCheck::assertNumber('H.3', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('3*2');
        $expected = 6;
        TestCheck::assertNumber('H.4', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2*-3');
        $expected = -6;
        TestCheck::assertNumber('H.5', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-3*2');
        $expected = -6;
        TestCheck::assertNumber('H.6', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1.2*3');
        $expected = 3.6;
        TestCheck::assertNumber('H.7', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1*2.3');
        $expected = 2.3;
        TestCheck::assertNumber('H.8', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1.2*3.4');
        $expected = 4.08;
        TestCheck::assertNumber('H.9', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e2*2');
        $expected = (float)200;
        TestCheck::assertNumber('H.10', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2*1e2');
        $expected = (float)200;
        TestCheck::assertNumber('H.11', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e-2*2');
        $expected = 0.02;
        TestCheck::assertNumber('H.12', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2*1e-2');
        $expected = 0.02;
        TestCheck::assertNumber('H.13', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e*2-2');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('H.14', 'Expression; fail if one of the operands isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2-1e*2');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('H.15', 'Expression; fail if one of the operands isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2e1*1e2');
        $expected = (float)2000;
        TestCheck::assertNumber('H.16', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2e1*-1e2');
        $expected = (float)-2000;
        TestCheck::assertNumber('H.17', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1e2*2e1');
        $expected = (float)-2000;
        TestCheck::assertNumber('H.18', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);



        // TEST: arithmetic operators with both left and right numeric parameters; calculate the result

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1/0');
        $expected = null;
        TestCheck::assertNull('I.1', 'Expression; return null when a number is divided by zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0/1');
        $expected = (float)0;
        TestCheck::assertNumber('I.2', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('4/2');
        $expected = (float)2;
        TestCheck::assertNumber('I.3', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2/4');
        $expected = (float)0.5;
        TestCheck::assertNumber('I.4', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('4/-2');
        $expected = (float)-2;
        TestCheck::assertNumber('I.5', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-2/4');
        $expected = (float)-0.5;
        TestCheck::assertNumber('I.6', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('6/1.5');
        $expected = (float)4;
        TestCheck::assertNumber('I.7', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1.5/6');
        $expected = (float)0.25;
        TestCheck::assertNumber('I.8', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1.5*1.25');
        $expected = (float)1.875;
        TestCheck::assertNumber('I.9', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e2/2');
        $expected = (float)50;
        TestCheck::assertNumber('I.10', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2/1e2');
        $expected = (float)0.02;
        TestCheck::assertNumber('I.11', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e-2/2');
        $expected = 0.005;
        TestCheck::assertNumber('I.12', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2/1e-2');
        $expected = (float)200;
        TestCheck::assertNumber('I.13', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e/2-2');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('I.14', 'Expression; fail if one of the operands isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2-1e/2');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('I.15', 'Expression; fail if one of the operands isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2e1/1e2');
        $expected = (float)0.20;
        TestCheck::assertNumber('I.16', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2e1/-1e2');
        $expected = (float)-0.2;
        TestCheck::assertNumber('I.17', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1e2/2e1');
        $expected = (float)-5;
        TestCheck::assertNumber('I.18', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);



        // TEST: arithmetic operators with both left and right numeric parameters; calculate the result;
        // note: x%y is equivalent to mod(x,y) and returns the remainder of x divided by y; this is
        // different behavior than the classical mathematical mod(x,y) function (expressed as
        // x - y * FLOOR(x/y)) when x or y are negative

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1%0');
        $expected = null;
        TestCheck::assertNull('J.1', 'Expression; return null when a calculating a modulo with zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0%1');
        $expected = 0;
        TestCheck::assertNumber('J.2', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2%3');
        $expected = 2;
        TestCheck::assertNumber('J.3', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('3%2');
        $expected = 1;
        TestCheck::assertNumber('J.4', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2%-3');
        $expected = 2;
        TestCheck::assertNumber('J.5', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-3%2');
        $expected = -1;
        TestCheck::assertNumber('J.6', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2.4%3');
        $expected = 2.4;
        TestCheck::assertNumber('J.7', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('3%2.4');
        $expected = 0.6;
        TestCheck::assertNumber('J.8', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('15%2.4');
        $expected = 0.6;
        TestCheck::assertNumber('J.9', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('11%4');
        $expected = 3;
        TestCheck::assertNumber('J.10', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('11%-4');
        $expected = 3;
        TestCheck::assertNumber('J.11', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-11%4');
        $expected = -3;
        TestCheck::assertNumber('J.12', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-11%-4');
        $expected = -3;
        TestCheck::assertNumber('J.13', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e2%2');
        $expected = (float)0;
        TestCheck::assertNumber('J.14', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2%1e2');
        $expected = (float)2;
        TestCheck::assertNumber('J.15', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e-2%2');
        $expected = 0.01;
        TestCheck::assertNumber('J.16', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2%1e-2');
        $expected = (float)0;
        TestCheck::assertNumber('J.17', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e%2-2');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('J.18', 'Expression; fail if one of the operands isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2-1e%2');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('J.19', 'Expression; fail if one of the operands isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2e1%1e2');
        $expected = (float)20;
        TestCheck::assertNumber('J.20', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2e1%-1e2');
        $expected = (float)20;
        TestCheck::assertNumber('J.21', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1e2%2e1');
        $expected = (float)0;
        TestCheck::assertNumber('J.22', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);
    }
}
