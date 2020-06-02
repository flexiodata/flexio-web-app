<?php
/**
 *
 * Copyright (c) 2017, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-11-27
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
        // TODO: placeholder job to test basic functionality; fill out tests


        // TEST: Email Job

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([["email" => "read", "to" => "", "subject" => "Test", "body" => "This is a test"]]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $output = $process->getStdout()->getReader()->read();
        $actual = json_decode($output,true);
        $expected = '';
        \Flexio\Tests\Check::assertString('A.1', 'Email; check basic functionality',  $actual, $expected, $results);
    }
}
