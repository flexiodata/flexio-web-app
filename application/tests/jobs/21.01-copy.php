<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-02-12
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
        $files = \Flexio\Tests\Util::getTestDataSamples();
        $test_folder = 'tests' . \Flexio\Tests\Util::getTimestampName();
        $source_directory = '/home/' . $test_folder . '/';
        $target_directory = "/" . \Flexio\Tests\Base::STORAGE_DROPBOX . "/" . $test_folder . '/';

        $process_owner = \Flexio\Tests\Util::getTestStorageOwner();

        $idx = 1;
        foreach ($files as $filename) // copy files into home directory
        {
            $stream = \Flexio\Tests\Util::createStreamFromFile($filename);

            $source_filepath = \Flexio\Tests\Util::getOutputFilePath($source_directory, $filename);
            $write = \Flexio\Tests\Task::create([["op" => "write", "path" => $source_filepath]]);   
            $process_write = \Flexio\Jobs\Process::create()->setOwner($process_owner)->setStdin($stream)->execute($write);

            $read = \Flexio\Tests\Task::create([["op" => "read", "path" => $source_filepath]]);
            $process_read = \Flexio\Jobs\Process::create()->setOwner($process_owner)->execute($read);
            $actual_contents = \Flexio\Base\Util::getStreamContents($process_read->getStdout());

            $expected_contents = \Flexio\Base\Util::getStreamContents($stream);
            $actual = md5($actual_contents);
            $expected = md5($expected_contents);

            \Flexio\Tests\Check::assertString("A.$idx", 'Read/Write; check write/read to/from ' . $source_filepath, $actual, $expected, $results);
            $idx++;
        }


        // TEST: Copy Job; Basic Copy
        $copy = \Flexio\Tests\Task::create([["op" => "copy", "from" => $source_directory, "to" => $target_directory]]);
        $list_source = \Flexio\Tests\Task::create([["op" => "list", "path" => $source_directory]]);
        $list_target = \Flexio\Tests\Task::create([["op" => "list", "path" => $target_directory]]);
        $process_copy = \Flexio\Jobs\Process::create()->execute($copy);
        $process_list_source = \Flexio\Jobs\Process::create()->execute($list_source);
        $process_list_target = \Flexio\Jobs\Process::create()->execute($list_target);
        $actual_contents = \Flexio\Base\Util::getStreamContents($process_list_target->getStdout());
        $expected_contents = \Flexio\Base\Util::getStreamContents($process_list_source->getStdout());


        \Flexio\Tests\Check::assertString("A.1", "Copy; compare contents of copied folder $target_directory with source folder $source_directory", $actual, $expected, $results);
    }
}
