<?php
/**
 *
 * Copyright (c) 2020, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2020-06-02
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
        // TEST: resource read/write

        // BEGIN TEST
        $resource = \Flexio\Base\Resource::create();
        $actual = $resource->read();
        $resource->close();
        $expected = "";
        \Flexio\Tests\Check::assertString('A.1', 'Resource; read/write',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $resource = \Flexio\Base\Resource::create();
        $resource->write("A");
        $resource->rewind();
        $actual = $resource->read();
        $resource->close();
        $expected = "A";
        \Flexio\Tests\Check::assertString('A.2', 'Resource; read/write',  $actual, $expected, $results);

        // BEGIN TEST
        $resource = \Flexio\Base\Resource::create();
        $resource->write("abcdefg");
        $resource->rewind();
        $actual = $resource->read();
        $resource->close();
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('A.3', 'Resource; read/write',  $actual, $expected, $results);

        // BEGIN TEST
        $resource = \Flexio\Base\Resource::create();
        $resource->write("abcdefg");
        $resource->rewind();
        $actual = $resource->read(0);
        $resource->close();
        $expected = "";
        \Flexio\Tests\Check::assertString('A.4', 'Resource; read/write',  $actual, $expected, $results);

        // BEGIN TEST
        $resource = \Flexio\Base\Resource::create();
        $resource->write("abcdefg");
        $resource->rewind();
        $actual = $resource->read(1);
        $resource->close();
        $expected = "a";
        \Flexio\Tests\Check::assertString('A.5', 'Resource; read/write',  $actual, $expected, $results);

        // BEGIN TEST
        $resource = \Flexio\Base\Resource::create();
        $resource->write("abcdefg");
        $resource->rewind();
        $actual = $resource->read(2);
        $resource->close();
        $expected = "ab";
        \Flexio\Tests\Check::assertString('A.6', 'Resource; read/write',  $actual, $expected, $results);

        // BEGIN TEST
        $resource = \Flexio\Base\Resource::create();
        $resource->write("abcdefg");
        $resource->rewind();
        $actual = $resource->read(6);
        $resource->close();
        $expected = "abcdef";
        \Flexio\Tests\Check::assertString('A.7', 'Resource; read/write',  $actual, $expected, $results);

        // BEGIN TEST
        $resource = \Flexio\Base\Resource::create();
        $resource->write("abcdefg");
        $resource->rewind();
        $actual = $resource->read(7);
        $resource->close();
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('A.8', 'Resource; read/write',  $actual, $expected, $results);

        // BEGIN TEST
        $resource = \Flexio\Base\Resource::create();
        $resource->write("abcdefg");
        $resource->rewind();
        $actual = $resource->read(8);
        $resource->close();
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('A.9', 'Resource; read/write',  $actual, $expected, $results);
    }
}
