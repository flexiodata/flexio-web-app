<?php
/**
 *
 * Copyright (c) 2018, Flex Research LLC. All rights reserved.
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
        // ENDPOINT: POST /:teamid/processes/:objeid/run


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/v1';
        $userid = \Flexio\Tests\Util::getTestStorageOwner();
        $token = \Flexio\Tests\Util::getTestStorageOwnerToken();

        $storage_items = array();

        if (\Flexio\Tests\Base::TEST_SERVICE_AMAZONS3 === true)
            $storage_items[] = \Flexio\Tests\Base::STORAGE_AMAZONS3;
        if (\Flexio\Tests\Base::TEST_SERVICE_BOX === true)
            $storage_items[] = \Flexio\Tests\Base::STORAGE_BOX;
        if (\Flexio\Tests\Base::TEST_SERVICE_DROPBOX === true)
            $storage_items[] = \Flexio\Tests\Base::STORAGE_DROPBOX;
        if (\Flexio\Tests\Base::TEST_SERVICE_GITHUB === true)
            $storage_items[] = \Flexio\Tests\Base::STORAGE_GITHUB;
        if (\Flexio\Tests\Base::TEST_SERVICE_GOOGLEDRIVE === true)
            $storage_items[] = \Flexio\Tests\Base::STORAGE_GOOGLEDRIVE;
        if (\Flexio\Tests\Base::TEST_SERVICE_GOOGLECLOUDSTORAGE === true)
            $storage_items[] = \Flexio\Tests\Base::STORAGE_GOOGLECLOUDSTORAGE;
        if (\Flexio\Tests\Base::TEST_SERVICE_SFTP === true)
            $storage_items[] = \Flexio\Tests\Base::STORAGE_SFTP;


        // TEST: listing of a file within a folder with multiple files

        // BEGIN TEST
        $idx = 0;
        foreach ($storage_items as $storage_location)
        {
            $idx++;
            $folderpath = "/job-tests-" . \Flexio\Tests\Util::getTimestampName() . "/";
            $full_folderpath = "$storage_location:$folderpath";
            $filename1 = \Flexio\Base\Util::generateHandle() . '.txt';
            $filename2 = \Flexio\Base\Util::generateHandle() . '.txt';
            $filepath1 = $full_folderpath . '/' . $filename1;
            $filepath2 = $full_folderpath . '/' . $filename2;
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
                "full_path":"'.$full_folderpath.$filename1.'",
                "type":"FILE"
            }]';
            \Flexio\Tests\Check::assertInArray("B.$idx", 'Process List; ('.$storage_location.') listing of a file within a folder with multiple files' . $folderpath, $actual, $expected, $results);
        }
    }
}

