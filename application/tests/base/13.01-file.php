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
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('name');
        $expected = 'name';
        \Flexio\Tests\Check::assertString('A.2', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('.');
        $expected = '';
        \Flexio\Tests\Check::assertString('A.3', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('name.');
        $expected = 'name';
        \Flexio\Tests\Check::assertString('A.4', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('.ext');
        $expected = '';
        \Flexio\Tests\Check::assertString('A.5', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('name.ext');
        $expected = 'name';
        \Flexio\Tests\Check::assertString('A.6', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('/name.EXT');
        $expected = 'name';
        \Flexio\Tests\Check::assertString('A.7', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('name.ext1.ext2');
        $expected = 'name.ext1';
        \Flexio\Tests\Check::assertString('A.8', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('/path/name.ext');
        $expected = 'name';
        \Flexio\Tests\Check::assertString('A.9', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('/path/.ext');
        $expected = '';
        \Flexio\Tests\Check::assertString('A.10', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('https://www.flex.io/path/test.csv');
        $expected = 'test';
        \Flexio\Tests\Check::assertString('A.11', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('https://www.flex.io/path/TEST.TXT');
        $expected = 'TEST';
        \Flexio\Tests\Check::assertString('A.12', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('\path\name.ext');
        $expected = 'name';
        \Flexio\Tests\Check::assertString('A.13', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFilename('c:\\path\name.ext');
        $expected = 'name';
        \Flexio\Tests\Check::assertString('A.14', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);



        // TEST: validation tests for getFileExtension()

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('');
        $expected = '';
        \Flexio\Tests\Check::assertString('B.1', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('name');
        $expected = '';
        \Flexio\Tests\Check::assertString('B.2', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('.');
        $expected = '';
        \Flexio\Tests\Check::assertString('B.3', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('name.');
        $expected = '';
        \Flexio\Tests\Check::assertString('B.4', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('.ext');
        $expected = 'ext';
        \Flexio\Tests\Check::assertString('B.5', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('name.ext');
        $expected = 'ext';
        \Flexio\Tests\Check::assertString('B.6', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('/name.EXT');
        $expected = 'EXT';
        \Flexio\Tests\Check::assertString('B.7', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('name.ext1.ext2');
        $expected = 'ext2';
        \Flexio\Tests\Check::assertString('B.8', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('/path/name.ext');
        $expected = 'ext';
        \Flexio\Tests\Check::assertString('B.9', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('/path/.ext');
        $expected = 'ext';
        \Flexio\Tests\Check::assertString('B.10', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('https://www.flex.io/path/test.csv');
        $expected = 'csv';
        \Flexio\Tests\Check::assertString('B.11', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('https://www.flex.io/path/TEST.TXT');
        $expected = 'TXT';
        \Flexio\Tests\Check::assertString('B.12', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('\path\name.ext');
        $expected = 'ext';
        \Flexio\Tests\Check::assertString('B.13', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::getFileExtension('c:\\path\name.ext');
        $expected = 'ext';
        \Flexio\Tests\Check::assertString('B.14', '\Flexio\Base\File::getFileExtension() test to extract extension',  $actual, $expected, $results);



        // TEST: \Flexio\Base\File::matchPath()

        // BEGIN TEST
        $actual = \Flexio\Base\File::matchPath('', '', true);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.1', '\Flexio\Base\File::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::matchPath('a', '', true);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.2', '\Flexio\Base\File::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::matchPath('', 'a', true);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.3', '\Flexio\Base\File::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::matchPath('a', 'a', true);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.4', '\Flexio\Base\File::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::matchPath('A', 'a', true);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.5', '\Flexio\Base\File::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::matchPath('a', 'A', true);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.6', '\Flexio\Base\File::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::matchPath('A', 'a', false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.7', '\Flexio\Base\File::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::matchPath('a', 'A', false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.8', '\Flexio\Base\File::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::matchPath('a', '*', true);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.9', '\Flexio\Base\File::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::matchPath('/folder1/folder2/file.txt', '*.txt', true);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.10', '\Flexio\Base\File::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::matchPath('/folder1/folder2/file.txt', '/folder1/folder2/file.txt', true);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.11', '\Flexio\Base\File::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::matchPath('/folder1/folder2/file.txt', '/folder1/folder2/*.txt', true);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.12', '\Flexio\Base\File::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::matchPath('/folder1/folder2/file.txt', '/folder1/folder2/*.*', true);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.13', '\Flexio\Base\File::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::matchPath('/folder1/folder2/file.txt', '/folder1/**/file.txt', true);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.14', '\Flexio\Base\File::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::matchPath('/folder1/folder2/file.txt', '/folder1/folder?/file.txt', true);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.15', '\Flexio\Base\File::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::matchPath('/folder1/folder2/file.txt', '/folder1/folder2/*.{txt,csv}', true);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.16', '\Flexio\Base\File::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::matchPath('/folder1/folder2/file.csv', '/folder1/folder2/*.{txt,csv}', true);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.17', '\Flexio\Base\File::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::matchPath('/folder1/folder2/file.json', '/folder1/folder2/*.{txt,csv}', true);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.18', '\Flexio\Base\File::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::matchPath('/folder1/folder2/file.json', '/folder1/folder2/file[1-9].*', true);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.19', '\Flexio\Base\File::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::matchPath('/folder1/folder2/file1.json', '/folder1/folder2/file[1-9].*', true);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.20', '\Flexio\Base\File::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::matchPath('/folder1/folder2/filea.json', '/folder1/folder2/file[1-9].*', true);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.21', '\Flexio\Base\File::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\File::matchPath('/folder1/folder2/file1abc.json', '/folder1/folder2/file[1-9]*.*', true);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.22', '\Flexio\Base\File::matchPath() test for path match',  $actual, $expected, $results);
    }
}
