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
        // TEST: non-string inputs

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid(null);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Base\Identifier::isValid() null input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid(false);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\Identifier::isValid() false input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid(true);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Base\Identifier::isValid() true input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid(111111111111);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.4', '\Flexio\Base\Identifier::isValid() numeric input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid(array());
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.5', '\Flexio\Base\Identifier::isValid() array input', $actual, $expected, $results);



        // TEST: identifiers should be a lowercase string

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('xxxxx');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Base\Identifier::isValid(); identifiers should be a lowercase string', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('Xxxxx');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.2', '\Flexio\Base\Identifier::isValid(); identifiers should be a lowercase string', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('xxxxX');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.3', '\Flexio\Base\Identifier::isValid(); identifiers should be a lowercase string', $actual, $expected, $results);



        // TEST: identifiers should be between 3 and 80 chars in length

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('xx');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.1', '\Flexio\Base\Identifier::isValid(); identifiers should be between 3 and 80 chars', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('xxx');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.2', '\Flexio\Base\Identifier::isValid(); identifiers should be between 3 and 80 chars', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'); // 80 chars
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.3', '\Flexio\Base\Identifier::isValid(); identifiers should be between 3 and 80 chars', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'); // 81 chars
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.4', '\Flexio\Base\Identifier::isValid(); identifiers should be between 3 and 80 chars', $actual, $expected, $results);

    }
}
