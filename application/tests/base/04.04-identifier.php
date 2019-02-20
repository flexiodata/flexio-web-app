<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-06-17
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
        // TEST: identifiers can't use a reserved or illegal words

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('flexiodata');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('index');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('home');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('data');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.4', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('api');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.5', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('public');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.6', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('private');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.7', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('project');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.8', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('workspace');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.9', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('connection');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.10', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('account');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.11', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('sex');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.12', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('shit');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.13', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::isValid('damn');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.14', '\Flexio\Base\Identifier::isValid(); identifiers can\'t use reserved or illegal words', $actual, $expected, $results);
    }
}
