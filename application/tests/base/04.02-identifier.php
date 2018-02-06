<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
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
        // TEST: identifiers should be a lowercase string

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('xxxxx');
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Flexio\Base\Identifier::isValid(); identifiers should be a lowercase string', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('Xxxxx');
        $expected = false;
        TestCheck::assertBoolean('A.2', '\Flexio\Base\Identifier::isValid(); identifiers should be a lowercase string', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('xxxxX');
        $expected = false;
        TestCheck::assertBoolean('A.3', '\Flexio\Base\Identifier::isValid(); identifiers should be a lowercase string', $actual, $expected, $results);



        // TEST: identifiers should be between 3 and 80 chars in length

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('xx');
        $expected = false;
        TestCheck::assertBoolean('B.1', '\Flexio\Base\Identifier::isValid(); identifiers should be between 3 and 80 chars', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('xxx');
        $expected = true;
        TestCheck::assertBoolean('B.2', '\Flexio\Base\Identifier::isValid(); identifiers should be between 3 and 80 chars', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'); // 80 chars
        $expected = true;
        TestCheck::assertBoolean('B.3', '\Flexio\Base\Identifier::isValid(); identifiers should be between 3 and 80 chars', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'); // 81 chars
        $expected = false;
        TestCheck::assertBoolean('B.4', '\Flexio\Base\Identifier::isValid(); identifiers should be between 3 and 80 chars', $actual, $expected, $results);

    }
}
