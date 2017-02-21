<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-07
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TEST: \Model::checkPasswordHash(); tests for empty string input

        // BEGIN TEST
        $actual = \Model::checkPasswordHash('','');
        $expected = false;
        TestCheck::assertBoolean('A.1', '\Model::checkPasswordHash() empty string input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::checkPasswordHash('{SSHA}b5e06a0994664b8674c182864515de4dc44333b0','');
        $expected = true;
        TestCheck::assertBoolean('A.2', '\Model::checkPasswordHash() hash for empty string input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::checkPasswordHash('{SSHA}b5e06a0994664b8674c182864515de4dc44333b0',' '); // check for trimming
        $expected = false;
        TestCheck::assertBoolean('A.3', '\Model::checkPasswordHash() single space password; check that spaces arent trimmed', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::checkPasswordHash('b5e06a0994664b8674c182864515de4dc44333b0',''); // check for leading SSHA prefix
        $expected = false;
        TestCheck::assertBoolean('A.4', '\Model::checkPasswordHash() check for hash identifier', $actual, $expected, $results);



        // TEST: \Model::checkPasswordHash(); tests for non-empty string input

        // BEGIN TEST
        $actual = \Model::checkPasswordHash('{SSHA}87a0f0cfc2cd5b68a9a3b5a3937ca1211227a542','test');
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Model::checkPasswordHash() basic non-empty string input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::checkPasswordHash('{SSHA}87a0f0cfc2cd5b68a9a3b5a3937ca1211227a542','test '); // don't allow trimming after
        $expected = false;
        TestCheck::assertBoolean('B.2', '\Model::checkPasswordHash() non-empty string input; check that spaces after password are not trimmed', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::checkPasswordHash('{SSHA}87a0f0cfc2cd5b68a9a3b5a3937ca1211227a542',' test'); // don't allow trimming before
        $expected = false;
        TestCheck::assertBoolean('B.3', '\Model::checkPasswordHash() non-empty string input; check that spaces before password are not trimmed', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::checkPasswordHash('{SSHA}3226155047ca866b1724d14f2e8167aa2ef88afb','mj6dc95k99tc');
        $expected = true;
        TestCheck::assertBoolean('B.4', '\Model::checkPasswordHash() non-trivial password check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::checkPasswordHash('{SSHA}3226155047ca866b1724d14f2e8167aa2ef88afb','mj6dc95k99tcd');
        $expected = false;
        TestCheck::assertBoolean('B.5', '\Model::checkPasswordHash() non-trivial password check; password length sensitivity', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::checkPasswordHash('{SSHA}3226155047ca866b1724d14f2e8167aa2ef88afb','mj6dc95k99t');
        $expected = false;
        TestCheck::assertBoolean('B.6', '\Model::checkPasswordHash() non-trivial password check; password length sensitivity', $actual, $expected, $results);
    }
}
