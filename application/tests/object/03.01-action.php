<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-06-25
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
        // SETUP
        $model = TestUtil::getModel();


        // TEST: Action constant tests

        // BEGIN TEST
        $actual = \Flexio\Object\Action::TYPE_UNDEFINED;
        $expected = '';
        TestCheck::assertString('A.1', 'Action type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Action::TYPE_READ;
        $expected = 'read';
        TestCheck::assertString('A.2', 'Action type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Action::TYPE_WRITE;
        $expected = 'write';
        TestCheck::assertString('A.3', 'Action type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Action::TYPE_DELETE;
        $expected = 'delete';
        TestCheck::assertString('A.4', 'Action type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Action::TYPE_EXECUTE;
        $expected = 'execute';
        TestCheck::assertString('A.5', 'Action type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Action::TYPE_READ_RIGHTS;
        $expected = 'read.rights';
        TestCheck::assertString('A.6', 'Action type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Action::TYPE_WRITE_RIGHTS;
        $expected = 'write.rights';
        TestCheck::assertString('A.7', 'Action type',  $actual, $expected, $results);
    }
}
