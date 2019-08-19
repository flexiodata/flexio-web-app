<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
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
        $process_owner = \Flexio\Tests\Util::getTestStorageOwner();
        $files = \Flexio\Tests\Util::getTestDataSamples();
        $folderpath = \Flexio\Tests\Base::STORAGE_FLEX . ":/job-tests-" . \Flexio\Tests\Util::getTimestampName() . "/";


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
            $actual_contents = \Flexio\Base\StreamUtil::getStreamContents($process_read->getStdout());
            $expected_contents = \Flexio\Base\StreamUtil::getStreamContents($stream);
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
            $actual = \Flexio\Base\StreamUtil::getStreamContents($process_read->getStdout());
            $expected = $c;
            \Flexio\Tests\Check::assertString("E.$idx", 'Read/Write; overwrite check; write/read to/from ' . $filepath, $actual, $expected, $results);
        }
    }
}
