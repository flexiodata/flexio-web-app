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

        if (\Flexio\Tests\Base::TEST_STORAGE_FLEX== true)
            $storage_items[] = \Flexio\Tests\Base::STORAGE_FLEX;
        if (\Flexio\Tests\Base::TEST_STORAGE_AMAZONS3=== true)
            $storage_items[] = \Flexio\Tests\Base::STORAGE_AMAZONS3;
        if (\Flexio\Tests\Base::TEST_STORAGE_BOX === true)
            $storage_items[] = \Flexio\Tests\Base::STORAGE_BOX;
        if (\Flexio\Tests\Base::TEST_STORAGE_DROPBOX === true)
            $storage_items[] = \Flexio\Tests\Base::STORAGE_DROPBOX;
        if (\Flexio\Tests\Base::TEST_STORAGE_GITHUB === true)
            $storage_items[] = \Flexio\Tests\Base::STORAGE_GITHUB;
        if (\Flexio\Tests\Base::TEST_STORAGE_GOOGLEDRIVE === true)
            $storage_items[] = \Flexio\Tests\Base::STORAGE_GOOGLEDRIVE;
        if (\Flexio\Tests\Base::TEST_STORAGE_GOOGLECLOUDSTORAGE=== true)
            $storage_items[] = \Flexio\Tests\Base::STORAGE_GOOGLECLOUDSTORAGE;
        if (\Flexio\Tests\Base::TEST_STORAGE_SFTP=== true)
            $storage_items[] = \Flexio\Tests\Base::STORAGE_SFTP;


        // TODO: try to delete a file that doesn't exist
        // TODO: delete an empty folder
        // TODO: delete a populated folder


        // TEST: Delete Job; Basic Delete

        // BEGIN TEST
        $idx = 0;
        foreach ($storage_items as $storage_location)
        {
            $idx++;

            $folderpath = "$storage_location:/job-tests-" . \Flexio\Tests\Util::getTimestampName() . "/";
            $filename = \Flexio\Base\Util::generateHandle() . '.txt';
            $task = \Flexio\Tests\Task::create([
                ["op" => "create", "path" => $folderpath.$filename],
                ["op" => "list", "path" => $folderpath.$filename]
            ]);
            $result1 = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
            $result1 = json_decode($result1['response'], true);
            $result1 = array_column($result1, 'name');
            $task = \Flexio\Tests\Task::create([
                ["op" => "delete", "path" => $folderpath.$filename],
                ["op" => "list", "path" => $folderpath.$filename]
            ]);
            $result2 = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
            $result2 = json_decode($result2['response'], true);
            $result2 = array_column($result2, 'name');
            $actual = array_values(array_diff($result1, $result2));
            $expected = [$filename];
            \Flexio\Tests\Check::assertArray("G.1", 'Delete; delete a file that exists; file is ' . $folderpath.$filename, $actual, $expected, $results);
        }
    }
}

