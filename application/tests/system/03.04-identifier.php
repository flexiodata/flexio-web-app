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
        // TEST: identifiers can't use a reserved or illegal words

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('flexio');
        $expected = false;
        TestCheck::assertBoolean('A.1', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('index');
        $expected = false;
        TestCheck::assertBoolean('A.2', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('home');
        $expected = false;
        TestCheck::assertBoolean('A.3', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('data');
        $expected = false;
        TestCheck::assertBoolean('A.4', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('api');
        $expected = false;
        TestCheck::assertBoolean('A.5', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('public');
        $expected = false;
        TestCheck::assertBoolean('A.6', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('private');
        $expected = false;
        TestCheck::assertBoolean('A.7', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('project');
        $expected = false;
        TestCheck::assertBoolean('A.8', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('workspace');
        $expected = false;
        TestCheck::assertBoolean('A.9', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('connection');
        $expected = false;
        TestCheck::assertBoolean('A.10', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('account');
        $expected = false;
        TestCheck::assertBoolean('A.11', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('sex');
        $expected = false;
        TestCheck::assertBoolean('A.12', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('shit');
        $expected = false;
        TestCheck::assertBoolean('A.13', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('damn');
        $expected = false;
        TestCheck::assertBoolean('A.14', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);
    }
}
