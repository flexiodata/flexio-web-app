<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-11
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
        // FUNCTION: \Flexio\Model\User::checkPasswordHash()


        // SETUP
        $model = \Flexio\Tests\Util::getModel()->user;


        // TEST: tests for empty string input

        // BEGIN TEST
        $actual = $model->checkPasswordHash('','');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Model\User::checkPasswordHash(): empty string input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = $model->checkPasswordHash('{SSHA}b5e06a0994664b8674c182864515de4dc44333b0','');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Model\User::checkPasswordHash(): hash for empty string input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = $model->checkPasswordHash('{SSHA}b5e06a0994664b8674c182864515de4dc44333b0',' '); // check for trimming
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Model\User::checkPasswordHash(): single space password; check that spaces arent trimmed', $actual, $expected, $results);

        // BEGIN TEST
        $actual = $model->checkPasswordHash('b5e06a0994664b8674c182864515de4dc44333b0',''); // check for leading SSHA prefix
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.4', '\Flexio\Model\User::checkPasswordHash(): check for hash identifier', $actual, $expected, $results);


        // TEST: tests for non-empty string input

        // BEGIN TEST
        $actual = $model->checkPasswordHash('{SSHA}87a0f0cfc2cd5b68a9a3b5a3937ca1211227a542','test');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Model\User::checkPasswordHash(): basic non-empty string input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = $model->checkPasswordHash('{SSHA}87a0f0cfc2cd5b68a9a3b5a3937ca1211227a542','test '); // don't allow trimming after
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.2', '\Flexio\Model\User::checkPasswordHash(): non-empty string input; check that spaces after password are not trimmed', $actual, $expected, $results);

        // BEGIN TEST
        $actual = $model->checkPasswordHash('{SSHA}87a0f0cfc2cd5b68a9a3b5a3937ca1211227a542',' test'); // don't allow trimming before
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.3', '\Flexio\Model\User::checkPasswordHash(): non-empty string input; check that spaces before password are not trimmed', $actual, $expected, $results);

        // BEGIN TEST
        $actual = $model->checkPasswordHash('{SSHA}3226155047ca866b1724d14f2e8167aa2ef88afb','mj6dc95k99tc');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.4', '\Flexio\Model\User::checkPasswordHash(): non-trivial password check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = $model->checkPasswordHash('{SSHA}3226155047ca866b1724d14f2e8167aa2ef88afb','mj6dc95k99tcd');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.5', '\Flexio\Model\User::checkPasswordHash(): non-trivial password check; password length sensitivity', $actual, $expected, $results);

        // BEGIN TEST
        $actual = $model->checkPasswordHash('{SSHA}3226155047ca866b1724d14f2e8167aa2ef88afb','mj6dc95k99t');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.6', '\Flexio\Model\User::checkPasswordHash(): non-trivial password check; password length sensitivity', $actual, $expected, $results);
    }
}
