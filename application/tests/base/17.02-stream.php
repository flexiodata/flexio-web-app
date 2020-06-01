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
        // TEST: stream read/writer; basic file read/write

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write("");
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->read();
        $reader->close();
        $expected = "";
        \Flexio\Tests\Check::assertString('A.1', 'StreamReader/StreamWriter; check basic file read/write ',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write("A");
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->read();
        $reader->close();
        $expected = "A";
        \Flexio\Tests\Check::assertString('A.2', 'StreamReader/StreamWriter; check basic file read/write ',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->read();
        $reader->close();
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('A.3', 'StreamReader/StreamWriter; check basic file read/write ',  $actual, $expected, $results);
    }
}
