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
        // TEST: set/get email 'from'

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->getFrom();
        $expected = '[]';
        \Flexio\Tests\Check::assertArray('A.1', '\Flexio\Services\NoticeEmail::getFrom(); default value', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setFrom(123)->getFrom();
        $expected = '[]';
        \Flexio\Tests\Check::assertArray('A.2', '\Flexio\Services\NoticeEmail::setFrom(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setFrom('no-reply@flex.io')->getFrom();
        $expected = ['no-reply@flex.io'];
        \Flexio\Tests\Check::assertArray('A.3', '\Flexio\Services\NoticeEmail::setFrom(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setFrom(['no-reply@flex.io'])->getFrom();
        $expected = ['no-reply@flex.io'];
        \Flexio\Tests\Check::assertArray('A.4', '\Flexio\Services\NoticeEmail::setFrom(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setFrom(['no-reply@flex.io', 'info@flex.io'])->getFrom();
        $expected = ['no-reply@flex.io', 'info@flex.io'];
        \Flexio\Tests\Check::assertArray('A.5', '\Flexio\Services\NoticeEmail::setFrom(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setFrom([false, 'info@flex.io'])->getFrom();
        $expected = ['info@flex.io'];
        \Flexio\Tests\Check::assertArray('A.6', '\Flexio\Services\NoticeEmail::setFrom(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setFrom('  info@flex.io  ')->getFrom();
        $expected = ['info@flex.io'];
        \Flexio\Tests\Check::assertArray('A.7', '\Flexio\Services\NoticeEmail::setFrom(); trim input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setFrom(['  no-reply@flex.io  ', '  info@flex.io  '])->getFrom();
        $expected = ['no-reply@flex.io', 'info@flex.io'];
        \Flexio\Tests\Check::assertArray('A.8', '\Flexio\Services\NoticeEmail::setFrom(); trim input', $actual, $expected, $results);



        // TEST: set/get email 'to'

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->getTo();
        $expected = '[]';
        \Flexio\Tests\Check::assertArray('B.1', '\Flexio\Services\NoticeEmail::getTo(); default value', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setTo(123)->getTo();
        $expected = '[]';
        \Flexio\Tests\Check::assertArray('B.2', '\Flexio\Services\NoticeEmail::setTo(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setTo('no-reply@flex.io')->getTo();
        $expected = ['no-reply@flex.io'];
        \Flexio\Tests\Check::assertArray('B.3', '\Flexio\Services\NoticeEmail::setTo(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setTo(['no-reply@flex.io'])->getTo();
        $expected = ['no-reply@flex.io'];
        \Flexio\Tests\Check::assertArray('B.4', '\Flexio\Services\NoticeEmail::setTo(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setTo(['no-reply@flex.io', 'info@flex.io'])->getTo();
        $expected = ['no-reply@flex.io', 'info@flex.io'];
        \Flexio\Tests\Check::assertArray('B.5', '\Flexio\Services\NoticeEmail::setTo(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setTo([false, 'info@flex.io'])->getTo();
        $expected = ['info@flex.io'];
        \Flexio\Tests\Check::assertArray('B.6', '\Flexio\Services\NoticeEmail::setTo(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setTo('  info@flex.io  ')->getTo();
        $expected = ['info@flex.io'];
        \Flexio\Tests\Check::assertArray('B.7', '\Flexio\Services\NoticeEmail::setTo(); trim input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setTo(['  no-reply@flex.io  ', '  info@flex.io  '])->getTo();
        $expected = ['no-reply@flex.io', 'info@flex.io'];
        \Flexio\Tests\Check::assertArray('B.8', '\Flexio\Services\NoticeEmail::setTo(); trim input', $actual, $expected, $results);



        // TEST: set/get email 'cc'

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->getCC();
        $expected = '[]';
        \Flexio\Tests\Check::assertArray('C.1', '\Flexio\Services\NoticeEmail::getCC(); default value', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setCC(123)->getCC();
        $expected = '[]';
        \Flexio\Tests\Check::assertArray('C.2', '\Flexio\Services\NoticeEmail::setCC(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setCC('no-reply@flex.io')->getCC();
        $expected = ['no-reply@flex.io'];
        \Flexio\Tests\Check::assertArray('C.3', '\Flexio\Services\NoticeEmail::setCC(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setCC(['no-reply@flex.io'])->getCC();
        $expected = ['no-reply@flex.io'];
        \Flexio\Tests\Check::assertArray('C.4', '\Flexio\Services\NoticeEmail::setCC(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setCC(['no-reply@flex.io', 'info@flex.io'])->getCC();
        $expected = ['no-reply@flex.io', 'info@flex.io'];
        \Flexio\Tests\Check::assertArray('C.5', '\Flexio\Services\NoticeEmail::setCC(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setCC([false, 'info@flex.io'])->getCC();
        $expected = ['info@flex.io'];
        \Flexio\Tests\Check::assertArray('C.6', '\Flexio\Services\NoticeEmail::setCC(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setCC('  info@flex.io  ')->getCC();
        $expected = ['info@flex.io'];
        \Flexio\Tests\Check::assertArray('C.7', '\Flexio\Services\NoticeEmail::setCC(); trim input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setCC(['  no-reply@flex.io  ', '  info@flex.io  '])->getCC();
        $expected = ['no-reply@flex.io', 'info@flex.io'];
        \Flexio\Tests\Check::assertArray('C.8', '\Flexio\Services\NoticeEmail::setCC(); trim input', $actual, $expected, $results);



        // TEST: set/get email 'bcc'

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->getBCC();
        $expected = '[]';
        \Flexio\Tests\Check::assertArray('D.1', '\Flexio\Services\NoticeEmail::getBCC(); default value', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setBCC(123)->getBCC();
        $expected = '[]';
        \Flexio\Tests\Check::assertArray('D.2', '\Flexio\Services\NoticeEmail::setBCC(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setBCC('no-reply@flex.io')->getBCC();
        $expected = ['no-reply@flex.io'];
        \Flexio\Tests\Check::assertArray('D.3', '\Flexio\Services\NoticeEmail::setBCC(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setBCC(['no-reply@flex.io'])->getBCC();
        $expected = ['no-reply@flex.io'];
        \Flexio\Tests\Check::assertArray('D.4', '\Flexio\Services\NoticeEmail::setBCC(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setBCC(['no-reply@flex.io', 'info@flex.io'])->getBCC();
        $expected = ['no-reply@flex.io', 'info@flex.io'];
        \Flexio\Tests\Check::assertArray('D.5', '\Flexio\Services\NoticeEmail::setBCC(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setBCC([false, 'info@flex.io'])->getBCC();
        $expected = ['info@flex.io'];
        \Flexio\Tests\Check::assertArray('D.6', '\Flexio\Services\NoticeEmail::setBCC(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setBCC('  info@flex.io  ')->getBCC();
        $expected = ['info@flex.io'];
        \Flexio\Tests\Check::assertArray('D.7', '\Flexio\Services\NoticeEmail::setBCC(); trim input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setBCC(['  no-reply@flex.io  ', '  info@flex.io  '])->getBCC();
        $expected = ['no-reply@flex.io', 'info@flex.io'];
        \Flexio\Tests\Check::assertArray('D.8', '\Flexio\Services\NoticeEmail::setBCC(); trim input', $actual, $expected, $results);



        // TEST: set/get email 'replyto'

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->getReplyTo();
        $expected = '[]';
        \Flexio\Tests\Check::assertArray('E.1', '\Flexio\Services\NoticeEmail::getReplyTo(); default value', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setReplyTo(123)->getReplyTo();
        $expected = '[]';
        \Flexio\Tests\Check::assertArray('E.2', '\Flexio\Services\NoticeEmail::setReplyTo(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setReplyTo('no-reply@flex.io')->getReplyTo();
        $expected = ['no-reply@flex.io'];
        \Flexio\Tests\Check::assertArray('E.3', '\Flexio\Services\NoticeEmail::setReplyTo(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setReplyTo(['no-reply@flex.io'])->getReplyTo();
        $expected = ['no-reply@flex.io'];
        \Flexio\Tests\Check::assertArray('E.4', '\Flexio\Services\NoticeEmail::setReplyTo(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setReplyTo(['no-reply@flex.io', 'info@flex.io'])->getReplyTo();
        $expected = ['no-reply@flex.io', 'info@flex.io'];
        \Flexio\Tests\Check::assertArray('E.5', '\Flexio\Services\NoticeEmail::setReplyTo(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setReplyTo([false, 'info@flex.io'])->getReplyTo();
        $expected = ['info@flex.io'];
        \Flexio\Tests\Check::assertArray('E.6', '\Flexio\Services\NoticeEmail::setReplyTo(); set parameter based on string or array with strings', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setReplyTo('  info@flex.io  ')->getReplyTo();
        $expected = ['info@flex.io'];
        \Flexio\Tests\Check::assertArray('E.7', '\Flexio\Services\NoticeEmail::setReplyTo(); trim input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setReplyTo(['  no-reply@flex.io  ', '  info@flex.io  '])->getReplyTo();
        $expected = ['no-reply@flex.io', 'info@flex.io'];
        \Flexio\Tests\Check::assertArray('E.8', '\Flexio\Services\NoticeEmail::setReplyTo(); trim input', $actual, $expected, $results);



        // TEST: set/get email 'subject'

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->getSubject();
        $expected = '';
        \Flexio\Tests\Check::assertString('F.1', '\Flexio\Services\NoticeEmail::getSubject(); default value', $actual, $expected, $results);

        // BEGIN TEST
        try
        {
            \Flexio\Services\NoticeEmail::create()->setSubject(123)->getSubject();
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('F.2', '\Flexio\Services\NoticeEmail::setSubject(); set parameter based on string', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setSubject('Sign up for Flex.io!')->getSubject();
        $expected = 'Sign up for Flex.io!';
        \Flexio\Tests\Check::assertString('F.3', '\Flexio\Services\NoticeEmail::setSubject(); set parameter based on string', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setSubject('  Sign up for Flex.io!  ')->getSubject();
        $expected = '  Sign up for Flex.io!  ';
        \Flexio\Tests\Check::assertString('F.4', '\Flexio\Services\NoticeEmail::setSubject(); set parameter based on string; preserve leading/trailing whitespace', $actual, $expected, $results);



        // TEST: set/get email 'text message'

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->getMessageText();
        $expected = '';
        \Flexio\Tests\Check::assertString('G.1', '\Flexio\Services\NoticeEmail::getMessageText(); default value', $actual, $expected, $results);

        // BEGIN TEST
        try
        {
            \Flexio\Services\NoticeEmail::create()->setMessageText(123)->getMessageText();
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('G.2', '\Flexio\Services\NoticeEmail::setMessageText(); set parameter based on string', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setMessageText('Hello <first_name>, Thank you for signing up for Flex.io!')->getMessageText();
        $expected = 'Hello <first_name>, Thank you for signing up for Flex.io!';
        \Flexio\Tests\Check::assertString('G.3', '\Flexio\Services\NoticeEmail::setMessageText(); set parameter based on string', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setMessageText('  Hello <first_name>, Thank you for signing up for Flex.io!  ')->getMessageText();
        $expected = '  Hello <first_name>, Thank you for signing up for Flex.io!  ';
        \Flexio\Tests\Check::assertString('G.4', '\Flexio\Services\NoticeEmail::setMessageText(); set parameter based on string; preserve leading/trailing whitespace', $actual, $expected, $results);



        // TEST: set/get email 'html message'

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->getMessageHtml();
        $expected = '';
        \Flexio\Tests\Check::assertString('H.1', '\Flexio\Services\NoticeEmail::getMessageHtml(); default value', $actual, $expected, $results);

        // BEGIN TEST
        try
        {
            \Flexio\Services\NoticeEmail::create()->setMessageHtml(123)->getMessageHtml();
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('H.2', '\Flexio\Services\NoticeEmail::setMessageHtml(); set parameter based on string', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setMessageHtml('Hello <first_name>, Thank you for signing up for Flex.io!')->getMessageHtml();
        $expected = 'Hello <first_name>, Thank you for signing up for Flex.io!';
        \Flexio\Tests\Check::assertString('H.3', '\Flexio\Services\NoticeEmail::setMessageHtml(); set parameter based on string', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setMessageHtml('  Hello <first_name>, Thank you for signing up for Flex.io!  ')->getMessageHtml();
        $expected = '  Hello <first_name>, Thank you for signing up for Flex.io!  ';
        \Flexio\Tests\Check::assertString('H.4', '\Flexio\Services\NoticeEmail::setMessageHtml(); set parameter based on string; preserve leading/trailing whitespace', $actual, $expected, $results);



       // TEST: set/get email 'html message'

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->getMessageHtmlEmbedded();
        $expected = '';
        \Flexio\Tests\Check::assertString('I.1', '\Flexio\Services\NoticeEmail::getMessageHtml(); default value', $actual, $expected, $results);

        // BEGIN TEST
        try
        {
            \Flexio\Services\NoticeEmail::create()->setMessageHtmlEmbedded(123)->getMessageHtmlEmbedded();
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('I.2', '\Flexio\Services\NoticeEmail::setMessageHtmlEmbedded(); set parameter based on string', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setMessageHtmlEmbedded('Hello <first_name>, Thank you for signing up for Flex.io!')->getMessageHtmlEmbedded();
        $expected = 'Hello <first_name>, Thank you for signing up for Flex.io!';
        \Flexio\Tests\Check::assertString('I.3', '\Flexio\Services\NoticeEmail::setMessageHtmlEmbedded(); set parameter based on string', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->setMessageHtmlEmbedded('  Hello <first_name>, Thank you for signing up for Flex.io!  ')->getMessageHtmlEmbedded();
        $expected = '  Hello <first_name>, Thank you for signing up for Flex.io!  ';
        \Flexio\Tests\Check::assertString('I.4', '\Flexio\Services\NoticeEmail::setMessageHtmlEmbedded(); set parameter based on string; preserve leading/trailing whitespace', $actual, $expected, $results);



       // TEST: add/get/clear email attachments

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->getAttachments();
        $expected = '[]';
        \Flexio\Tests\Check::assertArray('J.1', '\Flexio\Services\NoticeEmail::getAttachments(); default value', $actual, $expected, $results);

        // BEGIN TEST
        try
        {
            \Flexio\Services\NoticeEmail::create()->addAttachment(123)->getAttachments();
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('J.2', '\Flexio\Services\NoticeEmail::getAttachments(); don\'t add invalid attachments', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->addAttachment(array())->getAttachments();
        $expected = '[{"name":"","file":"","mime_type":"","content":""}]';
        \Flexio\Tests\Check::assertArray('J.3', '\Flexio\Services\NoticeEmail::getAttachments(); supply default attachment parameters if input doesn\'t supply them', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->addAttachment(array("name"=>"a"))->getAttachments();
        $expected = '[{"name":"a","file":"","mime_type":"","content":""}]';
        \Flexio\Tests\Check::assertArray('J.4', '\Flexio\Services\NoticeEmail::getAttachments(); supply default attachment parameters if input doesn\'t supply them', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->addAttachment(array("mime_type"=>"a"))->getAttachments();
        $expected = '[{"name":"","file":"","mime_type":"a","content":""}]';
        \Flexio\Tests\Check::assertArray('J.5', '\Flexio\Services\NoticeEmail::getAttachments(); supply default attachment parameters if input doesn\'t supply them', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->addAttachment(array("content"=>"a"))->getAttachments();
        $expected = '[{"name":"","file":"","mime_type":"","content":"a"}]';
        \Flexio\Tests\Check::assertArray('J.6', '\Flexio\Services\NoticeEmail::getAttachments(); supply default attachment parameters if input doesn\'t supply them', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->addAttachment(array("content"=>"a","attribute"=>"b"))->getAttachments();
        $expected = '[{"name":"","file":"","mime_type":"","content":"a"}]';
        \Flexio\Tests\Check::assertArray('J.7', '\Flexio\Services\NoticeEmail::getAttachments(); don\'t save attachment parameters we don\'t care about', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->addAttachment(array("name"=>"a","file"=>"f","mime_type"=>"b","content"=>"c"))
                                 ->addAttachment(array("name"=>"d","file"=>"f","mime_type"=>"e","content"=>"f"))
                                 ->getAttachments();
        $expected = '[{"name":"a","file":"f","mime_type":"b","content":"c"},{"name":"d","file":"f","mime_type":"e","content":"f"}]';
        \Flexio\Tests\Check::assertArray('J.8', '\Flexio\Services\NoticeEmail::getAttachments(); allow multiple attachments', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->addAttachment(array("name"=>"a","file"=>"f","mime_type"=>"b","content"=>"c"))
                                 ->addAttachment(array("name"=>"d","file"=>"f","mime_type"=>"e","content"=>"f"))
                                 ->clearAttachments()
                                 ->getAttachments();
        $expected = '[]';
        \Flexio\Tests\Check::assertArray('J.9', '\Flexio\Services\NoticeEmail::getAttachments(); make sure clear function removes previously added attachments', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\NoticeEmail::create()->addAttachment(array("name"=>"a","file"=>"f","mime_type"=>"b","content"=>"c"))
                                 ->clearAttachments()
                                 ->addAttachment(array("name"=>"d","file"=>"f","mime_type"=>"e","content"=>"f"))
                                 ->getAttachments();
        $expected = '[{"name":"d","file":"f","mime_type":"e","content":"f"}]';
        \Flexio\Tests\Check::assertArray('J.10', '\Flexio\Services\NoticeEmail::getAttachments(); make sure clear function removes previously added attachments', $actual, $expected, $results);
    }
}
