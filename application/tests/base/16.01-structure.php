<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-10-21
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
        // BEGIN TEST
        $actual = \Flexio\Base\Structure::TYPE_INVALID;
        $expected = '';
        \Flexio\Tests\Check::assertString('A.1', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Structure::TYPE_TEXT;
        $expected = 'text';
        \Flexio\Tests\Check::assertString('A.2', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Structure::TYPE_CHARACTER;
        $expected = 'character';
        \Flexio\Tests\Check::assertString('A.3', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Structure::TYPE_WIDECHARACTER;
        $expected = 'widecharacter';
        \Flexio\Tests\Check::assertString('A.4', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Structure::TYPE_NUMERIC;
        $expected = 'numeric';
        \Flexio\Tests\Check::assertString('A.5', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Structure::TYPE_DOUBLE;
        $expected = 'double';
        \Flexio\Tests\Check::assertString('A.6', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Structure::TYPE_INTEGER;
        $expected = 'integer';
        \Flexio\Tests\Check::assertString('A.7', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Structure::TYPE_DATE;
        $expected = 'date';
        \Flexio\Tests\Check::assertString('A.8', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Structure::TYPE_DATETIME;
        $expected = 'datetime';
        \Flexio\Tests\Check::assertString('A.9', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Structure::TYPE_BOOLEAN;
        $expected = 'boolean';
        \Flexio\Tests\Check::assertString('A.10', 'Structure; verify class constants',  $actual, $expected, $results);



        // TEST: Structure wildcard constants

        // BEGIN TEST
        $actual = \Flexio\Base\Structure::WILDCARD_INVALID;
        $expected = '';
        \Flexio\Tests\Check::assertString('B.1', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Structure::WILDCARD_ALL;
        $expected = '*';
        \Flexio\Tests\Check::assertString('B.2', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Structure::WILDCARD_TEXT;
        $expected = ':text';
        \Flexio\Tests\Check::assertString('B.3', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Structure::WILDCARD_NUMBER;
        $expected = ':number';
        \Flexio\Tests\Check::assertString('B.4', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Structure::WILDCARD_DATE;
        $expected = ':date';
        \Flexio\Tests\Check::assertString('B.5', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Structure::WILDCARD_DATETIME;
        $expected = ':datetime';
        \Flexio\Tests\Check::assertString('B.6', 'Structure; verify class constants',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Structure::WILDCARD_BOOLEAN;
        $expected = ':boolean';
        \Flexio\Tests\Check::assertString('B.7', 'Structure; verify class constants',  $actual, $expected, $results);
    }
}
