<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-02-20
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
        $process_owner = \Flexio\Tests\Util::getTestStorageOwner();
        $files = \Flexio\Tests\Util::getTestDataFiles();
        $folderpath = "/" . \Flexio\Tests\Base::STORAGE_GITHUB . "/" . 'job-tests-' . \Flexio\Tests\Util::getTimestampName() . "/";



        // TEST: Write/Read Job; Basic Copy

        // BEGIN TEST
        $filename = 'file_that_does_not_exist.txt';
        $process_read = \Flexio\Tests\Process::read($process_owner, $folderpath . '/' . $filename);
        $actual = $process_read->hasError();
        $expected = true;
        \Flexio\Tests\Check::assertBoolean("D.1", 'Read; throw an exception when attempting to read from a file that doesn\'t exist.', $actual, $expected, $results);

        // BEGIN TEST
        $idx = 1;
        foreach ($files as $filename)
        {
            $idx++;

            $filepath = \Flexio\Tests\Util::getOutputFilePath($folderpath, $filename);
            $stream = \Flexio\Tests\Util::createStreamFromFile($filename);
            $process_write = \Flexio\Tests\Process::write($process_owner, $filepath, $stream);
            $process_read = \Flexio\Tests\Process::read($process_owner, $filepath);
            $actual_contents = \Flexio\Base\Util::getStreamContents($process_read->getStdout());
            $expected_contents = \Flexio\Base\Util::getStreamContents($stream);
            $actual = md5($actual_contents);
            $expected = md5($expected_contents);
            \Flexio\Tests\Check::assertString("D.$idx", 'Read/Write; check write/read to/from ' . $filepath, $actual, $expected, $results);
        }



        // TEST: Write/Read Job; Overwrite

        // BEGIN TEST
        $filename = \Flexio\Base\Util::generateHandle() . '.txt';
        $filepath = $folderpath . '/' . $filename;
        $contents = ["", "abc", "cba", "", "abcd"];

        $idx = 0;
        foreach ($contents as $c)
        {
            $idx++;
            $stream = \Flexio\Base\Stream::create();
            $stream->getWriter()->write($c);
            $process_write = \Flexio\Tests\Process::write($process_owner, $filepath, $stream);
            $process_read = \Flexio\Tests\Process::read($process_owner, $filepath);
            $actual = \Flexio\Base\Util::getStreamContents($process_read->getStdout());
            $expected = $c;
            \Flexio\Tests\Check::assertString("E.$idx", 'Read/Write; overwrite check; write/read to/from ' . $filepath, $actual, $expected, $results);
        }



        // TEST: Write/Read Job; Implicit Format Conversion
        $create = json_decode('
        {
            "op": "create",
            "params": {
                "name": "table",
                "content_type": "'.\Flexio\Base\ContentType::FLEXIO_TABLE.'",
                "columns": [
                    { "name": "c1", "type": "character", "width": 3 },
                    { "name": "c2", "type": "character", "width": 20 },
                    { "name": "n1", "type": "numeric", "width": 6, "scale": 2 },
                    { "name": "n2", "type": "double", "width": 10, "scale": 4 },
                    { "name": "n3", "type": "integer" },
                    { "name": "d1", "type": "date" },
                    { "name": "d2", "type": "datetime" },
                    { "name": "b1", "type": "boolean" }
                ],
                "content": [
                    { "c1": "aBC", "c2": "()[]{}<>",       "n1": -1.02, "n2": -1.23, "n3": -1,   "d1": "1776-07-04", "d2": "1776-07-04 01:02:03", "b1": true  },
                    { "c1": "c a", "c2": "| \\/",          "n1": null,  "n2": 0.00,  "n3": 0,    "d1": "1970-11-22", "d2": "1970-11-22 01:02:03", "b1": null  },
                    { "c1": " -1", "c2": ":;\"\'",         "n1": 0.00,  "n2": 0.99,  "n3": 1,    "d1": "1999-12-31", "d2": "1999-12-31 01:02:03", "b1": false },
                    { "c1": "0% ", "c2": ",.?",            "n1": 0.99,  "n2": 4.56,  "n3": 2,    "d1": "2000-01-01", "d2": "2000-01-01 01:02:03", "b1": null  },
                    { "c1": null,  "c2": "~`!@#$%^&*-+_=", "n1": 2.00,  "n2": null,  "n3": null, "d1": null,         "d2": null,                  "b1": true  }
                ]
            }
        }
        ',true);
        $filename = \Flexio\Base\Util::generateHandle() . '.csv';
        $filepath = $folderpath . '/' . $filename;
        $read = \Flexio\Tests\Task::create([["op" => "read", "path" => $filepath]]);
        $write = \Flexio\Tests\Task::create([["op" => "write", "path" => $filepath]]);
        $process_write = \Flexio\Jobs\Process::create()->execute($create)->execute($write);
        $process_read = \Flexio\Jobs\Process::create()->execute($read);
        $actual_contents = \Flexio\Base\Util::getStreamContents($process_read->getStdout());
        $expected_contents = <<<EOD
c1,c2,n1,n2,n3,d1,d2,b1
aBC,()[]{}<>,-1.02,-1.23,-1,1776-07-04,"1776-07-04 01:02:03",true
"c a","| /",,0,0,1970-11-22,"1970-11-22 01:02:03",
" -1",":;""'",0,0.99,1,1999-12-31,"1999-12-31 01:02:03",false
"0% ",",.?",0.99,4.56,2,2000-01-01,"2000-01-01 01:02:03",
,~`!@#$%^&*-+_=,2,,,,,true

EOD;
        $actual = $actual_contents;
        $expected = $expected_contents;
        \Flexio\Tests\Check::assertString("F.1", 'Read/Write; check write/read with implicit type conversion; file output here: ' . $filepath, $actual, $expected, $results);
    }
}
