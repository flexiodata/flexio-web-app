<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
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
        // note: expressions support whitespace where appropriate
        // (e.g. "1>0", "1 > 0", "1  >  0", etc)

        // TODO:
        //     - add tests to make sure that expressions tolerate whitepsace
        //       in appropriate places, but only in appropriate places
        //     - add tests with different kinds of whitespace, include tabs, line terminators, etc;
        //       include cases where these whitespaces are included in literals as well as outside
        //       the literals in the expressions themselves


        // TEST: don't allow whitespace inside non-string literals

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('n ull');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.1', 'Expression; fail if space is included inside non-string literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('nul l');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.2', 'Expression; fail if space is included inside non-string literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('t rue');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.3', 'Expression; fail if space is included inside non-string literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('tru e');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.4', 'Expression; fail if space is included inside non-string literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('f alse');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.5', 'Expression; fail if space is included inside non-string literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('fals e');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.6', 'Expression; fail if space is included inside non-string literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1 23');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.7', 'Expression; fail if space is included inside non-string literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('12 3');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.8', 'Expression; fail if space is included inside non-string literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1 .23');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.9', 'Expression; fail if space is included inside non-string literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1. 23');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.10', 'Expression; fail if space is included inside non-string literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.2 3');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.11', 'Expression; fail if space is included inside non-string literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1 e2');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.12', 'Expression; fail if space is included inside non-string literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e 2');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.13', 'Expression; fail if space is included inside non-string literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e -2');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.14', 'Expression; fail if space is included inside non-string literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e- 2');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.15', 'Expression; fail if space is included inside non-string literal',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.2 e2');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.16', 'Expression; fail if space is included inside non-string literal',  $actual, $expected, $results);



        // TEST: make sure whitespace inside a string literal is preserved

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('""');
        $expected = "";
        \Flexio\Tests\Check::assertString('B.1', 'Expression; whitespace inside string literal should be preserved',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('\'\'');
        $expected = "";
        \Flexio\Tests\Check::assertString('B.2', 'Expression; whitespace inside string literal should be preserved',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"  "');
        $expected = "  ";
        \Flexio\Tests\Check::assertString('B.3', 'Expression; whitespace inside string literal should be preserved',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('\'  \'');
        $expected = "  ";
        \Flexio\Tests\Check::assertString('B.4', 'Expression; whitespace inside string literal should be preserved',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"   A"');
        $expected = "   A";
        \Flexio\Tests\Check::assertString('B.5', 'Expression; whitespace inside string literal should be preserved',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('\'   A\'');
        $expected = "   A";
        \Flexio\Tests\Check::assertString('B.6', 'Expression; whitespace inside string literal should be preserved',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"A   "');
        $expected = "A   ";
        \Flexio\Tests\Check::assertString('B.7', 'Expression; whitespace inside string literal should be preserved',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('\'A   \'');
        $expected = "A   ";
        \Flexio\Tests\Check::assertString('B.8', 'Expression; whitespace inside string literal should be preserved',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"   A   "');
        $expected = "   A   ";
        \Flexio\Tests\Check::assertString('B.9', 'Expression; whitespace inside string literal should be preserved',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('\'   A   \'');
        $expected = "   A   ";
        \Flexio\Tests\Check::assertString('B.10', 'Expression; whitespace inside string literal should be preserved',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"A   B"');
        $expected = "A   B";
        \Flexio\Tests\Check::assertString('B.11', 'Expression; whitespace inside string literal should be preserved',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('\'A   B\'');
        $expected = "A   B";
        \Flexio\Tests\Check::assertString('B.12', 'Expression; whitespace inside string literal should be preserved',  $actual, $expected, $results);



        // TEST : allow whitepace around literals

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('null');
        $expected = null;
        \Flexio\Tests\Check::assertNull('C.1', 'Expression; allow whitespace around literals',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('   null');
        $expected = null;
        \Flexio\Tests\Check::assertNull('C.2', 'Expression; allow whitespace around literals',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('null   ');
        $expected = null;
        \Flexio\Tests\Check::assertNull('C.3', 'Expression; allow whitespace around literals',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('   null   ');
        $expected = null;
        \Flexio\Tests\Check::assertNull('C.4', 'Expression; allow whitespace around literals',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.5', 'Expression; allow whitespace around literals',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('   true');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.6', 'Expression; allow whitespace around literals',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true   ');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.7', 'Expression; allow whitespace around literals',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('   true   ');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.8', 'Expression; allow whitespace around literals',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.9', 'Expression; allow whitespace around literals',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('   false');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.10', 'Expression; allow whitespace around literals',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false   ');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.11', 'Expression; allow whitespace around literals',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('   false   ');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.12', 'Expression; allow whitespace around literals',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1');
        $expected = 1;
        \Flexio\Tests\Check::assertNumber('C.13', 'Expression; allow whitespace around literals',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('   1');
        $expected = 1;
        \Flexio\Tests\Check::assertNumber('C.14', 'Expression; allow whitespace around literals',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1   ');
        $expected = 1;
        \Flexio\Tests\Check::assertNumber('C.15', 'Expression; allow whitespace around literals',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('   1   ');
        $expected = 1;
        \Flexio\Tests\Check::assertNumber('C.16', 'Expression; allow whitespace around literals',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.2');
        $expected = -1.2;
        \Flexio\Tests\Check::assertNumber('C.17', 'Expression; allow whitespace around literals',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('   -1.2');
        $expected = -1.2;
        \Flexio\Tests\Check::assertNumber('C.18', 'Expression; allow whitespace around literals',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1.2   ');
        $expected = -1.2;
        \Flexio\Tests\Check::assertNumber('C.19', 'Expression; allow whitespace around literals',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('   -1.2   ');
        $expected = -1.2;
        \Flexio\Tests\Check::assertNumber('C.20', 'Expression; allow whitespace around literals',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"A"');
        $expected = "A";
        \Flexio\Tests\Check::assertString('C.21', 'Expression; allow whitespace around literals',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('   "A"');
        $expected = "A";
        \Flexio\Tests\Check::assertString('C.22', 'Expression; allow whitespace around literals',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('"A"   ');
        $expected = "A";
        \Flexio\Tests\Check::assertString('C.23', 'Expression; allow whitespace around literals',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('   "A"   ');
        $expected = "A";
        \Flexio\Tests\Check::assertString('C.24', 'Expression; allow whitespace around literals',  $actual, $expected, $results);



        // TEST : check for required whitespace between logical OR operator and literals

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trueortrue');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.1', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('(true)or(true)');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.2', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true(or)true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.3', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trueor false');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.4', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false ortrue');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.5', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('falseor true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.6', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true orfalse');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.7', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('(True)Or(True)');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.8', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('True(Or)True');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.9', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('TrueOr False');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.10', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('False OrTrue');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.11', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('FalseOr True');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.12', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('True OrFalse');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.13', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1>0or1>0');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.14', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('(1>0)or(1>0)');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.15', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1>0(or)1>0');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.16', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1>0or 1>0');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.17', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1>0 or1>0');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.18', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1>0 or-1<0');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.19', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);



        // TEST : check for required whitespace between logical AND operator and literals

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trueandtrue');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.1', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('(true)and(true)');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.2', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true(and)true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.3', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trueand false');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.4', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('false andtrue');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.5', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('falseand true');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.6', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true andfalse');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.7', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('TrueAndTrue');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.8', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('(True)And(True)');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.9', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('True(And)True');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.10', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('TrueAnd False');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.11', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('False AndTrue');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.12', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('FalseAnd True');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.13', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('True AndFalse');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.14', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1>0and1>0');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.15', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('(1>0)and(1>0)');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.16', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1>0(and)1>0');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.17', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1>0and 1>0');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.18', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1>0 and1>0');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.19', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1>0 and-1<0');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.20', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);



        // TEST : check for required whitespace between logical NOT operator and literals;
        // note: symbolic version of NOT (!) doesn't require whitespace in between it and operand

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('notfalse');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.1', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('not(false)');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.2', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('(not)false');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.3', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true ornot false');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.4', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('(true) ornot (false)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.5', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true or notfalse');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.6', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true andnot false');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.7', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true and notfalse');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.8', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('NotFalse');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.9', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('True OrNot False');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.10', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('True Or NotFalse');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.11', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('True AndNot False');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.12', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('True And NotFalse');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.13', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('not1<0');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.14', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('not 1<0');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.15', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('not(1<0)');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.16', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('not-1>0');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.17', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('not -1>0');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.18', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('not(-1>0)');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.19', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('!true');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.20', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('!false');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.21', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('! true');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.22', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('! false');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.23', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('!(true)');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.24', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('! (true)');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.25', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('!(false)');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.26', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('! (false)');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.27', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('(!)false');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.28', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true or! false');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.29', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('(true) or! (false)');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.30', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true or !false');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.31', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true or ! false');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.32', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true and! false');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.33', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true and !false');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.34', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('true and ! false');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.35', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('!1<0');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.36', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('! 1<0');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.37', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('!(1<0)');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.38', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('! (1<0)');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.39', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('!-1>0');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.40', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('! -1>0');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.41', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('!(-1>0)');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.42', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('! (-1>0)');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.43', 'Expression; fail if logical operators don\'t have appropriate whitespace',  $actual, $expected, $results);



        // TEST : allow whitepace around grouping operator

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('(true)');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.1', 'Expression; allow whitespace around grouping operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('   (true)');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.2', 'Expression; allow whitespace around grouping operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('(true)   ');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.3', 'Expression; allow whitespace around grouping operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('   (true)   ');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.4', 'Expression; allow whitespace around grouping operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('(   true)');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.5', 'Expression; allow whitespace around grouping operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('(true   )');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.6', 'Expression; allow whitespace around grouping operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('(   true   )');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.7', 'Expression; allow whitespace around grouping operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('((true))');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.8', 'Expression; allow whitespace around grouping operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('(   (true))');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.9', 'Expression; allow whitespace around grouping operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('((true)   )');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.10', 'Expression; allow whitespace around grouping operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('(   (true)   )');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.11', 'Expression; allow whitespace around grouping operator',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('   (   (true)   )   ');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.12', 'Expression; allow whitespace around grouping operator',  $actual, $expected, $results);
    }
}
