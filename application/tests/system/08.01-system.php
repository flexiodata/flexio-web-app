<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-01-18
 *
 * @package flexio
 * @subpackage Tests
 */


class Test
{
    public function run(&$results)
    {
        // TEST: system constant tests

        // BEGIN TEST
        $session_version = \System::SESSION_VERSION;
        $actual = is_integer($session_version) && $session_version > 0;
        $expected = true;
        TestCheck::assertBoolean('A.1', 'System class constant',  $actual, $expected, $results);



        // TEST: test for object instantiation

        // BEGIN TEST
        $model = \System::getModel();
        $actual = get_class($model);
        $expected = 'Model';
        TestCheck::assertString('B.1', '\System::getModel(); make sure function returns a Model object',  $actual, $expected, $results);



        // TEST: test for object instantiation

        // BEGIN TEST
        $date = \System::getTimestamp();
        $actual = is_string($date);
        $expected = true;
        TestCheck::assertBoolean('C.1', '\System::getTimestamp(); make sure function returns a string',  $actual, $expected, $results);



        // TEST: test for object instantiation

        // BEGIN TEST
        $file = '';
        $actual = \System::getUpdateVersionFromFilename($file);
        $expected = 0;
        TestCheck::assertNumber('D.1', '\System::getTimestamp(); make sure function returns zero if the file doesn\'t match the pattern',  $actual, $expected, $results);

        $file = 'update';
        $actual = \System::getUpdateVersionFromFilename($file);
        $expected = 0;
        TestCheck::assertNumber('D.2', '\System::getTimestamp(); make sure function returns zero if the file doesn\'t match the pattern',  $actual, $expected, $results);

        $file = 'update.php';
        $actual = \System::getUpdateVersionFromFilename($file);
        $expected = 0;
        TestCheck::assertNumber('D.3', '\System::getTimestamp(); make sure function returns zero if the file doesn\'t match the pattern',  $actual, $expected, $results);

        $file = 'badprefix1';
        $actual = \System::getUpdateVersionFromFilename($file);
        $expected = 0;
        TestCheck::assertNumber('D.4', '\System::getTimestamp(); return the numeric portion of the file matching the pattern',  $actual, $expected, $results);

        $file = 'update1';
        $actual = \System::getUpdateVersionFromFilename($file);
        $expected = 1;
        TestCheck::assertNumber('D.5', '\System::getTimestamp(); return the numeric portion of the file matching the pattern',  $actual, $expected, $results);

        $file = 'update1.php';
        $actual = \System::getUpdateVersionFromFilename($file);
        $expected = 1;
        TestCheck::assertNumber('D.6', '\System::getTimestamp(); return the numeric portion of the file matching the pattern',  $actual, $expected, $results);

        $file = 'update0001.php';
        $actual = \System::getUpdateVersionFromFilename($file);
        $expected = 1;
        TestCheck::assertNumber('D.7', '\System::getTimestamp(); return the numeric portion of the file matching the pattern',  $actual, $expected, $results);

        $file = 'update0001_name.php';
        $actual = \System::getUpdateVersionFromFilename($file);
        $expected = 1;
        TestCheck::assertNumber('D.8', '\System::getTimestamp(); return the numeric portion of the file matching the pattern',  $actual, $expected, $results);

        $file = 'update1001_name.php';
        $actual = \System::getUpdateVersionFromFilename($file);
        $expected = 1001;
        TestCheck::assertNumber('D.9', '\System::getTimestamp(); return the numeric portion of the file matching the pattern',  $actual, $expected, $results);

        $file = 'update9999_name.php';
        $actual = \System::getUpdateVersionFromFilename($file);
        $expected = 9999;
        TestCheck::assertNumber('D.10', '\System::getTimestamp(); return the numeric portion of the file matching the pattern',  $actual, $expected, $results);



        // TEST: test for object instantiation

        // BEGIN TEST
        $version = \System::getLatestVersionNumber();
        $actual = is_integer($version);
        $expected = true;
        TestCheck::assertBoolean('E.1', '\System::getLatestVersionNumber(); make sure function returns an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $version = \System::getLatestVersionNumber();
        $actual = intval($version) > 0;
        $expected = true;
        TestCheck::assertBoolean('E.2', '\System::getLatestVersionNumber(); make sure function returns an integer that\'s positive',  $actual, $expected, $results);
    }
}
