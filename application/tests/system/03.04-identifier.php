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


class Test
{
    public function run(&$results)
    {
        // TEST: identifiers can't use a reserved or illegal words

        // BEGIN TEST
        $actual = \Identifier::isValid('flexio');
        $expected = false;
        TestCheck::assertBoolean('A.1', '\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('index');
        $expected = false;
        TestCheck::assertBoolean('A.2', '\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('home');
        $expected = false;
        TestCheck::assertBoolean('A.3', '\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('data');
        $expected = false;
        TestCheck::assertBoolean('A.4', '\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('api');
        $expected = false;
        TestCheck::assertBoolean('A.5', '\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('public');
        $expected = false;
        TestCheck::assertBoolean('A.6', '\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('private');
        $expected = false;
        TestCheck::assertBoolean('A.7', '\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('project');
        $expected = false;
        TestCheck::assertBoolean('A.8', '\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('workspace');
        $expected = false;
        TestCheck::assertBoolean('A.9', '\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('connection');
        $expected = false;
        TestCheck::assertBoolean('A.10', '\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('account');
        $expected = false;
        TestCheck::assertBoolean('A.11', '\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('sex');
        $expected = false;
        TestCheck::assertBoolean('A.12', '\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('shit');
        $expected = false;
        TestCheck::assertBoolean('A.13', '\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Identifier::isValid('damn');
        $expected = false;
        TestCheck::assertBoolean('A.14', '\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);
    }
}
