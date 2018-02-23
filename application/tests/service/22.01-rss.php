<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-10-31
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
        // TODO: add additional tests

        // TEST: service creation

        // BEGIN TEST
        $service = new \Flexio\Services\Rss;
        $actual = get_class($service);
        $expected = 'Flexio\Services\Rss';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Services\Rss; basic file syntax check',  $actual, $expected, $results);
    }
}
