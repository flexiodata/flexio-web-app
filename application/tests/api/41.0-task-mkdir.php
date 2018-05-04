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
            \Flexio\Tests\Base::STORAGE_GITHUB,
            \Flexio\Tests\Base::STORAGE_GOOGLEDRIVE,
            \Flexio\Tests\Base::STORAGE_SFTP
        ];


        // TEST: creating an empty folder (no trailing slash after folder name)

        // BEGIN TEST
        $idx = 0;
        foreach ($storage_items as $storage_location)
        {
            $idx++;
            $foldername = 'empty_folder1';
            $folderpath = "/$storage_location/job-tests-" . \Flexio\Tests\Util::getTimestampName() . "/$foldername"; // folder path without trailing slash
            $tasks = json_decode('[
                {"op": "mkdir", "params": {"path": "'.$folderpath.'"}},
                {"op": "list", "params": {"path": "'.$folderpath.'"}}
            ]',true);
            $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $tasks);
            $actual = json_decode($result['response'],true);
            $expected = '[{
                "name":"'.$foldername.'",
                "path":"'.$folderpath.'",
                "type":"DIR"
            }]';
            \Flexio\Tests\Check::assertInArray("A.$idx", 'Process Mkdir; ('.$storage_location.') creating an empty folder (no trailing slash after folder name); folder should be ' . $folderpath, $actual, $expected, $results);
        }


        // TEST: creating an empty folder (trailing slash after folder name)

        // BEGIN TEST
        $idx = 0;
        foreach ($storage_items as $storage_location)
        {
            $idx++;
            $foldername = 'empty_folder1';
            $folderpath = "/$storage_location/job-tests-" . \Flexio\Tests\Util::getTimestampName() . "/$foldername/"; // folder path with trailing slash
            $tasks = json_decode('[
                {"op": "mkdir", "params": {"path": "'.$folderpath.'"}},
                {"op": "list", "params": {"path": "'.$folderpath.'"}}
            ]',true);
            $result = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $tasks);
            $actual = json_decode($result['response'],true);
            $expected = '[{
                "name":"'.$foldername.'",
                "path":"'.$folderpath.'",
                "type":"DIR"
            }]';
            \Flexio\Tests\Check::assertInArray("B.$idx", 'Process Mkdir; ('.$storage_location.') creating an empty folder (trailing slash after folder name); folder should be ' . $folderpath, $actual, $expected, $results);

/*
        // BEGIN TEST
        $foldername = 'empty_folder1';
        $process_create = \Flexio\Tests\Process::mkdir($process_owner, $folderpath . '/' . $foldername . '/'); // folder path with path terminator
        $process_list = \Flexio\Tests\Process::list($process_owner, $folderpath);
        $actual = \Flexio\Tests\Content::getValues($process_list->getStdout(), 'name');
        $expected = [$foldername];
        \Flexio\Tests\Check::assertInArray("B.1", 'Process Mkdir; create an empty folder; folder should be ' . $foldername, $actual, $expected, $results);

        // BEGIN TEST
        $foldername = 'empty_folder2';
        $process_create = \Flexio\Tests\Process::mkdir($process_owner, $folderpath . '/' . $foldername); // folder path without path terminator
        $process_list = \Flexio\Tests\Process::list($process_owner, $folderpath);
        $actual = \Flexio\Tests\Content::getValues($process_list->getStdout(), 'name');
        $expected = [$foldername];
        \Flexio\Tests\Check::assertInArray("B.2", 'Process Mkdir; create an empty folder; folder should be ' . $foldername, $actual, $expected, $results);

        // BEGIN TEST
        $foldername = 'empty_folder3';
        $process_create = \Flexio\Tests\Process::mkdir($process_owner, $folderpath . '/' . $foldername);
        $has_error_after_first_attempt = $process_create->hasError();
        $process_create = \Flexio\Tests\Process::mkdir($process_owner, $folderpath . '/' . $foldername);
        $has_error_after_second_attempt = $process_create->hasError();
        $actual = ($has_error_after_first_attempt === false && $has_error_after_second_attempt === true);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean("B.3", 'Process Mkdir; throw an exception when attempting to create a folder that already exists', $actual, $expected, $results);
*/
        }
    }
}

