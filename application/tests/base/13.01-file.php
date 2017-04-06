<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-04-06
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
        // TODO: add tests for
        // getTempFilename()
        // appendPath()
        // createTempFile()

        // TEST: validation tests for getFilename()

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('');
        $expected = '';
        TestCheck::assertString('A.1', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('name');
        $expected = 'name';
        TestCheck::assertString('A.2', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('.');
        $expected = '';
        TestCheck::assertString('A.3', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('name.');
        $expected = 'name';
        TestCheck::assertString('A.4', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('.ext');
        $expected = '';
        TestCheck::assertString('A.5', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('name.ext');
        $expected = 'name';
        TestCheck::assertString('A.6', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('/name.EXT');
        $expected = 'name';
        TestCheck::assertString('A.7', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('name.ext1.ext2');
        $expected = 'name.ext1';
        TestCheck::assertString('A.8', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('/path/name.ext');
        $expected = 'name';
        TestCheck::assertString('A.9', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('/path/.ext');
        $expected = '';
        TestCheck::assertString('A.10', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('https://www.flex.io/path/test.csv');
        $expected = 'test';
        TestCheck::assertString('A.11', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('https://www.flex.io/path/TEST.TXT');
        $expected = 'TEST';
        TestCheck::assertString('A.12', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('\path\name.ext');
        $expected = 'name';
        TestCheck::assertString('A.13', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('c:\\path\name.ext');
        $expected = 'name';
        TestCheck::assertString('A.14', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);



        // TEST: validation tests for getFileExtension()

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('');
        $expected = '';
        TestCheck::assertString('B.1', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('name');
        $expected = '';
        TestCheck::assertString('B.2', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('.');
        $expected = '';
        TestCheck::assertString('B.3', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('name.');
        $expected = '';
        TestCheck::assertString('B.4', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('.ext');
        $expected = 'ext';
        TestCheck::assertString('B.5', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('name.ext');
        $expected = 'ext';
        TestCheck::assertString('B.6', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('/name.EXT');
        $expected = 'EXT';
        TestCheck::assertString('B.7', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('name.ext1.ext2');
        $expected = 'ext2';
        TestCheck::assertString('B.8', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('/path/name.ext');
        $expected = 'ext';
        TestCheck::assertString('B.9', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('/path/.ext');
        $expected = 'ext';
        TestCheck::assertString('B.10', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('https://www.flex.io/path/test.csv');
        $expected = 'csv';
        TestCheck::assertString('B.11', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('https://www.flex.io/path/TEST.TXT');
        $expected = 'TXT';
        TestCheck::assertString('B.12', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('\path\name.ext');
        $expected = 'ext';
        TestCheck::assertString('B.13', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('c:\\path\name.ext');
        $expected = 'ext';
        TestCheck::assertString('B.14', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);
    }
}
