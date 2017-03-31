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
        // note: the following case-insenstivie logical operators are currently supported:
        //     not, ! (logical "not")
        //     and    (logical "and")
        //     or     (logical "or")


        // TEST: logical operators without paired type parameters on left and right side

        // BEGIN TEST
        $actual = TestUtil::evalExpression('NOT');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.1', 'Expression; fail if logical operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('AND');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.2', 'Expression; fail if logical operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('OR');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.3', 'Expression; fail if logical operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('!');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.4', 'Expression; fail if logical operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);



        // TEST: comparison operators with only left parameters

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true NOT');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.1', 'Expression; fail if logical operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true AND');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.2', 'Expression; fail if logical operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true OR');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.3', 'Expression; fail if logical operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true!');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.4', 'Expression; fail if logical operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);



        // TEST: comparison operators with only right parameters

        // BEGIN TEST
        $actual = TestUtil::evalExpression('NOT true');
        $expected = false;
        TestCheck::assertBoolean('C.1', 'Expression; NOT is a unary operator, so evaluate when there\'s a parameter on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('NOT false');
        $expected = true;
        TestCheck::assertBoolean('C.2', 'Expression; NOT is a unary operator, so evaluate when there\'s a parameter on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('AND true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.3', 'Expression; fail if logical operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('AND false');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.4', 'Expression; fail if logical operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('OR true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.5', 'Expression; fail if logical operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('OR false');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.6', 'Expression; fail if logical operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('!true');
        $expected = false;
        TestCheck::assertBoolean('C.7', 'Expression; ! is a unary operator, so evaluate when there\'s a parameter on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('!false');
        $expected = true;
        TestCheck::assertBoolean('C.8', 'Expression; ! is a unary operator, so evaluate when there\'s a parameter on the right side',  $actual, $expected, $results);



        // TEST: comparison operators with left and right mixed parameters

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true NOT "a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.1', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a" NOT true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.2', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true NOT 1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.3', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1 NOT true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.4', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true AND "a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.5', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a" AND true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.6', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true AND 1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.7', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1 AND true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.8', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true OR "a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.9', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a" OR true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.10', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true OR 1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.11', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1 OR true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.12', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true !"a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.13', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a" !true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.14', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true !1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.15', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1 !true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.16', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);



        // TEST: NOT (!) operator with boolean operands

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true NOT true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.1', 'Expression; fail if NOT operand has a left operand',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true NOT false');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.2', 'Expression; fail if NOT operand has a left operand',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false NOT true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.3', 'Expression; fail if NOT operand has a left operand',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false NOT false');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.4', 'Expression; fail if NOT operand has a left operand',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('NOT true');
        $expected = false;
        TestCheck::assertBoolean('E.5', 'Expression; return result of NOT expression',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('NOT false');
        $expected = true;
        TestCheck::assertBoolean('E.6', 'Expression; return result of NOT expression',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true not true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.7', 'Expression; fail if NOT operand has a left operand',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true not false');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.8', 'Expression; fail if NOT operand has a left operand',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false not true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.9', 'Expression; fail if NOT operand has a left operand',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false not false');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.10', 'Expression; fail if NOT operand has a left operand',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('not true');
        $expected = false;
        TestCheck::assertBoolean('E.11', 'Expression; return result of NOT expression',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('not false');
        $expected = true;
        TestCheck::assertBoolean('E.12', 'Expression; return result of NOT expression',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true ! true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.13', 'Expression; fail if ! operand has a left operand',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true ! false');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.14', 'Expression; fail if ! operand has a left operand',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false ! true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.15', 'Expression; fail if ! operand has a left operand',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false ! false');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.16', 'Expression; fail if ! operand has a left operand',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('!true');
        $expected = false;
        TestCheck::assertBoolean('E.17', 'Expression; return result of ! expression',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('!false');
        $expected = true;
        TestCheck::assertBoolean('E.18', 'Expression; return result of ! expression',  $actual, $expected, $results);



        // TEST: AND operator with boolean operands

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true AND true');
        $expected = true;
        TestCheck::assertBoolean('F.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true AND false');
        $expected = false;
        TestCheck::assertBoolean('F.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false AND true');
        $expected = false;
        TestCheck::assertBoolean('F.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false AND false');
        $expected = false;
        TestCheck::assertBoolean('F.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true and true');
        $expected = true;
        TestCheck::assertBoolean('F.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true and false');
        $expected = false;
        TestCheck::assertBoolean('F.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false and true');
        $expected = false;
        TestCheck::assertBoolean('F.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false and false');
        $expected = false;
        TestCheck::assertBoolean('F.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: OR operator with boolean operands

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true OR true');
        $expected = true;
        TestCheck::assertBoolean('G.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true OR false');
        $expected = true;
        TestCheck::assertBoolean('G.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false OR true');
        $expected = true;
        TestCheck::assertBoolean('G.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false OR false');
        $expected = false;
        TestCheck::assertBoolean('G.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true or true');
        $expected = true;
        TestCheck::assertBoolean('G.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true or false');
        $expected = true;
        TestCheck::assertBoolean('G.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false or true');
        $expected = true;
        TestCheck::assertBoolean('G.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false or false');
        $expected = false;
        TestCheck::assertBoolean('G.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);
    }
}
