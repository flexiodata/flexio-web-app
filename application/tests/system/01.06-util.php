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


class Test
{
    public function run(&$results)
    {
        // TEST: \Util::beforeFirst()

        // BEGIN TEST
        $str = \Util::beforeFirst('', '');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('A.1', 'Util::beforeFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::beforeFirst('a', '');
        $actual = ($str === 'a');
        $expected = true;
        TestCheck::assertBoolean('A.2', 'Util::beforeFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::beforeFirst('', 'a');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('A.3', 'Util::beforeFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::beforeFirst('a', 'a');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('A.4', 'Util::beforeFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::beforeFirst('aa', 'a');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('A.5', 'Util::beforeFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::beforeFirst('aab', 'b');
        $actual = ($str === 'aa');
        $expected = true;
        TestCheck::assertBoolean('A.6', 'Util::beforeFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::beforeFirst('aabab', 'ab');
        $actual = ($str === 'a');
        $expected = true;
        TestCheck::assertBoolean('A.7', 'Util::beforeFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::beforeFirst('aab', 'c');
        $actual = ($str === 'aab');
        $expected = true;
        TestCheck::assertBoolean('A.8', 'Util::beforeFirst() test for substring extraction',  $actual, $expected, $results);



        // TEST: \Util::afterFirst()

        // BEGIN TEST
        $str = \Util::afterFirst('', '');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('B.1', 'Util::afterFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::afterFirst('a', '');
        $actual = ($str === 'a');
        $expected = true;
        TestCheck::assertBoolean('B.2', 'Util::afterFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::afterFirst('', 'a');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('B.3', 'Util::afterFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::afterFirst('a', 'a');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('B.4', 'Util::afterFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::afterFirst('aab', 'a');
        $actual = ($str === 'ab');
        $expected = true;
        TestCheck::assertBoolean('B.5', 'Util::afterFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::afterFirst('aab', 'a');
        $actual = ($str === 'ab');
        $expected = true;
        TestCheck::assertBoolean('B.6', 'Util::afterFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::afterFirst('ababa', 'ab');
        $actual = ($str === 'aba');
        $expected = true;
        TestCheck::assertBoolean('B.7', 'Util::afterFirst() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::afterFirst('aab', 'c');
        $actual = ($str === 'aab');
        $expected = true;
        TestCheck::assertBoolean('B.8', 'Util::afterFirst() test for substring extraction',  $actual, $expected, $results);



        // TEST: \Util::beforeLast()

        // BEGIN TEST
        $str = \Util::beforeLast('', '');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('C.1', 'Util::beforeLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::beforeLast('a', '');
        $actual = ($str === 'a');
        $expected = true;
        TestCheck::assertBoolean('C.2', 'Util::beforeLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::beforeLast('', 'a');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('C.3', 'Util::beforeLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::beforeLast('a', 'a');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('C.4', 'Util::beforeLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::beforeLast('aa', 'a');
        $actual = ($str === 'a');
        $expected = true;
        TestCheck::assertBoolean('C.5', 'Util::beforeLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::beforeLast('abb', 'b');
        $actual = ($str === 'ab');
        $expected = true;
        TestCheck::assertBoolean('C.6', 'Util::beforeLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::beforeLast('ababa', 'ab');
        $actual = ($str === 'ab');
        $expected = true;
        TestCheck::assertBoolean('C.7', 'Util::beforeLast() test for substring extraction',  $actual, $expected, $results);

        $str = \Util::beforeLast('aab', 'c');
        $actual = ($str === 'aab');
        $expected = true;
        TestCheck::assertBoolean('C.8', 'Util::beforeLast() test for substring extraction',  $actual, $expected, $results);



        // TEST: \Util::afterLast()

        // BEGIN TEST
        $str = \Util::afterLast('', '');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('D.1', 'Util::afterLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::afterLast('a', '');
        $actual = ($str === 'a');
        $expected = true;
        TestCheck::assertBoolean('D.2', 'Util::afterLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::afterLast('', 'a');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('D.3', 'Util::afterLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::afterLast('a', 'a');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('D.4', 'Util::afterLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::afterLast('aa', 'a');
        $actual = ($str === '');
        $expected = true;
        TestCheck::assertBoolean('D.5', 'Util::afterLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::afterLast('aab', 'a');
        $actual = ($str === 'b');
        $expected = true;
        TestCheck::assertBoolean('D.6', 'Util::afterLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::afterLast('ababa', 'ab');
        $actual = ($str === 'a');
        $expected = true;
        TestCheck::assertBoolean('D.7', 'Util::afterLast() test for substring extraction',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Util::afterLast('aab', 'c');
        $actual = ($str === 'aab');
        $expected = true;
        TestCheck::assertBoolean('D.8', 'Util::afterLast() test for substring extraction',  $actual, $expected, $results);



        // TEST: \Util::matchPath()

        // BEGIN TEST
        $actual = \Util::matchPath('', '', true);
        $expected = true;
        TestCheck::assertBoolean('E.1', 'Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Util::matchPath('a', '', true);
        $expected = false;
        TestCheck::assertBoolean('E.2', 'Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Util::matchPath('', 'a', true);
        $expected = false;
        TestCheck::assertBoolean('E.3', 'Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Util::matchPath('a', 'a', true);
        $expected = true;
        TestCheck::assertBoolean('E.4', 'Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Util::matchPath('A', 'a', true);
        $expected = false;
        TestCheck::assertBoolean('E.5', 'Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Util::matchPath('a', 'A', true);
        $expected = false;
        TestCheck::assertBoolean('E.6', 'Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Util::matchPath('A', 'a', false);
        $expected = true;
        TestCheck::assertBoolean('E.7', 'Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Util::matchPath('a', 'A', false);
        $expected = true;
        TestCheck::assertBoolean('E.8', 'Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Util::matchPath('a', '*', true);
        $expected = true;
        TestCheck::assertBoolean('E.9', 'Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Util::matchPath('/folder1/folder2/file.txt', '*.txt', true);
        $expected = true;
        TestCheck::assertBoolean('E.10', 'Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Util::matchPath('/folder1/folder2/file.txt', '/folder1/folder2/file.txt', true);
        $expected = true;
        TestCheck::assertBoolean('E.11', 'Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Util::matchPath('/folder1/folder2/file.txt', '/folder1/folder2/*.txt', true);
        $expected = true;
        TestCheck::assertBoolean('E.12', 'Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Util::matchPath('/folder1/folder2/file.txt', '/folder1/folder2/*.*', true);
        $expected = true;
        TestCheck::assertBoolean('E.13', 'Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Util::matchPath('/folder1/folder2/file.txt', '/folder1/**/file.txt', true);
        $expected = true;
        TestCheck::assertBoolean('E.14', 'Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Util::matchPath('/folder1/folder2/file.txt', '/folder1/folder?/file.txt', true);
        $expected = true;
        TestCheck::assertBoolean('E.15', 'Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Util::matchPath('/folder1/folder2/file.txt', '/folder1/folder2/*.{txt,csv}', true);
        $expected = false;
        TestCheck::assertBoolean('E.16', 'Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Util::matchPath('/folder1/folder2/file.csv', '/folder1/folder2/*.{txt,csv}', true);
        $expected = false;
        TestCheck::assertBoolean('E.17', 'Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Util::matchPath('/folder1/folder2/file.json', '/folder1/folder2/*.{txt,csv}', true);
        $expected = false;
        TestCheck::assertBoolean('E.18', 'Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Util::matchPath('/folder1/folder2/file.json', '/folder1/folder2/file[1-9].*', true);
        $expected = false;
        TestCheck::assertBoolean('E.19', 'Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Util::matchPath('/folder1/folder2/file1.json', '/folder1/folder2/file[1-9].*', true);
        $expected = true;
        TestCheck::assertBoolean('E.20', 'Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Util::matchPath('/folder1/folder2/filea.json', '/folder1/folder2/file[1-9].*', true);
        $expected = false;
        TestCheck::assertBoolean('E.21', 'Util::matchPath() test for path match',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Util::matchPath('/folder1/folder2/file1abc.json', '/folder1/folder2/file[1-9]*.*', true);
        $expected = true;
        TestCheck::assertBoolean('E.22', 'Util::matchPath() test for path match',  $actual, $expected, $results);
    }
}
