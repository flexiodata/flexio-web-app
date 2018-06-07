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
                    "format" => "rss"
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
        // TODO: add additional tests


        // TEST: Convert RSS; empty file

        // BEGIN TEST
        $task = self::createConvertTask();
        $stream = \Flexio\Tests\Util::createStream('/rss/01.01-empty.rss');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = ["link","title","description","content","source","author","date"];
        \Flexio\Tests\Check::assertArray('A.1', 'Convert RSS; empty file',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask();
        $stream = \Flexio\Tests\Util::createStream('/rss/01.01-empty.rss');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.2', 'Convert RSS; empty file',  $actual, $expected, $results);


        // TEST: Convert RSS; simple file

        // BEGIN TEST
        $task = self::createConvertTask();
        $stream = \Flexio\Tests\Util::createStream('/rss/01.03-simple.rss');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = ["link","title","description","content","source","author","date"];
        \Flexio\Tests\Check::assertArray('B.1', 'Convert RSS; simple file',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask();
        $stream = \Flexio\Tests\Util::createStream('/rss/01.03-simple.rss');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["","","","",null,null,null]];
        \Flexio\Tests\Check::assertArray('B.2', 'Convert RSS; simple file',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask();
        $stream = \Flexio\Tests\Util::createStream('/rss/01.04-simple.rss');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = ["link","title","description","content","source","author","date"];
        \Flexio\Tests\Check::assertArray('B.3', 'Convert RSS; simple file',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask();
        $stream = \Flexio\Tests\Util::createStream('/rss/01.04-simple.rss');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["http://www.flex.io/rss/item.html","Item Title","This is a test.","This is a test.",null,null,null]];
        \Flexio\Tests\Check::assertArray('B.4', 'Convert RSS; simple file',  $actual, $expected, $results);
    }
}
