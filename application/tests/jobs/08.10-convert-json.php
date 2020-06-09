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
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "convert",
                "input" => [
                    "format" => "json"
                ],
                "output" => [
                    "format" => "csv"
                ]
            ]
        ]);

        return $task;
    }

    public function run(&$results)
    {
        // TODO: fill out
    }
}
