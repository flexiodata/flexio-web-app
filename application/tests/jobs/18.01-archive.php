<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-06-23
 *
 * @package flexio
 * @subpackage Tests
 */


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function createArchiveTask($format, $path = null, $files = null)
    {
        $task_arr = [
            "op" => "archive",
            "format" => $format
        ];

        if (isset($path))
            $task_arr["path"] = $path;
        if (isset($files))
            $task_arr["files"] = $files;

        $task = \Flexio\Tests\Task::create([$task_arr]);
        return $task;
    }

    public function createUnArchiveTask($format, $path = null, $files = null, $target = null)
    {
        $task_arr = [
            "op" => "unarchive",
            "format" => $format
        ];

        if (isset($path))
            $task_arr["path"] = $path;
        if (isset($files))
            $task_arr["files"] = $files;
        if (isset($files))
            $task_arr["target"] = $target;

        $task = \Flexio\Tests\Task::create([$task_arr]);
        return $task;
    }

    public function run(&$results)
    {
/*
    { "op": "archive", "format": "zip", "path": "/vfs/output.zip", "files": [ "/vfs/file1.txt", "/vfs/file2.txt" ] }
    { "op": "unarchive", "format": "zip", "path": "/vfs/input.zip", "files": [ "file_in_zip1.txt", "file_in_zip2.txt" ], "target": "/vfs/output_path" }
    { "op": "archive", "format": "gzip"}
    { "op": "unarchive", "format": "gzip"}
*/

        // TEST: Archive/Unarchive; basic file

        // BEGIN TEST
        $archivetask = self::createArchiveTask('gzip');
        $unarchivetask = self::createUnarchiveTask('gzip');
        $stream = \Flexio\Tests\Util::createStream('/text/02.11-header-basic.csv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($archivetask)->execute($unarchivetask);
        $actual = $process->getStdout()->getReader()->read();
        $expected = 'f1';
        \Flexio\Tests\Check::assertArray('A.1', 'Archive/Unarchive; basic file',  $actual, $expected, $results);
    }
}
