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



        // TEST: stream read/writer; single field/value

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('B.1', 'StreamReader/StreamWriter; single field with a null value',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('B.2', 'StreamReader/StreamWriter; single field with a null value',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('B.3', 'StreamReader/StreamWriter; single field with a zero-length string',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('B.4', 'StreamReader/StreamWriter; single field with a zero-length string',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('B.5', 'StreamReader/StreamWriter; single field with no keys in common',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('B.6', 'StreamReader/StreamWriter; single field with non-zero-length string',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('B.7', 'StreamReader/StreamWriter; single field with non-zero-length string',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.8', 'StreamReader/StreamWriter; keyless row array mismatch',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('B.9', 'StreamReader/StreamWriter; keyless row array mismatch',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('B.10', 'StreamReader/StreamWriter; keyed row array mismatch',  $actual, $expected, $results);



        // TEST: stream read/writer; multiple field/value, single row

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('C.1', 'StreamReader/StreamWriter; multiple null values',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('C.2', 'StreamReader/StreamWriter; multiple null values',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('C.3', 'StreamReader/StreamWriter; multiple zero-length strings',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('C.4', 'StreamReader/StreamWriter; multiple zero-length strings',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('C.5', 'StreamReader/StreamWriter; single null value and non-zero-length string',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('C.6', 'StreamReader/StreamWriter; single null value and non-zero-length string',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('C.7', 'StreamReader/StreamWriter; single null value and non-zero-length string',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('C.8', 'StreamReader/StreamWriter; single null value and non-zero-length string',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('C.9', 'StreamReader/StreamWriter; non-zero-length strings',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('C.10', 'StreamReader/StreamWriter; non-zero-length strings',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.11', 'StreamReader/StreamWriter; keyless row mismatch; counts need to match',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.12', 'StreamReader/StreamWriter; keyless row mismatch; counts need to match',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('C.13', 'StreamReader/StreamWriter; inserting with a key that doesn\'t exist',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('C.14', 'StreamReader/StreamWriter; keyless row mismatch; inserting with a key that doesn\'t exist',  $actual, $expected, $results);



        // TEST: stream read/writer; single field/value, multiple row

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('D.1', 'StreamReader/StreamWriter; multiple null values',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('D.2', 'StreamReader/StreamWriter; multiple null values',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('D.3', 'StreamReader/StreamWriter; multiple zero-length string values',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('D.4', 'StreamReader/StreamWriter; multiple zero-length string values',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('D.5', 'StreamReader/StreamWriter; single null value with non-zero-length string',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('D.6', 'StreamReader/StreamWriter; single null value with non-zero-length string',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('D.7', 'StreamReader/StreamWriter; single null value with non-zero-length string',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('D.8', 'StreamReader/StreamWriter; single null value with non-zero-length string',  $actual, $expected, $results);



        // TEST: problem values

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('E.1', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('E.2', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('E.3', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('E.4', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('E.5', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('E.6', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('E.7', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('E.8', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('E.9', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('E.10', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('E.11', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('E.12', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);



        // TEST: numbers

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('F.1', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        $expected = json_decode('{ "f1" : 0}',true);
        \Flexio\Tests\Check::assertArray('F.2', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        $expected = json_decode('{ "f1" : 1}',true);
        \Flexio\Tests\Check::assertArray('F.3', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        $expected = json_decode('{ "f1" : -1}',true);
        \Flexio\Tests\Check::assertArray('F.4', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);



        // TEST: booleans

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('G.1', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('G.2', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
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
        \Flexio\Tests\Check::assertArray('G.3', 'StreamReader/StreamWriter; make sure values that could potentially cause problems are handled correctly',  $actual, $expected, $results);
    }
}
