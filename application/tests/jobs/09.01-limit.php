<?php
/**
 *
 * Copyright (c) 2016, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-06-07
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
        // TEST: Limit Job

        // BEGIN TEST
        $task = array(
            "op" => "limit",
            "value" => 1
        );
        $stream = \Flexio\Tests\Util::createStream('/text/02.70-sample-table.csv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = $process->getStdout()->getReader()->read(100);
        $expected = "c1,c2,n1,n2,n3,d1,d2,b1";
        \Flexio\Tests\Check::assertString('A.1', 'Limit Job; check basic functionality',  $actual, $expected, $results);
    }
}
