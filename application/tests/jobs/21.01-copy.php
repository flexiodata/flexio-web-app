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
        $files = \Flexio\Tests\Util::getTestDataFiles();
        $test_folder = 'tests' . \Flexio\Tests\Util::getTimestampName();
        $source_directory = '/home/' . $test_folder . '/';
        $target_directory = "/" . \Flexio\Tests\Base::STORAGE_DROPBOX . "/" . $test_folder . '/';


        foreach ($files as $filename) // copy files into home directory
        {
            $source_filepath = \Flexio\Tests\Util::getOutputFilePath($source_directory, $filename);
            $read = \Flexio\Tests\Task::create([["op" => "read", "path" => $source_filepath]]);
            $write = \Flexio\Tests\Task::create([["op" => "write", "path" => $source_filepath]]);
            $stream = \Flexio\Tests\Util::createStreamFromFile($filename);
            $process_write = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($write);
            $process_read = \Flexio\Jobs\Process::create()->execute($read);
            $actual_contents = \Flexio\Base\Util::getStreamContents($process_read->getStdout());
            $expected_contents = \Flexio\Base\Util::getStreamContents($stream);
            $actual = md5($actual_contents);
            $expected = md5($expected_contents);
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
        \Flexio\Tests\Check::assertArray("A.1", "Copy; compare contents of copied folder $target_directory with source folder $source_directory", $actual, $expected, $results);
    }
}
