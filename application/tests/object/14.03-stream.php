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



        // TEST: test range of rows/values on character type

        // BEGIN TEST
        $stream_info = array();
        $stream_info['connection_eid'] = \Flexio\Object\Connection::getDatastoreConnectionEid();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Object\Structure::create(json_decode('
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
        $writer = \Flexio\Object\StreamWriter::create($stream_info);
        $data = json_decode('
        [
            [ "1",  "A", "A", "",  "D",  "D00000", "00000D", "D00000", "00000D"],
            [ "2",  "A", "",  "",  "D",  "D00000", "00000D", "D00000", "00000D"],
            [ "3",  "A", "",  "",  "D",  "D00000", "00000D", "D00000", "00000D"],
            [ "4",  "A", "",  "",  null, "D00000", "00000D", "D00000", "00000D"],
            [ "5",  "A", "",  "",  null, "D00000", "00000D", "D00000", "00000D"],
            [ "6",  "A", "",  "",  "b",  "b00000", "00000b", "b00000", "00000b"],
            [ "7",  "A", "",  "",  "c",  "c00000", "00000c", "c00000", "00000c"],
            [ "8",  "A", "",  "",  "a",  "a00000", "00000a", "a00000", "00000a"],
            [ "9",  "A", "",  "",  "B",  "B00000", "00000B", "B00000", "00000B"],
            [ "10", "A", "",  "",  "C",  "C00000", "00000C", "C00000", "00000C"],
            [ "11", "A", "",  "",  "A",  "A00000", "00000A", "A00000", "00000A"],
            [ "12", "A", "",  "",  "d",  "d00000", "00000d", "d00000", "00000d"],
            [ "13", "A", "",  "",  "d",  "d00000", "00000d", "d00000", "00000d"],
            [ "14", "A", "",  "",  "d",  "d00000", "00000d", "d00000", "00000d"],
            [ "15", "A", "",  "",  null, "d00000", "00000d", "d00000", "00000d"],
            [ "16", "A", "",  "",  null, "E00000", "00000E", "E00000", "00000E"],
            [ "17", "A", "",  "",  null, "E00000", "00000E", "E00000", "00000E"],
            [ "18", "A", "",  "",  "E",  "E00000", "00000E", "E00000", "00000E"],
            [ "19", "A", "",  "",  "E",  "E00000", "00000E", "E00000", "00000E"],
            [ "20", "A", "",  "A", "E",  "E00000", "00000E", "E00000", "00000E"]
        ]
        ',true);
        foreach ($data as $row)
        {
            $writer->write($row);
        }
        $writer->close();
        $reader = \Flexio\Object\StreamReader::create($stream_info);
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
        TestCheck::assertArray('A.1', 'StreamReader/StreamWriter; test character type with multiple rows and combinations of values',  $actual, $expected, $results);



        // TEST: test range of rows/values on numeric type

        // BEGIN TEST
        $stream_info = array();
        $stream_info['connection_eid'] = \Flexio\Object\Connection::getDatastoreConnectionEid();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Object\Structure::create(json_decode('
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
        $writer = \Flexio\Object\StreamWriter::create($stream_info);
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
        $reader = \Flexio\Object\StreamReader::create($stream_info);
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
            "num_1a": "1",
            "num_1b": "0",
            "num_1c": "1",
            "num_1d": "5",
            "num_1e": "5000000000000",
            "num_1f": "0.000000000005",
            "num_1g": "5000000000000",
            "num_1h": "0.000000000005"
        }
        ',true);
        TestCheck::assertArray('B.1', 'StreamReader/StreamWriter; test numeric type with multiple rows and combinations of values',  $actual, $expected, $results);



        // TEST: test range of rows/values on numeric type

        // BEGIN TEST
        $stream_info = array();
        $stream_info['connection_eid'] = \Flexio\Object\Connection::getDatastoreConnectionEid();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_info['structure'] = \Flexio\Object\Structure::create(json_decode('
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
        $writer = \Flexio\Object\StreamWriter::create($stream_info);
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
        $reader = \Flexio\Object\StreamReader::create($stream_info);
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
            "num_2a": "1",
            "num_2b": "0",
            "num_2c": "1",
            "num_2d": "5",
            "num_2e": "5000000000000",
            "num_2f": "5e-12",
            "num_2g": "5000000000000",
            "num_2h": "5e-12"
        }
        ',true);
        TestCheck::assertArray('C.1', 'StreamReader/StreamWriter; test double type with multiple rows and combinations of values',  $actual, $expected, $results);
    }
}
