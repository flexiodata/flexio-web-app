<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
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
        $storage_items[] = \Flexio\Tests\Base::STORAGE_FLEX;
        if (\Flexio\Tests\Base::TEST_EXTERNAL_STORAGE === true)
        {
            $storage_items[] = \Flexio\Tests\Base::STORAGE_AMAZONS3;
            $storage_items[] = \Flexio\Tests\Base::STORAGE_BOX;
            $storage_items[] = \Flexio\Tests\Base::STORAGE_DROPBOX;
            //$storage_items[] = \Flexio\Tests\Base::STORAGE_GITHUB;
            $storage_items[] = \Flexio\Tests\Base::STORAGE_GOOGLEDRIVE;
            //$storage_items[] = \Flexio\Tests\Base::STORAGE_GOOGLECLOUDSTORAGE;
            $storage_items[] = \Flexio\Tests\Base::STORAGE_SFTP;
        }


        // TEST: service creation

        // BEGIN TEST
        $test_user_eid = \Flexio\Tests\Util::getTestStorageOwner();
        $service = new \Flexio\Services\Vfs($test_user_eid);
        $actual = get_class($service);
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

            $vfs = new \Flexio\Services\Vfs($test_user_eid);
            $vfs->createDirectory($folderpath);
            $vfs->createFile($filepath);

            // check file info on a path that doesn't exist
            $idx++;
            try
            {
                $vfs->getFileInfo(\Flexio\Base\Util::generateHandle()); // name outside storage namespace
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
                $vfs->getFileInfo($folderpath . \Flexio\Base\Util::generateHandle() . '.txt'); // name within storage namespace
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
                $actual = $vfs->getFileInfo($folderpath);
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
                $actual = $vfs->getFileInfo($filepath);
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
