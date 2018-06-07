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
    public function createConvertTask(string $delimiter, string $qualifier, bool $header)
    {
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "convert",
                "input" => [
                    "format" => "delimited",
                    "delimiter" => "$delimiter",
                    "qualifier" => "$qualifier",
                    "header" => $header,
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
        // TODO: additional tests should include:
        // 1. handle bad job convert parameters
        // 2. blank initial rows (first row isn't on first row)
        // 3. embedded blank rows
        // 4. trailing blank rows
        // 5. varying number of delimiters in various rows
        // 6. different line-feed characters
        // 7. different text qualifiers


        // TEST: Convert Delimited; empty file

        // BEGIN TEST
        $task = self::createConvertTask("{comma}", "{none}", true);
        $stream = \Flexio\Tests\Util::createStream('/delimited/01.01-empty.txt');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.1', 'Convert Delimited; empty file',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("{comma}", "{none}", false);
        $stream = \Flexio\Tests\Util::createStream('/delimited/01.01-empty.txt');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.2', 'Convert Delimited; empty file',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("{comma}", "{none}", true);
        $stream = \Flexio\Tests\Util::createStream('/delimited/01.01-empty.txt');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.3', 'Convert Delimited; empty file',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("{comma}", "{none}", false);
        $stream = \Flexio\Tests\Util::createStream('/delimited/01.01-empty.txt');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [];
        \Flexio\Tests\Check::assertArray('A.4', 'Convert Delimited; empty file',  $actual, $expected, $results);


        // TEST: Convert Delimited; variations in delimiter

        // BEGIN TEST
        $task = self::createConvertTask("{none}", "{none}", false);
        $stream = \Flexio\Tests\Util::createStream('/delimited/01.02-delimiter.txt');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [
            [",|	~; :,|	~; :"],
            ["|	~; :,|	~; :,"],
            ["	~; :,|	~; :,|"],
            ["~; :,|	~; :,|	"],
            ["; :,|	~; :,|	~"],
            [" :,|	~; :,|	~;"],
            [":,|	~; :,|	~; "]
        ];
        \Flexio\Tests\Check::assertArray('B.1', 'Convert Delimited; variations in delimiter',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("{comma}", "{none}", false);
        $stream = \Flexio\Tests\Util::createStream('/delimited/01.02-delimiter.txt');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [
            ["","|	~; :","|	~; :"],
            ["|	~; :","|	~; :",""],
            ["	~; :","|	~; :","|"],
            ["~; :","|	~; :","|	"],
            ["; :","|	~; :","|	~"],
            [" :","|	~; :","|	~;"],
            [":","|	~; :","|	~; "]
        ];
        \Flexio\Tests\Check::assertArray('B.2', 'Convert Delimited; variations in delimiter',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("{semicolon}", "{none}", false);
        $stream = \Flexio\Tests\Util::createStream('/delimited/01.02-delimiter.txt');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [
            [",|	~"," :,|	~"," :"],
            ["|	~"," :,|	~"," :,"],
            ["	~"," :,|	~"," :,|"],
            ["~"," :,|	~"," :,|	"],
            [""," :,|	~"," :,|	~"],
            [" :,|	~"," :,|	~",""],
            [":,|	~"," :,|	~"," "]
        ];
        \Flexio\Tests\Check::assertArray('B.3', 'Convert Delimited; variations in delimiter',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("{pipe}", "{none}", false);
        $stream = \Flexio\Tests\Util::createStream('/delimited/01.02-delimiter.txt');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [
            [",","	~; :,","	~; :"],
            ["","	~; :,","	~; :,"],
            ["	~; :,","	~; :,",""],
            ["~; :,","	~; :,","	"],
            ["; :,","	~; :,","	~"],
            [" :,","	~; :,","	~;"],
            [":,","	~; :,","	~; "]
        ];
        \Flexio\Tests\Check::assertArray('B.4', 'Convert Delimited; variations in delimiter',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("{tab}", "{none}", false);
        $stream = \Flexio\Tests\Util::createStream('/delimited/01.02-delimiter.txt');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [
            [",|","~; :,|","~; :"],
            ["|","~; :,|","~; :,"],
            ["","~; :,|","~; :,|"],
            ["~; :,|","~; :,|",""],
            ["; :,|","~; :,|","~"],
            [" :,|","~; :,|","~;"],
            [":,|","~; :,|","~; "]
        ];
        \Flexio\Tests\Check::assertArray('B.5', 'Convert Delimited; variations in delimiter',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("{space}", "{none}", false);
        $stream = \Flexio\Tests\Util::createStream('/delimited/01.02-delimiter.txt');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [
            [",|	~;",":,|	~;",":"],
            ["|	~;",":,|	~;",":,"],
            ["	~;",":,|	~;",":,|"],
            ["~;",":,|	~;",":,|	"],
            [";",":,|	~;",":,|	~"],
            ["",":,|	~;",":,|	~;"],
            [":,|	~;",":,|	~;",""]
        ];
        \Flexio\Tests\Check::assertArray('B.6', 'Convert Delimited; variations in delimiter',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask(",", "{none}", false);
        $stream = \Flexio\Tests\Util::createStream('/delimited/01.02-delimiter.txt');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [
            ["","|	~; :","|	~; :"],
            ["|	~; :","|	~; :",""],
            ["	~; :","|	~; :","|"],
            ["~; :","|	~; :","|	"],
            ["; :","|	~; :","|	~"],
            [" :","|	~; :","|	~;"],
            [":","|	~; :","|	~; "]
        ];
        \Flexio\Tests\Check::assertArray('B.7', 'Convert Delimited; variations in delimiter',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("\t", "{none}", false);
        $stream = \Flexio\Tests\Util::createStream('/delimited/01.02-delimiter.txt');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [
            [",|","~; :,|","~; :"],
            ["|","~; :,|","~; :,"],
            ["","~; :,|","~; :,|"],
            ["~; :,|","~; :,|",""],
            ["; :,|","~; :,|","~"],
            [" :,|","~; :,|","~;"],
            [":,|","~; :,|","~; "]
        ];
        \Flexio\Tests\Check::assertArray('B.8', 'Convert Delimited; variations in delimiter',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask(":", "{none}", false);
        $stream = \Flexio\Tests\Util::createStream('/delimited/01.02-delimiter.txt');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [
            [",|	~; ",",|	~; ",""],
            ["|	~; ",",|	~; ",","],
            ["	~; ",",|	~; ",",|"],
            ["~; ",",|	~; ",",|	"],
            ["; ",",|	~; ",",|	~"],
            [" ",",|	~; ",",|	~;"],
            ["",",|	~; ",",|	~; "]
        ];
        \Flexio\Tests\Check::assertArray('B.9', 'Convert Delimited; variations in delimiter',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("`", "{none}", false);
        $stream = \Flexio\Tests\Util::createStream('/delimited/01.02-delimiter.txt');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [
            [",|	~; :,|	~; :"],
            ["|	~; :,|	~; :,"],
            ["	~; :,|	~; :,|"],
            ["~; :,|	~; :,|	"],
            ["; :,|	~; :,|	~"],
            [" :,|	~; :,|	~;"],
            [":,|	~; :,|	~; "]
        ];
        \Flexio\Tests\Check::assertArray('B.10', 'Convert Delimited; variations in delimiter (delimiter doesn\'t exist)',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask(",,", "{none}", false);
        $stream = \Flexio\Tests\Util::createStream('/delimited/01.02-delimiter.txt');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [
            [",|	~; :,|	~; :"],
            ["|	~; :,|	~; :,"],
            ["	~; :,|	~; :,|"],
            ["~; :,|	~; :,|	"],
            ["; :,|	~; :,|	~"],
            [" :,|	~; :,|	~;"],
            [":,|	~; :,|	~; "]
        ];
        \Flexio\Tests\Check::assertArray('B.11', 'Convert Delimited; variations in delimiter (multiple-character delimiter)',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask(",|", "{none}", false);
        $stream = \Flexio\Tests\Util::createStream('/delimited/01.02-delimiter.txt');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [
            ["","	~; :","	~; :"],
            ["|	~; :","	~; :,",null],
            ["	~; :","	~; :",""],
            ["~; :","	~; :","	"],
            ["; :","	~; :","	~"],
            [" :","	~; :","	~;"],
            [":","	~; :","	~; "]
        ];
        \Flexio\Tests\Check::assertArray('B.12', 'Convert Delimited; variations in delimiter (multiple-character delimiter)',  $actual, $expected, $results);
    }
}
