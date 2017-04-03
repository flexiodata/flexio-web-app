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
        // SETUP
        $model = TestUtil::getModel();


        // TODO: fix bad tests that are commented out

        // TEST: content parsing; empty input

        // BEGIN TEST
        $email = \Flexio\Services\Email::parseText('');
        $actual = get_class($email);
        $expected = 'Flexio\Services\Email';
        TestCheck::assertString('A.1', '\Flexio\Services\Email::parseText(); empty input', $actual, $expected, $results);



        // TEST: content parsing; "from" information

        // BEGIN TEST
        $content = getTestEmailContents('m0001.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Doug Sauder <doug@example.com>"]';
        TestCheck::assertArray('B.1', '\Flexio\Services\Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0002.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Doug Sauder <doug@example.com>"]';
        TestCheck::assertArray('B.2', '\Flexio\Services\Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0003.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Doug Sauder <doug@example.com>"]';
        TestCheck::assertArray('B.3', '\Flexio\Services\Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0004.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Doug Sauder <doug@example.com>"]';
        TestCheck::assertArray('B.4', '\Flexio\Services\Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0005.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Doug Sauder <doug@example.com>"]';
        TestCheck::assertArray('B.5', '\Flexio\Services\Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0006.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Doug Sauder <doug@example.com>"]';
        TestCheck::assertArray('B.6', '\Flexio\Services\Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0007.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Doug Sauder <doug@example.com>"]';
        TestCheck::assertArray('B.7', '\Flexio\Services\Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0008.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Doug Sauder <doug@example.com>"]';
        TestCheck::assertArray('B.8', '\Flexio\Services\Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0009.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Doug Sauder <doug@example.com>"]';
        TestCheck::assertArray('B.9', '\Flexio\Services\Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0010.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Doug Sauder <doug@example.com>"]';
        TestCheck::assertArray('B.10', '\Flexio\Services\Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0011.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Doug Sauder <doug@example.com>"]';
        TestCheck::assertArray('B.11', '\Flexio\Services\Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0012.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Doug Sauder <doug@example.com>"]';
        TestCheck::assertArray('B.12', '\Flexio\Services\Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0013.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Doug Sauder <doug@example.com>"]';
        TestCheck::assertArray('B.13', '\Flexio\Services\Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0014.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Doug Sauder <doug@example.com>"]';
        TestCheck::assertArray('B.14', '\Flexio\Services\Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0015.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Doug Sauder <doug@example.com>"]';
        TestCheck::assertArray('B.15', '\Flexio\Services\Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0016.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Doug Sauder <doug@example.com>"]';
        TestCheck::assertArray('B.16', '\Flexio\Services\Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0017.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Doug Sauder <doug@example.com>"]';
        TestCheck::assertArray('B.17', '\Flexio\Services\Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0018.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Doug Sauder <doug@example.com>"]';
        TestCheck::assertArray('B.18', '\Flexio\Services\Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0019.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Doug Sauder <doug@example.com>"]';
        TestCheck::assertArray('B.19', '\Flexio\Services\Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0020.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Doug Sauder <doug@example.com>"]';
        TestCheck::assertArray('B.20', '\Flexio\Services\Email::parseText(); get the "from" addresses', $actual, $expected, $results);



        // TEST: content parsing; "to" information

        // BEGIN TEST
        $content = getTestEmailContents('m0001.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["J\u00fcrgen Schm\u00fcrgen <schmuergen@example.com>"]';
        TestCheck::assertArray('C.1', '\Flexio\Services\Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0002.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["J\u00fcrgen Schm\u00fcrgen <schmuergen@example.com>"]';
        TestCheck::assertArray('C.2', '\Flexio\Services\Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0003.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["J\u00fcrgen Schm\u00fcrgen <schmuergen@example.com>"]';
        TestCheck::assertArray('C.3', '\Flexio\Services\Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0004.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["J\u00fcrgen Schm\u00fcrgen <schmuergen@example.com>"]';
        TestCheck::assertArray('C.4', '\Flexio\Services\Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0005.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["J\u00fcrgen Schm\u00fcrgen <schmuergen@example.com>"]';
        TestCheck::assertArray('C.5', '\Flexio\Services\Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0006.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["Joe Blow <jblow@example.com>","J\u00fcrgen Schm\u00fcrgen <schmuergen@example.com>"]';
        TestCheck::assertArray('C.6', '\Flexio\Services\Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0007.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["Heinz M\u00fcller <mueller@example.com>"]';
        TestCheck::assertArray('C.7', '\Flexio\Services\Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0008.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["Heinz M\u00fcller <mueller@example.com>"]';
        TestCheck::assertArray('C.8', '\Flexio\Services\Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0009.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["Heinz M\u00fcller <mueller@example.com>"]';
        TestCheck::assertArray('C.9', '\Flexio\Services\Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0010.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["Heinz M\u00fcller <mueller@example.com>"]';
        TestCheck::assertArray('C.10', '\Flexio\Services\Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0011.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["Heinz M\u00fcller <mueller@example.com>"]';
        TestCheck::assertArray('C.11', '\Flexio\Services\Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0012.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["Joe Blow <jblow@example.com>"]';
        TestCheck::assertArray('C.12', '\Flexio\Services\Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0013.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["Joe Blow <jblow@example.com>"]';
        TestCheck::assertArray('C.13', '\Flexio\Services\Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0014.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["Joe Blow <jblow@example.com>"]';
        TestCheck::assertArray('C.14', '\Flexio\Services\Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0015.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["Joe Blow <jblow@example.com>"]';
        TestCheck::assertArray('C.15', '\Flexio\Services\Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0016.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["Joe Blow <jblow@example.com>"]';
        TestCheck::assertArray('C.16', '\Flexio\Services\Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0017.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["Joe Blow <jblow@example.com>"]';
        TestCheck::assertArray('C.17', '\Flexio\Services\Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0018.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["Joe Blow <jblow@example.com>"]';
        TestCheck::assertArray('C.18', '\Flexio\Services\Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0019.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["Joe Blow <jblow@example.com>"]';
        TestCheck::assertArray('C.19', '\Flexio\Services\Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0020.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["Joe Blow <jblow@example.com>"]';
        TestCheck::assertArray('C.20', '\Flexio\Services\Email::parseText(); get the "to" addresses', $actual, $expected, $results);



        // TEST: content parsing; "subject" information

        // BEGIN TEST
        $content = getTestEmailContents('m0001.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getSubject();
        $expected = "Die Hasen und die Frösche";
        TestCheck::assertString('D.1', '\Flexio\Services\Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0002.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Die Hasen und die Frösche';
        TestCheck::assertString('D.2', '\Flexio\Services\Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0003.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Die Hasen und die Frösche';
        TestCheck::assertString('D.3', '\Flexio\Services\Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0004.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Die Hasen und die Frösche (Microsoft Outlook 00)';
        TestCheck::assertString('D.4', '\Flexio\Services\Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0005.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Die Hasen und die Frösche (Microsoft Outlook 00)';
        TestCheck::assertString('D.5', '\Flexio\Services\Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0006.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Die Hasen und die Frösche (Microsoft Outlook 00)';
        TestCheck::assertString('D.6', '\Flexio\Services\Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0007.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Die Hasen und die Frösche (Microsoft Outlook 00)';
        TestCheck::assertString('D.7', '\Flexio\Services\Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0008.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Die Hasen und die Frösche (Microsoft Outlook 00)';
        TestCheck::assertString('D.8', '\Flexio\Services\Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0009.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Die Hasen und die Frösche';
        TestCheck::assertString('D.9', '\Flexio\Services\Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0010.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Die Hasen und die Frösche (Microsoft Outlook 00)';
        TestCheck::assertString('D.10', '\Flexio\Services\Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0011.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Test message from Microsoft Outlook 00';
        TestCheck::assertString('D.11', '\Flexio\Services\Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0012.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Test message from Microsoft Outlook 00';
        TestCheck::assertString('D.12', '\Flexio\Services\Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0013.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Test message from Microsoft Outlook 00';
        TestCheck::assertString('D.13', '\Flexio\Services\Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0014.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Test message from Microsoft Outlook 00';
        TestCheck::assertString('D.14', '\Flexio\Services\Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0015.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Test message from Microsoft Outlook 00';
        TestCheck::assertString('D.15', '\Flexio\Services\Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0016.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Test message from Microsoft Outlook 00';
        TestCheck::assertString('D.16', '\Flexio\Services\Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0017.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Test message from Microsoft Outlook 00';
        TestCheck::assertString('D.17', '\Flexio\Services\Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0018.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Test message from Microsoft Outlook 00';
        TestCheck::assertString('D.18', '\Flexio\Services\Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0019.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Test message from Microsoft Outlook 00';
        TestCheck::assertString('D.19', '\Flexio\Services\Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0020.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Test message from Microsoft Outlook 00';
        TestCheck::assertString('D.20', '\Flexio\Services\Email::parseText(); get the "subject"', $actual, $expected, $results);



        // TEST: content parsing; "message text" information

        // BEGIN TEST
        $content = getTestEmailContents('m0001.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageText(),".");
        $expected = 4;
        TestCheck::assertString('E.1', '\Flexio\Services\Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0002.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageText(),".");
        $expected = 4;
        TestCheck::assertString('E.2', '\Flexio\Services\Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0003.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageText(),".");
        $expected = 4;
        TestCheck::assertString('E.3', '\Flexio\Services\Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0004.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageText(),".");
        $expected = 4;
        TestCheck::assertString('E.4', '\Flexio\Services\Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0005.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageText(),".");
        $expected = 4;
        TestCheck::assertString('E.5', '\Flexio\Services\Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0006.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageText(),".");
        $expected = 4;
        TestCheck::assertString('E.6', '\Flexio\Services\Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0007.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageText(),".");
        $expected = 4;
        TestCheck::assertString('E.7', '\Flexio\Services\Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0008.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageText(),".");
        $expected = 4;
        TestCheck::assertString('E.8', '\Flexio\Services\Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0009.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageText(),".");
        $expected = 4;
        TestCheck::assertString('E.9', '\Flexio\Services\Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0010.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageText(),".");
        $expected = 4;
        TestCheck::assertString('E.10', '\Flexio\Services\Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0011.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageText(),".");
        $expected = 7;
        TestCheck::assertString('E.11', '\Flexio\Services\Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0012.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageText(),".");
        $expected = 0;
        TestCheck::assertString('E.12', '\Flexio\Services\Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0013.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageText(),".");
        $expected = 0;
        TestCheck::assertString('E.13', '\Flexio\Services\Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0014.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageText(),".");
        $expected = 7;
        TestCheck::assertString('E.14', '\Flexio\Services\Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0015.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageText(),".");
        $expected = 7;
        TestCheck::assertString('E.15', '\Flexio\Services\Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0016.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageText(),".");
        $expected = 7;
        TestCheck::assertString('E.16', '\Flexio\Services\Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0017.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageText(),".");
        $expected = 7;
        TestCheck::assertString('E.17', '\Flexio\Services\Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0018.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageText(),".");
        $expected = 7;
        TestCheck::assertString('E.18', '\Flexio\Services\Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0019.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageText(),".");
        $expected = 7;
        TestCheck::assertString('E.19', '\Flexio\Services\Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0020.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageText(),".");
        $expected = 7;
        TestCheck::assertString('E.20', '\Flexio\Services\Email::parseText(); get the message text', $actual, $expected, $results);



        // TEST: content parsing; "message html" information

        // BEGIN TEST
        $content = getTestEmailContents('m0001.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 0;
        TestCheck::assertNumber('F.1', '\Flexio\Services\Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0002.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 0;
        TestCheck::assertNumber('F.2', '\Flexio\Services\Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0003.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 0;
        TestCheck::assertNumber('F.3', '\Flexio\Services\Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0004.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 0;
        TestCheck::assertNumber('F.4', '\Flexio\Services\Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0005.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 0;
        TestCheck::assertNumber('F.5', '\Flexio\Services\Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0006.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 0;
        TestCheck::assertNumber('F.6', '\Flexio\Services\Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0007.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 0;
        TestCheck::assertNumber('F.7', '\Flexio\Services\Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0008.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 0;
        TestCheck::assertNumber('F.8', '\Flexio\Services\Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0009.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 0;
        TestCheck::assertNumber('F.9', '\Flexio\Services\Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0010.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 0;
        TestCheck::assertNumber('F.10', '\Flexio\Services\Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0011.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 0;
        TestCheck::assertNumber('F.11', '\Flexio\Services\Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0012.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 0;
        TestCheck::assertNumber('F.12', '\Flexio\Services\Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0013.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 0;
        TestCheck::assertNumber('F.13', '\Flexio\Services\Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0014.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 19;
        TestCheck::assertNumber('F.14', '\Flexio\Services\Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0015.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 19;
        TestCheck::assertNumber('F.15', '\Flexio\Services\Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0016.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 35;
        TestCheck::assertNumber('F.16', '\Flexio\Services\Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0017.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 28;
        TestCheck::assertNumber('F.17', '\Flexio\Services\Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0018.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 0;
        TestCheck::assertNumber('F.18', '\Flexio\Services\Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0019.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 19;
        TestCheck::assertNumber('F.19', '\Flexio\Services\Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0020.txt');
        $email = \Flexio\Services\Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 19;
        TestCheck::assertNumber('F.20', '\Flexio\Services\Email::parseText(); get the message html', $actual, $expected, $results);
    }
}

