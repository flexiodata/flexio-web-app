<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-06-28
 *
 * @package flexio
 * @subpackage Tests
 */


declare(strict_types=1);
namespace Flexio\Tests;


function getTestEmailContents($filename)
{
    // loads a test email stream from the php parsing library;
    // TODO: remove external library dependency on these tests

    $testfilepath = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'mailmimeparser' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . '_data' . DIRECTORY_SEPARATOR . 'emails';
    $testfile = $testfilepath . DIRECTORY_SEPARATOR . $filename;
    return file_get_contents($testfile);
}

function getTestEmailFile($filename)
{
    $testfilepath = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'mailmimeparser' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . '_data' . DIRECTORY_SEPARATOR . 'emails';
    $testfile = $testfilepath . DIRECTORY_SEPARATOR . $filename;
    return $testfile;
}


class Test
{
    public function run(&$results)
    {
        // TODO: fill out tests

        // TEST: stream parsing; empty input

        // BEGIN TEST
        $email = \Flexio\Services\Email::parseStream('');
        $actual = $email;
        $expected = false;
        TestCheck::assertBoolean('A.1', '\Flexio\Services\Email::parseStream(); basic test', $actual, $expected, $results);


        // TEST: content parsing; "from" information

        // BEGIN TEST
        $file = getTestEmailFile('m0001.txt');
        $email = \Flexio\Services\Email::parseStream($file);
        $actual = $email->getFrom();
        $expected = '["Doug Sauder <doug@example.com>"]';
        TestCheck::assertArray('B.1', '\Flexio\Services\Email::parseText(); get the "from" addresses', $actual, $expected, $results);
    }
}
