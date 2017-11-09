<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-03-31
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

        // SETUP
        $create = json_decode('{
            "type": "flexio.create",
            "params": {
                "name": "table",
                "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                "columns": [
                    { "name": "c1", "type": "character", "width": 3 }
                ],
                "content": [
                    {"c1" : "a"},
                    {"c1" : "b"}
                ]
            }
        }',true);



        // TEST: Base Execute Job Tests

        // BEGIN TEST
        $script = <<<EOD
def flexio_handler(input,output):
    output.content_type = "text/plain"
    output.write("Hello, World!")
EOD;
        $task = array($create, json_decode('{
            "type": "flexio.execute",
            "params": {
                "lang": "python",
                "code": "'.base64_encode($script).'"
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getStdout()->content(0,50);
        $expected = 'Hello, World!';
        TestCheck::assertString('A.1', 'Execute Job; check basic python execution functionality',  $actual, $expected, $results);

        // BEGIN TEST
        $script = <<<EOD
exports.flexio_handler = function(input, output) {
    output.content_type = "text/plain"
    output.write('Hello, World!')
}
EOD;
        $task = array($create, json_decode('{
            "type": "flexio.execute",
            "params": {
                "lang": "javascript",
                "code": "'.base64_encode($script).'"
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getStdout()->content(0,50);
        $expected = 'Hello, World!';
        TestCheck::assertString('A.2', 'Execute Job; check basic javascript execution functionality',  $actual, $expected, $results);



        // TEST: Base Execute Job Code SHA256 Integrity Check

        // BEGIN TEST
        $script = "exports.flexio_handler=function(input,output){output.content_type='text/plain';output.write('Hello,World!');}";
        $task = array($create, json_decode('{
            "type": "flexio.execute",
            "params": {
                "lang": "javascript",
                "code": "'.base64_encode($script).'",
                "integrity": "sha256-9beee4ffd5906d126ac456b262c5f6fac718062bab269886149f73773a31d9b7"
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getStdout()->content(0,50);
        $expected = 'Hello,World!';
        TestCheck::assertString('B.1', 'Execute Job; sha256 integrity check',  $actual, $expected, $results);

        // BEGIN TEST
        $script = "exports.flexio_handler=function(input,output){output.content_type='text/plain';output.write('Hello,World!');}";
        $task = array($create, json_decode('{
            "type": "flexio.execute",
            "params": {
                "lang": "javascript",
                "code": "'.base64_encode($script).'",
                "integrity": "sha256-9BEEE4FFD5906D126AC456B262C5F6FAC718062BAB269886149F73773A31D9B7"
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getStdout()->content(0,50);
        $expected = 'Hello,World!';
        TestCheck::assertString('B.2', 'Execute Job; sha256 integrity check with uppercase sha256 code',  $actual, $expected, $results);

        // BEGIN TEST
        $script = "exports.flexio_handler=function(input,output){output.content_type='text/plain';output.write('Hello,You!');}";
        $task = array($create, json_decode('{
            "type": "flexio.execute",
            "params": {
                "lang": "javascript",
                "code": "'.base64_encode($script).'",
                "integrity": "sha256-9beee4ffd5906d126ac456b262c5f6fac718062bab269886149f73773a31d9b7"
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.3', 'Execute Job; check code integrity; sha256 integrity failure',  $actual, $expected, $results);

        // BEGIN TEST
        $script = "exports.flexio_handler=function(input,output){output.content_type='text/plain';output.write('Hello,World!');}";
        $task = array($create, json_decode('{
            "type": "flexio.execute",
            "params": {
                "lang": "python",
                "code": "'.base64_encode($script).'",
                "integrity": "sha512-9beee4ffd5906d126ac456b262c5f6fac718062bab269886149f73773a31d9b7"
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.4', 'Execute Job; check code integrity; sha256 format (sha512 indicated) integrity failure',  $actual, $expected, $results);

        // BEGIN TEST
        $script = "exports.flexio_handler=function(input,output){output.content_type='text/plain';output.write('Hello,World!');}";
        $task = array($create, json_decode('{
            "type": "flexio.execute",
            "params": {
                "lang": "python",
                "code": "'.base64_encode($script).'",
                "integrity": "md5-a223b94fdf072f085adf67a4310abb59"
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('B.5', 'Execute Job; check code integrity; md5 integrity failure; md5 not supported',  $actual, $expected, $results);



        // TEST: Base Execute Job Code SHA384 Integrity Check

        // BEGIN TEST
        $script = "exports.flexio_handler=function(input,output){output.content_type='text/plain';output.write('Hello,World!');}";
        $task = array($create, json_decode('{
            "type": "flexio.execute",
            "params": {
                "lang": "javascript",
                "code": "'.base64_encode($script).'",
                "integrity": "sha384-621a93ffd4d3b57d8e786d6dab8ce59ef6e9a187a19c0b64a71d749370fed32e54ddbacd2bcdd931b6db525d883fc9b6"
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getStdout()->content(0,50);
        $expected = 'Hello,World!';
        TestCheck::assertString('C.1', 'Execute Job; sha384 integrity check',  $actual, $expected, $results);

        // BEGIN TEST
        $script = "exports.flexio_handler=function(input,output){output.content_type='text/plain';output.write('Hello,You!');}";
        $task = array($create, json_decode('{
            "type": "flexio.execute",
            "params": {
                "lang": "javascript",
                "code": "'.base64_encode($script).'",
                "integrity": "sha384-621a93ffd4d3b57d8e786d6dab8ce59ef6e9a187a19c0b64a71d749370fed32e54ddbacd2bcdd931b6db525d883fc9b6"
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('C.2', 'Execute Job; check code integrity; sha384 integrity failure',  $actual, $expected, $results);



        // TEST: Base Execute Job Code SHA512 Integrity Check

        // BEGIN TEST
        $script = "exports.flexio_handler=function(input,output){output.content_type='text/plain';output.write('Hello,World!');}";
        $task = array($create, json_decode('{
            "type": "flexio.execute",
            "params": {
                "lang": "javascript",
                "code": "'.base64_encode($script).'",
                "integrity": "sha512-8febacfe8bb5bbd5c821da18dfd915d48911af97ab98f83770cd201a6da385a444017fffdc5c183194fb8406b3ad1ffba5a25b44077d4b80629b96e9c77a37ed"
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getStdout()->content(0,50);
        $expected = 'Hello,World!';
        TestCheck::assertString('D.1', 'Execute Job; sha512 integrity check',  $actual, $expected, $results);

        // BEGIN TEST
        $script = "exports.flexio_handler=function(input,output){output.content_type='text/plain';output.write('Hello,You!');}";
        $task = array($create, json_decode('{
            "type": "flexio.execute",
            "params": {
                "lang": "javascript",
                "code": "'.base64_encode($script).'",
                "integrity": "sha512-8febacfe8bb5bbd5c821da18dfd915d48911af97ab98f83770cd201a6da385a444017fffdc5c183194fb8406b3ad1ffba5a25b44077d4b80629b96e9c77a37ed"
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = $process->getProcessStatus();
        $expected = \Model::PROCESS_STATUS_FAILED;
        TestCheck::assertString('D.2', 'Execute Job; check code integrity; sha512 integrity failure',  $actual, $expected, $results);
    }
}
