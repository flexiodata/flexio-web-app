<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  FleY.io App
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
        // note: the following comparison operators are currently supported:
        //     =      (equal)
        //     !=, <> (not equal)
        //     >      (greater than)
        //     >=     (greater than or equal to)
        //     <      (less than)
        //     <=     (less than or equal to)
        //     ~      (matches pattern based on a case-sensitivite match)
        //     ~*     (matches pattern based on a case-insensitivite match)
        //     !~     (does not match pattern based on a case-sensitive match)
        //     !~*    (does not match pattern based on a case-insensitive match)


        // TEST: comparison operators without paired type parameters on left and right side

        // BEGIN TEST
        $actual = TestUtil::evalExpression('=');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.1', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('!=');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.2', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('<>');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.3', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('>');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.4', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('>=');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.5', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('<');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.6', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('<=');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.7', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('~');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.8', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('~*');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.9', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('!~');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.10', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('!~*');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.11', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);



        // TEST: comparison operators with only left parameters

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1=');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.1', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1!=');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.2', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1<>');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.3', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1>');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.4', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1>=');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.5', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1<');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.6', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1<=');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.7', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1~');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.8', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1~*');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.9', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1!~');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.10', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1!~*');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.11', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"=');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.12', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!=');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.13', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<>');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.14', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.15', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">=');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.16', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.17', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<=');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.18', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.19', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~*');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.20', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.21', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~*');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.22', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);



        // TEST: comparison operators with only right parameters

        // BEGIN TEST
        $actual = TestUtil::evalExpression('=1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.1', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('!=1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.2', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('<>1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.3', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('>1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.4', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('>=1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.5', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('<1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.6', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('<=1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.7', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('~1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.8', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('~*1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.9', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('!~1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.10', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('!~*1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.11', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('="a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.12', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('!="a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.13', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('<>"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.14', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('>"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.15', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('>="a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.16', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('<"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.17', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('<="a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.18', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('~"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.19', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('~*"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.20', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('!~"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.21', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('!~*"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.22', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);



        // TEST: comparison operators with left and right mixed parameters

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"=1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.1', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1="a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.2', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"=true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.3', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true="a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.4', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!=1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.5', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1!="a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.6', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!=true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.7', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true!="a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.8', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<>1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.9', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1<>"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.10', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<>true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.11', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true<>"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.12', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.13', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1>"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.14', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.15', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true>"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.16', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">=1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.17', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1>="a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.18', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">=true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.19', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true>="a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.20', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.21', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1<"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.22', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.23', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true<"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.24', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<=1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.25', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1<="a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.26', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<=true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.27', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true<="a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.28', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.29', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1~"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.30', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.31', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true~"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.32', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~*1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.33', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1~*"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.34', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~*true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.35', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true~*"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.36', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.37', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1!~"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.38', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.39', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true!~"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.40', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~*1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.41', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1!~*"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.42', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~*true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.43', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true!~*"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.44', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);



        // TEST: equality operator with numeric comparison

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1=1');
        $expected = true;
        TestCheck::assertBoolean('E.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1=2');
        $expected = false;
        TestCheck::assertBoolean('E.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2=1');
        $expected = false;
        TestCheck::assertBoolean('E.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1=-1');
        $expected = false;
        TestCheck::assertBoolean('E.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1=1');
        $expected = false;
        TestCheck::assertBoolean('E.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1=0.99');
        $expected = false;
        TestCheck::assertBoolean('E.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.99=1');
        $expected = false;
        TestCheck::assertBoolean('E.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.99=0.99');
        $expected = true;
        TestCheck::assertBoolean('E.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.99=0.98');
        $expected = false;
        TestCheck::assertBoolean('E.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.01=-1.01');
        $expected = true;
        TestCheck::assertBoolean('E.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e10=1E10');
        $expected = true;
        TestCheck::assertBoolean('E.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e10=+1e+10');
        $expected = true;
        TestCheck::assertBoolean('E.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e10=1e1');
        $expected = false;
        TestCheck::assertBoolean('E.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.e-1=-1e-1');
        $expected = true;
        TestCheck::assertBoolean('E.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1.1e1=1e1');
        $expected = false;
        TestCheck::assertBoolean('E.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('10000000000=+1e10');
        $expected = true;
        TestCheck::assertBoolean('E.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e+10=10000000000');
        $expected = true;
        TestCheck::assertBoolean('E.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e+10=10000000001');
        $expected = false;
        TestCheck::assertBoolean('E.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.0000000001=+1e-10');
        $expected = true;
        TestCheck::assertBoolean('E.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e-10=0.0000000001');
        $expected = true;
        TestCheck::assertBoolean('E.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e-10=0.00000000009');
        $expected = false;
        TestCheck::assertBoolean('E.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-11000000000=-1.1E10');
        $expected = true;
        TestCheck::assertBoolean('E.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E10=-11000000000');
        $expected = true;
        TestCheck::assertBoolean('E.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E10=-11000000001');
        $expected = false;
        TestCheck::assertBoolean('E.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-0.00000000011=-1.1E-10');
        $expected = true;
        TestCheck::assertBoolean('E.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E-10=-0.00000000011');
        $expected = true;
        TestCheck::assertBoolean('E.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E-10=-0.000000000109');
        $expected = false;
        TestCheck::assertBoolean('E.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: not-equal (!=) operator with numeric comparison

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1!=1');
        $expected = false;
        TestCheck::assertBoolean('F.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1!=2');
        $expected = true;
        TestCheck::assertBoolean('F.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2!=1');
        $expected = true;
        TestCheck::assertBoolean('F.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1!=-1');
        $expected = true;
        TestCheck::assertBoolean('F.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1!=1');
        $expected = true;
        TestCheck::assertBoolean('F.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1!=0.99');
        $expected = true;
        TestCheck::assertBoolean('F.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.99!=1');
        $expected = true;
        TestCheck::assertBoolean('F.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.99!=0.99');
        $expected = false;
        TestCheck::assertBoolean('F.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.99!=0.98');
        $expected = true;
        TestCheck::assertBoolean('F.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.01!=-1.01');
        $expected = false;
        TestCheck::assertBoolean('F.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e10!=1E10');
        $expected = false;
        TestCheck::assertBoolean('F.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e10!=+1e+10');
        $expected = false;
        TestCheck::assertBoolean('F.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e10!=1e1');
        $expected = true;
        TestCheck::assertBoolean('F.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.e-1!=-1e-1');
        $expected = false;
        TestCheck::assertBoolean('F.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1.1e1!=1e1');
        $expected = true;
        TestCheck::assertBoolean('F.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('10000000000!=+1e10');
        $expected = false;
        TestCheck::assertBoolean('F.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e+10!=10000000000');
        $expected = false;
        TestCheck::assertBoolean('F.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e+10!=10000000001');
        $expected = true;
        TestCheck::assertBoolean('F.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.0000000001!=+1e-10');
        $expected = false;
        TestCheck::assertBoolean('F.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e-10!=0.0000000001');
        $expected = false;
        TestCheck::assertBoolean('F.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e-10!=0.00000000009');
        $expected = true;
        TestCheck::assertBoolean('F.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-11000000000!=-1.1E10');
        $expected = false;
        TestCheck::assertBoolean('F.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E10!=-11000000000');
        $expected = false;
        TestCheck::assertBoolean('F.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E10!=-11000000001');
        $expected = true;
        TestCheck::assertBoolean('F.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-0.00000000011!=-1.1E-10');
        $expected = false;
        TestCheck::assertBoolean('F.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E-10!=-0.00000000011');
        $expected = false;
        TestCheck::assertBoolean('F.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E-10!=-0.000000000109');
        $expected = true;
        TestCheck::assertBoolean('F.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: not-equal (<>) operator with numeric comparison

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1<>1');
        $expected = false;
        TestCheck::assertBoolean('G.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1<>2');
        $expected = true;
        TestCheck::assertBoolean('G.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2<>1');
        $expected = true;
        TestCheck::assertBoolean('G.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1<>-1');
        $expected = true;
        TestCheck::assertBoolean('G.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1<>1');
        $expected = true;
        TestCheck::assertBoolean('G.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1<>0.99');
        $expected = true;
        TestCheck::assertBoolean('G.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.99<>1');
        $expected = true;
        TestCheck::assertBoolean('G.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.99<>0.99');
        $expected = false;
        TestCheck::assertBoolean('G.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.99<>0.98');
        $expected = true;
        TestCheck::assertBoolean('G.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.01<>-1.01');
        $expected = false;
        TestCheck::assertBoolean('G.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e10<>1E10');
        $expected = false;
        TestCheck::assertBoolean('G.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e10<>+1e+10');
        $expected = false;
        TestCheck::assertBoolean('G.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e10<>1e1');
        $expected = true;
        TestCheck::assertBoolean('G.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.e-1<>-1e-1');
        $expected = false;
        TestCheck::assertBoolean('G.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1.1e1<>1e1');
        $expected = true;
        TestCheck::assertBoolean('G.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('10000000000<>+1e10');
        $expected = false;
        TestCheck::assertBoolean('G.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e+10<>10000000000');
        $expected = false;
        TestCheck::assertBoolean('G.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e+10<>10000000001');
        $expected = true;
        TestCheck::assertBoolean('G.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.0000000001<>+1e-10');
        $expected = false;
        TestCheck::assertBoolean('G.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e-10<>0.0000000001');
        $expected = false;
        TestCheck::assertBoolean('G.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e-10<>0.00000000009');
        $expected = true;
        TestCheck::assertBoolean('G.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-11000000000<>-1.1E10');
        $expected = false;
        TestCheck::assertBoolean('G.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E10<>-11000000000');
        $expected = false;
        TestCheck::assertBoolean('G.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E10<>-11000000001');
        $expected = true;
        TestCheck::assertBoolean('G.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-0.00000000011<>-1.1E-10');
        $expected = false;
        TestCheck::assertBoolean('G.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E-10<>-0.00000000011');
        $expected = false;
        TestCheck::assertBoolean('G.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E-10<>-0.000000000109');
        $expected = true;
        TestCheck::assertBoolean('G.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: greater-than (>) operator with numeric comparison

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1>1');
        $expected = false;
        TestCheck::assertBoolean('H.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1>2');
        $expected = false;
        TestCheck::assertBoolean('H.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2>1');
        $expected = true;
        TestCheck::assertBoolean('H.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1>-1');
        $expected = true;
        TestCheck::assertBoolean('H.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1>1');
        $expected = false;
        TestCheck::assertBoolean('H.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1>0.99');
        $expected = true;
        TestCheck::assertBoolean('H.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.99>1');
        $expected = false;
        TestCheck::assertBoolean('H.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.99>0.99');
        $expected = false;
        TestCheck::assertBoolean('H.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.99>0.98');
        $expected = true;
        TestCheck::assertBoolean('H.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.01>-1.01');
        $expected = false;
        TestCheck::assertBoolean('H.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e10>1E10');
        $expected = false;
        TestCheck::assertBoolean('H.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e10>+1e+10');
        $expected = false;
        TestCheck::assertBoolean('H.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e10>1e1');
        $expected = true;
        TestCheck::assertBoolean('H.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.e-1>-1e-1');
        $expected = false;
        TestCheck::assertBoolean('H.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1.1e1>1e1');
        $expected = true;
        TestCheck::assertBoolean('H.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('10000000000>+1e10');
        $expected = false;
        TestCheck::assertBoolean('H.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e+10>10000000000');
        $expected = false;
        TestCheck::assertBoolean('H.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e+10>10000000001');
        $expected = false;
        TestCheck::assertBoolean('H.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.0000000001>+1e-10');
        $expected = false;
        TestCheck::assertBoolean('H.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e-10>0.0000000001');
        $expected = false;
        TestCheck::assertBoolean('H.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e-10>0.00000000009');
        $expected = true;
        TestCheck::assertBoolean('H.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-11000000000>-1.1E10');
        $expected = false;
        TestCheck::assertBoolean('H.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E10>-11000000000');
        $expected = false;
        TestCheck::assertBoolean('H.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E10>-11000000001');
        $expected = true;
        TestCheck::assertBoolean('H.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-0.00000000011>-1.1E-10');
        $expected = false;
        TestCheck::assertBoolean('H.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E-10>-0.00000000011');
        $expected = false;
        TestCheck::assertBoolean('H.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E-10>-0.000000000109');
        $expected = false;
        TestCheck::assertBoolean('H.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: greater-than-or-equal-to (>=) operator with numeric comparison

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1>=1');
        $expected = true;
        TestCheck::assertBoolean('I.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1>=2');
        $expected = false;
        TestCheck::assertBoolean('I.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2>=1');
        $expected = true;
        TestCheck::assertBoolean('I.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1>=-1');
        $expected = true;
        TestCheck::assertBoolean('I.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1>=1');
        $expected = false;
        TestCheck::assertBoolean('I.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1>=0.99');
        $expected = true;
        TestCheck::assertBoolean('I.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.99>=1');
        $expected = false;
        TestCheck::assertBoolean('I.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.99>=0.99');
        $expected = true;
        TestCheck::assertBoolean('I.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.99>=0.98');
        $expected = true;
        TestCheck::assertBoolean('I.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.01>=-1.01');
        $expected = true;
        TestCheck::assertBoolean('I.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e10>=1E10');
        $expected = true;
        TestCheck::assertBoolean('I.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e10>=+1e+10');
        $expected = true;
        TestCheck::assertBoolean('I.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e10>=1e1');
        $expected = true;
        TestCheck::assertBoolean('I.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.e-1>=-1e-1');
        $expected = true;
        TestCheck::assertBoolean('I.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1.1e1>=1e1');
        $expected = true;
        TestCheck::assertBoolean('I.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('10000000000>=+1e10');
        $expected = true;
        TestCheck::assertBoolean('I.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e+10>=10000000000');
        $expected = true;
        TestCheck::assertBoolean('I.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e+10>=10000000001');
        $expected = false;
        TestCheck::assertBoolean('I.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.0000000001>=+1e-10');
        $expected = true;
        TestCheck::assertBoolean('I.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e-10>=0.0000000001');
        $expected = true;
        TestCheck::assertBoolean('I.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e-10>=0.00000000009');
        $expected = true;
        TestCheck::assertBoolean('I.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-11000000000>=-1.1E10');
        $expected = true;
        TestCheck::assertBoolean('I.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E10>=-11000000000');
        $expected = true;
        TestCheck::assertBoolean('I.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E10>=-11000000001');
        $expected = true;
        TestCheck::assertBoolean('I.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-0.00000000011>=-1.1E-10');
        $expected = true;
        TestCheck::assertBoolean('I.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E-10>=-0.00000000011');
        $expected = true;
        TestCheck::assertBoolean('I.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E-10>=-0.000000000109');
        $expected = false;
        TestCheck::assertBoolean('I.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: less-than (<) operator with numeric comparison

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1<1');
        $expected = false;
        TestCheck::assertBoolean('J.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1<2');
        $expected = true;
        TestCheck::assertBoolean('J.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2<1');
        $expected = false;
        TestCheck::assertBoolean('J.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1<-1');
        $expected = false;
        TestCheck::assertBoolean('J.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1<1');
        $expected = true;
        TestCheck::assertBoolean('J.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1<0.99');
        $expected = false;
        TestCheck::assertBoolean('J.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.99<1');
        $expected = true;
        TestCheck::assertBoolean('J.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.99<0.99');
        $expected = false;
        TestCheck::assertBoolean('J.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.99<0.98');
        $expected = false;
        TestCheck::assertBoolean('J.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.01<-1.01');
        $expected = false;
        TestCheck::assertBoolean('J.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e10<1E10');
        $expected = false;
        TestCheck::assertBoolean('J.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e10<+1e+10');
        $expected = false;
        TestCheck::assertBoolean('J.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e10<1e1');
        $expected = false;
        TestCheck::assertBoolean('J.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.e-1<-1e-1');
        $expected = false;
        TestCheck::assertBoolean('J.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1.1e1<1e1');
        $expected = false;
        TestCheck::assertBoolean('J.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('10000000000<+1e10');
        $expected = false;
        TestCheck::assertBoolean('J.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e+10<10000000000');
        $expected = false;
        TestCheck::assertBoolean('J.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e+10<10000000001');
        $expected = true;
        TestCheck::assertBoolean('J.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.0000000001<+1e-10');
        $expected = false;
        TestCheck::assertBoolean('J.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e-10<0.0000000001');
        $expected = false;
        TestCheck::assertBoolean('J.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e-10<0.00000000009');
        $expected = false;
        TestCheck::assertBoolean('J.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-11000000000<-1.1E10');
        $expected = false;
        TestCheck::assertBoolean('J.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E10<-11000000000');
        $expected = false;
        TestCheck::assertBoolean('J.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E10<-11000000001');
        $expected = false;
        TestCheck::assertBoolean('J.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-0.00000000011<-1.1E-10');
        $expected = false;
        TestCheck::assertBoolean('J.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E-10<-0.00000000011');
        $expected = false;
        TestCheck::assertBoolean('J.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E-10<-0.000000000109');
        $expected = true;
        TestCheck::assertBoolean('J.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: less-than-or-equal-to (<=) operator with numeric comparison

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1<=1');
        $expected = true;
        TestCheck::assertBoolean('K.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1<=2');
        $expected = true;
        TestCheck::assertBoolean('K.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('2<=1');
        $expected = false;
        TestCheck::assertBoolean('K.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1<=-1');
        $expected = false;
        TestCheck::assertBoolean('K.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1<=1');
        $expected = true;
        TestCheck::assertBoolean('K.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1<=0.99');
        $expected = false;
        TestCheck::assertBoolean('K.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.99<=1');
        $expected = true;
        TestCheck::assertBoolean('K.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.99<=0.99');
        $expected = true;
        TestCheck::assertBoolean('K.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.99<=0.98');
        $expected = false;
        TestCheck::assertBoolean('K.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.01<=-1.01');
        $expected = true;
        TestCheck::assertBoolean('K.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e10<=1E10');
        $expected = true;
        TestCheck::assertBoolean('K.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e10<=+1e+10');
        $expected = true;
        TestCheck::assertBoolean('K.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e10<=1e1');
        $expected = false;
        TestCheck::assertBoolean('K.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.e-1<=-1e-1');
        $expected = true;
        TestCheck::assertBoolean('K.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1.1e1<=1e1');
        $expected = false;
        TestCheck::assertBoolean('K.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('10000000000<=+1e10');
        $expected = true;
        TestCheck::assertBoolean('K.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e+10<=10000000000');
        $expected = true;
        TestCheck::assertBoolean('K.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e+10<=10000000001');
        $expected = true;
        TestCheck::assertBoolean('K.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0.0000000001<=+1e-10');
        $expected = true;
        TestCheck::assertBoolean('K.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e-10<=0.0000000001');
        $expected = true;
        TestCheck::assertBoolean('K.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1e-10<=0.00000000009');
        $expected = false;
        TestCheck::assertBoolean('K.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-11000000000<=-1.1E10');
        $expected = true;
        TestCheck::assertBoolean('K.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E10<=-11000000000');
        $expected = true;
        TestCheck::assertBoolean('K.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E10<=-11000000001');
        $expected = false;
        TestCheck::assertBoolean('K.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-0.00000000011<=-1.1E-10');
        $expected = true;
        TestCheck::assertBoolean('K.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E-10<=-0.00000000011');
        $expected = true;
        TestCheck::assertBoolean('K.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.1E-10<=-0.000000000109');
        $expected = true;
        TestCheck::assertBoolean('K.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: equality operator with string comparison

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"="a"');
        $expected = true;
        TestCheck::assertBoolean('L.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"=\'a\'');
        $expected = true;
        TestCheck::assertBoolean('L.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('\'a\'="a"');
        $expected = true;
        TestCheck::assertBoolean('L.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"="b"');
        $expected = false;
        TestCheck::assertBoolean('L.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"="a"');
        $expected = false;
        TestCheck::assertBoolean('L.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"="A"');
        $expected = false;
        TestCheck::assertBoolean('L.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"A"="a"');
        $expected = false;
        TestCheck::assertBoolean('L.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"="ab"');
        $expected = false;
        TestCheck::assertBoolean('L.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"="a"');
        $expected = false;
        TestCheck::assertBoolean('L.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"A"="ab"');
        $expected = false;
        TestCheck::assertBoolean('L.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"="A"');
        $expected = false;
        TestCheck::assertBoolean('L.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"="aB"');
        $expected = false;
        TestCheck::assertBoolean('L.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"="a"');
        $expected = false;
        TestCheck::assertBoolean('L.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"="ab"');
        $expected = false;
        TestCheck::assertBoolean('L.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"="b"');
        $expected = false;
        TestCheck::assertBoolean('L.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"B"="aB"');
        $expected = false;
        TestCheck::assertBoolean('L.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"="B"');
        $expected = false;
        TestCheck::assertBoolean('L.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"="Ab"');
        $expected = false;
        TestCheck::assertBoolean('L.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"Ab"="b"');
        $expected = false;
        TestCheck::assertBoolean('L.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"="ab"');
        $expected = true;
        TestCheck::assertBoolean('L.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"="Ab"');
        $expected = false;
        TestCheck::assertBoolean('L.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"="ab"');
        $expected = false;
        TestCheck::assertBoolean('L.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"n"="ñ"');
        $expected = false;
        TestCheck::assertBoolean('L.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ñ"="Ñ"');
        $expected = false;
        TestCheck::assertBoolean('L.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"="á"');
        $expected = false;
        TestCheck::assertBoolean('L.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"="à"');
        $expected = false;
        TestCheck::assertBoolean('L.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"="ä"');
        $expected = false;
        TestCheck::assertBoolean('L.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"="â"');
        $expected = false;
        TestCheck::assertBoolean('L.28', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"="ã"');
        $expected = false;
        TestCheck::assertBoolean('L.29', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"="a"');
        $expected = false;
        TestCheck::assertBoolean('L.30', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"="à"');
        $expected = false;
        TestCheck::assertBoolean('L.31', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"="ä"');
        $expected = false;
        TestCheck::assertBoolean('L.32', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"="â"');
        $expected = false;
        TestCheck::assertBoolean('L.33', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"="ã"');
        $expected = false;
        TestCheck::assertBoolean('L.34', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"="á"');
        $expected = false;
        TestCheck::assertBoolean('L.35', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"="a"');
        $expected = false;
        TestCheck::assertBoolean('L.36', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"="ä"');
        $expected = false;
        TestCheck::assertBoolean('L.37', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"="â"');
        $expected = false;
        TestCheck::assertBoolean('L.38', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"="ã"');
        $expected = false;
        TestCheck::assertBoolean('L.39', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"="á"');
        $expected = false;
        TestCheck::assertBoolean('L.40', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"="à"');
        $expected = false;
        TestCheck::assertBoolean('L.41', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"="a"');
        $expected = false;
        TestCheck::assertBoolean('L.42', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"="â"');
        $expected = false;
        TestCheck::assertBoolean('L.43', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"="ã"');
        $expected = false;
        TestCheck::assertBoolean('L.44', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"="á"');
        $expected = false;
        TestCheck::assertBoolean('L.45', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"="à"');
        $expected = false;
        TestCheck::assertBoolean('L.46', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"="ä"');
        $expected = false;
        TestCheck::assertBoolean('L.47', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"="a"');
        $expected = false;
        TestCheck::assertBoolean('L.48', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"="ã"');
        $expected = false;
        TestCheck::assertBoolean('L.49', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"="á"');
        $expected = false;
        TestCheck::assertBoolean('L.50', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"="à"');
        $expected = false;
        TestCheck::assertBoolean('L.51', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"="ä"');
        $expected = false;
        TestCheck::assertBoolean('L.52', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"="â"');
        $expected = false;
        TestCheck::assertBoolean('L.53', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"="a"');
        $expected = false;
        TestCheck::assertBoolean('L.54', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"+"="+"');
        $expected = true;
        TestCheck::assertBoolean('L.55', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"-"="+"');
        $expected = false;
        TestCheck::assertBoolean('L.56', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"+"="-"');
        $expected = false;
        TestCheck::assertBoolean('L.57', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('")"=")"');
        $expected = true;
        TestCheck::assertBoolean('L.58', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"("=")"');
        $expected = false;
        TestCheck::assertBoolean('L.59', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('")"="("');
        $expected = false;
        TestCheck::assertBoolean('L.60', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"?"="?"');
        $expected = true;
        TestCheck::assertBoolean('L.61', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"*"="?"');
        $expected = false;
        TestCheck::assertBoolean('L.62', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"?"="*"');
        $expected = false;
        TestCheck::assertBoolean('L.63', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: not-equal operator with string comparison

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!="a"');
        $expected = false;
        TestCheck::assertBoolean('M.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!=\'a\'');
        $expected = false;
        TestCheck::assertBoolean('M.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('\'a\'!="a"');
        $expected = false;
        TestCheck::assertBoolean('M.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!="b"');
        $expected = true;
        TestCheck::assertBoolean('M.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"!="a"');
        $expected = true;
        TestCheck::assertBoolean('M.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!="A"');
        $expected = true;
        TestCheck::assertBoolean('M.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"A"!="a"');
        $expected = true;
        TestCheck::assertBoolean('M.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!="ab"');
        $expected = true;
        TestCheck::assertBoolean('M.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"!="a"');
        $expected = true;
        TestCheck::assertBoolean('M.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"A"!="ab"');
        $expected = true;
        TestCheck::assertBoolean('M.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"!="A"');
        $expected = true;
        TestCheck::assertBoolean('M.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!="aB"');
        $expected = true;
        TestCheck::assertBoolean('M.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"!="a"');
        $expected = true;
        TestCheck::assertBoolean('M.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"!="ab"');
        $expected = true;
        TestCheck::assertBoolean('M.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"!="b"');
        $expected = true;
        TestCheck::assertBoolean('M.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"B"!="aB"');
        $expected = true;
        TestCheck::assertBoolean('M.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"!="B"');
        $expected = true;
        TestCheck::assertBoolean('M.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"!="Ab"');
        $expected = true;
        TestCheck::assertBoolean('M.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"Ab"!="b"');
        $expected = true;
        TestCheck::assertBoolean('M.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"!="ab"');
        $expected = false;
        TestCheck::assertBoolean('M.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"!="Ab"');
        $expected = true;
        TestCheck::assertBoolean('M.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"!="ab"');
        $expected = true;
        TestCheck::assertBoolean('M.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"n"!="ñ"');
        $expected = true;
        TestCheck::assertBoolean('M.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ñ"!="Ñ"');
        $expected = true;
        TestCheck::assertBoolean('M.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!="á"');
        $expected = true;
        TestCheck::assertBoolean('M.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!="à"');
        $expected = true;
        TestCheck::assertBoolean('M.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!="ä"');
        $expected = true;
        TestCheck::assertBoolean('M.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!="â"');
        $expected = true;
        TestCheck::assertBoolean('M.28', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!="ã"');
        $expected = true;
        TestCheck::assertBoolean('M.29', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"!="a"');
        $expected = true;
        TestCheck::assertBoolean('M.30', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"!="à"');
        $expected = true;
        TestCheck::assertBoolean('M.31', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"!="ä"');
        $expected = true;
        TestCheck::assertBoolean('M.32', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"!="â"');
        $expected = true;
        TestCheck::assertBoolean('M.33', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"!="ã"');
        $expected = true;
        TestCheck::assertBoolean('M.34', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"!="á"');
        $expected = true;
        TestCheck::assertBoolean('M.35', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"!="a"');
        $expected = true;
        TestCheck::assertBoolean('M.36', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"!="ä"');
        $expected = true;
        TestCheck::assertBoolean('M.37', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"!="â"');
        $expected = true;
        TestCheck::assertBoolean('M.38', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"!="ã"');
        $expected = true;
        TestCheck::assertBoolean('M.39', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"!="á"');
        $expected = true;
        TestCheck::assertBoolean('M.40', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"!="à"');
        $expected = true;
        TestCheck::assertBoolean('M.41', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"!="a"');
        $expected = true;
        TestCheck::assertBoolean('M.42', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"!="â"');
        $expected = true;
        TestCheck::assertBoolean('M.43', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"!="ã"');
        $expected = true;
        TestCheck::assertBoolean('M.44', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"!="á"');
        $expected = true;
        TestCheck::assertBoolean('M.45', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"!="à"');
        $expected = true;
        TestCheck::assertBoolean('M.46', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"!="ä"');
        $expected = true;
        TestCheck::assertBoolean('M.47', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"!="a"');
        $expected = true;
        TestCheck::assertBoolean('M.48', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"!="ã"');
        $expected = true;
        TestCheck::assertBoolean('M.49', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"!="á"');
        $expected = true;
        TestCheck::assertBoolean('M.50', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"!="à"');
        $expected = true;
        TestCheck::assertBoolean('M.51', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"!="ä"');
        $expected = true;
        TestCheck::assertBoolean('M.52', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"!="â"');
        $expected = true;
        TestCheck::assertBoolean('M.53', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"!="a"');
        $expected = true;
        TestCheck::assertBoolean('M.54', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"+"!="+"');
        $expected = false;
        TestCheck::assertBoolean('M.55', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"-"!="+"');
        $expected = true;
        TestCheck::assertBoolean('M.56', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"+"!="-"');
        $expected = true;
        TestCheck::assertBoolean('M.57', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('")"!=")"');
        $expected = false;
        TestCheck::assertBoolean('M.58', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"("!=")"');
        $expected = true;
        TestCheck::assertBoolean('M.59', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('")"!="("');
        $expected = true;
        TestCheck::assertBoolean('M.60', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"?"!="?"');
        $expected = false;
        TestCheck::assertBoolean('M.61', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"*"!="?"');
        $expected = true;
        TestCheck::assertBoolean('M.62', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"?"!="*"');
        $expected = true;
        TestCheck::assertBoolean('M.63', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: not-equal operator with string comparison

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<>"a"');
        $expected = false;
        TestCheck::assertBoolean('N.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<>\'a\'');
        $expected = false;
        TestCheck::assertBoolean('N.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('\'a\'<>"a"');
        $expected = false;
        TestCheck::assertBoolean('N.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<>"b"');
        $expected = true;
        TestCheck::assertBoolean('N.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"<>"a"');
        $expected = true;
        TestCheck::assertBoolean('N.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<>"A"');
        $expected = true;
        TestCheck::assertBoolean('N.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"A"<>"a"');
        $expected = true;
        TestCheck::assertBoolean('N.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<>"ab"');
        $expected = true;
        TestCheck::assertBoolean('N.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"<>"a"');
        $expected = true;
        TestCheck::assertBoolean('N.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"A"<>"ab"');
        $expected = true;
        TestCheck::assertBoolean('N.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"<>"A"');
        $expected = true;
        TestCheck::assertBoolean('N.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<>"aB"');
        $expected = true;
        TestCheck::assertBoolean('N.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"<>"a"');
        $expected = true;
        TestCheck::assertBoolean('N.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"<>"ab"');
        $expected = true;
        TestCheck::assertBoolean('N.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"<>"b"');
        $expected = true;
        TestCheck::assertBoolean('N.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"B"<>"aB"');
        $expected = true;
        TestCheck::assertBoolean('N.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"<>"B"');
        $expected = true;
        TestCheck::assertBoolean('N.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"<>"Ab"');
        $expected = true;
        TestCheck::assertBoolean('N.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"Ab"<>"b"');
        $expected = true;
        TestCheck::assertBoolean('N.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"<>"ab"');
        $expected = false;
        TestCheck::assertBoolean('N.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"<>"Ab"');
        $expected = true;
        TestCheck::assertBoolean('N.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"<>"ab"');
        $expected = true;
        TestCheck::assertBoolean('N.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"n"<>"ñ"');
        $expected = true;
        TestCheck::assertBoolean('N.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ñ"<>"Ñ"');
        $expected = true;
        TestCheck::assertBoolean('N.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<>"á"');
        $expected = true;
        TestCheck::assertBoolean('N.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<>"à"');
        $expected = true;
        TestCheck::assertBoolean('N.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<>"ä"');
        $expected = true;
        TestCheck::assertBoolean('N.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<>"â"');
        $expected = true;
        TestCheck::assertBoolean('N.28', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<>"ã"');
        $expected = true;
        TestCheck::assertBoolean('N.29', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"<>"a"');
        $expected = true;
        TestCheck::assertBoolean('N.30', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"<>"à"');
        $expected = true;
        TestCheck::assertBoolean('N.31', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"<>"ä"');
        $expected = true;
        TestCheck::assertBoolean('N.32', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"<>"â"');
        $expected = true;
        TestCheck::assertBoolean('N.33', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"<>"ã"');
        $expected = true;
        TestCheck::assertBoolean('N.34', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"<>"á"');
        $expected = true;
        TestCheck::assertBoolean('N.35', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"<>"a"');
        $expected = true;
        TestCheck::assertBoolean('N.36', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"<>"ä"');
        $expected = true;
        TestCheck::assertBoolean('N.37', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"<>"â"');
        $expected = true;
        TestCheck::assertBoolean('N.38', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"<>"ã"');
        $expected = true;
        TestCheck::assertBoolean('N.39', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"<>"á"');
        $expected = true;
        TestCheck::assertBoolean('N.40', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"<>"à"');
        $expected = true;
        TestCheck::assertBoolean('N.41', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"<>"a"');
        $expected = true;
        TestCheck::assertBoolean('N.42', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"<>"â"');
        $expected = true;
        TestCheck::assertBoolean('N.43', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"<>"ã"');
        $expected = true;
        TestCheck::assertBoolean('N.44', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"<>"á"');
        $expected = true;
        TestCheck::assertBoolean('N.45', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"<>"à"');
        $expected = true;
        TestCheck::assertBoolean('N.46', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"<>"ä"');
        $expected = true;
        TestCheck::assertBoolean('N.47', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"<>"a"');
        $expected = true;
        TestCheck::assertBoolean('N.48', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"<>"ã"');
        $expected = true;
        TestCheck::assertBoolean('N.49', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"<>"á"');
        $expected = true;
        TestCheck::assertBoolean('N.50', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"<>"à"');
        $expected = true;
        TestCheck::assertBoolean('N.51', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"<>"ä"');
        $expected = true;
        TestCheck::assertBoolean('N.52', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"<>"â"');
        $expected = true;
        TestCheck::assertBoolean('N.53', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"<>"a"');
        $expected = true;
        TestCheck::assertBoolean('N.54', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"+"<>"+"');
        $expected = false;
        TestCheck::assertBoolean('N.55', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"-"<>"+"');
        $expected = true;
        TestCheck::assertBoolean('N.56', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"+"<>"-"');
        $expected = true;
        TestCheck::assertBoolean('N.57', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('")"<>")"');
        $expected = false;
        TestCheck::assertBoolean('N.58', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"("<>")"');
        $expected = true;
        TestCheck::assertBoolean('N.59', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('")"<>"("');
        $expected = true;
        TestCheck::assertBoolean('N.60', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"?"<>"?"');
        $expected = false;
        TestCheck::assertBoolean('N.61', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"*"<>"?"');
        $expected = true;
        TestCheck::assertBoolean('N.62', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"?"<>"*"');
        $expected = true;
        TestCheck::assertBoolean('N.63', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: greater-than operator with string comparison

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">"a"');
        $expected = false;
        TestCheck::assertBoolean('O.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">\'a\'');
        $expected = false;
        TestCheck::assertBoolean('O.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('\'a\'>"a"');
        $expected = false;
        TestCheck::assertBoolean('O.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">"b"');
        $expected = false;
        TestCheck::assertBoolean('O.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b">"a"');
        $expected = true;
        TestCheck::assertBoolean('O.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">"A"');
        $expected = false;
        TestCheck::assertBoolean('O.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"A">"a"');
        $expected = true;
        TestCheck::assertBoolean('O.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">"ab"');
        $expected = false;
        TestCheck::assertBoolean('O.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab">"a"');
        $expected = true;
        TestCheck::assertBoolean('O.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"A">"ab"');
        $expected = false;
        TestCheck::assertBoolean('O.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab">"A"');
        $expected = true;
        TestCheck::assertBoolean('O.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">"aB"');
        $expected = false;
        TestCheck::assertBoolean('O.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB">"a"');
        $expected = true;
        TestCheck::assertBoolean('O.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b">"ab"');
        $expected = true;
        TestCheck::assertBoolean('O.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab">"b"');
        $expected = false;
        TestCheck::assertBoolean('O.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"B">"aB"');
        $expected = true;
        TestCheck::assertBoolean('O.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB">"B"');
        $expected = false;
        TestCheck::assertBoolean('O.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b">"Ab"');
        $expected = true;
        TestCheck::assertBoolean('O.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"Ab">"b"');
        $expected = false;
        TestCheck::assertBoolean('O.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab">"ab"');
        $expected = false;
        TestCheck::assertBoolean('O.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab">"Ab"');
        $expected = false;
        TestCheck::assertBoolean('O.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB">"ab"');
        $expected = true;
        TestCheck::assertBoolean('O.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"n">"ñ"');
        $expected = false;
        TestCheck::assertBoolean('O.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ñ">"Ñ"');
        $expected = false;
        TestCheck::assertBoolean('O.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">"á"');
        $expected = false;
        TestCheck::assertBoolean('O.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">"à"');
        $expected = false;
        TestCheck::assertBoolean('O.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">"ä"');
        $expected = false;
        TestCheck::assertBoolean('O.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">"â"');
        $expected = false;
        TestCheck::assertBoolean('O.28', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">"ã"');
        $expected = false;
        TestCheck::assertBoolean('O.29', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á">"a"');
        $expected = true;
        TestCheck::assertBoolean('O.30', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á">"à"');
        $expected = false;
        TestCheck::assertBoolean('O.31', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á">"ä"');
        $expected = false;
        TestCheck::assertBoolean('O.32', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á">"â"');
        $expected = false;
        TestCheck::assertBoolean('O.33', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á">"ã"');
        $expected = false;
        TestCheck::assertBoolean('O.34', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à">"á"');
        $expected = true;
        TestCheck::assertBoolean('O.35', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à">"a"');
        $expected = true;
        TestCheck::assertBoolean('O.36', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à">"ä"');
        $expected = false;
        TestCheck::assertBoolean('O.37', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à">"â"');
        $expected = false;
        TestCheck::assertBoolean('O.38', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à">"ã"');
        $expected = false;
        TestCheck::assertBoolean('O.39', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä">"á"');
        $expected = true;
        TestCheck::assertBoolean('O.40', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä">"à"');
        $expected = true;
        TestCheck::assertBoolean('O.41', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä">"a"');
        $expected = true;
        TestCheck::assertBoolean('O.42', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä">"â"');
        $expected = true;
        TestCheck::assertBoolean('O.43', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä">"ã"');
        $expected = false;
        TestCheck::assertBoolean('O.44', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â">"á"');
        $expected = true;
        TestCheck::assertBoolean('O.45', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â">"à"');
        $expected = true;
        TestCheck::assertBoolean('O.46', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â">"ä"');
        $expected = false;
        TestCheck::assertBoolean('O.47', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â">"a"');
        $expected = true;
        TestCheck::assertBoolean('O.48', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â">"ã"');
        $expected = false;
        TestCheck::assertBoolean('O.49', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã">"á"');
        $expected = true;
        TestCheck::assertBoolean('O.50', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã">"à"');
        $expected = true;
        TestCheck::assertBoolean('O.51', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã">"ä"');
        $expected = true;
        TestCheck::assertBoolean('O.52', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã">"â"');
        $expected = true;
        TestCheck::assertBoolean('O.53', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã">"a"');
        $expected = true;
        TestCheck::assertBoolean('O.54', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"+">"+"');
        $expected = false;
        TestCheck::assertBoolean('O.55', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"-">"+"');
        $expected = false;
        TestCheck::assertBoolean('O.56', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"+">"-"');
        $expected = true;
        TestCheck::assertBoolean('O.57', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('")">")"');
        $expected = false;
        TestCheck::assertBoolean('O.58', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"(">")"');
        $expected = false;
        TestCheck::assertBoolean('O.59', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('")">"("');
        $expected = true;
        TestCheck::assertBoolean('O.60', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"?">"?"');
        $expected = false;
        TestCheck::assertBoolean('O.61', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"*">"?"');
        $expected = true;
        TestCheck::assertBoolean('O.62', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"?">"*"');
        $expected = false;
        TestCheck::assertBoolean('O.63', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: greater-than-or-equal-to operator with string comparison

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">="a"');
        $expected = true;
        TestCheck::assertBoolean('P.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">=\'a\'');
        $expected = true;
        TestCheck::assertBoolean('P.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('\'a\'>="a"');
        $expected = true;
        TestCheck::assertBoolean('P.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">="b"');
        $expected = false;
        TestCheck::assertBoolean('P.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b">="a"');
        $expected = true;
        TestCheck::assertBoolean('P.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">="A"');
        $expected = false;
        TestCheck::assertBoolean('P.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"A">="a"');
        $expected = true;
        TestCheck::assertBoolean('P.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">="ab"');
        $expected = false;
        TestCheck::assertBoolean('P.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab">="a"');
        $expected = true;
        TestCheck::assertBoolean('P.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"A">="ab"');
        $expected = false;
        TestCheck::assertBoolean('P.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab">="A"');
        $expected = true;
        TestCheck::assertBoolean('P.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">="aB"');
        $expected = false;
        TestCheck::assertBoolean('P.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB">="a"');
        $expected = true;
        TestCheck::assertBoolean('P.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b">="ab"');
        $expected = true;
        TestCheck::assertBoolean('P.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab">="b"');
        $expected = false;
        TestCheck::assertBoolean('P.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"B">="aB"');
        $expected = true;
        TestCheck::assertBoolean('P.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB">="B"');
        $expected = false;
        TestCheck::assertBoolean('P.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b">="Ab"');
        $expected = true;
        TestCheck::assertBoolean('P.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"Ab">="b"');
        $expected = false;
        TestCheck::assertBoolean('P.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab">="ab"');
        $expected = true;
        TestCheck::assertBoolean('P.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab">="Ab"');
        $expected = false;
        TestCheck::assertBoolean('P.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB">="ab"');
        $expected = true;
        TestCheck::assertBoolean('P.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"n">="ñ"');
        $expected = false;
        TestCheck::assertBoolean('P.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ñ">="Ñ"');
        $expected = false;
        TestCheck::assertBoolean('P.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">="á"');
        $expected = false;
        TestCheck::assertBoolean('P.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">="à"');
        $expected = false;
        TestCheck::assertBoolean('P.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">="ä"');
        $expected = false;
        TestCheck::assertBoolean('P.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">="â"');
        $expected = false;
        TestCheck::assertBoolean('P.28', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a">="ã"');
        $expected = false;
        TestCheck::assertBoolean('P.29', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á">="a"');
        $expected = true;
        TestCheck::assertBoolean('P.30', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á">="à"');
        $expected = false;
        TestCheck::assertBoolean('P.31', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á">="ä"');
        $expected = false;
        TestCheck::assertBoolean('P.32', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á">="â"');
        $expected = false;
        TestCheck::assertBoolean('P.33', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á">="ã"');
        $expected = false;
        TestCheck::assertBoolean('P.34', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à">="á"');
        $expected = true;
        TestCheck::assertBoolean('P.35', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à">="a"');
        $expected = true;
        TestCheck::assertBoolean('P.36', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à">="ä"');
        $expected = false;
        TestCheck::assertBoolean('P.37', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à">="â"');
        $expected = false;
        TestCheck::assertBoolean('P.38', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à">="ã"');
        $expected = false;
        TestCheck::assertBoolean('P.39', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä">="á"');
        $expected = true;
        TestCheck::assertBoolean('P.40', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä">="à"');
        $expected = true;
        TestCheck::assertBoolean('P.41', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä">="a"');
        $expected = true;
        TestCheck::assertBoolean('P.42', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä">="â"');
        $expected = true;
        TestCheck::assertBoolean('P.43', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä">="ã"');
        $expected = false;
        TestCheck::assertBoolean('P.44', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â">="á"');
        $expected = true;
        TestCheck::assertBoolean('P.45', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â">="à"');
        $expected = true;
        TestCheck::assertBoolean('P.46', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â">="ä"');
        $expected = false;
        TestCheck::assertBoolean('P.47', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â">="a"');
        $expected = true;
        TestCheck::assertBoolean('P.48', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â">="ã"');
        $expected = false;
        TestCheck::assertBoolean('P.49', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã">="á"');
        $expected = true;
        TestCheck::assertBoolean('P.50', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã">="à"');
        $expected = true;
        TestCheck::assertBoolean('P.51', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã">="ä"');
        $expected = true;
        TestCheck::assertBoolean('P.52', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã">="â"');
        $expected = true;
        TestCheck::assertBoolean('P.53', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã">="a"');
        $expected = true;
        TestCheck::assertBoolean('P.54', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"+">="+"');
        $expected = true;
        TestCheck::assertBoolean('P.55', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"-">="+"');
        $expected = false;
        TestCheck::assertBoolean('P.56', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"+">="-"');
        $expected = true;
        TestCheck::assertBoolean('P.57', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('")">=")"');
        $expected = true;
        TestCheck::assertBoolean('P.58', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"(">=")"');
        $expected = false;
        TestCheck::assertBoolean('P.59', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('")">="("');
        $expected = true;
        TestCheck::assertBoolean('P.60', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"?">="?"');
        $expected = true;
        TestCheck::assertBoolean('P.61', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"*">="?"');
        $expected = true;
        TestCheck::assertBoolean('P.62', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"?">="*"');
        $expected = false;
        TestCheck::assertBoolean('P.63', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: less-than operator with string comparison

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<"a"');
        $expected = false;
        TestCheck::assertBoolean('Q.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<\'a\'');
        $expected = false;
        TestCheck::assertBoolean('Q.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('\'a\'<"a"');
        $expected = false;
        TestCheck::assertBoolean('Q.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<"b"');
        $expected = true;
        TestCheck::assertBoolean('Q.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"<"a"');
        $expected = false;
        TestCheck::assertBoolean('Q.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<"A"');
        $expected = true;
        TestCheck::assertBoolean('Q.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"A"<"a"');
        $expected = false;
        TestCheck::assertBoolean('Q.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<"ab"');
        $expected = true;
        TestCheck::assertBoolean('Q.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"<"a"');
        $expected = false;
        TestCheck::assertBoolean('Q.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"A"<"ab"');
        $expected = true;
        TestCheck::assertBoolean('Q.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"<"A"');
        $expected = false;
        TestCheck::assertBoolean('Q.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<"aB"');
        $expected = true;
        TestCheck::assertBoolean('Q.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"<"a"');
        $expected = false;
        TestCheck::assertBoolean('Q.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"<"ab"');
        $expected = false;
        TestCheck::assertBoolean('Q.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"<"b"');
        $expected = true;
        TestCheck::assertBoolean('Q.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"B"<"aB"');
        $expected = false;
        TestCheck::assertBoolean('Q.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"<"B"');
        $expected = true;
        TestCheck::assertBoolean('Q.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"<"Ab"');
        $expected = false;
        TestCheck::assertBoolean('Q.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"Ab"<"b"');
        $expected = true;
        TestCheck::assertBoolean('Q.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"<"ab"');
        $expected = false;
        TestCheck::assertBoolean('Q.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"<"Ab"');
        $expected = true;
        TestCheck::assertBoolean('Q.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"<"ab"');
        $expected = false;
        TestCheck::assertBoolean('Q.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"n"<"ñ"');
        $expected = true;
        TestCheck::assertBoolean('Q.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ñ"<"Ñ"');
        $expected = true;
        TestCheck::assertBoolean('Q.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<"á"');
        $expected = true;
        TestCheck::assertBoolean('Q.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<"à"');
        $expected = true;
        TestCheck::assertBoolean('Q.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<"ä"');
        $expected = true;
        TestCheck::assertBoolean('Q.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<"â"');
        $expected = true;
        TestCheck::assertBoolean('Q.28', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<"ã"');
        $expected = true;
        TestCheck::assertBoolean('Q.29', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"<"a"');
        $expected = false;
        TestCheck::assertBoolean('Q.30', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"<"à"');
        $expected = true;
        TestCheck::assertBoolean('Q.31', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"<"ä"');
        $expected = true;
        TestCheck::assertBoolean('Q.32', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"<"â"');
        $expected = true;
        TestCheck::assertBoolean('Q.33', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"<"ã"');
        $expected = true;
        TestCheck::assertBoolean('Q.34', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"<"á"');
        $expected = false;
        TestCheck::assertBoolean('Q.35', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"<"a"');
        $expected = false;
        TestCheck::assertBoolean('Q.36', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"<"ä"');
        $expected = true;
        TestCheck::assertBoolean('Q.37', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"<"â"');
        $expected = true;
        TestCheck::assertBoolean('Q.38', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"<"ã"');
        $expected = true;
        TestCheck::assertBoolean('Q.39', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"<"á"');
        $expected = false;
        TestCheck::assertBoolean('Q.40', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"<"à"');
        $expected = false;
        TestCheck::assertBoolean('Q.41', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"<"a"');
        $expected = false;
        TestCheck::assertBoolean('Q.42', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"<"â"');
        $expected = false;
        TestCheck::assertBoolean('Q.43', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"<"ã"');
        $expected = true;
        TestCheck::assertBoolean('Q.44', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"<"á"');
        $expected = false;
        TestCheck::assertBoolean('Q.45', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"<"à"');
        $expected = false;
        TestCheck::assertBoolean('Q.46', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"<"ä"');
        $expected = true;
        TestCheck::assertBoolean('Q.47', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"<"a"');
        $expected = false;
        TestCheck::assertBoolean('Q.48', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"<"ã"');
        $expected = true;
        TestCheck::assertBoolean('Q.49', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"<"á"');
        $expected = false;
        TestCheck::assertBoolean('Q.50', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"<"à"');
        $expected = false;
        TestCheck::assertBoolean('Q.51', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"<"ä"');
        $expected = false;
        TestCheck::assertBoolean('Q.52', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"<"â"');
        $expected = false;
        TestCheck::assertBoolean('Q.53', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"<"a"');
        $expected = false;
        TestCheck::assertBoolean('Q.54', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"+"<"+"');
        $expected = false;
        TestCheck::assertBoolean('Q.55', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"-"<"+"');
        $expected = true;
        TestCheck::assertBoolean('Q.56', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"+"<"-"');
        $expected = false;
        TestCheck::assertBoolean('Q.57', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('")"<")"');
        $expected = false;
        TestCheck::assertBoolean('Q.58', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"("<")"');
        $expected = true;
        TestCheck::assertBoolean('Q.59', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('")"<"("');
        $expected = false;
        TestCheck::assertBoolean('Q.60', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"?"<"?"');
        $expected = false;
        TestCheck::assertBoolean('Q.61', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"*"<"?"');
        $expected = false;
        TestCheck::assertBoolean('Q.62', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"?"<"*"');
        $expected = true;
        TestCheck::assertBoolean('Q.63', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: less-than-or-equal-to operator with string comparison

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<="a"');
        $expected = true;
        TestCheck::assertBoolean('R.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<=\'a\'');
        $expected = true;
        TestCheck::assertBoolean('R.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('\'a\'<="a"');
        $expected = true;
        TestCheck::assertBoolean('R.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<="b"');
        $expected = true;
        TestCheck::assertBoolean('R.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"<="a"');
        $expected = false;
        TestCheck::assertBoolean('R.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<="A"');
        $expected = true;
        TestCheck::assertBoolean('R.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"A"<="a"');
        $expected = false;
        TestCheck::assertBoolean('R.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<="ab"');
        $expected = true;
        TestCheck::assertBoolean('R.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"<="a"');
        $expected = false;
        TestCheck::assertBoolean('R.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"A"<="ab"');
        $expected = true;
        TestCheck::assertBoolean('R.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"<="A"');
        $expected = false;
        TestCheck::assertBoolean('R.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<="aB"');
        $expected = true;
        TestCheck::assertBoolean('R.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"<="a"');
        $expected = false;
        TestCheck::assertBoolean('R.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"<="ab"');
        $expected = false;
        TestCheck::assertBoolean('R.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"<="b"');
        $expected = true;
        TestCheck::assertBoolean('R.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"B"<="aB"');
        $expected = false;
        TestCheck::assertBoolean('R.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"<="B"');
        $expected = true;
        TestCheck::assertBoolean('R.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"<="Ab"');
        $expected = false;
        TestCheck::assertBoolean('R.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"Ab"<="b"');
        $expected = true;
        TestCheck::assertBoolean('R.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"<="ab"');
        $expected = true;
        TestCheck::assertBoolean('R.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"<="Ab"');
        $expected = true;
        TestCheck::assertBoolean('R.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"<="ab"');
        $expected = false;
        TestCheck::assertBoolean('R.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"n"<="ñ"');
        $expected = true;
        TestCheck::assertBoolean('R.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ñ"<="Ñ"');
        $expected = true;
        TestCheck::assertBoolean('R.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<="á"');
        $expected = true;
        TestCheck::assertBoolean('R.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<="à"');
        $expected = true;
        TestCheck::assertBoolean('R.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<="ä"');
        $expected = true;
        TestCheck::assertBoolean('R.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<="â"');
        $expected = true;
        TestCheck::assertBoolean('R.28', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"<="ã"');
        $expected = true;
        TestCheck::assertBoolean('R.29', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"<="a"');
        $expected = false;
        TestCheck::assertBoolean('R.30', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"<="à"');
        $expected = true;
        TestCheck::assertBoolean('R.31', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"<="ä"');
        $expected = true;
        TestCheck::assertBoolean('R.32', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"<="â"');
        $expected = true;
        TestCheck::assertBoolean('R.33', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"<="ã"');
        $expected = true;
        TestCheck::assertBoolean('R.34', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"<="á"');
        $expected = false;
        TestCheck::assertBoolean('R.35', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"<="a"');
        $expected = false;
        TestCheck::assertBoolean('R.36', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"<="ä"');
        $expected = true;
        TestCheck::assertBoolean('R.37', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"<="â"');
        $expected = true;
        TestCheck::assertBoolean('R.38', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"<="ã"');
        $expected = true;
        TestCheck::assertBoolean('R.39', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"<="á"');
        $expected = false;
        TestCheck::assertBoolean('R.40', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"<="à"');
        $expected = false;
        TestCheck::assertBoolean('R.41', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"<="a"');
        $expected = false;
        TestCheck::assertBoolean('R.42', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"<="â"');
        $expected = false;
        TestCheck::assertBoolean('R.43', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"<="ã"');
        $expected = true;
        TestCheck::assertBoolean('R.44', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"<="á"');
        $expected = false;
        TestCheck::assertBoolean('R.45', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"<="à"');
        $expected = false;
        TestCheck::assertBoolean('R.46', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"<="ä"');
        $expected = true;
        TestCheck::assertBoolean('R.47', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"<="a"');
        $expected = false;
        TestCheck::assertBoolean('R.48', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"<="ã"');
        $expected = true;
        TestCheck::assertBoolean('R.49', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"<="á"');
        $expected = false;
        TestCheck::assertBoolean('R.50', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"<="à"');
        $expected = false;
        TestCheck::assertBoolean('R.51', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"<="ä"');
        $expected = false;
        TestCheck::assertBoolean('R.52', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"<="â"');
        $expected = false;
        TestCheck::assertBoolean('R.53', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"<="a"');
        $expected = false;
        TestCheck::assertBoolean('R.54', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"+"<="+"');
        $expected = true;
        TestCheck::assertBoolean('R.55', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"-"<="+"');
        $expected = true;
        TestCheck::assertBoolean('R.56', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"+"<="-"');
        $expected = false;
        TestCheck::assertBoolean('R.57', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('")"<=")"');
        $expected = true;
        TestCheck::assertBoolean('R.58', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"("<=")"');
        $expected = true;
        TestCheck::assertBoolean('R.59', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('")"<="("');
        $expected = false;
        TestCheck::assertBoolean('R.60', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"?"<="?"');
        $expected = true;
        TestCheck::assertBoolean('R.61', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"*"<="?"');
        $expected = false;
        TestCheck::assertBoolean('R.62', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"?"<="*"');
        $expected = true;
        TestCheck::assertBoolean('R.63', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: equality operator with boolean comparison

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true=true');
        $expected = true;
        TestCheck::assertBoolean('S.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true=false');
        $expected = false;
        TestCheck::assertBoolean('S.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false=true');
        $expected = false;
        TestCheck::assertBoolean('S.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false=false');
        $expected = true;
        TestCheck::assertBoolean('S.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: not-equal operator with boolean comparison

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true!=true');
        $expected = false;
        TestCheck::assertBoolean('T.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true!=false');
        $expected = true;
        TestCheck::assertBoolean('T.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false!=true');
        $expected = true;
        TestCheck::assertBoolean('T.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false!=false');
        $expected = false;
        TestCheck::assertBoolean('T.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: not-equal operator with boolean comparison

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true<>true');
        $expected = false;
        TestCheck::assertBoolean('U.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true<>false');
        $expected = true;
        TestCheck::assertBoolean('U.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false<>true');
        $expected = true;
        TestCheck::assertBoolean('U.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false<>false');
        $expected = false;
        TestCheck::assertBoolean('U.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: greater-than operator with boolean comparison

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true>true');
        $expected = false;
        TestCheck::assertBoolean('V.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true>false');
        $expected = true;
        TestCheck::assertBoolean('V.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false>true');
        $expected = false;
        TestCheck::assertBoolean('V.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false>false');
        $expected = false;
        TestCheck::assertBoolean('V.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: greater-than-or-equal-to operator with boolean comparison

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true>=true');
        $expected = true;
        TestCheck::assertBoolean('W.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true>=false');
        $expected = true;
        TestCheck::assertBoolean('W.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false>=true');
        $expected = false;
        TestCheck::assertBoolean('W.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false>=false');
        $expected = true;
        TestCheck::assertBoolean('W.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: greater-than-or-equal-to operator with boolean comparison

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true<true');
        $expected = false;
        TestCheck::assertBoolean('X.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true<false');
        $expected = false;
        TestCheck::assertBoolean('X.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false<true');
        $expected = true;
        TestCheck::assertBoolean('X.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false<false');
        $expected = false;
        TestCheck::assertBoolean('X.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: greater-than-or-equal-to operator with boolean comparison

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true<=true');
        $expected = true;
        TestCheck::assertBoolean('Y.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true<=false');
        $expected = false;
        TestCheck::assertBoolean('Y.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false<=true');
        $expected = true;
        TestCheck::assertBoolean('Y.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false<=false');
        $expected = true;
        TestCheck::assertBoolean('Y.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: pattern-match operator with string comparison

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~"a"');
        $expected = true;
        TestCheck::assertBoolean('Z.1', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~\'a\'');
        $expected = true;
        TestCheck::assertBoolean('Z.2', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('\'a\'~"a"');
        $expected = true;
        TestCheck::assertBoolean('Z.3', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~"b"');
        $expected = false;
        TestCheck::assertBoolean('Z.4', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"~"a"');
        $expected = false;
        TestCheck::assertBoolean('Z.5', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~"A"');
        $expected = false;
        TestCheck::assertBoolean('Z.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"A"~"a"');
        $expected = false;
        TestCheck::assertBoolean('Z.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~"ab"');
        $expected = false;
        TestCheck::assertBoolean('Z.8', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"~"a"');
        $expected = true;
        TestCheck::assertBoolean('Z.9', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"A"~"ab"');
        $expected = false;
        TestCheck::assertBoolean('Z.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"~"A"');
        $expected = false;
        TestCheck::assertBoolean('Z.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~"aB"');
        $expected = false;
        TestCheck::assertBoolean('Z.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"~"a"');
        $expected = true;
        TestCheck::assertBoolean('Z.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"~"ab"');
        $expected = false;
        TestCheck::assertBoolean('Z.14', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"~"b"');
        $expected = true;
        TestCheck::assertBoolean('Z.15', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"B"~"aB"');
        $expected = false;
        TestCheck::assertBoolean('Z.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"~"B"');
        $expected = true;
        TestCheck::assertBoolean('Z.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"~"Ab"');
        $expected = false;
        TestCheck::assertBoolean('Z.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"Ab"~"b"');
        $expected = true;
        TestCheck::assertBoolean('Z.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"~"ab"');
        $expected = true;
        TestCheck::assertBoolean('Z.20', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"~"Ab"');
        $expected = false;
        TestCheck::assertBoolean('Z.21', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"~"ab"');
        $expected = false;
        TestCheck::assertBoolean('Z.22', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"n"~"ñ"');
        $expected = false;
        TestCheck::assertBoolean('Z.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ñ"~"Ñ"');
        $expected = false;
        TestCheck::assertBoolean('Z.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~"á"');
        $expected = false;
        TestCheck::assertBoolean('Z.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~"à"');
        $expected = false;
        TestCheck::assertBoolean('Z.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~"ä"');
        $expected = false;
        TestCheck::assertBoolean('Z.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~"â"');
        $expected = false;
        TestCheck::assertBoolean('Z.28', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~"ã"');
        $expected = false;
        TestCheck::assertBoolean('Z.29', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"~"a"');
        $expected = false;
        TestCheck::assertBoolean('Z.30', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"~"à"');
        $expected = false;
        TestCheck::assertBoolean('Z.31', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"~"ä"');
        $expected = false;
        TestCheck::assertBoolean('Z.32', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"~"â"');
        $expected = false;
        TestCheck::assertBoolean('Z.33', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"~"ã"');
        $expected = false;
        TestCheck::assertBoolean('Z.34', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"~"á"');
        $expected = false;
        TestCheck::assertBoolean('Z.35', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"~"a"');
        $expected = false;
        TestCheck::assertBoolean('Z.36', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"~"ä"');
        $expected = false;
        TestCheck::assertBoolean('Z.37', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"~"â"');
        $expected = false;
        TestCheck::assertBoolean('Z.38', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"~"ã"');
        $expected = false;
        TestCheck::assertBoolean('Z.39', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"~"á"');
        $expected = false;
        TestCheck::assertBoolean('Z.40', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"~"à"');
        $expected = false;
        TestCheck::assertBoolean('Z.41', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"~"a"');
        $expected = false;
        TestCheck::assertBoolean('Z.42', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"~"â"');
        $expected = false;
        TestCheck::assertBoolean('Z.43', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"~"ã"');
        $expected = false;
        TestCheck::assertBoolean('Z.44', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"~"á"');
        $expected = false;
        TestCheck::assertBoolean('Z.45', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"~"à"');
        $expected = false;
        TestCheck::assertBoolean('Z.46', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"~"ä"');
        $expected = false;
        TestCheck::assertBoolean('Z.47', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"~"a"');
        $expected = false;
        TestCheck::assertBoolean('Z.48', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"~"ã"');
        $expected = false;
        TestCheck::assertBoolean('Z.49', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"~"á"');
        $expected = false;
        TestCheck::assertBoolean('Z.50', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"~"à"');
        $expected = false;
        TestCheck::assertBoolean('Z.51', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"~"ä"');
        $expected = false;
        TestCheck::assertBoolean('Z.52', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"~"â"');
        $expected = false;
        TestCheck::assertBoolean('Z.53', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"~"a"');
        $expected = false;
        TestCheck::assertBoolean('Z.54', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"+"~"\+"');
        $expected = true;
        TestCheck::assertBoolean('Z.55', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"-"~"\+"');
        $expected = false;
        TestCheck::assertBoolean('Z.56', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"+"~"-"');
        $expected = false;
        TestCheck::assertBoolean('Z.57', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('")"~"\)"');
        $expected = true;
        TestCheck::assertBoolean('Z.58', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"("~"\)"');
        $expected = false;
        TestCheck::assertBoolean('Z.59', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('")"~"\("');
        $expected = false;
        TestCheck::assertBoolean('Z.60', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"?"~"\?"');
        $expected = true;
        TestCheck::assertBoolean('Z.61', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"*"~"\?"');
        $expected = false;
        TestCheck::assertBoolean('Z.62', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"?"~"\*"');
        $expected = false;
        TestCheck::assertBoolean('Z.63', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"abc"~"^a"');
        $expected = true;
        TestCheck::assertBoolean('Z.64', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"abc"~"(b|d)"');
        $expected = true;
        TestCheck::assertBoolean('Z.65', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"abc"~"^(b|d)"');
        $expected = false;
        TestCheck::assertBoolean('Z.66', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"abc"~"^[A-Z]"');
        $expected = false;
        TestCheck::assertBoolean('Z.67', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: pattern-match operator with string comparison

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~*"a"');
        $expected = true;
        TestCheck::assertBoolean('AA.1', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~*\'a\'');
        $expected = true;
        TestCheck::assertBoolean('AA.2', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('\'a\'~*"a"');
        $expected = true;
        TestCheck::assertBoolean('AA.3', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~*"b"');
        $expected = false;
        TestCheck::assertBoolean('AA.4', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"~*"a"');
        $expected = false;
        TestCheck::assertBoolean('AA.5', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~*"A"');
        $expected = true;
        TestCheck::assertBoolean('AA.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"A"~*"a"');
        $expected = true;
        TestCheck::assertBoolean('AA.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~*"ab"');
        $expected = false;
        TestCheck::assertBoolean('AA.8', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"~*"a"');
        $expected = true;
        TestCheck::assertBoolean('AA.9', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"A"~*"ab"');
        $expected = false;
        TestCheck::assertBoolean('AA.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"~*"A"');
        $expected = true;
        TestCheck::assertBoolean('AA.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~*"aB"');
        $expected = false;
        TestCheck::assertBoolean('AA.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"~*"a"');
        $expected = true;
        TestCheck::assertBoolean('AA.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"~*"ab"');
        $expected = false;
        TestCheck::assertBoolean('AA.14', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"~*"b"');
        $expected = true;
        TestCheck::assertBoolean('AA.15', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"B"~*"aB"');
        $expected = false;
        TestCheck::assertBoolean('AA.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"~*"B"');
        $expected = true;
        TestCheck::assertBoolean('AA.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"~*"Ab"');
        $expected = false;
        TestCheck::assertBoolean('AA.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"Ab"~*"b"');
        $expected = true;
        TestCheck::assertBoolean('AA.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"~*"ab"');
        $expected = true;
        TestCheck::assertBoolean('AA.20', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"~*"Ab"');
        $expected = true;
        TestCheck::assertBoolean('AA.21', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"~*"ab"');
        $expected = true;
        TestCheck::assertBoolean('AA.22', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"n"~*"ñ"');
        $expected = false;
        TestCheck::assertBoolean('AA.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ñ"~*"Ñ"');
        $expected = true;
        TestCheck::assertBoolean('AA.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~*"á"');
        $expected = false;
        TestCheck::assertBoolean('AA.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~*"à"');
        $expected = false;
        TestCheck::assertBoolean('AA.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~*"ä"');
        $expected = false;
        TestCheck::assertBoolean('AA.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~*"â"');
        $expected = false;
        TestCheck::assertBoolean('AA.28', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"~*"ã"');
        $expected = false;
        TestCheck::assertBoolean('AA.29', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"~*"a"');
        $expected = false;
        TestCheck::assertBoolean('AA.30', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"~*"à"');
        $expected = false;
        TestCheck::assertBoolean('AA.31', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"~*"ä"');
        $expected = false;
        TestCheck::assertBoolean('AA.32', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"~*"â"');
        $expected = false;
        TestCheck::assertBoolean('AA.33', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"~*"ã"');
        $expected = false;
        TestCheck::assertBoolean('AA.34', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"~*"á"');
        $expected = false;
        TestCheck::assertBoolean('AA.35', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"~*"a"');
        $expected = false;
        TestCheck::assertBoolean('AA.36', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"~*"ä"');
        $expected = false;
        TestCheck::assertBoolean('AA.37', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"~*"â"');
        $expected = false;
        TestCheck::assertBoolean('AA.38', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"~*"ã"');
        $expected = false;
        TestCheck::assertBoolean('AA.39', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"~*"á"');
        $expected = false;
        TestCheck::assertBoolean('AA.40', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"~*"à"');
        $expected = false;
        TestCheck::assertBoolean('AA.41', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"~*"a"');
        $expected = false;
        TestCheck::assertBoolean('AA.42', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"~*"â"');
        $expected = false;
        TestCheck::assertBoolean('AA.43', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"~*"ã"');
        $expected = false;
        TestCheck::assertBoolean('AA.44', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"~*"á"');
        $expected = false;
        TestCheck::assertBoolean('AA.45', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"~*"à"');
        $expected = false;
        TestCheck::assertBoolean('AA.46', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"~*"ä"');
        $expected = false;
        TestCheck::assertBoolean('AA.47', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"~*"a"');
        $expected = false;
        TestCheck::assertBoolean('AA.48', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"~*"ã"');
        $expected = false;
        TestCheck::assertBoolean('AA.49', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"~*"á"');
        $expected = false;
        TestCheck::assertBoolean('AA.50', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"~*"à"');
        $expected = false;
        TestCheck::assertBoolean('AA.51', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"~*"ä"');
        $expected = false;
        TestCheck::assertBoolean('AA.52', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"~*"â"');
        $expected = false;
        TestCheck::assertBoolean('AA.53', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"~*"a"');
        $expected = false;
        TestCheck::assertBoolean('AA.54', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"+"~*"\+"');
        $expected = true;
        TestCheck::assertBoolean('AA.55', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"-"~*"\+"');
        $expected = false;
        TestCheck::assertBoolean('AA.56', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"+"~*"-"');
        $expected = false;
        TestCheck::assertBoolean('AA.57', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('")"~*"\)"');
        $expected = true;
        TestCheck::assertBoolean('AA.58', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"("~*"\)"');
        $expected = false;
        TestCheck::assertBoolean('AA.59', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('")"~*"\("');
        $expected = false;
        TestCheck::assertBoolean('AA.60', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"?"~*"\?"');
        $expected = true;
        TestCheck::assertBoolean('AA.61', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"*"~*"\?"');
        $expected = false;
        TestCheck::assertBoolean('AA.62', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"?"~*"\*"');
        $expected = false;
        TestCheck::assertBoolean('AA.63', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"abc"~*"^a"');
        $expected = true;
        TestCheck::assertBoolean('AA.64', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"abc"~*"(b|d)"');
        $expected = true;
        TestCheck::assertBoolean('AA.65', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"abc"~*"^(b|d)"');
        $expected = false;
        TestCheck::assertBoolean('AA.66', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"abc"~*"^[A-Z]"');
        $expected = true;
        TestCheck::assertBoolean('AA.67', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: pattern-match operator with string comparison

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~"a"');
        $expected = false;
        TestCheck::assertBoolean('AB.1', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~\'a\'');
        $expected = false;
        TestCheck::assertBoolean('AB.2', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('\'a\'!~"a"');
        $expected = false;
        TestCheck::assertBoolean('AB.3', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~"b"');
        $expected = true;
        TestCheck::assertBoolean('AB.4', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"!~"a"');
        $expected = true;
        TestCheck::assertBoolean('AB.5', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~"A"');
        $expected = true;
        TestCheck::assertBoolean('AB.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"A"!~"a"');
        $expected = true;
        TestCheck::assertBoolean('AB.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~"ab"');
        $expected = true;
        TestCheck::assertBoolean('AB.8', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"!~"a"');
        $expected = false;
        TestCheck::assertBoolean('AB.9', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"A"!~"ab"');
        $expected = true;
        TestCheck::assertBoolean('AB.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"!~"A"');
        $expected = true;
        TestCheck::assertBoolean('AB.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~"aB"');
        $expected = true;
        TestCheck::assertBoolean('AB.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"!~"a"');
        $expected = false;
        TestCheck::assertBoolean('AB.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"!~"ab"');
        $expected = true;
        TestCheck::assertBoolean('AB.14', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"!~"b"');
        $expected = false;
        TestCheck::assertBoolean('AB.15', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"B"!~"aB"');
        $expected = true;
        TestCheck::assertBoolean('AB.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"!~"B"');
        $expected = false;
        TestCheck::assertBoolean('AB.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"!~"Ab"');
        $expected = true;
        TestCheck::assertBoolean('AB.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"Ab"!~"b"');
        $expected = false;
        TestCheck::assertBoolean('AB.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"!~"ab"');
        $expected = false;
        TestCheck::assertBoolean('AB.20', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"!~"Ab"');
        $expected = true;
        TestCheck::assertBoolean('AB.21', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"!~"ab"');
        $expected = true;
        TestCheck::assertBoolean('AB.22', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"n"!~"ñ"');
        $expected = true;
        TestCheck::assertBoolean('AB.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ñ"!~"Ñ"');
        $expected = true;
        TestCheck::assertBoolean('AB.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~"á"');
        $expected = true;
        TestCheck::assertBoolean('AB.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~"à"');
        $expected = true;
        TestCheck::assertBoolean('AB.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~"ä"');
        $expected = true;
        TestCheck::assertBoolean('AB.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~"â"');
        $expected = true;
        TestCheck::assertBoolean('AB.28', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~"ã"');
        $expected = true;
        TestCheck::assertBoolean('AB.29', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"!~"a"');
        $expected = true;
        TestCheck::assertBoolean('AB.30', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"!~"à"');
        $expected = true;
        TestCheck::assertBoolean('AB.31', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"!~"ä"');
        $expected = true;
        TestCheck::assertBoolean('AB.32', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"!~"â"');
        $expected = true;
        TestCheck::assertBoolean('AB.33', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"!~"ã"');
        $expected = true;
        TestCheck::assertBoolean('AB.34', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"!~"á"');
        $expected = true;
        TestCheck::assertBoolean('AB.35', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"!~"a"');
        $expected = true;
        TestCheck::assertBoolean('AB.36', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"!~"ä"');
        $expected = true;
        TestCheck::assertBoolean('AB.37', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"!~"â"');
        $expected = true;
        TestCheck::assertBoolean('AB.38', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"!~"ã"');
        $expected = true;
        TestCheck::assertBoolean('AB.39', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"!~"á"');
        $expected = true;
        TestCheck::assertBoolean('AB.40', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"!~"à"');
        $expected = true;
        TestCheck::assertBoolean('AB.41', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"!~"a"');
        $expected = true;
        TestCheck::assertBoolean('AB.42', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"!~"â"');
        $expected = true;
        TestCheck::assertBoolean('AB.43', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"!~"ã"');
        $expected = true;
        TestCheck::assertBoolean('AB.44', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"!~"á"');
        $expected = true;
        TestCheck::assertBoolean('AB.45', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"!~"à"');
        $expected = true;
        TestCheck::assertBoolean('AB.46', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"!~"ä"');
        $expected = true;
        TestCheck::assertBoolean('AB.47', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"!~"a"');
        $expected = true;
        TestCheck::assertBoolean('AB.48', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"!~"ã"');
        $expected = true;
        TestCheck::assertBoolean('AB.49', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"!~"á"');
        $expected = true;
        TestCheck::assertBoolean('AB.50', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"!~"à"');
        $expected = true;
        TestCheck::assertBoolean('AB.51', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"!~"ä"');
        $expected = true;
        TestCheck::assertBoolean('AB.52', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"!~"â"');
        $expected = true;
        TestCheck::assertBoolean('AB.53', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"!~"a"');
        $expected = true;
        TestCheck::assertBoolean('AB.54', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"+"!~"\+"');
        $expected = false;
        TestCheck::assertBoolean('AB.55', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"-"!~"\+"');
        $expected = true;
        TestCheck::assertBoolean('AB.56', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"+"!~"-"');
        $expected = true;
        TestCheck::assertBoolean('AB.57', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('")"!~"\)"');
        $expected = false;
        TestCheck::assertBoolean('AB.58', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"("!~"\)"');
        $expected = true;
        TestCheck::assertBoolean('AB.59', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('")"!~"\("');
        $expected = true;
        TestCheck::assertBoolean('AB.60', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"?"!~"\?"');
        $expected = false;
        TestCheck::assertBoolean('AB.61', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"*"!~"\?"');
        $expected = true;
        TestCheck::assertBoolean('AB.62', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"?"!~"\*"');
        $expected = true;
        TestCheck::assertBoolean('AB.63', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"abc"!~"^a"');
        $expected = false;
        TestCheck::assertBoolean('AB.64', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"abc"!~"(b|d)"');
        $expected = false;
        TestCheck::assertBoolean('AB.65', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"abc"!~"^(b|d)"');
        $expected = true;
        TestCheck::assertBoolean('AB.66', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"abc"!~"^[A-Z]"');
        $expected = true;
        TestCheck::assertBoolean('AB.67', 'Expression; return result of logical comparison',  $actual, $expected, $results);


        // TEST: pattern-match operator with string comparison

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~*"a"');
        $expected = false;
        TestCheck::assertBoolean('AC.1', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~*\'a\'');
        $expected = false;
        TestCheck::assertBoolean('AC.2', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('\'a\'!~*"a"');
        $expected = false;
        TestCheck::assertBoolean('AC.3', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~*"b"');
        $expected = true;
        TestCheck::assertBoolean('AC.4', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"!~*"a"');
        $expected = true;
        TestCheck::assertBoolean('AC.5', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~*"A"');
        $expected = false;
        TestCheck::assertBoolean('AC.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"A"!~*"a"');
        $expected = false;
        TestCheck::assertBoolean('AC.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~*"ab"');
        $expected = true;
        TestCheck::assertBoolean('AC.8', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"!~*"a"');
        $expected = false;
        TestCheck::assertBoolean('AC.9', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"A"!~*"ab"');
        $expected = true;
        TestCheck::assertBoolean('AC.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"!~*"A"');
        $expected = false;
        TestCheck::assertBoolean('AC.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~*"aB"');
        $expected = true;
        TestCheck::assertBoolean('AC.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"!~*"a"');
        $expected = false;
        TestCheck::assertBoolean('AC.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"!~*"ab"');
        $expected = true;
        TestCheck::assertBoolean('AC.14', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"!~*"b"');
        $expected = false;
        TestCheck::assertBoolean('AC.15', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"B"!~*"aB"');
        $expected = true;
        TestCheck::assertBoolean('AC.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"!~*"B"');
        $expected = false;
        TestCheck::assertBoolean('AC.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"b"!~*"Ab"');
        $expected = true;
        TestCheck::assertBoolean('AC.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"Ab"!~*"b"');
        $expected = false;
        TestCheck::assertBoolean('AC.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"!~*"ab"');
        $expected = false;
        TestCheck::assertBoolean('AC.20', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ab"!~*"Ab"');
        $expected = false;
        TestCheck::assertBoolean('AC.21', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"aB"!~*"ab"');
        $expected = false;
        TestCheck::assertBoolean('AC.22', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"n"!~*"ñ"');
        $expected = true;
        TestCheck::assertBoolean('AC.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ñ"!~*"Ñ"');
        $expected = false;
        TestCheck::assertBoolean('AC.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~*"á"');
        $expected = true;
        TestCheck::assertBoolean('AC.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~*"à"');
        $expected = true;
        TestCheck::assertBoolean('AC.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~*"ä"');
        $expected = true;
        TestCheck::assertBoolean('AC.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~*"â"');
        $expected = true;
        TestCheck::assertBoolean('AC.28', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a"!~*"ã"');
        $expected = true;
        TestCheck::assertBoolean('AC.29', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"!~*"a"');
        $expected = true;
        TestCheck::assertBoolean('AC.30', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"!~*"à"');
        $expected = true;
        TestCheck::assertBoolean('AC.31', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"!~*"ä"');
        $expected = true;
        TestCheck::assertBoolean('AC.32', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"!~*"â"');
        $expected = true;
        TestCheck::assertBoolean('AC.33', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"á"!~*"ã"');
        $expected = true;
        TestCheck::assertBoolean('AC.34', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"!~*"á"');
        $expected = true;
        TestCheck::assertBoolean('AC.35', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"!~*"a"');
        $expected = true;
        TestCheck::assertBoolean('AC.36', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"!~*"ä"');
        $expected = true;
        TestCheck::assertBoolean('AC.37', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"!~*"â"');
        $expected = true;
        TestCheck::assertBoolean('AC.38', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"à"!~*"ã"');
        $expected = true;
        TestCheck::assertBoolean('AC.39', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"!~*"á"');
        $expected = true;
        TestCheck::assertBoolean('AC.40', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"!~*"à"');
        $expected = true;
        TestCheck::assertBoolean('AC.41', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"!~*"a"');
        $expected = true;
        TestCheck::assertBoolean('AC.42', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"!~*"â"');
        $expected = true;
        TestCheck::assertBoolean('AC.43', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ä"!~*"ã"');
        $expected = true;
        TestCheck::assertBoolean('AC.44', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"!~*"á"');
        $expected = true;
        TestCheck::assertBoolean('AC.45', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"!~*"à"');
        $expected = true;
        TestCheck::assertBoolean('AC.46', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"!~*"ä"');
        $expected = true;
        TestCheck::assertBoolean('AC.47', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"!~*"a"');
        $expected = true;
        TestCheck::assertBoolean('AC.48', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"â"!~*"ã"');
        $expected = true;
        TestCheck::assertBoolean('AC.49', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"!~*"á"');
        $expected = true;
        TestCheck::assertBoolean('AC.50', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"!~*"à"');
        $expected = true;
        TestCheck::assertBoolean('AC.51', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"!~*"ä"');
        $expected = true;
        TestCheck::assertBoolean('AC.52', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"!~*"â"');
        $expected = true;
        TestCheck::assertBoolean('AC.53', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"ã"!~*"a"');
        $expected = true;
        TestCheck::assertBoolean('AC.54', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"+"!~*"\+"');
        $expected = false;
        TestCheck::assertBoolean('AC.55', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"-"!~*"\+"');
        $expected = true;
        TestCheck::assertBoolean('AC.56', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"+"!~*"-"');
        $expected = true;
        TestCheck::assertBoolean('AC.57', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('")"!~*"\)"');
        $expected = false;
        TestCheck::assertBoolean('AC.58', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"("!~*"\)"');
        $expected = true;
        TestCheck::assertBoolean('AC.59', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('")"!~*"\("');
        $expected = true;
        TestCheck::assertBoolean('AC.60', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"?"!~*"\?"');
        $expected = false;
        TestCheck::assertBoolean('AC.61', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"*"!~*"\?"');
        $expected = true;
        TestCheck::assertBoolean('AC.62', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"?"!~*"\*"');
        $expected = true;
        TestCheck::assertBoolean('AC.63', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"abc"!~*"^a"');
        $expected = false;
        TestCheck::assertBoolean('AC.64', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"abc"!~*"(b|d)"');
        $expected = false;
        TestCheck::assertBoolean('AC.65', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"abc"!~*"^(b|d)"');
        $expected = true;
        TestCheck::assertBoolean('AC.66', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"abc"!~*"^[A-Z]"');
        $expected = false;
        TestCheck::assertBoolean('AC.67', 'Expression; return result of logical comparison',  $actual, $expected, $results);
    }
}
