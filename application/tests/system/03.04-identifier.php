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
        $actual = \Flexio\System\Identifier::isValid('flexio');
        $expected = false;
        TestCheck::assertBoolean('A.1', '\Flexio\System\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Identifier::isValid('index');
        $expected = false;
        TestCheck::assertBoolean('A.2', '\Flexio\System\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Identifier::isValid('home');
        $expected = false;
        TestCheck::assertBoolean('A.3', '\Flexio\System\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Identifier::isValid('data');
        $expected = false;
        TestCheck::assertBoolean('A.4', '\Flexio\System\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Identifier::isValid('api');
        $expected = false;
        TestCheck::assertBoolean('A.5', '\Flexio\System\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Identifier::isValid('public');
        $expected = false;
        TestCheck::assertBoolean('A.6', '\Flexio\System\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Identifier::isValid('private');
        $expected = false;
        TestCheck::assertBoolean('A.7', '\Flexio\System\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Identifier::isValid('project');
        $expected = false;
        TestCheck::assertBoolean('A.8', '\Flexio\System\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Identifier::isValid('workspace');
        $expected = false;
        TestCheck::assertBoolean('A.9', '\Flexio\System\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Identifier::isValid('connection');
        $expected = false;
        TestCheck::assertBoolean('A.10', '\Flexio\System\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Identifier::isValid('account');
        $expected = false;
        TestCheck::assertBoolean('A.11', '\Flexio\System\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Identifier::isValid('sex');
        $expected = false;
        TestCheck::assertBoolean('A.12', '\Flexio\System\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Identifier::isValid('shit');
        $expected = false;
        TestCheck::assertBoolean('A.13', '\Flexio\System\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Identifier::isValid('damn');
        $expected = false;
        TestCheck::assertBoolean('A.14', '\Flexio\System\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);
    }
}
