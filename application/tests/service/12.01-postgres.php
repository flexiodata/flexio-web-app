<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-05-16
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
        $service = new \Flexio\Services\Postgres;
        $actual = get_class($service);
        $expected = 'Flexio\Services\Postgres';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Services\Postgres::create(); basic file syntax check',  $actual, $expected, $results);
    }
}
