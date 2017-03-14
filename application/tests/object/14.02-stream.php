<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-04-20
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TEST: stream read/writer

        // BEGIN TEST
        $stream_info = array();
        $stream_info['connection_eid'] = \Flexio\Object\Connection::getDatastoreConnectionEid();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $writer = \Flexio\Object\StreamWriter::create($stream_info);
        $writer->write("");
        $writer->close();
        $reader = \Flexio\Object\StreamReader::create($stream_info);
        $actual = $reader->read();
        $expected = "";
        TestCheck::assertString('A.1', 'StreamReader/StreamWriter; check basic read/write ',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['connection_eid'] = \Flexio\Object\Connection::getDatastoreConnectionEid();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $writer = \Flexio\Object\StreamWriter::create($stream_info);
        $writer->write("A");
        $writer->close();
        $reader = \Flexio\Object\StreamReader::create($stream_info);
        $actual = $reader->read();
        $expected = "A";
        TestCheck::assertString('A.2', 'StreamReader/StreamWriter; check basic read/write ',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['connection_eid'] = \Flexio\Object\Connection::getDatastoreConnectionEid();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $writer = \Flexio\Object\StreamWriter::create($stream_info);
        $writer->write("abcdefg");
        $writer->close();
        $reader = \Flexio\Object\StreamReader::create($stream_info);
        $actual = $reader->read();
        $expected = "abcdefg";
        TestCheck::assertString('A.3', 'StreamReader/StreamWriter; check basic read/write ',  $actual, $expected, $results);
    }
}
