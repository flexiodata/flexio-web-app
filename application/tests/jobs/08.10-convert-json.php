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
    public function createConvertTask()
    {
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "convert",
                "input" => [
                    "format" => "json"
                ],
                "output" => [
                    "format" => "table"
                ]
            ]
        ]);

        return $task;
    }

    public function run(&$results)
    {
        // TEST: Convert JSON; empty file
/*
        // BEGIN TEST
        $task = self::createConvertTask();
        $stream = \Flexio\Tests\Util::createStream('/json/01.01-empty.json');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.1', 'Convert JSON; empty file',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask();
        $stream = \Flexio\Tests\Util::createStream('/json/01.01-empty.json');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.2', 'Convert JSON; empty file',  $actual, $expected, $results);


        // TEST: Convert JSON; malformed file

        // BEGIN TEST
        $task = self::createConvertTask();
        $stream = \Flexio\Tests\Util::createStream('/json/01.02-malformed.json');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = [];
        \Flexio\Tests\Check::assertArray('B.1', 'Convert JSON; malformed file',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask();
        $stream = \Flexio\Tests\Util::createStream('/json/01.02-malformed.json');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [];
        \Flexio\Tests\Check::assertArray('B.2', 'Convert JSON; malformed file',  $actual, $expected, $results);


        // TEST: Convert JSON; simple file

        // BEGIN TEST
        $task = self::createConvertTask();
        $stream = \Flexio\Tests\Util::createStream('/json/01.03-simple.json');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = [];
        \Flexio\Tests\Check::assertArray('C.1', 'Convert JSON; simple file',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask();
        $stream = \Flexio\Tests\Util::createStream('/json/01.03-simple.json');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [];
        \Flexio\Tests\Check::assertArray('C.2', 'Convert JSON; simple file',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask();
        $stream = \Flexio\Tests\Util::createStream('/json/01.04-simple.json');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = [];
        \Flexio\Tests\Check::assertArray('C.3', 'Convert JSON; simple file',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask();
        $stream = \Flexio\Tests\Util::createStream('/json/01.04-simple.json');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [];
        \Flexio\Tests\Check::assertArray('C.4', 'Convert JSON; simple file',  $actual, $expected, $results);
*/
    }
}
