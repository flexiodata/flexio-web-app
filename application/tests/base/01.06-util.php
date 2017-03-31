<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-05
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
        // TEST: \Flexio\Base\Util::beforeFirst()

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeFirst('', '');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Flexio\Base\Util::beforeFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeFirst('a', '');
        $actual = ($str === 'a');
        $expected = true;
        TestCheck::assertBoolean('A.2', '\Flexio\Base\Util::beforeFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeFirst('', 'a');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('A.3', '\Flexio\Base\Util::beforeFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeFirst('a', 'a');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('A.4', '\Flexio\Base\Util::beforeFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeFirst('aa', 'a');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('A.5', '\Flexio\Base\Util::beforeFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeFirst('aab', 'b');
        $actual = ($str === 'aa');
        $expected = true;
        TestCheck::assertBoolean('A.6', '\Flexio\Base\Util::beforeFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeFirst('aabab', 'ab');
        $actual = ($str === 'a');
        $expected = true;
        TestCheck::assertBoolean('A.7', '\Flexio\Base\Util::beforeFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeFirst('aab', 'c');
        $actual = ($str === 'aab');
        $expected = true;
        TestCheck::assertBoolean('A.8', '\Flexio\Base\Util::beforeFirst() test for substring extraction',  $actual, $expected, $results);



        // TEST: \Flexio\Base\Util::afterFirst()

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterFirst('', '');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Flexio\Base\Util::afterFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterFirst('a', '');
        $actual = ($str === 'a');
        $expected = true;
        TestCheck::assertBoolean('B.2', '\Flexio\Base\Util::afterFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterFirst('', 'a');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('B.3', '\Flexio\Base\Util::afterFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterFirst('a', 'a');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('B.4', '\Flexio\Base\Util::afterFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterFirst('aab', 'a');
        $actual = ($str === 'ab');
        $expected = true;
        TestCheck::assertBoolean('B.5', '\Flexio\Base\Util::afterFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterFirst('aab', 'a');
        $actual = ($str === 'ab');
        $expected = true;
        TestCheck::assertBoolean('B.6', '\Flexio\Base\Util::afterFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterFirst('ababa', 'ab');
        $actual = ($str === 'aba');
        $expected = true;
        TestCheck::assertBoolean('B.7', '\Flexio\Base\Util::afterFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterFirst('aab', 'c');
        $actual = ($str === 'aab');
        $expected = true;
        TestCheck::assertBoolean('B.8', '\Flexio\Base\Util::afterFirst() test for substring extraction',  $actual, $expected, $results);



        // TEST: \Flexio\Base\Util::beforeLast()

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeLast('', '');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('C.1', '\Flexio\Base\Util::beforeLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeLast('a', '');
        $actual = ($str === 'a');
        $expected = true;
        TestCheck::assertBoolean('C.2', '\Flexio\Base\Util::beforeLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeLast('', 'a');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('C.3', '\Flexio\Base\Util::beforeLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeLast('a', 'a');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('C.4', '\Flexio\Base\Util::beforeLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeLast('aa', 'a');
        $actual = ($str === 'a');
        $expected = true;
        TestCheck::assertBoolean('C.5', '\Flexio\Base\Util::beforeLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeLast('abb', 'b');
        $actual = ($str === 'ab');
        $expected = true;
        TestCheck::assertBoolean('C.6', '\Flexio\Base\Util::beforeLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::beforeLast('ababa', 'ab');
        $actual = ($str === 'ab');
        $expected = true;
        TestCheck::assertBoolean('C.7', '\Flexio\Base\Util::beforeLast() test for substring extraction',  $actual, $expected, $results);

        $str = \Flexio\Base\Util::beforeLast('aab', 'c');
        $actual = ($str === 'aab');
        $expected = true;
        TestCheck::assertBoolean('C.8', '\Flexio\Base\Util::beforeLast() test for substring extraction',  $actual, $expected, $results);



        // TEST: \Flexio\Base\Util::afterLast()

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterLast('', '');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('D.1', '\Flexio\Base\Util::afterLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterLast('a', '');
        $actual = ($str === 'a');
        $expected = true;
        TestCheck::assertBoolean('D.2', '\Flexio\Base\Util::afterLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterLast('', 'a');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('D.3', '\Flexio\Base\Util::afterLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterLast('a', 'a');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('D.4', '\Flexio\Base\Util::afterLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterLast('aa', 'a');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('D.5', '\Flexio\Base\Util::afterLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterLast('aab', 'a');
        $actual = ($str === 'b');
        $expected = true;
        TestCheck::assertBoolean('D.6', '\Flexio\Base\Util::afterLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterLast('ababa', 'ab');
        $actual = ($str === 'a');
        $expected = true;
        TestCheck::assertBoolean('D.7', '\Flexio\Base\Util::afterLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Util::afterLast('aab', 'c');
        $actual = ($str === 'aab');
        $expected = true;
        TestCheck::assertBoolean('D.8', '\Flexio\Base\Util::afterLast() test for substring extraction',  $actual, $expected, $results);



        // TEST: \Flexio\Base\Util::matchPath()

        // BEGIN TEST
        $actual = \Flexio\Base\Util::matchPath('', '', true);
        $expected = true;
        TestCheck::assertBoolean('E.1', '\Flexio\Base\Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::matchPath('a', '', true);
        $expected = false;
        TestCheck::assertBoolean('E.2', '\Flexio\Base\Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::matchPath('', 'a', true);
        $expected = false;
        TestCheck::assertBoolean('E.3', '\Flexio\Base\Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::matchPath('a', 'a', true);
        $expected = true;
        TestCheck::assertBoolean('E.4', '\Flexio\Base\Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::matchPath('A', 'a', true);
        $expected = false;
        TestCheck::assertBoolean('E.5', '\Flexio\Base\Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::matchPath('a', 'A', true);
        $expected = false;
        TestCheck::assertBoolean('E.6', '\Flexio\Base\Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::matchPath('A', 'a', false);
        $expected = true;
        TestCheck::assertBoolean('E.7', '\Flexio\Base\Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::matchPath('a', 'A', false);
        $expected = true;
        TestCheck::assertBoolean('E.8', '\Flexio\Base\Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::matchPath('a', '*', true);
        $expected = true;
        TestCheck::assertBoolean('E.9', '\Flexio\Base\Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::matchPath('/folder1/folder2/file.txt', '*.txt', true);
        $expected = true;
        TestCheck::assertBoolean('E.10', '\Flexio\Base\Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::matchPath('/folder1/folder2/file.txt', '/folder1/folder2/file.txt', true);
        $expected = true;
        TestCheck::assertBoolean('E.11', '\Flexio\Base\Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::matchPath('/folder1/folder2/file.txt', '/folder1/folder2/*.txt', true);
        $expected = true;
        TestCheck::assertBoolean('E.12', '\Flexio\Base\Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::matchPath('/folder1/folder2/file.txt', '/folder1/folder2/*.*', true);
        $expected = true;
        TestCheck::assertBoolean('E.13', '\Flexio\Base\Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::matchPath('/folder1/folder2/file.txt', '/folder1/**/file.txt', true);
        $expected = true;
        TestCheck::assertBoolean('E.14', '\Flexio\Base\Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::matchPath('/folder1/folder2/file.txt', '/folder1/folder?/file.txt', true);
        $expected = true;
        TestCheck::assertBoolean('E.15', '\Flexio\Base\Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::matchPath('/folder1/folder2/file.txt', '/folder1/folder2/*.{txt,csv}', true);
        $expected = false;
        TestCheck::assertBoolean('E.16', '\Flexio\Base\Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::matchPath('/folder1/folder2/file.csv', '/folder1/folder2/*.{txt,csv}', true);
        $expected = false;
        TestCheck::assertBoolean('E.17', '\Flexio\Base\Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::matchPath('/folder1/folder2/file.json', '/folder1/folder2/*.{txt,csv}', true);
        $expected = false;
        TestCheck::assertBoolean('E.18', '\Flexio\Base\Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::matchPath('/folder1/folder2/file.json', '/folder1/folder2/file[1-9].*', true);
        $expected = false;
        TestCheck::assertBoolean('E.19', '\Flexio\Base\Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::matchPath('/folder1/folder2/file1.json', '/folder1/folder2/file[1-9].*', true);
        $expected = true;
        TestCheck::assertBoolean('E.20', '\Flexio\Base\Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::matchPath('/folder1/folder2/filea.json', '/folder1/folder2/file[1-9].*', true);
        $expected = false;
        TestCheck::assertBoolean('E.21', '\Flexio\Base\Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::matchPath('/folder1/folder2/file1abc.json', '/folder1/folder2/file[1-9]*.*', true);
        $expected = true;
        TestCheck::assertBoolean('E.22', '\Flexio\Base\Util::matchPath() test for path match',  $actual, $expected, $results);
    }
}
