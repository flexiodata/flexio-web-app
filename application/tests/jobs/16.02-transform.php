<?php
/**
 *
 * Copyright (c) 2016, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-02-17
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



        // TEST: Transform Job: standardize text with capitalization (none, lowercase, uppercase, proper, first letter)

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "transform",
                "operations" => [
                    ["operation" => "case", "case" => "upper"]
                ]
            ]
        ]);
        $stream = \Flexio\Tests\Util::createStream('/text/02.70-sample-table.csv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getReader()->read(50);
        $expected = "C1,C2,N1,N2,N3,D1,D2,B1\nABC,()[]{}<>,-1.02,-1.23,-";
        \Flexio\Tests\Check::assertString('A.1', 'Transform Job; basic transformation on stream content',  $actual, $expected, $results);
    }
}
