<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-09-16
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
        // note: runtime checks for bad inputs in evaluation


        // TODO:
        //     - add tests for nested function calls, particularly those where one
        //       function result may not be suited as input for another (e.g. numeric
        //       function resulting in zero, that's then used in the MOD() function
        //     - add tests for potentially bad strings inside expressions that may
        //       cause the parser to choke; e.g., include the following in tests involving
        //       the various ~ match operators:
        //       - //
        //       - /**/
        //       - 1 + 1 // comments
        //       - 1 + /* comments */ 1
        //       - /a/


        // TEST: runtime NaN checks

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1 % 0');
        $expected = NAN;
        TestCheck::assertNaN('A.1', 'Expression; runtime out-of-bounds checks',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1 % sin(0)');
        $expected = NAN;
        TestCheck::assertNaN('A.2', 'Expression; runtime out-of-bounds checks',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1 % sign(0)');
        $expected = NAN;
        TestCheck::assertNaN('A.3', 'Expression; runtime out-of-bounds checks',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('mod(1.0,floor(0))');
        $expected = NAN;
        TestCheck::assertNaN('A.4', 'Expression; runtime out-of-bounds checks',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('log(cos(pi()))');
        $expected = NAN;
        TestCheck::assertNaN('A.5', 'Expression; runtime out-of-bounds checks',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pow(cos(pi()),0.5)');
        $expected = NAN;
        TestCheck::assertNaN('A.6', 'Expression; runtime out-of-bounds checks',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1.23456789e300*1.23456789e300');
        $expected = NAN;
        TestCheck::assertNaN('A.7', 'Expression; runtime out-of-bounds checks',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pow(10,1000)');
        $expected = NAN;
        TestCheck::assertNaN('A.8', 'Expression; runtime out-of-bounds checks',  $actual, $expected, $results);



        // TEST: tests for code injection; raw ascii characters

        // BEGIN TEST
        $expr = chr(0); // null char
        $actual = TestUtil::evalExpression($expr);
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.1', 'Expression; make sure injected code doesn\'t run when parsing expressions',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = '"' . chr(0) . '"'; // null char
        $actual = TestUtil::evalExpression($expr);
        $expected = chr(0);
        TestCheck::assertString('B.2', 'Expression; make sure injected code doesn\'t run when parsing expressions',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = chr(10); // line feed
        $actual = TestUtil::evalExpression($expr);
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.3', 'Expression; make sure injected code doesn\'t run when parsing expressions',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = '"' . chr(10) . '"'; // line feed
        $actual = TestUtil::evalExpression($expr);
        $expected = chr(10);
        TestCheck::assertString('B.4', 'Expression; make sure injected code doesn\'t run when parsing expressions',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = chr(13); // carriage return
        $actual = TestUtil::evalExpression($expr);
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.5', 'Expression; make sure injected code doesn\'t run when parsing expressions',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = '"' . chr(13) . '"'; // carriage return
        $actual = TestUtil::evalExpression($expr);
        $expected = chr(13);
        TestCheck::assertString('B.6', 'Expression; make sure injected code doesn\'t run when parsing expressions',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = chr(47); // slash
        $actual = TestUtil::evalExpression($expr);
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.7', 'Expression; make sure injected code doesn\'t run when parsing expressions',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = '"' . chr(47) . '"'; // slash
        $actual = TestUtil::evalExpression($expr);
        $expected = chr(47);
        TestCheck::assertString('B.8', 'Expression; make sure injected code doesn\'t run when parsing expressions',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = chr(92); // backslash
        $actual = TestUtil::evalExpression($expr);
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.9', 'Expression; make sure injected code doesn\'t run when parsing expressions',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = '"' . chr(92) . '"'; // backslash
        $actual = TestUtil::evalExpression($expr);
        $expected = chr(92);
        TestCheck::assertString('B.10', 'Expression; make sure injected code doesn\'t run when parsing expressions',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = chr(255); // nbsp
        $actual = TestUtil::evalExpression($expr);
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.11', 'Expression; make sure injected code doesn\'t run when parsing expressions',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = '"' . chr(255) . '"'; // nbsp
        $actual = TestUtil::evalExpression($expr);
        $expected = chr(255);
        TestCheck::assertString('B.12', 'Expression; make sure injected code doesn\'t run when parsing expressions',  $actual, $expected, $results);



        // TEST: tests for code injection; code snippets characters

        // BEGIN TEST
        $expr = '// /*';
        $actual = TestUtil::evalExpression($expr);
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.1', 'Expression; make sure injected code doesn\'t run when parsing expressions',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = '"// /*"';
        $actual = TestUtil::evalExpression($expr);
        $expected = '// /*';
        TestCheck::assertString('C.2', 'Expression; make sure injected code doesn\'t run when parsing expressions',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = '.*';
        $actual = TestUtil::evalExpression($expr);
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.3', 'Expression; make sure injected code doesn\'t run when parsing expressions',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = '".*"';
        $actual = TestUtil::evalExpression($expr);
        $expected = '.*';
        TestCheck::assertString('C.4', 'Expression; make sure injected code doesn\'t run when parsing expressions',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = '/.*/';
        $actual = TestUtil::evalExpression($expr);
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.5', 'Expression; make sure injected code doesn\'t run when parsing expressions',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = '"/.*/"';
        $actual = TestUtil::evalExpression($expr);
        $expected = '/.*/';
        TestCheck::assertString('C.6', 'Expression; make sure injected code doesn\'t run when parsing expressions',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = 'true;';
        $actual = TestUtil::evalExpression($expr);
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.7', 'Expression; make sure injected code doesn\'t run when parsing expressions',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = '"true;"';
        $actual = TestUtil::evalExpression($expr);
        $expected = 'true;';
        TestCheck::assertString('C.8', 'Expression; make sure injected code doesn\'t run when parsing expressions',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = ';break;';
        $actual = TestUtil::evalExpression($expr);
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.9', 'Expression; make sure injected code doesn\'t run when parsing expressions',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = '";break;"';
        $actual = TestUtil::evalExpression($expr);
        $expected = ';break;';
        TestCheck::assertString('C.10', 'Expression; make sure injected code doesn\'t run when parsing expressions',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = ';$a=1/0;$b=';
        $actual = TestUtil::evalExpression($expr);
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.11', 'Expression; make sure injected code doesn\'t run when parsing expressions',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = '\';$a=1/0;$b=\'';
        $actual = TestUtil::evalExpression($expr);
        $expected = ';$a=1/0;$b=';
        TestCheck::assertString('C.12', 'Expression; make sure injected code doesn\'t run when parsing expressions',  $actual, $expected, $results);



        // TEST: tests for code injection in evaluation

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"+" ~ "+"');
        $expected = true;
        TestCheck::assertBoolean('D.1', 'Expression; make sure injected code doesn\'t run when evaluating expressions',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"{}" ~ "{"');
        $expected = true;
        TestCheck::assertBoolean('D.2', 'Expression; make sure injected code doesn\'t run when evaluating expressions',  $actual, $expected, $results);
    }
}
