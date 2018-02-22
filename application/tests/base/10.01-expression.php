<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-02-05
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
        // TEST: non-string input

        // BEGIN TEST
        $actual = TestUtil::evalExpression(null);
        $expected = TestError::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.1', 'Expression; non-string input',  $actual, $expected, $results);

        $actual = TestUtil::evalExpression(true);
        $expected = TestError::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.2', 'Expression; non-string input',  $actual, $expected, $results);

        $actual = TestUtil::evalExpression(1);
        $expected = TestError::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.3', 'Expression; non-string input',  $actual, $expected, $results);

        $actual = TestUtil::evalExpression(array());
        $expected = TestError::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.4', 'Expression; non-string input',  $actual, $expected, $results);

        $actual = TestUtil::evalExpression(new \stdClass());
        $expected = TestError::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.5', 'Expression; non-string input',  $actual, $expected, $results);

        $actual = TestUtil::evalExpression('');
        $expected = TestError::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.6', 'Expression; empty string',  $actual, $expected, $results);
    }
}
