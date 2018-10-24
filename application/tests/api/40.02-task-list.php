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
            \Flexio\Tests\Base::STORAGE_FLEX,
            \Flexio\Tests\Base::STORAGE_AMAZONS3,
            \Flexio\Tests\Base::STORAGE_BOX,
            \Flexio\Tests\Base::STORAGE_DROPBOX,
            \Flexio\Tests\Base::STORAGE_GITHUB,
            \Flexio\Tests\Base::STORAGE_GOOGLEDRIVE,
            \Flexio\Tests\Base::STORAGE_GOOGLECLOUDSTORAGE,
            \Flexio\Tests\Base::STORAGE_SFTP
        ];


        // TEST: listing of a file within a folder with multiple files

        // BEGIN TEST
        $idx = 0;
        foreach ($storage_items as $storage_location)
        {
            $idx++;
            $folderpath = "$storage_location:/job-tests-" . \Flexio\Tests\Util::getTimestampName() . "/";
            $filename1 = \Flexio\Base\Util::generateHandle() . '.txt';
            $filename2 = \Flexio\Base\Util::generateHandle() . '.txt';
            $filepath1 = $folderpath . '/' . $filename1;
            $filepath2 = $folderpath . '/' . $filename2;
            $task = \Flexio\Tests\Task::create([
                ["op" => "write", "path" => $filepath1],
                ["op" => "write", "path" => $filepath2],
                ["op" => "list", "path" => $filepath1]
            ]);
            $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
            $actual = json_decode($result['response'],true);
            $expected = '[{
                "name":"'.$filename1.'",
                "path":"'.$folderpath.$filename1.'",
                "type":"FILE"
            }]';
            \Flexio\Tests\Check::assertInArray("B.$idx", 'Process List; ('.$storage_location.') listing of a file within a folder with multiple files' . $folderpath, $actual, $expected, $results);
        }
    }
}

