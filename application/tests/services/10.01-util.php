<?php
/**
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-09-12
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
        // TEST: service creation

        // BEGIN TEST
        $instance = new \Flexio\Services\Util;
        $actual = get_class($instance);
        $expected = 'Flexio\Services\Util';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Services\Util; basic file syntax check',  $actual, $expected, $results);
    }
}
