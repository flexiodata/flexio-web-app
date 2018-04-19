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
        $process_owner = \Flexio\System\System::getCurrentUserEid();
        $files = \Flexio\Tests\Util::getTestDataFiles();
        $folderpath = "/" . \Flexio\Tests\Base::STORAGE_BOX . "/" . 'job-tests-' . \Flexio\Tests\Util::getTimestampName() . "/";



        // TEST: List Job; Basic List

        // BEGIN TEST
        $filename = \Flexio\Base\Util::generateHandle() . '.txt';
        $filepath = $folderpath . '/' . $filename;
        $process_write = \Flexio\Tests\Process::write($process_owner, $filepath);
        $process_list = \Flexio\Tests\Process::list($process_owner, $folderpath);
        $actual = \Flexio\Tests\Content::getValues($process_list->getStdout(), 'name');
        $expected = [$filename];
        \Flexio\Tests\Check::assertArray("A.1", 'List; listing of folder with single file ' . $folderpath, $actual, $expected, $results);

        // BEGIN TEST
        $filename1 = \Flexio\Base\Util::generateHandle() . '.txt';
        $filename2 = \Flexio\Base\Util::generateHandle() . '.txt';
        $filepath1 = $folderpath . '/' . $filename1;
        $filepath2 = $folderpath . '/' . $filename2;
        $process_write = \Flexio\Tests\Process::write($process_owner, $filepath1);
        $process_write = \Flexio\Tests\Process::write($process_owner, $filepath2);
        $process_list = \Flexio\Tests\Process::list($process_owner, $filepath1);
        $actual = \Flexio\Tests\Content::getValues($process_list->getStdout(), 'name');
        $expected = [$filename1];
        \Flexio\Tests\Check::assertArray("A.2", 'List; listing of folder with single file ' . $folderpath, $actual, $expected, $results);

        // BEGIN TEST
        $folder = $folderpath . 'subfolder';
        $filenames = ["file1.txt","file2.csv","file3.png","file4.jpg","file5.csv"];
        foreach ($filenames as $f)
        {
            $filepath = $folder . '/' . $f;
            $process_write = \Flexio\Tests\Process::write($process_owner, $filepath);
        }
        $tests = [
            ["pattern" => "$folder/*.txt",       "expected" => ["file1.txt"]],
            ["pattern" => "$folder/*.csv",       "expected" => ["file2.csv","file5.csv"]],
            ["pattern" => "$folder/*.png",       "expected" => ["file3.png"]],
            ["pattern" => "$folder/*.jpg",       "expected" => ["file4.jpg"]],
            ["pattern" => "$folder/*.xls",       "expected" => []],
            ["pattern" => "$folder/file1.*",     "expected" => ["file1.txt"]],
            ["pattern" => "$folder/file2.*",     "expected" => ["file2.csv"]],
            ["pattern" => "$folder/file3.*",     "expected" => ["file3.png"]],
            ["pattern" => "$folder/file4.*",     "expected" => ["file4.jpg"]],
            ["pattern" => "$folder/file5.*",     "expected" => ["file5.csv"]],
            ["pattern" => "$folder/file*.CSV",   "expected" => ["file2.csv", "file5.csv"]],
            ["pattern" => "$folder/file1.*",     "expected" => ["file1.txt"]],
            ["pattern" => "$folder/file[2-3].*", "expected" => ["file2.csv", "file3.png"]],
            ["pattern" => "$folder/file[0-9].*", "expected" => ["file1.txt","file2.csv","file3.png","file4.jpg","file5.csv"]]
        ];
        $idx = 2;
        foreach ($tests as $t)
        {
            $idx++;
            $process_list = \Flexio\Tests\Process::list($process_owner, $t['pattern']);
            $actual = \Flexio\Tests\Content::getValues($process_list->getStdout(), 'name');
            $expected = $t['expected'];
            \Flexio\Tests\Check::assertArray("A.$idx", 'List; listing of folder with wildcard' . $folder, $actual, $expected, $results);
        }



        // TEST: Mkdir Job; Basic Directory Creation

        // BEGIN TEST
        $foldername = 'empty_folder1';
        $process_create = \Flexio\Tests\Process::mkdir($process_owner, $folderpath . '/' . $foldername . '/'); // folder path with path terminator
        $process_list = \Flexio\Tests\Process::list($process_owner, $folderpath);
        $actual = \Flexio\Tests\Content::getValues($process_list->getStdout(), 'name');
        $expected = [$foldername];
        \Flexio\Tests\Check::assertInArray("B.1", 'Mkdir; create an empty folder; folder should be ' . $foldername, $actual, $expected, $results);

        // BEGIN TEST
        $foldername = 'empty_folder2';
        $process_create = \Flexio\Tests\Process::mkdir($process_owner, $folderpath . '/' . $foldername); // folder path without path terminator
        $process_list = \Flexio\Tests\Process::list($process_owner, $folderpath);
        $actual = \Flexio\Tests\Content::getValues($process_list->getStdout(), 'name');
        $expected = [$foldername];
        \Flexio\Tests\Check::assertInArray("B.2", 'Mkdir; create an empty folder; folder should be ' . $foldername, $actual, $expected, $results);

        // BEGIN TEST
        $foldername = 'empty_folder3';
        $process_create = \Flexio\Tests\Process::mkdir($process_owner, $folderpath . '/' . $foldername);
        $has_error_after_first_attempt = $process_create->hasError();
        $process_create = \Flexio\Tests\Process::mkdir($process_owner, $folderpath . '/' . $foldername);
        $has_error_after_second_attempt = $process_create->hasError();
        $actual = ($has_error_after_first_attempt === false && $has_error_after_second_attempt === true);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean("B.3", 'Mkdir; throw an exception when attempting to create a folder that already exists', $actual, $expected, $results);



        // TEST: Create Job; Basic Create

        // BEGIN TEST
        $filename = \Flexio\Base\Util::generateHandle() . '.txt';
        $process_create = \Flexio\Tests\Process::create($process_owner, $folderpath . '/' . $filename);
        $process_list = \Flexio\Tests\Process::list($process_owner, $folderpath);
        $actual = \Flexio\Tests\Content::getValues($process_list->getStdout(), 'name');
        $expected = [$filename];
        \Flexio\Tests\Check::assertInArray("C.1", 'Create; create a file with no content in a folder; file should be ' . $filename, $actual, $expected, $results);

        // BEGIN TEST
        $name = 'test_folder';
        $process_create = \Flexio\Tests\Process::create($process_owner, $folderpath . '/' . $name . '/file.txt');
        $has_error_after_first_attempt = $process_create->hasError();
        $process_create = \Flexio\Tests\Process::create($process_owner, $folderpath . '/' . $name);
        $has_error_after_second_attempt = $process_create->hasError();
        $actual = ($has_error_after_first_attempt === false && $has_error_after_second_attempt === true);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean("C.2", 'Create; throw an exception when attempting to create a file with the same name as a folder', $actual, $expected, $results);



        // TEST: Write/Read Job; Basic Copy

        // BEGIN TEST
        $filename = 'file_that_does_not_exist.txt';
        $process_read = \Flexio\Tests\Process::read($process_owner, $folderpath . '/' . $filename);
        $actual = $process_read->hasError();
        $expected = true;
        \Flexio\Tests\Check::assertBoolean("D.1", 'Read; throw an exception when attempting to read from a file that doesn\'t exist.', $actual, $expected, $results);

        // BEGIN TEST
        $idx = 1;
        foreach ($files as $filename)
        {
            $idx++;

            $filepath = \Flexio\Tests\Util::getOutputFilePath($folderpath, $filename);
            $stream = \Flexio\Tests\Util::createStreamFromFile($filename);
            $process_write = \Flexio\Tests\Process::write($process_owner, $filepath, $stream);
            $process_read = \Flexio\Tests\Process::read($process_owner, $filepath);
            $actual_contents = \Flexio\Base\Util::getStreamContents($process_read->getStdout());
            $expected_contents = \Flexio\Base\Util::getStreamContents($stream);
            $actual = md5($actual_contents);
            $expected = md5($expected_contents);
            \Flexio\Tests\Check::assertString("D.$idx", 'Read/Write; check write/read to/from ' . $filepath, $actual, $expected, $results);
        }



        // TEST: Write/Read Job; Overwrite

        // BEGIN TEST
        $filename = \Flexio\Base\Util::generateHandle() . '.txt';
        $filepath = $folderpath . '/' . $filename;
        $contents = ["", "abc", "cba", "", "abcd"];

        $idx = 0;
        foreach ($contents as $c)
        {
            $idx++;
            $stream = \Flexio\Base\Stream::create();
            $stream->getWriter()->write($c);
            $process_write = \Flexio\Tests\Process::write($process_owner, $filepath, $stream);
            $process_read = \Flexio\Tests\Process::read($process_owner, $filepath);
            $actual = \Flexio\Base\Util::getStreamContents($process_read->getStdout());
            $expected = $c;
            \Flexio\Tests\Check::assertString("E.$idx", 'Read/Write; overwrite check; write/read to/from ' . $filepath, $actual, $expected, $results);
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
        $filepath = $folderpath . '/' . $filename;
        $read = json_decode('{"op": "read", "params": {"path": "'. $filepath . '"}}',true);
        $write = json_decode('{"op": "write", "params": { "path": "'. $filepath . '"}}',true);
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
        \Flexio\Tests\Check::assertString("F.1", 'Read/Write; check write/read with implicit type conversion; file output here: ' . $filepath, $actual, $expected, $results);



        // TEST: Delete Job; Basic Delete

        // TODO: try to delete a file that doesn't exist

        // BEGIN TEST
        $filename = \Flexio\Base\Util::generateHandle() . '.txt';
        $filepath = $folderpath . '/' . $filename;
        $process_create = \Flexio\Tests\Process::create($process_owner, $filepath);
        $process_list1 = \Flexio\Tests\Process::list($process_owner, $folderpath);
        $process_delete = \Flexio\Tests\Process::delete($process_owner, $filepath);
        $process_list2 = \Flexio\Tests\Process::list($process_owner, $folderpath);
        $list1 = \Flexio\Tests\Content::getValues($process_list1->getStdout(),'name');
        $list2 = \Flexio\Tests\Content::getValues($process_list2->getStdout(),'name');
        $actual = array_values(array_diff($list1, $list2));
        $expected = [$filename];
        \Flexio\Tests\Check::assertArray("G.1", 'Delete; delete a file that exists; file is ' . $filepath, $actual, $expected, $results);

        // TODO: delete an empty folder
        // TODO: delete a populated folder
    }
}
