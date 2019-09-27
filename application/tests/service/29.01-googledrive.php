<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-05-16
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
        if (\Flexio\Tests\Base::TEST_SERVICE_GOOGLEDRIVE === false)
            return;


        // SETUP
        $test_user_eid = \Flexio\Tests\Util::getTestStorageOwner();
        $test_connection_eid = \Flexio\Object\Connection::getEidFromName($test_user_eid, \Flexio\Tests\Base::STORAGE_GOOGLEDRIVE);
        $service = \Flexio\Object\Connection::load($test_connection_eid)->getService();

        $foldername = \Flexio\Tests\Util::getTimestampName();
        $folderpath = "/service-tests/$foldername/";
        $filename = \Flexio\Base\Util::generateHandle() . '.txt';
        $filepath = $folderpath . $filename;

        $service->createDirectory($folderpath);
        $service->createFile($filepath);


        // TEST: service creation

        // BEGIN TEST
        $instance = new \Flexio\Services\GoogleDrive;
        $actual = get_class($instance);
        $expected = 'Flexio\Services\GoogleDrive';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Services\GoogleDrive::create(); basic file syntax check',  $actual, $expected, $results);

        // BEGIN TEST
        $instance = new \Flexio\Services\GoogleDrive;
        $actual = ($instance instanceof \Flexio\IFace\IConnection) ? true : false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', 'new \Flexio\Services\GoogleDrive; instance of IConnection ',  $actual, $expected, $results);

        // BEGIN TEST
        $instance = new \Flexio\Services\GoogleDrive;
        $actual = ($instance instanceof \Flexio\IFace\IOAuthConnection) ? true : false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.3', 'new \Flexio\Services\GoogleDrive; instance of IOAuthConnection ',  $actual, $expected, $results);


        // TEST: basic service functions

        // BEGIN TEST
        try
        {
            $service->getFileInfo(\Flexio\Base\Util::generateHandle()); // name outside storage namespace
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
            $expected = 'Exception: ' . \Flexio\Base\Error::UNAVAILABLE;
            \Flexio\Tests\Check::assertString("B.1", '\Flexio\Services\GoogleDrive::getFileInfo(); file path check on folder that doesn\'t exist should throw an exception' . $storage_location,  $actual, $expected, $results);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $error = json_decode($e->getMessage(),true);
            $actual = $error['code'];
            $expected = \Flexio\Base\Error::UNAVAILABLE;
            \Flexio\Tests\Check::assertString("B.1", '\Flexio\Services\GoogleDrive::getFileInfo(); check exception code',  $actual, $expected, $results);
        }

        // BEGIN TEST
        try
        {
            $service->getFileInfo($folderpath . \Flexio\Base\Util::generateHandle() . '.txt'); // name within storage namespace
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
            $expected = 'Exception: ' . \Flexio\Base\Error::UNAVAILABLE;
            \Flexio\Tests\Check::assertString("B.2", '\Flexio\Services\GoogleDrive::getFileInfo(); file path check on folder that doesn\'t exist should throw an exception' . $storage_location,  $actual, $expected, $results);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $error = json_decode($e->getMessage(),true);
            $actual = $error['code'];
            $expected = \Flexio\Base\Error::UNAVAILABLE;
            \Flexio\Tests\Check::assertString("B.2", '\Flexio\Services\GoogleDrive::getFileInfo(); check exception code',  $actual, $expected, $results);
        }

        // BEGIN TEST
        try
        {
            $actual = $service->getFileInfo($folderpath);
            $expected = array('name' => $foldername, 'type' => 'DIR');
            \Flexio\Tests\Check::assertInArray("B.3", '\Flexio\Services\GoogleDrive::getFileInfo(); basic file info check',  $actual, $expected, $results);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
            $expected = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
            \Flexio\Tests\Check::assertString("B.3", '\Flexio\Services\GoogleDrive::getFileInfo(); basic file info check',  $actual, $expected, $results);
        }

        // BEGIN TEST
        try
        {
            $actual = $service->getFileInfo($filepath);
            $expected = array('name' => $filename, 'type' => 'FILE');
            \Flexio\Tests\Check::assertInArray("B.4", '\Flexio\Services\GoogleDrive::getFileInfo(); basic file info check',  $actual, $expected, $results);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
            $expected = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
            \Flexio\Tests\Check::assertString("B.4", '\Flexio\Services\GoogleDrive::getFileInfo(); basic file info check',  $actual, $expected, $results);
        }
    }
}
