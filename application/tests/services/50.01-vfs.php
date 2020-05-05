<?php
/**
 *
 * Copyright (c) 2017, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-11-11
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
        // SETUP
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


        // SETUP
        $test_user_eid = \Flexio\Tests\Util::getTestStorageOwner();
        $service = new \Flexio\Services\Vfs($test_user_eid);


        // TEST: service creation

        // BEGIN TEST
        $instance = new \Flexio\Services\Vfs($test_user_eid);
        $actual = get_class($instance);
        $expected = 'Flexio\Services\Vfs';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Services\Vfs; basic file syntax check',  $actual, $expected, $results);


        // TEST: basic service functions

        // BEGIN TEST
        $idx = 0;
        foreach ($storage_items as $storage_location)
        {
            $foldername = \Flexio\Tests\Util::getTimestampName();
            $folderpath = "$storage_location:/service-tests/$foldername/";
            $filename = \Flexio\Base\Util::generateHandle() . '.txt';
            $filepath = $folderpath . $filename;

            $service->createDirectory($folderpath);
            $service->createFile($filepath);

            // check file info on a path that doesn't exist
            $idx++;
            try
            {
                $service->getFileInfo(\Flexio\Base\Util::generateHandle()); // name outside storage namespace
                $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
                $expected = 'Exception: ' . \Flexio\Base\Error::UNAVAILABLE;
                \Flexio\Tests\Check::assertString("B.$idx", '\Flexio\Services\Vfs::getFileInfo(); file path check on folder that doesn\'t exist should throw an exception' . $storage_location,  $actual, $expected, $results);
            }
            catch (\Flexio\Base\Exception $e)
            {
                $error = json_decode($e->getMessage(),true);
                $actual = $error['code'];
                $expected = \Flexio\Base\Error::UNAVAILABLE;
                \Flexio\Tests\Check::assertString("B.$idx", '\Flexio\Services\Vfs::getFileInfo(); check exception code' . $storage_location,  $actual, $expected, $results);
            }

            // check file info on a path that doesn't exist within a particular service
            $idx++;
            try
            {
                $service->getFileInfo($folderpath . \Flexio\Base\Util::generateHandle() . '.txt'); // name within storage namespace
                $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
                $expected = 'Exception: ' . \Flexio\Base\Error::UNAVAILABLE;
                \Flexio\Tests\Check::assertString("B.$idx", '\Flexio\Services\Vfs::getFileInfo(); file path check on folder that doesn\'t exist should throw an exception' . $storage_location,  $actual, $expected, $results);
            }
            catch (\Flexio\Base\Exception $e)
            {
                $error = json_decode($e->getMessage(),true);
                $actual = $error['code'];
                $expected = \Flexio\Base\Error::UNAVAILABLE;
                \Flexio\Tests\Check::assertString("B.$idx", '\Flexio\Services\Vfs::getFileInfo(); check exception code' . $storage_location,  $actual, $expected, $results);
            }

            // check file info on a directory that exists
            $idx++;
            try
            {
                $actual = $service->getFileInfo($folderpath);
                $expected = array('name' => $foldername, 'type' => 'DIR');
                \Flexio\Tests\Check::assertInArray("B.$idx", '\Flexio\Services\Vfs::getFileInfo(); basic file info check on: ' . $storage_location,  $actual, $expected, $results);
            }
            catch (\Flexio\Base\Exception $e)
            {
                $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
                $expected = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
                \Flexio\Tests\Check::assertString("B.$idx", '\Flexio\Services\Vfs::getFileInfo(); basic file info check on: ' . $storage_location,  $actual, $expected, $results);
            }

            // check file info on a file that exists
            $idx++;
            try
            {
                $actual = $service->getFileInfo($filepath);
                $expected = array('name' => $filename, 'type' => 'FILE');
                \Flexio\Tests\Check::assertInArray("B.$idx", '\Flexio\Services\Vfs::getFileInfo(); basic file info check on: ' . $storage_location,  $actual, $expected, $results);
            }
            catch (\Flexio\Base\Exception $e)
            {
                $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
                $expected = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
                \Flexio\Tests\Check::assertString("B.$idx", '\Flexio\Services\Vfs::getFileInfo(); basic file info check on: ' . $storage_location,  $actual, $expected, $results);
            }
        }
    }
}
