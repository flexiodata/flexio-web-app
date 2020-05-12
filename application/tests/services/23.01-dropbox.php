<?php
/**
 *
 * Copyright (c) 2016, Flex Research LLC. All rights reserved.
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
        if (\Flexio\Tests\Base::TEST_SERVICE_DROPBOX === false)
            return;


        // SETUP
        $test_user_eid = \Flexio\Tests\Util::getTestStorageOwner();
        $test_connection_eid = \Flexio\Object\Connection::getEidFromName($test_user_eid, \Flexio\Tests\Base::STORAGE_DROPBOX);
        $service = \Flexio\Object\Connection::load($test_connection_eid)->getService();

        $foldername = \Flexio\Tests\Util::getTimestampName();
        $folderpath = "/service-tests/$foldername/";
        $filename = \Flexio\Base\Util::generateHandle() . '.txt';
        $filepath = $folderpath . $filename;

        $service->createDirectory($folderpath);
        $service->createFile($filepath);


        // TEST: service creation

        // BEGIN TEST
        $instance = new \Flexio\Services\Dropbox;
        $actual = get_class($instance);
        $expected = 'Flexio\Services\Dropbox';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Services\Dropbox::create(); basic file syntax check',  $actual, $expected, $results);

        // BEGIN TEST
        $instance = new \Flexio\Services\Dropbox;
        $actual = ($instance instanceof \Flexio\IFace\IConnection) ? true : false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', 'new \Flexio\Services\Dropbox; instance of IConnection ',  $actual, $expected, $results);

        // BEGIN TEST
        $instance = new \Flexio\Services\Dropbox;
        $actual = ($instance instanceof \Flexio\IFace\IOAuthConnection) ? true : false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.3', 'new \Flexio\Services\Dropbox; instance of IOAuthConnection ',  $actual, $expected, $results);


        // TEST: getFileInfo()

        // BEGIN TEST
        try
        {
            $service->getFileInfo(\Flexio\Base\Util::generateHandle()); // name outside storage namespace
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
            $expected = 'Exception: ' . \Flexio\Base\Error::UNAVAILABLE;
            \Flexio\Tests\Check::assertString("B.1", '\Flexio\Services\Dropbox::getFileInfo(); file path check on folder that doesn\'t exist should throw an exception' . $storage_location,  $actual, $expected, $results);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $error = json_decode($e->getMessage(),true);
            $actual = $error['code'];
            $expected = \Flexio\Base\Error::UNAVAILABLE;
            \Flexio\Tests\Check::assertString("B.1", '\Flexio\Services\Dropbox::getFileInfo(); check exception code',  $actual, $expected, $results);
        }

        // BEGIN TEST
        try
        {
            $service->getFileInfo($folderpath . \Flexio\Base\Util::generateHandle() . '.txt'); // name within storage namespace
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
            $expected = 'Exception: ' . \Flexio\Base\Error::UNAVAILABLE;
            \Flexio\Tests\Check::assertString("B.2", '\Flexio\Services\Dropbox::getFileInfo(); file path check on folder that doesn\'t exist should throw an exception' . $storage_location,  $actual, $expected, $results);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $error = json_decode($e->getMessage(),true);
            $actual = $error['code'];
            $expected = \Flexio\Base\Error::UNAVAILABLE;
            \Flexio\Tests\Check::assertString("B.2", '\Flexio\Services\Dropbox::getFileInfo(); check exception code',  $actual, $expected, $results);
        }

        // BEGIN TEST
        try
        {
            $actual = $service->getFileInfo($folderpath);
            $expected = array('name' => $foldername, 'type' => 'DIR');
            \Flexio\Tests\Check::assertInArray("B.3", '\Flexio\Services\Dropbox::getFileInfo(); basic file info check',  $actual, $expected, $results);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
            $expected = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
            \Flexio\Tests\Check::assertString("B.3", '\Flexio\Services\Dropbox::getFileInfo(); basic file info check',  $actual, $expected, $results);
        }

        // BEGIN TEST
        try
        {
            $actual = $service->getFileInfo($filepath);
            $expected = array('name' => $filename, 'type' => 'FILE');
            \Flexio\Tests\Check::assertInArray("B.4", '\Flexio\Services\Dropbox::getFileInfo(); basic file info check',  $actual, $expected, $results);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
            $expected = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
            \Flexio\Tests\Check::assertString("B.4", '\Flexio\Services\Dropbox::getFileInfo(); basic file info check',  $actual, $expected, $results);
        }


        // TEST: read()/write()

        // BEGIN TEST
        try
        {
            $input = <<<EOD
c1,c2,n1,n2,n3,d1,d2,b1
aBC,()[]{}<>,-1.02,-1.23,-1,1776-07-04,"1776-07-04 01:02:03",true
"c a","| /",,0,0,1970-11-22,"1970-11-22 01:02:03",
" -1",":;""'",0,0.99,1,1999-12-31,"1999-12-31 01:02:03",false
"0% ",",.?",0.99,4.56,2,2000-01-01,"2000-01-01 01:02:03",
,~`!@#$%^&*-+_=,2,,,,,true
EOD;

            // write the content
            $stream = fopen('php://memory','r+');
            fwrite($stream, $input);
            rewind($stream);

            $done = false;
            $service->write(['path' => $filepath], function($len) use (&$stream, &$done) {
                if ($done)
                    return false;
                $buf = fread($stream, $len);
                if (strlen($buf) != $len)
                    $done = true;
                return $buf;
            });

            fclose($stream);

            // read the content
            $output = '';
            $service->read(['path' => $filepath], function($data) use (&$output) {
                $output .= $data;
            });

            // check that input/output are the same
            $actual = $output;
            $expected = $input;
            \Flexio\Tests\Check::assertString("C.1", '\Flexio\Services\AmazonS3; read()/write() check',  $actual, $expected, $results);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
            $expected = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
            \Flexio\Tests\Check::assertString("C.1", '\Flexio\Services\AmazonS3; read()/write() check',  $actual, $expected, $results);
        }
    }
}
