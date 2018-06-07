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
                    "format" => "rss"
                ]
            ]
        ]);

        return $task;
    }

    public function run(&$results)
    {
    }
}
