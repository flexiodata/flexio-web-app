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
        $files = TestUtil::getTestDataFiles();
        $store_alias = "testsuite-dropbox";

        $idx = 0;
        foreach ($files as $full_filename)
        {
            $idx++;
            $filename = \Flexio\Base\File::getFilename($full_filename);
            $fileextension = \Flexio\Base\File::getFileExtension($full_filename);
            $output_file_path = "/" . $store_alias . "/" . $filename . "." . $fileextension;

            $read = json_decode('
            {
                "op": "read",
                "params": {
                    "path": "'. $output_file_path . '"
                }
            }
            ',true);

            $write = json_decode('
            {
                "op": "write",
                "params": {
                    "path": "'. $output_file_path . '"
                }
            }
            ',true);

            $stream = TestUtil::createStreamFromFile($full_filename);
            $process_write = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($write);
            $process_read = \Flexio\Jobs\Process::create()->execute($read);
            $actual_contents = \Flexio\Base\Util::getStreamContents($process_read->getStdout());
            $expected_contents = \Flexio\Base\Util::getStreamContents($stream);
            $actual = md5($actual_contents);
            $expected = md5($expected_contents);
            TestCheck::assertString("A.$idx", 'Read/Write; check write/read to/from ' . $output_file_path, $actual, $expected, $results);
        }
    }
}
