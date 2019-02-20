<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-01-18
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
        // TEST: system constant tests

        // BEGIN TEST
        $session_version = \Flexio\System\System::SESSION_VERSION;
        $actual = is_integer($session_version) && $session_version > 0;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', 'System class constant',  $actual, $expected, $results);



        // TEST: test for object instantiation

        // BEGIN TEST
        $model = \Flexio\System\System::getModel();
        $actual = get_class($model);
        $expected = 'Model';
        \Flexio\Tests\Check::assertString('B.1', '\Flexio\System\System::getModel(); make sure function returns a Model object',  $actual, $expected, $results);



        // TEST: test for object instantiation

        // BEGIN TEST
        $date = \Flexio\System\System::getTimestamp();
        $actual = is_string($date);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.1', '\Flexio\System\System::getTimestamp(); make sure function returns a string',  $actual, $expected, $results);



        // TEST: test for object instantiation

        // BEGIN TEST
        $file = '';
        $actual = \Flexio\System\System::getUpdateVersionFromFilename($file);
        $expected = 0;
        \Flexio\Tests\Check::assertNumber('D.1', '\Flexio\System\System::getTimestamp(); make sure function returns zero if the file doesn\'t match the pattern',  $actual, $expected, $results);

        $file = 'update';
        $actual = \Flexio\System\System::getUpdateVersionFromFilename($file);
        $expected = 0;
        \Flexio\Tests\Check::assertNumber('D.2', '\Flexio\System\System::getTimestamp(); make sure function returns zero if the file doesn\'t match the pattern',  $actual, $expected, $results);

        $file = 'update.php';
        $actual = \Flexio\System\System::getUpdateVersionFromFilename($file);
        $expected = 0;
        \Flexio\Tests\Check::assertNumber('D.3', '\Flexio\System\System::getTimestamp(); make sure function returns zero if the file doesn\'t match the pattern',  $actual, $expected, $results);

        $file = 'badprefix1';
        $actual = \Flexio\System\System::getUpdateVersionFromFilename($file);
        $expected = 0;
        \Flexio\Tests\Check::assertNumber('D.4', '\Flexio\System\System::getTimestamp(); return the numeric portion of the file matching the pattern',  $actual, $expected, $results);

        $file = 'update1';
        $actual = \Flexio\System\System::getUpdateVersionFromFilename($file);
        $expected = 1;
        \Flexio\Tests\Check::assertNumber('D.5', '\Flexio\System\System::getTimestamp(); return the numeric portion of the file matching the pattern',  $actual, $expected, $results);

        $file = 'update1.php';
        $actual = \Flexio\System\System::getUpdateVersionFromFilename($file);
        $expected = 1;
        \Flexio\Tests\Check::assertNumber('D.6', '\Flexio\System\System::getTimestamp(); return the numeric portion of the file matching the pattern',  $actual, $expected, $results);

        $file = 'update0001.php';
        $actual = \Flexio\System\System::getUpdateVersionFromFilename($file);
        $expected = 1;
        \Flexio\Tests\Check::assertNumber('D.7', '\Flexio\System\System::getTimestamp(); return the numeric portion of the file matching the pattern',  $actual, $expected, $results);

        $file = 'update0001_name.php';
        $actual = \Flexio\System\System::getUpdateVersionFromFilename($file);
        $expected = 1;
        \Flexio\Tests\Check::assertNumber('D.8', '\Flexio\System\System::getTimestamp(); return the numeric portion of the file matching the pattern',  $actual, $expected, $results);

        $file = 'update1001_name.php';
        $actual = \Flexio\System\System::getUpdateVersionFromFilename($file);
        $expected = 1001;
        \Flexio\Tests\Check::assertNumber('D.9', '\Flexio\System\System::getTimestamp(); return the numeric portion of the file matching the pattern',  $actual, $expected, $results);

        $file = 'update9999_name.php';
        $actual = \Flexio\System\System::getUpdateVersionFromFilename($file);
        $expected = 9999;
        \Flexio\Tests\Check::assertNumber('D.10', '\Flexio\System\System::getTimestamp(); return the numeric portion of the file matching the pattern',  $actual, $expected, $results);



        // TEST: test for object instantiation

        // BEGIN TEST
        $version = \Flexio\System\System::getLatestVersionNumber();
        $actual = is_integer($version);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.1', '\Flexio\System\System::getLatestVersionNumber(); make sure function returns an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $version = \Flexio\System\System::getLatestVersionNumber();
        $actual = intval($version) > 0;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.2', '\Flexio\System\System::getLatestVersionNumber(); make sure function returns an integer that\'s positive',  $actual, $expected, $results);



        // TEST: validation tests for getGitRevision()

        // BEGIN TEST
        $git_revision = \Flexio\System\System::getGitRevision();
        $actual = is_string($git_revision) === true && preg_match('/^[0-9a-f]{40}$/', $git_revision) === 1;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.1', '\Flexio\System\System::getGitRevision() test to verify a valid hash is returned',  $actual, $expected, $results);



        // TEST: platform check tests; see if we're on a standard platform

        // BEGIN TEST
        $is_windows = \Flexio\System\System::isPlatformWindows();
        $is_mac = \Flexio\System\System::isPlatformMac();
        $is_linux = \Flexio\System\System::isPlatformLinux();
        $platform_check = $is_windows || $is_mac || $is_linux;
        $actual = $platform_check;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('G.1', 'Platform detection test',  $actual, $expected, $results);
    }
}
