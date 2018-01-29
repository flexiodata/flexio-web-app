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
        // TODO: set storage path

        // SETUP
        $storage_path= "/vfs-alias/path/file.txt";

        $read = json_decode('
        {
            "op": "read",
            "params": {
                "path": "'. $storage_path . '"
            }
        }
        ',true);

        $write = json_decode('
        {
            "op": "write",
            "params": {
                "path": "'. $storage_path . '"
            }
        }
        ',true);



        // TEST: Read/Write

        // BEGIN TEST
        $data = <<<EOD

        Test

EOD;

        $stream = \Flexio\Base\Stream::create();
        $stream->getWriter()->write($data);
        $process_write = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($write);
        $process_read = \Flexio\Jobs\Process::create()->execute($read);
        $actual = $process_read->getStdout()->getReader()->read();
        $expected = $data;
        TestCheck::assertString('A.1', 'Read/Write; check basic functionality',  $actual, $expected, $results);
    }
}
