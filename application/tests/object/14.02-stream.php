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



        // TEST: stream read/writer; basic file read/write

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
        TestCheck::assertString('A.1', 'StreamReader/StreamWriter; check basic file read/write ',  $actual, $expected, $results);

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
        TestCheck::assertString('A.2', 'StreamReader/StreamWriter; check basic file read/write ',  $actual, $expected, $results);

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
        TestCheck::assertString('A.3', 'StreamReader/StreamWriter; check basic file read/write ',  $actual, $expected, $results);



        // TEST: stream read/writer; basic table read/write

        // BEGIN TEST
        $stream_info = array();
        $stream_info['connection_eid'] = \Flexio\Object\Connection::getDatastoreConnectionEid();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Object\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"},
            { "name": "f2", "type": "text"}
        ]
        ',true))->get();
        $writer = \Flexio\Object\StreamWriter::create($stream_info);
        $data = json_decode('{ "f1" : "a", "f2": "b"}',true);
        $writer->write($data);
        $writer->close();
        $reader = \Flexio\Object\StreamReader::create($stream_info);
        $actual = $reader->readRow();
        $expected = $data;
        TestCheck::assertArray('B.1', 'StreamReader/StreamWriter; check basic table read/write ',  $actual, $expected, $results);
    }
}
