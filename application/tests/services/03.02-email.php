<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-06-17
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // note: more extensive tests for email are included in tests of \Flexio\Base\Util::isValidEmail()

        // TEST: email should be a string

        // BEGIN TEST
        $actual = \Flexio\Services\Email::isValid('user@flex.io');
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Flexio\Services\Email::isValid(); email should be a string', $actual, $expected, $results);



        // TEST: email should be less than 255 chars

        // BEGIN TEST
        $actual = \Flexio\Services\Email::isValid('a@b.com');
        $expected = true; // 7 chars
        TestCheck::assertBoolean('B.1', '\Flexio\Services\Email::isValid(); email must not be longer than 254 chars', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::isValid('email@xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxx.com');
        $expected = true; // 254 chars
        TestCheck::assertBoolean('B.2', '\Flexio\Services\Email::isValid(); email must not be longer than 254 chars', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::isValid('email@xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxx.com');
        $expected = false; // 255 chars
        TestCheck::assertBoolean('B.3', '\Flexio\Services\Email::isValid(); email must not be longer than 254 chars', $actual, $expected, $results);



        // TEST: email should contain an @ somewhere

        // BEGIN TEST
        $actual = \Flexio\Services\Email::isValid('name.domain.com');
        $expected = false;
        TestCheck::assertBoolean('C.1', '\Flexio\Services\Email::isValid(); email should contain an @ somewhere', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::isValid('name@domain.com');
        $expected = true;
        TestCheck::assertBoolean('C.2', '\Flexio\Services\Email::isValid(); email should contain an @ somewhere', $actual, $expected, $results);



        // TEST: email should contain a valid name and address

        // BEGIN TEST
        $actual = \Flexio\Services\Email::isValid('@domain.com');
        $expected = false;
        TestCheck::assertBoolean('D.1', '\Flexio\Services\Email::isValid(); email is missing a name', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::isValid('name@');
        $expected = false;
        TestCheck::assertBoolean('D.2', '\Flexio\Services\Email::isValid(); email is missing a domain', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::isValid('name@domain.');
        $expected = false;
        TestCheck::assertBoolean('D.3', '\Flexio\Services\Email::isValid(); email doesn\'t contain a valid domain;', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Email::isValid('name@.com');
        $expected = false;
        TestCheck::assertBoolean('D.4', '\Flexio\Services\Email::isValid(); email doesn\'t contain a valid domain', $actual, $expected, $results);
    }
}
