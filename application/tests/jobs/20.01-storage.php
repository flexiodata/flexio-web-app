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
        $storage_paths = self::getStoragePaths();
        $data_strings = self::getDataStrings();

        $idx = 0;
        foreach ($storage_paths as $path_info)
        {
            foreach ($data_strings as $data_info)
            {
                $idx++;

                $read = json_decode('
                {
                    "op": "read",
                    "params": {
                        "path": "'. $path_info['path'] . '"
                    }
                }
                ',true);

                $write = json_decode('
                {
                    "op": "write",
                    "params": {
                        "path": "'. $path_info['path'] . '"
                    }
                }
                ',true);

                $stream = \Flexio\Base\Stream::create();
                $stream->getWriter()->write($data_info['data']);
                $process_write = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($write);
                $process_read = \Flexio\Jobs\Process::create()->execute($read);
                $actual = $process_read->getStdout()->getReader()->read();
                $expected = $data_info['data'];
                TestCheck::assertString($path_info['id'] . '.' . $data_info['id'], 'Read/Write; check basic functionality ('. $path_info['path'] .')',  $actual, $expected, $results);
            }
        }
    }

    public function getStoragePaths()
    {
        $paths = array();
        $paths[] = array("id" => "A", "path" => "/storagepath1/file1.txt");
        $paths[] = array("id" => "B", "path" => "/storagepath2/file2.txt");
        return $paths;
    }

    public function getDataStrings()
    {
        $data = array();


// TEST
$d = <<<EOD
EOD;
$data[] = array("id" => 1,"data" => $d);


// TEST
$d = <<<EOD

EOD;
$data[] = array("id" => 2,"data" => $d);


// TEST
$d = <<<EOD

        Test

EOD;
$data[] = array("id" => 2,"data" => $d);


        return $data;
    }
}
