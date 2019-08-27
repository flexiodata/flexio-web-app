<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-01-29
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
        if (\Flexio\Tests\Base::TEST_STORAGE_GOOGLESHEETS === false)
            return;


        // SETUP
        $process_owner = \Flexio\Tests\Util::getTestStorageOwner();
        $files = \Flexio\Tests\Util::getTestDataFolder("text");
        $folderpath = \Flexio\Tests\Base::STORAGE_GOOGLESHEETS . ":/job-tests-" . \Flexio\Tests\Util::getTimestampName() . "-";

/*
        // TEST: Write/Read Job; Basic Copy

        // BEGIN TEST
        $filename = 'sheet_that_does_not_exist';
        $process_read = \Flexio\Tests\Process::read($process_owner, $folderpath . '-' . $filename);
        $actual = $process_read->hasError();
        $expected = true;
        \Flexio\Tests\Check::assertBoolean("D.1", 'Read; throw an exception when attempting to read from a file that doesn\'t exist.', $actual, $expected, $results);
*/

        // BEGIN TEST
        $idx = 1;
        foreach ($files as $filename)
        {
            if (strpos($filename, 'empty') !== false)
                continue;
            if (strpos($filename, 'content') === false)
                continue;

            // google sheets cannot stored null values
            if (strpos($filename, 'content-null') !== false)
                continue;

            $idx++;

            //if ($idx > 3)
            //    continue;

            $stream = \Flexio\Tests\Util::createStreamFromFile($filename);

            $filepath = \Flexio\Tests\Util::getOutputFilePath($folderpath, $filename);
            $filepath = str_replace('.tsv', '', $filepath);


            $process = \Flexio\Jobs\Process::create()->setOwner($process_owner);
            $process->setStdin($stream);
            $process->execute([ "op" => "convert", "input" => [ "format" => "delimited", "header" => false, "delimiter" => "{tab}", "qualifier" => '"' ], "output" => "table" ]);
            $expected_contents = \Flexio\Base\StreamUtil::getStreamContents($process->getStdout());
            if (is_array($expected_contents))
            {
                foreach ($expected_contents as &$row)
                    $row = array_values($row);
            }
            $process->execute([ "op" => "write", "path" => $filepath ]);

            $process = \Flexio\Jobs\Process::create()->setOwner($process_owner);
            $process->execute([ "op" => "read", "path" => $filepath ]);
            $actual_contents = \Flexio\Base\StreamUtil::getStreamContents($process->getStdout());
            if (is_array($actual_contents))
            {
                foreach ($actual_contents as &$row)
                    $row = array_values($row);
            }

            $actual = json_encode($actual_contents);
            $expected = json_encode($expected_contents);

            \Flexio\Tests\Check::assertString("D.$idx", 'Read/Write; check write/read to/from ' . $filepath, $actual, $expected, $results);
        }


/*
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
            $actual = \Flexio\Base\StreamUtil::getStreamContents($process_read->getStdout());
            $expected = $c;
            \Flexio\Tests\Check::assertString("E.$idx", 'Read/Write; overwrite check; write/read to/from ' . $filepath, $actual, $expected, $results);
        }
*/
    }
}
