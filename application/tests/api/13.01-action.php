<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
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
        $class = new \Flexio\Api\Action;
        $actual = get_class($class);
        $expected = 'Flexio\Api\Action';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Api\Action; basic class syntax check',  $actual, $expected, $results);
    }
}
