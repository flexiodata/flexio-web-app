<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-10-23
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
        // TEST: model constant tests

        // BEGIN TEST
        $actual = \Model::PIPE_STATUS_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('A.1', 'Pipe status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PIPE_STATUS_ACTIVE;
        $expected = 'A';
        \Flexio\Tests\Check::assertString('A.2', 'Pipe status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PIPE_STATUS_INACTIVE;
        $expected = 'I';
        \Flexio\Tests\Check::assertString('A.3', 'Pipe status constant',  $actual, $expected, $results);
    }
}
