<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
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
// TODO:
// settype tests are deprecated with new memory stream until settype can be reimplemented with the new
return;


        // TODO: placeholder job to test basic functionality; fill out tests

        // SETUP
        $create = json_decode('{
            "op": "create",
            "params": {
                "content_type": "'.\Flexio\Base\ContentType::FLEXIO_TABLE.'",
                "columns": [
                    { "name": "field1", "type": "character", "width": 6, "scale": 0 },
                    { "name": "field2", "type": "character", "width": 6, "scale": 2 }
                ],
                "content": [
                    ["1","1.2"],
                    ["2","2.3"],
                    ["3","3.6"]
                ]
            }
        }',true);



        // TEST: SetType Job

        // BEGIN TEST
        $task = json_decode('{
            "op": "settype",
            "params": {
                "columns": ["field1"],
                "type": "numeric",
                "width": 10,
                "decimals": 2
            }
        }',true);
        $process = \Flexio\Jobs\Process::create()->execute($create)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["1.00","1.2"],["2.00","2.3"],["3.00","3.6"]];
        TestCheck::assertString('A.1', 'SetType Job; check basic functionality',  $actual, $expected, $results);

        // BEGIN TEST
        $task = json_decode('{
            "op": "settype",
            "params": {
                "columns": ["field2"],
                "type": "numeric",
                "width": 10,
                "decimals": 0
            }
        }',true);
        $process = \Flexio\Jobs\Process::create()->execute($create)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["1","1"],["2","2"],["3","4"]];
        TestCheck::assertString('A.2', 'SetType Job; check basic functionality',  $actual, $expected, $results);

        // BEGIN TEST
        $task = json_decode('{
            "op": "settype",
            "params": {
                "columns": ["field2", "field1"],
                "type": "integer",
                "width": 10,
                "decimals": 0
            }
        }',true);
        $process = \Flexio\Jobs\Process::create()->execute($create)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [[1,1],[2,2],[3,4]];
        TestCheck::assertString('A.3', 'SetType Job; check basic functionality',  $actual, $expected, $results);

        // BEGIN TEST
        $task = json_decode('{
            "op": "settype",
            "params": {
                "columns": ["field2"],
                "type": "numeric",
                "width": 10,
                "decimals": 0
            }
        }',true);
        $process = \Flexio\Jobs\Process::create()->execute($create)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["1","1"],["2","2"],["3","4"]];
        TestCheck::assertString('A.4', 'SetType Job; check basic functionality',  $actual, $expected, $results);

        // BEGIN TEST
        $task = json_decode('{
            "op": "settype",
            "params": {
                "columns": ["field[0-9]*"],
                "type": "numeric",
                "width": 10,
                "decimals": 3
            }
        }',true);
        $process = \Flexio\Jobs\Process::create()->execute($create)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["1.000","1.200"],["2.000","2.300"],["3.000","3.600"]];
        TestCheck::assertString('A.5', 'SetType Job; check basic functionality',  $actual, $expected, $results);
    }
}
