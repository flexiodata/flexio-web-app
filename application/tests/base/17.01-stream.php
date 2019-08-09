<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
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
        // TEST: object creation

        // BEGIN TEST
        $object = \Flexio\Base\Stream::create();
        $actual = 'Flexio\Base\Stream';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('A.1', 'Stream::create(); return the object if it\'s successfully created',  $actual, $expected, $results);




        // TEST: object property setting

        // BEGIN TEST
        $object = \Flexio\Base\Stream::create();
        $object = $object->set([]);
        $actual =  'Flexio\Base\Stream';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('D.1', 'Stream::set(); return the object',  $actual, $expected, $results);



        // TEST: object property retrieval

        // BEGIN TEST
        $object = \Flexio\Base\Stream::create();
        $properties = $object->get();
        $actual =  is_array($properties);
        $expected = true;
        \Flexio\Tests\Check::assertString('E.1', 'Stream::get(); return the properties as an array',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Base\Stream::create();
        $properties = $object->get();
        $actual =  $properties;
        $expected = json_decode('
        {
            "name" : null,
            "path" : null,
            "size" : null,
            "hash" : null,
            "mime_type" : null,
            "structure" : {
            },
            "file_created" : null,
            "file_modified" : null,
            "created" : null,
            "updated" : null
        }
        ',true);
        \Flexio\Tests\Check::assertArrayKeys('E.2', 'Stream::get(); return the properties as an array',  $actual, $expected, $results);



        // TEST: basic content query

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, PHP_INT_MAX, 1);
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('G.1', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, PHP_INT_MAX, 2);
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('G.2', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, PHP_INT_MAX, 3);
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('G.3', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, PHP_INT_MAX, 4);
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('G.4', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, PHP_INT_MAX, 5);
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('G.5', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, PHP_INT_MAX, 6);
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('G.6', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, PHP_INT_MAX, 7);
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('G.7', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, PHP_INT_MAX, 100);
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('G.8', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 0, 1);
        $expected = '';
        \Flexio\Tests\Check::assertString('G.9', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 0, 2);
        $expected = '';
        \Flexio\Tests\Check::assertString('G.10', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 0, 3);
        $expected = '';
        \Flexio\Tests\Check::assertString('G.11', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 0, 4);
        $expected = '';
        \Flexio\Tests\Check::assertString('G.12', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 0, 5);
        $expected = '';
        \Flexio\Tests\Check::assertString('G.13', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 0, 6);
        $expected = '';
        \Flexio\Tests\Check::assertString('G.14', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 0, 7);
        $expected = '';
        \Flexio\Tests\Check::assertString('G.15', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 0, 100);
        $expected = '';
        \Flexio\Tests\Check::assertString('G.16', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 1, 1);
        $expected = "a";
        \Flexio\Tests\Check::assertString('G.17', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 1, 2);
        $expected = "a";
        \Flexio\Tests\Check::assertString('G.18', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 1, 3);
        $expected = "a";
        \Flexio\Tests\Check::assertString('G.19', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 1, 4);
        $expected = "a";
        \Flexio\Tests\Check::assertString('G.20', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 1, 5);
        $expected = "a";
        \Flexio\Tests\Check::assertString('G.21', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 1, 6);
        $expected = "a";
        \Flexio\Tests\Check::assertString('G.22', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 1, 7);
        $expected = "a";
        \Flexio\Tests\Check::assertString('G.23', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 1, 100);
        $expected = "a";
        \Flexio\Tests\Check::assertString('G.24', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 1, 1, 1);
        $expected = "b";
        \Flexio\Tests\Check::assertString('G.25', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 1, 1, 2);
        $expected = "b";
        \Flexio\Tests\Check::assertString('G.26', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 1, 1, 3);
        $expected = "b";
        \Flexio\Tests\Check::assertString('G.27', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 1, 1, 4);
        $expected = "b";
        \Flexio\Tests\Check::assertString('G.28', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 1, 1, 5);
        $expected = "b";
        \Flexio\Tests\Check::assertString('G.29', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 1, 1, 6);
        $expected = "b";
        \Flexio\Tests\Check::assertString('G.30', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 1, 1, 7);
        $expected = "b";
        \Flexio\Tests\Check::assertString('G.31', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 1, 1, 100);
        $expected = "b";
        \Flexio\Tests\Check::assertString('G.32', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 6, 1, 1);
        $expected = "g";
        \Flexio\Tests\Check::assertString('G.33', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 6, 1, 2);
        $expected = "g";
        \Flexio\Tests\Check::assertString('G.34', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 6, 1, 3);
        $expected = "g";
        \Flexio\Tests\Check::assertString('G.35', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 6, 1, 4);
        $expected = "g";
        \Flexio\Tests\Check::assertString('G.36', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 6, 1, 5);
        $expected = "g";
        \Flexio\Tests\Check::assertString('G.37', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 6, 1, 6);
        $expected = "g";
        \Flexio\Tests\Check::assertString('G.38', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 6, 1, 7);
        $expected = "g";
        \Flexio\Tests\Check::assertString('G.39', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 6, 1, 100);
        $expected = "g";
        \Flexio\Tests\Check::assertString('G.40', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 7, 1, 1);
        $expected = '';
        \Flexio\Tests\Check::assertString('G.41', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 7, 1, 2);
        $expected = '';
        \Flexio\Tests\Check::assertString('G.42', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 7, 1, 3);
        $expected = '';
        \Flexio\Tests\Check::assertString('G.43', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 7, 1, 4);
        $expected = '';
        \Flexio\Tests\Check::assertString('G.44', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 7, 1, 5);
        $expected = '';
        \Flexio\Tests\Check::assertString('G.45', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 7, 1, 6);
        $expected = '';
        \Flexio\Tests\Check::assertString('G.46', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 7, 1, 7);
        $expected = '';
        \Flexio\Tests\Check::assertString('G.47', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 7, 1, 100);
        $expected = '';
        \Flexio\Tests\Check::assertString('G.48', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 2, 1);
        $expected = "ab";
        \Flexio\Tests\Check::assertString('G.49', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 2, 2);
        $expected = "ab";
        \Flexio\Tests\Check::assertString('G.50', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 2, 3);
        $expected = "ab";
        \Flexio\Tests\Check::assertString('G.51', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 2, 4);
        $expected = "ab";
        \Flexio\Tests\Check::assertString('G.52', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 2, 5);
        $expected = "ab";
        \Flexio\Tests\Check::assertString('G.53', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 2, 6);
        $expected = "ab";
        \Flexio\Tests\Check::assertString('G.54', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 2, 7);
        $expected = "ab";
        \Flexio\Tests\Check::assertString('G.55', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 2, 100);
        $expected = "ab";
        \Flexio\Tests\Check::assertString('G.56', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 4, 2, 1);
        $expected = "ef";
        \Flexio\Tests\Check::assertString('G.57', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 4, 2, 2);
        $expected = "ef";
        \Flexio\Tests\Check::assertString('G.58', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 4, 2, 3);
        $expected = "ef";
        \Flexio\Tests\Check::assertString('G.59', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 4, 2, 4);
        $expected = "ef";
        \Flexio\Tests\Check::assertString('G.60', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 4, 2, 5);
        $expected = "ef";
        \Flexio\Tests\Check::assertString('G.61', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 4, 2, 6);
        $expected = "ef";
        \Flexio\Tests\Check::assertString('G.62', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 4, 2, 7);
        $expected = "ef";
        \Flexio\Tests\Check::assertString('G.63', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 4, 2, 100);
        $expected = "ef";
        \Flexio\Tests\Check::assertString('G.64', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 3, 3, 1);
        $expected = "def";
        \Flexio\Tests\Check::assertString('G.65', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 3, 3, 2);
        $expected = "def";
        \Flexio\Tests\Check::assertString('G.66', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 3, 3, 3);
        $expected = "def";
        \Flexio\Tests\Check::assertString('G.67', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 3, 3, 4);
        $expected = "def";
        \Flexio\Tests\Check::assertString('G.68', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 3, 3, 5);
        $expected = "def";
        \Flexio\Tests\Check::assertString('G.69', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 3, 3, 6);
        $expected = "def";
        \Flexio\Tests\Check::assertString('G.70', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 3, 3, 7);
        $expected = "def";
        \Flexio\Tests\Check::assertString('G.71', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 3, 3, 100);
        $expected = "def";
        \Flexio\Tests\Check::assertString('G.72', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, -1, 2, 1);
        $expected = "ab";
        \Flexio\Tests\Check::assertString('G.73', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, -1, 1);
        $expected = '';
        \Flexio\Tests\Check::assertString('G.74', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 100, 1);
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('G.75', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 100, 0);
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('G.76', 'Stream; check basic content query',  $actual, $expected, $results);
    }
}
