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
        // TEST: function for making a valid identifier from a string

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::makeValid('my-function');
        $expected = 'my-function';
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Base\Identifier::makeValid(); allow strings that are already valid', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::makeValid('My-Function');
        $expected = 'my-function';
        \Flexio\Tests\Check::assertString('A.2', '\Flexio\Base\Identifier::makeValid(); convert strings to lowercase', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::makeValid('  my-function  ');
        $expected = 'my-function';
        \Flexio\Tests\Check::assertString('A.3', '\Flexio\Base\Identifier::makeValid(); trim leading/trailing spaces', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::makeValid('a b c');
        $expected = 'a-b-c';
        \Flexio\Tests\Check::assertString('A.4', '\Flexio\Base\Identifier::makeValid(); fix strings that contain illegal characters', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::makeValid('file.txt');
        $expected = 'file-txt';
        \Flexio\Tests\Check::assertString('A.5', '\Flexio\Base\Identifier::makeValid(); fix strings that contain illegal characters', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::makeValid('1bc');
        $expected = 'id-1bc';
        \Flexio\Tests\Check::assertString('A.6', '\Flexio\Base\Identifier::makeValid(); fix strings that start with a number', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::makeValid('');
        $expected = 'id-';
        \Flexio\Tests\Check::assertString('A.7', '\Flexio\Base\Identifier::makeValid(); fix strings that are too short', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::makeValid('a');
        $expected = 'id-a';
        \Flexio\Tests\Check::assertString('A.8', '\Flexio\Base\Identifier::makeValid(); fix strings that are too short', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::makeValid('aaaaaaaaaabbbbbbbbbbccccccccccddddddddddeeeeeeeeeeffffffffffgggggggggghhhhhhhhhhiiiiiiiiii');
        $expected = 'aaaaaaaaaabbbbbbbbbbccccccccccddddddddddeeeeeeeeeeffffffffffgggggggggghhhhhhhhhh';
        \Flexio\Tests\Check::assertString('A.9', '\Flexio\Base\Identifier::makeValid(); fix strings that are too long', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::makeValid('Pipe');
        $expected = 'id-pipe';
        \Flexio\Tests\Check::assertString('A.10', '\Flexio\Base\Identifier::makeValid(); fix reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::makeValid('Pipes');
        $expected = 'id-pipes';
        \Flexio\Tests\Check::assertString('A.11', '\Flexio\Base\Identifier::makeValid(); fix reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::makeValid('Connection');
        $expected = 'id-connection';
        \Flexio\Tests\Check::assertString('A.12', '\Flexio\Base\Identifier::makeValid(); fix reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::makeValid('Connections');
        $expected = 'id-connections';
        \Flexio\Tests\Check::assertString('A.13', '\Flexio\Base\Identifier::makeValid(); fix reserved or illegal words', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Identifier::makeValid('index');
        $expected = 'id-index';
        \Flexio\Tests\Check::assertString('A.14', '\Flexio\Base\Identifier::makeValid(); fix reserved or illegal words', $actual, $expected, $results);
    }
}
