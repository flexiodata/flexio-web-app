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


declare(strict_types=1);
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
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write("");
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->read();
        $reader->close();
        $expected = "";
        TestCheck::assertString('A.1', 'StreamReader/StreamWriter; check basic file read/write ',  $actual, $expected, $results, TestCheck::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write("A");
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->read();
        $reader->close();
        $expected = "A";
        TestCheck::assertString('A.2', 'StreamReader/StreamWriter; check basic file read/write ',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->read();
        $reader->close();
        $expected = "abcdefg";
        TestCheck::assertString('A.3', 'StreamReader/StreamWriter; check basic file read/write ',  $actual, $expected, $results);



        // TEST: stream read/writer; single field/value

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('[null]',true);
        $writer->write($data);
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : null}',true);
        TestCheck::assertArray('B.1', 'StreamReader/StreamWriter; single field with a null value',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('{ "f1" : null}',true);
        $writer->write($data);
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : null}',true);
        TestCheck::assertArray('B.2', 'StreamReader/StreamWriter; single field with a null value',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('[""]',true);
        $writer->write($data);
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : ""}',true);
        TestCheck::assertArray('B.3', 'StreamReader/StreamWriter; single field with a zero-length string',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('{ "f1" : ""}',true);
        $writer->write($data);
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : ""}',true);
        TestCheck::assertArray('B.4', 'StreamReader/StreamWriter; single field with a zero-length string',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('{ "f2" : ""}',true);
        $writer->write($data);
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : null}',true);
        TestCheck::assertArray('B.5', 'StreamReader/StreamWriter; single field with no keys in common',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('["a"]',true);
        $writer->write($data);
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : "a"}',true);
        TestCheck::assertArray('B.6', 'StreamReader/StreamWriter; single field with non-zero-length string',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('{ "f1" : "a"}',true);
        $writer->write($data);
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : "a"}',true);
        TestCheck::assertArray('B.7', 'StreamReader/StreamWriter; single field with non-zero-length string',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('["a","b","c"]',true);
        $writer->write($data);
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : "a"}',true);
        TestCheck::assertArray('B.8', 'StreamReader/StreamWriter; keyless row array mismatch',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('[]',true);
        $writer->write($data);
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : null}',true);
        TestCheck::assertArray('B.9', 'StreamReader/StreamWriter; keyless row array mismatch',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('{"f2" : "a"}',true);
        $writer->write($data);
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : null}',true);
        TestCheck::assertArray('B.10', 'StreamReader/StreamWriter; keyed row array mismatch',  $actual, $expected, $results);



        // TEST: stream read/writer; multiple field/value, single row

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"},
            { "name": "f2", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('[null,null]',true);
        $writer->write($data);
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : null, "f2": null}',true);
        TestCheck::assertArray('C.1', 'StreamReader/StreamWriter; multiple null values',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"},
            { "name": "f2", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('{ "f1" : null, "f2": null}',true);
        $writer->write($data);
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : null, "f2": null}',true);
        TestCheck::assertArray('C.2', 'StreamReader/StreamWriter; multiple null values',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"},
            { "name": "f2", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('["",""]',true);
        $writer->write($data);
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : "", "f2": ""}',true);
        TestCheck::assertArray('C.3', 'StreamReader/StreamWriter; multiple zero-length strings',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"},
            { "name": "f2", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('{ "f1" : "", "f2": ""}',true);
        $writer->write($data);
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : "", "f2": ""}',true);
        TestCheck::assertArray('C.4', 'StreamReader/StreamWriter; multiple zero-length strings',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"},
            { "name": "f2", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('[null,"a"]',true);
        $writer->write($data);
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : null, "f2": "a"}',true);
        TestCheck::assertArray('C.5', 'StreamReader/StreamWriter; single null value and non-zero-length string',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"},
            { "name": "f2", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('{ "f1" : null, "f2": "a"}',true);
        $writer->write($data);
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : null, "f2": "a"}',true);
        TestCheck::assertArray('C.6', 'StreamReader/StreamWriter; single null value and non-zero-length string',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"},
            { "name": "f2", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('["a",null]',true);
        $writer->write($data);
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : "a", "f2": null}',true);
        TestCheck::assertArray('C.7', 'StreamReader/StreamWriter; single null value and non-zero-length string',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"},
            { "name": "f2", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('{ "f1" : "a", "f2": null}',true);
        $writer->write($data);
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : "a", "f2": null}',true);
        TestCheck::assertArray('C.8', 'StreamReader/StreamWriter; single null value and non-zero-length string',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"},
            { "name": "f2", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('["a","b"]',true);
        $writer->write($data);
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : "a", "f2": "b"}',true);
        TestCheck::assertArray('C.9', 'StreamReader/StreamWriter; non-zero-length strings',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"},
            { "name": "f2", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('{ "f1" : "a", "f2": "b"}',true);
        $writer->write($data);
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : "a", "f2": "b"}',true);
        TestCheck::assertArray('C.10', 'StreamReader/StreamWriter; non-zero-length strings',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"},
            { "name": "f2", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('["a","b","c"]',true);
        $writer->write($data);
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : "a", "f2": "b"}',true);
        TestCheck::assertArray('C.11', 'StreamReader/StreamWriter; keyless row mismatch',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"},
            { "name": "f2", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('["a"]',true);
        $writer->write($data);
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : "a", "f2": null}',true);
        TestCheck::assertArray('C.12', 'StreamReader/StreamWriter; keyless row mismatch',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"},
            { "name": "f2", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('{ "f1" : "a", "f3": "b"}',true);
        $writer->write($data);
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : "a", "f2": null}',true);
        TestCheck::assertArray('C.13', 'StreamReader/StreamWriter; keyless row mismatch',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"},
            { "name": "f2", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('{ "f2" : "a", "f3": "b"}',true);
        $writer->write($data);
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : null, "f2": "a"}',true);
        TestCheck::assertArray('C.14', 'StreamReader/StreamWriter; keyless row mismatch',  $actual, $expected, $results);



        // TEST: stream read/writer; single field/value, multiple row

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('[null]',true));
        $writer->write(json_decode('[null]',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = array($reader->readRow(), $reader->readRow());
        $reader->close();
        $expected = json_decode('[{ "f1" : null}, {"f1": null}]',true);
        TestCheck::assertArray('D.1', 'StreamReader/StreamWriter; multiple null values',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('{ "f1" : null}',true));
        $writer->write(json_decode('{ "f1" : null}',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = array($reader->readRow(), $reader->readRow());
        $reader->close();
        $expected = json_decode('[{ "f1" : null}, {"f1": null}]',true);
        TestCheck::assertArray('D.2', 'StreamReader/StreamWriter; multiple null values',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('[""]',true));
        $writer->write(json_decode('[""]',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = array($reader->readRow(), $reader->readRow());
        $reader->close();
        $expected = json_decode('[{ "f1" : ""}, {"f1": ""}]',true);
        TestCheck::assertArray('D.3', 'StreamReader/StreamWriter; multiple zero-length string values',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('{ "f1" : ""}',true));
        $writer->write(json_decode('{ "f1" : ""}',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = array($reader->readRow(), $reader->readRow());
        $reader->close();
        $expected = json_decode('[{ "f1" : ""}, {"f1": ""}]',true);
        TestCheck::assertArray('D.4', 'StreamReader/StreamWriter; multiple zero-length string values',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('["a"]',true));
        $writer->write(json_decode('[null]',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = array($reader->readRow(), $reader->readRow());
        $reader->close();
        $expected = json_decode('[{ "f1" : "a"}, {"f1": null}]',true);
        TestCheck::assertArray('D.5', 'StreamReader/StreamWriter; single null value with non-zero-length string',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('{ "f1" : "a"}',true));
        $writer->write(json_decode('{ "f1" : null}',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = array($reader->readRow(), $reader->readRow());
        $reader->close();
        $expected = json_decode('[{ "f1" : "a"}, {"f1": null}]',true);
        TestCheck::assertArray('D.6', 'StreamReader/StreamWriter; single null value with non-zero-length string',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('[null]',true));
        $writer->write(json_decode('["a"]',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = array($reader->readRow(), $reader->readRow());
        $reader->close();
        $expected = json_decode('[{ "f1" : null}, {"f1": "a"}]',true);
        TestCheck::assertArray('D.7', 'StreamReader/StreamWriter; single null value with non-zero-length string',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('{ "f1" : null}',true));
        $writer->write(json_decode('{ "f1" : "a"}',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = array($reader->readRow(), $reader->readRow());
        $reader->close();
        $expected = json_decode('[{ "f1" : null}, {"f1": "a"}]',true);
        TestCheck::assertArray('D.8', 'StreamReader/StreamWriter; single null value with non-zero-length string',  $actual, $expected, $results);



        // TEST: problem values

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('{ "f1" : null}',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : null}',true);
        TestCheck::assertArray('E.1', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('{ "f1" : ""}',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : ""}',true);
        TestCheck::assertArray('E.2', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('{ "f1" : "\t"}',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : "\t"}',true);
        TestCheck::assertArray('E.3', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('{ "f1" : "\r"}',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : "\r"}',true);
        TestCheck::assertArray('E.4', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('{ "f1" : "\n"}',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : "\n"}',true);
        TestCheck::assertArray('E.5', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('{ "f1" : "\u0000"}',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : ""}',true);
        TestCheck::assertArray('E.6', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('{ "f1" : "\'"}',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : "\'"}',true);
        TestCheck::assertArray('E.7', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('{ "f1" : "\""}',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : "\""}',true);
        TestCheck::assertArray('E.8', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('{ "f1" : "("}',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : "("}',true);
        TestCheck::assertArray('E.9', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('{ "f1" : ")"}',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : ")"}',true);
        TestCheck::assertArray('E.10', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('{ "f1" : "/"}',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : "/"}',true);
        TestCheck::assertArray('E.11', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "text"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('{ "f1" : "\\\\"}',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : "\\\\"}',true);
        TestCheck::assertArray('E.12', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);



        // TEST: numbers

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "numeric", "width": 10, "scale": 0}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('{ "f1" : null}',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : null}',true);
        TestCheck::assertArray('F.1', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "numeric", "width": 10, "scale": 0}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('{ "f1" : 0}',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : "0"}',true);
        TestCheck::assertArray('F.2', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "numeric", "width": 10, "scale": 0}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('{ "f1" : 1}',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : "1"}',true);
        TestCheck::assertArray('F.3', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "numeric", "width": 10, "scale": 0}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('{ "f1" : -1}',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : "-1"}',true);
        TestCheck::assertArray('F.4', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);



        // TEST: booleans

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "boolean"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('{ "f1" : null}',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : null}',true);
        TestCheck::assertArray('G.1', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "boolean"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('{ "f1" : true}',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : true}',true);
        TestCheck::assertArray('G.2', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            { "name": "f1", "type": "boolean"}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write(json_decode('{ "f1" : false}',true));
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->readRow();
        $reader->close();
        $expected = json_decode('{ "f1" : false}',true);
        TestCheck::assertArray('G.3', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);
    }
}
