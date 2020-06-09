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
        $resource->write("abcd\nefg");
        $resource->rewind();
        $actual = $resource->readLine();
        $resource->close();
        $expected = "abcd\n";
        \Flexio\Tests\Check::assertString('A.1', 'Resource; read/write',  $actual, $expected, $results);

        // BEGIN TEST
        $resource = \Flexio\Base\Resource::create();
        $resource->write("abcd\nefg");
        $resource->rewind();
        $resource->readLine();
        $actual = $resource->readLine();
        $resource->close();
        $expected = "efg";
        \Flexio\Tests\Check::assertString('A.2', 'Resource; read/write',  $actual, $expected, $results);
    }
}
