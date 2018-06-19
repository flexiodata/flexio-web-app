<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-06-18
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
        // TEST: Unarchive; gzip

        // BEGIN TEST
        $unarchivetask = self::createUnarchiveTask('gzip');
        $inputstream = \Flexio\Tests\Util::createStream('/zip/02.01-empty.txt.gz');
        $unarchivestream = \Flexio\Jobs\Process::create()->setStdin($inputstream)->execute($unarchivetask)->getStdout();
        $actual = $unarchivestream->getReader()->read();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.1', 'Unarchive; empty file with a gz extension',  $actual, $expected, $results);

        // BEGIN TEST
        $unarchivetask = self::createUnarchiveTask('gzip');
        $inputstream = \Flexio\Tests\Util::createStream('/zip/02.02-malformed.txt.gz');
        $unarchivestream = \Flexio\Jobs\Process::create()->setStdin($inputstream)->execute($unarchivetask)->getStdout();
        $actual = $unarchivestream->getReader()->read();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.2', 'Unarchive; malformed gz file',  $actual, $expected, $results);

        // BEGIN TEST
        $unarchivetask = self::createUnarchiveTask('gzip');
        $inputstream = \Flexio\Tests\Util::createStream('/zip/02.03-minimum.txt.gz');
        $unarchivestream = \Flexio\Jobs\Process::create()->setStdin($inputstream)->execute($unarchivetask)->getStdout();
        $actual = $unarchivestream->getReader()->read();
        $expected = '';
        \Flexio\Tests\Check::assertString('A.3', 'Unarchive; valid gz file with no content',  $actual, $expected, $results);

        // BEGIN TEST
        $unarchivetask = self::createUnarchiveTask('gzip');
        $inputstream = \Flexio\Tests\Util::createStream('/zip/02.04-simple.txt.gz');
        $unarchivestream = \Flexio\Jobs\Process::create()->setStdin($inputstream)->execute($unarchivetask)->getStdout();
        $actual = $unarchivestream->getReader()->read();
        $expected = 'This is a test.';
        \Flexio\Tests\Check::assertString('A.4', 'Unarchive; valid gz file with a simple string',  $actual, $expected, $results);


        // TEST: Unarchive; zip

        // BEGIN TEST
        $unarchivetask = self::createUnarchiveTask('zip');
        $inputstream = \Flexio\Tests\Util::createStream('/zip/01.01-empty.zip');
        $unarchivestream = \Flexio\Jobs\Process::create()->setStdin($inputstream)->execute($unarchivetask)->getStdout();
        $actual = $unarchivestream->getReader()->read();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.1', 'Unarchive; empty file with a zip extension',  $actual, $expected, $results);

        // BEGIN TEST
        $unarchivetask = self::createUnarchiveTask('zip');
        $inputstream = \Flexio\Tests\Util::createStream('/zip/01.02-malformed.zip');
        $unarchivestream = \Flexio\Jobs\Process::create()->setStdin($inputstream)->execute($unarchivetask)->getStdout();
        $actual = $unarchivestream->getReader()->read();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.2', 'Unarchive; malformed zip file',  $actual, $expected, $results);
    }
}
