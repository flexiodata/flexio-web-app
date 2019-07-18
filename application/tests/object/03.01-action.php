<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
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
        // BEGIN TEST
        $actual = \Flexio\Object\Action::TYPE_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('A.1', 'Action type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Action::TYPE_READ;
        $expected = 'object.read';
        \Flexio\Tests\Check::assertString('A.2', 'Action type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Action::TYPE_WRITE;
        $expected = 'object.write';
        \Flexio\Tests\Check::assertString('A.3', 'Action type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Action::TYPE_DELETE;
        $expected = 'object.delete';
        \Flexio\Tests\Check::assertString('A.4', 'Action type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Action::TYPE_EXECUTE;
        $expected = 'object.execute';
        \Flexio\Tests\Check::assertString('A.5', 'Action type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Action::TYPE_READ_RIGHTS;
        $expected = 'rights.read';
        \Flexio\Tests\Check::assertString('A.6', 'Action type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Action::TYPE_WRITE_RIGHTS;
        $expected = 'rights.write';
        \Flexio\Tests\Check::assertString('A.7', 'Action type',  $actual, $expected, $results);
    }
}
