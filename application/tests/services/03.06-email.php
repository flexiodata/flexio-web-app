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


function getTestEmailContents($filename)
{
    // loads a test email stream from the php parsing library;
    // TODO: remove external library dependency on these tests

    $testfilepath = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'phpmimemailparser' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'mails';
    $testfile = $testfilepath . DIRECTORY_SEPARATOR . $filename;
    return file_get_contents($testfile);
}

function getTestEmailFile($filename)
{
    $testfilepath = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'phpmimemailparser' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'mails';
    $testfile = $testfilepath . DIRECTORY_SEPARATOR . $filename;
    return $testfile;
}


class Test
{
    public function run(&$results)
    {
        // TODO: fix bad tests that are commented out

        // TEST: content parsing; empty input

        // BEGIN TEST
        $email = Email::parseText('');
        $actual = get_class($email);
        $expected = 'Email';
        TestCheck::assertString('A.1', 'Email::parseText(); empty input', $actual, $expected, $results);



        // TEST: content parsing; "from" information

        // BEGIN TEST
        $content = getTestEmailContents('m0001');
        $email = Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Name <name@company.com>"]';
        TestCheck::assertArray('B.1', 'Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0002');
        $email = Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Name <name@company.com>"]';
        TestCheck::assertArray('B.2', 'Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0003');
        $email = Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Name <name@company.com>"]';
        TestCheck::assertArray('B.3', 'Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0004');
        $email = Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Name <name@company.com>"]';
        TestCheck::assertArray('B.4', 'Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0005');
        $email = Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Name <name@company.com>"]';
        TestCheck::assertArray('B.5', 'Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0006');
        $email = Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Name <name@company.com>"]';
        TestCheck::assertArray('B.6', 'Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0007');
        $email = Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Name <name@company.com>"]';
        TestCheck::assertArray('B.7', 'Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0008');
        $email = Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Name <name@company.com>"]';
        TestCheck::assertArray('B.8', 'Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0009');
        $email = Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Ogone <noreply@ogone.com>"]';
        TestCheck::assertArray('B.9', 'Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0010');
        $email = Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Name <name@company.com>"]';
        TestCheck::assertArray('B.10', 'Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0011');
        $email = Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Name <name@company.com>"]';
        TestCheck::assertArray('B.11', 'Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0012');
        $email = Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Name <name@company.com>"]';
        TestCheck::assertArray('B.12', 'Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0013');
        $email = Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["NAME Firstname <firstname.name@groupe-company.com>"]';
        TestCheck::assertArray('B.13', 'Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0014');
        $email = Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Doug Sauder <dwsauder@example.com>"]';
        TestCheck::assertArray('B.14', 'Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0015');
        $email = Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Vitamart.ca <service@vitamart.ca>"]';
        TestCheck::assertArray('B.15', 'Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0016');
        $email = Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Doug Sauder <dwsauder@example.com>"]';
        TestCheck::assertArray('B.16', 'Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        // TODO: causing problems
        //$content = getTestEmailContents('m0017');
        //$email = Email::parseText($content);
        //$actual = $email->getFrom();
        //$expected = '[]';
        //TestCheck::assertArray('B.17', 'Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0018');
        $email = Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["name@company.com <name@company.com>"]';
        TestCheck::assertArray('B.18', 'Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0019');
        $email = Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["sende\u00e4r <sender@test.com>"]';
        TestCheck::assertArray('B.19', 'Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0020');
        $email = Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Finntack Newsletter <newsletter@finntack.com>"]';
        TestCheck::assertArray('B.20', 'Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0021');
        $email = Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["mail@exemple.com <mail@exemple.com>"]';
        TestCheck::assertArray('B.21', 'Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0022');
        $email = Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["sende\u00e4r <sender@test.com>"]';
        TestCheck::assertArray('B.22', 'Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0023');
        $email = Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Keith Moore <moore@cs.utk.edu>"]';
        TestCheck::assertArray('B.23', 'Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0024');
        $email = Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["John DOE <blablafakeemail@provider.fr>"]';
        TestCheck::assertArray('B.24', 'Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0025');
        $email = Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Name <name@company.com>"]';
        TestCheck::assertArray('B.25', 'Email::parseText(); get the "from" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('issue84');
        $email = Email::parseText($content);
        $actual = $email->getFrom();
        $expected = '["Ferenc Kovacs <tyra3l@gmail.com>"]';
        TestCheck::assertArray('B.26', 'Email::parseText(); get the "from" addresses', $actual, $expected, $results);



        // TEST: content parsing; "to" information

        // BEGIN TEST
        $content = getTestEmailContents('m0001');
        $email = Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["name@company2.com <name@company2.com>"]';
        TestCheck::assertArray('C.1', 'Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0002');
        $email = Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["name@company2.com <name@company2.com>"]';
        TestCheck::assertArray('C.2', 'Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0003');
        $email = Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["name@company2.com <name@company2.com>"]';
        TestCheck::assertArray('C.3', 'Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0004');
        $email = Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["name@company2.com <name@company2.com>"]';
        TestCheck::assertArray('C.4', 'Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0005');
        $email = Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["name@company2.com <name@company2.com>"]';
        TestCheck::assertArray('C.5', 'Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0006');
        $email = Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["name@company2.com <name@company2.com>"]';
        TestCheck::assertArray('C.6', 'Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0007');
        $email = Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["name@company2.com <name@company2.com>"]';
        TestCheck::assertArray('C.7', 'Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0008');
        $email = Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["Name <name@company2.com>"]';
        TestCheck::assertArray('C.8', 'Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0009');
        $email = Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["info@testsite.com <info@testsite.com>"]';
        TestCheck::assertArray('C.9', 'Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0010');
        $email = Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["name@company2.com <name@company2.com>"]';
        TestCheck::assertArray('C.10', 'Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0011');
        $email = Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["Name <name@company.com>"]';
        TestCheck::assertArray('C.11', 'Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0012');
        $email = Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["Name <name@company.com>"]';
        TestCheck::assertArray('C.12', 'Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0013');
        $email = Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["paul.dupont@company.com <paul.dupont@company.com>"]';
        TestCheck::assertArray('C.13', 'Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0014');
        $email = Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["Joe Blow <blow@example.com>"]';
        TestCheck::assertArray('C.14', 'Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0015');
        $email = Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["me@somewhere.com <me@somewhere.com>"]';
        TestCheck::assertArray('C.15', 'Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0016');
        $email = Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["Joe Blow <blow@example.com>"]';
        TestCheck::assertArray('C.16', 'Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        // TODO: causing problems
        //$content = getTestEmailContents('m0017');
        //$email = Email::parseText($content);
        //$actual = $email->getTo();
        //$expected = '[]';
        //TestCheck::assertArray('C.17', 'Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0018');
        $email = Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["name@company2.com <name@company2.com>"]';
        TestCheck::assertArray('C.18', 'Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0019');
        $email = Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["test <test@asdasd.com>"]';
        TestCheck::assertArray('C.19', 'Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0020');
        $email = Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["Clement Wong <clement.wong@finntack.com>"]';
        TestCheck::assertArray('C.20', 'Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0021');
        $email = Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["mail@exemple.com <mail@exemple.com>","mail2@exemple3.com <mail2@exemple3.com>","mail3@exemple2.com <mail3@exemple2.com>"]';
        TestCheck::assertArray('C.21', 'Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0022');
        $email = Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["test <test@asdasd.com>"]';
        TestCheck::assertArray('C.22', 'Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0023');
        $email = Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["Keld J\u00f8rn Simonsen <keld@dkuug.dk>"]';
        TestCheck::assertArray('C.23', 'Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0024');
        $email = Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["list-name <list-name@list-domain.org>"]';
        TestCheck::assertArray('C.24', 'Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0025');
        $email = Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["Name <name@company2.com>"]';
        TestCheck::assertArray('C.25', 'Email::parseText(); get the "to" addresses', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('issue84');
        $email = Email::parseText($content);
        $actual = $email->getTo();
        $expected = '["Levi Morrison <levim@php.net>"]';
        TestCheck::assertArray('C.26', 'Email::parseText(); get the "to" addresses', $actual, $expected, $results);



        // TEST: content parsing; "subject" information

        // BEGIN TEST
        $content = getTestEmailContents('m0001');
        $email = Email::parseText($content);
        $actual = $email->getSubject();
        $expected = "Mail avec fichier attaché de 1ko";
        TestCheck::assertString('D.1', 'Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0002');
        $email = Email::parseText($content);
        $actual = $email->getSubject();
        $expected = "Mail avec fichier attaché de 3ko";
        TestCheck::assertString('D.2', 'Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0003');
        $email = Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Mail de 14 Ko';
        TestCheck::assertString('D.3', 'Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0004');
        $email = Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Mail de 800ko';
        TestCheck::assertString('D.4', 'Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0005');
        $email = Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Mail de 1500 Ko';
        TestCheck::assertString('D.5', 'Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0006');
        $email = Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Mail de 3 196 Ko';
        TestCheck::assertString('D.6', 'Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0007');
        $email = Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Mail avec fichier attaché de 3ko';
        TestCheck::assertString('D.7', 'Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0008');
        $email = Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Testing MIME E-mail composing with cid';
        TestCheck::assertString('D.8', 'Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0009');
        $email = Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Ogone NIEUWE order Maurits PAYID: 951597484 / orderID: 456123 / status: 5';
        TestCheck::assertString('D.9', 'Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0010');
        $email = Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Mail de 800ko without filename';
        TestCheck::assertString('D.10', 'Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0011');
        $email = Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Hello World !';
        TestCheck::assertString('D.11', 'Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0012');
        $email = Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Hello World !';
        TestCheck::assertString('D.12', 'Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0013');
        $email = Email::parseText($content);
        $actual = $email->getSubject();
        $expected = '50032266 CAR 11_MNPA00A01_9PTX_H00 ATT N° 1467829. pdf';
        TestCheck::assertString('D.13', 'Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0014');
        $email = Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Test message from Netscape Communicator 4.7';
        TestCheck::assertString('D.14', 'Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0015');
        $email = Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Up to $30 Off Multivitamins!';
        TestCheck::assertString('D.15', 'Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0016');
        $email = Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Test message with multiple From headers';
        TestCheck::assertString('D.16', 'Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        // TODO: causing problems
        //$content = getTestEmailContents('m0017');
        //$email = Email::parseText($content);
        //$actual = $email->getSubject();
        //$expected = '';
        //TestCheck::assertString('D.17', 'Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0018');
        $email = Email::parseText($content);
        $actual = $email->getSubject();
        $expected = '[Korea] Name';
        TestCheck::assertString('D.18', 'Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0019');
        $email = Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Re: Maya Ethnobotanicals - Emails';
        TestCheck::assertString('D.19', 'Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0020');
        $email = Email::parseText($content);
        $actual = $email->getSubject();
        $expected = '1';
        TestCheck::assertString('D.20', 'Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0021');
        $email = Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'occurs when divided into an array, and the last e of the array! Пут ін хуйло!!!!!!';
        TestCheck::assertString('D.21', 'Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0022');
        $email = Email::parseText($content);
        $actual = $email->getSubject();
        $expected = '[PRJ-OTH] asdf  árvíztűrő tükörfúrógép';
        TestCheck::assertString('D.22', 'Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0023');
        $email = Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'If you can read this you understand the example.';
        TestCheck::assertString('D.23', 'Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0024');
        $email = Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Persil, abeilles ...';
        TestCheck::assertString('D.24', 'Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0025');
        $email = Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Testing MIME E-mail composing with cid';
        TestCheck::assertString('D.25', 'Email::parseText(); get the "subject"', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('issue84');
        $email = Email::parseText($content);
        $actual = $email->getSubject();
        $expected = 'Re: [PHP-DEV] [RFC] Remove PHP 4 Constructors';
        TestCheck::assertString('D.26', 'Email::parseText(); get the "subject"', $actual, $expected, $results);



        // TEST: content parsing; "message text" information

        // BEGIN TEST
        $content = getTestEmailContents('m0001');
        $email = Email::parseText($content);
        $actual = $email->getMessageText();
        $expected = "\n";
        TestCheck::assertString('E.1', 'Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0002');
        $email = Email::parseText($content);
        $actual = $email->getMessageText();
        $expected = "\n";
        TestCheck::assertString('E.2', 'Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0003');
        $email = Email::parseText($content);
        $actual = $email->getMessageText();
        $expected = "\n";
        TestCheck::assertString('E.3', 'Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0004');
        $email = Email::parseText($content);
        $actual = $email->getMessageText();
        $expected = "\n";
        TestCheck::assertString('E.4', 'Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0005');
        $email = Email::parseText($content);
        $actual = $email->getMessageText();
        $expected = "\n";
        TestCheck::assertString('E.5', 'Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0006');
        $email = Email::parseText($content);
        $actual = $email->getMessageText();
        $expected = "\n";
        TestCheck::assertString('E.6', 'Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0007');
        $email = Email::parseText($content);
        $actual = $email->getMessageText();
        $expected = "\n";
        TestCheck::assertString('E.7', 'Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0008');
        $email = Email::parseText($content);
        $actual = $email->getMessageText();
        $expected = "This is an HTML message. Please use an HTML capable mail program to read\nthis message.\n";
        TestCheck::assertString('E.8', 'Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0009');
        $email = Email::parseText($content);
        $actual = substr($email->getMessageText(),100,50);
        $expected = "*********************************************\n \nBe";
        TestCheck::assertString('E.9', 'Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0010');
        $email = Email::parseText($content);
        $actual = $email->getMessageText();
        $expected = "\n";
        TestCheck::assertString('E.10', 'Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0011');
        $email = Email::parseText($content);
        $actual = $email->getMessageText();
        $expected = "Hello World !\nThis is a text body\n";
        TestCheck::assertString('E.11', 'Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0012');
        $email = Email::parseText($content);
        $actual = $email->getMessageText();
        $expected = "Hello World !\nThis is a text body\n";
        TestCheck::assertString('E.12', 'Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0013');
        $email = Email::parseText($content);
        $actual = substr($email->getMessageText(),100,50);
        $expected = "otre environnement.\nN'imprimez ce mail qu'en cas d";
        TestCheck::assertString('E.13', 'Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0014');
        $email = Email::parseText($content);
        $actual = substr($email->getMessageText(),300,50);
        $expected = "ben.\" \r\n\r\nIn einem nahen Teich wollten sie sich nu";
        TestCheck::assertString('E.14', 'Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0015');
        $email = Email::parseText($content);
        $actual = substr($email->getMessageText(),200,50);
        $expected = " best....\n\nThe good news is there are 5 key things";
        TestCheck::assertString('E.15', 'Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0016');
        $email = Email::parseText($content);
        $actual = substr($email->getMessageText(),300,50);
        $expected = "ben.\" \r\n\r\nIn einem nahen Teich wollten sie sich nu";
        TestCheck::assertString('E.16', 'Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        // TODO: causing problems
        //$content = getTestEmailContents('m0017');
        //$email = Email::parseText($content);
        //$actual = $email->getMessageText();
        //$expected = "";
        //TestCheck::assertString('E.17', 'Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0018');
        $email = Email::parseText($content);
        $actual = $email->getMessageText();
        $expected = "My traveling companions!\r\nMy friend, Le Pliage! \r\n\r\n";
        TestCheck::assertString('E.18', 'Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0019');
        $email = Email::parseText($content);
        $actual = substr($email->getMessageText(),200,50);
        $expected = "directly above this quoted-printable wrapper, than";
        TestCheck::assertString('E.19', 'Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0020');
        $email = Email::parseText($content);
        $actual = $email->getMessageText();
        $expected = "1\r\n\r\n";
        TestCheck::assertString('E.20', 'Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0021');
        $email = Email::parseText($content);
        $actual = $email->getMessageText();
        $expected = "mini plain body\n\n";
        TestCheck::assertString('E.21', 'Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0022');
        $email = Email::parseText($content);
        $actual = substr($email->getMessageText(),200,50);
        $expected = "directly above this quoted-printable wrapper, than";
        TestCheck::assertString('E.22', 'Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0023');
        $email = Email::parseText($content);
        $actual = substr($email->getMessageText(),200,50);
        $expected = "directly above this quoted-printable wrapper, than";
        TestCheck::assertString('E.23', 'Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0024');
        $email = Email::parseText($content);
        $actual = $email->getMessageText();
        $expected = "";
        TestCheck::assertString('E.24', 'Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0025');
        $email = Email::parseText($content);
        $actual = $email->getMessageText();
        $expected = "This is an HTML message. Please use an HTML capable mail program to read\nthis message.\n";
        TestCheck::assertString('E.25', 'Email::parseText(); get the message text', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('issue84');
        $email = Email::parseText($content);
        $actual = substr($email->getMessageText(),200,50);
        $expected = "as their defining class will no\n> longer be recogn";
        TestCheck::assertString('E.26', 'Email::parseText(); get the message text', $actual, $expected, $results);



        // TEST: content parsing; "message html" information

        // BEGIN TEST
        $content = getTestEmailContents('m0001');
        $email = Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 3;
        TestCheck::assertNumber('F.1', 'Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0002');
        $email = Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 3;
        TestCheck::assertNumber('F.2', 'Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0003');
        $email = Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 3;
        TestCheck::assertNumber('F.3', 'Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0004');
        $email = Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 3;
        TestCheck::assertNumber('F.4', 'Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0005');
        $email = Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 3;
        TestCheck::assertNumber('F.5', 'Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0006');
        $email = Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 3;
        TestCheck::assertNumber('F.6', 'Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0007');
        $email = Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 3;
        TestCheck::assertNumber('F.7', 'Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0008');
        $email = Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 37;
        TestCheck::assertNumber('F.8', 'Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0009');
        $email = Email::parseText($content);
        $actual = $email->getMessageHtml();
        $expected = "";
        TestCheck::assertString('F.9', 'Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0010');
        $email = Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 3;
        TestCheck::assertNumber('F.10', 'Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0011');
        $email = Email::parseText($content);
        $actual = $email->getMessageHtml();
        $expected = "";
        TestCheck::assertString('F.11', 'Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0012');
        $email = Email::parseText($content);
        $actual = $email->getMessageHtml();
        $expected = "";
        TestCheck::assertString('F.12', 'Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0013');
        $email = Email::parseText($content);
        $actual = $email->getMessageHtml();
        $expected = "";
        TestCheck::assertString('F.13', 'Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0014');
        $email = Email::parseText($content);
        $actual = $email->getMessageHtml();
        $expected = "";
        TestCheck::assertString('F.14', 'Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0015');
        $email = Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 424;
        TestCheck::assertNumber('F.15', 'Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0016');
        $email = Email::parseText($content);
        $actual = $email->getMessageHtml();
        $expected = "";
        TestCheck::assertString('F.16', 'Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        // TODO: causing problems
        //$content = getTestEmailContents('m0017');
        //$email = Email::parseText($content);
        //$actual = $email->getMessageHtml();
        //$expected = "";
        //TestCheck::assertString('F.17', 'Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0018');
        $email = Email::parseText($content);
        $actual = $email->getMessageHtml();
        $expected = "";
        TestCheck::assertString('F.18', 'Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0019');
        $email = Email::parseText($content);
        $actual = $email->getMessageHtml();
        $expected = "";
        TestCheck::assertString('F.19', 'Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0020');
        $email = Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 20;
        TestCheck::assertNumber('F.20', 'Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0021');
        $email = Email::parseText($content);
        $actual = $email->getMessageHtml();
        $expected = "";
        TestCheck::assertString('F.21', 'Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0022');
        $email = Email::parseText($content);
        $actual = $email->getMessageHtml();
        $expected = "";
        TestCheck::assertString('F.22', 'Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0023');
        $email = Email::parseText($content);
        $actual = $email->getMessageHtml();
        $expected = "";
        TestCheck::assertString('F.23', 'Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0024');
        $email = Email::parseText($content);
        $actual = $email->getMessageHtml();
        $expected = "";
        TestCheck::assertString('F.24', 'Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('m0025');
        $email = Email::parseText($content);
        $actual = substr_count($email->getMessageHtml(),"<");
        $expected = 37;
        TestCheck::assertNumber('F.25', 'Email::parseText(); get the message html', $actual, $expected, $results);

        // BEGIN TEST
        $content = getTestEmailContents('issue84');
        $email = Email::parseText($content);
        $actual = $email->getMessageHtml();
        $expected = "";
        TestCheck::assertString('F.26', 'Email::parseText(); get the message html', $actual, $expected, $results);
    }
}

