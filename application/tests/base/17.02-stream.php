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
/*
        // TEST: stream read/write

        // BEGIN TEST
        $stream = \Flexio\Base\Stream::create();
        $reader = $stream->getReader();
        $actual = $reader->read();
        $reader->close();
        $expected = "";
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Base\Stream; read/write',  $actual, $expected, $results);

        // BEGIN TEST
        $stream = \Flexio\Base\Stream::create();
        $writer = $stream->getWriter();
        $writer->write("A");
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->read();
        $reader->close();
        $expected = "A";
        \Flexio\Tests\Check::assertString('A.2', '\Flexio\Base\Stream; read/write',  $actual, $expected, $results);


        // TEST: stream read/writer; multiple

        // BEGIN TEST
        $stream = \Flexio\Base\Stream::create();
        $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->read();
        $reader->close();
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('B.1', '\Flexio\Base\Stream; read/write',  $actual, $expected, $results);

        // BEGIN TEST
        $stream = \Flexio\Base\Stream::create();
        $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->read(0);
        $reader->close();
        $expected = "";
        \Flexio\Tests\Check::assertString('B.2', '\Flexio\Base\Stream; read/write',  $actual, $expected, $results);

        // BEGIN TEST
        $stream = \Flexio\Base\Stream::create();
        $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->read(1);
        $reader->close();
        $expected = "a";
        \Flexio\Tests\Check::assertString('B.3', '\Flexio\Base\Stream; read/write',  $actual, $expected, $results);

        // BEGIN TEST
        $stream = \Flexio\Base\Stream::create();
        $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->read(2);
        $reader->close();
        $expected = "ab";
        \Flexio\Tests\Check::assertString('B.4', '\Flexio\Base\Stream; read/write',  $actual, $expected, $results);

        // BEGIN TEST
        $stream = \Flexio\Base\Stream::create();
        $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->read(6);
        $reader->close();
        $expected = "abcdef";
        \Flexio\Tests\Check::assertString('B.5', '\Flexio\Base\Stream; read/write',  $actual, $expected, $results);

        // BEGIN TEST
        $stream = \Flexio\Base\Stream::create();
        $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->read(7);
        $reader->close();
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('B.6', '\Flexio\Base\Stream; read/write',  $actual, $expected, $results);

        // BEGIN TEST
        $stream = \Flexio\Base\Stream::create();
        $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $reader = $stream->getReader();
        $actual = $reader->read(8);
        $reader->close();
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('B.7', '\Flexio\Base\Stream; read/write',  $actual, $expected, $results);
*/

        // TEST: stream read/write

        // BEGIN TEST
        $stream = \Flexio\Base\Stream::create();

        $test_string = 'abcdefghi\n';
        $actual_string = '';
        $expected_string = '';

        $writer = $stream->getWriter();
        for ($i = 0; $i < 10000; $i++) // number of interations times chars in test string should be less than 2MB but greater than 1024 read length
        {
            $actual_string .= $test_string;
            $writer->write($test_string);
        }
        $writer->close();

        $reader = $stream->getReader();
        while (true)
        {
            $row = $reader->read(1024);
            if ($row === false)
                break;

            $expected_string .= $row;
        }
        $reader->close();

        // make sure output is equal
        $actual = strlen($actual_string) < 2000000; // something less than 2MB
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.1', '\Flexio\Base\Stream; read/write with large sizes to test conversion from memory to file',  $actual, $expected, $results);

        // make sure output is equal
        $actual = strlen($actual_string);
        $expected = strlen($expected_string);
        \Flexio\Tests\Check::assertNumber('C.2', '\Flexio\Base\Stream; read/write with large sizes to test conversion from memory to file',  $actual, $expected, $results);

        $actual = $actual_string;
        $expected = $expected_string;
        \Flexio\Tests\Check::assertString('C.3', '\Flexio\Base\Stream; read/write with large sizes to test conversion from memory to file',  $actual, $expected, $results);


        // BEGIN TEST
        $stream = \Flexio\Base\Stream::create();

        $test_string = 'abcdefghi\n';
        $actual_string = '';
        $expected_string = '';

        $writer = $stream->getWriter();
        for ($i = 0; $i < 250000; $i++) // number of interations times chars in test string should be greater than 2MB
        {
            $actual_string .= $test_string;
            $writer->write($test_string);
        }
        $writer->close();

        $reader = $stream->getReader();
        while (true)
        {
            $row = $reader->read(1024);
            if ($row === false)
                break;

            $expected_string .= $row;
        }
        $reader->close();

        // make sure output is equal
        $actual = strlen($actual_string) > 2100000; // something greater than 2MB
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.4', '\Flexio\Base\Stream; read/write with large sizes to test conversion from memory to file',  $actual, $expected, $results);

        // make sure output is equal
        $actual = strlen($actual_string);
        $expected = strlen($expected_string);
        \Flexio\Tests\Check::assertNumber('C.5', '\Flexio\Base\Stream; read/write with large sizes to test conversion from memory to file',  $actual, $expected, $results);

        $actual = $actual_string;
        $expected = $expected_string;
        \Flexio\Tests\Check::assertString('C.6', '\Flexio\Base\Stream; read/write with large sizes to test conversion from memory to file',  $actual, $expected, $results);
    }
}
