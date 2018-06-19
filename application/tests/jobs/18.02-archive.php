<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-06-19
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
        // { "op": "archive", "format": "zip", "path": "/vfs/output.zip", "files": [ "/vfs/file1.txt", "/vfs/file2.txt" ] }
        // { "op": "archive", "format": "gzip"}

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
        // { "op": "unarchive", "format": "zip", "path": "/vfs/input.zip", "files": [ "file_in_zip1.txt", "file_in_zip2.txt" ], "target": "/vfs/output_path" }
        // { "op": "unarchive", "format": "gzip"}

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
        // TEST: Archive; basic file

        // BEGIN TEST
        $archivetask = self::createArchiveTask('gzip');
        $unarchivetask = self::createUnarchiveTask('gzip');
        $inputstream = \Flexio\Tests\Util::createStream('/text/02.11-header-basic.csv');
        $archivestream = \Flexio\Jobs\Process::create()->setStdin($inputstream)->execute($archivetask)->getStdout();
        $unarchivestream = \Flexio\Jobs\Process::create()->setStdin($archivestream)->execute($unarchivetask)->getStdout();
        $actual = $unarchivestream->getReader()->read();
        $expected = "f1\r\n";
        \Flexio\Tests\Check::assertString('A.1', 'Archive; basic file',  $actual, $expected, $results);
    }
}
