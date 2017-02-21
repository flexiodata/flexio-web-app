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
        // note: the following grouping operators are supported:
        //     ()


        // TEST: group operators must be matched; evaluate and return results
        // within matched group operators

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.1', 'Expression; fail if group operator isn\'t matched',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression(')');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.2', 'Expression; fail if group operator isn\'t matched',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.3', 'Expression; fail if group operator doesn\'t contain a valid expression',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(null');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.4', 'Expression; fail if group operator isn\'t matched',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('null)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.5', 'Expression; fail if group operator isn\'t matched',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(null)');
        $expected = null;
        TestCheck::assertNull('A.6', 'Expression; if a group operator contains a valid expression, return the results of that expression',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.7', 'Expression; fail if group operator isn\'t matched',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.8', 'Expression; fail if group operator isn\'t matched',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(true)');
        $expected = true;
        TestCheck::assertBoolean('A.9', 'Expression; if a group operator contains a valid expression, return the results of that expression',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('((true)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.10', 'Expression; fail if group operator isn\'t matched',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(true))');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.11', 'Expression; fail if group operator isn\'t matched',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('((true))');
        $expected = true;
        TestCheck::assertBoolean('A.12', 'Expression; if a group operator contains a valid expression, return the results of that expression',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.13', 'Expression; fail if group operator isn\'t matched',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.14', 'Expression; fail if group operator isn\'t matched',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(1)');
        $expected = 1;
        TestCheck::assertNumber('A.15', 'Expression; if a group operator contains a valid expression, return the results of that expression',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('((1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.16', 'Expression; fail if group operator isn\'t matched',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(1))');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.17', 'Expression; fail if group operator isn\'t matched',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('((1))');
        $expected = 1;
        TestCheck::assertNumber('A.18', 'Expression; if a group operator contains a valid expression, return the results of that expression',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(-1.2');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.19', 'Expression; fail if group operator isn\'t matched',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('-1.2)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.20', 'Expression; fail if group operator isn\'t matched',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(-1.2)');
        $expected = -1.2;
        TestCheck::assertNumber('A.21', 'Expression; if a group operator contains a valid expression, return the results of that expression',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('((-1.2)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.22', 'Expression; fail if group operator isn\'t matched',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(-1.2))');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.23', 'Expression; fail if group operator isn\'t matched',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('((-1.2))');
        $expected = -1.2;
        TestCheck::assertNumber('A.24', 'Expression; if a group operator contains a valid expression, return the results of that expression',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('("a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.25', 'Expression; fail if group operator isn\'t matched',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"a")');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.26', 'Expression; fail if group operator isn\'t matched',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('("a")');
        $expected = "a";
        TestCheck::assertString('A.27', 'Expression; if a group operator contains a valid expression, return the results of that expression',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(("a")');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.28', 'Expression; fail if group operator isn\'t matched',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('("a"))');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.29', 'Expression; fail if group operator isn\'t matched',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(("a"))');
        $expected = "a";
        TestCheck::assertString('A.30', 'Expression; if a group operator contains a valid expression, return the results of that expression',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(((((((((("a")))))))))))');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.31', 'Expression; make sure large numbers of matching group operators evaluate properly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(((((((((("a"))))))))))');
        $expected = "a";
        TestCheck::assertString('A.32', 'Expression; make sure large numbers of matching group operators evaluate properly; fail if operators aren\'t matched',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((("a")))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.33', 'Expression; make sure large numbers of matching group operators evaluate properly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('(((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((("a"))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))');
        $expected = "a";
        TestCheck::assertString('A.34', 'Expression; make sure large numbers of matching group operators evaluate properly; fail if operators aren\'t matched',  $actual, $expected, $results);
        // BEGIN TEST
        $actual = TestUtil::evalExpression('((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((("a"))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.35', 'Expression; make sure large numbers of matching group operators evaluate properly',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((("a")))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))');
        $expected = "a";
        TestCheck::assertString('A.36', 'Expression; make sure large numbers of matching group operators evaluate properly',  $actual, $expected, $results);
    }
}
