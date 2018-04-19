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


declare(strict_types=1);
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
        $actual = \Flexio\Tests\Util::evalExpression('=');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.1', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('!=');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.2', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('<>');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.3', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('>');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.4', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('>=');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.5', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('<');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.6', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('<=');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.7', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('~');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.8', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('~*');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.9', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('!~');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.10', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('!~*');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.11', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);



        // TEST: comparison operators with only left parameters

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1=');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.1', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1!=');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.2', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1<>');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.3', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1>');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.4', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1>=');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.5', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1<');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.6', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1<=');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.7', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1~');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.8', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1~*');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.9', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1!~');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.10', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1!~*');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.11', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"=');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.12', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!=');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.13', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<>');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.14', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.15', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">=');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.16', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.17', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<=');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.18', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.19', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~*');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.20', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.21', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~*');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.22', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);



        // TEST: comparison operators with only right parameters

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('=1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.1', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('!=1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.2', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('<>1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.3', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('>1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.4', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('>=1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.5', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('<1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.6', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('<=1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.7', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('~1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.8', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('~*1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.9', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('!~1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.10', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('!~*1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.11', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('="a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.12', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('!="a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.13', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('<>"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.14', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('>"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.15', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('>="a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.16', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('<"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.17', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('<="a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.18', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('~"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.19', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('~*"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.20', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('!~"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.21', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('!~*"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.22', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);



        // TEST: comparison operators with left and right mixed parameters

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"=1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.1', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1="a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.2', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"=true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.3', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true="a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.4', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!=1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.5', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1!="a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.6', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!=true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.7', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true!="a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.8', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<>1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.9', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1<>"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.10', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<>true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.11', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true<>"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.12', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.13', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1>"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.14', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.15', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true>"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.16', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">=1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.17', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1>="a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.18', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">=true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.19', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true>="a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.20', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.21', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1<"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.22', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.23', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true<"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.24', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<=1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.25', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1<="a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.26', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<=true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.27', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true<="a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.28', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.29', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1~"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.30', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.31', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true~"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.32', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~*1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.33', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1~*"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.34', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~*true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.35', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true~*"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.36', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.37', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1!~"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.38', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.39', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true!~"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.40', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~*1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.41', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1!~*"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.42', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~*true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.43', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true!~*"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.44', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);



        // TEST: equality operator with numeric comparison

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1=1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1=2');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2=1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1=-1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1=1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1=0.99');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.99=1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.99=0.99');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.99=0.98');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.01=-1.01');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e10=1E10');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e10=+1e+10');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e10=1e1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.e-1=-1e-1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.1e1=1e1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('10000000000=+1e10');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e+10=10000000000');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e+10=10000000001');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.0000000001=+1e-10');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e-10=0.0000000001');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e-10=0.00000000009');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-11000000000=-1.1E10');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E10=-11000000000');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E10=-11000000001');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-0.00000000011=-1.1E-10');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E-10=-0.00000000011');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E-10=-0.000000000109');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: not-equal (!=) operator with numeric comparison

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1!=1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1!=2');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2!=1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1!=-1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1!=1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1!=0.99');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.99!=1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.99!=0.99');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.99!=0.98');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.01!=-1.01');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e10!=1E10');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e10!=+1e+10');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e10!=1e1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.e-1!=-1e-1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.1e1!=1e1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('10000000000!=+1e10');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e+10!=10000000000');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e+10!=10000000001');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.0000000001!=+1e-10');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e-10!=0.0000000001');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e-10!=0.00000000009');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-11000000000!=-1.1E10');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E10!=-11000000000');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E10!=-11000000001');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-0.00000000011!=-1.1E-10');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E-10!=-0.00000000011');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E-10!=-0.000000000109');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: not-equal (<>) operator with numeric comparison

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1<>1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1<>2');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2<>1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1<>-1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1<>1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1<>0.99');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.99<>1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.99<>0.99');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.99<>0.98');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.01<>-1.01');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e10<>1E10');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e10<>+1e+10');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e10<>1e1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.e-1<>-1e-1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.1e1<>1e1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('10000000000<>+1e10');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e+10<>10000000000');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e+10<>10000000001');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.0000000001<>+1e-10');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e-10<>0.0000000001');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e-10<>0.00000000009');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-11000000000<>-1.1E10');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E10<>-11000000000');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E10<>-11000000001');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-0.00000000011<>-1.1E-10');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E-10<>-0.00000000011');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E-10<>-0.000000000109');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: greater-than (>) operator with numeric comparison

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1>1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('H.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1>2');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('H.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2>1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1>-1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1>1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('H.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1>0.99');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.99>1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('H.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.99>0.99');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('H.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.99>0.98');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.01>-1.01');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('H.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e10>1E10');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('H.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e10>+1e+10');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('H.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e10>1e1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.e-1>-1e-1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('H.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.1e1>1e1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('10000000000>+1e10');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('H.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e+10>10000000000');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('H.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e+10>10000000001');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('H.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.0000000001>+1e-10');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('H.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e-10>0.0000000001');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('H.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e-10>0.00000000009');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-11000000000>-1.1E10');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('H.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E10>-11000000000');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('H.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E10>-11000000001');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-0.00000000011>-1.1E-10');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('H.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E-10>-0.00000000011');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('H.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E-10>-0.000000000109');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('H.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: greater-than-or-equal-to (>=) operator with numeric comparison

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1>=1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('I.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1>=2');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('I.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2>=1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('I.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1>=-1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('I.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1>=1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('I.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1>=0.99');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('I.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.99>=1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('I.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.99>=0.99');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('I.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.99>=0.98');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('I.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.01>=-1.01');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('I.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e10>=1E10');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('I.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e10>=+1e+10');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('I.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e10>=1e1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('I.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.e-1>=-1e-1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('I.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.1e1>=1e1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('I.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('10000000000>=+1e10');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('I.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e+10>=10000000000');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('I.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e+10>=10000000001');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('I.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.0000000001>=+1e-10');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('I.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e-10>=0.0000000001');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('I.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e-10>=0.00000000009');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('I.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-11000000000>=-1.1E10');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('I.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E10>=-11000000000');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('I.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E10>=-11000000001');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('I.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-0.00000000011>=-1.1E-10');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('I.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E-10>=-0.00000000011');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('I.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E-10>=-0.000000000109');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('I.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: less-than (<) operator with numeric comparison

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1<1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('J.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1<2');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('J.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2<1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('J.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1<-1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('J.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1<1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('J.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1<0.99');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('J.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.99<1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('J.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.99<0.99');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('J.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.99<0.98');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('J.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.01<-1.01');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('J.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e10<1E10');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('J.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e10<+1e+10');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('J.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e10<1e1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('J.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.e-1<-1e-1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('J.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.1e1<1e1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('J.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('10000000000<+1e10');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('J.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e+10<10000000000');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('J.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e+10<10000000001');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('J.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.0000000001<+1e-10');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('J.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e-10<0.0000000001');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('J.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e-10<0.00000000009');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('J.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-11000000000<-1.1E10');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('J.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E10<-11000000000');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('J.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E10<-11000000001');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('J.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-0.00000000011<-1.1E-10');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('J.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E-10<-0.00000000011');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('J.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E-10<-0.000000000109');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('J.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: less-than-or-equal-to (<=) operator with numeric comparison

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1<=1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('K.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1<=2');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('K.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('2<=1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('K.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1<=-1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('K.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1<=1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('K.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1<=0.99');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('K.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.99<=1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('K.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.99<=0.99');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('K.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.99<=0.98');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('K.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.01<=-1.01');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('K.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e10<=1E10');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('K.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e10<=+1e+10');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('K.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e10<=1e1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('K.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.e-1<=-1e-1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('K.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.1e1<=1e1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('K.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('10000000000<=+1e10');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('K.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e+10<=10000000000');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('K.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e+10<=10000000001');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('K.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.0000000001<=+1e-10');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('K.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e-10<=0.0000000001');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('K.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e-10<=0.00000000009');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('K.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-11000000000<=-1.1E10');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('K.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E10<=-11000000000');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('K.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E10<=-11000000001');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('K.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-0.00000000011<=-1.1E-10');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('K.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E-10<=-0.00000000011');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('K.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.1E-10<=-0.000000000109');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('K.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: equality operator with string comparison

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"="a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('L.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"=\'a\'');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('L.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('\'a\'="a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('L.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"="b"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"="a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"="A"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"A"="a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"="ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"="a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"A"="ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"="A"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"="aB"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"="a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"="ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"="b"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"B"="aB"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"="B"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"="Ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"Ab"="b"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"="ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('L.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"="Ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"="ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"n"="ñ"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ñ"="Ñ"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"="á"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"="à"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"="ä"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"="â"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.28', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"="ã"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.29', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"="a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.30', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"="à"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.31', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"="ä"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.32', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"="â"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.33', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"="ã"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.34', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"="á"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.35', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"="a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.36', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"="ä"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.37', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"="â"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.38', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"="ã"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.39', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"="á"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.40', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"="à"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.41', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"="a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.42', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"="â"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.43', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"="ã"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.44', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"="á"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.45', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"="à"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.46', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"="ä"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.47', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"="a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.48', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"="ã"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.49', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"="á"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.50', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"="à"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.51', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"="ä"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.52', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"="â"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.53', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"="a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.54', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"+"="+"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('L.55', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"-"="+"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.56', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"+"="-"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.57', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('")"=")"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('L.58', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"("=")"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.59', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('")"="("');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.60', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"?"="?"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('L.61', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"*"="?"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.62', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"?"="*"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.63', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: not-equal operator with string comparison

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!="a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('M.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!=\'a\'');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('M.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('\'a\'!="a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('M.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!="b"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"!="a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!="A"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"A"!="a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!="ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"!="a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"A"!="ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"!="A"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!="aB"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"!="a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"!="ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"!="b"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"B"!="aB"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"!="B"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"!="Ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"Ab"!="b"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"!="ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('M.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"!="Ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"!="ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"n"!="ñ"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ñ"!="Ñ"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!="á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!="à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!="ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!="â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.28', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!="ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.29', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"!="a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.30', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"!="à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.31', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"!="ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.32', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"!="â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.33', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"!="ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.34', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"!="á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.35', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"!="a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.36', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"!="ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.37', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"!="â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.38', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"!="ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.39', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"!="á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.40', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"!="à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.41', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"!="a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.42', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"!="â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.43', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"!="ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.44', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"!="á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.45', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"!="à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.46', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"!="ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.47', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"!="a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.48', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"!="ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.49', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"!="á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.50', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"!="à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.51', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"!="ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.52', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"!="â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.53', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"!="a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.54', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"+"!="+"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('M.55', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"-"!="+"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.56', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"+"!="-"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.57', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('")"!=")"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('M.58', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"("!=")"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.59', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('")"!="("');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.60', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"?"!="?"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('M.61', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"*"!="?"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.62', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"?"!="*"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('M.63', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: not-equal operator with string comparison

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<>"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('N.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<>\'a\'');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('N.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('\'a\'<>"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('N.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<>"b"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"<>"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<>"A"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"A"<>"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<>"ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"<>"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"A"<>"ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"<>"A"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<>"aB"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"<>"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"<>"ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"<>"b"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"B"<>"aB"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"<>"B"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"<>"Ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"Ab"<>"b"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"<>"ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('N.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"<>"Ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"<>"ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"n"<>"ñ"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ñ"<>"Ñ"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<>"á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<>"à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<>"ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<>"â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.28', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<>"ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.29', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"<>"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.30', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"<>"à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.31', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"<>"ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.32', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"<>"â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.33', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"<>"ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.34', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"<>"á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.35', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"<>"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.36', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"<>"ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.37', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"<>"â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.38', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"<>"ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.39', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"<>"á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.40', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"<>"à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.41', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"<>"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.42', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"<>"â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.43', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"<>"ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.44', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"<>"á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.45', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"<>"à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.46', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"<>"ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.47', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"<>"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.48', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"<>"ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.49', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"<>"á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.50', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"<>"à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.51', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"<>"ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.52', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"<>"â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.53', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"<>"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.54', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"+"<>"+"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('N.55', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"-"<>"+"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.56', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"+"<>"-"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.57', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('")"<>")"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('N.58', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"("<>")"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.59', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('")"<>"("');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.60', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"?"<>"?"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('N.61', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"*"<>"?"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.62', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"?"<>"*"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('N.63', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: greater-than operator with string comparison

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">\'a\'');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('\'a\'>"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">"b"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b">"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">"A"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"A">"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">"ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab">"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"A">"ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab">"A"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">"aB"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB">"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b">"ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab">"b"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"B">"aB"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB">"B"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b">"Ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"Ab">"b"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab">"ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab">"Ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB">"ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"n">"ñ"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ñ">"Ñ"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">"á"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">"à"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">"ä"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">"â"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.28', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">"ã"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.29', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á">"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.30', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á">"à"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.31', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á">"ä"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.32', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á">"â"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.33', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á">"ã"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.34', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à">"á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.35', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à">"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.36', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à">"ä"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.37', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à">"â"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.38', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à">"ã"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.39', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä">"á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.40', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä">"à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.41', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä">"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.42', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä">"â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.43', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä">"ã"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.44', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â">"á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.45', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â">"à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.46', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â">"ä"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.47', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â">"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.48', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â">"ã"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.49', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã">"á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.50', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã">"à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.51', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã">"ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.52', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã">"â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.53', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã">"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.54', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"+">"+"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.55', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"-">"+"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.56', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"+">"-"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.57', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('")">")"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.58', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"(">")"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.59', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('")">"("');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.60', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"?">"?"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.61', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"*">"?"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('O.62', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"?">"*"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('O.63', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: greater-than-or-equal-to operator with string comparison

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">="a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">=\'a\'');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('\'a\'>="a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">="b"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b">="a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">="A"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"A">="a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">="ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab">="a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"A">="ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab">="A"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">="aB"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB">="a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b">="ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab">="b"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"B">="aB"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB">="B"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b">="Ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"Ab">="b"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab">="ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab">="Ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB">="ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"n">="ñ"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ñ">="Ñ"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">="á"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">="à"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">="ä"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">="â"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.28', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a">="ã"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.29', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á">="a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.30', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á">="à"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.31', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á">="ä"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.32', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á">="â"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.33', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á">="ã"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.34', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à">="á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.35', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à">="a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.36', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à">="ä"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.37', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à">="â"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.38', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à">="ã"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.39', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä">="á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.40', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä">="à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.41', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä">="a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.42', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä">="â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.43', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä">="ã"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.44', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â">="á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.45', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â">="à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.46', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â">="ä"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.47', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â">="a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.48', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â">="ã"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.49', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã">="á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.50', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã">="à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.51', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã">="ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.52', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã">="â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.53', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã">="a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.54', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"+">="+"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.55', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"-">="+"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.56', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"+">="-"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.57', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('")">=")"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.58', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"(">=")"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.59', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('")">="("');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.60', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"?">="?"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.61', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"*">="?"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('P.62', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"?">="*"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.63', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: less-than operator with string comparison

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<\'a\'');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('\'a\'<"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<"b"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"<"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<"A"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"A"<"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<"ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"<"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"A"<"ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"<"A"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<"aB"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"<"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"<"ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"<"b"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"B"<"aB"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"<"B"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"<"Ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"Ab"<"b"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"<"ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"<"Ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"<"ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"n"<"ñ"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ñ"<"Ñ"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<"á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<"à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<"ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<"â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.28', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<"ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.29', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"<"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.30', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"<"à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.31', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"<"ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.32', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"<"â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.33', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"<"ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.34', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"<"á"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.35', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"<"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.36', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"<"ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.37', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"<"â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.38', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"<"ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.39', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"<"á"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.40', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"<"à"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.41', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"<"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.42', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"<"â"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.43', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"<"ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.44', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"<"á"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.45', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"<"à"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.46', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"<"ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.47', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"<"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.48', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"<"ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.49', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"<"á"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.50', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"<"à"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.51', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"<"ä"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.52', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"<"â"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.53', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"<"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.54', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"+"<"+"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.55', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"-"<"+"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.56', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"+"<"-"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.57', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('")"<")"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.58', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"("<")"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.59', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('")"<"("');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.60', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"?"<"?"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.61', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"*"<"?"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.62', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"?"<"*"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Q.63', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: less-than-or-equal-to operator with string comparison

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<="a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<=\'a\'');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('\'a\'<="a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<="b"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"<="a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<="A"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"A"<="a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<="ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"<="a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.9', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"A"<="ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"<="A"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<="aB"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"<="a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"<="ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.14', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"<="b"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.15', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"B"<="aB"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"<="B"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"<="Ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"Ab"<="b"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"<="ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.20', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"<="Ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.21', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"<="ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.22', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"n"<="ñ"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ñ"<="Ñ"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<="á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<="à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<="ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<="â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.28', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"<="ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.29', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"<="a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.30', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"<="à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.31', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"<="ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.32', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"<="â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.33', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"<="ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.34', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"<="á"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.35', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"<="a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.36', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"<="ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.37', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"<="â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.38', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"<="ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.39', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"<="á"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.40', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"<="à"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.41', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"<="a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.42', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"<="â"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.43', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"<="ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.44', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"<="á"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.45', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"<="à"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.46', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"<="ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.47', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"<="a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.48', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"<="ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.49', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"<="á"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.50', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"<="à"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.51', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"<="ä"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.52', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"<="â"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.53', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"<="a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.54', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"+"<="+"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.55', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"-"<="+"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.56', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"+"<="-"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.57', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('")"<=")"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.58', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"("<=")"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.59', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('")"<="("');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.60', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"?"<="?"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.61', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"*"<="?"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('R.62', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"?"<="*"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('R.63', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: equality operator with boolean comparison

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true=true');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('S.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true=false');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('S.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false=true');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('S.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false=false');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('S.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: not-equal operator with boolean comparison

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true!=true');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('T.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true!=false');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('T.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false!=true');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('T.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false!=false');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('T.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: not-equal operator with boolean comparison

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true<>true');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('U.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true<>false');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('U.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false<>true');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('U.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false<>false');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('U.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: greater-than operator with boolean comparison

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true>true');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('V.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true>false');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('V.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false>true');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('V.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false>false');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('V.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: greater-than-or-equal-to operator with boolean comparison

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true>=true');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('W.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true>=false');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('W.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false>=true');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('W.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false>=false');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('W.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: greater-than-or-equal-to operator with boolean comparison

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true<true');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('X.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true<false');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('X.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false<true');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('X.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false<false');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('X.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: greater-than-or-equal-to operator with boolean comparison

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true<=true');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Y.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true<=false');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Y.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false<=true');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Y.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false<=false');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Y.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: pattern-match operator with string comparison

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Z.1', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~\'a\'');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Z.2', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('\'a\'~"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Z.3', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~"b"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.4', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"~"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.5', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~"A"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"A"~"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~"ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.8', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"~"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Z.9', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"A"~"ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"~"A"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~"aB"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"~"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Z.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"~"ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.14', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"~"b"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Z.15', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"B"~"aB"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"~"B"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Z.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"~"Ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"Ab"~"b"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Z.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"~"ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Z.20', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"~"Ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.21', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"~"ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.22', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"n"~"ñ"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ñ"~"Ñ"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~"á"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~"à"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~"ä"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~"â"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.28', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~"ã"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.29', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"~"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.30', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"~"à"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.31', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"~"ä"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.32', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"~"â"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.33', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"~"ã"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.34', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"~"á"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.35', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"~"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.36', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"~"ä"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.37', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"~"â"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.38', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"~"ã"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.39', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"~"á"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.40', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"~"à"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.41', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"~"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.42', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"~"â"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.43', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"~"ã"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.44', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"~"á"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.45', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"~"à"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.46', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"~"ä"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.47', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"~"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.48', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"~"ã"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.49', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"~"á"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.50', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"~"à"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.51', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"~"ä"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.52', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"~"â"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.53', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"~"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.54', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"+"~"\+"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Z.55', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"-"~"\+"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.56', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"+"~"-"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.57', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('")"~"\)"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Z.58', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"("~"\)"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.59', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('")"~"\("');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.60', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"?"~"\?"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Z.61', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"*"~"\?"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.62', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"?"~"\*"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.63', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"abc"~"^a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Z.64', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"abc"~"(b|d)"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('Z.65', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"abc"~"^(b|d)"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.66', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"abc"~"^[A-Z]"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Z.67', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: pattern-match operator with string comparison

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~*"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AA.1', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~*\'a\'');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AA.2', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('\'a\'~*"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AA.3', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~*"b"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.4', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"~*"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.5', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~*"A"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AA.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"A"~*"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AA.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~*"ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.8', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"~*"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AA.9', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"A"~*"ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"~*"A"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AA.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~*"aB"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"~*"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AA.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"~*"ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.14', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"~*"b"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AA.15', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"B"~*"aB"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"~*"B"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AA.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"~*"Ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"Ab"~*"b"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AA.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"~*"ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AA.20', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"~*"Ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AA.21', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"~*"ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AA.22', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"n"~*"ñ"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ñ"~*"Ñ"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AA.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~*"á"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~*"à"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~*"ä"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~*"â"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.28', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"~*"ã"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.29', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"~*"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.30', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"~*"à"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.31', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"~*"ä"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.32', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"~*"â"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.33', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"~*"ã"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.34', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"~*"á"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.35', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"~*"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.36', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"~*"ä"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.37', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"~*"â"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.38', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"~*"ã"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.39', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"~*"á"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.40', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"~*"à"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.41', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"~*"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.42', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"~*"â"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.43', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"~*"ã"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.44', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"~*"á"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.45', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"~*"à"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.46', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"~*"ä"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.47', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"~*"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.48', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"~*"ã"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.49', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"~*"á"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.50', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"~*"à"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.51', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"~*"ä"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.52', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"~*"â"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.53', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"~*"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.54', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"+"~*"\+"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AA.55', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"-"~*"\+"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.56', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"+"~*"-"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.57', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('")"~*"\)"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AA.58', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"("~*"\)"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.59', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('")"~*"\("');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.60', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"?"~*"\?"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AA.61', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"*"~*"\?"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.62', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"?"~*"\*"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.63', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"abc"~*"^a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AA.64', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"abc"~*"(b|d)"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AA.65', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"abc"~*"^(b|d)"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AA.66', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"abc"~*"^[A-Z]"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AA.67', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: pattern-match operator with string comparison

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AB.1', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~\'a\'');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AB.2', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('\'a\'!~"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AB.3', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~"b"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.4', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"!~"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.5', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~"A"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"A"!~"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~"ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.8', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"!~"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AB.9', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"A"!~"ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"!~"A"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~"aB"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"!~"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AB.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"!~"ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.14', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"!~"b"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AB.15', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"B"!~"aB"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"!~"B"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AB.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"!~"Ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"Ab"!~"b"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AB.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"!~"ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AB.20', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"!~"Ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.21', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"!~"ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.22', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"n"!~"ñ"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ñ"!~"Ñ"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~"á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~"à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~"ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~"â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.28', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~"ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.29', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"!~"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.30', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"!~"à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.31', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"!~"ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.32', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"!~"â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.33', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"!~"ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.34', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"!~"á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.35', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"!~"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.36', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"!~"ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.37', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"!~"â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.38', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"!~"ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.39', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"!~"á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.40', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"!~"à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.41', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"!~"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.42', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"!~"â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.43', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"!~"ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.44', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"!~"á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.45', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"!~"à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.46', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"!~"ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.47', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"!~"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.48', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"!~"ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.49', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"!~"á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.50', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"!~"à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.51', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"!~"ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.52', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"!~"â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.53', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"!~"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.54', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"+"!~"\+"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AB.55', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"-"!~"\+"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.56', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"+"!~"-"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.57', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('")"!~"\)"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AB.58', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"("!~"\)"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.59', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('")"!~"\("');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.60', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"?"!~"\?"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AB.61', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"*"!~"\?"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.62', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"?"!~"\*"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.63', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"abc"!~"^a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AB.64', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"abc"!~"(b|d)"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AB.65', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"abc"!~"^(b|d)"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.66', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"abc"!~"^[A-Z]"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AB.67', 'Expression; return result of logical comparison',  $actual, $expected, $results);


        // TEST: pattern-match operator with string comparison

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~*"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AC.1', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~*\'a\'');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AC.2', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('\'a\'!~*"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AC.3', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~*"b"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.4', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"!~*"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.5', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~*"A"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AC.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"A"!~*"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AC.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~*"ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.8', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"!~*"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AC.9', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"A"!~*"ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.10', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"!~*"A"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AC.11', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~*"aB"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.12', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"!~*"a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AC.13', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"!~*"ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.14', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"!~*"b"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AC.15', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"B"!~*"aB"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.16', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"!~*"B"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AC.17', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"b"!~*"Ab"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.18', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"Ab"!~*"b"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AC.19', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"!~*"ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AC.20', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ab"!~*"Ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AC.21', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"aB"!~*"ab"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AC.22', 'Expression; return result of pattern-match comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"n"!~*"ñ"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.23', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ñ"!~*"Ñ"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AC.24', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~*"á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.25', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~*"à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.26', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~*"ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.27', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~*"â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.28', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a"!~*"ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.29', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"!~*"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.30', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"!~*"à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.31', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"!~*"ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.32', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"!~*"â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.33', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"á"!~*"ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.34', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"!~*"á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.35', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"!~*"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.36', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"!~*"ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.37', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"!~*"â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.38', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"à"!~*"ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.39', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"!~*"á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.40', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"!~*"à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.41', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"!~*"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.42', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"!~*"â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.43', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ä"!~*"ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.44', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"!~*"á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.45', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"!~*"à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.46', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"!~*"ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.47', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"!~*"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.48', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"â"!~*"ã"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.49', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"!~*"á"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.50', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"!~*"à"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.51', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"!~*"ä"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.52', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"!~*"â"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.53', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"ã"!~*"a"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.54', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"+"!~*"\+"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AC.55', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"-"!~*"\+"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.56', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"+"!~*"-"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.57', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('")"!~*"\)"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AC.58', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"("!~*"\)"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.59', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('")"!~*"\("');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.60', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"?"!~*"\?"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AC.61', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"*"!~*"\?"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.62', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"?"!~*"\*"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.63', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"abc"!~*"^a"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AC.64', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"abc"!~*"(b|d)"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AC.65', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"abc"!~*"^(b|d)"');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('AC.66', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"abc"!~*"^[A-Z]"');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('AC.67', 'Expression; return result of logical comparison',  $actual, $expected, $results);
    }
}
