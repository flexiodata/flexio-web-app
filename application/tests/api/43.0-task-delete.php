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


        // TODO: try to delete a file that doesn't exist
        // TODO: delete an empty folder
        // TODO: delete a populated folder


        // TEST: Delete Job; Basic Delete

        // BEGIN TEST
        $idx = 0;
        foreach ($storage_items as $storage_location)
        {
            $idx++;

            $folderpath = "/$storage_location/job-tests-" . \Flexio\Tests\Util::getTimestampName() . "/";
            $filename = \Flexio\Base\Util::generateHandle() . '.txt';
            $tasks = json_decode('[
                {"op": "create", "params": {"path": "'.$folderpath.$filename.'"}},
                {"op": "list", "params": {"path": "'.$folderpath.$filename.'"}}
            ]',true);
            $result1 = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $tasks);
            $result1 = json_decode($result1['response'], true);
            $result1 = array_column($result1, 'name');
            $tasks = json_decode('[
                {"op": "delete", "params": {"path": "'.$folderpath.$filename.'"}},
                {"op": "list", "params": {"path": "'.$folderpath.$filename.'"}}
            ]',true);
            $result2 = \Flexio\Tests\Util::runProcess($apibase, $userid, $token, $tasks);
            $result2 = json_decode($result2['response'], true);
            $result2 = array_column($result2, 'name');
            $actual = array_values(array_diff($result1, $result2));
            $expected = [$filename];
            \Flexio\Tests\Check::assertArray("G.1", 'Delete; delete a file that exists; file is ' . $folderpath.$filename, $actual, $expected, $results);
        }
    }
}

