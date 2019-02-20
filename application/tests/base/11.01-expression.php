<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
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
        $actual = \Flexio\Tests\Util::evalExpression(null);
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.1', 'Expression; non-string input',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression(true);
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.2', 'Expression; non-string input',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression(1);
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.3', 'Expression; non-string input',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression(array());
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.4', 'Expression; non-string input',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression(new \stdClass());
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.5', 'Expression; non-string input',  $actual, $expected, $results);

        $actual = \Flexio\Tests\Util::evalExpression('');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.6', 'Expression; empty string',  $actual, $expected, $results);
    }
}
