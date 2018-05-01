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
        // TODO: writing with bad info (e.g. malformed paths, no file extension, bad file characters)

        // SETUP
        $process_owner = \Flexio\Tests\Base::getTestStorageOwner();
        $files = \Flexio\Tests\Util::getTestDataFiles();
        $folderpath = "/" . \Flexio\Tests\Base::STORAGE_LOCAL . "/" . 'job-tests-' . \Flexio\Tests\Util::getTimestampName() . "/";



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
        // TODO: do we need an implicit format conversion for internal storage?



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
