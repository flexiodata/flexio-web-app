<?php
/**
 *
 * Copyright (c) 2016, Flex Research LLC. All rights reserved.
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
        if (\Flexio\Tests\Base::TEST_SERVICE_POSTGRES === false)
            return;


        // TEST: service creation

        // BEGIN TEST
        $instance = new \Flexio\Services\Postgres;
        $actual = get_class($instance);
        $expected = 'Flexio\Services\Postgres';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Services\Postgres::create(); basic file syntax check',  $actual, $expected, $results);

        // BEGIN TEST
        $instance = new \Flexio\Services\Postgres;
        $actual = ($instance instanceof \Flexio\IFace\IConnection) ? true : false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', 'new \Flexio\Services\Postgres; instance of IConnection ',  $actual, $expected, $results);
    }
}
