<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-06-27
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
        // TEST: Message object constant tests

        // BEGIN TEST
        $actual = \Flexio\Api\Message::TYPE_EMAIL_WELCOME;
        $expected = 'email_welcome';
        \Flexio\Tests\Check::assertString('A.1', 'Message error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Api\Message::TYPE_EMAIL_RESET_PASSWORD;
        $expected = 'email_reset_password';
        \Flexio\Tests\Check::assertString('A.2', 'Message error constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Api\Message::TYPE_EMAIL_SHARE_PIPE;
        $expected = 'email_share_pipe';
        \Flexio\Tests\Check::assertString('A.3', 'Message error constant',  $actual, $expected, $results);
    }
}
