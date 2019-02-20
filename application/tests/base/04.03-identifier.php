<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-06-17
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
        // TEST: identifiers should start with a character

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('abc');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Base\Identifier::isValid(); identifiers should start with a character', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('1bc');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\Identifier::isValid(); identifiers should start with a character', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('.bc');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Base\Identifier::isValid(); identifiers should start with a character', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('/bc');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.4', '\Flexio\Base\Identifier::isValid(); identifiers should start with a character', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('\bc');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.5', '\Flexio\Base\Identifier::isValid(); identifiers should start with a character', $actual, $expected, $results);



        // TEST: chars after first char should be alphanmeric

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('abcdef123');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Base\Identifier::isValid(); non-alphanumeric characters', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('abc.def');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.2', '\Flexio\Base\Identifier::isValid(); non-alphanumeric characters', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('abc/def');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.3', '\Flexio\Base\Identifier::isValid(); non-alphanumeric characters', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('abc\def');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.4', '\Flexio\Base\Identifier::isValid(); non-alphanumeric characters', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('abc-def');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.5', '\Flexio\Base\Identifier::isValid(); non-alphanumeric characters; hyphens and underscores are allowed', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('abc_def');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.6', '\Flexio\Base\Identifier::isValid(); non-alphanumeric characters; hyphens and underscores are allowed', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('abc/');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.7', '\Flexio\Base\Identifier::isValid(); non-alphanumeric characters', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('abc.');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.8', '\Flexio\Base\Identifier::isValid(); non-alphanumeric characters', $actual, $expected, $results);
    }
}
