<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
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
        $storage_items[] = \Flexio\Tests\Base::STORAGE_FLEX;
        if (\Flexio\Tests\Base::TEST_EXTERNAL_STORAGE === true)
        {
            $storage_items[] = \Flexio\Tests\Base::STORAGE_AMAZONS3;
            $storage_items[] = \Flexio\Tests\Base::STORAGE_BOX;
            $storage_items[] = \Flexio\Tests\Base::STORAGE_DROPBOX;
            $storage_items[] = \Flexio\Tests\Base::STORAGE_GITHUB;
            $storage_items[] = \Flexio\Tests\Base::STORAGE_GOOGLEDRIVE;
            //$storage_items[] = \Flexio\Tests\Base::STORAGE_GOOGLECLOUDSTORAGE;
            $storage_items[] = \Flexio\Tests\Base::STORAGE_SFTP;
        }


        // TEST: listing of a folder with a single file

        // BEGIN TEST
        $idx = 0;
        foreach ($storage_items as $storage_location)
        {
            $idx++;
            $folderpath = "$storage_location:/job-tests-" . \Flexio\Tests\Util::getTimestampName() . "/";
            $filename = \Flexio\Base\Util::generateHandle() . '.txt';
            $filepath = $folderpath . '/' . $filename;
            $task = \Flexio\Tests\Task::create([
                ["op" => "write", "path" => $filepath],
                ["op" => "list", "path" => $folderpath]
            ]);
            $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
            $actual = json_decode($result['response'],true);
            $expected = '[{
                "name":"'.$filename.'",
                "path":"'.$folderpath.$filename.'",
                "type":"FILE"
            }]';
            \Flexio\Tests\Check::assertInArray("A.$idx", 'Process List; ('.$storage_location.') listing of folder with single file ' . $folderpath, $actual, $expected, $results);
        }
    }
}

