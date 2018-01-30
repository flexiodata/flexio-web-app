<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-06-07
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
        $store_alias = "testsuite-dropbox";
        $output_folder = "/" . $store_alias . "/" . 'tests' . TestUtil::getTimestampName() . "/";



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
            TestCheck::assertString("A.$idx", 'Read/Write; check write/read to/from ' . $output_filepath, $actual, $expected, $results);
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
            TestCheck::assertString("B.$idx", 'Read/Write; overwrite check; write/read to/from ' . $output_filepath, $actual, $expected, $results);
        }
    }

    public function getOutputPath($output_folder, $input_filename)
    {
        $filename = \Flexio\Base\File::getFilename($input_filename);
        $fileextension = \Flexio\Base\File::getFileExtension($input_filename);
        $output_filepath = $output_folder . $filename . "." . $fileextension;
        return $output_filepath;
    }
}
