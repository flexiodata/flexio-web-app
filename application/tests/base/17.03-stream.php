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
        // TEST: test range of rows/values on character type

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            {"name" : "id", "type" : "integer", "width" : 4, "scale" : 0},
            {"name" : "char_1a", "type" : "character", "width" : 10, "scale" : 0},
            {"name" : "char_1b", "type" : "character", "width" : 10, "scale" : 0},
            {"name" : "char_1c", "type" : "character", "width" : 10, "scale" : 0},
            {"name" : "char_1d", "type" : "character", "width" : 10, "scale" : 0},
            {"name" : "char_1e", "type" : "character", "width" : 254, "scale" : 0},
            {"name" : "char_1f", "type" : "character", "width" : 254, "scale" : 0},
            {"name" : "char_1g", "type" : "character", "width" : 254, "scale" : 0},
            {"name" : "char_1h", "type" : "character", "width" : 254, "scale" : 0}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('
        [
            ["1",  "A", "A", "",  "D",  "D00000", "00000D", "D00000", "00000D"],
            ["2",  "A", "",  "",  "D",  "D00000", "00000D", "D00000", "00000D"],
            ["3",  "A", "",  "",  "D",  "D00000", "00000D", "D00000", "00000D"],
            ["4",  "A", "",  "",  null, "D00000", "00000D", "D00000", "00000D"],
            ["5",  "A", "",  "",  null, "D00000", "00000D", "D00000", "00000D"],
            ["6",  "A", "",  "",  "b",  "b00000", "00000b", "b00000", "00000b"],
            ["7",  "A", "",  "",  "c",  "c00000", "00000c", "c00000", "00000c"],
            ["8",  "A", "",  "",  "a",  "a00000", "00000a", "a00000", "00000a"],
            ["9",  "A", "",  "",  "B",  "B00000", "00000B", "B00000", "00000B"],
            ["10", "A", "",  "",  "C",  "C00000", "00000C", "C00000", "00000C"],
            ["11", "A", "",  "",  "A",  "A00000", "00000A", "A00000", "00000A"],
            ["12", "A", "",  "",  "d",  "d00000", "00000d", "d00000", "00000d"],
            ["13", "A", "",  "",  "d",  "d00000", "00000d", "d00000", "00000d"],
            ["14", "A", "",  "",  "d",  "d00000", "00000d", "d00000", "00000d"],
            ["15", "A", "",  "",  null, "d00000", "00000d", "d00000", "00000d"],
            ["16", "A", "",  "",  null, "E00000", "00000E", "E00000", "00000E"],
            ["17", "A", "",  "",  null, "E00000", "00000E", "E00000", "00000E"],
            ["18", "A", "",  "",  "E",  "E00000", "00000E", "E00000", "00000E"],
            ["19", "A", "",  "",  "E",  "E00000", "00000E", "E00000", "00000E"],
            ["20", "A", "",  "A", "E",  "E00000", "00000E", "E00000", "00000E"]
        ]
        ',true);
        foreach ($data as $row)
        {
            $writer->write($row);
        }
        $writer->close();
        $reader = $stream->getReader();
        $last_row = false;
        while (true)
        {
            $row = $reader->readRow();
            if ($row === false)
                break;

            $last_row = $row;
        }
        $reader->close();
        $actual = $last_row;
        $expected = json_decode('
        {
            "id": 20,
            "char_1a": "A",
            "char_1b": "",
            "char_1c": "A",
            "char_1d": "E",
            "char_1e": "E00000",
            "char_1f": "00000E",
            "char_1g": "E00000",
            "char_1h": "00000E"
        }
        ',true);
        \Flexio\Tests\Check::assertArray('A.1', 'StreamReader/StreamWriter; test character type with multiple rows and combinations of values',  $actual, $expected, $results);



        // TEST: test range of rows/values on numeric type

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            {"name" : "num_1a", "type" : "numeric", "width" : 10, "scale" : 0},
            {"name" : "num_1b", "type" : "numeric", "width" : 10, "scale" : 0},
            {"name" : "num_1c", "type" : "numeric", "width" : 10, "scale" : 0},
            {"name" : "num_1d", "type" : "numeric", "width" : 10, "scale" : 0},
            {"name" : "num_1e", "type" : "numeric", "width" : 18, "scale" : 0},
            {"name" : "num_1f", "type" : "numeric", "width" : 18, "scale" : 12},
            {"name" : "num_1g", "type" : "numeric", "width" : 18, "scale" : 0},
            {"name" : "num_1h", "type" : "numeric", "width" : 18, "scale" : 12}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('
        [
            [1, 1, 0,    4,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004],
            [1, 0, 0,    4,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004],
            [1, 0, 0,    4,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004],
            [1, 0, 0, null,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004],
            [1, 0, 0, null,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004],
            [1, 0, 0,   -2, -2000000000000, -0.000000000002, -2000000000000, -0.000000000002],
            [1, 0, 0,   -3, -3000000000000, -0.000000000003, -3000000000000, -0.000000000003],
            [1, 0, 0,   -1, -1000000000000, -0.000000000001, -1000000000000, -0.000000000001],
            [1, 0, 0,    2,  2000000000000,  0.000000000002,  2000000000000,  0.000000000002],
            [1, 0, 0,    3,  3000000000000,  0.000000000003,  3000000000000,  0.000000000003],
            [1, 0, 0,    1,  1000000000000,  0.000000000001,  1000000000000,  0.000000000001],
            [1, 0, 0,   -4, -4000000000000, -0.000000000004, -4000000000000, -0.000000000004],
            [1, 0, 0,   -4, -4000000000000, -0.000000000004, -4000000000000, -0.000000000004],
            [1, 0, 0,   -4, -4000000000000, -0.000000000004, -4000000000000, -0.000000000004],
            [1, 0, 0, null, -4000000000000, -0.000000000004, -4000000000000, -0.000000000004],
            [1, 0, 0, null,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005],
            [1, 0, 0, null,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005],
            [1, 0, 0,    5,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005],
            [1, 0, 0,    5,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005],
            [1, 0, 1,    5,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005]
        ]
        ',true);
        foreach ($data as $row)
        {
            $writer->write($row);
        }
        $writer->close();
        $reader = $stream->getReader();
        $last_row = false;
        while (true)
        {
            $row = $reader->readRow();
            if ($row === false)
                break;

            $last_row = $row;
        }
        $reader->close();
        $actual = $last_row;
        $expected = json_decode('
        {
            "num_1a": 1,
            "num_1b": 0,
            "num_1c": 1,
            "num_1d": 5,
            "num_1e": 5000000000000,
            "num_1f": 0.000000000005,
            "num_1g": 5000000000000,
            "num_1h": 0.000000000005
        }
        ',true);
        \Flexio\Tests\Check::assertArray('B.1', 'StreamReader/StreamWriter; test numeric type with multiple rows and combinations of values',  $actual, $expected, $results);



        // TEST: test range of rows/values on double type

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            {"name" : "num_2a", "type" : "double", "width" : 8, "scale" : 0},
            {"name" : "num_2b", "type" : "double", "width" : 8, "scale" : 0},
            {"name" : "num_2c", "type" : "double", "width" : 8, "scale" : 0},
            {"name" : "num_2d", "type" : "double", "width" : 8, "scale" : 0},
            {"name" : "num_2e", "type" : "double", "width" : 8, "scale" : 0},
            {"name" : "num_2f", "type" : "double", "width" : 8, "scale" : 12},
            {"name" : "num_2g", "type" : "double", "width" : 8, "scale" : 0},
            {"name" : "num_2h", "type" : "double", "width" : 8, "scale" : 12}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('
        [
            [1, 1, 0,     4,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004],
            [1, 0, 0,     4,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004],
            [1, 0, 0,     4,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004],
            [1, 0, 0,  null,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004],
            [1, 0, 0,  null,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004],
            [1, 0, 0,    -2, -2000000000000, -0.000000000002, -2000000000000, -0.000000000002],
            [1, 0, 0,    -3, -3000000000000, -0.000000000003, -3000000000000, -0.000000000003],
            [1, 0, 0,    -1, -1000000000000, -0.000000000001, -1000000000000, -0.000000000001],
            [1, 0, 0,     2,  2000000000000,  0.000000000002,  2000000000000,  0.000000000002],
            [1, 0, 0,     3,  3000000000000,  0.000000000003,  3000000000000,  0.000000000003],
            [1, 0, 0,     1,  1000000000000,  0.000000000001,  1000000000000,  0.000000000001],
            [1, 0, 0,    -4, -4000000000000, -0.000000000004, -4000000000000, -0.000000000004],
            [1, 0, 0,    -4, -4000000000000, -0.000000000004, -4000000000000, -0.000000000004],
            [1, 0, 0,    -4, -4000000000000, -0.000000000004, -4000000000000, -0.000000000004],
            [1, 0, 0,  null, -4000000000000, -0.000000000004, -4000000000000, -0.000000000004],
            [1, 0, 0,  null,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005],
            [1, 0, 0,  null,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005],
            [1, 0, 0,     5,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005],
            [1, 0, 0,     5,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005],
            [1, 0, 1,     5,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005]
        ]
        ',true);
        foreach ($data as $row)
        {
            $writer->write($row);
        }
        $writer->close();
        $reader = $stream->getReader();
        $last_row = false;
        while (true)
        {
            $row = $reader->readRow();
            if ($row === false)
                break;

            $last_row = $row;
        }
        $reader->close();
        $actual = $last_row;
        $expected = array(
            "num_2a" => (float)1,
            "num_2b" => (float)0,
            "num_2c" => (float)1,
            "num_2d" => (float)5,
            "num_2e" => (float)5000000000000,
            "num_2f" => (float)0.000000000005,
            "num_2g" => (float)5000000000000,
            "num_2h" => (float)0.000000000005
        );
        \Flexio\Tests\Check::assertArray('C.1', 'StreamReader/StreamWriter; test double type with multiple rows and combinations of values',  $actual, $expected, $results);



        // TEST: test range of rows/values on integer type

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            {"name" : "num_3a", "type" : "integer", "width" : 4, "scale" : 0},
            {"name" : "num_3b", "type" : "integer", "width" : 4, "scale" : 0},
            {"name" : "num_3c", "type" : "integer", "width" : 4, "scale" : 0},
            {"name" : "num_3d", "type" : "integer", "width" : 4, "scale" : 0},
            {"name" : "num_3e", "type" : "integer", "width" : 4, "scale" : 0},
            {"name" : "num_3f", "type" : "integer", "width" : 4, "scale" : 0},
            {"name" : "num_3g", "type" : "integer", "width" : 4, "scale" : 0},
            {"name" : "num_3h", "type" : "integer", "width" : 4, "scale" : 0}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('
        [
            [1, 1, 0,     4,  400000000,  4,  400000000,  4],
            [1, 0, 0,     4,  400000000,  4,  400000000,  4],
            [1, 0, 0,     4,  400000000,  4,  400000000,  4],
            [1, 0, 0,  null,  400000000,  4,  400000000,  4],
            [1, 0, 0,  null,  400000000,  4,  400000000,  4],
            [1, 0, 0,    -2, -200000000, -2, -200000000, -2],
            [1, 0, 0,    -3, -300000000, -3, -300000000, -3],
            [1, 0, 0,    -1, -100000000, -1, -100000000, -1],
            [1, 0, 0,     2,  200000000,  2,  200000000,  2],
            [1, 0, 0,     3,  300000000,  3,  300000000,  3],
            [1, 0, 0,     1,  100000000,  1,  100000000,  1],
            [1, 0, 0,    -4, -400000000, -4, -400000000, -4],
            [1, 0, 0,    -4, -400000000, -4, -400000000, -4],
            [1, 0, 0,    -4, -400000000, -4, -400000000, -4],
            [1, 0, 0,  null, -400000000, -4, -400000000, -4],
            [1, 0, 0,  null,  500000000,  5,  500000000,  5],
            [1, 0, 0,  null,  500000000,  5,  500000000,  5],
            [1, 0, 0,     5,  500000000,  5,  500000000,  5],
            [1, 0, 0,     5,  500000000,  5,  500000000,  5],
            [1, 0, 1,     5,  500000000,  5,  500000000,  5]
        ]
        ',true);
        foreach ($data as $row)
        {
            $writer->write($row);
        }
        $writer->close();
        $reader = $stream->getReader();
        $last_row = false;
        while (true)
        {
            $row = $reader->readRow();
            if ($row === false)
                break;

            $last_row = $row;
        }
        $reader->close();
        $actual = $last_row;
        $expected = json_decode('
        {
            "num_3a": 1,
            "num_3b": 0,
            "num_3c": 1,
            "num_3d": 5,
            "num_3e": 500000000,
            "num_3f": 5,
            "num_3g": 500000000,
            "num_3h": 5
        }
        ',true);
        \Flexio\Tests\Check::assertArray('D.1', 'StreamReader/StreamWriter; test integer type with multiple rows and combinations of values',  $actual, $expected, $results);



        // TEST: test range of rows/values on date type

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            {"name" : "date_1a", "type" : "date", "width" : 4, "scale" : 0},
            {"name" : "date_1b", "type" : "date", "width" : 4, "scale" : 0},
            {"name" : "date_1c", "type" : "date", "width" : 4, "scale" : 0},
            {"name" : "date_1d", "type" : "date", "width" : 4, "scale" : 0},
            {"name" : "date_1e", "type" : "date", "width" : 4, "scale" : 0},
            {"name" : "date_1f", "type" : "date", "width" : 4, "scale" : 0},
            {"name" : "date_1g", "type" : "date", "width" : 4, "scale" : 0},
            {"name" : "date_1h", "type" : "date", "width" : 4, "scale" : 0}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('
        [
            ["2001-01-01", "2001-01-01", "",           "2001-01-05", "2005-01-01", "2001-01-05", "2005-01-01", "2001-01-05"],
            ["2001-01-01", "",           "",           "2001-01-05", "2005-01-01", "2001-01-05", "2005-01-01", "2001-01-05"],
            ["2001-01-01", "",           "",           "2001-01-05", "2005-01-01", "2001-01-05", "2005-01-01", "2001-01-05"],
            ["2001-01-01", "",           "",           null,         "2005-01-01", "2001-01-05", "2005-01-01", "2001-01-05"],
            ["2001-01-01", "",           "",           null,         "2005-01-01", "2001-01-05", "2005-01-01", "2001-01-05"],
            ["2001-01-01", "",           "",           "2000-12-30", "1970-12-31", "1999-12-31", "1970-12-31", "1999-12-31"],
            ["2001-01-01", "",           "",           "2000-12-29", "1970-01-01", "1999-11-30", "1970-01-01", "1999-11-30"],
            ["2001-01-01", "",           "",           "2000-12-31", "2000-01-01", "2000-01-01", "2000-01-01", "2000-01-01"],
            ["2001-01-01", "",           "",           "2001-01-03", "2002-01-01", "2000-01-03", "2002-01-01", "2000-01-03"],
            ["2001-01-01", "",           "",           "2001-01-04", "2003-01-01", "2000-02-03", "2003-01-01", "2000-02-03"],
            ["2001-01-01", "",           "",           "2001-01-02", "2001-01-01", "2000-01-02", "2001-01-01", "2000-01-02"],
            ["2001-01-01", "",           "",           "2000-12-28", "1969-12-31", "1998-12-28", "1969-12-31", "1998-12-28"],
            ["2001-01-01", "",           "",           "2000-12-28", "1969-12-31", "1998-12-28", "1969-12-31", "1998-12-28"],
            ["2001-01-01", "",           "",           "2000-12-28", "1969-12-31", "1998-12-28", "1969-12-31", "1998-12-28"],
            ["2001-01-01", "",           "",           null,         "1969-12-31", "1998-12-28", "1969-12-31", "1998-12-28"],
            ["2001-01-01", "",           "",           null,         "2006-01-01", "2001-01-06", "2006-01-01", "2001-01-06"],
            ["2001-01-01", "",           "",           null,         "2006-01-01", "2001-01-06", "2006-01-01", "2001-01-06"],
            ["2001-01-01", "",           "",           "2001-01-06", "2006-01-01", "2001-01-06", "2006-01-01", "2001-01-06"],
            ["2001-01-01", "",           "",           "2001-01-06", "2006-01-01", "2001-01-06", "2006-01-01", "2001-01-06"],
            ["2001-01-01", "",           "2001-01-01", "2001-01-06", "2006-01-01", "2001-01-06", "2006-01-01", "2001-01-06"]
        ]
        ',true);

        foreach ($data as $row)
        {
            $writer->write($row);
        }
        $writer->close();
        $reader = $stream->getReader();
        $last_row = false;
        while (true)
        {
            $row = $reader->readRow();
            if ($row === false)
                break;

            $last_row = $row;
        }
        $reader->close();
        $actual = $last_row;
        $expected = json_decode('
        {
            "date_1a": "2001-01-01",
            "date_1b": null,
            "date_1c": "2001-01-01",
            "date_1d": "2001-01-06",
            "date_1e": "2006-01-01",
            "date_1f": "2001-01-06",
            "date_1g": "2006-01-01",
            "date_1h": "2001-01-06"
        }
        ',true);
        \Flexio\Tests\Check::assertArray('E.1', 'StreamReader/StreamWriter; test date type with multiple rows and combinations of values',  $actual, $expected, $results);



        // TEST: test range of rows/values on datetime type

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            {"name" : "date_2a", "type" : "datetime", "width" : 8, "scale" : 0},
            {"name" : "date_2b", "type" : "datetime", "width" : 8, "scale" : 0},
            {"name" : "date_2c", "type" : "datetime", "width" : 8, "scale" : 0},
            {"name" : "date_2d", "type" : "datetime", "width" : 8, "scale" : 0},
            {"name" : "date_2e", "type" : "datetime", "width" : 8, "scale" : 0},
            {"name" : "date_2f", "type" : "datetime", "width" : 8, "scale" : 0},
            {"name" : "date_2g", "type" : "datetime", "width" : 8, "scale" : 0},
            {"name" : "date_2h", "type" : "datetime", "width" : 8, "scale" : 0}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('
        [
            ["2001-01-01 01:01:01", "2001-01-01 01:01:01", "",                    "2001-01-05 01:01:01", "2000-01-02 00:00:00", "2000-01-01 00:01:00", "2000-01-02 00:00:00", "2000-01-01 00:01:00"],
            ["2001-01-01 01:01:01", "",                    "",                    "2001-01-05 01:01:01", "2000-01-02 00:00:00", "2000-01-01 00:01:00", "2000-01-02 00:00:00", "2000-01-01 00:01:00"],
            ["2001-01-01 01:01:01", "",                    "",                    "2001-01-05 01:01:01", "2000-01-02 00:00:00", "2000-01-01 00:01:00", "2000-01-02 00:00:00", "2000-01-01 00:01:00"],
            ["2001-01-01 01:01:01", "",                    "",                    null,                  "2000-01-02 00:00:00", "2000-01-01 00:01:00", "2000-01-02 00:00:00", "2000-01-01 00:01:00"],
            ["2001-01-01 01:01:01", "",                    "",                    null,                  "2000-01-02 00:00:00", "2000-01-01 00:01:00", "2000-01-02 00:00:00", "2000-01-01 00:01:00"],
            ["2001-01-01 01:01:01", "",                    "",                    "2000-12-30 01:01:01", "1970-01-01 00:01:00", "1970-01-01 00:00:01", "1970-01-01 00:01:00", "1970-01-01 00:00:01"],
            ["2001-01-01 01:01:01", "",                    "",                    "2000-12-29 01:01:01", "1970-01-01 00:00:00", "1970-01-01 00:00:00", "1970-01-01 00:00:00", "1970-01-01 00:00:00"],
            ["2001-01-01 01:01:01", "",                    "",                    "2000-12-31 01:01:01", "1999-12-31 23:00:00", "1999-12-31 23:59:59", "1999-12-31 23:00:00", "1999-12-31 23:59:59"],
            ["2001-01-01 01:01:01", "",                    "",                    "2001-01-03 01:01:01", "2000-01-01 01:00:00", "2000-01-01 00:00:01", "2000-01-01 01:00:00", "2000-01-01 00:00:01"],
            ["2001-01-01 01:01:01", "",                    "",                    "2001-01-04 01:01:01", "2000-01-01 23:00:00", "2000-01-01 00:00:59", "2000-01-01 23:00:00", "2000-01-01 00:00:59"],
            ["2001-01-01 01:01:01", "",                    "",                    "2001-01-02 01:01:01", "2000-01-01 00:00:00", "2000-01-01 00:00:00", "2000-01-01 00:00:00", "2000-01-01 00:00:00"],
            ["2001-01-01 01:01:01", "",                    "",                    "2000-12-28 01:01:01", "1969-12-31 23:59:00", "1969-12-31 23:59:59", "1969-12-31 23:59:00", "1969-12-31 23:59:59"],
            ["2001-01-01 01:01:01", "",                    "",                    "2000-12-28 01:01:01", "1969-12-31 23:59:00", "1969-12-31 23:59:59", "1969-12-31 23:59:00", "1969-12-31 23:59:59"],
            ["2001-01-01 01:01:01", "",                    "",                    "2000-12-28 01:01:01", "1969-12-31 23:59:00", "1969-12-31 23:59:59", "1969-12-31 23:59:00", "1969-12-31 23:59:59"],
            ["2001-01-01 01:01:01", "",                    "",                    null,                  "1969-12-31 23:59:00", "1969-12-31 23:59:59", "1969-12-31 23:59:00", "1969-12-31 23:59:59"],
            ["2001-01-01 01:01:01", "",                    "",                    null,                  "2001-01-01 00:00:00", "2000-01-01 01:00:00", "2001-01-01 00:00:00", "2000-01-01 01:00:00"],
            ["2001-01-01 01:01:01", "",                    "",                    null,                  "2001-01-01 00:00:00", "2000-01-01 01:00:00", "2001-01-01 00:00:00", "2000-01-01 01:00:00"],
            ["2001-01-01 01:01:01", "",                    "",                    "2001-01-06 01:01:01", "2001-01-01 00:00:00", "2000-01-01 01:00:00", "2001-01-01 00:00:00", "2000-01-01 01:00:00"],
            ["2001-01-01 01:01:01", "",                    "",                    "2001-01-06 01:01:01", "2001-01-01 00:00:00", "2000-01-01 01:00:00", "2001-01-01 00:00:00", "2000-01-01 01:00:00"],
            ["2001-01-01 01:01:01", "",                    "2001-01-01 01:01:01", "2001-01-06 01:01:01", "2001-01-01 00:00:00", "2000-01-01 01:00:00", "2001-01-01 00:00:00", "2000-01-01 01:00:00"]
        ]
        ',true);

        foreach ($data as $row)
        {
            $writer->write($row);
        }
        $writer->close();
        $reader = $stream->getReader();
        $last_row = false;
        while (true)
        {
            $row = $reader->readRow();
            if ($row === false)
                break;

            $last_row = $row;
        }
        $reader->close();
        $actual = $last_row;
        $expected = json_decode('
        {
            "date_2a": "2001-01-01 01:01:01",
            "date_2b": null,
            "date_2c": "2001-01-01 01:01:01",
            "date_2d": "2001-01-06 01:01:01",
            "date_2e": "2001-01-01 00:00:00",
            "date_2f": "2000-01-01 01:00:00",
            "date_2g": "2001-01-01 00:00:00",
            "date_2h": "2000-01-01 01:00:00"
        }
        ',true);
        \Flexio\Tests\Check::assertArray('F.1', 'StreamReader/StreamWriter; test datetime type with multiple rows and combinations of values',  $actual, $expected, $results);



        // TEST: test range of rows/values on boolean type

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Base\Structure::create(json_decode('
        [
            {"name" : "bool_1a", "type" : "boolean", "width" : 1, "scale" : 0},
            {"name" : "bool_1b", "type" : "boolean", "width" : 1, "scale" : 0},
            {"name" : "bool_1c", "type" : "boolean", "width" : 1, "scale" : 0},
            {"name" : "bool_1d", "type" : "boolean", "width" : 1, "scale" : 0},
            {"name" : "bool_1e", "type" : "boolean", "width" : 1, "scale" : 0},
            {"name" : "bool_1f", "type" : "boolean", "width" : 1, "scale" : 0},
            {"name" : "bool_1g", "type" : "boolean", "width" : 1, "scale" : 0},
            {"name" : "bool_1h", "type" : "boolean", "width" : 1, "scale" : 0}
        ]
        ',true))->get();
        $stream = \Flexio\Base\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $data = json_decode('
        [
            [ true, true,  false, true,  true,  false, true,  false ],
            [ true, false, false, true,  true,  false, true,  false ],
            [ true, false, false, true,  true,  false, true,  false ],
            [ true, false, false, null,  true,  false, true,  false ],
            [ true, false, false, null,  true,  false, true,  false ],
            [ true, false, false, false, false, true,  false, true  ],
            [ true, false, false, false, false, true,  false, true  ],
            [ true, false, false, false, false, true,  false, true  ],
            [ true, false, false, true,  true,  false, true,  false ],
            [ true, false, false, true,  true,  false, true,  false ],
            [ true, false, false, true,  true,  false, true,  false ],
            [ true, false, false, false, false, true,  false, true  ],
            [ true, false, false, false, false, true,  false, true  ],
            [ true, false, false, false, false, true,  false, true  ],
            [ true, false, false, null,  false, true,  false, true  ],
            [ true, false, false, null,  true,  false, true,  false ],
            [ true, false, false, null,  true,  false, true,  false ],
            [ true, false, false, true,  true,  false, true,  false ],
            [ true, false, false, true,  true,  false, true,  false ],
            [ true, false, true,  null,  true,  false, true,  false ]
        ]
        ',true);

        foreach ($data as $row)
        {
            $writer->write($row);
        }
        $writer->close();
        $reader = $stream->getReader();
        $last_row = false;
        while (true)
        {
            $row = $reader->readRow();
            if ($row === false)
                break;

            $last_row = $row;
        }
        $reader->close();
        $actual = $last_row;
        $expected = json_decode('
        {
            "bool_1a": true,
            "bool_1b": false,
            "bool_1c": true,
            "bool_1d": null,
            "bool_1e": true,
            "bool_1f": false,
            "bool_1g": true,
            "bool_1h": false
        }
        ',true);
        \Flexio\Tests\Check::assertArray('G.1', 'StreamReader/StreamWriter; test boolean type with multiple rows and combinations of values',  $actual, $expected, $results);
    }
}
