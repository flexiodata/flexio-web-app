<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-01-29
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
        $files = TestUtil::getTestDataFiles();
        $store_alias = "testsuite-googledrive";
        $output_folder = "/" . $store_alias . "/" . 'tests' . TestUtil::getTimestampName() . "/";



        // TEST: List Job; Basic List

        // BEGIN TEST
        $filename = \Flexio\Base\Util::generateHandle() . '.txt';
        $output_filepath = TestUtil::getOutputFilePath($output_folder, $filename);
        $write = json_decode('{"op": "write", "params": { "path": "'. $output_filepath . '"}}',true);
        $list = json_decode('{"op": "list", "params": {"path": "'. $output_folder . '"}}',true);
        $process_write = \Flexio\Jobs\Process::create()->execute($write);
        $process_list = \Flexio\Jobs\Process::create()->execute($list);
        $actual = \Flexio\Base\Util::getStreamContents($process_list->getStdout());
        $expected = array(array("name" => $filename, "type" => "FILE"));
        TestCheck::assertInArray("A.1", 'List; listing of folder with single file ' . $output_folder, $actual, $expected, $results);



        // TEST: Mkdir Job; Basic Directory Creation

        // BEGIN TEST
        $foldername = 'empty_folder1';
        $create = json_decode('{"op": "mkdir", "params": { "path": "'. $output_folder . '/' . $foldername . '/"}}',true); // folder path with path terminator
        $list = json_decode('{"op": "list", "params": {"path": "'. $output_folder . '"}}',true);
        $process_create = \Flexio\Jobs\Process::create()->execute($create);
        $process_list = \Flexio\Jobs\Process::create()->execute($list);
        $actual = \Flexio\Base\Util::getStreamContents($process_list->getStdout());
        $expected = array(array("name" => $foldername, "type" => "DIR"));
        TestCheck::assertInArray("B.1", 'Mkdir; create an empty folder; folder should be ' . $foldername, $actual, $expected, $results);

        // BEGIN TEST
        $foldername = 'empty_folder2';
        $create = json_decode('{"op": "mkdir", "params": { "path": "'. $output_folder . '/' . $foldername . '"}}',true); // folder path without path terminator
        $list = json_decode('{"op": "list", "params": {"path": "'. $output_folder . '"}}',true);
        $process_create = \Flexio\Jobs\Process::create()->execute($create);
        $process_list = \Flexio\Jobs\Process::create()->execute($list);
        $actual = \Flexio\Base\Util::getStreamContents($process_list->getStdout());
        $expected = array(array("name" => $foldername, "type" => "DIR"));
        TestCheck::assertInArray("B.2", 'Mkdir; create an empty folder; folder should be ' . $foldername, $actual, $expected, $results);



        // TEST: Create Job; Basic Create

        // BEGIN TEST
        $filename = \Flexio\Base\Util::generateHandle() . '.txt';
        $output_filepath = TestUtil::getOutputFilePath($output_folder, $filename);
        $create = json_decode('{"op": "create", "params": { "path": "'. $output_filepath . '"}}',true);
        $list = json_decode('{"op": "list", "params": {"path": "'. $output_folder . '"}}',true);
        $process_create = \Flexio\Jobs\Process::create()->execute($create);
        $process_list = \Flexio\Jobs\Process::create()->execute($list);
        $actual = \Flexio\Base\Util::getStreamContents($process_list->getStdout());
        $expected = array(array("name" => $filename, "type" => "FILE"));
        TestCheck::assertInArray("C.1", 'Create; create a file with no content in a folder; file should be ' . $output_filepath, $actual, $expected, $results);



        // TEST: Write/Read Job; Basic Copy

        // BEGIN TEST
        $idx = 0;
        foreach ($files as $filename)
        {
            $idx++;

            $output_filepath = TestUtil::getOutputFilePath($output_folder, $filename);
            $read = json_decode('{"op": "read", "params": {"path": "'. $output_filepath . '"}}',true);
            $write = json_decode('{"op": "write", "params": { "path": "'. $output_filepath . '"}}',true);

            $stream = TestUtil::createStreamFromFile($filename);
            $process_write = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($write);
            $process_read = \Flexio\Jobs\Process::create()->execute($read);
            $actual_contents = \Flexio\Base\Util::getStreamContents($process_read->getStdout());
            $expected_contents = \Flexio\Base\Util::getStreamContents($stream);
            $actual = md5($actual_contents);
            $expected = md5($expected_contents);
            TestCheck::assertString("D.$idx", 'Read/Write; check write/read to/from ' . $output_filepath, $actual, $expected, $results);
        }



        // TEST: Write/Read Job; Overwrite

        // BEGIN TEST
        $filename = \Flexio\Base\Util::generateHandle() . '.txt';
        $output_filepath = TestUtil::getOutputFilePath($output_folder, $filename);
        $read = json_decode('{"op": "read", "params": {"path": "'. $output_filepath . '"}}',true);
        $write = json_decode('{"op": "write", "params": { "path": "'. $output_filepath . '"}}',true);
        $contents = ["", "abc", "cba", "", "abcd"];

        $idx = 0;
        foreach ($contents as $c)
        {
            $idx++;
            $stream = \Flexio\Base\Stream::create();
            $stream->getWriter()->write($c);
            $process_write = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($write); // first write
            $process_read = \Flexio\Jobs\Process::create()->execute($read);
            $actual = \Flexio\Base\Util::getStreamContents($process_read->getStdout());
            $expected = $c;
            TestCheck::assertString("E.$idx", 'Read/Write; overwrite check; write/read to/from ' . $output_filepath, $actual, $expected, $results);
        }



        // TEST: Write/Read Job; Implicit Format Conversion
        $create = json_decode('
        {
            "op": "create",
            "params": {
                "name": "table",
                "content_type": "'.\Flexio\Base\ContentType::FLEXIO_TABLE.'",
                "columns": [
                    { "name": "c1", "type": "character", "width": 3 },
                    { "name": "c2", "type": "character", "width": 20 },
                    { "name": "n1", "type": "numeric", "width": 6, "scale": 2 },
                    { "name": "n2", "type": "double", "width": 10, "scale": 4 },
                    { "name": "n3", "type": "integer" },
                    { "name": "d1", "type": "date" },
                    { "name": "d2", "type": "datetime" },
                    { "name": "b1", "type": "boolean" }
                ],
                "content": [
                    { "c1": "aBC", "c2": "()[]{}<>",       "n1": -1.02, "n2": -1.23, "n3": -1,   "d1": "1776-07-04", "d2": "1776-07-04 01:02:03", "b1": true  },
                    { "c1": "c a", "c2": "| \\/",          "n1": null,  "n2": 0.00,  "n3": 0,    "d1": "1970-11-22", "d2": "1970-11-22 01:02:03", "b1": null  },
                    { "c1": " -1", "c2": ":;\"\'",         "n1": 0.00,  "n2": 0.99,  "n3": 1,    "d1": "1999-12-31", "d2": "1999-12-31 01:02:03", "b1": false },
                    { "c1": "0% ", "c2": ",.?",            "n1": 0.99,  "n2": 4.56,  "n3": 2,    "d1": "2000-01-01", "d2": "2000-01-01 01:02:03", "b1": null  },
                    { "c1": null,  "c2": "~`!@#$%^&*-+_=", "n1": 2.00,  "n2": null,  "n3": null, "d1": null,         "d2": null,                  "b1": true  }
                ]
            }
        }
        ',true);
        $filename = \Flexio\Base\Util::generateHandle() . '.csv';
        $output_filepath = TestUtil::getOutputFilePath($output_folder, $filename);
        $read = json_decode('{"op": "read", "params": {"path": "'. $output_filepath . '"}}',true);
        $write = json_decode('{"op": "write", "params": { "path": "'. $output_filepath . '"}}',true);
        $process_write = \Flexio\Jobs\Process::create()->execute($create)->execute($write);
        $process_read = \Flexio\Jobs\Process::create()->execute($read);
        $actual_contents = \Flexio\Base\Util::getStreamContents($process_read->getStdout());
        $expected_contents = <<<EOD
c1,c2,n1,n2,n3,d1,d2,b1
aBC,()[]{}<>,-1.02,-1.23,-1,1776-07-04,"1776-07-04 01:02:03",true
"c a","| /",,0,0,1970-11-22,"1970-11-22 01:02:03",
" -1",":;""'",0,0.99,1,1999-12-31,"1999-12-31 01:02:03",false
"0% ",",.?",0.99,4.56,2,2000-01-01,"2000-01-01 01:02:03",
,~`!@#$%^&*-+_=,2,,,,,true

EOD;
        $actual = $actual_contents;
        $expected = $expected_contents;
        TestCheck::assertString("F.1", 'Read/Write; check write/read with implicit type conversion; file output here: ' . $output_filepath, $actual, $expected, $results);



        // TEST: Delete Job; Basic Delete

        // TODO: try to delete a file that doesn't exist

        // BEGIN TEST
        $filename = \Flexio\Base\Util::generateHandle() . '.txt';
        $output_filepath = TestUtil::getOutputFilePath($output_folder, $filename);
        $create = json_decode('{"op": "create", "params": { "path": "'. $output_filepath . '"}}',true);
        $delete = json_decode('{"op": "delete", "params": { "path": "'. $output_filepath . '"}}',true);
        $list = json_decode('{"op": "list", "params": {"path": "'. $output_folder . '"}}',true);
        $process_create = \Flexio\Jobs\Process::create()->execute($create);
        $process_list1 = \Flexio\Jobs\Process::create()->execute($list);
        $process_delete = \Flexio\Jobs\Process::create()->execute($delete);
        $process_list2 = \Flexio\Jobs\Process::create()->execute($list);
        $list1 = json_decode(\Flexio\Base\Util::getStreamContents($process_list1->getStdout()),true);
        $list2 = json_decode(\Flexio\Base\Util::getStreamContents($process_list2->getStdout()),true);
        $actual = TestUtil::fileExistsInList($filename, $list1) === true && TestUtil::fileExistsInList($filename, $list2) === false;
        $expected = true;
        TestCheck::assertBoolean("G.1", 'Delete; delete a file that exists; file is ' . $output_filepath, $actual, $expected, $results);

        // TODO: delete an empty folder
        // TODO: delete a populated folder
    }
}
