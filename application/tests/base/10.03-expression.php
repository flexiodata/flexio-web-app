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
        // TEST: multiple literals need to be combined with operators

        // BEGIN TEST
        $actual = TestUtil::evalExpression('null null');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.1', 'Expression; fail if combinations of literals are combined without operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.2', 'Expression; fail if combinations of literals are combined without operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false true');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.3', 'Expression; fail if combinations of literals are combined without operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('true false');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.4', 'Expression; fail if combinations of literals are combined without operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('false false');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.5', 'Expression; fail if combinations of literals are combined without operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1 0');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.6', 'Expression; fail if combinations of literals are combined without operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('0 1');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.7', 'Expression; fail if combinations of literals are combined without operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('1 ""');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.8', 'Expression; fail if combinations of literals are combined without operators',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('"" "a"');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.9', 'Expression; fail if combinations of literals are combined without operators',  $actual, $expected, $results);
    }
}
