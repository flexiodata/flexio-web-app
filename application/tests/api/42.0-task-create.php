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
        $storage_items = [
            \Flexio\Tests\Base::STORAGE_FLEX,
            \Flexio\Tests\Base::STORAGE_AMAZONS3,
            \Flexio\Tests\Base::STORAGE_BOX,
            \Flexio\Tests\Base::STORAGE_DROPBOX,
            \Flexio\Tests\Base::STORAGE_GITHUB,
            \Flexio\Tests\Base::STORAGE_GOOGLEDRIVE,
            //\Flexio\Tests\Base::STORAGE_GOOGLECLOUDSTORAGE,
            \Flexio\Tests\Base::STORAGE_SFTP
        ];


        // TEST: Create; create a file with no content

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
            $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
            $result = json_decode($result['response'],true);
            $actual = array_column($result, 'name');
            sort($actual);
            $expected = [$filename];
            \Flexio\Tests\Check::assertInArray("A.$idx", 'Create; create a file with no content; file should be ' . $filename, $actual, $expected, $results);
        }


        // TEST: Create; make sure creating a file doesn't overwrite a folder of the same name

        // BEGIN TEST
        $idx = 0;
        foreach ($storage_items as $storage_location)
        {
            $idx++;
            $folderpath = "$storage_location:/job-tests-" . \Flexio\Tests\Util::getTimestampName() . "/test_folder/";
            $filename = \Flexio\Base\Util::generateHandle() . '.txt';
            $task = \Flexio\Tests\Task::create([
                ["op" => "create", "path" => $folderpath.$filename],
                ["op" => "list", "path" => $folderpath.$filename]
            ]);
            $result1 = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
            $actual1 = $result1['code'];
            $task = \Flexio\Tests\Task::create([
                ["op" => "create", "path" => $folderpath],
                ["op" => "list", "path" => $folderpath]
            ]);
            $result2 = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $task);
            $actual2 = $result2['code'];
            $actual = $actual1 === 200 && $actual2 === 422;
            $expected = true;
            \Flexio\Tests\Check::assertString("A.$idx", 'Create; throw an exception when attempting to create a file with the same name as a folder', $actual, $expected, $results);
        }
    }
}

