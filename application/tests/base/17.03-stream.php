<?php
/**
 *
 * Copyright (c) 2016, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-04-20
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
        // TEST: stream read/write

        // BEGIN TEST
        $stream = \Flexio\Base\Stream::create();
        $reader = $stream->getReader();
        $actual = $reader->readline();
        $reader->close();
        $expected = "";
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Base\Stream: read/write',  $actual, $expected, $results);

        // BEGIN TEST
        $stream = \Flexio\Base\Stream::create();
        $writer = $stream->getWriter();
        $writer->write("A");
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readline();
        $reader->close();
        $expected = "A";
        \Flexio\Tests\Check::assertString('A.2', '\Flexio\Base\Stream: read/write',  $actual, $expected, $results);
    }
}
