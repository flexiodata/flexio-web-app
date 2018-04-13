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
        $actual = \Flexio\Tests\Util::evalExpression('NOT');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.1', 'Expression; fail if logical operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('AND');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.2', 'Expression; fail if logical operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('OR');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.3', 'Expression; fail if logical operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('!');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.4', 'Expression; fail if logical operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);



        // TEST: comparison operators with only left parameters

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true NOT');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.1', 'Expression; fail if logical operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true AND');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.2', 'Expression; fail if logical operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true OR');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.3', 'Expression; fail if logical operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true!');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.4', 'Expression; fail if logical operator doesn\'t have appropriate paired types on the right side',  $actual, $expected, $results);



        // TEST: comparison operators with only right parameters

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('NOT true');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.1', 'Expression; NOT is a unary operator, so evaluate when there\'s a parameter on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('NOT false');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.2', 'Expression; NOT is a unary operator, so evaluate when there\'s a parameter on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('AND true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.3', 'Expression; fail if logical operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('AND false');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.4', 'Expression; fail if logical operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('OR true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.5', 'Expression; fail if logical operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('OR false');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.6', 'Expression; fail if logical operator doesn\'t have appropriate paired types on the left side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('!true');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.7', 'Expression; ! is a unary operator, so evaluate when there\'s a parameter on the right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('!false');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.8', 'Expression; ! is a unary operator, so evaluate when there\'s a parameter on the right side',  $actual, $expected, $results);



        // TEST: comparison operators with left and right mixed parameters

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true NOT "a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.1', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a" NOT true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.2', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true NOT 1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.3', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1 NOT true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.4', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true AND "a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.5', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a" AND true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.6', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true AND 1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.7', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1 AND true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.8', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true OR "a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.9', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a" OR true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.10', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true OR 1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.11', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1 OR true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.12', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true !"a"');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.13', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"a" !true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.14', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true !1');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.15', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1 !true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.16', 'Expression; fail if comparison operator doesn\'t have appropriate paired types on the left and right side',  $actual, $expected, $results);



        // TEST: NOT (!) operator with boolean operands

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true NOT true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.1', 'Expression; fail if NOT operand has a left operand',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true NOT false');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.2', 'Expression; fail if NOT operand has a left operand',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false NOT true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.3', 'Expression; fail if NOT operand has a left operand',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false NOT false');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.4', 'Expression; fail if NOT operand has a left operand',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('NOT true');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.5', 'Expression; return result of NOT expression',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('NOT false');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.6', 'Expression; return result of NOT expression',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true not true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.7', 'Expression; fail if NOT operand has a left operand',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true not false');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.8', 'Expression; fail if NOT operand has a left operand',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false not true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.9', 'Expression; fail if NOT operand has a left operand',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false not false');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.10', 'Expression; fail if NOT operand has a left operand',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('not true');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.11', 'Expression; return result of NOT expression',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('not false');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.12', 'Expression; return result of NOT expression',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true ! true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.13', 'Expression; fail if ! operand has a left operand',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true ! false');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.14', 'Expression; fail if ! operand has a left operand',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false ! true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.15', 'Expression; fail if ! operand has a left operand',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false ! false');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.16', 'Expression; fail if ! operand has a left operand',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('!true');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.17', 'Expression; return result of ! expression',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('!false');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.18', 'Expression; return result of ! expression',  $actual, $expected, $results);



        // TEST: AND operator with boolean operands

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true AND true');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true AND false');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false AND true');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false AND false');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true and true');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true and false');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false and true');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false and false');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);



        // TEST: OR operator with boolean operands

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true OR true');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.1', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true OR false');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.2', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false OR true');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.3', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false OR false');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.4', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true or true');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.5', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true or false');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.6', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false or true');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.7', 'Expression; return result of logical comparison',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false or false');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.8', 'Expression; return result of logical comparison',  $actual, $expected, $results);
    }
}
