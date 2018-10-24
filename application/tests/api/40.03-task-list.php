<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-05-02
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
        // ENDPOINT: POST /:userid/processes/:objeid/run


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/v1';
        $userid = \Flexio\Tests\Util::getTestStorageOwner();
        $token = \Flexio\Tests\Util::getTestStorageOwnerToken();
        $storage_items = [
            \Flexio\Tests\Base::STORAGE_LOCAL,
            \Flexio\Tests\Base::STORAGE_AMAZONS3,
            \Flexio\Tests\Base::STORAGE_BOX,
            \Flexio\Tests\Base::STORAGE_DROPBOX,
            //\Flexio\Tests\Base::STORAGE_GITHUB,
            \Flexio\Tests\Base::STORAGE_GOOGLEDRIVE,
            \Flexio\Tests\Base::STORAGE_GOOGLECLOUDSTORAGE,
            \Flexio\Tests\Base::STORAGE_SFTP
        ];


        // TEST: listing of files using wildcards

        // BEGIN TEST
        $timestamp = \Flexio\Tests\Util::getTimestampName();
        foreach ($storage_items as $storage_location)
        {
            $folderpath = "$storage_location:/job-tests-$timestamp/";
            $task = \Flexio\Tests\Task::create([
                ["op" => "write", "path" => $folderpath."file1.txt"],
                ["op" => "write", "path" => $folderpath."file2.csv"],
                ["op" => "write", "path" => $folderpath."file3.png"],
                ["op" => "write", "path" => $folderpath."file4.jpg"],
                ["op" => "write", "path" => $folderpath."file5.csv"],
            ]);
            $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
        }

        $tests = [
            ["pattern" => "/*.txt",       "expected" => ["file1.txt"]],
            ["pattern" => "/*.csv",       "expected" => ["file2.csv","file5.csv"]],
            ["pattern" => "/*.png",       "expected" => ["file3.png"]],
            ["pattern" => "/*.jpg",       "expected" => ["file4.jpg"]],
            ["pattern" => "/*.xls",       "expected" => []],
            ["pattern" => "/file1.*",     "expected" => ["file1.txt"]],
            ["pattern" => "/file2.*",     "expected" => ["file2.csv"]],
            ["pattern" => "/file3.*",     "expected" => ["file3.png"]],
            ["pattern" => "/file4.*",     "expected" => ["file4.jpg"]],
            ["pattern" => "/file5.*",     "expected" => ["file5.csv"]],
            ["pattern" => "/file*.CSV",   "expected" => ["file2.csv", "file5.csv"]],
            ["pattern" => "/file1.*",     "expected" => ["file1.txt"]],
            ["pattern" => "/file[2-3].*", "expected" => ["file2.csv", "file3.png"]],
            ["pattern" => "/file[0-9].*", "expected" => ["file1.txt","file2.csv","file3.png","file4.jpg","file5.csv"]]
        ];

        $idx = 0;
        foreach ($storage_items as $storage_location)
        {
            foreach ($tests as $t)
            {
                $idx++;
                $folderpath = "$storage_location:/job-tests-$timestamp/";
                $task = \Flexio\Tests\Task::create([
                    ["op" => "list", "path" => $folderpath.$t['pattern']]
                ]);
                $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
                $result = json_decode($result['response'],true);
                $actual = array_column($result, 'name');
                sort($actual);
                $expected = $t['expected'];
                \Flexio\Tests\Check::assertArray("A.$idx", 'Process List; ('.$storage_location.') listing of files using wildcards' . $folderpath, $actual, $expected, $results);
            }
        }
    }
}

