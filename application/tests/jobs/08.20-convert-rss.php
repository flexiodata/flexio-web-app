<?php
/**
 *
 * Copyright (c) 2015, Flex Research LLC. All rights reserved.
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
        $task = array(
            "op" => "convert",
            "input" => array(
                "format" => "rss"
            ),
            "output" => array(
                "format" => "ndjson"
            )
        );

        return $task;
    }

    public function run(&$results)
    {
        // TEST: Convert RSS; empty file

        // BEGIN TEST
        $task = self::createConvertTask();
        $stream = \Flexio\Tests\Util::createStream('/xml/01.01-empty.rss');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = ["link","title","description","content","source","author","date"];
        \Flexio\Tests\Check::assertArray('A.1', 'Convert RSS; empty file',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask();
        $stream = \Flexio\Tests\Util::createStream('/xml/01.01-empty.rss');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getReader()->read();
        $expected = '';
        \Flexio\Tests\Check::assertString('A.2', 'Convert RSS; empty file',  $actual, $expected, $results);


        // TEST: Convert RSS; malformed file

        // BEGIN TEST
        $task = self::createConvertTask();
        $stream = \Flexio\Tests\Util::createStream('/xml/01.02-malformed.rss');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = ["link","title","description","content","source","author","date"];
        \Flexio\Tests\Check::assertArray('B.1', 'Convert RSS; malformed file',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask();
        $stream = \Flexio\Tests\Util::createStream('/xml/01.02-malformed.rss');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getReader()->read();
        $expected = '';
        \Flexio\Tests\Check::assertString('B.2', 'Convert RSS; malformed file',  $actual, $expected, $results);


        // TEST: Convert RSS; simple file

        // BEGIN TEST
        $task = self::createConvertTask();
        $stream = \Flexio\Tests\Util::createStream('/xml/01.03-minimum.rss');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = ["link","title","description","content","source","author","date"];
        \Flexio\Tests\Check::assertArray('C.1', 'Convert RSS; simple file',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask();
        $stream = \Flexio\Tests\Util::createStream('/xml/01.03-minimum.rss');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getReader()->read();
        $expected = '{"link":"","title":"","description":"","content":"","source":null,"author":null,"date":null}';
        \Flexio\Tests\Check::assertString('C.2', 'Convert RSS; simple file',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask();
        $stream = \Flexio\Tests\Util::createStream('/xml/01.04-simple.rss');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = ["link","title","description","content","source","author","date"];
        \Flexio\Tests\Check::assertArray('C.3', 'Convert RSS; simple file',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask();
        $stream = \Flexio\Tests\Util::createStream('/xml/01.04-simple.rss');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getReader()->read();
        $expected = '{"link":"http://www.flex.io/rss/item.html","title":"Item Title","description":"This is a test.","content":"This is a test.","source":null,"author":null,"date":null}';
        \Flexio\Tests\Check::assertString('C.4', 'Convert RSS; simple file',  $actual, $expected, $results);
    }
}
