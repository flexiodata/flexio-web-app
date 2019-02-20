<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-07-08
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
        // TEST: Convert Delimited; single fieldname

        // BEGIN TEST
        $task = self::createConvertTask(",", "\"", true);
        $stream = \Flexio\Tests\Util::createStream('/text/02.10-header-basic.csv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["f1"]';
        \Flexio\Tests\Check::assertArray('A.1', 'Convert Delimited; single fieldname should create correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask(",", "\"", true);
        $stream = \Flexio\Tests\Util::createStream('/text/02.11-header-basic.csv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["f1"]';
        \Flexio\Tests\Check::assertArray('A.2', 'Convert Delimited; single fieldname should create correctly',  $actual, $expected, $results);



        // TEST: Convert Delimited; fieldnames with leading/trailing/embedded spaces

        // BEGIN TEST
        $task = self::createConvertTask(",", "\"", true);
        $stream = \Flexio\Tests\Util::createStream('/text/02.20-header-space.csv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["f1", "f2", "f3", "f4", "f  5", "f  6", "f  7", "f  8", "f9"]';
        \Flexio\Tests\Check::assertArray('B.1', 'Convert Delimited; leading/trailing spaces in a fieldname should be trimmed; internal spaces preserved',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask(",", "\"", true);
        $stream = \Flexio\Tests\Util::createStream('/text/02.21-header-space.csv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["f1", "f2", "f3", "f4", "f  5", "f  6", "f  7", "f  8", "f9"]';
        \Flexio\Tests\Check::assertArray('B.2', 'Convert Delimited; leading/trailing spaces in a fieldname should be trimmed; internal spaces preserved',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask(",", "\"", true);
        $stream = \Flexio\Tests\Util::createStream('/text/02.21-header-space.csv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["f1", "f2", "f3", "f4", "f  5", "f  6", "f  7", "f  8", "f9"]';
        \Flexio\Tests\Check::assertArray('B.3', 'Convert Delimited; leading/trailing spaces in a fieldname should be trimmed; internal spaces preserved',  $actual, $expected, $results);



        // TEST: Convert Delimited; fieldnames with uppercase characters

        // BEGIN TEST
        $task = self::createConvertTask(",", "\"", true);
        $stream = \Flexio\Tests\Util::createStream('/text/02.30-header-case.csv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["f1", "f2", "f3", "f4", "f  f5", "f  f6", "f  f7", "f  f8", "f_f9"]';
        \Flexio\Tests\Check::assertArray('D.1', 'Convert Delimited; uppercase characters in a fieldname should be converted to lowercase',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask(",", "\"", true);
        $stream = \Flexio\Tests\Util::createStream('/text/02.31-header-case.csv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["f1", "f2", "f3", "f4", "f  f5", "f  f6", "f  f7", "f  f8", "f_f9"]';
        \Flexio\Tests\Check::assertArray('D.2', 'Convert Delimited; uppercase characters in a fieldname should be converted to lowercase',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask(",", "\"", true);
        $stream = \Flexio\Tests\Util::createStream('/text/02.32-header-case.csv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["f1", "f2", "f3", "f4", "f  f5", "f  f6", "f  f7", "f  f8", "f_f9"]';
        \Flexio\Tests\Check::assertArray('D.3', 'Convert Delimited; uppercase characters in a fieldname should be converted to lowercase in',  $actual, $expected, $results);



        // TEST: Convert Delimited; fieldnames with embedded symbols

        // BEGIN TEST
        $task = self::createConvertTask(",", "\"", true);
        $stream = \Flexio\Tests\Util::createStream('/text/02.40-header-symbol.csv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '[",","field2","field3","field3_1","field5","field6"]';
        \Flexio\Tests\Check::assertArray('E.1', 'Convert Job; allow a fieldname to contain embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask(",", "\"", true);
        $stream = \Flexio\Tests\Util::createStream('/text/02.41-header-symbol.csv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["10","20","30","40","50"]';
        \Flexio\Tests\Check::assertArray('E.2', 'Convert Job; allow a fieldname to contain embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask(",", "\"", true);
        $stream = \Flexio\Tests\Util::createStream('/text/02.42-header-symbol.csv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["t1.f1","t1.f2","t2.f1","t2.f2","t3. f1","t3 .f2"]';
        \Flexio\Tests\Check::assertArray('E.3', 'Convert Job; allow a fieldname to contain embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask(",", "\"", true);
        $stream = \Flexio\Tests\Util::createStream('/text/02.43-header-symbol.csv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["#1","# 2","3#"]';
        \Flexio\Tests\Check::assertArray('E.4', 'Convert Job; allow a fieldname to contain embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask(",", "\"", true);
        $stream = \Flexio\Tests\Util::createStream('/text/02.44-header-symbol.csv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["f(1)","(f)2","(f3)","(f 4)","f (5)","(f) 6","( f 7 )","()","( )"]';
        \Flexio\Tests\Check::assertArray('E.5', 'Convert Job; allow a fieldname to contain embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask(",", "\"", true);
        $stream = \Flexio\Tests\Util::createStream('/text/02.45-header-symbol.csv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["<25%","25-50%","50-75%","> 75%"]';
        \Flexio\Tests\Check::assertArray('E.6', 'Convert Job; allow a fieldname to contain embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask(",", "\"", true);
        $stream = \Flexio\Tests\Util::createStream('/text/02.46-header-symbol.csv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["<=100","101-199","200 - 299","300-"]';
        \Flexio\Tests\Check::assertArray('E.7', 'Convert Job; allow a fieldname to contain embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask(",", "\"", true);
        $stream = \Flexio\Tests\Util::createStream('/text/02.47-header-symbol.csv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["1/2010","2/2010","3/2010","[2020?]"]';
        \Flexio\Tests\Check::assertArray('E.8', 'Convert Job; allow a fieldname to contain embedded symbols',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask(",", "\"", true);
        $stream = \Flexio\Tests\Util::createStream('/text/02.48-header-symbol.csv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["%","% (of total amount)","$","$ (total)"]';
        \Flexio\Tests\Check::assertArray('E.9', 'Convert Job; allow a fieldname to contain embedded symbols',  $actual, $expected, $results);



        // TEST: Convert; keywords in fieldnames

        // BEGIN TEST
        $task = self::createConvertTask(",", "\"", true);
        $stream = \Flexio\Tests\Util::createStream('/text/02.50-header-keyword.csv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["row","column","min","max","sum","avg","count","total","update","updated","delete","deleted","select","where","true","false","yes","no","null"]';
        \Flexio\Tests\Check::assertArray('F.1', 'Convert Delimited; allow a fieldname to be a keyword',  $actual, $expected, $results);



        // TEST: Convert; duplicate fieldnames should be enumerated to avoid duplication

        // BEGIN TEST
        $task = self::createConvertTask(",", "\"", true);
        $stream = \Flexio\Tests\Util::createStream('/text/02.60-header-duplicate.csv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["id","order #","id_1","order #_1","id_2","order #_2"]';
        \Flexio\Tests\Check::assertArray('G.1', 'Convert Job; duplicate fieldnames should be enumerated to avoid duplication',  $actual, $expected, $results);

    }
}
