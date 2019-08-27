<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-10-13
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
        if (\Flexio\Tests\Base::TEST_STORAGE_GITHUB === false)
            return;


        // SETUP
        $foldername = \Flexio\Tests\Util::getTimestampName();
        $folderpath = "/service-tests/$foldername/";
        $filename = \Flexio\Base\Util::generateHandle() . '.txt';
        $filepath = $folderpath . $filename;

        $service = new \Flexio\Services\Github;
        $service->createDirectory($folderpath);
        $service->createFile($filepath);


        // TEST: service creation

        // BEGIN TEST
        $actual = get_class($service);
        $expected = 'Flexio\Services\Github';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Services\Github::create(); basic file syntax check',  $actual, $expected, $results);


        // TEST: basic service functions

        // BEGIN TEST
        try
        {
            $service->getFileInfo(\Flexio\Base\Util::generateHandle()); // name outside storage namespace
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
            $expected = 'Exception: ' . \Flexio\Base\Error::UNAVAILABLE;
            \Flexio\Tests\Check::assertString("B.1", '\Flexio\Services\Github::getFileInfo(); file path check on folder that doesn\'t exist should throw an exception' . $storage_location,  $actual, $expected, $results);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $error = json_decode($e->getMessage(),true);
            $actual = $error['code'];
            $expected = \Flexio\Base\Error::UNAVAILABLE;
            \Flexio\Tests\Check::assertString("B.1", '\Flexio\Services\Github::getFileInfo(); check exception code' . $storage_location,  $actual, $expected, $results);
        }

        // BEGIN TEST
        try
        {
            $service->getFileInfo($folderpath . \Flexio\Base\Util::generateHandle() . '.txt'); // name within storage namespace
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
            $expected = 'Exception: ' . \Flexio\Base\Error::UNAVAILABLE;
            \Flexio\Tests\Check::assertString("B.2", '\Flexio\Services\Github::getFileInfo(); file path check on folder that doesn\'t exist should throw an exception' . $storage_location,  $actual, $expected, $results);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $error = json_decode($e->getMessage(),true);
            $actual = $error['code'];
            $expected = \Flexio\Base\Error::UNAVAILABLE;
            \Flexio\Tests\Check::assertString("B.2", '\Flexio\Services\Github::getFileInfo(); check exception code' . $storage_location,  $actual, $expected, $results);
        }

        // BEGIN TEST
        try
        {
            $actual = $service->getFileInfo($folderpath);
            $expected = array('name' => $foldername, 'type' => 'DIR');
            \Flexio\Tests\Check::assertInArray("B.3", '\Flexio\Services\Github::getFileInfo(); basic file info check on: ' . $storage_location,  $actual, $expected, $results);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
            $expected = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
            \Flexio\Tests\Check::assertString("B.3", '\Flexio\Services\Github::getFileInfo(); basic file info check on: ' . $storage_location,  $actual, $expected, $results);
        }

        // BEGIN TEST
        try
        {
            $actual = $service->getFileInfo($filepath);
            $expected = array('name' => $filename, 'type' => 'FILE');
            \Flexio\Tests\Check::assertInArray("B.4", '\Flexio\Services\Github::getFileInfo(); basic file info check on: ' . $storage_location,  $actual, $expected, $results);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
            $expected = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
            \Flexio\Tests\Check::assertString("B.4", '\Flexio\Services\Github::getFileInfo(); basic file info check on: ' . $storage_location,  $actual, $expected, $results);
        }
    }
}
