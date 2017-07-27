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
        $actual = \Flexio\Object\Right::TYPE_UNDEFINED;
        $expected = '';
        TestCheck::assertString('A.1', 'Right type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Right::TYPE_READ;
        $expected = 'object.read';
        TestCheck::assertString('A.2', 'Right type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Right::TYPE_WRITE;
        $expected = 'object.write';
        TestCheck::assertString('A.3', 'Right type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Right::TYPE_DELETE;
        $expected = 'object.delete';
        TestCheck::assertString('A.4', 'Right type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Right::TYPE_EXECUTE;
        $expected = 'object.execute';
        TestCheck::assertString('A.5', 'Right type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Right::TYPE_READ_RIGHTS;
        $expected = 'rights.read';
        TestCheck::assertString('A.6', 'Right type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Right::TYPE_WRITE_RIGHTS;
        $expected = 'rights.write';
        TestCheck::assertString('A.7', 'Right type',  $actual, $expected, $results);
    }
}
