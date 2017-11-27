<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-06-27
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
        // SETUP
        $model = TestUtil::getModel();


        // TEST: set/get email 'from'

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->getFrom();
        $expected = '[]';
        TestCheck::assertArray('A.1', '\Flexio\Services\Email::getFrom(); default value', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setFrom(123)->getFrom();
        $expected = '[]';
        TestCheck::assertArray('A.2', '\Flexio\Services\Email::setFrom(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setFrom('no-reply@flex.io')->getFrom();
        $expected = ['no-reply@flex.io'];
        TestCheck::assertArray('A.3', '\Flexio\Services\Email::setFrom(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setFrom(['no-reply@flex.io'])->getFrom();
        $expected = ['no-reply@flex.io'];
        TestCheck::assertArray('A.4', '\Flexio\Services\Email::setFrom(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setFrom(['no-reply@flex.io', 'info@flex.io'])->getFrom();
        $expected = ['no-reply@flex.io', 'info@flex.io'];
        TestCheck::assertArray('A.5', '\Flexio\Services\Email::setFrom(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setFrom([false, 'info@flex.io'])->getFrom();
        $expected = ['info@flex.io'];
        TestCheck::assertArray('A.6', '\Flexio\Services\Email::setFrom(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setFrom('  info@flex.io  ')->getFrom();
        $expected = ['info@flex.io'];
        TestCheck::assertArray('A.7', '\Flexio\Services\Email::setFrom(); trim input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setFrom(['  no-reply@flex.io  ', '  info@flex.io  '])->getFrom();
        $expected = ['no-reply@flex.io', 'info@flex.io'];
        TestCheck::assertArray('A.8', '\Flexio\Services\Email::setFrom(); trim input', $actual, $expected, $results);



        // TEST: set/get email 'to'

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->getTo();
        $expected = '[]';
        TestCheck::assertArray('B.1', '\Flexio\Services\Email::getTo(); default value', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setTo(123)->getTo();
        $expected = '[]';
        TestCheck::assertArray('B.2', '\Flexio\Services\Email::setTo(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setTo('no-reply@flex.io')->getTo();
        $expected = ['no-reply@flex.io'];
        TestCheck::assertArray('B.3', '\Flexio\Services\Email::setTo(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setTo(['no-reply@flex.io'])->getTo();
        $expected = ['no-reply@flex.io'];
        TestCheck::assertArray('B.4', '\Flexio\Services\Email::setTo(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setTo(['no-reply@flex.io', 'info@flex.io'])->getTo();
        $expected = ['no-reply@flex.io', 'info@flex.io'];
        TestCheck::assertArray('B.5', '\Flexio\Services\Email::setTo(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setTo([false, 'info@flex.io'])->getTo();
        $expected = ['info@flex.io'];
        TestCheck::assertArray('B.6', '\Flexio\Services\Email::setTo(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setTo('  info@flex.io  ')->getTo();
        $expected = ['info@flex.io'];
        TestCheck::assertArray('B.7', '\Flexio\Services\Email::setTo(); trim input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setTo(['  no-reply@flex.io  ', '  info@flex.io  '])->getTo();
        $expected = ['no-reply@flex.io', 'info@flex.io'];
        TestCheck::assertArray('B.8', '\Flexio\Services\Email::setTo(); trim input', $actual, $expected, $results);



        // TEST: set/get email 'cc'

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->getCC();
        $expected = '[]';
        TestCheck::assertArray('C.1', '\Flexio\Services\Email::getCC(); default value', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setCC(123)->getCC();
        $expected = '[]';
        TestCheck::assertArray('C.2', '\Flexio\Services\Email::setCC(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setCC('no-reply@flex.io')->getCC();
        $expected = ['no-reply@flex.io'];
        TestCheck::assertArray('C.3', '\Flexio\Services\Email::setCC(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setCC(['no-reply@flex.io'])->getCC();
        $expected = ['no-reply@flex.io'];
        TestCheck::assertArray('C.4', '\Flexio\Services\Email::setCC(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setCC(['no-reply@flex.io', 'info@flex.io'])->getCC();
        $expected = ['no-reply@flex.io', 'info@flex.io'];
        TestCheck::assertArray('C.5', '\Flexio\Services\Email::setCC(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setCC([false, 'info@flex.io'])->getCC();
        $expected = ['info@flex.io'];
        TestCheck::assertArray('C.6', '\Flexio\Services\Email::setCC(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setCC('  info@flex.io  ')->getCC();
        $expected = ['info@flex.io'];
        TestCheck::assertArray('C.7', '\Flexio\Services\Email::setCC(); trim input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setCC(['  no-reply@flex.io  ', '  info@flex.io  '])->getCC();
        $expected = ['no-reply@flex.io', 'info@flex.io'];
        TestCheck::assertArray('C.8', '\Flexio\Services\Email::setCC(); trim input', $actual, $expected, $results);



        // TEST: set/get email 'bcc'

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->getBCC();
        $expected = '[]';
        TestCheck::assertArray('D.1', '\Flexio\Services\Email::getBCC(); default value', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setBCC(123)->getBCC();
        $expected = '[]';
        TestCheck::assertArray('D.2', '\Flexio\Services\Email::setBCC(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setBCC('no-reply@flex.io')->getBCC();
        $expected = ['no-reply@flex.io'];
        TestCheck::assertArray('D.3', '\Flexio\Services\Email::setBCC(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setBCC(['no-reply@flex.io'])->getBCC();
        $expected = ['no-reply@flex.io'];
        TestCheck::assertArray('D.4', '\Flexio\Services\Email::setBCC(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setBCC(['no-reply@flex.io', 'info@flex.io'])->getBCC();
        $expected = ['no-reply@flex.io', 'info@flex.io'];
        TestCheck::assertArray('D.5', '\Flexio\Services\Email::setBCC(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setBCC([false, 'info@flex.io'])->getBCC();
        $expected = ['info@flex.io'];
        TestCheck::assertArray('D.6', '\Flexio\Services\Email::setBCC(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setBCC('  info@flex.io  ')->getBCC();
        $expected = ['info@flex.io'];
        TestCheck::assertArray('D.7', '\Flexio\Services\Email::setBCC(); trim input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setBCC(['  no-reply@flex.io  ', '  info@flex.io  '])->getBCC();
        $expected = ['no-reply@flex.io', 'info@flex.io'];
        TestCheck::assertArray('D.8', '\Flexio\Services\Email::setBCC(); trim input', $actual, $expected, $results);



        // TEST: set/get email 'replyto'

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->getReplyTo();
        $expected = '[]';
        TestCheck::assertArray('E.1', '\Flexio\Services\Email::getReplyTo(); default value', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setReplyTo(123)->getReplyTo();
        $expected = '[]';
        TestCheck::assertArray('E.2', '\Flexio\Services\Email::setReplyTo(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setReplyTo('no-reply@flex.io')->getReplyTo();
        $expected = ['no-reply@flex.io'];
        TestCheck::assertArray('E.3', '\Flexio\Services\Email::setReplyTo(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setReplyTo(['no-reply@flex.io'])->getReplyTo();
        $expected = ['no-reply@flex.io'];
        TestCheck::assertArray('E.4', '\Flexio\Services\Email::setReplyTo(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setReplyTo(['no-reply@flex.io', 'info@flex.io'])->getReplyTo();
        $expected = ['no-reply@flex.io', 'info@flex.io'];
        TestCheck::assertArray('E.5', '\Flexio\Services\Email::setReplyTo(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setReplyTo([false, 'info@flex.io'])->getReplyTo();
        $expected = ['info@flex.io'];
        TestCheck::assertArray('E.6', '\Flexio\Services\Email::setReplyTo(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setReplyTo('  info@flex.io  ')->getReplyTo();
        $expected = ['info@flex.io'];
        TestCheck::assertArray('E.7', '\Flexio\Services\Email::setReplyTo(); trim input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setReplyTo(['  no-reply@flex.io  ', '  info@flex.io  '])->getReplyTo();
        $expected = ['no-reply@flex.io', 'info@flex.io'];
        TestCheck::assertArray('E.8', '\Flexio\Services\Email::setReplyTo(); trim input', $actual, $expected, $results);



        // TEST: set/get email 'subject'

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->getSubject();
        $expected = '';
        TestCheck::assertString('F.1', '\Flexio\Services\Email::getSubject(); default value', $actual, $expected, $results);

        // BEGIN TEST
        try
        {
            \Flexio\Services\Email::create()->setSubject(123)->getSubject();
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('F.2', '\Flexio\Services\Email::setSubject(); set parameter based on string', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setSubject('Sign up for Flex.io!')->getSubject();
        $expected = 'Sign up for Flex.io!';
        TestCheck::assertString('F.3', '\Flexio\Services\Email::setSubject(); set parameter based on string', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setSubject('  Sign up for Flex.io!  ')->getSubject();
        $expected = '  Sign up for Flex.io!  ';
        TestCheck::assertString('F.4', '\Flexio\Services\Email::setSubject(); set parameter based on string; preserve leading/trailing whitespace', $actual, $expected, $results);



        // TEST: set/get email 'text message'

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->getMessageText();
        $expected = '';
        TestCheck::assertString('G.1', '\Flexio\Services\Email::getMessageText(); default value', $actual, $expected, $results);

        // BEGIN TEST
        try
        {
            \Flexio\Services\Email::create()->setMessageText(123)->getMessageText();
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('G.2', '\Flexio\Services\Email::setMessageText(); set parameter based on string', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setMessageText('Hello <first_name>, Thank you for signing up for Flex.io!')->getMessageText();
        $expected = 'Hello <first_name>, Thank you for signing up for Flex.io!';
        TestCheck::assertString('G.3', '\Flexio\Services\Email::setMessageText(); set parameter based on string', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setMessageText('  Hello <first_name>, Thank you for signing up for Flex.io!  ')->getMessageText();
        $expected = '  Hello <first_name>, Thank you for signing up for Flex.io!  ';
        TestCheck::assertString('G.4', '\Flexio\Services\Email::setMessageText(); set parameter based on string; preserve leading/trailing whitespace', $actual, $expected, $results);



        // TEST: set/get email 'html message'

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->getMessageHtml();
        $expected = '';
        TestCheck::assertString('H.1', '\Flexio\Services\Email::getMessageHtml(); default value', $actual, $expected, $results);

        // BEGIN TEST
        try
        {
            \Flexio\Services\Email::create()->setMessageHtml(123)->getMessageHtml();
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('H.2', '\Flexio\Services\Email::setMessageHtml(); set parameter based on string', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setMessageHtml('Hello <first_name>, Thank you for signing up for Flex.io!')->getMessageHtml();
        $expected = 'Hello <first_name>, Thank you for signing up for Flex.io!';
        TestCheck::assertString('H.3', '\Flexio\Services\Email::setMessageHtml(); set parameter based on string', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setMessageHtml('  Hello <first_name>, Thank you for signing up for Flex.io!  ')->getMessageHtml();
        $expected = '  Hello <first_name>, Thank you for signing up for Flex.io!  ';
        TestCheck::assertString('H.4', '\Flexio\Services\Email::setMessageHtml(); set parameter based on string; preserve leading/trailing whitespace', $actual, $expected, $results);



       // TEST: set/get email 'html message'

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->getMessageHtmlEmbedded();
        $expected = '';
        TestCheck::assertString('I.1', '\Flexio\Services\Email::getMessageHtml(); default value', $actual, $expected, $results);

        // BEGIN TEST
        try
        {
            \Flexio\Services\Email::create()->setMessageHtmlEmbedded(123)->getMessageHtmlEmbedded();
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('I.2', '\Flexio\Services\Email::setMessageHtmlEmbedded(); set parameter based on string', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setMessageHtmlEmbedded('Hello <first_name>, Thank you for signing up for Flex.io!')->getMessageHtmlEmbedded();
        $expected = 'Hello <first_name>, Thank you for signing up for Flex.io!';
        TestCheck::assertString('I.3', '\Flexio\Services\Email::setMessageHtmlEmbedded(); set parameter based on string', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->setMessageHtmlEmbedded('  Hello <first_name>, Thank you for signing up for Flex.io!  ')->getMessageHtmlEmbedded();
        $expected = '  Hello <first_name>, Thank you for signing up for Flex.io!  ';
        TestCheck::assertString('I.4', '\Flexio\Services\Email::setMessageHtmlEmbedded(); set parameter based on string; preserve leading/trailing whitespace', $actual, $expected, $results);



       // TEST: add/get/clear email attachments

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->getAttachments();
        $expected = '[]';
        TestCheck::assertArray('J.1', '\Flexio\Services\Email::getAttachments(); default value', $actual, $expected, $results);

        // BEGIN TEST
        try
        {
            \Flexio\Services\Email::create()->addAttachment(123)->getAttachments();
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('J.2', '\Flexio\Services\Email::getAttachments(); don\'t add invalid attachments', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->addAttachment(array())->getAttachments();
        $expected = '[{"name":"","file":"","mime_type":"","content":""}]';
        TestCheck::assertArray('J.3', '\Flexio\Services\Email::getAttachments(); supply default attachment parameters if input doesn\'t supply them', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->addAttachment(array("name"=>"a"))->getAttachments();
        $expected = '[{"name":"a","file":"","mime_type":"","content":""}]';
        TestCheck::assertArray('J.4', '\Flexio\Services\Email::getAttachments(); supply default attachment parameters if input doesn\'t supply them', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->addAttachment(array("mime_type"=>"a"))->getAttachments();
        $expected = '[{"name":"","file":"","mime_type":"a","content":""}]';
        TestCheck::assertArray('J.5', '\Flexio\Services\Email::getAttachments(); supply default attachment parameters if input doesn\'t supply them', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->addAttachment(array("content"=>"a"))->getAttachments();
        $expected = '[{"name":"","file":"","mime_type":"","content":"a"}]';
        TestCheck::assertArray('J.6', '\Flexio\Services\Email::getAttachments(); supply default attachment parameters if input doesn\'t supply them', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->addAttachment(array("content"=>"a","attribute"=>"b"))->getAttachments();
        $expected = '[{"name":"","file":"","mime_type":"","content":"a"}]';
        TestCheck::assertArray('J.7', '\Flexio\Services\Email::getAttachments(); don\'t save attachment parameters we don\'t care about', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->addAttachment(array("name"=>"a","file"=>"f","mime_type"=>"b","content"=>"c"))
                                 ->addAttachment(array("name"=>"d","file"=>"f","mime_type"=>"e","content"=>"f"))
                                 ->getAttachments();
        $expected = '[{"name":"a","file":"f","mime_type":"b","content":"c"},{"name":"d","file":"f","mime_type":"e","content":"f"}]';
        TestCheck::assertArray('J.8', '\Flexio\Services\Email::getAttachments(); allow multiple attachments', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->addAttachment(array("name"=>"a","file"=>"f","mime_type"=>"b","content"=>"c"))
                                 ->addAttachment(array("name"=>"d","file"=>"f","mime_type"=>"e","content"=>"f"))
                                 ->clearAttachments()
                                 ->getAttachments();
        $expected = '[]';
        TestCheck::assertArray('J.9', '\Flexio\Services\Email::getAttachments(); make sure clear function removes previously added attachments', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::create()->addAttachment(array("name"=>"a","file"=>"f","mime_type"=>"b","content"=>"c"))
                                 ->clearAttachments()
                                 ->addAttachment(array("name"=>"d","file"=>"f","mime_type"=>"e","content"=>"f"))
                                 ->getAttachments();
        $expected = '[{"name":"d","file":"f","mime_type":"e","content":"f"}]';
        TestCheck::assertArray('J.10', '\Flexio\Services\Email::getAttachments(); make sure clear function removes previously added attachments', $actual, $expected, $results);
    }
}
