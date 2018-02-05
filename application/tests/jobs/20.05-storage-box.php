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
        $store_alias = "testsuite-box";
        $output_folder = "/" . $store_alias . "/" . 'tests' . TestUtil::getTimestampName() . "/";



        // TEST: List Job; Basic List

        // BEGIN TEST
        $filename = \Flexio\Base\Util::generateHandle() . '.txt';
        $output_filepath = self::getOutputPath($output_folder, $filename);
        $write = json_decode('{"op": "write", "params": { "path": "'. $output_filepath . '"}}',true);
        $list = json_decode('{"op": "list", "params": {"path": "'. $output_folder . '"}}',true);
        $process_write = \Flexio\Jobs\Process::create()->execute($write);
        $process_list = \Flexio\Jobs\Process::create()->execute($list);
        $actual = \Flexio\Base\Util::getStreamContents($process_list->getStdout());
        $expected = array(array("name" => $filename, "type" => "FILE"));
        TestCheck::assertInArray("A.1", 'List; listing of folder with single file ' . $output_folder, $actual, $expected, $results);



        // TEST: Create Job; Basic Create

        // BEGIN TEST
        $filename = \Flexio\Base\Util::generateHandle() . '.txt';
        $output_filepath = self::getOutputPath($output_folder, $filename);
        $create = json_decode('{"op": "create", "params": { "path": "'. $output_filepath . '"}}',true);
        $list = json_decode('{"op": "list", "params": {"path": "'. $output_folder . '"}}',true);
        $process_create = \Flexio\Jobs\Process::create()->execute($create);
        $process_list = \Flexio\Jobs\Process::create()->execute($list);
        $actual = \Flexio\Base\Util::getStreamContents($process_list->getStdout());
        $expected = array(array("name" => $filename, "type" => "FILE"));
        TestCheck::assertInArray("B.1", 'Create; create a file with no content in a folder; file should be ' . $output_filepath, $actual, $expected, $results);

        // BEGIN TEST
        $foldername = 'empty_folder1';
        $create = json_decode('{"op": "create", "params": { "path": "'. $output_folder . '/' . $foldername . '/"}}',true); // folder path with path terminator
        $list = json_decode('{"op": "list", "params": {"path": "'. $output_folder . '"}}',true);
        $process_create = \Flexio\Jobs\Process::create()->execute($create);
        $process_list = \Flexio\Jobs\Process::create()->execute($list);
        $actual = \Flexio\Base\Util::getStreamContents($process_list->getStdout());
        $expected = array(array("name" => $foldername, "type" => "DIR"));
        TestCheck::assertInArray("B.2", 'Create; create an empty folder; folder should be ' . $foldername, $actual, $expected, $results);

        // BEGIN TEST
        $foldername = 'empty_folder2';
        $create = json_decode('{"op": "create", "params": { "path": "'. $output_folder . '/' . $foldername . '"}}',true); // folder path without path terminator
        $list = json_decode('{"op": "list", "params": {"path": "'. $output_folder . '"}}',true);
        $process_create = \Flexio\Jobs\Process::create()->execute($create);
        $process_list = \Flexio\Jobs\Process::create()->execute($list);
        $actual = \Flexio\Base\Util::getStreamContents($process_list->getStdout());
        $expected = array(array("name" => $foldername, "type" => "DIR"));
        TestCheck::assertInArray("B.3", 'Create; create an empty folder; folder should be ' . $foldername, $actual, $expected, $results);



        // TEST: Write/Read Job; Basic Copy

        // BEGIN TEST
        $idx = 0;
        foreach ($files as $filename)
        {
            $idx++;

            $output_filepath = self::getOutputPath($output_folder, $filename);
            $read = json_decode('{"op": "read", "params": {"path": "'. $output_filepath . '"}}',true);
            $write = json_decode('{"op": "write", "params": { "path": "'. $output_filepath . '"}}',true);

            $stream = TestUtil::createStreamFromFile($filename);
            $process_write = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($write);
            $process_read = \Flexio\Jobs\Process::create()->execute($read);
            $actual_contents = \Flexio\Base\Util::getStreamContents($process_read->getStdout());
            $expected_contents = \Flexio\Base\Util::getStreamContents($stream);
            $actual = md5($actual_contents);
            $expected = md5($expected_contents);
            TestCheck::assertString("C.$idx", 'Read/Write; check write/read to/from ' . $output_filepath, $actual, $expected, $results);
        }



        // TEST: Write/Read Job; Overwrite

        // BEGIN TEST
        $filename = \Flexio\Base\Util::generateHandle() . '.txt';
        $output_filepath = self::getOutputPath($output_folder, $filename);
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
            TestCheck::assertString("D.$idx", 'Read/Write; overwrite check; write/read to/from ' . $output_filepath, $actual, $expected, $results);
        }



        // TEST: Delete Job; Basic Delete

        // TODO: try to delete a file that doesn't exist

        // BEGIN TEST
        $filename = \Flexio\Base\Util::generateHandle() . '.txt';
        $output_filepath = self::getOutputPath($output_folder, $filename);
        $create = json_decode('{"op": "create", "params": { "path": "'. $output_filepath . '"}}',true);
        $delete = json_decode('{"op": "delete", "params": { "path": "'. $output_filepath . '"}}',true);
        $list = json_decode('{"op": "list", "params": {"path": "'. $output_folder . '"}}',true);
        $process_create = \Flexio\Jobs\Process::create()->execute($create);
        $process_list1 = \Flexio\Jobs\Process::create()->execute($list);
        $process_delete = \Flexio\Jobs\Process::create()->execute($delete);
        $process_list2 = \Flexio\Jobs\Process::create()->execute($list);
        $list1 = json_decode(\Flexio\Base\Util::getStreamContents($process_list1->getStdout()),true);
        $list2 = json_decode(\Flexio\Base\Util::getStreamContents($process_list2->getStdout()),true);
        $actual = self::fileExistsInList($filename, $list1) === true && self::fileExistsInList($filename, $list1) === false;
        $expected = true;
        TestCheck::assertBoolean("E.1", 'Delete; delete a file that exists; file is ' . $output_filepath, $actual, $expected, $results);

        // TODO: delete an empty folder
        // TODO: delete a populated folder
    }

    public function getOutputPath($output_folder, $input_filename)
    {
        $filename = \Flexio\Base\File::getFilename($input_filename);
        $fileextension = \Flexio\Base\File::getFileExtension($input_filename);
        $output_filepath = $output_folder . $filename . "." . $fileextension;
        return $output_filepath;
    }

    public function fileExistsInList(string $name, array $list) : bool
    {
        foreach ($list as $l)
        {
            if (!isset($l['name']))
                continue;

            if ($l['name'] === $name)
                return true;
        }

        return false;
    }
}
