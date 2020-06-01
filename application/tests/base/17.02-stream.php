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
        // TEST: stream read()

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream = \Flexio\Base\Stream::create($stream_info);
        $reader = $stream->getReader();
        $actual = $reader->read();
        $reader->close();
        $expected = "";
        \Flexio\Tests\Check::assertString('A.1', 'StreamReader/StreamWriter; check read()',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write("A");
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->read();
        $reader->close();
        $expected = "A";
        \Flexio\Tests\Check::assertString('A.2', 'StreamReader/StreamWriter; check read()',  $actual, $expected, $results);


        // TEST: stream read/writer; multiple

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->read();
        $reader->close();
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('B.1', 'StreamReader/StreamWriter; check read()',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->read(0);
        $reader->close();
        $expected = "";
        \Flexio\Tests\Check::assertString('B.2', 'StreamReader/StreamWriter; check read()',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->read(1);
        $reader->close();
        $expected = "a";
        \Flexio\Tests\Check::assertString('B.3', 'StreamReader/StreamWriter; check read()',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->read(2);
        $reader->close();
        $expected = "ab";
        \Flexio\Tests\Check::assertString('B.4', 'StreamReader/StreamWriter; check read()',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->read(6);
        $reader->close();
        $expected = "abcdef";
        \Flexio\Tests\Check::assertString('B.5', 'StreamReader/StreamWriter; check read()',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->read(7);
        $reader->close();
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('B.6', 'StreamReader/StreamWriter; check read()',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->read(8);
        $reader->close();
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('B.7', 'StreamReader/StreamWriter; check read()',  $actual, $expected, $results);
    }
}
