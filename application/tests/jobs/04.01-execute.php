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

        // TEST: Base Execute Job Tests

        // BEGIN TEST
        $script = <<<EOD
def flexio_handler(context):
    context.output.content_type = "text/plain"
    context.output.write("Hello, World!")
EOD;
        $task = array(json_decode('{
            "type": "flexio.execute",
            "params": {
                "lang": "python",
                "code": "'.base64_encode($script).'"
            }
        }',true));
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = $process->getStdout()->getReader()->read(50);
        $expected = 'Hello, World!';
        TestCheck::assertString('A.1', 'Execute Job; check basic python execution functionality',  $actual, $expected, $results);

        // BEGIN TEST
        $script = <<<EOD
exports.flexio_handler = function(context) {
    context.output.content_type = "text/plain"
    context.output.write('Hello, World!')
}
EOD;
        $task = array(json_decode('{
            "type": "flexio.execute",
            "params": {
                "lang": "javascript",
                "code": "'.base64_encode($script).'"
            }
        }',true));
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = $process->getStdout()->getReader()->read(50);
        $expected = 'Hello, World!';
        TestCheck::assertString('A.2', 'Execute Job; check basic javascript execution functionality',  $actual, $expected, $results);



        // TEST: Base Execute Job Code SHA256 Integrity Check

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.output.content_type='text/plain';context.output.write('Hello,World!');}";
        $task = array(json_decode('{
            "type": "flexio.execute",
            "params": {
                "lang": "javascript",
                "code": "'.base64_encode($script).'",
                "integrity": "sha256:91898ffd8d03ac47e045cbcd60d1c2133df7b6b9c845d87094ec69196cf39119"
            }
        }',true));
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = $process->getStdout()->getReader()->read(50);
        $expected = 'Hello,World!';
        TestCheck::assertString('B.1', 'Execute Job; sha256 integrity check',  $actual, $expected, $results);

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.output.content_type='text/plain';context.output.write('Hello,World!');}";
        $task = array(json_decode('{
            "type": "flexio.execute",
            "params": {
                "lang": "javascript",
                "code": "'.base64_encode($script).'",
                "integrity": "sha256:91898FFD8D03AC47E045CBCD60D1C2133DF7B6B9C845D87094EC69196CF39119"
            }
        }',true));
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = $process->getStdout()->getReader()->read(50);
        $expected = 'Hello,World!';
        TestCheck::assertString('B.2', 'Execute Job; sha256 integrity check with uppercase sha256 code',  $actual, $expected, $results);

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.output.content_type='text/plain';context.output.write('Hello,You!');}";
        $task = array(json_decode('{
            "type": "flexio.execute",
            "params": {
                "lang": "javascript",
                "code": "'.base64_encode($script).'",
                "integrity": "sha256:9beee4ffd5906d126ac456b262c5f6fac718062bab269886149f73773a31d9b7"
            }
        }',true));
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = $process->hasError();
        $expected = true;
        TestCheck::assertBoolean('B.3', 'Execute Job; check code integrity; sha256 integrity failure',  $actual, $expected, $results);

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.output.content_type='text/plain';context.output.write('Hello,World!');}";
        $task = array(json_decode('{
            "type": "flexio.execute",
            "params": {
                "lang": "python",
                "code": "'.base64_encode($script).'",
                "integrity": "sha512:9beee4ffd5906d126ac456b262c5f6fac718062bab269886149f73773a31d9b7"
            }
        }',true));
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = $process->hasError();
        $expected = true;
        TestCheck::assertBoolean('B.4', 'Execute Job; check code integrity; sha256 format (sha512 indicated) integrity failure',  $actual, $expected, $results);

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.output.content_type='text/plain';context.output.write('Hello,World!');}";
        $task = array(json_decode('{
            "type": "flexio.execute",
            "params": {
                "lang": "python",
                "code": "'.base64_encode($script).'",
                "integrity": "md5-a223b94fdf072f085adf67a4310abb59"
            }
        }',true));
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = $process->hasError();
        $expected = true;
        TestCheck::assertBoolean('B.5', 'Execute Job; check code integrity; md5 integrity failure; md5 not supported',  $actual, $expected, $results);



        // TEST: Base Execute Job Code SHA384 Integrity Check

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.output.content_type='text/plain';context.output.write('Hello,World!');}";
        $task = array(json_decode('{
            "type": "flexio.execute",
            "params": {
                "lang": "javascript",
                "code": "'.base64_encode($script).'",
                "integrity": "sha384:c72e2df773b8c4117b19e3ced4c7e30582a5a75f93837632287b5fb65e0c9c393435c4d816f6d24054148f099b43be23"
            }
        }',true));
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = $process->getStdout()->getReader()->read(50);
        $expected = 'Hello,World!';
        TestCheck::assertString('C.1', 'Execute Job; sha384 integrity check',  $actual, $expected, $results);

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.output.content_type='text/plain';context.output.write('Hello,You!');}";
        $task = array(json_decode('{
            "type": "flexio.execute",
            "params": {
                "lang": "javascript",
                "code": "'.base64_encode($script).'",
                "integrity": "sha384:c72e2df773b8c4117b19e3ced4c7e30582a5a75f93837632287b5fb65e0c9c393435c4d816f6d24054148f099b43be23"
            }
        }',true));
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = $process->hasError();
        $expected = true;
        TestCheck::assertBoolean('C.2', 'Execute Job; check code integrity; sha384 integrity failure',  $actual, $expected, $results);



        // TEST: Base Execute Job Code SHA512 Integrity Check

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.output.content_type='text/plain';context.output.write('Hello,World!');}";
        $task = array(json_decode('{
            "type": "flexio.execute",
            "params": {
                "lang": "javascript",
                "code": "'.base64_encode($script).'",
                "integrity": "sha512:c43a28e60208e274d88bfd4a25d219854e921a78c68395c2d1b7ee5a65da2d057f82cd266b5ae5fde7740b3e9e175e0802633b0844b34366bb3b0eef9d165a1d"
            }
        }',true));
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = $process->getStdout()->getReader()->read(50);
        $expected = 'Hello,World!';
        TestCheck::assertString('D.1', 'Execute Job; sha512 integrity check',  $actual, $expected, $results);

        // BEGIN TEST
        $script = "exports.flexio_handler=function(context){context.output.content_type='text/plain';context.output.write('Hello,You!');}";
        $task = array(json_decode('{
            "type": "flexio.execute",
            "params": {
                "lang": "javascript",
                "code": "'.base64_encode($script).'",
                "integrity": "sha512:c43a28e60208e274d88bfd4a25d219854e921a78c68395c2d1b7ee5a65da2d057f82cd266b5ae5fde7740b3e9e175e0802633b0844b34366bb3b0eef9d165a1d"
            }
        }',true));
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = $process->hasError();
        $expected = true;
        TestCheck::assertBoolean('D.2', 'Execute Job; check code integrity; sha512 integrity failure',  $actual, $expected, $results);



        // TEST: Base Execute Job Code SHA512 Integrity Check

        // BEGIN TEST
        $task = array(json_decode('{
            "type": "flexio.execute",
            "params": {
                "lang": "python",
                "path": "https://raw.githubusercontent.com/flexiodata/examples/master/functions/hello-world.py"
            }
        }',true));
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = $process->getStdout()->getReader()->read(50);
        $expected = 'Hello, World!';
        TestCheck::assertString('E.1', 'Execute Job; remote execution',  $actual, $expected, $results);

        // BEGIN TEST
        $task = array(json_decode('{
            "type": "flexio.execute",
            "params": {
                "lang": "python",
                "path": "https://raw.githubusercontent.com/flexiodata/examples/master/functions/hello-world.py",
                "integrity": "sha256:891568494dfb8fce562955b1509aee5a1ce0ce05ae210da6556517dd3986de36"
            }
        }',true));
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = $process->getStdout()->getReader()->read(50);
        $expected = 'Hello, World!';
        TestCheck::assertString('E.2', 'Execute Job; remote execution with sha256 check',  $actual, $expected, $results);



        // TEST: Base Execute Job Code with local code override

        // BEGIN TEST
        $script = <<<EOD
def flexio_handler(context):
    context.output.content_type = "text/plain"
    context.output.write("This is local.")
EOD;
        $task = array(json_decode('{
            "type": "flexio.execute",
            "params": {
                "lang": "python",
                "code": "'.base64_encode($script).'",
                "path": "https://raw.githubusercontent.com/flexiodata/examples/master/functions/hello-world.py"
            }
        }',true));
        $process = \Flexio\Jobs\Process::create()->setTasks($task)->execute();
        $actual = $process->getStdout()->getReader()->read(50);
        $expected = 'This is local.';
        TestCheck::assertString('F.1', 'Execute Job; local code override',  $actual, $expected, $results);
    }
}
