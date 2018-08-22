<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-11-11
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
        $service = new \Flexio\Services\Firebase;
        $actual = get_class($service);
        $expected = 'Flexio\Services\Firebase';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Services\Firebase; basic file syntax check',  $actual, $expected, $results);
    }
}
