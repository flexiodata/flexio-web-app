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


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TODO: add email creation tests that include additional paramaters,
        // such as attachments

        // TEST: email creation

        // BEGIN TEST
        $email = \Flexio\Services\Email::create();
        $actual = get_class($email);
        $expected = 'Flexio\\Services\\Email';
        TestCheck::assertString('A.1', '\Flexio\Services\Email::create(); basic test', $actual, $expected, $results);

        // BEGIN TEST
        $email = \Flexio\Services\Email::create(false);
        $actual = get_class($email);
        $expected = 'Flexio\\Services\\Email';
        TestCheck::assertString('A.2', '\Flexio\Services\Email::create(); basic test', $actual, $expected, $results);

        // BEGIN TEST
        $email = \Flexio\Services\Email::create(array(
            'to' => "user@flex.io",
            'from' => "User via Flex.io <no-reply@flex.io>",
            'subject' => "A user wants to share something with you",
            'msg_text' => "Please join my project",
            'msg_html' => "<br>Please join my project<br>"
        ));
        $actual = $email->getTo();
        $expected = '["user@flex.io"]';
        TestCheck::assertArray('A.3', '\Flexio\Services\Email::create(); basic test', $actual, $expected, $results);

        // BEGIN TEST
        $email = \Flexio\Services\Email::create(array(
            'to' => "user@flex.io",
            'from' => "User via Flex.io <no-reply@flex.io>",
            'subject' => "A user wants to share something with you",
            'msg_text' => "Please join my project",
            'msg_html' => "<br>Please join my project<br>"
        ));
        $actual = $email->getFrom();
        $expected = '["User via Flex.io <no-reply@flex.io>"]';
        TestCheck::assertArray('A.4', '\Flexio\Services\Email::create(); basic test', $actual, $expected, $results);

        // BEGIN TEST
        $email = \Flexio\Services\Email::create(array(
            'to' => "user@flex.io",
            'from' => "User via Flex.io <no-reply@flex.io>",
            'subject' => "A user wants to share something with you",
            'msg_text' => "Please join my project",
            'msg_html' => "<br>Please join my project<br>"
        ));
        $actual = $email->getSubject();
        $expected = 'A user wants to share something with you';
        TestCheck::assertString('A.5', '\Flexio\Services\Email::create(); basic test', $actual, $expected, $results);

        // BEGIN TEST
        $email = \Flexio\Services\Email::create(array(
            'to' => "user@flex.io",
            'from' => "User via Flex.io <no-reply@flex.io>",
            'subject' => "A user wants to share something with you",
            'msg_text' => "Please join my project",
            'msg_html' => "<br>Please join my project<br>"
        ));
        $actual = $email->getMessageText();
        $expected = 'Please join my project';
        TestCheck::assertString('A.6', '\Flexio\Services\Email::create(); basic test', $actual, $expected, $results);

        // BEGIN TEST
        $email = \Flexio\Services\Email::create(array(
            'to' => "user@flex.io",
            'from' => "User via Flex.io <no-reply@flex.io>",
            'subject' => "A user wants to share something with you",
            'msg_text' => "Please join my project",
            'msg_html' => "<br>Please join my project<br>"
        ));
        $actual = $email->getMessageHtml();
        $expected = '<br>Please join my project<br>';
        TestCheck::assertString('A.7', '\Flexio\Services\Email::create(); basic test', $actual, $expected, $results);
    }
}
