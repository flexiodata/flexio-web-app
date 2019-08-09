<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-08-24
 *
 * @package flexio
 * @subpackage Tests
 */


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function createConvertTask()
    {
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "convert",
                "input" => [
                    "format" => "pdf"
                ]
            ]
        ]);

        return $task;
    }

    public function run(&$results)
    {
        // TEST: Convert PDF; empty file

        // BEGIN TEST
        $task = self::createConvertTask();
        $stream = \Flexio\Tests\Util::createStream('/pdf/01.01-empty.pdf');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getReader()->read(100);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.1', 'Convert PDF; empty file',  $actual, $expected, $results);


        // TEST: Convert PDF; malformed file

        // BEGIN TEST
        $task = self::createConvertTask();
        $stream = \Flexio\Tests\Util::createStream('/pdf/01.02-malformed.pdf');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getReader()->read(100);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.1', 'Convert PDF; malformed file',  $actual, $expected, $results);


        // TEST: Convert PDF; simple file

        // BEGIN TEST
        $task = self::createConvertTask();
        $stream = \Flexio\Tests\Util::createStream('/pdf/01.03-minimum.pdf');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getReader()->read(100);
        $expected = "  ";
        \Flexio\Tests\Check::assertString('C.1', 'Convert PDF; simple file',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask();
        $stream = \Flexio\Tests\Util::createStream('/pdf/01.04-simple.pdf');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getReader()->read(100);
        $expected = "This is a test.\t  ";
        \Flexio\Tests\Check::assertString('C.2', 'Convert PDF; simple file',  $actual, $expected, $results);
    }
}
