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


declare(strict_types=1);
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
        $actual = \Flexio\Tests\Util::evalExpression('+');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.1', 'Expression; fail if arithemtic operator doesn\'t have numbers on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.2', 'Expression; fail if arithemtic operator doesn\'t have numbers on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('*');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.3', 'Expression; fail if arithemtic operator doesn\'t have numbers on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('/');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.4', 'Expression; fail if arithemtic operator doesn\'t have numbers on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('%');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.5', 'Expression; fail if arithemtic operator doesn\'t have numbers on the left and right side',  $actual, $expected, $results);



        // TEST: arithmetic operators with only left numeric parameters

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1+');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.1', 'Expression; fail if arithemtic operator doesn\'t have numbers on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1-');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.2', 'Expression; fail if arithemtic operator doesn\'t have numbers on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1*');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.3', 'Expression; fail if arithemtic operator doesn\'t have numbers on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1/');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.4', 'Expression; fail if arithemtic operator doesn\'t have numbers on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1%');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.5', 'Expression; fail if arithemtic operator doesn\'t have numbers on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e1+');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.6', 'Expression; fail if arithemtic operator doesn\'t have numbers on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e1-');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.7', 'Expression; fail if arithemtic operator doesn\'t have numbers on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e1*');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.8', 'Expression; fail if arithemtic operator doesn\'t have numbers on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e1/');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.9', 'Expression; fail if arithemtic operator doesn\'t have numbers on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e1%');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.10', 'Expression; fail if arithemtic operator doesn\'t have numbers on the right side',  $actual, $expected, $results);



        // TEST: unary/arithmetic operators with only left numeric parameters

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('+ 1');
        $expected = 1;
        \Flexio\Tests\Check::assertNumber('C.1', 'Expression; + operator on the left side of a number is the identity operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('- 1');
        $expected = -1;
        \Flexio\Tests\Check::assertNumber('C.2', 'Expression; + operator on the left side of a number is the negation operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('*1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.3', 'Expression; fail if arithemtic operator doesn\'t have numbers on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('/1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.4', 'Expression; fail if arithemtic operator doesn\'t have numbers on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('%1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.5', 'Expression; fail if arithemtic operator doesn\'t have numbers on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('+ 1e1');
        $expected = (float)10;
        \Flexio\Tests\Check::assertNumber('C.6', 'Expression; + operator on the left side of a number is the identity operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('- 1e1');
        $expected = (float)-10;
        \Flexio\Tests\Check::assertNumber('C.7', 'Expression; + operator on the left side of a number is the negation operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('*1e1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.8', 'Expression; fail if arithemtic operator doesn\'t have numbers on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('/1e1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.9', 'Expression; fail if arithemtic operator doesn\'t have numbers on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('%1e1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.10', 'Expression; fail if arithemtic operator doesn\'t have numbers on the left side',  $actual, $expected, $results);



        // TEST: unary operators with right parameter, where one of them is non-numeric

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('+true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.1', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.2', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('+"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.3', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.4', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);



        // TEST: arithmetic operators with both left and right parameters, but where one of them is non-numeric

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1+true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.1', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true+1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.2', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1+"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.3', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"+1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.4', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1-true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.5', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true-1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.6', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1-"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.7', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"-1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.8', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1*true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.9', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true*1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.10', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1*"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.11', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"*1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.12', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1/true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.13', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true/1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.14', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1/"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.15', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"/1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.16', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1%true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.17', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true%1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.18', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1%"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.20', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"%1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.21', 'Expression; fail if one of the operands is non-numeric',  $actual, $expected, $results);



        // TEST: arithmetic operators with both left and right numeric parameters; calculate the result

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1+0');
        $expected = 1;
        \Flexio\Tests\Check::assertNumber('F.1', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0+1');
        $expected = 1;
        \Flexio\Tests\Check::assertNumber('F.2', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2+3');
        $expected = 5;
        \Flexio\Tests\Check::assertNumber('F.3', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('3+2');
        $expected = 5;
        \Flexio\Tests\Check::assertNumber('F.4', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2+-3');
        $expected = -1;
        \Flexio\Tests\Check::assertNumber('F.5', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-3+2');
        $expected = -1;
        \Flexio\Tests\Check::assertNumber('F.6', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.2+3');
        $expected = 4.2;
        \Flexio\Tests\Check::assertNumber('F.7', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1+2.3');
        $expected = 3.3;
        \Flexio\Tests\Check::assertNumber('F.8', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.2+3.4');
        $expected = 4.6;
        \Flexio\Tests\Check::assertNumber('F.9', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e2-2');
        $expected = (float)98;
        \Flexio\Tests\Check::assertNumber('F.10', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2-1e2');
        $expected = (float)-98;
        \Flexio\Tests\Check::assertNumber('F.11', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e-2+2');
        $expected = 2.01;
        \Flexio\Tests\Check::assertNumber('F.12', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2+1e-2');
        $expected = 2.01;
        \Flexio\Tests\Check::assertNumber('F.13', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e+2-2');
        $expected = (float)98;
        \Flexio\Tests\Check::assertNumber('F.14', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2-1e+2');
        $expected = (float)-98;
        \Flexio\Tests\Check::assertNumber('F.15', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('1+e2');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.16', 'Expression; fail if one of the operands isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e2-2e1');
        $expected = (float)80;
        \Flexio\Tests\Check::assertNumber('F.17', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e2+2e1');
        $expected = (float)120;
        \Flexio\Tests\Check::assertNumber('F.18', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e-2+2e1');
        $expected = 20.01;
        \Flexio\Tests\Check::assertNumber('F.19', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e+2-2e1');
        $expected = (float)80;
        \Flexio\Tests\Check::assertNumber('F.20', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);



        // TEST: arithmetic operators with both left and right numeric parameters; calculate the result

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1-0');
        $expected = 1;
        \Flexio\Tests\Check::assertNumber('G.1', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0-1');
        $expected = -1;
        \Flexio\Tests\Check::assertNumber('G.2', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2-3');
        $expected = -1;
        \Flexio\Tests\Check::assertNumber('G.3', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('3-2');
        $expected = 1;
        \Flexio\Tests\Check::assertNumber('G.4', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2-+3');
        $expected = -1;
        \Flexio\Tests\Check::assertNumber('G.5', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('+3-2');
        $expected = 1;
        \Flexio\Tests\Check::assertNumber('G.6', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.2-3');
        $expected = -1.8;
        \Flexio\Tests\Check::assertNumber('G.7', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1-2.3');
        $expected = -1.3;
        \Flexio\Tests\Check::assertNumber('G.8', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.2-3.4');
        $expected = -2.2;
        \Flexio\Tests\Check::assertNumber('G.9', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);



        // TEST: arithmetic operators with both left and right numeric parameters; calculate the result

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1*0');
        $expected = 0;
        \Flexio\Tests\Check::assertNumber('H.1', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0*1');
        $expected = 0;
        \Flexio\Tests\Check::assertNumber('H.2', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2*3');
        $expected = 6;
        \Flexio\Tests\Check::assertNumber('H.3', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('3*2');
        $expected = 6;
        \Flexio\Tests\Check::assertNumber('H.4', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2*-3');
        $expected = -6;
        \Flexio\Tests\Check::assertNumber('H.5', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-3*2');
        $expected = -6;
        \Flexio\Tests\Check::assertNumber('H.6', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.2*3');
        $expected = 3.6;
        \Flexio\Tests\Check::assertNumber('H.7', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1*2.3');
        $expected = 2.3;
        \Flexio\Tests\Check::assertNumber('H.8', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.2*3.4');
        $expected = 4.08;
        \Flexio\Tests\Check::assertNumber('H.9', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e2*2');
        $expected = (float)200;
        \Flexio\Tests\Check::assertNumber('H.10', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2*1e2');
        $expected = (float)200;
        \Flexio\Tests\Check::assertNumber('H.11', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e-2*2');
        $expected = 0.02;
        \Flexio\Tests\Check::assertNumber('H.12', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2*1e-2');
        $expected = 0.02;
        \Flexio\Tests\Check::assertNumber('H.13', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e*2-2');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('H.14', 'Expression; fail if one of the operands isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2-1e*2');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('H.15', 'Expression; fail if one of the operands isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2e1*1e2');
        $expected = (float)2000;
        \Flexio\Tests\Check::assertNumber('H.16', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2e1*-1e2');
        $expected = (float)-2000;
        \Flexio\Tests\Check::assertNumber('H.17', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1e2*2e1');
        $expected = (float)-2000;
        \Flexio\Tests\Check::assertNumber('H.18', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);



        // TEST: arithmetic operators with both left and right numeric parameters; calculate the result

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1/0');
        $expected = null;
        \Flexio\Tests\Check::assertNull('I.1', 'Expression; return null when a number is divided by zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0/1');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('I.2', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('4/2');
        $expected = (float)2;
        \Flexio\Tests\Check::assertNumber('I.3', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2/4');
        $expected = (float)0.5;
        \Flexio\Tests\Check::assertNumber('I.4', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('4/-2');
        $expected = (float)-2;
        \Flexio\Tests\Check::assertNumber('I.5', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-2/4');
        $expected = (float)-0.5;
        \Flexio\Tests\Check::assertNumber('I.6', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('6/1.5');
        $expected = (float)4;
        \Flexio\Tests\Check::assertNumber('I.7', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.5/6');
        $expected = (float)0.25;
        \Flexio\Tests\Check::assertNumber('I.8', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.5*1.25');
        $expected = (float)1.875;
        \Flexio\Tests\Check::assertNumber('I.9', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e2/2');
        $expected = (float)50;
        \Flexio\Tests\Check::assertNumber('I.10', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2/1e2');
        $expected = (float)0.02;
        \Flexio\Tests\Check::assertNumber('I.11', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e-2/2');
        $expected = 0.005;
        \Flexio\Tests\Check::assertNumber('I.12', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2/1e-2');
        $expected = (float)200;
        \Flexio\Tests\Check::assertNumber('I.13', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e/2-2');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('I.14', 'Expression; fail if one of the operands isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2-1e/2');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('I.15', 'Expression; fail if one of the operands isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2e1/1e2');
        $expected = (float)0.20;
        \Flexio\Tests\Check::assertNumber('I.16', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2e1/-1e2');
        $expected = (float)-0.2;
        \Flexio\Tests\Check::assertNumber('I.17', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1e2/2e1');
        $expected = (float)-5;
        \Flexio\Tests\Check::assertNumber('I.18', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);



        // TEST: arithmetic operators with both left and right numeric parameters; calculate the result;
        // note: x%y is equivalent to mod(x,y) and returns the remainder of x divided by y; this is
        // different behavior than the classical mathematical mod(x,y) function (expressed as
        // x - y * FLOOR(x/y)) when x or y are negative

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1%0');
        $expected = null;
        \Flexio\Tests\Check::assertNull('J.1', 'Expression; return null when a calculating a modulo with zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0%1');
        $expected = 0;
        \Flexio\Tests\Check::assertNumber('J.2', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2%3');
        $expected = 2;
        \Flexio\Tests\Check::assertNumber('J.3', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('3%2');
        $expected = 1;
        \Flexio\Tests\Check::assertNumber('J.4', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2%-3');
        $expected = 2;
        \Flexio\Tests\Check::assertNumber('J.5', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-3%2');
        $expected = -1;
        \Flexio\Tests\Check::assertNumber('J.6', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2.4%3');
        $expected = 2.4;
        \Flexio\Tests\Check::assertNumber('J.7', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('3%2.4');
        $expected = 0.6;
        \Flexio\Tests\Check::assertNumber('J.8', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('15%2.4');
        $expected = 0.6;
        \Flexio\Tests\Check::assertNumber('J.9', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('11%4');
        $expected = 3;
        \Flexio\Tests\Check::assertNumber('J.10', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('11%-4');
        $expected = 3;
        \Flexio\Tests\Check::assertNumber('J.11', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-11%4');
        $expected = -3;
        \Flexio\Tests\Check::assertNumber('J.12', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-11%-4');
        $expected = -3;
        \Flexio\Tests\Check::assertNumber('J.13', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e2%2');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('J.14', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2%1e2');
        $expected = (float)2;
        \Flexio\Tests\Check::assertNumber('J.15', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e-2%2');
        $expected = 0.01;
        \Flexio\Tests\Check::assertNumber('J.16', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2%1e-2');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('J.17', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e%2-2');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('J.18', 'Expression; fail if one of the operands isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2-1e%2');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('J.19', 'Expression; fail if one of the operands isn\'t formatted correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2e1%1e2');
        $expected = (float)20;
        \Flexio\Tests\Check::assertNumber('J.20', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2e1%-1e2');
        $expected = (float)20;
        \Flexio\Tests\Check::assertNumber('J.21', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1e2%2e1');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('J.22', 'Expression; calculate the result when the operands are both numeric',  $actual, $expected, $results);
    }
}
