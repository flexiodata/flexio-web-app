<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
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
        // TODO: add additional tests

        // TEST: service creation

        // BEGIN TEST
        $service = new \Flexio\Services\Vfs;
        $actual = get_class($service);
        $expected = 'Flexio\Services\Vfs';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Services\Vfs; basic file syntax check',  $actual, $expected, $results);



        // TEST: basic service functions

        // BEGIN TEST

        $services = [
            \Flexio\Tests\Base::STORAGE_LOCAL,
            \Flexio\Tests\Base::STORAGE_DROPBOX,
            \Flexio\Tests\Base::STORAGE_GOOGLEDRIVE,
            \Flexio\Tests\Base::STORAGE_AMAZONS3,
            \Flexio\Tests\Base::STORAGE_BOX,
            \Flexio\Tests\Base::STORAGE_SFTP,
        ];

        $idx = 0;
        foreach ($services as $s)
        {
            $foldername = \Flexio\Tests\Util::getTimestampName();
            $folderpath = "/$s/service-tests/$foldername/";
            $filename = \Flexio\Base\Util::generateHandle() . '.txt';
            $filepath = $folderpath . $filename;

            $vfs = new \Flexio\Services\Vfs();
            $vfs->createDirectory($folderpath);
            $vfs->createFile($filepath);

            // check file info on a path that doesn't exist
            $idx++;
            try
            {
                $vfs->getFileInfo(\Flexio\Base\Util::generateHandle()); // name outside storage namespace
                $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
                $expected = 'Exception: ' . \Flexio\Base\Error::NOT_FOUND;
                \Flexio\Tests\Check::assertString("B.$idx", '\Flexio\Services\Vfs::getFileInfo(); file path check on folder that doesn\'t exist should throw an exception' . $s,  $actual, $expected, $results);
            }
            catch (\Flexio\Base\Exception $e)
            {
                $error = json_decode($e->getMessage(),true);
                $actual = $error['code'];
                $expected = \Flexio\Base\Error::NOT_FOUND;
                \Flexio\Tests\Check::assertString("B.$idx", '\Flexio\Services\Vfs::getFileInfo(); check exception code' . $s,  $actual, $expected, $results);
            }

            // check file info on a path that doesn't exist within a particular service
            $idx++;
            try
            {
                $vfs->getFileInfo($folderpath . \Flexio\Base\Util::generateHandle() . '.txt'); // name within storage namespace
                $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
                $expected = 'Exception: ' . \Flexio\Base\Error::NOT_FOUND;
                \Flexio\Tests\Check::assertString("B.$idx", '\Flexio\Services\Vfs::getFileInfo(); file path check on folder that doesn\'t exist should throw an exception' . $s,  $actual, $expected, $results);
            }
            catch (\Flexio\Base\Exception $e)
            {
                $error = json_decode($e->getMessage(),true);
                $actual = $error['code'];
                $expected = \Flexio\Base\Error::NOT_FOUND;
                \Flexio\Tests\Check::assertString("B.$idx", '\Flexio\Services\Vfs::getFileInfo(); check exception code' . $s,  $actual, $expected, $results);
            }

            // check file info on a directory that exists
            $idx++;
            try
            {
                $actual = $vfs->getFileInfo($folderpath);
                $expected = array('name' => $foldername, 'type' => 'DIR');
                \Flexio\Tests\Check::assertInArray("B.$idx", '\Flexio\Services\Vfs::getFileInfo(); basic file info check on: ' . $s,  $actual, $expected, $results);
            }
            catch (\Flexio\Base\Exception $e)
            {
                $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
                $expected = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
                \Flexio\Tests\Check::assertString("B.$idx", '\Flexio\Services\Vfs::getFileInfo(); basic file info check on: ' . $s,  $actual, $expected, $results);
            }

            // check file info on a file that exists
            $idx++;
            try
            {
                $actual = $vfs->getFileInfo($filepath);
                $expected = array('name' => $filename, 'type' => 'FILE');
                \Flexio\Tests\Check::assertInArray("B.$idx", '\Flexio\Services\Vfs::getFileInfo(); basic file info check on: ' . $s,  $actual, $expected, $results);
            }
            catch (\Flexio\Base\Exception $e)
            {
                $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
                $expected = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
                \Flexio\Tests\Check::assertString("B.$idx", '\Flexio\Services\Vfs::getFileInfo(); basic file info check on: ' . $s,  $actual, $expected, $results);
            }
        }
    }
}
