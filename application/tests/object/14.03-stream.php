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
        TestCheck::assertArray('A.1', 'StreamReader/StreamWriter; test character types with multiple rows and combinations of values',  $actual, $expected, $results);
    }
}
