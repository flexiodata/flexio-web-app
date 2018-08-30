<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-04-03
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
        // TEST: class syntax check

        // BEGIN TEST
        $class = new \Flexio\Api\Cron;
        $actual = get_class($class);
        $expected = 'Flexio\Api\Cron';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Api\Cron; basic class syntax check',  $actual, $expected, $results);
    }
}
