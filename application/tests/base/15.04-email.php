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


class Test
{
    public function run(&$results)
    {
        // TODO: add email creation tests that include additional paramaters,
        // such as attachments

        // TEST: email creation

        // BEGIN TEST
        $email = \Flexio\Services\NoticeEmail::create();
        $actual = get_class($email);
        $expected = 'Flexio\Services\NoticeEmail';
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Services\NoticeEmail::create(); basic test', $actual, $expected, $results);

        // BEGIN TEST
        try
        {
            $email = \Flexio\Services\NoticeEmail::create(false);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.2', '\Flexio\Services\NoticeEmail::create(); basic test', $actual, $expected, $results);

        // BEGIN TEST
        $email = \Flexio\Services\NoticeEmail::create(array(
            'to' => "user@flex.io",
            'from' => "User via Flex.io <no-reply@flex.io>",
            'subject' => "A user wants to share something with you",
            'msg_text' => "Please join my project",
            'msg_html' => "<br>Please join my project<br>"
        ));
        $actual = $email->getTo();
        $expected = '["user@flex.io"]';
        \Flexio\Tests\Check::assertArray('A.3', '\Flexio\Services\NoticeEmail::create(); basic test', $actual, $expected, $results);

        // BEGIN TEST
        $email = \Flexio\Services\NoticeEmail::create(array(
            'to' => "user@flex.io",
            'from' => "User via Flex.io <no-reply@flex.io>",
            'subject' => "A user wants to share something with you",
            'msg_text' => "Please join my project",
            'msg_html' => "<br>Please join my project<br>"
        ));
        $actual = $email->getFrom();
        $expected = '["User via Flex.io <no-reply@flex.io>"]';
        \Flexio\Tests\Check::assertArray('A.4', '\Flexio\Services\NoticeEmail::create(); basic test', $actual, $expected, $results);

        // BEGIN TEST
        $email = \Flexio\Services\NoticeEmail::create(array(
            'to' => "user@flex.io",
            'from' => "User via Flex.io <no-reply@flex.io>",
            'subject' => "A user wants to share something with you",
            'msg_text' => "Please join my project",
            'msg_html' => "<br>Please join my project<br>"
        ));
        $actual = $email->getSubject();
        $expected = 'A user wants to share something with you';
        \Flexio\Tests\Check::assertString('A.5', '\Flexio\Services\NoticeEmail::create(); basic test', $actual, $expected, $results);

        // BEGIN TEST
        $email = \Flexio\Services\NoticeEmail::create(array(
            'to' => "user@flex.io",
            'from' => "User via Flex.io <no-reply@flex.io>",
            'subject' => "A user wants to share something with you",
            'msg_text' => "Please join my project",
            'msg_html' => "<br>Please join my project<br>"
        ));
        $actual = $email->getMessageText();
        $expected = 'Please join my project';
        \Flexio\Tests\Check::assertString('A.6', '\Flexio\Services\NoticeEmail::create(); basic test', $actual, $expected, $results);

        // BEGIN TEST
        $email = \Flexio\Services\NoticeEmail::create(array(
            'to' => "user@flex.io",
            'from' => "User via Flex.io <no-reply@flex.io>",
            'subject' => "A user wants to share something with you",
            'msg_text' => "Please join my project",
            'msg_html' => "<br>Please join my project<br>"
        ));
        $actual = $email->getMessageHtml();
        $expected = '<br>Please join my project<br>';
        \Flexio\Tests\Check::assertString('A.7', '\Flexio\Services\NoticeEmail::create(); basic test', $actual, $expected, $results);
    }
}
